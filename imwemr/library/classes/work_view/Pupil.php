<?php
/*
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
*/
?>
<?php
/*
File: Pupil.php
Coded in PHP7
Purpose: This class provides pupil exam related functions.
Access Type : Include file
*/
?>
<?php
//Pupil.php
class Pupil extends ChartNote {
	private $examName,$tbl,$xmlFileOd,$xmlFileOs;
	public function __construct($pid,$fid=""){
		parent::__construct($pid,$fid);
		$this->tbl="chart_pupil";
		//$this->xmlFileOd=$GLOBALS['incdir']."/chart_notes/xml/pupil_od.xml";
		//$this->xmlFileOs=$GLOBALS['incdir']."/chart_notes/xml/pupil_os.xml";
		$oExamXml = new ExamXml();
		$tmp = $oExamXml->getExamXmlFiles("Pupil");
		$this->xmlFileOd = $tmp["od"];
		$this->xmlFileOs = $tmp["os"];
		
		$this->examName="Pupil";
	}

	function isRecordExists($a="",$b="",$c="",$d=""){
		return parent::isRecordExists($this->tbl,"formId", "patientId");
	}

	function getRecord($sel=" * ",$a="",$b="",$c="",$d=""){
		return parent::getRecord($this->tbl,$sel,"formId", "patientId");
	}

	function getLastRecord($sel=" * ",$LF="0",$dt="", $a="", $b="", $c="" ){
		return parent::getLastRecord($this->tbl,"formId",$LF,$sel,$dt);
	}

	function insertNew(){	
		if(!empty($this->pid) && !empty($this->fid)){
		$wnl_value = $this->getExamWnlStr($this->examName);
		$sql = "INSERT INTO ".$this->tbl." (pupil_id, formId, patientId, examDate, uid, wnl_value)
				VALUES (NULL, '".$this->fid."','".$this->pid."','".date("Y-m-d H:i:s")."','".$this->uid."', '".$wnl_value."') ";
		$return= sqlInsert($sql);
		}else{ $return=0; }
		return $return;
	}

	function carryForward(){
		$res = $this->getLastRecord(" c2.pupil_id ","1");		
		if($res!=false){
			$pupilId_LF = $res["pupil_id"];
		}		
		//Insert
		$insertId = $this->insertNew();
		//CopyLF
		$ignoreFlds = "formId,examDate,uid,statusElem,examinedNoChange,nc_od,nc_os,modi_note_od,modi_note_os,wnl_value"; //ut_elem,
		if(!empty($pupilId_LF)) $this->carryForwardExe($this->tbl,$insertId,$pupilId_LF,$ignoreFlds);
	}

	function smartChart($arr){
	
		$statusElemCur=$this->getCurStatusFromPost($arr, "elem_chng_divPupil_Od", "elem_chng_divPupil_Os");		
		/*
		//get all post data
		$al_post=array();
		if(count($arr)>0){
			foreach($arr as $k=>$v){
				if(isset($v["level"])&&!empty($v["level"])){
					$al_post = unserialize($v["level"]);
					break;
				}
			}
		}
		if(isset($al_post["elem_chng_divPupil_Od"])&&isset($al_post["elem_chng_divPupil_Os"])){ 
			$statusElemCur="elem_chng_divPupil_Od=".$al_post["elem_chng_divPupil_Od"].",elem_chng_divPupil_Os=".$al_post["elem_chng_divPupil_Os"];			
		}
		*/

		if(!$this->isRecordExists()){
			//In
			$this->carryForward();
		}

		$res=$this->getRecord();
		if($res!=false){
			$xmlOd = $res["pupilOd"];
			$xmlOs = $res["pupilOs"];
			$statusElem = $res["statusElem"];
			//$descPupil = $res["descPupil"];
			$wnl = $res["wnl"];
			$wnlOd=$res["wnlPupilOd"];
			$wnlOs=$res["wnlPupilOs"];
			$ut_elem=$res["ut_elem"];
			
		}else{
			$descPupil = "";
		}

		//Edit Xml --
		$arrIn["xOd"]=$xmlOd;
		$arrIn["xOs"]=$xmlOs;
		$arrIn["xFileOd"]=$this->xmlFileOd;
		$arrIn["xFileOs"]=$this->xmlFileOs;
		$arrIn["arrSc"] = $arr;
		$arrIn["ut_elem"] = $ut_elem;
		$arrOut= $this->editXml($arrIn);

		$xmlOd=$arrOut["xOd"];
		$xmlOs=$arrOut["xOs"];
		$siteExm=$arrOut["siteExm"];
		$descPupil.=$arrOut["desc"];
		$sumOd = $arrOut["sumOd"];
		$sumOs = $arrOut["sumOs"];
		$arrElemEd=$arrOut["elemEd"];
		$ut_elem = $arrOut["ut_elem"];		
		
		//Edit Xml --

		//Set WNL --
		list($wnl,$wnlOd,$wnlOs,$pos) = $this->setExmWnlPos($sumOd,$sumOs,$wnl,$wnlOd,$wnlOs,$pos);
		//Set WNL --

		//Set Status Fields--
		if(!empty($statusElemCur)){ 
			$statusElem=$statusElemCur;			
		}else{		
			$statusElem = $this->setStatusElem("Pupil",$statusElem,$sumOd,$sumOs,$siteExm);
		}
		//Set Status Fields--
		
		//UTteleme --
		$elem_utElems_cur="";
		if(count($arrElemEd)>0){
			$elem_utElems_cur=implode(",", $arrElemEd);
			$elem_utElems_cur=$elem_utElems_cur.",";
		}
		$ut_elem = $this->getUTElemString($ut_elem,$elem_utElems_cur);
		//UTteleme --		

		//Update Records--
		$sql="UPDATE ".$this->tbl." ".
			"SET ".
			"examDate = '".date("Y-m-d H:i:s")."', ".
			"wnl = '".$wnl."', ".
			"pupilOd = '".sqlEscStr($xmlOd)."', ".
			"pupilOs = '".sqlEscStr($xmlOs)."', ".
			"isPositive = '".$pos."', ".
			"sumOdPupil = '".sqlEscStr($sumOd)."', ".
			"sumOsPupil = '".sqlEscStr($sumOs)."', ".
			"wnlPupilOd='".$wnlOd."', ".
			"wnlPupilOs='".$wnlOs."', ".
			"descPupil = '".sqlEscStr($descPupil)."', ".
			"uid = '".$this->uid."', ".
			"statusElem = '".sqlEscStr($statusElem)."', ".
			"ut_elem = '".sqlEscStr($ut_elem)."' ".
			"WHERE formId = '".$this->fid."' AND patientId = '".$this->pid."' AND purged = '0' ";
		$res=sqlQuery($sql);
	}

