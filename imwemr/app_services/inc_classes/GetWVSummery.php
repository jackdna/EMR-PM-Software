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
File: GetWVSummary.php
Purpose: This file provides Work view Summary sheet of a patient visit.
Access Type : Include file
*/
?>
<?php
//GetWVSummery
set_time_limit(900);
include_once(dirname(__FILE__).'/patient_app.php');
include_once($GLOBALS['srcdir']."/classes/work_view/ChartRecArc.php");

class GetWVSummery extends patient_app{
	public $ar_req;
	
	private $patient_id;
	private $form_id;
	private $getAmsGridCVF;
	
	public function __construct($patient_id, $form_id){
		parent::__construct();
		$this->patient_id= $patient_id;
		$this->form_id = $form_id;
		$this->getAmsGridCVF = 0;
	}
	
	function getOE(){
		
		$patient_id = $this->patient_id;
		$form_id = $this->form_id;
		
		$sql = "SELECT phth_pros, eyePrPh, phth_pros_set_curr  FROM chart_left_cc_history WHERE patient_id = '".$patient_id."' AND form_id = '".$form_id."' ";
		$row = sqlQuery($sql);
		if(($row == false) ){			
			
			//include_once($GLOBALS['incdir']."/chart_notes/common/functions.php");
			$elem_ccId = "0";
			// getPast Finalized
			$row = $this->valNewRecordCcHis($patient_id);

		}else{
			$elem_ccId = $row["cc_id"];
			
		}
		
		if($row != false){	
			$elem_phth_pros = $row["phth_pros"];
			$elem_eyePrPh = $row["eyePrPh"];	
			$elem_curset_phth_pros = ($elem_ccId != "0") ? $row["phth_pros_set_curr"] : "0" ;			
		}
		
		//One Eye Str
		if(!empty($elem_phth_pros)&&!empty($elem_eyePrPh)){
			$oe_prms = $elem_phth_pros."~!~".$elem_eyePrPh."~!~".$elem_curset_phth_pros;
		}else{
			$oe_prms = "";
		}
		
		return $oe_prms;
	}
	
	/*
	function getWVSummery(){
		
		$ignoreAuth=1;
		include_once($GLOBALS['incdir']."/chart_notes/onload_wv_exams.php");
		include_once($GLOBALS['incdir']."/chart_notes/onload_wv_functions.inc.php");
		include_once($GLOBALS['incdir']."/chart_notes/common/ChartTemp.php");
		include_once($GLOBALS['incdir']."/chart_notes/common/User.php");
		
		global $patient_id,$form_id;
		$patient_id = $this->patient_id;
		$form_id = $this->form_id;				
		
		//user
		$oUser = new User($this->ar_req['phyId']);
		$user_type = $oUser->getUType();
		//--	
		
		//getTemplete Id and exams
		$oChartTemp = new ChartTemp();
		$elem_chartTemplateId = $oChartTemp->getChartTempId($patient_id,$form_id);
		if(empty($elem_chartTemplateId)){
			//get comprehensive settings
			$elem_chartTemplateId=$oChartTemp->getIdFromName("comprehensive");
		}
		
		if(!empty($elem_chartTemplateId)){
			$tmp = $oChartTemp->getTempInfo($elem_chartTemplateId);
			if(!empty($tmp[1])){
				$elem_chartTempName = $tmp[1];		
				//check for logged in user : physician or technician and chart finalized		
				//Please remember Scribe are same as Physician i.e. their view should be same as Physician View: userid = 13		
				//Can see physician view		
				if( in_array($user_type, $GLOBALS['arrValidCNPhy']) || ($finalize_flag == 1 && $isReviewable==false) || $user_type == 13 || !empty($_SESSION["flg_phy_view"])){			
					$arrTempProc = (!empty($tmp[2])) ? explode(",", stripslashes($tmp[2])) : array(); //Phy			
				}else{
					$arrTempProc = (!empty($tmp[4])) ? explode(",", stripslashes($tmp[4])) : array(); //Tech			
				}			
			}
		}
		//--		
		
		//One Eye ----
		$this->ar_req["oe"]=$this->getOE();	
		//--
		
		
		$this->ar_req["webservice"] = "1"; //for web service version
		
		$cwidth="100%";
		if(isset($this->ar_req["cwidth"])){  $cwidth=$this->ar_req["cwidth"];  }
		if(strpos($cwidth,"%")===false && strpos($cwidth,"px")===false){ $cwidth.="%"; }
		
		$strHtml="";
		$strHtml.="<div class=\"esum\" style=\"height:20px;\" ><table><tr align=\"center\" style=\"line-height:20px;font-weight:bold;\"><td class=\"od sumod\">OD</td><td class=\"os sumos\" >OS</td></tr></table></div>";
		//$arr=array();	
		//$allexm = $allexm;
		$arr_AllExam = $arrTempProc; //explode(",",$this->ar_req["allexm"]);
		if(count($arr_AllExam)>0){
			foreach($arr_AllExam as $key => $val){
				$enm =trim($val);				
				if(!empty($enm) && $enm!="CVF" && $enm!="Amsler Grid"){
					//$arr[$enm] =  onlwv_GetExamSummary($enm,$this->ar_req);
					$strHtml.=onlwv_GetExamSummary($enm,$this->ar_req);
				}
			}
		}		
		
		echo "<style>
					
				.esum {width:".$cwidth.";position:relative; }
				.esum table{ width:100%; padding:0px; border-collapse:collapse;vertical-align:top;
					font-weight:500;font-size:11px;color:black;border:1px solid black;min-height:30px;
					display:inline-table; /*safari*-/ }

				.esum table td{ border-right:1px solid black; padding:0px 0px 5px 2px;vertical-align:top; }

				.edone { background-color:#FFFFFF;width:auto; }

				.ehdr { padding:0px 0px 0px 0px; }

				.einfo { width:100%;float:left;font-weight:bold; }

				.hdr { background-color:#e58e4b; }

				.sumod {background-color:#d3d3d3;width:50%;word-wrap:break-word;}

				.sumos {background-color:#d3d3d3;width:50%;word-wrap:break-word;}

				.esubexam { padding-left:6px; }
				.bgWhite{background-color:white;}
				
				.od{color:blue;}
				.os{color:green;}
				
				</style>
				".		
			$strHtml.
			"";
			
		exit();
		
		///return $strHtml;		
	}
	*/
	//returns xml
	function getWVSummery(){
		
		$ignoreAuth=1;
		//include_once($GLOBALS['srcdir']."/chart_notes/onload_wv_exams.php");
		//include_once($GLOBALS['srcdir']."/chart_notes/onload_wv_functions.inc.php");
		include_once($GLOBALS['srcdir']."/classes/work_view/ChartTemp.php");
		include_once($GLOBALS['srcdir']."/classes/work_view/User.php");
		
		global $patient_id,$form_id;
		$patient_id = $this->patient_id;
		$form_id = $this->form_id;				
		
		//user
		$oUser = new User($this->ar_req['phyId']);
		$user_type = $oUser->getUType();
		//--	
		
		//getTemplete Id and exams
		$oChartTemp = new ChartTemp();
		$elem_chartTemplateId = $oChartTemp->getChartTempId($patient_id,$form_id);
		
		//if(empty($elem_chartTemplateId)){ //stopped template check at the instruction of Sarika ma'am  : 18-05-2015
			//get comprehensive settings
			$elem_chartTemplateId=$oChartTemp->getIdFromName("comprehensive");
		//}
		
		if(!empty($elem_chartTemplateId)){
			$tmp = $oChartTemp->getTempInfo($elem_chartTemplateId);
			if(!empty($tmp[1])){
				$elem_chartTempName = $tmp[1];
				$tmp[2] = $tmp[4] = "Vision,Distance,Near,AR,AK,PC 1,PC 2,PC 3,MR 1,MR 2,MR 3,BAT,CVF,Amsler Grid,ICP Color Plate,Stereopsis,Diplopia,W4Dot,Retinoscopy,Exophthalmometer,Comments,Pupil,EOM,External,L&A,IOP/Gonio,SLE,Fundus Exam,Refractive Surgery,Conjunctiva,Cornea,Ant. Chamber,Iris & Pupil,Lens,DrawSLE,Opt. Nev,Vitreous,DrawFundus,Retinal Exam,Contact Lens,Plastic,VF/OCT - GL";
				//check for logged in user : physician or technician and chart finalized		
				//Please remember Scribe are same as Physician i.e. their view should be same as Physician View: userid = 13		
				//Can see physician view		
				if( in_array($user_type, $GLOBALS['arrValidCNPhy']) || ($finalize_flag == 1 && $isReviewable==false) || $user_type == 13 || !empty($_SESSION["flg_phy_view"])){			
					$arrTempProc = (!empty($tmp[2])) ? explode(",", stripslashes($tmp[2])) : array(); //Phy			
				}else{
					$arrTempProc = (!empty($tmp[4])) ? explode(",", stripslashes($tmp[4])) : array(); //Tech			
				}			
			}
		}
		//--		
		
		//One Eye ----
		$this->ar_req["oe"]=$this->getOE();	
		//--
		
		
		$this->ar_req["webservice"] = "1"; //for web service version
		
		$cwidth="100%";
		if(isset($this->ar_req["cwidth"])){  $cwidth=$this->ar_req["cwidth"];  }
		if(strpos($cwidth,"%")===false && strpos($cwidth,"px")===false){ $cwidth.="%"; }
		
		//$strHtml=""; 
		$strHtml=array(); 
		//$strHtml.="<div class=\"esum\" style=\"height:20px;\" ><table><tr align=\"center\" style=\"line-height:20px;font-weight:bold;\"><td class=\"od sumod\">OD</td><td class=\"os sumos\" >OS</td></tr></table></div>";
		//$strHtml.="<?xml version=\"1.0\" encoding=\"ISO-8859-1\"? >";
		//$strHtml.="<all_summary>";
		//$arr=array();	
		//$allexm = $allexm;
		$arr_AllExam = $arrTempProc; //explode(",",$this->ar_req["allexm"]);
		if(count($arr_AllExam)>0){
			foreach($arr_AllExam as $key => $val){
				$enm =trim($val);				
				if(!empty($enm)){
					//$arr[$enm] =  onlwv_GetExamSummary($enm,$this->ar_req);
					
					if($this->getAmsGridCVF == 1){ //only cvf + amsgrid
						if($enm!="CVF" && $enm!="Amsler Grid"){
							continue;
						}						
					}else{
						if($enm=="CVF" || $enm=="Amsler Grid"){
							continue;
						}
					}
					
					$tmp = $this->onlwv_GetExamSummary($enm,$this->ar_req,$this->ar_req['patId'],$this->ar_req['form_id']);
					
					if(!empty($tmp)){
						$tmp_arr = unserialize($tmp);
						if(count($tmp_arr)>0){  $strHtml[] = $tmp_arr;   }
					}
				}
			}
		}
		//$strHtml.="</all_summary>";
		
		//echo "<pre>";
		//print_r($strHtml);
		//exit();
		
		if($this->getAmsGridCVF == 1){ //only cvf + amsgrid
			return $strHtml;
		}else{
		
		echo "".json_encode($strHtml).
			"";
			
		exit();
		
		}
		///return $strHtml;		
	}
	
	
	function getWVDrawing(){
		$flgApp = 1;
		$ignoreAuth=1;
		
		include_once($GLOBALS['srcdir']."/classes/work_view/wv_functions.php");
		include_once($GLOBALS['srcdir']."/classes/work_view/ChartNote.php");
		include_once($GLOBALS['srcdir']."/classes/work_view/Onload.php");
		include_once($GLOBALS['srcdir']."/classes/work_view/Pupil.php");
		include_once($GLOBALS['srcdir']."/classes/work_view/EOM.php");
		include_once($GLOBALS['srcdir']."/classes/work_view/ExternalExam.php");
		include_once($GLOBALS['srcdir']."/classes/work_view/LA.php");
		include_once($GLOBALS['srcdir']."/classes/work_view/Gonio.php");
		include_once($GLOBALS['srcdir']."/classes/work_view/ChartIop.php");
		include_once($GLOBALS['srcdir']."/classes/work_view/SLE.php");
		include_once($GLOBALS['srcdir']."/classes/work_view/OpticNerve.php");
		include_once($GLOBALS['srcdir']."/classes/work_view/FundusExam.php");
		include_once($GLOBALS['srcdir']."/classes/work_view/RefSurg.php");
		include_once($GLOBALS['srcdir']."/classes/work_view/CVF.php");
		include_once($GLOBALS['srcdir']."/classes/work_view/AmslerGrid.php");
		include_once($GLOBALS['srcdir']."/classes/work_view/User.php");
		include_once($GLOBALS['srcdir']."/classes/work_view/CLSDrawingData.php");
		$onLoadObj = New Onload();
		//include_once($GLOBALS['incdir']."/chart_notes/onload_wv_exams.php");
		//include_once($GLOBALS['incdir']."/chart_notes/onload_wv_functions.inc.php");
		
		//?reqModule=chart_notes&service=getWVDrawing&phyId=209&patId=263232910&form_id=40170
		
		//global $patient_id,$form_id;
		$patient_id = $this->patient_id;
		$form_id = $this->form_id;
		
		$ret = "No Drawing exits.";
		$arrRet=array();
		
		
		if(!empty($patient_id)&&!empty($form_id)){
			
			//$ret = "IN 1";
			
			$OBJDrawingData = new CLSDrawingData();
			
			list($strDocIds, $str_row_modify_Draw, $arrDocPaths, $arrDocIds, $arr_flg_Prv_Drw) = $OBJDrawingData->getPtChartDrawings($patient_id, $form_id);		
			
			//return $strDocIds."TEST";
			//$rowGetDrawing["row_modify_by"],$rowGetDrawing["drawLMB"]
			
			//$arr_strDocIds = explode(",", $strDocIds);
			if(count($arrDocPaths)>0){
				
				//$ret .= "".print_r($arrDocPaths,1);
				$ret = "";
				
				foreach($arrDocPaths as $key => $arridocpath){
					
					$idocpath = $arridocpath[0];	
					$drawDate = $arridocpath[1];	
					$idLMB = $arridocpath[2];
					$dtLMB = $arridocpath[3];
				
					if(!empty($idocpath)){					
						
						if(isset($drawDate)&&!empty($drawDate)){						
							
							//LMB--
							$strLMB="";
							if(isset($idLMB)&&!empty($idLMB)){
								$tmpLMB = getUserFirstName($idLMB,2);
								$strLMB="LMB ".$tmpLMB[2];
								if(!empty($dtLMB)) {$strLMB.=" ".$dtLMB;}									
							}
							//LMB--
							
							if($flgApp==1){
								$strLMB=array($drawDate,$strLMB);
							}else{
								$strLMB="<label>".$drawDate."<br/>".$strLMB."</label>"; 
							}
							
						}else{
							$strLMB="";
						}			
						
						$tmp = $onLoadObj->getDrwThumb("DrawingPane", $idocpath, $arrDocIds[$key], $arr_flg_Prv_Drw[$key], $strLMB, $flgApp);
						if($flgApp==1){
							$arrRet[]=$tmp;
						}else{
							$ret .= $tmp;
							$ret .= "<br/>";
						}
					}
				}			
				
				//$ret = "<xmp>".$ret."</xmp>";
				
			}else{
				$ret = "No Drawing exits.";			
			}
			
		}	
		
		if($flgApp==1){
			$arr = $arrRet;
		}else{
			$arr = $ret;
		}
		
		//$arr = onlwv_GetExamDrawings(1);		
		return $arr;
	}
	function getWVSummery_app(){
		
		$ignoreAuth=1;
		include_once($GLOBALS['incdir']."/chart_notes/onload_wv_exams.php");
		include_once($GLOBALS['incdir']."/chart_notes/onload_wv_functions.inc.php");
		include_once($GLOBALS['incdir']."/chart_notes/common/ChartTemp.php");
		include_once($GLOBALS['incdir']."/chart_notes/common/User.php");
		
		global $patient_id,$form_id;
		$patient_id = $this->patient_id;
		$form_id = $this->form_id;				
		
		//user
		$oUser = new User($this->ar_req['phyId']);
		$user_type = $oUser->getUType();
		//--	
		
		//getTemplete Id and exams
		$oChartTemp = new ChartTemp();
		$elem_chartTemplateId = $oChartTemp->getChartTempId($patient_id,$form_id);
		
		//if(empty($elem_chartTemplateId)){ //stopped template check at the instruction of Sarika ma'am  : 18-05-2015
			//get comprehensive settings
			$elem_chartTemplateId=$oChartTemp->getIdFromName("comprehensive");
		//}
		
		if(!empty($elem_chartTemplateId)){
			$tmp = $oChartTemp->getTempInfo($elem_chartTemplateId);
			if(!empty($tmp[1])){
				$elem_chartTempName = $tmp[1];
				$tmp[2] = $tmp[4] = "Vision,Distance,Near,AR,AK,PC 1,PC 2,PC 3,MR 1,MR 2,MR 3,BAT,CVF,Amsler Grid,ICP Color Plate,Stereopsis,Diplopia,W4Dot,Retinoscopy,Exophthalmometer,Comments,Pupil,EOM,External,L&A,IOP/Gonio,SLE,Fundus Exam,Refractive Surgery,Conjunctiva,Cornea,Ant. Chamber,Iris & Pupil,Lens,DrawSLE,Opt. Nev,Vitreous,DrawFundus,Retinal Exam,Contact Lens,Plastic,VF/OCT - GL";
				//check for logged in user : physician or technician and chart finalized		
				//Please remember Scribe are same as Physician i.e. their view should be same as Physician View: userid = 13		
				//Can see physician view		
				if( in_array($user_type, $GLOBALS['arrValidCNPhy']) || ($finalize_flag == 1 && $isReviewable==false) || $user_type == 13 || !empty($_SESSION["flg_phy_view"])){			
					$arrTempProc = (!empty($tmp[2])) ? explode(",", stripslashes($tmp[2])) : array(); //Phy			
				}else{
					$arrTempProc = (!empty($tmp[4])) ? explode(",", stripslashes($tmp[4])) : array(); //Tech			
				}			
			}
		}
		//--		
		
		//One Eye ----
		$this->ar_req["oe"]=$this->getOE();	
		//--
		
		
		$this->ar_req["webservice"] = "1"; //for web service version
		
		$cwidth="100%";
		if(isset($this->ar_req["cwidth"])){  $cwidth=$this->ar_req["cwidth"];  }
		if(strpos($cwidth,"%")===false && strpos($cwidth,"px")===false){ $cwidth.="%"; }
		
		//$strHtml=""; 
		$strHtml=array(); 
		//$strHtml.="<div class=\"esum\" style=\"height:20px;\" ><table><tr align=\"center\" style=\"line-height:20px;font-weight:bold;\"><td class=\"od sumod\">OD</td><td class=\"os sumos\" >OS</td></tr></table></div>";
		//$strHtml.="<?xml version=\"1.0\" encoding=\"ISO-8859-1\"? >";
		//$strHtml.="<all_summary>";
		//$arr=array();	
		//$allexm = $allexm;
		$arr_AllExam = $arrTempProc; //explode(",",$this->ar_req["allexm"]);
		if(count($arr_AllExam)>0){
			foreach($arr_AllExam as $key => $val){
				$enm =trim($val);				
				if(!empty($enm)){
					//$arr[$enm] =  onlwv_GetExamSummary($enm,$this->ar_req);
					
					if($this->getAmsGridCVF == 1){ //only cvf + amsgrid
						if($enm!="CVF" && $enm!="Amsler Grid"){
							continue;
						}						
					}else{
						if($enm=="CVF" || $enm=="Amsler Grid"){
							continue;
						}
					}
					
					$tmp = $this->onlwv_GetExamSummary($enm,$this->ar_req,$this->patient_id,$this->form_id);	
					if(!empty($tmp)){
						$tmp_arr = unserialize($tmp);
						if(count($tmp_arr)>0){  $strHtml[] = $tmp_arr;   }
					}
				}
			}
		}
		//$strHtml.="</all_summary>";
		
		//echo "<pre>";
		//print_r($strHtml);
		//exit();
		
		if($this->getAmsGridCVF == 1){ //only cvf + amsgrid
			return $strHtml;
		}else{
		
		//echo "".json_encode($strHtml).
			//"";
			return $strHtml;
		exit();
		
		}
		///return $strHtml;		
	}
	
	
	
	
	function getAmsGrdCVF(){
		$this->getAmsGridCVF = 1;
		return $this->getWVSummery();
	}

	function valNewRecordCcHis($patient_id, $sel=" * ",$LF="0"){
		//CHECK IF OLD RECORDS OF THIS PATIENT EXISTS
		$LF = ($LF == "1") ? "AND chart_master_table.finalize = '1' " : "";
		$sql = "SELECT ".$sel." FROM chart_master_table ".
			   "INNER JOIN chart_left_cc_history ON chart_master_table.id = chart_left_cc_history.form_id ".
			   "WHERE chart_master_table.patient_id = '$patient_id' AND chart_master_table.delete_status='0' AND chart_master_table.purge_status='0' ".
			   "AND chart_master_table.record_validity = '1' AND memo='0' ".
			   $LF.
			   "ORDER BY update_date DESC, chart_master_table.id DESC LIMIT 0,1 ";	
		$res = sqlQuery($sql);
		return $res;
	}

	function onlwv_GetExamSummary($enm,$post,$patient_id = '', $form_id = ''){
		
		include_once($GLOBALS['srcdir']."/classes/work_view/wv_functions.php");
		include_once($GLOBALS['srcdir']."/classes/work_view/ChartNote.php");
		include_once($GLOBALS['srcdir']."/classes/work_view/Onload.php");
		
		$chartNoteObj = New ChartNote($patient_id, $form_id);
		$onLoadObj = New Onload();
		
		$oneeye = $post["oe"];
		
		switch($enm){
			case "Pupil":
			case "pupil":
			include_once($GLOBALS['srcdir']."/classes/SaveFile.php");
			include_once($GLOBALS['srcdir']."/classes/work_view/ExamXml.php");	
			include_once($GLOBALS['srcdir']."/classes/work_view/Pupil.php");
			
			//object Chart Rec Archive --
			$oChartRecArc = new ChartRecArc($patient_id,$form_id,$_SESSION['authId']);
			//---		
			
			$echo="";
			//c2-chart_pupil-------
			$oPupil = new Pupil($patient_id,$form_id);			
			$sql = "SELECT ".
				 "c2.isPositive AS flagPosPupil, c2.wnl AS flagWnlPupil, c2.examinedNoChange AS chPupil, ".
				 "c2.sumOdPupil, c2.sumOsPupil, c2.pupil_id, ".
				 "c2.wnlPupilOd, c2.wnlPupilOs, c2.descPupil,c2.perrla, ".
				 "c2.statusElem AS se_pupil, c2.other_values, c2.modi_note_od, c2.modi_note_os,c2.modi_note_Arr,  ".
				 "c2.wnl_value ".
				 "FROM chart_master_table c1 ".
				 "LEFT JOIN chart_pupil c2 ON c2.formId = c1.id  AND c2.purged='0'  ".
				 "WHERE c1.id = '".$form_id."' AND c1.patient_id = '".$patient_id."' ";	

			$row = sqlQuery($sql);
			//$row=$oPupil->sqlExe($sql);
			if($row != false){
				$elem_noChangePupil=assignZero($row["chPupil"]);
				$elem_wnlPupil=assignZero($row["flagWnlPupil"]);
				$elem_posPupil=assignZero($row["flagPosPupil"]);
				//Pupil
				$elem_wnlPupilOd=assignZero($row["wnlPupilOd"]);
				$elem_wnlPupilOs=assignZero($row["wnlPupilOs"]);
				$elem_perrlaPupil = assignZero($row["perrla"]);
				$elem_se_Pupil = $row["se_pupil"];
				$elem_sumOdPupil = $row["sumOdPupil"];
				$elem_sumOsPupil = $row["sumOsPupil"];
				$elem_pupil_id = $row["pupil_id"];
				$elem_txtDesc_pupil = stripslashes(trim($row["descPupil"]));
				$elem_other_values = $row["other_values"];
				$elem_wnl_value = $row["wnl_value"];
				
				$mnOd=$row["modi_note_od"];
				$mnOs=$row["modi_note_os"];
				if($row["modi_note_Arr"]!='')
				$arrHx['Pupil'] = unserialize($row["modi_note_Arr"]);
				
			}
			
			//previous chart Notes summary
			if(empty($elem_pupil_id)){
				
				//if(empty($elem_pupil_id) ){  /* Show Previous values in finalized chart also ~&& ($finalize_flag == 0)~ */
					$tmp = "";
					$tmp = " c2.isPositive AS flagPosPupil, c2.wnl AS flagWnlPupil, 
						     c2.examinedNoChange AS chPupil, ";
					$tmp .= " c2.wnlPupilOd, c2.wnlPupilOs, c2.descPupil, c2.perrla, ";
					$tmp .= " c2.sumOdPupil, c2.sumOsPupil, c2.pupil_id, c2.other_values, c2.wnl_value, ";
					$tmp .= " c2.statusElem as se_pupil ";

					//$res = valNewRecordPupil($patient_id, $tmp);
					$elem_dos=$oPupil->getDos();
					$elem_dos=wv_formatDate($elem_dos,0,0,"insert");
					//$elem_dos=$oPupil->formatDate($elem_dos,0,0,"insert");
					$res = $oPupil->getLastRecord($tmp,0,$elem_dos);
					if($res!=false){$row=$res;}else{$row=false;}
					//for($i=0;$row=sqlFetchArray($res);$i++)	{
					if($row!=false){
						$elem_sumOdPupil = $row["sumOdPupil"];
						$elem_sumOsPupil = $row["sumOsPupil"];
						$elem_wnlPupil=assignZero($row["flagWnlPupil"]);
						$elem_posPupil=assignZero($row["flagPosPupil"]);
						$elem_wnlPupilOd=assignZero($row["wnlPupilOd"]);
						$elem_wnlPupilOs=assignZero($row["wnlPupilOs"]);
						$elem_txtDesc_pupil = stripslashes($row["descPupil"]);
						$elem_perrlaPupil =assignZero($row["perrla"]);
						$elem_other_values = $row["other_values"];
						$elem_wnl_value = $row["wnl_value"];
						$elem_se_Pupil_prev = $row["se_pupil"];
					}
					//BG
					$bgColor_pupil = "bgSmoke";

				//}
			}
			
			//Perrla
			if($elem_perrlaPupil == 1){
				$elem_sumOdPupil = $elem_sumOsPupil = "PERRLA";
			}
			
			//Color
			$cnm = $flgSe_Pupil_Od = $flgSe_Pupil_Os = "";
			if(!isset($bgColor_pupil)){
				//$elem_se_Pupil
				if(!empty($elem_se_Pupil)){
					$tmpArrSe = $chartNoteObj->se_elemStatus("PUPIL","0",$elem_se_Pupil);
					$flgSe_Pupil_Od = $tmpArrSe["Pupil"]["od"];
					$flgSe_Pupil_Os = $tmpArrSe["Pupil"]["os"];
				}
			}else{
				if(!empty($elem_se_Pupil_prev)){
					$tmpArrSe_prev = $chartNoteObj->se_elemStatus("PUPIL","0",$elem_se_Pupil_prev);
					$flgSe_Pupil_Od_prev = $tmpArrSe_prev["Pupil"]["od"];
					$flgSe_Pupil_Os_prev = $tmpArrSe_prev["Pupil"]["os"];
				}
			}

			//Wnl			
			$wnlString = !empty($elem_wnl_value) ? $elem_wnl_value : $chartNoteObj->getExamWnlStr("Pupil", $patient_id, $form_id);//PERRLA, -ve APD
			$wnlStringOd = $wnlStringOs = $wnlString; 
			
			/*if(empty($flgSe_Pupil_Od) && empty($flgSe_Pupil_Od_prev) && !empty($elem_wnlPupilOd)){ $tmp = getExamWnlStr_fromPrvExm("Pupil", $patient_id, $form_id, "OD"); if(!empty($tmp)){ $wnlStringOd = $tmp;}  }
			if(empty($flgSe_Pupil_Os) && empty($flgSe_Pupil_Os_prev) && !empty($elem_wnlPupilOs)){  $tmp = getExamWnlStr_fromPrvExm("Pupil", $patient_id, $form_id, "OS"); if(!empty($tmp)){ $wnlStringOs = $tmp;}  }*/
			
			
			if(empty($flgSe_Pupil_Od) && empty($flgSe_Pupil_Od_prev) && !empty($elem_wnlPupilOd)){ $tmp = $chartNoteObj->getExamWnlStr_fromPrvExm("Pupil", "OD"); if(!empty($tmp)){ $wnlStringOd = $tmp;}  }
			if(empty($flgSe_Pupil_Os) && empty($flgSe_Pupil_Os_prev) && !empty($elem_wnlPupilOs)){  $tmp = $chartNoteObj->getExamWnlStr_fromPrvExm("Pupil", "OS"); if(!empty($tmp)){ $wnlStringOs = $tmp;}  }
			
			list($elem_sumOdPupil,$elem_sumOsPupil) = $onLoadObj->setWnlValuesinSumm(array("wValOd"=>$wnlStringOd,"wValOs"=>$wnlStringOs,
										"wOd"=>$elem_wnlPupilOd,"sOd"=>$elem_sumOdPupil,
										"wOs"=>$elem_wnlPupilOs,"sOs"=>$elem_sumOsPupil));
			
			//Nochanged
			if(!empty($elem_se_Pupil)&&strpos($elem_se_Pupil,"=1")!==false){
				$elem_noChangePupil=1;
			}
			
			//Archive Pupil --
			if($bgColor_pupil != "bgSmoke"){ //Show work for current records and not for previous carried forward values
			
			$arrDivArcCmn=array();
			$oChartRecArc->setChkTbl("chart_pupil");
			$arrInpArc = array("elem_sumOdPupil"=>array("sumOdPupil",$elem_sumOdPupil,"","wnlPupilOd",$wnlString,$mnOd),
								"elem_sumOsPupil"=>array("sumOsPupil",$elem_sumOsPupil,"","wnlPupilOs",$wnlString,$mnOs));
			$arTmpRecArc = $oChartRecArc->getArcRec($arrInpArc);
			//OD
			if(!empty($arTmpRecArc["div"]["elem_sumOdPupil"])){
				//echo $arTmpRecArc["div"]["elem_sumOdPupil"];
				
				$arrDivArcCmn["Pupil"]["OD"]=$arTmpRecArc["div"]["elem_sumOdPupil"];				
				$moeArc_od = $arTmpRecArc["js"]["elem_sumOdPupil"];
				$flgArcColor_od = $arTmpRecArc["css"]["elem_sumOdPupil"];
				if(!empty($arTmpRecArc["curText"]["elem_sumOdPupil"])) $elem_sumOdPupil = $arTmpRecArc["curText"]["elem_sumOdPupil"];
			}else{
				$moeArc_od = $flgArcColor_od="";
			}
			//OS
			if(!empty($arTmpRecArc["div"]["elem_sumOsPupil"])){
				//echo $arTmpRecArc["div"]["elem_sumOsPupil"];
				$arrDivArcCmn["Pupil"]["OS"]=$arTmpRecArc["div"]["elem_sumOsPupil"];
				$moeArc_os = $arTmpRecArc["js"]["elem_sumOsPupil"];
				$flgArcColor_os = $arTmpRecArc["css"]["elem_sumOsPupil"];
				if(!empty($arTmpRecArc["curText"]["elem_sumOsPupil"])) $elem_sumOsPupil = $arTmpRecArc["curText"]["elem_sumOsPupil"];
			}else{
				$moeArc_os = $flgArcColor_os="";
			}
			
			}//bgsmoke
			//Archive Pupil --			
			
			//Purged --------
			$htmPurge = $this->getPurgedHtm("Pupil",array(),$patientId,$form_id);
			//Purged --------
			
			//Modified Notes ----
			//if Edit is not Done && modified Notes exists
			if(!empty($mnOd) && empty($moeArc_od)){ //Od
				list($moeMN_od,$tmpDiv)=$onLoadObj->getModiNoteConDiv("elem_sumOdPupil", $mnOd);
				//echo $tmpDiv;
				$arrDivArcCmn["Pupil"]["OD"]=$tmpDiv;
			}else{
				$moeMN_od="";
			}
			if(!empty($mnOs) &&empty($moeArc_os)){ //Os
				list($moeMN_os,$tmpDiv)=$onLoadObj->getModiNoteConDiv("elem_sumOsPupil", $mnOs);
				//echo $tmpDiv;
				$arrDivArcCmn["Pupil"]["OS"]=$tmpDiv;
			}else{
				$moeMN_os="";
			}	
			//Modified Notes ----
			
			//create common div and echo ---
			//list($moeMN,$tmpDiv) = mkDivArcCmn("Pupil",$arrDivArcCmn);
			if($post["webservice"] != "1"){
				list($moeMN,$tmpDiv) = $onLoadObj->mkDivArcCmnNew("Pupil",$arrDivArcCmn,$arrHx);
			}
			
			$echo.= $tmpDiv;
			//echo "<xmp>CHECKKKKK: ".$tmpDiv."</xmp>";
			
			//---------
			
			$arr=array();
			$arr["ename"] = "Pupil";
			$arr["subExm"][0] = $onLoadObj->getArrExms_ms(array("enm"=>"Pupil",
											"sOd"=>$elem_sumOdPupil,"sOs"=>$elem_sumOsPupil,
											"fOd"=>$flgSe_Pupil_Od,"fOs"=>$flgSe_Pupil_Os,"pos"=>$elem_posPupil,
											//"arcJsOd"=>$moeArc_od,"arcJsOs"=>$moeArc_os,
											"arcCssOd"=>$flgArcColor_od,"arcCssOs"=>$flgArcColor_os
											//"mnOd"=>$moeMN_od,"mnOs"=>$moeMN_os
											));
			$arr["nochange"] = $elem_noChangePupil;
			$arr["oe"] = $oneeye;
			$arr["desc"] = $elem_txtDesc_pupil;
			$arr["bgColor"] = "".$bgColor_pupil;
			$arr["other_values"] = $elem_other_values;
			$arr["htmPurge"] = $htmPurge;
			$arr["moeMN"] = $moeMN;
			$arr["exm_flg_se"] = array($flgSe_Pupil_Od,$flgSe_Pupil_Os);
			$arr["elem_hidden_fields"] = array("elem_se_Pupil"=>$elem_se_Pupil, "elem_posPupil"=>$elem_posPupil, "elem_wnlPupil"=>$elem_wnlPupil);			
			
			if($post["webservice"] == "1"){
			$echo ="";
			$str = $this->getSummaryHTML_appwebservice($arr);
			
			}else{
			
			$str = getSummaryHTML($arr);
			
			}
			
			//---------
			$echo.= $str;
			
			return $echo;
			//c2-chart_pupil-------
			break;

			case "EOM":
			case "eom":
			include_once($GLOBALS['srcdir']."/classes/work_view/EOM.php");
			//require("common/ChartRecArc.php");
            //object Chart Rec Archive --
			$oChartRecArc = new ChartRecArc($patient_id,$form_id,$_SESSION['authId']);
			//---
			$echo="";

			$oEOM=new EOM($patient_id,$form_id);			
			$sql ="SELECT ".
				"c3.isPositive AS flagPosEom, c3.wnl AS flagWnlEom, c3.examined_no_change  AS chEom, ".
				"c3.examined_no_change2, c3.examined_no_change3, c3.wnl_3, ".
				"c3.sumEom, c3.eomDrawing, c3.eom_id, c3.descEom, c3.statusElem, ".
				"c3.modi_note, c3.modi_note_Draw, ".
				"c3.modi_note_Arr, ".
				"c3.eomDrawing_2, c3.idoc_drawing_id, c3.wnl_value ".
				"FROM chart_master_table c1 ".
				"LEFT JOIN chart_eom c3 ON c3.form_id = c1.id AND c3.purged='0'  ".
				"WHERE c1.id = '".$form_id."' AND c1.patient_id='".$patient_id."' ";
			$row = sqlQuery($sql);
			//$row=$oEOM->sqlExe($sql);
			
			if($row != false){
				if(!empty($row["chEom"])&&!empty($row["examined_no_change2"])&&!empty($row["examined_no_change3"])){
					$elem_noChangeEom=assignZero($row["chEom"]);
				}
				$elem_wnlEom=assignZero($row["flagWnlEom"]);
				$elem_posEom=assignZero($row["flagPosEom"]);
				$elem_eomDraw = $row["eomDrawing"];
				$elem_sumEom = $row["sumEom"];
				$elem_eom_id = $row["eom_id"];
				$elem_txtDesc_eom = stripslashes(trim($row["descEom"]));
				$elem_se_Eom=$row["statusElem"];
				$modi_note = $row["modi_note"];
				$modi_note_Draw = $row["modi_note_Draw"];
				
				$elem_examined_no_change3=$row["examined_no_change3"];
				$elem_wnl_3=$row["wnl_3"];
				$elem_wnl_value = $row["wnl_value"];
				
				if($row["modi_note_Arr"]!='')				
				$arrHx['EOM'] = unserialize($row["modi_note_Arr"]);
				
				if(!empty($row["idoc_drawing_id"])) {
					$drawdocId = $elem_eom_id;
				}elseif( !empty($row["eomDrawing_2"]) ){
					$ardrawApp = array($row["eomDrawing_2"]);
				}
				
			}

			//Previous 
			if(empty($elem_eom_id)){
				$tmp = "";
				$tmp = "c2.isPositive AS flagPosEom, c2.wnl AS flagWnlEom, c2.examined_no_change  AS chEom, ";
				$tmp .= "c2.descEom, wnl_3, examined_no_change3, ";
				$tmp .= "c2.sumEom, c2.eom_id, ";
				$tmp .= "c2.eomDrawing_2, c2.idoc_drawing_id, c2.wnl_value ";
				
				$elem_dos=$oEOM->getDos();
				$elem_dos=wv_formatDate($elem_dos,0,0,"insert");
				//$elem_dos=$oEOM->formatDate($elem_dos,0,0,"insert");
				$res = $oEOM->getLastRecord($tmp,0,$elem_dos);
				//pre($res);die;
				if($res!=false){$row=$res;}else{$row=false;}
				//$res = valNewRecordEom($patient_id, $tmp);
				if($row!=false){
				//for($i=0;$row=sqlFetchArray($res);$i++)	{
					$elem_sumEom = $row["sumEom"];
					$elem_wnlEom=assignZero($row["flagWnlEom"]);
					$elem_posEom=assignZero($row["flagPosEom"]);
					$elem_eomDraw = $row["eomDrawing"];
					$elem_txtDesc_eom = stripslashes($row["descEom"]);
					$elem_examined_no_change3=$row["examined_no_change3"];
					$elem_wnl_3=$row["wnl_3"];
					$elem_wnl_value = $row["wnl_value"];
					
					if(!empty($row["idoc_drawing_id"])) {
						$drawdocId = $row["eom_id"];
					}elseif( !empty($row["eomDrawing_2"]) ){
						$ardrawApp = array($row["eomDrawing_2"]);
					}
					
				}
				//BG
				$bgColor_eom = "bgSmoke";
			}
			
			//StatusFlag
			$flgSe_Eom = "";
			if(!isset($bgColor_eom)){
				if(!empty($elem_se_Eom) && strpos($elem_se_Eom,'Eom=1')!==false){
					$flgSe_Eom=1;	
				}
			}

			//WNL
			$wnlString = !empty($elem_wnl_value) ? $elem_wnl_value : $chartNoteObj->getExamWnlStr("EOM", $patient_id, $form_id);
			list($elem_sumEom) = $onLoadObj->setWnlValuesinSumm(array("wVal"=>$wnlString,
															"wOd"=>$elem_wnlEom,"sOd"=>$elem_sumEom));
			//---------			
			
			//Nochanged
			if(!empty($elem_se_Eom)&&strpos($elem_se_Eom,"=1")!==false){
				$elem_noChangeEom=1;
			}
			
			//Archive EOM --
			if($bgColor_eom != "bgSmoke"){
			
			$arrDivArcCmn=array();
			$oChartRecArc->setChkTbl("chart_eom");
			$arrInpArc = array("elem_sumOdEOM"=>array("sumEom",$elem_sumEom,"","wnl",$wnlString,$modi_note));
			$arTmpRecArc = $oChartRecArc->getArcRec($arrInpArc);
			//print_r($arTmpRecArc);
			//OD
			if(!empty($arTmpRecArc["div"]["elem_sumOdEOM"])){
				//echo $arTmpRecArc["div"]["elem_sumOdEOM"];
				$arrDivArcCmn["EOM"]["OD"]= $arTmpRecArc["div"]["elem_sumOdEOM"];
				$moeArc_od = $arTmpRecArc["js"]["elem_sumOdEOM"];
				$flgArcColor_od = $arTmpRecArc["css"]["elem_sumOdEOM"];
				if(!empty($arTmpRecArc["curText"]["elem_sumOdEOM"])) 
					$elem_sumEom = $arTmpRecArc["curText"]["elem_sumOdEOM"];
			}else{
				$moeArc_od = $flgArcColor_od="";
			}
			
			}//
			//Archive EOM --
			
			//
			
			$flgSe_Draw_Od = $flgSe_Draw_Os = "0";
			$tmpArrSe=array();
			if(!isset($bgColor_eom)){
				if(!empty($elem_se_Eom)){
					if(strpos($elem_se_Eom,"Eom3=1")!==false){ $flgSe_Draw_Od ="1"; $flgSe_Draw_Os ="1";  }
				}
			}	
			//--			
			
			
			//Purged --------
			$htmPurge = $this->getPurgedHtm("EOM",array(),$patientId,$form_id);
			//Purged --------
			
			//Modified Notes ----
			//if Edit is not Done && modified Notes exists
			if(!empty($modi_note) && empty($moeArc_od)){ //Od
				list($moeMN_od,$tmpDiv)=$onLoadObj->getModiNoteConDiv("elem_sumOdEOM", $modi_note);
				//echo $tmpDiv;
				$arrDivArcCmn["EOM"]["OD"]= $tmpDiv;
			}else{
				$moeMN_od="";
			}

			//Draw
			if(!empty($modi_note_Draw)){ //Od
				list($moeMN_od,$tmpDiv)=$onLoadObj->getModiNoteConDiv("elem_drawOdEOM", $modi_note_Draw);
				//echo $tmpDiv;
				$arrDivArcCmn["Drawing"]["OD"]= $tmpDiv;
			}else{
				$moeMN_od="";
			}

			//Modified Notes ----
			
			//create common div and echo ---			
			//list($moeMN,$tmpDiv) = mkDivArcCmn("EOM",$arrDivArcCmn);
			if($post["webservice"] != "1"){
			list($moeMN,$tmpDiv) = $onLoadObj->mkDivArcCmnNew("EOM",$arrDivArcCmn,$arrHx);
			}
			$echo.= $tmpDiv;
			//echo "<xmp>CHECKKKKK: ".$tmpDiv."</xmp>";
			
			$arr=array();
			$arr["ename"] = "EOM";
			$arr["subExm"][0] = $onLoadObj->getArrExms_ms(array("enm"=>"EOM",
											"sOd"=>$elem_sumEom,
											"fOd"=>$flgSe_Eom,"pos"=>$elem_posEom,
											//"arcJsOd"=>$moeArc_od, "arcJsOs"=>"",
											"arcCssOd"=>$flgArcColor_od, "arcCssOs"=>""
											//"mnOd"=>$moeMN_od,"mnOs"=>""
											));
			$arr["nochange"] = $elem_noChangeEom;
			$arr["desc"] = $elem_txtDesc_eom;
			$arr["bgColor"] = "".$bgColor_eom;
			$arr["htmPurge"] = $htmPurge;
			$arr["moeMN"] = $moeMN;
			$arr["flgGetDraw"] = $this->onwv_isDrawingChanged(array($elem_wnlDrawRvOd,$elem_wnlDrawRvOs,$elem_ncDrawRv,$elem_ncDrawRv));
			
			$arr["drawdocId"] = $drawdocId;
			$arr["drawapp"] = $ardrawApp;
			$arr["drawSE"] = array($flgSe_Draw_Od,$flgSe_Draw_Os);
			$arr["elem_hidden_fields"] = array("elem_se_Eom"=>$elem_se_Eom, "elem_posEom"=>$elem_posEom, "elem_wnlEom"=>$elem_wnlEom);
			
			
			if($post["webservice"] == "1"){	
				$echo ="";	
				$str = $this->getSummaryHTML_appwebservice($arr);			
			}else{			
				$str = getSummaryHTML($arr);			
			}
			
			$echo.= $str;
			return $echo;
			//---------

			break;

			case "External":
			case "external":
			
			include_once($GLOBALS['srcdir']."/classes/work_view/ExternalExam.php");
			//require("common/ChartRecArc.php");
           //object Chart Rec Archive --
			$oChartRecArc = new ChartRecArc($patient_id,$form_id,$_SESSION['authId']);
			//---
			$echo="";
			
			$oEE=new ExternalExam($patient_id,$form_id);
			$sql ="SELECT ".
				"c4.isPositive, c4.posEe AS flagPosEe, c4.posDraw, c4.wnl, c4.wnlEe AS flagWnlEe, c4.wnlDraw, ".
				"c4.examined_no_change, c4.ncEe AS chEe, c4.ncDraw, ".
				"c4.ee_drawing, c4.external_exam_summary, ".
				"c4.sumOsEE, c4.ee_id, ".
				"c4.wnlEeOd, c4.wnlEeOs, c4.descExternal, c4.modi_note_od, c4.modi_note_os, c4.modi_note_Draw, ".
				"c4.statusElem AS se_ee, c4.modi_note_Arr, ".
				"c4.ee_drawing, c4.idoc_drawing_id, c4.wnl_value ".
				"FROM chart_master_table c1 ".
				"LEFT JOIN chart_external_exam c4 ON c4.form_id = c1.id AND c4.purged='0'  ".
				"WHERE c1.id = '".$form_id."' AND c1.patient_id='".$patient_id."' ";			
			$row = sqlQuery($sql);
			//$row=$oEE->sqlExe($sql);			
			if($row != false){
				$elem_noChangeEe=assignZero($row["chEe"]);
				$elem_wnlEe=assignZero($row["flagWnlEe"]);
				$elem_posEe=assignZero($row["flagPosEe"]);
				$elem_wnlEeOd=assignZero($row["wnlEeOd"]);
				$elem_wnlEeOs=assignZero($row["wnlEeOs"]);
				$elem_se_Ee = $row["se_ee"];
				$elem_eeDraw = $this->isAppletDrawn($row["ee_drawing"]);
				$elem_sumOdEE = $row["external_exam_summary"];
				$elem_sumOsEE = $row["sumOsEE"];
				$elem_ee_id = $row["ee_id"];
				$elem_txtDesc_ee = stripslashes(trim($row["descExternal"]));
				
				$elem_wnlDraw=$row["wnlDraw"];
				$elem_ncDraw=$row["ncDraw"];
				$elem_wnl_value=$row["wnl_value"];
				
				$mnOd = $row["modi_note_od"];
				$mnOs = $row["modi_note_os"];
				$modi_note_Draw= $row["modi_note_Draw"];
				if($row["modi_note_Arr"]!='')
				$arrHx['External'] = unserialize($row["modi_note_Arr"]);
				
				if(!empty($row["idoc_drawing_id"])) {
					$drawdocId = $elem_ee_id;
				}elseif( !empty($row["ee_drawing"]) ){
					$ardrawApp = array($row["ee_drawing"]);
				}
				
			}

			//Previous
			if(empty($elem_ee_id)){
				$tmp = "";
				$tmp = " c2.isPositive, c2.posEe AS flagPosEe, 
						 c2.wnl, c2.wnlEe AS flagWnlEe, c2.examined_no_change, c2.ncEe  AS chEe,c2.wnlDraw,c2.ncDraw, ";
				$tmp .= " c2.wnlEeOd, c2.wnlEeOs, c2.descExternal, ";
				$tmp .= " c2.ee_drawing, c2.external_exam_summary, 
							c2.sumOsEE, c2.ee_id, ".
							"c2.ee_drawing, c2.idoc_drawing_id, c2.wnl_value, ".
							"c2.statusElem AS se_ee ";

				//$res = valNewRecordExter($patient_id, $tmp);
				$elem_dos=$oEE->getDos();
				$elem_dos=wv_formatDate($elem_dos,0,0,"insert");
				//$elem_dos=$oEE->formatDate($elem_dos,0,0,"insert");
				$res = $oEE->getLastRecord($tmp,0,$elem_dos);
				if($res!=false){$row=$res;}else{$row=false;}
				
				if($row!=false){
				//for($i=0;$row=sqlFetchArray($res);$i++)	{
					$elem_sumOdEE = $row["external_exam_summary"];
					$elem_sumOsEE = $row["sumOsEE"];
					$elem_wnlEe=assignZero($row["flagWnlEe"]);
					$elem_posEe=assignZero($row["flagPosEe"]);
					$elem_eeDraw = $this->isAppletDrawn($row["ee_drawing"]);
					$elem_wnlEeOd=assignZero($row["wnlEeOd"]);
					$elem_wnlEeOs=assignZero($row["wnlEeOs"]);
					$elem_txtDesc_ee = stripslashes($row["descExternal"]);
					$elem_wnlDraw=$row["wnlDraw"];
					//$elem_ncDraw=$row["ncDraw"];
					$elem_wnl_value=$row["wnl_value"];
					$elem_se_Ee_prev = $row["se_ee"];
					
					if(!empty($row["idoc_drawing_id"])) {
						$drawdocId = $row["ee_id"];
					}elseif( !empty($row["ee_drawing"]) ){
						$ardrawApp = array($row["ee_drawing"]);
					}
					
				}
				//BG
				$bgColor_external = "bgSmoke";
			}			
			
			//Color
			$flgSe_Ee_Od = $flgSe_Ee_Os = "";
			$flgSe_Draw_Od = $flgSe_Draw_Os = "0";
			if(!isset($bgColor_external)){
				if(!empty($elem_se_Ee)){
					$tmpArrSe = $chartNoteObj->se_elemStatus("EXTERNAL","0",$elem_se_Ee);
					$flgSe_Ee_Od = $tmpArrSe["Con"]["od"];
					$flgSe_Ee_Os = $tmpArrSe["Con"]["os"];
					$flgSe_Draw_Od=$tmpArrSe["Draw"]["od"];
					$flgSe_Draw_Os=$tmpArrSe["Draw"]["os"];
					
				}
			}else{
				if(!empty($elem_se_Ee_prev)){
					$tmpArrSe_prev = $chartNoteObj->se_elemStatus("EXTERNAL","0",$elem_se_Ee_prev);
					$flgSe_Ee_Od_prev = $tmpArrSe_prev["Con"]["od"];
					$flgSe_Ee_Os_prev = $tmpArrSe_prev["Con"]["os"];
					$flgSe_Draw_Od_prev=$tmpArrSe_prev["Draw"]["od"];
					$flgSe_Draw_Os_prev=$tmpArrSe_prev["Draw"]["os"];
				}
			}
			
			//Wnl
			$wnlString = !empty($elem_wnl_value) ? $elem_wnl_value : $chartNoteObj->getExamWnlStr("External", $patient_id, $form_id);
			$wnlStringOd = $wnlStringOs = $wnlString; 
			
			if(empty($flgSe_Ee_Od) && empty($flgSe_Ee_Od_prev) && !empty($elem_wnlEeOd)){ $tmp = $chartNoteObj->getExamWnlStr_fromPrvExm("External", "OD"); if(!empty($tmp)){ $wnlStringOd = $tmp;}  }
			if(empty($flgSe_Ee_Os) && empty($flgSe_Ee_Os_prev) && !empty($elem_wnlEeOs)){  $tmp = $chartNoteObj->getExamWnlStr_fromPrvExm("External", "OS"); if(!empty($tmp)){ $wnlStringOs = $tmp;}  }
			
			list($elem_sumOdEE,$elem_sumOsEE) = $onLoadObj->setWnlValuesinSumm(array("wValOd"=>$wnlStringOd,"wValOs"=>$wnlStringOs,
						"wOd"=>$elem_wnlEeOd,"sOd"=>$elem_sumOdEE,
						"wOs"=>$elem_wnlEeOs,"sOs"=>$elem_sumOsEE));
			
			//Nochanged
			if(!empty($elem_se_Ee)&&strpos($elem_se_Ee,"=1")!==false){
				$elem_noChangeEe=1;
			}			
			
			//Archive External --
			if($bgColor_external != "bgSmoke"){
			
			$arrDivArcCmn=array();
			$oChartRecArc->setChkTbl("chart_external_exam");
			$arrInpArc = array("elem_sumOdExternal"=>array("external_exam_summary",$elem_sumOdEE,"","wnlEeOd",$wnlString,$mnOd),
								"elem_sumOsExternal"=>array("sumOsEE",$elem_sumOsEE,"","wnlEeOs",$wnlString,$mnOs));
			$arTmpRecArc = $oChartRecArc->getArcRec($arrInpArc);
			//OD
			if(!empty($arTmpRecArc["div"]["elem_sumOdExternal"])){
				//echo $arTmpRecArc["div"]["elem_sumOdExternal"];
				$arrDivArcCmn["External"]["OD"]= $arTmpRecArc["div"]["elem_sumOdExternal"];
				$moeArc_od = $arTmpRecArc["js"]["elem_sumOdExternal"];
				$flgArcColor_od = $arTmpRecArc["css"]["elem_sumOdExternal"];
				if(!empty($arTmpRecArc["curText"]["elem_sumOdExternal"])) $elem_sumOdEE = $arTmpRecArc["curText"]["elem_sumOdExternal"];
			}else{
				$moeArc_od=$flgArcColor_od="";
			}
			//OS
			if(!empty($arTmpRecArc["div"]["elem_sumOsExternal"])){
				//echo $arTmpRecArc["div"]["elem_sumOsExternal"];
				$arrDivArcCmn["External"]["OS"]= $arTmpRecArc["div"]["elem_sumOsExternal"];
				$moeArc_os = $arTmpRecArc["js"]["elem_sumOsExternal"];
				$flgArcColor_os = $arTmpRecArc["css"]["elem_sumOsExternal"];
				if(!empty($arTmpRecArc["curText"]["elem_sumOsExternal"])) $elem_sumOsEE = $arTmpRecArc["curText"]["elem_sumOsExternal"];
			}else{
				$moeArc_os = $flgArcColor_os = "";
			}
			
			}//
			//Archive External --					
			
			//Purged --------
				$htmPurge = $this->getPurgedHtm("External",array(),$patientId,$form_id);
			//Purged --------
			
			//Modified Notes ----
			//if Edit is not Done && modified Notes exists
			if(!empty($mnOd) && empty($moeArc_od)){ //Od
				list($moeMN_od,$tmpDiv)=$onLoadObj->getModiNoteConDiv("elem_sumOdExternal", $mnOd);
				//echo $tmpDiv;
				$arrDivArcCmn["External"]["OD"]=$tmpDiv;
			}else{
				$moeMN_od="";
			}
			if(!empty($mnOs) &&empty($moeArc_os)){ //Os
				list($moeMN_os,$tmpDiv)=$onLoadObj->getModiNoteConDiv("elem_sumOsExternal", $mnOs);
				//echo $tmpDiv;
				$arrDivArcCmn["External"]["OS"]=$tmpDiv;
			}else{
				$moeMN_os="";
			}			
			
			//Drawing
			if(!empty($modi_note_Draw)){ //Os
				list($moeMN["od"]["draw"],$tmpDiv)=$onLoadObj->getModiNoteConDiv("elem_drawOdEE", $modi_note_Draw);
				//echo $tmpDiv;
				$arrDivArcCmn["Drawing"]["OD"]=$tmpDiv;
			}else{
				$moeMN["od"]["draw"]="";
			}
			//Modified Notes ----			
			
			//create common div and echo ---
			//list($moeMN,$tmpDiv) = mkDivArcCmn("External",$arrDivArcCmn);
			if($post["webservice"] != "1"){
			list($moeMN,$tmpDiv) = $onLoadObj->mkDivArcCmnNew("External",$arrDivArcCmn,$arrHx);
			}
			$echo.= $tmpDiv;
			//echo "<xmp>CHECKKKKK: ".$tmpDiv."</xmp>";			
			
			//---------

			$arr=array();
			$arr["ename"] = "External"; 
			$arr["subExm"][0] = $onLoadObj->getArrExms_ms(array("enm"=>"External",
												"sOd"=>$elem_sumOdEE,"sOs"=>$elem_sumOsEE,
												"fOd"=>$flgSe_Ee_Od,"fOs"=>$flgSe_Ee_Os,"pos"=>$elem_posEe,
												//"arcJsOd"=>$moeArc_od,"arcJsOs"=>$moeArc_os,
												"arcCssOd"=>$flgArcColor_od,"arcCssOs"=>$flgArcColor_os
												//"mnOd"=>$moeMN_od,"mnOs"=>$moeMN_os
												));
			$arr["nochange"] = $elem_noChangeEe;
			$arr["oe"] = $oneeye;
			$arr["desc"] = $elem_txtDesc_ee;
			$arr["bgColor"] = "".$bgColor_external;
			$arr["htmPurge"] = $htmPurge;
			$arr["moeMN"] = $moeMN;
			$arr["flgGetDraw"] = $this->onwv_isDrawingChanged(array($elem_wnlDraw,$elem_wnlDraw,$elem_ncDraw,$elem_ncDraw));
			$arr["exm_flg_se"] = array($flgSe_Ee_Od,$flgSe_Ee_Os);
			
			$arr["drawdocId"] = $drawdocId;
			$arr["drawapp"] = $ardrawApp;
			$arr["drawSE"] = array($flgSe_Draw_Od,$flgSe_Draw_Os);
			$arr["elem_hidden_fields"] = array("elem_se_Ee"=>$elem_se_Ee, "elem_posEe"=>$elem_posEe, 
										"elem_wnlEe"=>$elem_wnlEe, "elem_wnlEeOd"=>$elem_wnlEeOd, "elem_wnlEeOd"=>$elem_wnlEeOd);
								
			if($post["webservice"] == "1"){
				$echo ="";
				$str = $this->getSummaryHTML_appwebservice($arr);			
			}else{			
				$str = getSummaryHTML($arr);			
			}
			
			//---------

			$echo.= $str;
			return $echo;

			break;
			
			case "L&A":
			case "la":			
				include_once($GLOBALS['srcdir']."/classes/work_view/LA.php");
				include_once($GLOBALS['srcdir']."/classes/work_view/LALoader.php");
				$oload = new LALoader($patient_id,$form_id);
				$data_html = $oload->getWorkViewSummery($post);
				return $data_html;
			break;

			case "IOP/Gonio":
			case "iop_gon":			
			include_once($GLOBALS['srcdir']."/classes/work_view/Gonio.php");
			include_once($GLOBALS['srcdir']."/classes/work_view/ChartIop.php");
			//require("common/ChartRecArc.php");
			//object Chart Rec Archive --
			$oChartRecArc = new ChartRecArc($patient_id,$form_id,$_SESSION['authId']);
			$chartIopObj = new ChartIop($patient_id,$form_id,$_SESSION['authId']);
			//---
			$echo="";
			
			$oGonio=new Gonio($patient_id,$form_id);
			$sql ="SELECT ".
				//c5-chart_iop-------
				"c5.sumOdIop, c5.sumOsIop,".
				"c5.tetracaine,c5.flourocaine,c5.alcaine,c5.iop_time, c5.iop_id, c5.anesthetic,c5.statusElem AS statusElem_IOP, ".
				"c5.modi_note_IopOd, c5.modi_note_IopOs, c5.modi_note_AnestheticOd, c5.modi_note_AnestheticOs, ".
				"c5.sumAnesOd , c5.sumAnesOs, c5.spAnesTime,c5.modi_note_iopArr,c5.modi_note_AnestheticArr,".
				//c14-chart_dialation-------
				"c14.patientAllergic, c14.pheny25, c14.tropicanide,".
				"c14.cyclogel, c14.dilated_other, c14.dilated_time,".
				"c14.dia_id, c14.eyeSide,c14.noDilation,c14.mydiacyl5, c14.dilation,c14.statusElem AS statusElem_Dilation, ".
				"c14.unableDilation, c14.modi_note_DilationOd, c14.modi_note_DilationOs, c14.sumDilation_od, c14.sumDilation_os, c14.spDialTime,c14.modi_note_Arr AS modi_note_DilaArr,".
				//C15-chart_ood ----
				"c15.ood_id, c15.ood,c15.statusElem AS statusElem_OOD, c15.eye AS eye_ood, 
				 c15.sumOOD_od, c15.sumOOD_os, c15.modi_note_OodOd, c15.modi_note_OodOs, c15.spOODTime, c15.modi_note_Arr  AS modi_note_oodArr,".
				//c6-chart_gonio-------
				"c6.gonio_od_drawing, c6.isPositive AS flagPosIopGonio, ".
				"c6.wnl AS flagWnlIopGonio, c6.examined_no_change AS chGon, ".
				"c6.gonio_os_drawing, c6.gonio_od_summary, c6.gonio_os_summary, ".
				"c6.wnlOd AS wnlGonioOd, c6.wnlOs AS wnlGonioOs, c6.desc_ig, ".
				"c6.posGonio, c6.posDraw AS posDrawGonio, c6.wnlGonio, c6.wnlDraw AS wnlDrawGonio, ".
				"c6.wnlDrawOd AS wnlDrawGonioOd, c6.wnlDrawOs AS wnlDrawGonioOs, c6.gonio_id, c6.noChange_drawing, ".
				"c6.examDateGonio, c6.idoc_drawing_id, c6.modi_note_GonioOd, c6.modi_note_GonioOs, c6.modi_note_Draw, ".
				"c6.statusElem AS se_gon, c6.modi_note_Arr as modi_note_gonioArr, c6.wnl_value ".
				"FROM chart_master_table c1 ".
				"LEFT JOIN chart_iop c5 ON c5.form_id = c1.id AND c5.purged='0' ".
				"LEFT JOIN chart_gonio c6 ON c6.form_id = c1.id AND c6.purged='0' ".
				"LEFT JOIN chart_dialation c14 ON c14.form_id = c1.id AND c14.purged='0' ".
				"LEFT JOIN chart_ood c15 ON c15.form_id = c1.id AND c15.purged='0' ".
				"WHERE c1.id = '".$form_id."' AND c1.patient_id='".$patient_id."' ";						
			$row = sqlQuery($sql);			
			//$row=$oGonio->sqlExe($sql);
			
			if($row != false){
				if(!empty($row["chGon"])&&!empty($row["noChange_drawing"])){
					$elem_noChangeGonio=assignZero($row["chGon"]); //For Gonio only
				}
				$elem_wnlIopGonio=assignZero($row["flagWnlIopGonio"]); //for gonio only
				$elem_posIopGonio=assignZero($row["flagPosIopGonio"]); // for gonio only

				$elem_tetracaine=$row["tetracaine"];
				$elem_flourocaine=$row["flourocaine"];
				$elem_alcaine=$row["alcaine"];
				$elem_iop_time=$row["iop_time"];
				$elem_posIop = 0;
				$elem_wnlIop = 0;
				$elem_wnlIopOd=0;//assignZero($row["wnlIopOd"]);
				$elem_wnlIopOs=0;//assignZero($row["wnlIopOs"]);
				$elem_sumOdIop = $row["sumOdIop"];
				$elem_sumOsIop = $row["sumOsIop"];	
				$elem_sumAnesOd = $row["sumAnesOd"];	
				$elem_sumAnesOs = $row["sumAnesOs"];	
				$elem_spAnesTime = $row["spAnesTime"];	

				$elem_posGonio=$row["posGonio"];
				$elem_posDrawGonio=$row["posDrawGonio"];
				$elem_wnlGonio=$row["wnlGonio"];
				$elem_wnlDrawGonio=$row["wnlDrawGonio"];
				//$elem_sideIop = $row["sideIop"];
				$elem_wnlGonioOd=assignZero($row["wnlGonioOd"]);
				$elem_wnlGonioOs=assignZero($row["wnlGonioOs"]);
				$elem_wnlDrawGonioOd=assignZero($row["wnlDrawGonioOd"]);
				$elem_wnlDrawGonioOs=assignZero($row["wnlDrawGonioOs"]);
				$elem_wnl_value_gonio=$row["wnl_value"];
				
				$elem_se_Gonio = $row["se_gon"];
				$elem_sumOdGon = $row["gonio_od_summary"];
				$elem_sumOsGon = $row["gonio_os_summary"];
				$elem_anesthetic = $row["anesthetic"];
				$elem_se_iop = $row["statusElem_IOP"];
				$elem_ncGonio = assignZero($row["chGon"]);
				$elem_ncDrawGonio = assignZero($row["noChange_drawing"]);
				$examdate = wv_formatDate($row["examDateGonio"]);
				//$examdate = $oGonio->formatDate($row["examDateGonio"]);
				$elem_sumAnesOd=$row["sumAnesOd"];
				$elem_sumAnesOs=$row["sumAnesOs"];
				$elem_spAnesTime=$row["spAnesTime"];

				//Dialation
				$elem_pheny25 = $row["pheny25"];
				$elem_tropicanide = $row["tropicanide"];
				$elem_cyclogel = $row["cyclogel"];
				$elem_dilated_other = $row["dilated_other"];
				$elem_dilated_time = $row["dilated_time"];
				$elem_eyeSide = $row["eyeSide"];
				$patientAllergic=assignZero($row["patientAllergic"]);
				$elem_no_dilation = $row["noDilation"];
				$elem_mydiacyl5=$row["mydiacyl5"];
				$elem_dilation=$row["dilation"];
				$elem_se_dilation = $row["statusElem_Dilation"];
				$unableDilation=$row["unableDilation"];
				$dilated_mm=$row["dilated_mm"];
				$elem_sumDilationOd =$row["sumDilation_od"];
				$elem_sumDilationOs =$row["sumDilation_os"];
				$elem_spDialTime = $row["spDialTime"];				
				
				//C15-chart_ood
				$ood = $row["ood"];
				$statusElem_OOD = $row["statusElem_OOD"]; 
				$elem_spOODTime = $row["spOODTime"];
				$elem_sumOOD_od = $row["sumOOD_od"];
				$elem_sumOOD_os = $row["sumOOD_os"];
				$modi_note_OodOd = $row["modi_note_OodOd"];
				$modi_note_OodOs = $row["modi_note_OodOs"];			

				$elem_iop_id = $row["iop_id"];
				$elem_gonio_id = $row["gonio_id"];
				$elem_dia_id = $row["dia_id"];
				$elem_ood_id = $row["ood_id"];
				$elem_txtDesc_iop = stripslashes(trim($row["desc_ig"])); //Gonio
				
				$eye_ood = $row["eye_ood"];
				
				if(!empty($row["idoc_drawing_id"])) {
					$drawdocId = $elem_gonio_id;
				}elseif( !empty($row["gonio_od_drawing"]) ){
					$ardrawApp = array($row["gonio_od_drawing"]);
				}
				
				$modi_note_GonioOd=$row["modi_note_GonioOd"]; 
				$modi_note_GonioOs=$row["modi_note_GonioOs"]; 
				$modi_note_DilationOd=$row["modi_note_DilationOd"]; 
				$modi_note_DilationOs=$row["modi_note_DilationOs"]; 				
				$modi_note_IopOd=$row["modi_note_IopOd"]; 
				$modi_note_IopOs=$row["modi_note_IopOs"]; 
				$modi_note_AnestheticOd=$row["modi_note_AnestheticOd"];
				$modi_note_AnestheticOs=$row["modi_note_AnestheticOs"];
				$modi_note_Draw = $row["modi_note_Draw"];
				
				$modi_note_iopArr = unserialize($row["modi_note_iopArr"]);
				$modi_note_AnestheticArr = unserialize($row["modi_note_AnestheticArr"]);
				$modi_note_DilaArr = unserialize($row["modi_note_DilaArr"]);
				$modi_note_oodArr = unserialize($row["modi_note_oodArr"]);
				$modi_note_gonioArr = unserialize($row["modi_note_gonioArr"]);
				
					$arrHx = array();
					if(count($modi_note_iopArr)>0 && $row["modi_note_iopArr"]!='')
					$arrHx['IOP']	= $modi_note_iopArr;
					if(count($modi_note_AnestheticArr)>0 && $row["modi_note_AnestheticArr"]!='')
					$arrHx['Anesthetic']	= $modi_note_AnestheticArr;
					if(count($modi_note_DilaArr)>0 && $row["modi_note_DilaArr"]!='')
					$arrHx['Dilation'] = $modi_note_DilaArr;
					if(count($modi_note_oodArr)>0 && $row["modi_note_oodArr"]!='')
					$arrHx['Ophth. Drops'] = $modi_note_oodArr;
					if(count($modi_note_gonioArr)>0 && $row["modi_note_gonioArr"]!='')
					$arrHx['Gonio'] = $modi_note_gonioArr;
				
			}

			if(empty($elem_iop_id)){ /* Show Previous values in finalized chart also ~&& ($finalize_flag == 0)~ */
				$tmp = "";
				/*
				$tmp = "chart_iop.isPositive AS flagPosIopGonio, chart_iop.wnl AS flagWnlIopGonio, chart_iop.examined_no_change AS chIop, ";
				$tmp .= "chart_iop.tetracaine,chart_iop.flourocaine,chart_iop.alcaine,chart_iop.iop_time, ";
				$tmp .= "chart_iop.wnlIopOd AS wnlGonioOd, chart_iop.wnlIopOs AS wnlGonioOs, ";
				$tmp .= "chart_iop.gonio_od_drawing,chart_iop.gonio_os_drawing, chart_iop.desc_ig, ";
				$tmp .= "chart_iop.posGonio, chart_iop.posDraw AS posDrawGonio, chart_iop.wnlGonio, chart_iop.wnlDraw AS wnlDrawGonio, ";
				$tmp .= "chart_iop.wnlDrawOd AS wnlDrawGonioOd, chart_iop.wnlDrawOs AS wnlDrawGonioOs, ";
				$tmp .= "chart_iop.sumOdIop, chart_iop.sumOsIop, chart_iop.gonio_od_summary, chart_iop.gonio_os_summary, chart_iop.iop_id ";
				*/
				$tmp .="chart_iop.sumOdIop, chart_iop.sumOsIop,";
				$tmp .="chart_iop.tetracaine,chart_iop.flourocaine,chart_iop.alcaine,chart_iop.iop_time, chart_iop.iop_id ";
				$tmp .= "";	
				
				$res = $chartIopObj->valNewRecordIop($patient_id, $tmp);
				for($i=0;$row=sqlFetchArray($res);$i++)	{
					//$elem_sumOdIop = $row["sumOdIop"];
					//$elem_sumOsIop = $row["sumOsIop"];
					//$elem_noChangeIop=assignZero($row["chIop"]);
					//$elem_tetracaine=$row["tetracaine"];
					//$elem_flourocaine=$row["flourocaine"];
					//$elem_alcaine=$row["alcaine"];
					//$elem_iop_time=$row["iop_time"];
					$elem_posIop = 0;
					$elem_wnlIop = 0;
					$elem_wnlIopOd=0;
					$elem_wnlIopOs=0;
					/*$elem_wnlIopGonio=assignZero($row["flagWnlIopGonio"]);
					$elem_posIopGonio=assignZero($row["flagPosIopGonio"]);*/
					/*$elem_sumOdGon = $row["gonio_od_summary"];
					$elem_sumOsGon = $row["gonio_os_summary"];*/
					/*
					$Drawing_OD = $this->isAppletDrawn($row["gonio_od_drawing"]);
					$Drawing_OS= $this->isAppletDrawn($row["gonio_os_drawing"]);
					$elem_txtDesc_iop = $row["desc_ig"];
					$elem_posGonio=$row["posGonio"];
					$elem_posDrawGonio=$row["posDrawGonio"];
					$elem_wnlGonio=$row["wnlGonio"];
					$elem_wnlDrawGonio=$row["wnlDrawGonio"];
					$elem_wnlGonioOd=assignZero($row["wnlGonioOd"]);
					$elem_wnlGonioOs=assignZero($row["wnlGonioOs"]);
					$elem_wnlDrawGonioOd=assignZero($row["wnlDrawGonioOd"]);
					$elem_wnlDrawGonioOs=assignZero($row["wnlDrawGonioOs"]);
					*/
				}
				//BG
				$bgColor_IOP = "bgSmoke";
			}

			if(empty($elem_gonio_id)){ /* Show Previous values in finalized chart also ~&& ($finalize_flag == 0)~ */
				$tmp = "";
				$tmp .="c2.gonio_od_drawing, c2.isPositive AS flagPosIopGonio, c2.wnl AS flagWnlIopGonio, c2.examined_no_change AS chGon, ";
				$tmp .="c2.gonio_os_drawing, c2.gonio_od_summary, c2.gonio_os_summary, ";
				$tmp .="c2.wnlOd AS wnlGonioOd, c2.wnlOs AS wnlGonioOs, c2.desc_ig, ";
				$tmp .="c2.posGonio, c2.posDraw AS posDrawGonio, c2.wnlGonio, 
						c2.wnlDraw AS wnlDrawGonio, c2.noChange_drawing,c2.examDateGonio, c2.idoc_drawing_id, ";
				$tmp .="c2.wnlDrawOd AS wnlDrawGonioOd, c2.wnlDrawOs AS wnlDrawGonioOs, c2.gonio_id, c2.wnl_value, ";
				$tmp .= "c2.statusElem AS se_gon ";
				//$row = valNewRecordGonio($patient_id, $tmp);
				$elem_dos=$oGonio->getDos();
				$elem_dos=wv_formatDate($elem_dos,0,0,"insert");
				//$elem_dos=$oGonio->formatDate($elem_dos,0,0,"insert");
				$res = $oGonio->getLastRecord($tmp,0,$elem_dos);
				if($res!=false){$row=$res;}else{$row=false;}
				
				if($row != false){
					$elem_wnlIopGonio=assignZero($row["flagWnlIopGonio"]);
					$elem_posIopGonio=assignZero($row["flagPosIopGonio"]);
					$elem_sumOdGon = $row["gonio_od_summary"];
					$elem_sumOsGon = $row["gonio_os_summary"];

					$Drawing_OD = $this->isAppletDrawn($row["gonio_od_drawing"]);
					$Drawing_OS= $this->isAppletDrawn($row["gonio_os_drawing"]);
					$elem_txtDesc_iop = stripslashes($row["desc_ig"]);
					$elem_posGonio=$row["posGonio"];
					$elem_posDrawGonio=$row["posDrawGonio"];
					$elem_wnlGonio=$row["wnlGonio"];
					$elem_wnlDrawGonio=$row["wnlDrawGonio"];
					$elem_wnlGonioOd=assignZero($row["wnlGonioOd"]);
					$elem_wnlGonioOs=assignZero($row["wnlGonioOs"]);
					$elem_wnlDrawGonioOd=assignZero($row["wnlDrawGonioOd"]);
					$elem_wnlDrawGonioOs=assignZero($row["wnlDrawGonioOs"]);
					$elem_wnl_value_gonio=$row["wnl_value"];
					
					$elem_ncGonio = assignZero($row["chGon"]);
					$elem_ncDrawGonio = assignZero($row["noChange_drawing"]);					
					$examdate = wv_formatDate($row["examDateGonio"]);
					//$examdate = $oGonio->formatDate($row["examDateGonio"]);
					if(!empty($row["idoc_drawing_id"])) {
						$drawdocId = $row["gonio_id"];
					}elseif( !empty($row["gonio_od_drawing"]) ){
						$ardrawApp = array($row["gonio_od_drawing"]);
					}
					$elem_se_Gonio_prev = $row["se_gon"];
					
				}
				//BG
				$bgColor_gonio = "bgSmoke";
			}

			/* Stoped
			if(empty($elem_dia_id) && ($finalize_flag == 0)){
				$tmp = "";
				$tmp .= "chart_dialation.pheny25, chart_dialation.tropicanide, chart_dialation.cyclogel, chart_dialation.dilated_other, chart_dialation.dilated_time, chart_dialation.dia_id ";
				$row = valNewRecordDialation($patient_id, $tmp);
				if($row != false){
					Dialation
					$elem_pheny25 = $row["pheny25"];
					$elem_tropicanide = $row["tropicanide"];
					$elem_cyclogel = $row["cyclogel"];
					$elem_dilated_other = $row["dilated_other"];
					$elem_dilated_time = $row["dilated_time"];
				}
			}*/

			//---------

			//StatusFlag IOP
			$flgSe_IOP = 0;
			if(!isset($bgColor_IOP)){
				if(!empty($elem_se_iop)){
					$flgSe_IOP=1;	
				}
			}
			
			//Anes
			$spAnesTime="";
			$sumAnes = $sumAnes_od = $sumAnes_os = "";
			if(!empty($elem_anesthetic)){
			
				$sumAnes_od = $elem_sumAnesOd;
				$sumAnes_os = $elem_sumAnesOs;
				$spAnesTime = $elem_spAnesTime;
			
				/*
				$arrAnes=unserialize($elem_anesthetic);
				$tmp = count($arrAnes);
				$spAnesTime = $arrAnes[$tmp-1]["time"];
				
				// *
				//Test
				for($a=0;$a<$tmp;$a++){
					if((!empty($arrAnes[$a]["anes"])||!empty($arrAnes[$a]["other"]))&&!empty($arrAnes[$a]["time"])){
						
						$t = "";
						if(!empty($arrAnes[$a]["anes"])){
							$t .= $arrAnes[$a]["anes"];
						}						
						
						if(!empty($arrAnes[$a]["other"])){
							if(!empty($t)){$t .= ",";}
							$t .= "".$arrAnes[$a]["other"];
						}
						
						$t = preg_replace("/(\s)*\,(\s)*$/i", "", $t);							
						$t = str_replace(",",", ",$t); 
						$sumAnes .= $t." ".trim($arrAnes[$a]["time"])."<br/>";						
					}
				}
				
				$sumAnes_od = $sumAnes_os = $sumAnes;				
				//Test * /
				*/

			}/*
			else if(!empty($elem_tetracaine) || !empty($elem_flourocaine) || !empty($elem_alcaine)){
				$spAnesTime = $elem_iop_time;
				
				$t = "";
				if(!empty($elem_tetracaine)) { $t .= "Tetracaine,"; } 
				if(!empty($elem_flourocaine)) { $t .= "Flourocaine, "; } 
				if(!empty($elem_alcaine)) { $t .= "Alcaine "; } 
				$t .= " ".$spAnesTime;
				$sumAnes = $sumAnes_od = $sumAnes_os =  $t;
			}*/

			//Dilation --
			$spDialTime="";
			//$arrDilatInx = array("pheny25","mydiacyl5","mydiacyl1","Cyclogyl");
			//$arrDilatVals = array("Phenylephrine 2.5%","Mydriacyl 1/2%","Mydriacyl 1%","Cyclogyl 1%");
			
			//echo "<br/>HE: ".$elem_dia_id." - ".$elem_se_dilation;			
			$sumDilation = $sumDilation_od = $sumDilation_os = "";
			if(!empty($elem_dia_id) && !empty($elem_se_dilation)){
				$sumDilation_od = $elem_sumDilationOd;
				$sumDilation_os = $elem_sumDilationOs;
				$spDialTime = $elem_spDialTime;				
				
				/*
				if(!empty($elem_dilation)){
					
					if(!empty($elem_sumDilation)){
						$sumDilation = $elem_sumDilation;
					
					}else{
					
						/*
						//Need to remove with an update ---------
						$arrDilation=unserialize($elem_dilation);
						$tmp = count($arrDilation);
						for($a=0;$a<$tmp;$a++){
							if((!empty($arrDilation[$a]["dilate"])||!empty($arrDilation[$a]["other_desc"]))&&!empty($arrDilation[$a]["time"])){
								
								//if(empty($spDialTime)){
									$spDialTime = trim($arrDilation[$a]["time"].' '.$elem_eyeSide);							
									//break;
								//}
								
								$t = "";
								if(!empty($arrDilation[$a]["dilate"])){
									$t = str_replace($arrDilatInx,$arrDilatVals,$arrDilation[$a]["dilate"]);
								}						
								
								if(!empty($arrDilation[$a]["other_desc"])){
									$t = str_replace("Other", "".$arrDilation[$a]["other_desc"],$t);
								}
								$t = preg_replace("/(\s)*\,(\s)*$/i", "", $t);							
								$t = str_replace(",",", ",$t); 
								$sumDilation .= $t." ".trim($arrDilation[$a]["time"])."<br/>";
								
							}
						}
						//Need to remove with an update ---------
						* /
					}
					
				}
				/*
				else {
					//Need to remove with an update ---------
					if(!empty($elem_pheny25) || !empty($elem_tropicanide) || !empty($elem_cyclogel) || !empty($elem_mydiacyl5) ||
						!empty($elem_dilated_other)){
						$spDialTime = trim($elem_dilated_time.' '.$elem_eyeSide);
						
						$t = "";
						if(!empty($elem_pheny25)) {$t .= "Phenylephrine 2.5%,";}
						if(!empty($elem_tropicanide)) {$t .= "Mydriacyl 1%,";}
						if(!empty($elem_cyclogel)) {$t .= "Cyclogyl 1%,";}
						if(!empty($elem_mydiacyl5)) {$t .= "Mydriacyl 5%,";}
						if(!empty($elem_dilated_other)) {$t .= $elem_dilated_other.",";}
						$t = preg_replace("/(\s)*\,(\s)*$/i", "", $t);							
						$t = str_replace(",",", ",$t); 
						$sumDilation .= $t." ".$elem_dilated_time."<br/>";
						
					}
					//Need to remove with an update ---------
				}* /
				
				if($elem_eyeSide=="OU"||$elem_eyeSide=="OD"){
					$sumDilation_od=$sumDilation;
				}
				if($elem_eyeSide=="OU"||$elem_eyeSide=="OS"){
					$sumDilation_os=$sumDilation;
				}
				*/
				/*
				if($elem_no_dilation==1){
					$spDialTime = "No Dilation";
				}else if($unableDilation==1){
					$spDialTime = "Refuse Dilation";
				}
				*/
				
			}
			//Dilation --
			
			//OOD
			$spOODTime="";
			$sumOOD = $sumOOD_od = $sumOOD_os = "";
			//$arrOODInx = array("Piol","Alphagan","Iopidine","Diamox");
			//$arrOODVals = array("Pilo 1%","Alphagan P 0.1%","Iopidine 0.5%","Diamox");
			
			if(!empty($elem_ood_id) && !empty($statusElem_OOD)){
				$sumOOD_od = $elem_sumOOD_od;
				$sumOOD_os = $elem_sumOOD_os;
				$spOODTime = $elem_spOODTime;	
				
				/*
				if(!empty($ood)){
					$arrOOD=unserialize($ood);
					$tmp = count($arrOOD);
					for($a=0;$a<$tmp;$a++){
						if((!empty($arrOOD[$a]["ood"])||!empty($arrOOD[$a]["other_desc"]))&&!empty($arrOOD[$a]["time"])){
							
							$t = "";
							if(!empty($arrOOD[$a]["ood"])){
								$t = str_replace($arrOODInx,$arrOODVals,$arrOOD[$a]["ood"]);
							}
							
							if(!empty($arrOOD[$a]["other_desc"])){
								$t = str_replace("Other", "".$arrOOD[$a]["other_desc"],$t);
							}
							$t = preg_replace("/(\s)*\,(\s)*$/i", "", $t);							
							$t = str_replace(",",", ",$t); 
							$sumOOD .= $t." ".trim($arrOOD[$a]["time"])."<br/>";
						}
					}
				}
				*/
			}
			
			/*
			if($eye_ood=="OU"||$eye_ood=="OD"){
				$sumOOD_od=$sumOOD;
			}
			if($eye_ood=="OU"||$eye_ood=="OS"){
				$sumOOD_os=$sumOOD;
			}
			*/
			//OOD
			
			//----
			//is Change is made in new chart -----
				$flgSe_Gonio_Od = $flgSe_Gonio_Os = "0";
				$flgSe_Draw_Od = $flgSe_Draw_Os = "0";				
				if(!isset($bgColor_gonio)){
					if(!empty($elem_se_Gonio)){
						$tmpArrSe = $chartNoteObj->se_elemStatus("GONIO","0",$elem_se_Gonio);
						$flgSe_Gonio_Od = $tmpArrSe["Iop"]["od"];
						$flgSe_Gonio_Os = $tmpArrSe["Iop"]["os"];
						$flgSe_Draw_Od = $tmpArrSe["Iop3"]["od"];
						$flgSe_Draw_Os = $tmpArrSe["Iop3"]["os"];
					}
				}else{
					if(!empty($elem_se_Gonio_prev)){
						$tmpArrSe_prev = $chartNoteObj->se_elemStatus("GONIO","0",$elem_se_Gonio_prev);
						$flgSe_Gonio_Od_prev = $tmpArrSe_prev["Iop"]["od"];
						$flgSe_Gonio_Os_prev = $tmpArrSe_prev["Iop"]["os"];
						$flgSe_Draw_Od_prev = $tmpArrSe_prev["Iop3"]["od"];
						$flgSe_Draw_Os_prev = $tmpArrSe_prev["Iop3"]["os"];
					}
				}
			//is Change is made in new chart -----		
		
			//WNL
			//Gonio --
			$wnlString = !empty($elem_wnl_value_gonio) ? $elem_wnl_value_gonio : $chartNoteObj->getExamWnlStr("Gonio", $patient_id, $form_id);
			$wnlStringOd = $wnlStringOs = $wnlString; 
			
			if(empty($flgSe_Gonio_Od) && empty($flgSe_Gonio_Od_prev) && !empty($elem_wnlGonioOd)){ $tmp = $chartNoteObj->getExamWnlStr_fromPrvExm("Gonio", "OD"); if(!empty($tmp)){ $wnlStringOd = $tmp;}  }
			if(empty($flgSe_Gonio_Os) && empty($flgSe_Gonio_Os_prev) && !empty($elem_wnlGonioOs)){  $tmp = $chartNoteObj->getExamWnlStr_fromPrvExm("Gonio", "OS"); if(!empty($tmp)){ $wnlStringOs = $tmp;}  }
			
			list($elem_sumOdGon,$elem_sumOsGon) = $onLoadObj->setWnlValuesinSumm(array("wValOd"=>$wnlStringOd,"wValOs"=>$wnlStringOs,
									"wOd"=>$elem_wnlGonioOd,"sOd"=>$elem_sumOdGon,
									"wOs"=>$elem_wnlGonioOs,"sOs"=>$elem_sumOsGon));
			//Nochanged
			if(!empty($elem_se_Gonio)&&strpos($elem_se_Gonio,"=1")!==false){
				$elem_noChangeGonio=1;
			}

			//Archive IOP --
			$arrDivArcCmn=array();
			$oChartRecArc->setChkTbl("chart_iop");
			$arrInpArc = array("elem_sumOdIOP"=>array("sumOdIop",$elem_sumOdIop,"smof","","",$modi_note_IopOd),
								"elem_sumOsIOP"=>array("sumOsIop",$elem_sumOsIop,"smof","","",$modi_note_IopOs));
			$arTmpRecArc = $oChartRecArc->getArcRec($arrInpArc);
			//OD
			if(!empty($arTmpRecArc["div"]["elem_sumOdIOP"])){
				//echo $arTmpRecArc["div"]["elem_sumOdIOP"];
				$arrDivArcCmn["IOP"]["OD"]= $arTmpRecArc["div"]["elem_sumOdIOP"];
				$moeArc["od"]["Iop"] = $arTmpRecArc["js"]["elem_sumOdIOP"];
				$flgArcColor["od"]["Iop"] = $arTmpRecArc["css"]["elem_sumOdIOP"];
				if(!empty($arTmpRecArc["curText"]["elem_sumOdIOP"])) $elem_sumOdIop = $arTmpRecArc["curText"]["elem_sumOdIOP"];
			}else{
				$moeArc["od"]["Iop"]=$flgArcColor["od"]["Iop"]="";
			}
			//Os
			if(!empty($arTmpRecArc["div"]["elem_sumOsIOP"])){
				//echo $arTmpRecArc["div"]["elem_sumOsIOP"];
				$arrDivArcCmn["IOP"]["OS"]= $arTmpRecArc["div"]["elem_sumOsIOP"];
				$moeArc["os"]["Iop"] = $arTmpRecArc["js"]["elem_sumOsIOP"];
				$flgArcColor["os"]["Iop"] = $arTmpRecArc["css"]["elem_sumOsIOP"];
				if(!empty($arTmpRecArc["curText"]["elem_sumOsIOP"])) $elem_sumOsIop = $arTmpRecArc["curText"]["elem_sumOsIOP"];
			}else{
				$moeArc["os"]["Iop"]=$flgArcColor["os"]["Iop"]="";
			}
			//Archive IOP --

			//Archive Gonio --
			if($bgColor_gonio != "bgSmoke"){
			
			$oChartRecArc->setChkTbl("chart_gonio");
			$arrInpArc = array("elem_sumOdGonio"=>array("gonio_od_summary",$elem_sumOdGon,"","wnlOd", $wnlString, $modi_note_GonioOd),
								"elem_sumOsGonio"=>array("gonio_os_summary",$elem_sumOsGon,"","wnlOs", $wnlString, $modi_note_GonioOs));
			$arTmpRecArc = $oChartRecArc->getArcRec($arrInpArc);

			//OD
			if(!empty($arTmpRecArc["div"]["elem_sumOdGonio"])){
				//echo $arTmpRecArc["div"]["elem_sumOdGonio"];
				$arrDivArcCmn["Gonio"]["OD"]= $arTmpRecArc["div"]["elem_sumOdGonio"];
				$moeArc["od"]["Gonio"] = $arTmpRecArc["js"]["elem_sumOdGonio"];
				$flgArcColor["od"]["Gonio"] = $arTmpRecArc["css"]["elem_sumOdGonio"];
				if(!empty($arTmpRecArc["curText"]["elem_sumOdGonio"])) $elem_sumOdGon = $arTmpRecArc["curText"]["elem_sumOdGonio"];
			}else{
				$moeArc["od"]["Gonio"]=$flgArcColor["od"]["Gonio"]="";
			}
			//Os
			if(!empty($arTmpRecArc["div"]["elem_sumOsGonio"])){
				//echo $arTmpRecArc["div"]["elem_sumOsGonio"];
				$arrDivArcCmn["Gonio"]["OS"]= $arTmpRecArc["div"]["elem_sumOsGonio"];
				$moeArc["os"]["Gonio"] = $arTmpRecArc["js"]["elem_sumOsGonio"];
				$flgArcColor["os"]["Gonio"] = $arTmpRecArc["css"]["elem_sumOsGonio"];
				if(!empty($arTmpRecArc["curText"]["elem_sumOsGonio"])) $elem_sumOsGon = $arTmpRecArc["curText"]["elem_sumOsGonio"];
			}else{
				$moeArc["os"]["Gonio"]=$flgArcColor["os"]["Gonio"]="";
			}
			
			}
			//Archive Gonio --

			
			
			//Purged --------
			$htmPurge = $this->getPurgedHtm("IOP/Gonio",array(),$patientId,$form_id);
			//Purged --------
			
			//Modified Notes ----
			//if Edit is not Done && modified Notes exists
			//iop
			if(!empty($modi_note_IopOd) && empty($moeArc["od"]["Iop"])){ //Od
				list($moeMN["od"]["Iop"],$tmpDiv)=$onLoadObj->getModiNoteConDiv("elem_sumOdIOP", $modi_note_IopOd);
				//echo $tmpDiv;
				$arrDivArcCmn["IOP"]["OD"]=$tmpDiv;
			}else{
				$moeMN["od"]["Iop"]="";
			}
			if(!empty($modi_note_IopOs) && empty($moeArc["os"]["Iop"])){ //OS
				list($moeMN["os"]["Iop"],$tmpDiv)=$onLoadObj->getModiNoteConDiv("elem_sumOsIOP", $modi_note_IopOs);
				//echo $tmpDiv;
				$arrDivArcCmn["IOP"]["OS"]=$tmpDiv;
			}else{
				$moeMN["os"]["Iop"]="";
			}
			
			//gonio
			if(!empty($modi_note_GonioOd) && empty($moeArc["od"]["Gonio"])){ //Od
				list($moeMN["od"]["Gonio"],$tmpDiv)=$onLoadObj->getModiNoteConDiv("elem_sumOdGonio", $modi_note_GonioOd);
				//echo $tmpDiv;
				$arrDivArcCmn["Gonio"]["OD"]=$tmpDiv;
			}else{
				$moeMN["od"]["Gonio"]="";
			}
			if(!empty($modi_note_GonioOs) && empty($moeArc["os"]["Gonio"])){ //Os
				list($moeMN["os"]["Gonio"],$tmpDiv)=$onLoadObj->getModiNoteConDiv("elem_sumOsGonio", $modi_note_GonioOs);
				//echo $tmpDiv;
				$arrDivArcCmn["Gonio"]["OS"]=$tmpDiv;
			}else{
				$moeMN["os"]["Gonio"]="";
			}		
			
			//No Edit is working for Anesthetic+Ans Div is not known			
			if(!empty($modi_note_AnestheticOd) && empty($moeArc["od"]["Anes"])){ //OS
				list($moeMN["od"]["Anes"],$tmpDiv)=$onLoadObj->getModiNoteConDiv("elem_sumOdAnesthetic", $modi_note_AnestheticOd);
				//echo $tmpDiv;
				$arrDivArcCmn["Anesthetic"]["OD"]=$tmpDiv;
			}else{
				$moeMN["od"]["Anes"]="";
			}
			//
			if(!empty($modi_note_AnestheticOs) && empty($moeArc["os"]["Anes"])){ //OS
				list($moeMN["os"]["Anes"],$tmpDiv)=$onLoadObj->getModiNoteConDiv("elem_sumOsAnesthetic", $modi_note_AnestheticOs);
				//echo $tmpDiv;
				$arrDivArcCmn["Anesthetic"]["OS"]=$tmpDiv;
			}else{
				$moeMN["os"]["Anes"]="";
			}			
			
			//			
			//No Edit is working for Dilation+Dilation Div is not known
			if(!empty($modi_note_DilationOd) && empty($moeArc["od"]["dilation"])){ //OD
				list($moeMN["od"]["dilation"],$tmpDiv)=$onLoadObj->getModiNoteConDiv("elem_sumOdDilation", $modi_note_DilationOd);
				//echo $tmpDiv;
				$arrDivArcCmn["Dilation"]["OD"]=$tmpDiv;
			}else{
				$moeMN["od"]["dilation"]="";
			}
			if(!empty($modi_note_DilationOs) && empty($moeArc["os"]["dilation"])){ //OS
				list($moeMN["os"]["dilation"],$tmpDiv)=$onLoadObj->getModiNoteConDiv("elem_sumOsDilation", $modi_note_DilationOs);
				//echo $tmpDiv;
				$arrDivArcCmn["Dilation"]["OS"]=$tmpDiv;
			}else{
				$moeMN["os"]["dilation"]="";
			}
			
			//No Edit is working for OOD+OOD Div is not known
			if(!empty($modi_note_OodOd) && empty($moeArc["od"]["ood"])){ //OD
				list($moeMN["od"]["ood"],$tmpDiv)=$onLoadObj->getModiNoteConDiv("elem_sumOdOOD", $modi_note_OodOd);
				//echo $tmpDiv;
				$arrDivArcCmn["Ophth. Drops"]["OD"]=$tmpDiv;
			}else{
				$moeMN["od"]["ood"]="";
			}
			if(!empty($modi_note_OodOs) && empty($moeArc["os"]["ood"])){ //OS
				list($moeMN["os"]["ood"],$tmpDiv)=$onLoadObj->getModiNoteConDiv("elem_sumOsOOD", $modi_note_OodOs);
				//echo $tmpDiv;
				$arrDivArcCmn["Ophth. Drops"]["OS"]=$tmpDiv;
			}else{
				$moeMN["os"]["ood"]="";
			}			

			//Drawing
			if(!empty($modi_note_Draw)){ //Os
				list($moeMN["od"]["draw"],$tmpDiv)=$onLoadObj->getModiNoteConDiv("elem_drawOdGonio", $modi_note_Draw);
				//echo $tmpDiv;
				$arrDivArcCmn["Drawing"]["OD"]=$tmpDiv;
			}else{
				$moeMN["od"]["draw"]="";
			}			
			//Modified Notes ----
			
			//create common div and echo ---
			//list($moeMN,$tmpDiv) = mkDivArcCmn("Gonio",$arrDivArcCmn);
			if($post["webservice"] != "1"){
			list($moeMN,$tmpDiv) = $onLoadObj->mkDivArcCmnNew("Gonio",$arrDivArcCmn,$arrHx);
			}
			$echo.= $tmpDiv;
			//echo "<xmp>CHECKKKKK: ".$tmpDiv."</xmp>";
			
			$arr=array();
			$arr["ename"] = "IOP/Gonio";
			
			//IOP
			$arr["subExm"][] = $onLoadObj->getArrExms_ms(array("enm"=>"IOP",
												"sOd"=>$elem_sumOdIop,"sOs"=>$elem_sumOsIop,
												"fOd"=>$flgSe_IOP,"fOs"=>$flgSe_IOP,
												//"arcJsOd"=>$moeArc["od"]["Iop"],"arcJsOs"=>$moeArc["os"]["Iop"],
												"arcCssOd"=>$flgArcColor["od"]["Iop"],"arcCssOs"=>$flgArcColor["os"]["Iop"],
												//"mnOd"=>$moeMN["od"]["Iop"],"mnOs"=>$moeMN["os"]["Iop"],
												"enm_2"=>"IOP"));
			//Gonio
			$arr["subExm"][] = $onLoadObj->getArrExms_ms(array("enm"=>"Gonio",
												"sOd"=>$elem_sumOdGon,"sOs"=>$elem_sumOsGon,
												"fOd"=>$flgSe_Gonio_Od,"fOs"=>$flgSe_Gonio_Os,"pos"=>$elem_posGonio,
												//"arcJsOd"=>$moeArc["od"]["Gonio"],"arcJsOs"=>$moeArc["os"]["Gonio"],
												"arcCssOd"=>$flgArcColor["od"]["Gonio"],"arcCssOs"=>$flgArcColor["os"]["Gonio"],
												///"mnOd"=>$moeMN["od"]["Gonio"],"mnOs"=>$moeMN["os"]["Gonio"],
												"enm_2"=>"Gonio"));
			
			//Anes : No archive working  for Anes
			$arr["subExm"][] = $onLoadObj->getArrExms_ms(array("enm"=>"Anesthetic",
												"sOd"=>$sumAnes_od,"sOs"=>$sumAnes_os,
												"fOd"=>$flgSe_IOP,"fOs"=>$flgSe_IOP,
												//"arcJsOd"=>$moeArc["od"]["Anes"],"arcJsOs"=>$moeArc["os"]["Anes"],
												"arcCssOd"=>$flgArcColor["od"]["Anes"],"arcCssOs"=>$flgArcColor["os"]["Anes"],
												//"mnOd"=>$moeMN["od"]["Anes"],"mnOs"=>$moeMN["os"]["Anes"],
												"enm_2"=>"Anesthetic"));
			
			//Dilation: No archive working  for dilate
			$arr["subExm"][] = $onLoadObj->getArrExms_ms(array("enm"=>"Dilation",
												"sOd"=>$sumDilation_od,"sOs"=>$sumDilation_os,
												"fOd"=>$elem_se_dilation,"fOs"=>$elem_se_dilation,
												//"arcJsOd"=>$moeArc["od"]["dilation"],"arcJsOs"=>$moeArc["os"]["dilation"],
												"arcCssOd"=>$flgArcColor["od"]["dilation"],"arcCssOs"=>$flgArcColor["os"]["dilation"],
												//"mnOd"=>$moeMN["od"]["dilation"],"mnOs"=>$moeMN["os"]["dilation"],
												"enm_2"=>"Dilation"));
												
			//OOD: No archive working  for ood
			$arr["subExm"][] = $onLoadObj->getArrExms_ms(array("enm"=>"Ophth. Drops",
												"sOd"=>$sumOOD_od,"sOs"=>$sumOOD_os,
												"fOd"=>$statusElem_OOD,"fOs"=>$statusElem_OOD,
												//"arcJsOd"=>$moeArc["od"]["ood"],"arcJsOs"=>$moeArc["os"]["ood"],
												"arcCssOd"=>$flgArcColor["od"]["ood"],"arcCssOs"=>$flgArcColor["os"]["ood"],
												//"mnOd"=>$moeMN["od"]["ood"],"mnOs"=>$moeMN["os"]["ood"],
												"enm_2"=>"OOD"));
												
			//Drawing: must be  at end of array: 
			$arr["subExm"][] = $onLoadObj->getArrExms_ms(array("enm"=>"Drawing",
												"sOd"=>$drawdocId,"sOs"=>"",
												"fOd"=>$flgSe_Draw_Od,"fOs"=>$flgSe_Draw_Os,"pos"=>$elem_posDrawGonio,
												"enm_2"=>"DrawGonio"));					

			//Sub Exam List
			$arr["seList"] = 	array("IOP"=>array("enm"=>"IOP","pos"=>$elem_posIop,
								"wOd"=>$elem_wnlIopOd,"wOs"=>$elem_wnlIopOs),
							"Gonio"=>array("enm"=>"Gonio","pos"=>$elem_posGonio,
								"wOd"=>$elem_wnlGonioOd,"wOs"=>$elem_wnlGonioOs),
							"DrawGonio"=>array("enm"=>"Drawing","pos"=>$elem_posDrawGonio,
								"wOd"=>$elem_wnlDrawGonioOd,"wOs"=>$elem_wnlDrawGonioOs)
							);
			$arr["nochange"] = $elem_noChangeGonio;
			$arr["oe"] = $oneeye;
			$arr["desc"] = $elem_txtDesc_iop;
			$arr["Dilation"] = array("Anesthetic"=>$spAnesTime,"Dilate"=>$spDialTime,"PtAllergic"=>$patientAllergic,"OOD"=>$spOODTime);
			$arr["bgColor"] = "".$bgColor_gonio;
			$arr["drawdocId"] = $drawdocId;
			$arr["drawapp"] = $ardrawApp;			
			$arr["drawSE"] = array($flgSe_Draw_Od,$flgSe_Draw_Os);
			$arr["examdate"] = $examdate;
			$arr["htmPurge"] = $htmPurge;
			$arr["moeMN"] = $moeMN;
			$arr["flgGetDraw"] = $this->onwv_isDrawingChanged(array($elem_wnlDrawGonioOd,$elem_wnlDrawGonioOs,$elem_ncDrawGonio,$elem_ncDrawGonio));
			$arr["exm_flg_se"] = array($flgSe_Gonio_Od,$flgSe_Gonio_Os);
			
			if($post["webservice"] == "1"){
				$echo ="";
				$str = $this->getSummaryHTML_appwebservice($arr);			
			}else{			
				$str = getSummaryHTML($arr);			
			}
			
			//---------
			$echo.= $str;
			return $echo;

			break;

			case "SLE":
			case "sle":			
			include_once($GLOBALS['srcdir']."/classes/work_view/SLE.php");			
			include_once($GLOBALS['srcdir']."/classes/work_view/SLELoader.php");
			$oload = new SLELoader($patient_id,$form_id);
			$data_html = $oload->getWorkViewSummery($post);
			return $data_html;			
			break;

			case "Fundus Exam":
			case "fundus_exam":
			include_once($GLOBALS['srcdir']."/classes/work_view/OpticNerve.php");
			include_once($GLOBALS['srcdir']."/classes/work_view/FundusExam.php");
			$oload = new FundusExamLoader($patient_id,$form_id);
			$data_html = $oload->getWorkViewSummery($post);
			return $data_html;
			break;

			case "Refractive Surgery":
			case "ref_surg":
			include_once($GLOBALS['srcdir']."/classes/work_view/RefSurg.php");
			//require("common/ChartRecArc.php");
			//object Chart Rec Archive --
			$oChartRecArc = new ChartRecArc($patient_id,$form_id,$_SESSION['authId']);
			//---
			$echo="";
			
				$oRefSurg = new RefSurg($patient_id,$form_id);
				$sql =  "SELECT ".
						"c2.id_ref_surg, ".
						"c2.isPositive AS flagPosRS, c2.wnl AS flagWnlRS, ".
						"c2.examined_no_change  AS chRS, ".
						"c2.sumOdRefSurg, ".
						"c2.sumOsRefSurg, ".
						"c2.wnlRefSurgOd, c2.wnlRefSurgOs, c2.descRefSurg, ".
						"c2.statusElem AS se_RS ".						
						"FROM chart_master_table c1 ".
						"LEFT JOIN chart_ref_surgery c2 ON c2.form_id = c1.id AND c2.purged='0'  ".	
						"WHERE c1.id = '".$form_id."' AND c1.patient_id='".$patient_id."' ";
				$row = sqlQuery($sql);
				//$row=$oRefSurg->sqlExe($sql);
				if($row != false){
					$elem_noChangeRS=assignZero($row["chRS"]);
					//$elem_wnlRS=assignZero($row["flagWnlRS"]);
					$elem_posRS=assignZero($row["flagPosRS"]);
					//$elem_wnlRSOd=assignZero($row["wnlRefSurgOd"]);
					//$elem_wnlRSOs=assignZero($row["wnlRefSurgOs"]);
					$elem_se_RS = $row["se_RS"];					
					$elem_sumOdRS = $row["sumOdRefSurg"];
					$elem_sumOsRS = $row["sumOsRefSurg"];
					$elem_RS_id = $row["id_ref_surg"];
					$elem_txtDesc_RS = stripslashes(trim($row["descRefSurg"]));
				}

				//Previous
				if(empty($elem_RS_id)){
					$tmp = "";
					$tmp = "  c2.isPositive AS flagPosRS, 
							  c2.wnl AS flagWnlRS, c2.examined_no_change  AS chRS, ";
					$tmp .= " c2.wnlRefSurgOd, c2.wnlRefSurgOs, c2.descRefSurg, ";
					$tmp .= " c2.sumOdRefSurg, 
							  c2.sumOsRefSurg, c2.id_ref_surg ";
					
					$elem_dos=$oRefSurg->getDos();
					$elem_dos=wv_formatDate($elem_dos,0,0,"insert");
					//$elem_dos=$oRefSurg->formatDate($elem_dos,0,0,"insert");
					$res = $oRefSurg->getLastRecord($tmp,0,$elem_dos);
					if($res!=false){$row=$res;}else{$row=false;}

					if($row!=false){
					//$res = valNewRecordRef_Surg($patient_id, $tmp);
					//for($i=0;$row=sqlFetchArray($res);$i++)	{
						
						//$elem_wnlRS=assignZero($row["flagWnlRS"]);
						$elem_posRS=assignZero($row["flagPosRS"]);
						//$elem_wnlRSOd=assignZero($row["wnlRefSurgOd"]);
						//$elem_wnlRSOs=assignZero($row["wnlRefSurgOs"]);
						$elem_sumOdRS = $row["sumOdRefSurg"];
						$elem_sumOsRS = $row["sumOsRefSurg"];						
						$elem_txtDesc_RS = stripslashes(trim($row["descRefSurg"]));						
						
					}
					//BG
					$bgColor_RS = "bgSmoke";
				}

				//Wnl
				list($elem_sumOdRS,$elem_sumOsRS) = $onLoadObj->setWnlValuesinSumm(array("wVal"=>"WNL",
							"wOd"=>$elem_wnlRSOd,"sOd"=>$elem_sumOdRS,
							"wOs"=>$elem_wnlRSOs,"sOs"=>$elem_sumOsRS));
				
				//Color
				$flgSe_RS_Od = $flgSe_RS_Os = "";
				if(!isset($bgColor_RS)){
					if(!empty($elem_se_RS)){
						$tmpArrSe = $chartNoteObj->se_elemStatus("REF_SURG","0",$elem_se_RS);
						$flgSe_RS_Od = $tmpArrSe["1"]["od"];
						$flgSe_RS_Os = $tmpArrSe["1"]["os"];
					}
				}
				//Nochanged
				if(!empty($elem_se_RS)&&strpos($elem_se_RS,"=1")!==false){
					$elem_noChangeRS=1;
				}
				
				//Purged --------
				$htmPurge = $this->getPurgedHtm("RefSurg",array(),$patientId,$form_id);
				//Purged --------
				
				//---------			
				
				$arr=array();
				$arr["ename"] = "Refractive Surgery"; 
				$arr["subExm"][0] = $onLoadObj->getArrExms_ms(array("enm"=>"Refractive Surgery",
													"sOd"=>$elem_sumOdRS,"sOs"=>$elem_sumOsRS,
													"fOd"=>$flgSe_RS_Od,"fOs"=>$flgSe_RS_Os,"pos"=>$elem_posRS,
													"arcJsOd"=>$moeArc_od,"arcJsOs"=>$moeArc_os,
													"arcCssOd"=>$flgArcColor_od,"arcCssOs"=>$flgArcColor_os
													));
				$arr["nochange"] = $elem_noChangeRS;
				$arr["oe"] = $oneeye;
				$arr["desc"] = $elem_txtDesc_RS;
				$arr["bgColor"] = "".$bgColor_RS;
				$arr["htmPurge"] = $htmPurge;
				$arr["exm_flg_se"] = array($flgSe_RS_Od,$flgSe_RS_Os);
				
				if($post["webservice"] == "1"){
				$echo ="";
				$str = $this->getSummaryHTML_appwebservice($arr);			
				}else{			
				$str = getSummaryHTML($arr);			
				}
				
				//---------	
				$echo.= $str;
				return $echo;
			
			break;
			
			case "CVF":	
			case "cvf":	
				include_once($GLOBALS['srcdir']."/classes/work_view/CVF.php");
				
				$echo="";
				$oCVF = new CVF($patient_id,$form_id);
				$sql =	"SELECT ".
						//c12-chart_cvf-------
						"c12.drawOd, c12.wnlODHiddden, c12.wnlOSHiddden, c12.wnl AS CVFwnl,".
						"c12.drawOs, c12.summaryOd AS cvfSumOd, c12.summaryOs AS cvfSumOs,  c12.cvf_id, ".
						"c12.cvfOd, c12.cvfOs,nochange, ".
						"c12.drwpth_od, c12.drwpth_os, c12.wnl_value ".
						//c12-chart_cvf-------
						"FROM chart_master_table c1 ".
						"LEFT JOIN chart_cvf c12 ON c12.formId = c1.id ".
						"WHERE c1.id = '".$form_id."' AND c1.patient_id='".$patient_id."' ";

				$row = sqlQuery($sql);
				//$row=$oCVF->sqlExe($sql);
				if($row != false){
					$elem_cvfOd = $row["cvfOd"];
					$elem_cvfOs = $row["cvfOs"];
					$CVFwnl = $row["CVFwnl"];
					$elem_cvfSumOd = $row["cvfSumOd"];
					$elem_cvfSumOs = $row["cvfSumOs"];
					$elem_cvf_id = $row["cvf_id"];
					$elem_drawOd = $this->isAppletDrawn($row["drawOd"]);
					$elem_drawOs = $this->isAppletDrawn($row["drawOs"]);
					$wnlODHiddden = $row["wnlODHiddden"];
					$wnlOSHiddden = $row["wnlOSHiddden"];
					$nochange = $row["nochange"]; //!empty($row["nochange"]) ? "checked='checked'" : "";
					$drwpth_od=$row["drwpth_od"];
					$drwpth_os=$row["drwpth_os"];
					$elem_wnl_value = $row["wnl_value"];
					
				}

				if(empty($elem_cvf_id)){ /* Show Previous values in finalized chart also ~&& ($finalize_flag == 0)~ */
					$tmp = "";
					$tmp .= "	c2.drawOd, c2.drawOs, 
								c2.summaryOd AS cvfSumOd, c2.summaryOs AS cvfSumOs, 
								c2.drwpth_od, c2.drwpth_os, c2.wnl_value, 
								c2.cvf_id ";
					$elem_dos=$oCVF->getDos();
					$elem_dos=wv_formatDate($elem_dos,0,0,"insert");
					//$elem_dos=$oCVF->formatDate($elem_dos,0,0,"insert");
					$res = $oCVF->getLastRecord($tmp,0,$elem_dos);
					if($res!=false){$row=$res;}else{$row=false;}
					if($row!=false){
					//$row = valuesNewRecordsCvf($patient_id, $tmp);
					//if($row != false){
						$elem_drawOd = $this->isAppletDrawn($row["drawOd"]);
						$elem_drawOs = $this->isAppletDrawn($row["drawOs"]);
						//$elem_cvf_id = $row["cvf_id"];
						$elem_cvfSumOd = $row["cvfSumOd"];
						$elem_cvfSumOs = $row["cvfSumOs"];
						$drwpth_od=$row["drwpth_od"];
						$drwpth_os=$row["drwpth_os"];
						$elem_wnl_value = $row["wnl_value"];
					}
					//BG
					$bgColor_Cvf = "gray_color";
				}
				
				$var1=$var2="";
				$tmpcvfSum = explode(",",str_replace("<br>","",$elem_cvfSumOd));//od
				if($elem_drawOd || !empty($drwpth_od) ){
					$var1 = 'ABN';
				}else if((in_array("Full",$tmpcvfSum))){
					$var1 = !empty($elem_wnl_value) ? $elem_wnl_value : $chartNoteObj->getExamWnlStr("CVF", $patient_id, $form_id);
				}else if( (in_array("Constriction",$tmpcvfSum)) || (in_array("Superotemporal Defect",$tmpcvfSum)) ||
						(in_array("Inferotemporal Defect",$tmpcvfSum)) || (in_array("Superonasal Defect",$tmpcvfSum)) ||
						(in_array("Inferonasal Defect",$tmpcvfSum)) || (in_array("Superior Half Defect",$tmpcvfSum)) ||
						(in_array("Inferior Half Defect",$tmpcvfSum))  ){
					$var1 = 'ABN';
				}else{
					$var1 = '';
				}

				$tmpcvfSum = explode(",",str_replace("<br>","",$elem_cvfSumOs));//os
				if($elem_drawOs || !empty($drwpth_os)){
					$var2 = 'ABN';
				}else if((in_array("Full",$tmpcvfSum))){
					$var2 = !empty($elem_wnl_value) ? $elem_wnl_value : $chartNoteObj->getExamWnlStr("CVF", $patient_id, $form_id);
				}else if( (in_array("Constriction",$tmpcvfSum)) || (in_array("Superotemporal Defect",$tmpcvfSum)) ||
						(in_array("Inferotemporal Defect",$tmpcvfSum)) || (in_array("Superonasal Defect",$tmpcvfSum)) ||
						(in_array("Inferonasal Defect",$tmpcvfSum)) || (in_array("Superior Half Defect",$tmpcvfSum)) ||
						(in_array("Inferior Half Defect",$tmpcvfSum))  ){
					$var2 = 'ABN';
				}else{
					$var2 = '';
				}				
				
				if($elem_cvfSumOd!="" || $elem_cvfSumOs!=""){
					if($elem_cvfSumOd!="Full" || $elem_cvfSumOs!="Full" || $elem_drawOd || $elem_drawOs || !empty($drwpth_os) || !empty($drwpth_od) ){					
						if(!isset($bgColor_Cvf)){
							$posCvf="positive";
						}else{
							$posCvf=$bgColor_Cvf;
						}	
					}else{
						if(!isset($bgColor_Cvf)){							
							$posCvf="wnl_lbl";
						}else{
							$posCvf=$bgColor_Cvf;
						}
					}
				}
				
				//
				$var1_s=$var1; $var2_s=$var2;
				if(strlen($var1_s)>=5){ $var1_s=substr($var1_s, 0,3)."..";  } //
				if(strlen($var2_s)>=5){ $var2_s=substr($var2_s, 0,3)."..";  } //
				
				//NoChnage
				$strNC = (!empty($nochange)) ? "Prv" : "NC";

				$str = "<div class=\"vishdr ".$posCvf."\" > ".
						"<label onclick=\"openPW('CVF')\" >CVF</label>";
				//<!-- No Change -->
				$str .=	" ".
						//"<input type=\"checkbox\" id=\"elem_noChangeCVF\" name=\"elem_noChangeCVF\" value=\"1\" class=\"checkbox\" ".
						//"	onClick=\"autoSaveNoChange('CVF')\" ".
						//"	".$nochange." >".						
						//"<label for=\"elem_noChangeCVF\">NC</label>".
						"<input type=\"button\" id=\"elem_btnNoChangeCVF\" class=\"wnl\" value=\"".$strNC."\" onClick=\"autoSaveNoChange('CVF')\"".
						"	> ".
						"<input type=\"button\" id=\"elem_btnWNLCVF\" class=\"wnl\" value=\"WNL\" onClick=\"autoSaveWnl('CVF')\"".
						"	> ".
						"</div>".
						"<ul class=\"ulFL ".$posCvf."\" onClick=\"openPW('CVF')\">".
						"<li id=\"cvfShowOd\">".
						"<div class=\"hr\"></div>
						<div class=\"vr\"></div>".
						$var1_s.
						"</li>".
						"<li id=\"cvfShowOs\">".
						"<div class=\"hr\"></div>
						<div class=\"vr\"></div>".						
						$var2_s.
						"</li>".
						"</ul>";
				$echo.= $str;
				
				//app
				if($post["webservice"] == "1"){	
					$tmp=array();
					$drwpth_od = attachHttp2file($drwpth_od);
					$drwpth_os = attachHttp2file($drwpth_os);					
			
					$tmp["CVF"] = array("show_label_od"=>$var1, "show_label_os"=>$var2, "pos"=>$posCvf, "sumod"=>$elem_cvfSumOd, "sumos"=>$elem_cvfSumOs, "draw_od"=>$drwpth_od, "draw_os"=>$drwpth_os);
					//$tmp["htm"] = $str;
					return serialize($tmp);
					
				}else{	return $echo;	}

			break;

			case "Amsler Grid":
			case "amsgrid":
				include_once($GLOBALS['srcdir']."/classes/work_view/AmslerGrid.php");
				
				$echo="";
				$oAmslerGrid = new AmslerGrid($patient_id,$form_id);
				$sql = "SELECT ".
					//c11-amsler_grid-------
					 "c11.amsler_od, c11.amsler_os, c11.wnl_flag, c11.wnl_flagOd,".
					 "c11.wnl_flagOs, c11.id AS AmslerId, c11.nochange, ".
					 "c11.drwpth_od, c11.drwpth_os, c11.wnl_value, c11.notes, c11.drwpth_od, c11.drwpth_os, exam_date ".
					//c11-amsler_grid-------
					 "FROM chart_master_table c1 ".
					 "LEFT JOIN amsler_grid c11 ON c11.form_id = c1.id ".
					 "WHERE c1.id = '".$form_id."' AND c1.patient_id='".$patient_id."' ";
				$row = sqlQuery($sql);
				//$row=$oAmslerGrid->sqlExe($sql);
				if($row != false){
					$elem_amsler_od = $this->isAppletDrawn($row["amsler_od"]);
					$elem_amsler_os = $this->isAppletDrawn($row["amsler_os"]);
					$wnl_flag = $row["wnl_flag"];
					$wnl_flagOd = $row["wnl_flagOd"];
					$wnl_flagOs = $row["wnl_flagOs"];
					$elem_AmslerId = $row["AmslerId"];
					$nochange = $row["nochange"]; //!empty($row["nochange"]) ? "checked='checked'" : "" ;
					$drwpth_od = $row["drwpth_od"];
					$drwpth_os = $row["drwpth_os"];
					$elem_wnl_value = $row["wnl_value"];					
					$elem_notes = $row["notes"];
					$drwpth_od = $row["drwpth_od"];
					$drwpth_os = $row["drwpth_os"];
					$exam_date = $row["exam_date"];
					
				}

				if(empty($elem_AmslerId)){ /* Show Previous values in finalized chart also ~&& ($finalize_flag == 0)~ */
					$tmp = "";
					$tmp .= " c2.amsler_od, c2.amsler_os, c2.id AS AmslerId, ".
							"c2.wnl_flag, c2.wnl_flagOd, c2.wnl_flagOs, ".
							"c2.drwpth_od, c2.drwpth_os, c2.wnl_value, c2.notes, c2.drwpth_od, c2.drwpth_os, exam_date ";
					$elem_dos=$oAmslerGrid->getDos();
					$elem_dos=wv_formatDate($elem_dos,0,0,"insert");
					//$elem_dos=$oAmslerGrid->formatDate($elem_dos,0,0,"insert");
					$res = $oAmslerGrid->getLastRecord($tmp,0,$elem_dos);
					if($res!=false){$row=$res;}else{$row=false;}
					if($row!=false){
					//$row = valuesNewRecordsAmslar($patient_id, $tmp);
					//if($row != false){
						$elem_amsler_od = $this->isAppletDrawn($row["amsler_od"]);
						$elem_amsler_os = $this->isAppletDrawn($row["amsler_os"]);
						//$elem_AmslerId = $row["AmslerId"];
						$wnl_flag = $row["wnl_flag"];
						$wnl_flagOd = $row["wnl_flagOd"];
						$wnl_flagOs = $row["wnl_flagOs"];
						$drwpth_od = $row["drwpth_od"];
						$drwpth_os = $row["drwpth_os"];
						$elem_wnl_value = $row["wnl_value"];
						$elem_notes = $row["notes"];
						$drwpth_od = $row["drwpth_od"];
						$drwpth_os = $row["drwpth_os"];
						$exam_date = $row["exam_date"];
					}
					//BG
					$bgColor_Amsler = "gray_color";
				}

				if($elem_amsler_od||!empty($drwpth_od))	$var1= 'ABN'; 
				else if($wnl_flag == 'no' || ($wnl_flagOd == "1")) $var1= !empty($elem_wnl_value) ? $elem_wnl_value : $chartNoteObj->getExamWnlStr("Amsler Grid", $patient_id, $form_id);
				else $var1= '';

				if($elem_amsler_os||!empty($drwpth_os))	$var2= 'ABN'; 
				else if($wnl_flag == 'no' || ($wnl_flagOs == "1")) $var2= !empty($elem_wnl_value) ? $elem_wnl_value : $chartNoteObj->getExamWnlStr("Amsler Grid", $patient_id, $form_id);
				else $var2= '';
				
				if(!empty($elem_amsler_od) || !empty($elem_amsler_os) || !empty($drwpth_od) ||!empty($drwpth_os)) {
					if(!isset($bgColor_Amsler)){
						$pos="positive";
					}else{
						$pos=$bgColor_Amsler;
					}
				}else if(!empty($wnl_flagOd) || !empty($wnl_flagOs)){
					if(!isset($bgColor_Amsler)){
						$pos="wnl_lbl";
					}else{
						$pos=$bgColor_Amsler;
					}				
				}
				
				//
				$var1_s=$var1; $var2_s=$var2;
				if(strlen($var1_s)>=5){ $var1_s=substr($var1_s, 0,3)."..";  } //
				if(strlen($var2_s)>=5){ $var2_s=substr($var2_s, 0,3)."..";  } //
				
				//NoChnage
				$strNC = (!empty($nochange)) ? "Prv" : "NC";
				$str = '';
				if($GLOBALS['gl_browser_name']=='ipad'){
					$str .='<style type="text/css">input.wnl,input.prv{-webkit-appearance:none;border-radius:0;}</style>';
				}
				
				$str .= "<div class=\"vishdr ".$pos."\" >".
						"<label onclick=\"openPW('AG')\" title=\"Amsler Grid\">Ams Gd</label>";
						//<!-- No Change -->
				$str .=	" ".
						//"<input type=\"checkbox\" id=\"elem_noChangeAG\" name=\"elem_noChangeAG\" value=\"1\" class=\"checkbox\" ".
						//"	onClick=\"autoSaveNoChange('AG')\" ".
						//"	".$nochange." >".
						//"<label for=\"elem_noChangeAG\">NC</label>".
						"<input type=\"button\" id=\"elem_btnNoChangeAG\" class=\"wnl\" value=\"".$strNC."\" onClick=\"autoSaveNoChange('AG')\"".
						"	> ".
						"<input type=\"button\" id=\"elem_btnWNLAG\" class=\"wnl\" value=\"WNL\" onClick=\"autoSaveWnl('AG')\"".
						"	> ".
						"</div>".
						"<ul class=\"ulFL ".$pos."\" onClick=\"openPW('AG')\" title=\"Amsler Grid\">".
							"<li id=\"amslerImgD\">".$var1_s."</li>".
							"<li id=\"amslerImgD1\">".$var2_s."</li>".
						"</ul>";
				$echo.= $str;
				
				//app
				//app
				if($post["webservice"] == "1"){	
					$tmp=array();
					$drwpth_od = attachHttp2file($drwpth_od);
					$drwpth_os = attachHttp2file($drwpth_os);	
					$tmp["Amsler Grid"] = array("show_label_od"=>$var1, "show_label_os"=>$var2, "pos"=>$pos, "wnlod"=>$wnl_flagOd, "wnlos"=>$wnl_flagOs, "draw_od"=>$drwpth_od, "draw_os"=>$drwpth_os, "desc" => $elem_notes, "exam_date"=>$exam_date);
					//$tmp["htm"] = $str;
					return serialize($tmp);
					
				}else{		}				
				
				return $echo;
		
			break;
			/*	
			case "dipImgD":
				require_once("common/Diplopia.php");
				
				$echo="";
				$oDiplopia = new Diplopia($patient_id,$form_id);
				$sql = "SELECT ".
						//c13-chart_diplopia-------
						"c13.drawing, c13.summaryOd AS dipSumOd, c13.summaryOs AS dipSumOs,".
						"c13.dip_id, c13.wnl AS dipWnl ".
						//c13-chart_diplopia-------
						"FROM chart_master_table c1 ".
						"LEFT JOIN chart_diplopia c13 ON c13.formId = c1.id ".
						"WHERE c1.id = '".$form_id."' AND c1.patient_id='".$patient_id."' ";
				$row = sqlQuery($sql);
				if($row != false){
					$elem_wnlDip = $row["dipWnl"]; // diplopia wnl
					$elem_dipSumOd = $row["dipSumOd"];
					$elem_dipSumOs = $row["dipSumOs"];
					$elem_dip_id = $row["dip_id"];
					$elem_drawing = $this->isAppletDrawn($row["drawing"]);
				}
				
				if(empty($elem_dip_id)){ /* Show Previous values in finalized chart also ~&& ($finalize_flag == 0)~ *-/
				$tmp = "";
				$tmp .= " c2.drawing, c2.summaryOd AS dipSumOd, c2.summaryOs AS dipSumOs,
							c2.dip_id ";
				//$row = valuesNewRecordsDiplopia($patient_id, $tmp);
				$elem_dos=$oDiplopia->getDos();
				$elem_dos=$oDiplopia->formatDate($elem_dos,0,0,"insert");
				$res = $oDiplopia->getLastRecord($tmp,0,$elem_dos);
				if($res!=false){$row=$res->fields;}else{$row=false;}
					
				if($row != false){
					$elem_drawing = $this->isAppletDrawn($row["drawing"]);
					//$elem_dip_id = $row["dip_id"];
					$elem_dipSumOd = $row["dipSumOd"];
					$elem_dipSumOs = $row["dipSumOs"];
				}
				//BG
				$bgColor_Dip = "gray_color";
				}

				$pos=$var1="";
				if($elem_drawing || !empty($elem_dipSumOd) || !empty($elem_dipSumOs) /*|| !empty($elem_wnlDip)*-/){					
					$var1= 'ABN';
					//if(!empty($elem_wnlDip)) $var1= 'WNL';
					if(!isset($bgColor_Dip)){
						$pos="positive";
					}else{
						$pos=$bgColor_Dip;
					}
				}else if(!empty($elem_wnlDip)){
					$var1= 'WNL';
					if(!isset($bgColor_Dip)){
						$pos="wnl_lbl";
					}else{
						$pos=$bgColor_Dip;
					}				
				}

				echo "<span class=\"".$pos."\">".$var1."</span>"; 

			break;
			*/
			
			case "vf_oct_gl":
			
				//Only Doctor can interpret tests--
				//include_once(dirname(__FILE__)."/common/User.php");		
				include_once($GLOBALS['srcdir']."/classes/work_view/User.php");				
				$ouser=new User();
				$is_doc = $ouser->getUType(1);
				$showInterpretBtn=0;
				if(!empty($is_doc)){
					$flgUserAdmnSignExist = $ouser->getSign($flgCheck="1");				
					if(!empty($flgUserAdmnSignExist)){
						$showInterpretBtn=1;
					}
				}
				
				
				$echo="";
				//$echo .= "VF - GL";
				$sql = "SELECT descOd, descOs,synthesis_od,synthesis_os, examDate, vf_gl_id, sign_path, phyName FROM vf_gl WHERE formId = '".$form_id."' AND patientId = '".$patient_id."' AND purged='0' AND del_status='0' ORDER BY examDate DESC, vf_gl_id DESC  ";
				$rez = sqlStatement($sql);
				$tst_ln = mysql_num_rows($rez);
				$row =false;
				//
				$sql = "SELECT descOd, descOs,synthesis_od,synthesis_os, examDate, vf_gl_id, sign_path, phyName FROM vf_gl WHERE patientId = '".$patient_id."' AND purged='0' AND del_status='0' ORDER BY examDate DESC, vf_gl_id DESC  ";
				$rez2 = sqlStatement($sql);
				$tst_ln2 = mysql_num_rows($rez2);
				
				if($tst_ln<=0){	
					$rez=$rez2;
					$tst_ln = $tst_ln2;	
				}
				if($tst_ln>0){
					$row = sqlFetchArray($rez);
				}
				//
				$showPriorSyn=" style=\"visibility:hidden;\" ";
				if($tst_ln2>1){$showPriorSyn="";}
				$sign_path=$phyName="";
				if($row!=false){
					//$descOd = trim($row["descOd"]); 
					//$descOs = trim($row["descOs"]);
					
					$descOd = trim($row["synthesis_od"]); 
					$descOs = trim($row["synthesis_os"]);
					$dateofExam=FormatDate_show($row["examDate"]);
					$vf_gl_id=$row["vf_gl_id"];
					$sign_path=$row["sign_path"];
					$phyName=$row["phyName"];
					
				}
				
				if($tst_ln>0){
				if(!empty($descOd) || !empty($descOs)){
				
					//make bold labels;
					$arC=array("MD:","VFI:","Stage:","Reliab:","Interpret:","Comments:","Artifact:","Hemianopsis:", "Quadranopsia:","Congruity:" );
					$arR=array("<b>MD:</b>","<b>VFI:</b>","<b>Stage:</b>","<b>Reliab:</b>","<b>Interpret:</b>","<b>Comments:</b>","<b>Artifact:</b>","<b>Hemianopsis:</b>", "<b>Quadranopsia:</b>","<b>Congruity:</b>" );
					$descOd=str_replace($arC,$arR,$descOd);
					$descOs=str_replace($arC,$arR,$descOs);					
				}
					$rowspan=1;
					$enm_var = $enm = "VF-GL";
					
					// if already interpret then no show btn
					$showInterpretBtn_style="";
					if((!empty($sign_path) && !empty($phyName))||empty($showInterpretBtn)){	$showInterpretBtn_style=" style=\"display:none;\" "; }
					
					$str = "";
					$str .="<div id=\"dv_vf_gl\" class=\"esum\">".
							"<table>";

					$strDesc = "<td rowspan=\"".$rowspan."\" class=\"edesc\">";
					//$strDesc .="<div></div>";//Used to hgt cell	
					$strDesc .="</td>";
					//$strDesc="";//stopped
					
					$exm_header = "<div class=\"einfo\">".
								"<div class=\"hdr\">".
								"<label onclick=\"openPW('".$enm_var."','".$vf_gl_id."')\">".$enm."</label>".
								"<label>".$dateofExam."</label>";
					$exm_header .= "</div>";
					$exm_header .= "<input type=\"button\" name=\"elem_btnVF-GL_tsttab\" value=\"Test Tab\" onclick=\"go_test_tab()\" class=\"dff_button_sm\" >";
					$exm_header .= "<input type=\"button\" name=\"elem_btnVF-GLPrvSyn\" value=\"Prv Synthesis\" onclick=\"showPrvSynthesis('VF-GL','".$vf_gl_id."')\" class=\"dff_button_sm\" ".$showPriorSyn." > ";
					$exm_header .= "<input type=\"button\" name=\"elem_btnVF-GLRwd\" value=\"Interpret\" onclick=\"tests_interpret('VF-GL','".$vf_gl_id."')\" class=\"dff_button_sm\" ".$showInterpretBtn_style." >";					
					$exm_header .= "</div>";					

					$str .= "<tr>";
					$str .= "<td class=\"edone ehdr\">".$exm_header."</td>";
					$str .= "<td class=\"sumod bgWhite\">".nl2br($descOd)."</td>";
					$str .= "<td class=\"sumos bgWhite\">".nl2br($descOs)."</td>";
					$str .= "".$strDesc;
					$str .= "</tr>";

					$str .= "</table>";
					$str .= "</div>";
					$echo.=$str;
				}				
				
				$descOd=$descOs="";
				
				//$echo .= "OCT - RNFL";								
				$sql = "SELECT descOd, descOs,synthesis_od,synthesis_os,examDate,oct_rnfl_id, sign_path, phyName FROM oct_rnfl WHERE form_id = '".$form_id."' AND patient_id = '".$patient_id."' AND purged='0' AND del_status='0' ORDER BY examDate DESC, oct_rnfl_id DESC ";
				$rez = sqlStatement($sql);				
				$tst_ln = mysql_num_rows($rez);
				$row =false;
				//
				$sql = "SELECT descOd, descOs,synthesis_od,synthesis_os,examDate,oct_rnfl_id, sign_path, phyName FROM oct_rnfl WHERE patient_id = '".$patient_id."' AND purged='0' AND del_status='0' ORDER BY examDate DESC, oct_rnfl_id DESC  ";
				$rez2 = sqlStatement($sql);
				$tst_ln2 = mysql_num_rows($rez2);
				if($tst_ln<=0){	
					$rez=$rez2;
					$tst_ln = $tst_ln2;
				}
				if($tst_ln>0){
					$row = sqlFetchArray($rez);
				}
				//
				$showPriorSyn=" style=\"visibility:hidden;\" ";
				if($tst_ln2>1){$showPriorSyn="";}
				$sign_path=$phyName="";
				if($row!=false){
					//$descOd = trim($row["descOd"]); 
					//$descOs = trim($row["descOs"]);
					
					$descOd = trim($row["synthesis_od"]); 
					$descOs = trim($row["synthesis_os"]);
					$dateofExam=FormatDate_show($row["examDate"]);
					$oct_rnfl_id=$row["oct_rnfl_id"];
					$sign_path=$row["sign_path"];
					$phyName=$row["phyName"];
					
				}
				
				if($tst_ln>0){
				if(!empty($descOd) || !empty($descOs)){
				
					//make bold labels;
					$arC=array("SS:", "Qual:", "Disc area:", "Avg RNFL:", "Disc edema:", "Overall:", "Superior:", "Inferior:", "Temporal:", "Symmetric:", "Comments:", "Vertical C:D:","Nasal:","GCC:","GPA:");
					$arR=array("<b>SS:</b>", "<b>Qual:</b>", "<b>Disc area:</b>", "<b>Avg RNFL:</b>", "<b>Disc edema:</b>", "<b>Overall:</b>", "<b>Superior:</b>", "<b>Inferior:</b>", "<b>Temporal:</b>", "<b>Symmetric:</b>", "<b>Comments:</b>","<b>Vertical C:D:</b>","<b>Nasal:</b>","<b>GCC:</b>","<b>GPA:</b>" );
					$descOd=str_replace($arC,$arR,$descOd);
					$descOs=str_replace($arC,$arR,$descOs);					
				}
					$rowspan=1;
					$enm_var = $enm = "OCT - RNFL";
				
					// if already interpret then no show btn
					$showInterpretBtn_style="";
					if((!empty($sign_path) && !empty($phyName))||empty($showInterpretBtn)){	$showInterpretBtn_style=" style=\"display:none;\" "; }
					
					$str = "";
					$str .="<div id=\"dv_oct_rnfl\" class=\"esum\">".
							"<table>";

					$strDesc = "<td rowspan=\"".$rowspan."\" class=\"edesc\">";
					//$strDesc .="<div></div>";//Used to hgt cell	
					$strDesc .="</td>";
					//$strDesc="";//stopped
					
					$exm_header = "<div class=\"einfo\">".
								"<div class=\"hdr\">".
								"<label onclick=\"openPW('".$enm_var."','".$oct_rnfl_id."')\">".$enm."</label>".
								"<label>".$dateofExam."</label>";
					$exm_header .= "</div>";
					$exm_header .= "<input type=\"button\" name=\"elem_btnOCT-RNFL_tsttab\" value=\"Test Tab\" onclick=\"go_test_tab()\" class=\"dff_button_sm\" >";
					$exm_header .= "<input type=\"button\" name=\"elem_btnOCT-RNFLPrvSyn\" value=\"Prv Synthesis\" onclick=\"showPrvSynthesis('OCT-RNFL','".$oct_rnfl_id."')\" class=\"dff_button_sm\" ".$showPriorSyn." > ";					
					$exm_header .= "<input type=\"button\" name=\"elem_btnOCT-RNFLRwd\" value=\"Interpret\" onclick=\"tests_interpret('OCT-RNFL','".$oct_rnfl_id."')\" class=\"dff_button_sm\" ".$showInterpretBtn_style." >";					
					$exm_header .= "</div>";

					$str .= "<tr>";
					$str .= "<td class=\"edone ehdr\">".$exm_header."</td>";
					$str .= "<td class=\"sumod bgWhite\">".nl2br($descOd)."</td>";
					$str .= "<td class=\"sumos bgWhite\">".nl2br($descOs)."</td>";
					$str .= "".$strDesc;
					$str .= "</tr>";

					$str .= "</table>";
					$str .= "</div>";
					$echo.=$str;
				}				
				
				return $echo;
				
			break;

			default:
				
			//exit("DEFULT");

			break;

		}
		
	}
	