	function getDomSum($dom,$flgRet=""){
		$summ="";
		$arrExmDone=array();

		//Process --
		$root = $dom->documentElement;
		$main_exam = ($root->hasAttributes()) ? $root->getAttribute("main_exam") : $root->tagName;
		$root2 = $root->firstChild;
		foreach($root2->childNodes as $field){ //Loop1
			$attr_examName=$attr_examName_summary="";
			if($field->hasAttributes()){
				$attr_examName = $field->getAttribute("examname");
				$attr_examName_summary = $field->getAttribute("examname_summary");
				if(empty($attr_examName_summary)) $attr_examName_summary = $attr_examName;
			}

			if(!empty($attr_examName)){
				$summ_tmp = "";
				$summ_tmp_2 = "";

				foreach($field->childNodes as $field_2){ //Loop2
					$attr_examName_2=$attr_examName_summary_2="";
					if($field_2->hasAttributes()){
						$attr_examName_2 = $field_2->getAttribute("examname");
						$attr_examName_summary_2 = $field->getAttribute("examname_summary");
						if(empty($attr_examName_summary_2)) $attr_examName_summary_2 = $attr_examName_2;
					}

					if(empty($attr_examName_2)){
						if(!empty($field_2->nodeValue)){
							$summ_tmp .= $field_2->nodeValue." ";
						}
					}else{
						//Loop 3-
						$summ_tmp_3 = "";
						foreach($field_2->childNodes as $field_3){ //Loop3
							if(!empty($field_3->nodeValue)){
								$summ_tmp_3 .= $field_3->nodeValue." ";
							}
						}
						if(!empty($summ_tmp_3)){
							$summ_tmp_3 = $attr_examName_summary_2.": ".trim($summ_tmp_3).". ";
							$summ_tmp_2 .= $summ_tmp_3;
							if(strpos($summ_tmp_3,"-ve")===false){
								$tmpEm=$attr_examName."/".$attr_examName_2;
								$arrExmDone[$tmpEm]=$attr_examName;
							}
						}
					}
				}

				if(!empty($summ_tmp)){
					$summ_tmp = $attr_examName_summary.": ".trim($summ_tmp).". ";
					$summ.=$summ_tmp;
					if(strpos($summ_tmp,"-ve")===false){
						$arrExmDone[$attr_examName]=$attr_examName;
					}
				}
				if(!empty($summ_tmp_2)){
					$summ.= $summ_tmp_2;
				}

			}else{
				if(!empty($field->nodeValue)){
					if($field->tagName=="advanceoptions" || $field->tagName == "pupil_desc"){ $summ.="\nComments: "; }
					$summ.=$field->nodeValue.". ";
				}
			}
		}
		//Process --
		
		if($flgRet=="1"||1==1){ $summ=nl2br($summ);  }
		
		$summ = wv_strReplace($summ,"LASTDOT");
		return ($flgRet=="1") ? array("Summary"=>$summ,"ExmDone"=>$arrExmDone) : $summ ;
	}