	//GET Purge Htm
	function getPurgedHtm($exm,$arrTempProc=array(), $patient_id = '', $form_id = ''){
		//global $form_id,$patient_id;
		$htmPurge="";
		include_once($GLOBALS['srcdir']."/classes/work_view/wv_functions.php");
		include_once($GLOBALS['srcdir']."/classes/work_view/ChartNote.php");
		include_once($GLOBALS['srcdir']."/classes/work_view/Onload.php");
		
		$chartNoteObj = New ChartNote($patient_id, $form_id);
		$onLoadObj = New Onload();
		
		switch($exm){
			case "Pupil":			
				$sql = "SELECT ".
					 "c2.isPositive AS flagPosPupil, c2.wnl AS flagWnlPupil, c2.examinedNoChange AS chPupil, ".
					 "c2.sumOdPupil, c2.sumOsPupil, c2.pupil_id, ".
					 "c2.wnlPupilOd, c2.wnlPupilOs, c2.descPupil,c2.perrla, ".
					 "c2.statusElem AS se_pupil, c2.other_values, c2.purgerId, c2.purgeTime, c2.wnl_value ".
					 "FROM chart_master_table c1 ".
					 "INNER JOIN chart_pupil c2 ON c2.formId = c1.id  AND c2.purged!='0'  ".
					 "WHERE c1.id = '".$form_id."' AND c1.patient_id = '".$patient_id."' ".
					 "ORDER BY c2.purgeTime DESC";
				$rz = sqlStatement($sql);
				for($i=0;$row=sqlFetchArray($rz);$i++){					
					
					$elem_noChangePupil=assignZero($row["chPupil"]);
					$elem_wnlPupil=assignZero($row["flagWnlPupil"]);
					$elem_posPupil=assignZero($row["flagPosPupil"]);
					//Pupil
					$elem_wnlPupilOd=assignZero($row["wnlPupilOd"]);
					$elem_wnlPupilOs=assignZero($row["wnlPupilOs"]);
					$elem_wnl_value=$row["wnl_value"];
					$elem_perrlaPupil = assignZero($row["perrla"]);
					$elem_se_Pupil = $row["se_pupil"];
					$elem_sumOdPupil = $row["sumOdPupil"];
					$elem_sumOsPupil = $row["sumOsPupil"];
					$elem_pupil_id = $row["pupil_id"];
					$elem_txtDesc_pupil = stripslashes(trim($row["descPupil"]));
					$elem_other_values = $row["other_values"];
					
					//Perrla
					if($elem_perrlaPupil == 1){
						$elem_sumOdPupil = $elem_sumOsPupil = "PERRLA";
					}
					
					//Wnl
					$wnlString = !empty($elem_wnl_value) ? $elem_wnl_value : $chartNoteObj->getExamWnlStr("Pupil", $patient_id, $form_id); //"PERRLA, -ve APD"
					list($elem_sumOdPupil,$elem_sumOsPupil) = $onLoadObj->setWnlValuesinSumm(array("wVal"=>$wnlString,
											"wOd"=>$elem_wnlPupilOd,"sOd"=>$elem_sumOdPupil,
											"wOs"=>$elem_wnlPupilOs,"sOs"=>$elem_sumOsPupil));
					//Color
					$cnm = $flgSe_Pupil_Od = $flgSe_Pupil_Os = "";
					if(!isset($bgColor_pupil)){
						//$elem_se_Pupil
						if(!empty($elem_se_Pupil)){
							$tmpArrSe = $chartNoteObj->se_elemStatus("PUPIL","0",$elem_se_Pupil);
							$flgSe_Pupil_Od = $tmpArrSe["Pupil"]["od"];
							$flgSe_Pupil_Os = $tmpArrSe["Pupil"]["os"];
						}
					}
					
					//Nochanged
					if(!empty($elem_se_Pupil)&&strpos($elem_se_Pupil,"=1")!==false){
						$elem_noChangePupil=1;
					}
					
					$arr=array();
					$arr["ename"] = "Pupil";
					$arr["subExm"][0] = $onLoadObj->getArrExms_ms(array("enm"=>"Pupil",
													"sOd"=>$elem_sumOdPupil,"sOs"=>$elem_sumOsPupil,
													"fOd"=>$flgSe_Pupil_Od,"fOs"=>$flgSe_Pupil_Os,
													"pos"=>$elem_posPupil));
					$arr["nochange"] = $elem_noChangePupil;
					$arr["oe"] = $oneeye;
					$arr["desc"] = htmlentities($elem_txtDesc_pupil);
					$arr["bgColor"] = "".$bgColor_pupil;
					$arr["other_values"] = $elem_other_values;
					$arr["purgerId"] = $row["purgerId"];
					$arr["purgeTime"] = $row["purgeTime"];
					$arr["exm_flg_se"] = array($flgSe_Pupil_Od,$flgSe_Pupil_Os);				
					$htmPurge .= getSummaryHTML_purged($arr);				
					
				}			
				
			break;
			
			case "EOM":
				$oEOM=new EOM($patient_id,$form_id);			
				$sql ="SELECT ".
					"c3.isPositive AS flagPosEom, c3.wnl AS flagWnlEom, c3.examined_no_change  AS chEom, ".
					"c3.examined_no_change2, c3.examined_no_change3, ".
					"c3.sumEom, c3.eomDrawing, c3.eom_id, c3.descEom, c3.statusElem, c3.purgerId, c3.purgeTime, ".
					"c3.eomDrawing_2, c3.idoc_drawing_id, c3.wnl_value ".
					"FROM chart_master_table c1 ".
					"INNER JOIN chart_eom c3 ON c3.form_id = c1.id AND c3.purged!='0'  ".
					"WHERE c1.id = '".$form_id."' AND c1.patient_id='".$patient_id."' ".
					"ORDER BY c3.purgeTime DESC";
				$rz=sqlStatement($sql);			
				for($i=0;$row=sqlFetchArray($rz);$i++){
					if(!empty($row["chEom"])&&!empty($row["examined_no_change2"])&&!empty($row["examined_no_change3"])){
						$elem_noChangeEom=assignZero($row["chEom"]);
					}
					$elem_wnlEom=assignZero($row["flagWnlEom"]);
					$elem_posEom=assignZero($row["flagPosEom"]);
					$elem_eomDraw = $row["eomDrawing"];
					$elem_sumEom = $row["sumEom"];
					$elem_eom_id = $row["eom_id"];
					$elem_txtDesc_eom = stripslashes(trim($row["descEom"]));
					$elem_se_Eom=$row["statusElem"];	
					$elem_wnl_value=$row["wnl_value"];	
					
					if(!empty($row["idoc_drawing_id"])) {
						$drawdocId = $elem_eom_id;
					}elseif( !empty($row["eomDrawing_2"]) ){
						$ardrawApp = array($row["eomDrawing_2"]);
					}

					
					//StatusFlag
					$flgSe_Eom = "";
					if(!isset($bgColor_eom)){
						if(!empty($elem_se_Eom) && strpos($elem_se_Eom,'Eom=1')!==false){
							$flgSe_Eom=1;	
						}
					}
					
					$flgSe_Draw_Od = $flgSe_Draw_Os = "0";
					$tmpArrSe=array();
					if(!isset($bgColor_eom)){
						if(!empty($elem_se_Eom)){
							if(strpos($elem_se_Eom,"Eom3=1")!==false){ $flgSe_Draw_Od ="1"; $flgSe_Draw_Os ="1";  }
						}
					}

					//WNL : "~NPC = WNL, EOM = Full & Ortho"
					$wnlString = !empty($elem_wnl_value) ? $elem_wnl_value : $chartNoteObj->getExamWnlStr("EOM", $patient_id, $form_id); //"Full ductions, alternate cover test - ortho distance and near."
					list($elem_sumEom) = $onLoadObj->setWnlValuesinSumm(array("wVal"=>$wnlString,
																	"wOd"=>$elem_wnlEom,"sOd"=>$elem_sumEom));
					//---------			
					
					//Nochanged
					if(!empty($elem_se_Eom)&&strpos($elem_se_Eom,"=1")!==false){
						$elem_noChangeEom=1;
					}			

					$arr=array();
					$arr["ename"] = "EOM";
					$arr["subExm"][0] = $onLoadObj->getArrExms_ms(array("enm"=>"EOM",
													"sOd"=>$elem_sumEom,
													"fOd"=>$flgSe_Eom,"pos"=>$elem_posEom,
													"arcJsOd"=>$moeArc_od, "arcCssOd"=>$flgArcColor_od,
													"arcJsOs"=>"", "arcCssOs"=>""
													));
					$arr["nochange"] = $elem_noChangeEom;
					$arr["desc"] = htmlentities($elem_txtDesc_eom);
					$arr["bgColor"] = "".$bgColor_eom;
					$arr["purgerId"] = $row["purgerId"];
					$arr["purgeTime"] = $row["purgeTime"];
					$arr["drawdocId"] = $drawdocId;
					$arr["drawapp"] = $ardrawApp;
					$arr["drawSE"] = array($flgSe_Draw_Od,$flgSe_Draw_Os);
					$htmPurge .= getSummaryHTML_purged($arr);	
				}
				
			break;
			
			case "External":
				$oEE=new ExternalExam($patient_id,$form_id);
				$sql ="SELECT ".
					"c4.isPositive, c4.posEe AS flagPosEe, c4.posDraw, c4.wnl, c4.wnlEe AS flagWnlEe, c4.wnlDraw, ".
					"c4.examined_no_change, c4.ncEe AS chEe, c4.ncDraw, ".
					"c4.ee_drawing, c4.external_exam_summary, ".
					"c4.sumOsEE, c4.ee_id, ".
					"c4.wnlEeOd, c4.wnlEeOs, c4.descExternal, ".
					"c4.statusElem AS se_ee, c4.purgerId, c4.purgeTime, ".
					"c4.ee_drawing, c4.idoc_drawing_id, c4.wnl_value ".
					"FROM chart_master_table c1 ".
					"INNER JOIN chart_external_exam c4 ON c4.form_id = c1.id AND c4.purged!='0'  ".
					"WHERE c1.id = '".$form_id."' AND c1.patient_id='".$patient_id."' ".
					"ORDER BY c4.purgeTime DESC";			
				$rz = sqlStatement($sql);
				for($i=0;$row=sqlFetchArray($rz);$i++){
					$elem_noChangeEe=assignZero($row["chEe"]);
					$elem_wnlEe=assignZero($row["flagWnlEe"]);
					$elem_posEe=assignZero($row["flagPosEe"]);
					$elem_wnlEeOd=assignZero($row["wnlEeOd"]);
					$elem_wnlEeOs=assignZero($row["wnlEeOs"]);
					$elem_wnl_value=$row["wnl_value"];
					$elem_se_Ee = $row["se_ee"];
					$elem_eeDraw = $this->isAppletDrawn($row["ee_drawing"]);
					$elem_sumOdEE = $row["external_exam_summary"];
					$elem_sumOsEE = $row["sumOsEE"];
					$elem_ee_id = $row["ee_id"];
					$elem_txtDesc_ee = stripslashes(trim($row["descExternal"]));
					
					if(!empty($row["idoc_drawing_id"])) {
						$drawdocId = $elem_ee_id;
					}elseif( !empty($row["ee_drawing"]) ){
						$ardrawApp = array($row["ee_drawing"]);
					}

					
					//Wnl
					$wnlString = !empty($elem_wnl_value) ? $elem_wnl_value : $chartNoteObj->getExamWnlStr("External", $patient_id, $form_id); //WNL
					list($elem_sumOdEE,$elem_sumOsEE) = $onLoadObj->setWnlValuesinSumm(array("wVal"=>$wnlString,
								"wOd"=>$elem_wnlEeOd,"sOd"=>$elem_sumOdEE,
								"wOs"=>$elem_wnlEeOs,"sOs"=>$elem_sumOsEE));
					
					//Color
					$flgSe_Ee_Od = $flgSe_Ee_Os = "";
					$flgSe_Draw_Od = $flgSe_Draw_Os = "0";
					if(!isset($bgColor_external)){
						if(!empty($elem_se_Ee)){
							$tmpArrSe = $chartNoteObj->se_elemStatus("EXTERNAL","0",$elem_se_Ee);
							$flgSe_Ee_Od = $tmpArrSe["Con"]["od"];
							$flgSe_Ee_Os = $tmpArrSe["Con"]["os"];
							$flgSe_Draw_Od=$tmpArrSe["Draw"]["od"];
							$flgSe_Draw_Os=$tmpArrSe["Draw"]["os"];
						}
					}
					
					//Nochanged
					if(!empty($elem_se_Ee)&&strpos($elem_se_Ee,"=1")!==false){
						$elem_noChangeEe=1;
					}
					
					//---------

					$arr=array();
					$arr["ename"] = "External"; 
					$arr["subExm"][0] = $onLoadObj->getArrExms_ms(array("enm"=>"External",
														"sOd"=>$elem_sumOdEE,"sOs"=>$elem_sumOsEE,
														"fOd"=>$flgSe_Ee_Od,"fOs"=>$flgSe_Ee_Os,"pos"=>$elem_posEe,
														"arcJsOd"=>$moeArc_od,"arcJsOs"=>$moeArc_os,
														"arcCssOd"=>$flgArcColor_od,"arcCssOs"=>$flgArcColor_os
														));
					$arr["nochange"] = $elem_noChangeEe;
					$arr["oe"] = $oneeye;
					$arr["desc"] = $elem_txtDesc_ee;
					$arr["bgColor"] = "".$bgColor_external;
					$arr["purgerId"] = $row["purgerId"];
					$arr["purgeTime"] = $row["purgeTime"];
					$arr["exm_flg_se"] = array($flgSe_EE_Od,$flgSe_EE_Os);
					$arr["drawdocId"] = $drawdocId;
					$arr["drawapp"] = $ardrawApp;
					$arr["drawSE"] = array($flgSe_Draw_Od,$flgSe_Draw_Os);
					$htmPurge .= getSummaryHTML_purged($arr);
				}
				
				//---------
			break;

			case "LA":
				include_once($GLOBALS['srcdir']."/classes/work_view/LA.php");
				include_once($GLOBALS['srcdir']."/classes/work_view/LALoader.php");
				$oload = new LALoader($patient_id,$form_id);
				$htmPurge .= $oload->getPurgedHtm();				
			break;
			
			case "IOP/Gonio":
				$arrIOPVals=array();
				$arrGonioVals=array();
				$arrDilationVals=array();
				$arrOODVals=array();
				$arrPurgeTime=array();
				$arrPurgerID=array();
				
				//Get Values IOP
				$sql = "SELECT ".
					//c5-chart_iop-------
					"c5.sumOdIop, c5.sumOsIop, ".
					"c5.tetracaine,c5.flourocaine,c5.alcaine,c5.iop_time, c5.iop_id, c5.anesthetic,c5.statusElem AS statusElem_IOP, ".
					"c5.purgerId, c5.purgeTime ".
					"FROM chart_master_table c1 ".
					"INNER JOIN chart_iop c5 ON c5.form_id = c1.id AND c5.purged!='0' ".
					"WHERE c1.id = '".$form_id."' AND c1.patient_id='".$patient_id."' ".
					"ORDER BY c5.purgeTime DESC  ";
				$rez = sqlStatement($sql);
				for($i=0;$row = sqlFetchArray($rez);$i++){
					
					$prgTime=$row["purgeTime"];
					$prgrId=$row["purgerId"];
					if(!in_array($prgTime,$arrPurgeTime)){
						$arrPurgeTime[]=$prgTime;
						$arrPurgerID[$prgTime]=$prgrId;
					}
					
					$arrIOPVals[$prgTime] = $row;				
				}

				//Get Values Gonio
				$sql ="SELECT ".	
					//c6-chart_gonio-------
					"c6.gonio_od_drawing, c6.isPositive AS flagPosIopGonio, ".
					"c6.wnl AS flagWnlIopGonio, c6.examined_no_change AS chGon, ".
					"c6.gonio_os_drawing, c6.gonio_od_summary, c6.gonio_os_summary, ".
					"c6.wnlOd AS wnlGonioOd, c6.wnlOs AS wnlGonioOs, c6.desc_ig, ".
					"c6.posGonio, c6.posDraw AS posDrawGonio, c6.wnlGonio, c6.wnlDraw AS wnlDrawGonio, ".
					"c6.wnlDrawOd AS wnlDrawGonioOd, c6.wnlDrawOs AS wnlDrawGonioOs, c6.gonio_id, c6.noChange_drawing, ".
					"c6.examDateGonio, c6.idoc_drawing_id, c6.purgerId, c6.purgeTime, ".
					"c6.statusElem AS se_gon, c6.wnl_value ".
					"FROM chart_master_table c1 ".
					"INNER JOIN chart_gonio c6 ON c6.form_id = c1.id AND c6.purged!='0' ".
					"WHERE c1.id = '".$form_id."' AND c1.patient_id='".$patient_id."' ".
					"ORDER BY c6.purgeTime DESC  ";			
				$rez = sqlStatement($sql);
				for($i=0;$row = sqlFetchArray($rez);$i++){
					$prgTime=$row["purgeTime"];
					$prgrId=$row["purgerId"];
					if(!in_array($prgTime,$arrPurgeTime)){
						$arrPurgeTime[]=$prgTime;
						$arrPurgerID[$prgTime]=$prgrId;
					}
					
					$arrGonioVals[$prgTime] = $row;
				}
				
				//Get Value Dilation
				$sql ="SELECT ".	
					//c14-chart_dialation-------
					"c14.patientAllergic, c14.pheny25, c14.tropicanide, ".
					"c14.cyclogel, c14.dilated_other, c14.dilated_time, ".
					"c14.dia_id, c14.eyeSide,c14.noDilation,c14.mydiacyl5, c14.dilation,c14.statusElem AS statusElem_Dilation, ".				
					"c14.unableDilation, ".
					"c14.purgerId, c14.purgeTime ".
					"FROM chart_master_table c1 ".
					"INNER JOIN chart_dialation c14 ON c14.form_id = c1.id AND c14.purged!='0' ".
					"WHERE c1.id = '".$form_id."' AND c1.patient_id='".$patient_id."' ".
					"ORDER BY c14.purgeTime DESC  ";	
				$rez = sqlStatement($sql);
				for($i=0;$row = sqlFetchArray($rez);$i++){
					$prgTime=$row["purgeTime"];
					$prgrId=$row["purgerId"];
					if(!in_array($prgTime,$arrPurgeTime)){
						$arrPurgeTime[]=$prgTime;
						$arrPurgerID[$prgTime]=$prgrId;
					}
					
					$arrDilationVals[$prgTime] = $row;
				}		
				
				//Get Value OOD
				$sql ="SELECT ".	
					//C15-chart_ood ----
					"c15.ood_id, c15.ood,c15.statusElem AS statusElem_OOD, c15.eye AS eye_ood,  ".
					"c15.purgerId, c15.purgeTime ".
					"FROM chart_master_table c1 ".
					"INNER JOIN chart_ood c15 ON c15.form_id = c1.id AND c15.purged!='0' ".
					"WHERE c1.id = '".$form_id."' AND c1.patient_id='".$patient_id."' ".
					"ORDER BY c15.purgeTime DESC  ";				
				$rez = sqlStatement($sql);
				for($i=0;$row = sqlFetchArray($rez);$i++){
					$prgTime=$row["purgeTime"];
					$prgrId=$row["purgerId"];
					if(!in_array($prgTime,$arrPurgeTime)){
						$arrPurgeTime[]=$prgTime;
						$arrPurgerID[$prgTime]=$prgrId;
					}
					
					$arrOODVals[$prgTime] = $row;
				}	
				
				$oGonio=new Gonio($patient_id,$form_id);			
				
				//$row = sqlQuery($sql);			
				//$row=$oGonio->sqlExe($sql);
				//$rez = sqlStatement($sql);
				//for($i=0;$row = sqlFetchArray($rez);$i++){			
				if(count($arrPurgeTime) > 0){
					//Sort
					arsort($arrPurgeTime);
					
					foreach($arrPurgeTime AS $k => $v){
						
						$purgTime = $v;
						$prgrid=$arrPurgerID[$purgTime];						

						//IOP
						$row=$arrIOPVals[$purgTime];
						$elem_tetracaine=$row["tetracaine"];
						$elem_flourocaine=$row["flourocaine"];
						$elem_alcaine=$row["alcaine"];
						$elem_iop_time=$row["iop_time"];
						$elem_posIop = 0;
						$elem_wnlIop = 0;
						$elem_wnlIopOd=0;//assignZero($row["wnlIopOd"]);
						$elem_wnlIopOs=0;//assignZero($row["wnlIopOs"]);
						$elem_sumOdIop = $row["sumOdIop"];
						$elem_sumOsIop = $row["sumOsIop"];
						$elem_anesthetic = $row["anesthetic"];
						$elem_se_iop = $row["statusElem_IOP"];
						$elem_iop_id = $row["iop_id"];				
						
						//Gonio
						$row=$arrGonioVals[$purgTime];
						if(!empty($row["chGon"])&&!empty($row["noChange_drawing"])){
							$elem_noChangeGonio=assignZero($row["chGon"]); //For Gonio only
						}
						$elem_wnlIopGonio=assignZero($row["flagWnlIopGonio"]); //for gonio only
						$elem_posIopGonio=assignZero($row["flagPosIopGonio"]); // for gonio only
						$elem_posGonio=$row["posGonio"];
						$elem_posDrawGonio=$row["posDrawGonio"];
						$elem_wnlGonio=$row["wnlGonio"];
						$elem_wnlDrawGonio=$row["wnlDrawGonio"];
						//$elem_sideIop = $row["sideIop"];
						$elem_wnlGonioOd=assignZero($row["wnlGonioOd"]);
						$elem_wnlGonioOs=assignZero($row["wnlGonioOs"]);
						$elem_wnlDrawGonioOd=assignZero($row["wnlDrawGonioOd"]);
						$elem_wnlDrawGonioOs=assignZero($row["wnlDrawGonioOs"]);
						$elem_wnl_value_gonio=$row["wnl_value"];
						$elem_se_Gonio = $row["se_gon"];
						$elem_sumOdGon = $row["gonio_od_summary"];
						$elem_sumOsGon = $row["gonio_os_summary"];
						
						$elem_ncGonio = assignZero($row["chGon"]);
						$elem_ncDrawGonio = assignZero($row["noChange_drawing"]);
						$examdate = $oGonio->formatDate($row["examDateGonio"]);
						$elem_gonio_id = $row["gonio_id"];
						$elem_txtDesc_iop = stripslashes(trim($row["desc_ig"])); //Gonio
						if(!empty($row["idoc_drawing_id"])) {
							$drawdocId = $elem_gonio_id;
						}elseif( !empty($row["gonio_od_drawing"]) ){
							$ardrawApp = array($row["gonio_od_drawing"]);
						}

						//Dialation
						$row=$arrDilationVals[$purgTime];
						$elem_pheny25 = $row["pheny25"];
						$elem_tropicanide = $row["tropicanide"];
						$elem_cyclogel = $row["cyclogel"];
						$elem_dilated_other = $row["dilated_other"];
						$elem_dilated_time = $row["dilated_time"];
						$elem_eyeSide = $row["eyeSide"];
						$patientAllergic=assignZero($row["patientAllergic"]);
						$elem_no_dilation = $row["noDilation"];
						$elem_mydiacyl5=$row["mydiacyl5"];
						$elem_dilation=$row["dilation"];
						$elem_se_dilation = $row["statusElem_Dilation"];
						$unableDilation=$row["unableDilation"];
						$dilated_mm=$row["dilated_mm"];
						$elem_dia_id = $row["dia_id"];
						
						//C15-chart_ood
						$row=$arrOODVals[$purgTime];
						$ood = $row["ood"];
						$statusElem_OOD = $row["statusElem_OOD"]; 
						$eye_ood = $row["eye_ood"];
						$elem_ood_id = $row["ood_id"];				
						
						//--------------------				
						
						//StatusFlag IOP
						$flgSe_IOP = 0;
						if(!isset($bgColor_IOP)){
							if(!empty($elem_se_iop)){
								$flgSe_IOP=1;	
							}
						}
						
						//Anes
						$spAnesTime="";
						$sumAnes = $sumAnes_od = $sumAnes_os = "";
						if(!empty($elem_anesthetic)){
							$arrAnes=unserialize($elem_anesthetic);
							$tmp = count($arrAnes);
							$spAnesTime = $arrAnes[$tmp-1]["time"];
							
							//*
							for($a=0;$a<$tmp;$a++){
								if((!empty($arrAnes[$a]["anes"])||!empty($arrAnes[$a]["other"]))&&!empty($arrAnes[$a]["time"])){
									
									$t = "";
									if(!empty($arrAnes[$a]["anes"])){
										$t .= $arrAnes[$a]["anes"];
									}						
									
									if(!empty($arrAnes[$a]["other"])){
										if(!empty($t)){$t .= ",";}
										$t .= "".$arrAnes[$a]["other"];
									}
									
									$t = preg_replace("/(\s)*\,(\s)*$/i", "", $t);							
									$t = str_replace(",",", ",$t); 
									$sumAnes .= $t." ".trim($arrAnes[$a]["time"])."<br/>";						
								}
							}
							
							$sumAnes_od = $sumAnes_os = $sumAnes;				
							// */

						}else if(!empty($elem_tetracaine) || !empty($elem_flourocaine) || !empty($elem_alcaine)){
							$spAnesTime = $elem_iop_time;
							
							$t = "";
							if(!empty($elem_tetracaine)) { $t .= "Tetracaine,"; } 
							if(!empty($elem_flourocaine)) { $t .= "Flourocaine, "; } 
							if(!empty($elem_alcaine)) { $t .= "Alcaine "; } 
							$t .= " ".$spAnesTime;
							$sumAnes = $sumAnes_od = $sumAnes_os =  $t;
						}

						//Dilation --
						$spDialTime="";
						$arrDilatInx = array("pheny25","mydiacyl5","mydiacyl1","Cyclogyl","Paremyd");
						$arrDilatVals = array("Phenylephrine 2.5%","Mydriacyl 1/2%","Mydriacyl 1%","Cyclogyl 1%","Paremyd");
						
						$sumDilation = $sumDilation_od = $sumDilation_os = "";
						if(!empty($elem_dia_id) && !empty($elem_se_dilation)){
							if(!empty($elem_dilation)){
								$arrDilation=unserialize($elem_dilation);
								$tmp = count($arrDilation);
								for($a=0;$a<$tmp;$a++){
									if((!empty($arrDilation[$a]["dilate"])||!empty($arrDilation[$a]["other_desc"]))&&!empty($arrDilation[$a]["time"])){
										
										//if(empty($spDialTime)){
											$spDialTime = trim($arrDilation[$a]["time"].' '.$elem_eyeSide);							
											//break;
										//}
										
										$t = "";
										if(!empty($arrDilation[$a]["dilate"])){
											$t = str_replace($arrDilatInx,$arrDilatVals,$arrDilation[$a]["dilate"]);
										}						
										
										if(!empty($arrDilation[$a]["other_desc"])){
											$t = str_replace("Other", "".$arrDilation[$a]["other_desc"],$t);
										}
										$t = preg_replace("/(\s)*\,(\s)*$/i", "", $t);							
										$t = str_replace(",",", ",$t); 
										$sumDilation .= $t." ".trim($arrDilation[$a]["time"])."<br/>";
										
									}
								}
								
							}else {
								if(!empty($elem_pheny25) || !empty($elem_tropicanide) || !empty($elem_cyclogel) || !empty($elem_mydiacyl5) ||
									!empty($elem_dilated_other)){
									$spDialTime = trim($elem_dilated_time.' '.$elem_eyeSide);
									
									$t = "";
									if(!empty($elem_pheny25)) {$t .= "Phenylephrine 2.5%,";}
									if(!empty($elem_tropicanide)) {$t .= "Mydriacyl 1%,";}
									if(!empty($elem_cyclogel)) {$t .= "Cyclogyl 1%,";}
									if(!empty($elem_mydiacyl5)) {$t .= "Mydriacyl 5%,";}
									if(!empty($elem_dilated_other)) {$t .= $elem_dilated_other.",";}
									$t = preg_replace("/(\s)*\,(\s)*$/i", "", $t);							
									$t = str_replace(",",", ",$t); 
									$sumDilation .= $t." ".$elem_dilated_time."<br/>";
									
								}
							}
							
							if($elem_eyeSide=="OU"||$elem_eyeSide=="OD"){
								$sumDilation_od=$sumDilation;
							}
							if($elem_eyeSide=="OU"||$elem_eyeSide=="OS"){
								$sumDilation_os=$sumDilation;
							}
							
							if($elem_no_dilation==1){
								$spDialTime = "No Dilation";
							}else if($unableDilation==1){
								$spDialTime = "Refuse Dilation";
							}
						}
						//Dilation --
						
						//OOD
						$sumOOD = $sumOOD_od = $sumOOD_os = "";
						$arrOODInx = array("Piol","Alphagan","Iopidine","Diamox");
						$arrOODVals = array("Pilo 1%","Alphagan P 0.1%","Iopidine 0.5%","Diamox");
						
						if(!empty($elem_ood_id) && !empty($statusElem_OOD)){
							if(!empty($ood)){
								$arrOOD=unserialize($ood);
								$tmp = count($arrOOD);
								for($a=0;$a<$tmp;$a++){
									if((!empty($arrOOD[$a]["ood"])||!empty($arrOOD[$a]["other_desc"]))&&!empty($arrOOD[$a]["time"])){
										
										$t = "";
										if(!empty($arrOOD[$a]["ood"])){
											$t = str_replace($arrOODInx,$arrOODVals,$arrOOD[$a]["ood"]);
										}
										
										if(!empty($arrOOD[$a]["other_desc"])){
											$t = str_replace("Other", "".$arrOOD[$a]["other_desc"],$t);
										}
										$t = preg_replace("/(\s)*\,(\s)*$/i", "", $t);							
										$t = str_replace(",",", ",$t); 
										$sumOOD .= $t." ".trim($arrOOD[$a]["time"])."<br/>";
									}
								}
							}
						}
						
						if($eye_ood=="OU"||$eye_ood=="OD"){
							$sumOOD_od=$sumOOD;
						}
						if($eye_ood=="OU"||$eye_ood=="OS"){
							$sumOOD_os=$sumOOD;
						}			
						//OOD
					
						//WNL
						//Gonio --
						$wnlString = !empty($elem_wnl_value_gonio) ? $elem_wnl_value_gonio : $chartNoteObj->getExamWnlStr("Gonio", $patient_id, $form_id);
						list($elem_sumOdGon,$elem_sumOsGon) = $onLoadObj->setWnlValuesinSumm(array("wVal"=>$wnlString,
												"wOd"=>$elem_wnlGonioOd,"sOd"=>$elem_sumOdGon,
												"wOs"=>$elem_wnlGonioOs,"sOs"=>$elem_sumOsGon));
						//Nochanged
						if(!empty($elem_se_Gonio)&&strpos($elem_se_Gonio,"=1")!==false){
							$elem_noChangeGonio=1;
						}		

						//----
						//is Change is made in new chart -----
							$flgSe_Gonio_Od = $flgSe_Gonio_Os = "0";
							$flgSe_Draw_Od = $flgSe_Draw_Os = "0";
							if(!isset($bgColor_gonio)){
								if(!empty($elem_se_Gonio)){
									$tmpArrSe = $chartNoteObj->se_elemStatus("GONIO","0",$elem_se_Gonio);
									$flgSe_Gonio_Od = $tmpArrSe["Iop"]["od"];
									$flgSe_Gonio_Os = $tmpArrSe["Iop"]["os"];
									$flgSe_Draw_Od = $tmpArrSe["Iop3"]["od"];
									$flgSe_Draw_Os = $tmpArrSe["Iop3"]["os"];
								}
							}
						//is Change is made in new chart -----			

						$arr=array();
						$arr["ename"] = "IOP/Gonio";
						
						//IOP
						if(!empty($elem_iop_id)){
						$arr["subExm"][] = $onLoadObj->getArrExms_ms(array("enm"=>"IOP",
															"sOd"=>$elem_sumOdIop,"sOs"=>$elem_sumOsIop,
															"fOd"=>$flgSe_IOP,"fOs"=>$flgSe_IOP,
															"arcJsOd"=>$moeArc["od"]["Iop"],"arcJsOs"=>$moeArc["os"]["Iop"],
															"arcCssOd"=>$flgArcColor["od"]["Iop"],"arcCssOs"=>$flgArcColor["os"]["Iop"],
															"enm_2"=>"IOP"));
						}									
						
						//Gonio
						if(!empty($elem_gonio_id)){
						$arr["subExm"][] = $onLoadObj->getArrExms_ms(array("enm"=>"Gonio",
															"sOd"=>$elem_sumOdGon,"sOs"=>$elem_sumOsGon,
															"fOd"=>$flgSe_Gonio_Od,"fOs"=>$flgSe_Gonio_Os,"pos"=>$elem_posGonio,
															"arcJsOd"=>$moeArc["od"]["Gonio"],"arcJsOs"=>$moeArc["os"]["Gonio"],
															"arcCssOd"=>$flgArcColor["od"]["Gonio"],"arcCssOs"=>$flgArcColor["os"]["Gonio"],
															"enm_2"=>"Gonio"));
						}
						
						if(!empty($elem_iop_id)){
						//Anes : No archive working  for Anes
						$arr["subExm"][] = $onLoadObj->getArrExms_ms(array("enm"=>"Anesthetic",
													"sOd"=>$sumAnes_od,"sOs"=>$sumAnes_os,
													"fOd"=>$flgSe_IOP,"fOs"=>$flgSe_IOP,
													"arcJsOd"=>$moeArc["od"]["Anes"],"arcJsOs"=>$moeArc["os"]["Anes"],
													"arcCssOd"=>$flgArcColor["od"]["Anes"],"arcCssOs"=>$flgArcColor["os"]["Anes"],
													"enm_2"=>"Anesthetic"));	
						
						}
						
						//Dilation: No archive working  for dilate
						if(!empty($elem_dia_id)){
						$arr["subExm"][] = $onLoadObj->getArrExms_ms(array("enm"=>"Dilation",
															"sOd"=>$sumDilation_od,"sOs"=>$sumDilation_os,
															"fOd"=>$elem_se_dilation,"fOs"=>$elem_se_dilation,
															"arcJsOd"=>$moeArc["od"]["dilation"],"arcJsOs"=>$moeArc["os"]["dilation"],
															"arcCssOd"=>$flgArcColor["od"]["dilation"],"arcCssOs"=>$flgArcColor["os"]["dilation"],
															"enm_2"=>"Dilation"));
						}	
						
						//OOD: No archive working  for ood
						if(!empty($elem_ood_id)){
						$arr["subExm"][] = $onLoadObj->getArrExms_ms(array("enm"=>"Ophth. Drops",
															"sOd"=>$sumOOD_od,"sOs"=>$sumOOD_os,
															"fOd"=>$statusElem_OOD,"fOs"=>$statusElem_OOD,
															"arcJsOd"=>$moeArc["od"]["ood"],"arcJsOs"=>$moeArc["os"]["ood"],
															"arcCssOd"=>$flgArcColor["od"]["ood"],"arcCssOs"=>$flgArcColor["os"]["ood"],
															"enm_2"=>"Ophth. Drops"));														
						}
						
						if(!empty($elem_gonio_id)){	
						//Drawing				
						$arr["subExm"][] = $onLoadObj->getArrExms_ms(array("enm"=>"Drawing",
															"sOd"=>$drawdocId,"sOs"=>"",
															"fOd"=>$flgSe_Draw_Od,"fOs"=>$flgSe_Draw_Os,"pos"=>$elem_posDrawGonio,
															"enm_2"=>"DrawGonio"));					
						}
						
						
						//Sub Exam List
						$arr["seList"] = 	array("IOP"=>array("enm"=>"IOP","pos"=>$elem_posIop,
											"wOd"=>$elem_wnlIopOd,"wOs"=>$elem_wnlIopOs),
										"Gonio"=>array("enm"=>"Gonio","pos"=>$elem_posGonio,
											"wOd"=>$elem_wnlGonioOd,"wOs"=>$elem_wnlGonioOs),
										"DrawGonio"=>array("enm"=>"Drawing","pos"=>$elem_posDrawGonio,
											"wOd"=>$elem_wnlDrawGonioOd,"wOs"=>$elem_wnlDrawGonioOs)
										);
						$arr["nochange"] = $elem_noChangeGonio;
						$arr["oe"] = $oneeye;
						$arr["desc"] = $elem_txtDesc_iop;
						$arr["Dilation"] = array("Anesthetic"=>$spAnesTime,"Dilate"=>$spDialTime,"PtAllergic"=>$patientAllergic);
						$arr["bgColor"] = "".$bgColor_gonio;
						$arr["drawdocId"] = $drawdocId;
						$arr["drawapp"] = $ardrawApp;			
						$arr["drawSE"] = array($flgSe_Draw_Od,$flgSe_Draw_Os);
						$arr["examdate"] = $examdate;
						$arr["purgerId"] = $prgrid;
						$arr["purgeTime"] = $purgTime;
						$arr["exm_flg_se"] = array($flgSe_Gonio_Od,$flgSe_Gonio_Os);
						$htmPurge .= getSummaryHTML_purged($arr);
						//---------
					}
				}
			
			break;
			
			case "SLE":
				include_once($GLOBALS['srcdir']."/classes/work_view/SLE.php");
				include_once($GLOBALS['srcdir']."/classes/work_view/SLELoader.php");
				$oload = new SLELoader($patient_id,$form_id);
				$htmPurge .= $oload->getPurgedHtm();		
			break;

			case "FundusExam":
				$oFE=new FundusExam($patient_id,$form_id);				
				$htmPurge .= $oFE->getPurgedHtm();
			break;
			
			case "RefSurg":
				$oRefSurg = new RefSurg($patient_id,$form_id);
				$sql =  "SELECT ".
						"c2.id_ref_surg, ".
						"c2.isPositive AS flagPosRS, c2.wnl AS flagWnlRS, ".
						"c2.examined_no_change  AS chRS, ".
						"c2.sumOdRefSurg, ".
						"c2.sumOsRefSurg, ".
						"c2.wnlRefSurgOd, c2.wnlRefSurgOs, c2.descRefSurg, c2.purgerId, c2.purgeTime, ".
						"c2.statusElem AS se_RS ".				
						"FROM chart_master_table c1 ".
						"INNER JOIN chart_ref_surgery c2 ON c2.form_id = c1.id AND c2.purged!='0'  ".	
						"WHERE c1.id = '".$form_id."' AND c1.patient_id='".$patient_id."' ".
						"ORDER BY c2.purgeTime DESC ";			
				//$row = sqlQuery($sql);
				//$row=$oRefSurg->sqlExe($sql);
				$rez=sqlStatement($sql);
				for($i=0;$row=sqlFetchArray($rez);$i++){
					$elem_noChangeRS=assignZero($row["chRS"]);
					//$elem_wnlRS=assignZero($row["flagWnlRS"]);
					$elem_posRS=assignZero($row["flagPosRS"]);
					//$elem_wnlRSOd=assignZero($row["wnlRefSurgOd"]);
					//$elem_wnlRSOs=assignZero($row["wnlRefSurgOs"]);
					$elem_se_RS = $row["se_RS"];					
					$elem_sumOdRS = $row["sumOdRefSurg"];
					$elem_sumOsRS = $row["sumOsRefSurg"];
					$elem_RS_id = $row["id_ref_surg"];
					$elem_txtDesc_RS = stripslashes(trim($row["descRefSurg"]));
					
					//Wnl
					list($elem_sumOdRS,$elem_sumOsRS) = $onLoadObj->setWnlValuesinSumm(array("wVal"=>"WNL",
								"wOd"=>$elem_wnlRSOd,"sOd"=>$elem_sumOdRS,
								"wOs"=>$elem_wnlRSOs,"sOs"=>$elem_sumOsRS));
					
					//Color
					$flgSe_RS_Od = $flgSe_RS_Os = "";
					if(!isset($bgColor_RS)){
						if(!empty($elem_se_RS)){
							$tmpArrSe = $chartNoteObj->se_elemStatus("REF_SURG","0",$elem_se_RS);
							$flgSe_RS_Od = $tmpArrSe["1"]["od"];
							$flgSe_RS_Os = $tmpArrSe["1"]["os"];
						}
					}
					//Nochanged
					if(!empty($elem_se_RS)&&strpos($elem_se_RS,"=1")!==false){
						$elem_noChangeRS=1;
					}
					
					//---------
					
					$arr=array();
					$arr["ename"] = "Refractive Surgery"; 
					$arr["subExm"][0] = $onLoadObj->getArrExms_ms(array("enm"=>"Refractive Surgery",
														"sOd"=>$elem_sumOdRS,"sOs"=>$elem_sumOsRS,
														"fOd"=>$flgSe_RS_Od,"fOs"=>$flgSe_RS_Os,"pos"=>$elem_posRS,
														"arcJsOd"=>$moeArc_od,"arcJsOs"=>$moeArc_os,
														"arcCssOd"=>$flgArcColor_od,"arcCssOs"=>$flgArcColor_os
														));
					$arr["nochange"] = $elem_noChangeRS;
					$arr["oe"] = $oneeye;
					$arr["desc"] = $elem_txtDesc_RS;
					$arr["bgColor"] = "".$bgColor_RS;
					$arr["purgerId"] = $row["purgerId"];
					$arr["purgeTime"] = $row["purgeTime"];
					$arr["exm_flg_se"] = array($flgSe_RS_Od,$flgSe_RS_Os);
					$htmPurge .= getSummaryHTML_purged($arr);				
				}
				
			break;

			case "vf_oct_gl":
				
			break;
			
		}
		
		//
		if(!empty($htmPurge)){
			$htmPurge="<div class=\"purged\">".$htmPurge."</div>";
		}
		
		return $htmPurge;
	}
	
	//webs service
	function getSummaryHTML_appwebservice($arr){
		$enm_var = $enm = $arr["ename"];
		$arrSE = $arr["subExm"];
		$seList= isset($arr["seList"]) ? $arr["seList"] : "";
		$nochange = $arr["nochange"]; 
		$oneeye = (!empty($arr["oe"])) ? explode("~!~",$arr["oe"]):array();
		$desc = trim($arr["desc"]);
		$bgColor = $arr["bgColor"];
		$drawdocId = $arr["drawdocId"];
		$drawapp = $arr["drawapp"];	
		$htmPurge = $arr["htmPurge"];	
		$moeMN = $arr["moeMN"];	

		//For Variable
		if($enm=="L&amp;A")$enm_var = "LA";
		else if($enm=="IOP/Gonio")$enm_var = "Gonio";
		else if($enm=="Fundus Exam"||$enm=="Fundus")$enm_var = "RV";
		else if($enm=="Refractive Surgery")$enm_var = "Ref_Surg";	

		//One Eye
		if($oneeye[0]=="Poor View" && $enm_var!="RV"){ 
			$oneeye=array();
		}
		
		//--------------	
		
		///* ----------
		
		$exm_header = "<div class=\"einfo\">".
				"<div class=\"hdr\">".
				"<label onclick=\"openPW('".$enm_var."')\">".$enm."</label>";	
				
		//$exm_header_xml = "<header><title>".$enm."</title>";
		$exm_header_xml ["header"]["title"]=$enm;

		//Pupil : Pharama	--
		if($enm=="Pupil"){
			if(trim($arr["other_values"])=="Pharmacologically dilated on arrival,"){
				$arr["other_values"]["elem_pharmadilated"] = "Pharmacologically dilated on arrival";	
			}else{
				$arr["other_values"] = unserialize($arr["other_values"]);
			}		
		}
		//Pupil : Pharama	--

		//<!-- Reset -->	
		
		/*
		//<!-- WNL -->
		//No Wnl for IOP/Gonio && Refractive Surgery	
		if($enm != "IOP/Gonio" && $enm != "Refractive Surgery"){
			//NO OD+OS Wnl for EOM and External
			if($enm !="EOM" && $enm != "External"){
				$mouseEvt = " onmouseover=\"showWnlOpts(1,1,'".$enm_var."')\" onmouseout=\"showWnlOpts(0,1)\" ";
			}else{
				$mouseEvt = "";
			}
		
		$exm_header .=	"".
				"<input type=\"button\" id=\"elem_btnWnl".$enm_var."\" class=\"wnl\" value=\"WNL\" onClick=\"setWnlValues('".$enm_var."')\"".
				"".$mouseEvt1."> ";
		}
		
		//<!-- No Change -->	
		$tmp = (!empty($nochange)) ? "Prv" : "NC";
		$exm_header .=	"".			
				"<input type=\"button\" id=\"elem_btnNoChange".$enm_var."\" class=\"prv\" value=\"".$tmp."\" onClick=\"autoSaveNoChange('".$enm_var."')\"";
		if($enm !="EOM") $exm_header .=	"	onmouseover=\"showWnlOpts(1,2,'".$enm_var."')\" onmouseout=\"showWnlOpts(0,2)\"";
		$exm_header .=	" > ";
		//hx
		if(!empty($moeMN)){
		$exm_header .= "<label id=\"lblHx".$enm_var."\" class=\"hxlbl\" ".$moeMN." >Hx</label>";
		}
		*/
		
		$exm_header .= "</div>";	
		
		$exm_header .=	"</div>";
		
		//$exm_header_xml .= "</header>";
		// -------------  */
		
		
		
		///IOP :: Dilation & Ansthetic ------------
		/*
		$dilatDropAllergy = $anes = $dilate = $ood= "";
		if($enm == "IOP/Gonio"){		
			if(!empty($arr["Dilation"]["PtAllergic"])){
				$dilatDropAllergy = "<label id=\"ptAlrgic\">Dilation Drop Allergies</label>";
			}		
			
			if(!empty($arr["Dilation"]["Anesthetic"])) {
				$anes = 	"<label id=\"spAnesTime\">".
						//"Anesthetic ".
							"<a href=\"javascript:void(0);\" onclick=\"updateADTime('Anes')\" >".
							$arr["Dilation"]["Anesthetic"]."</a>".
						"</label>";
			}

			if(!empty($arr["Dilation"]["Dilate"])) {			
				if($arr["Dilation"]["Dilate"]=="No Dilation"||$arr["Dilation"]["Dilate"]=="Refuse Dilation"){
					$dilate = $arr["Dilation"]["Dilate"];
				}else{
					$dilate = 	"".
							//"Dilation ".
							"<a href=\"javascript:void(0);\" onclick=\"updateADTime('Dial')\" >".
								$arr["Dilation"]["Dilate"]."</a>";
				}
				$dilate = "<label id=\"spDialTime\">".$dilate."</label>";
			}
			
			if(!empty($arr["Dilation"]["OOD"])) {
				$ood = 	"<label id=\"spOODTime\">".
						//"Anesthetic ".
							"<a href=\"javascript:void(0);\" onclick=\"updateADTime('OOD')\" >".
							$arr["Dilation"]["OOD"]."</a>".
						"</label>";
			}
			
		}
		*/
		///IOP :: Dilation & Ansthetic ------------	
		
		//Summary --
		$str = ""; $str_xml ="";
		$str .=	"<div class=\"esum\">".
					"<table border=\"1\" >";	
		
		//$str_xml .="<exam>";
		///$str_xml .= $exm_header_xml;	
		$str_xml["exam"] = array();
		$str_xml["exam"] = array_merge($str_xml["exam"], $exm_header_xml);
		
			$len = count($arrSE);
			$rowspan = (count($oneeye)>0) ? $len+1 : $len;
			if(($enm_var == "LA" || $enm_var == "Gonio" || $enm_var == "RV" || $enm_var == "SLE") ||
				($enm=="SLE" && !empty($arr["penLight"]))||
				($enm=="Pupil" && $arr["other_values"]["elem_pharmadilated"]=="Pharmacologically dilated on arrival")){
				$rowspan +=1;
			}		
			
			//Pen Light--
			if($enm=="SLE" && !empty($arr["penLight"])){
				
				//Color
				if(empty($bgColor)){
					$tmp="bgWhite";
				}else{
					$tmp="";
				}
				
				$str .= "<tr>";
				$str .= "<td class=\"edone ehdr\">".$exm_header."</td>";
				$str .=	"<td class=\"prph sumod ".$tmp."\" > PenLight Exam "."</td>";
				$str .=	"<td class=\"prph sumos ".$tmp."\"> PenLight Exam "."</td>";
				//$str .= "".$strDesc;
				$str .="</tr>";

				$strDesc="";//Empty Desc after use
				$exm_header = ""; //Empty after use
				
				/*
				$str_xml .="<penlight_exam>
								<od class=\"".$tmp."\"> PenLight Exam </od>
								<os class=\"".$tmp."\"> PenLight Exam </os>
							</penlight_exam>";	
				*/
				$ar_temp=array();
				$ar_temp["penlight_exam"]["od"]["value"] = " PenLight Exam ";
				$ar_temp["penlight_exam"]["od"]["class"] = $tmp;
				
				$ar_temp["penlight_exam"]["os"]["value"] = " PenLight Exam ";
				$ar_temp["penlight_exam"]["os"]["class"] = $tmp;			
				$str_xml["exam"] = array_merge($str_xml["exam"], $ar_temp);
			}
			//Pen Light--

			// Pharmacologically dilated on arrival --
			if($enm=="Pupil" && $arr["other_values"]["elem_pharmadilated"]=="Pharmacologically dilated on arrival"){

				//Color
				if(empty($bgColor)){
					$tmp="bgWhite";
				}else{
					$tmp="";
				}

				$str .= "<tr>";
				$str .=	"<td class=\"edone ehdr\">".$exm_header."</td>";
				if($arr["other_values"]["elem_pharmadilated_eye"]=="OU"||$arr["other_values"]["elem_pharmadilated_eye"]=="OD"){
					$str .=	"<td class=\"prph sumod ".$tmp."\" > Pharmacologically dilated on arrival "."</td>";
				}else{
					$str .=	"<td class=\"prph sumod\" ></td>";
				}
				if($arr["other_values"]["elem_pharmadilated_eye"]=="OU"||$arr["other_values"]["elem_pharmadilated_eye"]=="OS"){
					$str .=	"<td class=\"prph sumos ".$tmp."\"> Pharmacologically dilated on arrival "."</td>";
				}else{
					$str .=	"<td class=\"prph sumod\" ></td>";
				}						
				$str .= "".$strDesc;
				$str .="</tr>";

				$strDesc="";//Empty Desc after use
				$exm_header = ""; //Empty after use
				
				//
				/*
				$str_xml .="<pharma_dilated>";
					if($arr["other_values"]["elem_pharmadilated_eye"]=="OU"||$arr["other_values"]["elem_pharmadilated_eye"]=="OD"){
						$str_xml .= "<od class=\"".$tmp."\">  Pharmacologically dilated on arrival </od>";
					}else{
						$str_xml .= "<od></od>";
					}
					
					if($arr["other_values"]["elem_pharmadilated_eye"]=="OU"||$arr["other_values"]["elem_pharmadilated_eye"]=="OS"){
						$str_xml .= "<os class=\"".$tmp."\">  Pharmacologically dilated on arrival </os>";
					}else{
						$str_xml .= "<os></os>";
					}
					
				$str_xml .= "</pharma_dilated>";
				*/
				$ar_temp["pharma_dilated"]=array();			
				if($arr["other_values"]["elem_pharmadilated_eye"]=="OU"||$arr["other_values"]["elem_pharmadilated_eye"]=="OD"){
					$ar_temp["pharma_dilated"]["od"]["value"] = " Pharmacologically dilated on arrival ";
					$ar_temp["pharma_dilated"]["od"]["class"] = $tmp;
				}else{
					$ar_temp["pharma_dilated"]["od"]["value"] = "";
					$ar_temp["pharma_dilated"]["od"]["class"] = "";
				}
				
				if($arr["other_values"]["elem_pharmadilated_eye"]=="OU"||$arr["other_values"]["elem_pharmadilated_eye"]=="OS"){
					$ar_temp["pharma_dilated"]["os"]["value"] = " Pharmacologically dilated on arrival ";
					$ar_temp["pharma_dilated"]["os"]["class"] = $tmp;
				}else{
					$ar_temp["pharma_dilated"]["os"]["value"] = "";
					$ar_temp["pharma_dilated"]["os"]["class"] = "";
				}
				$str_xml["exam"] = array_merge($str_xml["exam"], $ar_temp);
			}
			// Pharmacologically dilated on arrival --

		
		
			//One Eye --
			if(count($oneeye)>0){
				if($oneeye[1]=="OU" || $oneeye[1]=="OD"){
					$on_nm_od = $oneeye[0];							
					if($oneeye[2]==1){
						$cOd = "bgWhite";
					}else{
						$cOd = (!empty($arr["exm_flg_se"][0])) ? "bgWhite" : "";
					}
				}

				if($oneeye[1]=="OU" || $oneeye[1]=="OS"){
					$on_nm_os = $oneeye[0];
					if($oneeye[2]==1){
						$cOs = "bgWhite";
					}else{
						$cOs = (!empty($arr["exm_flg_se"][1])) ? "bgWhite" : "";
					}
				}

				$str .= "<tr>";
				$str .= "<td class=\"edone ehdr\">".$exm_header."</td>";
				$str .=	"<td class=\"prph sumod ".$cOd."\" >".$on_nm_od.
						"</td>";
				$str .=	"<td class=\"prph sumos ".$cOs."\">".$on_nm_os.
						"</td>";
				$str .= "".$strDesc;
				$str .="</tr>";

				$strDesc="";//Empty Desc after use
				$exm_header = ""; //Empty after use
				
				//
				/*
				$str_xml .= "<one_eye>";
					$str_xml .= "<od class=\"".$cOd."\">  ".$on_nm_od." </od>";
					$str_xml .= "<os class=\"".$cOd."\">  ".$on_nm_od." </os>";
				$str_xml .= "</one_eye>";
				*/
				
				$ar_temp["one_eye"]=array();			
				$ar_temp["one_eye"]["od"]["value"] = " ".$on_nm_od." ";
				$ar_temp["one_eye"]["od"]["class"] = $cOd;
				$ar_temp["one_eye"]["os"]["value"] = " ".$on_nm_os." ";
				$ar_temp["one_eye"]["os"]["class"] = $cOs;
				$str_xml["exam"] = array_merge($str_xml["exam"], $ar_temp);
				
			}
			//--

			
			
			//Header Info ----------------------
				/*
				//Add header bar for LA, Gonio, RV SLE
				if(!empty($exm_header) && ($enm_var == "LA" || $enm_var == "Gonio" || $enm_var == "RV" || $enm_var == "SLE")){
					
					//Color
					if(empty($bgColor)){
						$tmp="bgWhite";
					}else{
						$tmp="";
					}							
					
					$str .= "<tr>";
					$str .= "<td class=\"edone ehdr\">".$exm_header."</td>";
					$str .= "<td class=\"sumod  ".$tmp."\"></td>";
					$str .= "<td class=\"sumos ".$tmp."\"></td>";
					$str .= "".$strDesc;
					$str .= "</tr>";
					
					$strDesc="";//Empty Desc after use
					$exm_header = ""; //Empty after use
					
				}
				*/
			//Header Info ----------------------

			//add header
			//if($enm_var == "Pupil" || $enm_var == "EOM" || $enm_var == "External" || $enm_var == "Ref_Surg" ){
				$str .=	"<tr><td class=\"edone ehdr\" colspan=\"2\">".$exm_header."</td></tr>";
			//}
			
			//$str_xml .= "<sub_exams>";
			$str_xml["exam"]["sub_exams"]=array();
			
			for($i=0;$i<$len;$i++){
			$sumod = trim($arrSE[$i]["sumod"]);
			$sumos = trim($arrSE[$i]["sumos"]);
			$cnm=$cOd=$cOs=$se_nm="";	
			
			//$str_xml_se ="";
			$str_xml_se =array();
			
			//if($sumod!="" || $sumos!=""){ // Stopped check so that all exam appear in list
				$se_nm = $arrSE[$i]["nm"];
				if($sumod!="" && !empty($arrSE[$i]["cOd"])){
					$cOd = "bgWhite";
				}
				if($sumos!="" && !empty($arrSE[$i]["cOs"])){
					$cOs = "bgWhite";
				}
				if(!empty($arrSE[$i]["cnm"])){
					$cnm = "color_red";
				}

				$se_nm_var=$se_nm;
				if($se_nm=="Lid Position"){
					$se_nm_var="LidPos";
				}else if($se_nm=="Lacrimal System"){
					$se_nm_var="Lac";
				}else if($se_nm=="Conjunctiva"){
					$se_nm_var="Conj";
				}else if($se_nm=="Cornea"){
					$se_nm_var="Corn";
				}else if($se_nm=="Ant. Chamber"){
					$se_nm_var="Ant";
				}else if($se_nm=="Iris & Pupil"){
					$se_nm_var="Iris";
				}else if($se_nm=="Blood Vessels"){
					$se_nm_var="BV";
				}else if($se_nm=="Optic Nerve"){
					$se_nm_var="Optic";
				}else if($se_nm=="Ophth. Drops"){
					$se_nm_var="OOD";
				}else if($se_nm=="Retinal Exam"){
					$se_nm_var="Retinal";
				}
				
				//Stop Drawing to be last row --
				if($se_nm_var == "Drawing"){
					continue;
				}
				//Stop Drawing to be last row --						
				
				$elem_summOd = "elem_sumOd".$se_nm_var;
				$elem_summOs = "elem_sumOs".$se_nm_var;
				$js_od = $arrSE[$i]["arcJsOd"];
				$js_os = $arrSE[$i]["arcJsOs"];
				$css_od = $arrSE[$i]["arcCssOd"];
				$css_os =  $arrSE[$i]["arcCssOs"];						
				$mn_od = $arrSE[$i]["mnOd"];
				$mn_os = $arrSE[$i]["mnOs"];
				
				//echo "".$js_od." ".$mn_od."";
				
			//}		
			
			
			
			if($enm_var == "Pupil" || $enm_var == "EOM" || $enm_var == "External" || $enm_var == "Ref_Surg" ){
				//$str .=	"<td class=\"edone ehdr\">".$exm_header."</td>";
			}else{
				//First Row, Add Drawing label also
				if($i==0){
					$se_nm_drw = $arrSE[$len-1]["nm"];
				}else{
					$se_nm_drw = "";
				}
				
				if($se_nm_drw=="Drawing"){
					/*
					$cnm_drw = (!empty($arrSE[$len-1]["cnm"])) ? "color_red" : "color_black";
					//GET link settings						
					$arlinkDrw= getSubExamLabel($enm_var, "Drawing", $seList);							
					$strDrw ="<div ".$arlinkDrw["id"]." class=\"drawtab ".$cnm_drw." \" >".
							//"<span class=\"".$arlinkDrw["cls"]."\"></span>".
							"<label ".$arlinkDrw["onclick"]." >".$se_nm_drw."</label>".
							"</div>";
					*/
					
					//if CD
					if($se_nm == "CD"){
						/*
						$strCDRV ="<div id=\"dvCDRV\" class=\"formCDRV \" >".									
							"<label class=\"od\" >OD</label>".
							"<input type=\"text\" name=\"elem_rvcd_od\" value=\"".$arr["elem_rvcd"]["od"]."\" onblur=\"saveCDRV()\" >".
							"<label class=\"os\" >OS</label>".
							"<input type=\"text\" name=\"elem_rvcd_os\" value=\"".$arr["elem_rvcd"]["os"]."\" onblur=\"saveCDRV()\" >".
							//"<input type=\"button\" name=\"elem_rvcd_btn\" value=\"Save\" class=\"dff_button_sm\" onclick=\"saveCDRV()\">".
							"</div>";						
						$cssCDRV	=" tdCDRV ";
						*/
						$strCDRV="";
						$cssCDRV	="";
					}else{
						$strCDRV="";
						$cssCDRV	="";
					}							
					
					//GET link settings						
					$arlink= $this->getSubExamLabel($enm_var, $se_nm_var, $seList);
					if(empty($cnm) && $arlink["cls"]=="flgPos"){ //show red color label if summary exists
						$cnm = "color_red";
					}							
					
					//display name
					$tmp_se_nm = $se_nm;
					if(isset($arrSE[$i]["nm_2show"]) && !empty($arrSE[$i]["nm_2show"])){ $tmp_se_nm = $arrSE[$i]["nm_2show"];  }
					
					$str .= "<tr>";
					$str .="<td ".$arlink["id"]." class=\"edone esubexam ".$cnm." ".$cssCDRV." \"  colspan=\"2\" >".
							//"<span class=\"".$arlink["cls"]."\"></span>".
							"<label ".$arlink["onclick"]." >".$tmp_se_nm."</label>".
						//$strDrw.
						$strCDRV.
						"</td>";						
					$str .="</tr>";	
					
					//
					/*
					$str_xml_se.="<header_info>";						
					$str_xml_se.="<title class=\"".$cnm."\">".htmlentities($tmp_se_nm)."</title>";	
					$str_xml_se.="</header_info>";
					*/
					
					$str_xml_se["header_info"]=array();						
					$str_xml_se["header_info"]["title"]=$tmp_se_nm;
					$str_xml_se["header_info"]["class"]=$cnm;				
					
				}else{
					//GET link settings						
					$arlink= $this->getSubExamLabel($enm_var, $se_nm_var, $seList);
					if(empty($cnm) && $arlink["cls"]=="flgPos"){ ////show red color label if summary exists
						$cnm = "color_red";
					}
					
					//$str_xml_se.="<header_info>";
					$str_xml_se["header_info"]=array();
					
					//display name
					$tmp_se_nm = $se_nm;
					if(isset($arrSE[$i]["nm_2show"]) && !empty($arrSE[$i]["nm_2show"])){ $tmp_se_nm = $arrSE[$i]["nm_2show"];  }
					$str .= "<tr>";
					$str .="<td ".$arlink["id"]." class=\"edone esubexam ".$cnm." \"  colspan=\"2\" >".
							//"<span class=\"".$arlink["cls"]."\"></span>".
							"<label ".$arlink["onclick"]." >".$tmp_se_nm."</label>";
					//		
					//$str_xml_se.="<title class=\"".$cnm."\">".htmlentities($tmp_se_nm)."</title>";			
					$str_xml_se["header_info"]["title"]["value"]=htmlentities($tmp_se_nm);
					$str_xml_se["header_info"]["title"]["class"]=$cnm;
							
					if($se_nm_var=="Anesthetic") {$str .="".$anes;}
					if($se_nm_var=="Dilation") {
						if(!empty($dilate)){$str .="".$dilate; }else if(!empty($dilatDropAllergy)){$str .="".$dilatDropAllergy; }
					}
					if($se_nm_var=="OOD") { $str .= $ood;  }
					
					//
					/*	
					if($se_nm_var=="Anesthetic") { if(!empty($anes)){$str_xml_se.="<other_info>".$anes."</other_info>";}}
					if($se_nm_var=="Dilation") { if(!empty($dilate)){ $str_xml_se.="<other_info>".$dilate."</other_info>"; }
											else if(!empty($dilatDropAllergy)){ $str_xml_se.="<other_info>".$dilatDropAllergy."</other_info>";}}
					if($se_nm_var=="OOD") { if(!empty($ood)){$str_xml_se.="<other_info>". $ood."</other_info>";} }
					*/
					if($se_nm_var=="Anesthetic") { if(!empty($anes)){$str_xml_se["other_info"]=$anes;}}
					if($se_nm_var=="Dilation") { if(!empty($dilate)){ $str_xml_se["other_info"]=$dilate; }
											else if(!empty($dilatDropAllergy)){ $str_xml_se["other_info"]=$dilatDropAllergy;}}
					if($se_nm_var=="OOD") { if(!empty($ood)){$str_xml_se["other_info"]=$ood;} }
					
					/*
					//if last row and RV							
					if($enm_var == "RV" && $i==$len-2){
						$elem_periNotExamined = $arr["elem_periNotExamined"];
						$elem_peri_ne_eye = $arr["elem_peri_ne_eye"];
						
						$elem_periNotExamined = ($elem_periNotExamined==1) ? "checked" : "";
						$elem_peri_ne_OU = ($elem_peri_ne_eye=="OU") ? "selected" : "";
						$elem_peri_ne_OD = ($elem_peri_ne_eye=="OD") ? "selected" : "";
						$elem_peri_ne_OS = ($elem_peri_ne_eye=="OS") ? "selected" : "";
						
						$str .="<div class=\"peritab\" >".
							"
							<input type=\"checkbox\"  name=\"elem_periNotExamined\" value=\"1\" ".$elem_periNotExamined."  onclick=\"checkPneEye();savePNE();\" >
								Peri not ex.
							<select name=\"elem_peri_ne_eye\" onchange=\"savePNE();\">
								<option value=\"\"></option>
								<option value=\"OU\" ".$elem_peri_ne_OU.">OU</option>
								<option value=\"OD\" ".$elem_peri_ne_OD.">OD</option>
								<option value=\"OS\" ".$elem_peri_ne_OS.">OS</option>
							</select>
							".
							"</div>";			
					}	
					*/	
					
					$str .="</td>";	
					$str .="</tr>";
					
					//$str_xml_se.="</header_info>";
					
				}						
			}				
			
			
			
			if($se_nm_var == "Drawing"){ //This will not work now as drawing is stopped above.
				
				//Color
				//$str .=	"<td class=\"sumod ".$bgDraw."\" ></td>";
				//$str .=	"<td class=\"sumos ".$bgDraw."\" ></td>";
			
			}else{
				$str .= "<tr>";
				$str .=	"<td id=\"".$elem_summOd."\" class=\"sumod ".$cOd." ".$css_od."\" ".$strColspan." ".$js_od." ".$mn_od." >".$sumod.
						"</td>";

				if($enm != "EOM"){
				$str .=	"<td id=\"".$elem_summOs."\" class=\"sumos ".$cOs." ".$css_os."\" ".$js_os." ".$mn_os." >".$sumos.
						"</td>";
				}
				$str .="</tr>";				
				
				/*
				$str_xml_se.="<summary>";
					$tmpod=trim("".$cOd." ".$css_od."");
					$tmpos=trim("".$cOs." ".$css_os."");
					$str_xml_se.="<od class=\"".$tmpod."\">".$sumod."</od>";
					if($enm != "EOM"){$str_xml_se.="<os class=\"".$tmpos."\">".$sumos."</os>";}
				$str_xml_se.="</summary>";
				*/
				
				$str_xml_se["summary"]=array();			
				$tmpod=trim("".$cOd." ".$css_od."");
				$tmpos=trim("".$cOs." ".$css_os."");
				
				$str_xml_se["summary"]["od"]=array("value"=>$sumod, "class"=>$tmpod);
				if($enm != "EOM"){
					$str_xml_se["summary"]["os"]=array("value"=>$sumos, "class"=>$tmpos);
				}		
				
			}	

			//
			//if(!empty($str_xml_se)){$str_xml_se="<sub_exam>".$str_xml_se."</sub_exam>";}
			//$str_xml .= $str_xml_se;
			
			if(count($str_xml_se)>0){ $str_xml["exam"]["sub_exams"][] = $str_xml_se; }
			///$str_xml["sub_exam"][] = $str_xml_se;
			//$str_xml["sub_exams"] = array_merge($str_xml["sub_exams"], $str_xml_se);
			
			
			}// end loop					
		

		$str .= "</table>";
		$str .= "</div>";				
		
		//
		//$str_xml .= "</sub_exams>";
		//$str_xml .="</exam>";
		//$str_xml["exams"] = array_merge($str_xml["exams"], $str_xml["sub_exams"]);
		
		//Purgeed Records ------------
		/*
		if(!empty($htmPurge)){
			$str .= $htmPurge;
		}
		*/
		//Purgeed Records ------------	

		//
		if($flgRet=="html"){ return $str; }
		else{	return count($str_xml>0) ? serialize($str_xml):""; }		

	}
	