	function set2PrvVals(){
		if($this->isRecordExists()){
			$res1 = $this->getRecord(" pupil_id ");
			if($res1!=false){
				$pupilId = $res1["pupil_id"];
			}
			
			$res = $this->getLastRecord(" c2.pupil_id ","1");		
			if($res!=false){
				$pupilId_LF = $res["pupil_id"];
			}

			//CopyLF
			$ignoreFlds = "formId,examDate,uid,statusElem,examinedNoChange,nc_od,nc_os,modi_note_od,modi_note_os,wnl_value"; //ut_elem,
			if(!empty($pupilId_LF)&&!empty($pupilId)){
				$this->carryForwardExe($this->tbl,$pupilId,$pupilId_LF,$ignoreFlds);
				$this->setStatus("",$this->tbl,"formId", "patientId");
			}else if(!empty($pupilId)){ //when no previous exam
				$this->resetVals(); //empty exam values 
			}
		}	
	}
	
	function resetVals(){
	
		$is_cryfd=0;
		if(!$this->isRecordExists()){
			//In
			$this->carryForward();
			$is_cryfd=1;
		}
	
	
		//if($this->isRecordExists()){
		
			$statusElem = "";
			$res1 = $this->getRecord(" pupil_id, statusElem ");
			if($res1!=false){
				$pupilId = $res1["pupil_id"];
				if($is_cryfd==0){$statusElem = $res1["statusElem"];}
			}

			//CopyLF
			$ignoreFlds = "formId,examDate,uid,patientId,";
			if(!empty($pupilId)){
				if(empty($_POST["site"]) || $_POST["site"]=="OU"){ $statusElem = "";  }
				if($_POST["site"]=="OS"){ 
					$ignoreFlds .= "apdMinusOd,apdMinusOdSummary,apdPlusOd,apdPlusOdSummary,reactionOd,reactionOdSummary,
								shapeOd,shapeOdSummary,pupilOd,sumOdPupil,wnlPupilOd,nc_od,modi_note_od,"; 
					if(!empty($statusElem)){ $statusElem = str_replace("_Os=1","_Os=0",$statusElem);  }			
				}else if($_POST["site"]=="OD"){ 
					$ignoreFlds .= "apdMinusOs,apdMinusOsSummary,apdPlusOs,apdPlusOsSummary,reactionOs,reactionOsSummary,
								shapeOs,shapeOsSummary,pupilOs,sumOsPupil,wnlPupilOs,nc_os,modi_note_os,"; 
					if(!empty($statusElem)){ $statusElem = str_replace("_Od=1","_Od=0",$statusElem);  }				
				}
				
				if($_POST["site"]=="OD" || $_POST["site"]=="OS"){if($is_cryfd==0){$ignoreFlds .= "wnl_value,ut_elem,";}}
				$ignoreFlds = trim($ignoreFlds,",");
				
				$this->resetValsExe($this->tbl,$pupilId,$ignoreFlds);
				$this->setStatus($statusElem,$this->tbl,"formId", "patientId");
				
			}
			
		//}else{
			//
			//$this->insertNew();
		//}	
	}
	
	function isNoChanged(){
		$res= $this->getRecord("examinedNoChange,statusElem");
		if($res!=false){
			if( !empty($res["statusElem"]) && strpos($res["statusElem"],"=1")!==false){
				return true;
			}
			if( !empty($res["examinedNoChange"]) ){
				return true;
			}		
		}
		return false;
	}
	
	function save_wnl(){
		$form_id = $this->fid;
		$patientId = $this->pid;
		$w = strtoupper($_POST["w"]);
		$exmEye = $_POST["elem_exmEye"];
		$wnl_value=$wnl_phrase="";
		$modi_note_od=$modi_note_os="";	
		$flgCarry=0;
		if(!$this->isRecordExists()){
			$this->carryForward();
			$flgCarry=1;
		}
		
		$cQry = "select wnl,perrla,sumOdPupil,sumOsPupil,
					isPositive,wnlPupilOd,wnlPupilOs,statusElem,uid,wnl_value  
					FROM chart_pupil WHERE formId='".$form_id."' AND patientId='".$patientId."'  AND purged = '0' ";
		$row = sqlQuery($cQry);		
		if($row == false){
			
		}else{
			$elem_wnlPupil=$row["wnl"];
			$wnlPupilOd = $elem_wnlPupilOd=$row["wnlPupilOd"];
			$wnlPupilOs = $elem_wnlPupilOs=$row["wnlPupilOs"];
			$pos=$row["isPositive"];
			$statusElem=$row["statusElem"];
			$sumOd=$row["sumOdPupil"];
			$sumOs=$row["sumOsPupil"];
			$uid  = $row["uid"];
			$wnl_value=$row["wnl_value"];
		}
		
		$oWv = new WorkView();
		//Check For Alingment of WNL Values --

		$arrAlign = array(
						array($pos,$elem_wnlPupilOd,$elem_wnlPupilOs,$sumOd,$sumOs)
					);
		$arrAlign = $oWv->alignWnlVals($arrAlign,$exmEye);
		if(!empty($arrAlign["od"])){
			$elem_wnlPupil=$elem_wnlPupilOd="0";
		}

		if(!empty($arrAlign["os"])){
			$elem_wnlPupil=$elem_wnlPupilOs="0";
		}

		//Check For Alingment of WNL Values --
		
		//Toggle WNL
		if((!empty($statusElem)&&strpos($statusElem,"0")===false)||
				(empty($sumOd) && empty($elem_wnlPupilOd))||
				(empty($sumOs) && empty($elem_wnlPupilOs))){				
		list($elem_wnlPupilOd,$elem_wnlPupilOs,$elem_wnlPupil) =
								$oWv->toggleWNL($pos,$sumOd,$sumOs,$elem_wnlPupilOd,$elem_wnlPupilOs,$elem_wnlPupil,$exmEye);
		}
		
		//if Not carry			
		if($flgCarry==0){			
			//Modifying Notes----------------
			$modi_note_od=$oWv->getModiNotes($sumOd,$wnlPupilOd,$sumOd,$elem_wnlPupilOd,$uid);
			$modi_note_os=$oWv->getModiNotes($sumOs,$wnlPupilOs,$sumOs,$elem_wnlPupilOs,$uid);
			//Modifying Notes----------------
		
		}			
		//End if Not carry
		
		//getWnlValue
		if(empty($wnl_value)){
			$wnl_value=$this->getExamWnlStr("Pupil");
			$wnl_phrase = ", wnl_value='".sqlEscStr($wnl_value)."' ";				
		}
		
		$statusElem = $this->setEyeStatus($w, $exmEye,$statusElem);
		$sql = "UPDATE chart_pupil SET wnl='".$elem_wnlPupil."', wnlPupilOd='".$elem_wnlPupilOd."', wnlPupilOs='".$elem_wnlPupilOs."',			
				examDate='".date("Y-m-d H:i:s")."', uid='".$_SESSION["authId"]."',
				statusElem='".$statusElem."',
				modi_note_od = CONCAT('".sqlEscStr($modi_note_od)."',modi_note_od),
				modi_note_os = CONCAT('".sqlEscStr($modi_note_os)."',modi_note_os) ".$wnl_phrase."
				WHERE formId='".$form_id."' AND patientId='".$patientId."'  AND purged = '0' ";
				
		$res = sqlQuery($sql);		
	}
	
	function save_no_change(){
		$form_id = $this->fid;
		$patientId = $this->pid;
		$w = strtoupper($_POST["w"]);
		$exmEye = $_POST["elem_exmEye"];
		
		//$elem_noChangePupil = $_POST["elem_noChangePupil"];
		$tmpNC=$elem_noChangePupil=$elem_noChangePupil_OD=$elem_noChangePupil_OS=0;
		
		//
		$oPupil=$this; //new Pupil($patientId,$form_id);
		if(!$oPupil->isRecordExists()){
			$oPupil->carryForward();
			$tmpNC=1;
		}else if(!$oPupil->isNoChanged()){
			$tmpNC=1;
		}else{
			$oPupil->set2PrvVals();
			$tmpNC=0;
		}
			
		//Set NC
		if($tmpNC==1){
			if($exmEye=="OU"){
				$elem_noChangePupil=1;
				$elem_noChangePupil_OD=1;
				$elem_noChangePupil_OS=1;
			}else if($exmEye=="OD"){
				$elem_noChangePupil_OD=1;
			}else if($exmEye=="OS"){
				$elem_noChangePupil_OS=1;
			}
		}

		//Get status string --
		$statusElem="";
		if($elem_noChangePupil_OD==1||$elem_noChangePupil_OS==1){$statusElem=$this->se_elemStatus($w,"1","",$elem_noChangePupil_OD,$elem_noChangePupil_OS,0);}
		//Get status--
		
		//
		$sql = "UPDATE chart_pupil SET examinedNoChange='".$elem_noChangePupil."',
				nc_od='".$elem_noChangePupil_OD."', nc_os='".$elem_noChangePupil_OS."',
				examDate='".date("Y-m-d H:i:s")."', uid='".$_SESSION["authId"]."',
				statusElem='".$statusElem."'
				WHERE formId='".$form_id."' AND patientId='".$patientId."'  AND purged = '0' ";
		$res = sqlQuery($sql);		
		
	}
	
}
?>