	function onwv_isDrawingChanged($arr){
	
		if(in_array(1,$arr)){
			return 1;
		}else{
			return 0;
		}	

	}
	
	function se_checkFlgExm($arr,$ignr=0){
		$od = $os = 0;
		
		if(isset($arr) && count($arr)>0){
			foreach($arr as $key => $val){
				if($key!=$ignr){
					if($val["od"]==1){$od=1;}
					if($val["os"]==1){$os=1;}			
				}
			}
		}
		return array($od,$os);
	}
	
	function getSubExamLabel($enm_main, $enm_sub, $arr){
		
		if($enm_sub=="Lac"){
			$enm_sub="LacSys";
		}else if($enm_sub=="Periphery"){	
			$enm_sub="Peri";
		}else if($enm_sub=="Vitreous"){
			$enm_sub="Vit";
		}else if($enm_sub=="Macula"){
			$enm_sub="Mac";
		}else if($enm_sub=="Drawing"&&$enm_main=="LA"){
			$enm_sub="DrawLa";
		}else if($enm_sub=="Drawing"&&$enm_main=="Gonio"){
			$enm_sub="DrawGonio";
		}else if($enm_sub=="Drawing"&&$enm_main=="SLE"){
			$enm_sub="DrawSle";
		}else if($enm_sub=="Drawing"&&$enm_main=="RV"){
			$enm_sub="DrawRv";
		}else if($enm_sub=="Anesthetic"||$enm_sub=="Dilation"||$enm_sub=="OOD"){
			$enm_sub="IOP";
		}else if($enm_sub=="Retinal"){
			$enm_sub="Ret";
		}
		
		$ret = array();
		$val = $arr[$enm_sub];
		if(count($val)>0){
			$enm_s = $enm_sub;
			
			$enm=$val["enm"];			
			if(!empty($val["pos"])){
				$cls = "flgPos";
			}else if(!empty($val["wOd"])&&!empty($val["wOs"])){
				$cls = "flgWnl";
			}else{
				$cls ="";
			}
			
			$ret["id"] = " id=\"flagWnl".$enm_s."\" ";
			$ret["cls"] = $cls;
			$ret["onclick"] = " onclick=\"openPW('".$enm_main."','".$enm_s."')\" ";
		}else{
			
			if($enm_sub=="CD"){ //do not show flag to CD
				$enm_s = $enm_sub;
				$ret["id"] = " id=\"flagWnl".$enm_s."\" ";
				$ret["cls"] = "";
				$ret["onclick"] = " onclick=\"openPW('".$enm_main."','".$enm_s."')\" ";
			}
			
		}
		
		return $ret;
	}
	
	function isAppletDrawn($str){
		return ((trim($str) != "") && (trim($str) != "0-0-0:;") && (trim($str) != "255-0-0:;")) ? true : false;
	}


}


?>