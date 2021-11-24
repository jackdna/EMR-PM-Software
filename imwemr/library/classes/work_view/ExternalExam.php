<?php
/*
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
*/
?>
<?php
/*
File: ExternalExam.php
Coded in PHP7
Purpose: This class provides External Exam related chart note functions.
Access Type : Include file
*/
?>
<?php
//ExternalExam.php
class ExternalExam extends ChartNote{
	private $examName,$tbl,$xmlFileOd,$xmlFileOs;
	public function __construct($pid,$fid=""){
		parent::__construct($pid,$fid);
		$this->tbl="chart_external_exam";
		//$this->xmlFileOd=$GLOBALS['incdir']."/chart_notes/xml/external_od.xml";
		//$this->xmlFileOs=$GLOBALS['incdir']."/chart_notes/xml/external_os.xml";
		$oExamXml = new ExamXml();
		$tmp = $oExamXml->getExamXmlFiles("ExternalExam");
		$this->xmlFileOd = $tmp["od"];
		$this->xmlFileOs = $tmp["os"];
		
		$this->examName="External";
	}

	function isRecordExists($a="",$b="",$c="",$d=""){
		return parent::isRecordExists($this->tbl);
	}

	function getRecord($sel=" * ",$a="",$b="",$c="",$d=""){
		return parent::getRecord($this->tbl,$sel);
	}

	function getLastRecord($sel=" * ",$LF="0",$dt="", $a="", $b="", $c="" ){
		return parent::getLastRecord($this->tbl,"form_id",$LF,$sel,$dt);
	}

	function insertNew(){
		if(!empty($this->pid) && !empty($this->fid)){
		$wnl_value = $this->getExamWnlStr($this->examName, $this->pid, $this->fid);
		$sql = "INSERT INTO ".$this->tbl." (ee_id, form_id, patient_id, exam_date, uid, wnl_value)
				VALUES (NULL, '".$this->fid."','".$this->pid."','".date("Y-m-d H:i:s")."','".$this->uid."', '".$wnl_value."') ";
		$return=sqlInsert($sql);
		
		}else{ $return=0; }
		return $return;
	}

	function carryForward(){
		$res = $this->getLastRecord(" c2.ee_id ","1");
		if($res!=false){
			$Id_LF = $res["ee_id"];
		}
		//Insert
		$insertId = $this->insertNew();
		//CopyLF
		$ignoreFlds = "form_id,exam_date,uid,statusElem,examined_no_change,nc_od,nc_os,ncEe,ncDraw,".
					//"ut_elem,".
					"modi_note_od,modi_note_os,modi_note_Draw,wnl_value";
		if(!empty($Id_LF)) $this->carryForwardExe($this->tbl,$insertId,$Id_LF,$ignoreFlds,"EXTERNAL","ee_id");
	}

	function smartChart($arr){

		if(!$this->isRecordExists()){
			//In
			$this->carryForward();
		}

		$res=$this->getRecord();
		if($res!=false){
			$xmlOd = $res["external_exam"];
			$xmlOs = $res["external_exam_os"];
			$statusElem = $res["statusElem"];
			$wnl = $res["wnl"];
			$wnlOd=$res["wnlEeOd"];
			$wnlOs=$res["wnlEeOs"];
			$ut_elem=$res["ut_elem"];
		}else{
			$desc = "";
		}
		
		//
		$statusElemCur=$this->getCurStatusFromPost($arr, "elem_chng_divCon_Od", "elem_chng_divCon_Os", $statusElem);		

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
		$desc.=$arrOut["desc"];
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
			$statusElem = $this->setStatusElem("Con",$statusElem,$sumOd,$sumOs,$siteExm);
		}
		//Set Status Fields--
		
		//UTteleme --
		$elem_utElems_cur="";
		if(count($arrElemEd)>0){
			$elem_utElems_cur=implode(",", $arrElemEd);
			$elem_utElems_cur=$elem_utElems_cur.","; //attach a comma
		}
		$ut_elem = $this->getUTElemString($ut_elem,$elem_utElems_cur); //defined in saveCharts.php
		//UTteleme --

		//Update Records--
		$sql="UPDATE ".$this->tbl." ".
			"SET ".
			"exam_date = '".date("Y-m-d H:i:s")."', ".
			"wnl = '".$wnl."', ".
			"external_exam = '".sqlEscStr($xmlOd)."', ".
			"external_exam_os = '".sqlEscStr($xmlOs)."', ".
			"isPositive = '".$pos."', ".
			"external_exam_summary = '".sqlEscStr($sumOd)."', ".
			"sumOsEE = '".sqlEscStr($sumOs)."', ".
			"wnlEeOd='".$wnlOd."', ".
			"wnlEeOs='".$wnlOs."', ".
			"descExternal = '".sqlEscStr($desc)."', ".
			"uid = '".$this->uid."', ".
			"statusElem = '".sqlEscStr($statusElem)."', ".
			"ut_elem = '".sqlEscStr($ut_elem)."' ".
			"WHERE form_id = '".$this->fid."' AND patient_id = '".$this->pid."' AND purged = '0' ";
		$res=sqlQuery($sql);
	}

	function getDomSum($dom,$flgRet=""){
		$summ="";
		$arrExmDone=array();
		$arr_cm_sub_ex=array("Edema");

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
							if(in_array($attr_examName_summary_2, $arr_cm_sub_ex)){								
								$summ_tmp_3 = $attr_examName."/".$attr_examName_2.": ".trim($summ_tmp_3).". ";
							}else{
								$summ_tmp_3 = $attr_examName_summary_2.": ".trim($summ_tmp_3).". ";
							}							
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
					$summ.=$field->nodeValue;
				}
			}
		}
		//Process --
		
		if($flgRet=="1"||1==1){ $summ=nl2br($summ);  }

		return ($flgRet=="1") ? array("Summary"=>$summ,"ExmDone"=>$arrExmDone) : $summ ;
	}

	function set2PrvVals(){
		if($this->isRecordExists()){
			$res1 = $this->getRecord(" ee_id ");
			if($res1!=false){
				$Id = $res1["ee_id"];
			}
			
			$res = $this->getLastRecord(" c2.ee_id ","1");		
			if($res!=false){
				$Id_LF = $res["ee_id"];
			}

			//CopyLF
			$ignoreFlds = "form_id,exam_date,uid,statusElem,examined_no_change,nc_od,nc_os,ncEe,ncDraw,".
						//"ut_elem,".
						"modi_note_od,modi_note_os,modi_note_Draw,wnl_value";
			if(!empty($Id_LF)&&!empty($Id)){
				$this->carryForwardExe($this->tbl,$Id,$Id_LF,$ignoreFlds,"EXTERNAL","ee_id");
				$this->setStatus("",$this->tbl);
			}else if(!empty($Id)){ //when no previous exam
				$this->resetVals(); //empty exam values 
			}
		}	
	}

	//Reset
	function resetVals(){
		$is_cryfd=0;
		if(!$this->isRecordExists()){
			//In
			$this->carryForward();
			$is_cryfd=1;
		}
		
	
		//if($this->isRecordExists()){
		
			$statusElem = "";
			$res1 = $this->getRecord(" ee_id,statusElem ");
			if($res1!=false){
				$Id = $res1["ee_id"];
				if($is_cryfd==0){$statusElem = $res1["statusElem"];}
			}
		
			//CopyLF
			$ignoreFlds = "form_id,exam_date,uid,patient_id,idoc_drawing_id,"; //Only These fields will retain values, rest all will be empty.
			if(!empty($Id)){
				if(empty($_POST["site"]) || $_POST["site"]=="OU"){ $statusElem = "";  }
				if($_POST["site"]=="OS"){ 
					$ignoreFlds .= "external_exam,external_exam_summary,ee_desc,wnlEeOd,nc_od,wnlDrawOd,modi_note_od,"; 
					if(!empty($statusElem)){ $statusElem = str_replace("_Os=1","_Os=0",$statusElem);  }			
				}else if($_POST["site"]=="OD"){ 
					$ignoreFlds .= "external_exam_os,sumOsEE,wnlEeOs,nc_os,wnlDrawOs,ee_desc_os,modi_note_os,"; 
					if(!empty($statusElem)){ $statusElem = str_replace("_Od=1","_Od=0",$statusElem);  }				
				}

				if($_POST["site"]=="OD" || $_POST["site"]=="OS"){if($is_cryfd==0){$ignoreFlds .= "wnl_value,ut_elem,";}}
				$ignoreFlds = trim($ignoreFlds,",");

				$this->resetValsExe($this->tbl,$Id,$ignoreFlds,"EXTERNAL","ee_id");
				$this->setStatus($statusElem,$this->tbl);
			}
			
		//}else{			
			//
		//	$this->insertNew();
		//}	
	}
	function save_wnl(){
		$form_id = $this->fid;
		$patientId = $this->pid;
		$w = strtoupper($_POST["w"]);
		$exmEye = $_POST["elem_exmEye"];
		$wnl_value=$wnl_phrase="";
		$modi_note_od = $modi_note_os = "";
		$flgCarry=0;
		//
		if(!$this->isRecordExists()){
			$this->carryForward();
			$flgCarry=1;
		}

		$cQry = "select wnl, wnlEeOd, wnlEeOs,ee_id,statusElem, wnlEe,wnlDraw,
						posEe,posDraw,wnlDrawOd,wnlDrawOs,	
						isPositive,external_exam_summary,sumOsEE,uid, wnl_value
					FROM chart_external_exam 
					WHERE form_id='".$form_id."' AND patient_id='".$patientId."'  AND purged = '0' ";
		$row = sqlQuery($cQry);
		if($row == false){
			
		}else{
			$elem_wnlEe = $row["wnlEe"];
			$wnlEeOd = $elem_wnlEeOd = $row["wnlEeOd"]; //$wnlEeOd+$wnlEeOs used as prev values
			$wnlEeOs = $elem_wnlEeOs = $row["wnlEeOs"];
			$statusElem = $row["statusElem"];
			$pos = $row["isPositive"];
			$posEe = $row["posEe"];
			$sumOdEE = $row["external_exam_summary"];
			$sumOsEE = $row["sumOsEE"];
			$elem_wnlDraw = $row["wnlDraw"];
			$uid=$row["uid"];
			$wnl_value=$row["wnl_value"];	
		}
		
		$oWv = new WorkView();
		//Check For Alingment of WNL Values --
		$arrAlign = array(
						array($posEe,$elem_wnlEeOd,$elem_wnlEeOs,$sumOdEE,$sumOsEE)
					);
		$arrAlign = $oWv->alignWnlVals($arrAlign,$exmEye);

		if(!empty($arrAlign["od"])){
			$elem_wnlEe=$elem_wnlEeOd="0";
		}

		if(!empty($arrAlign["os"])){
			$elem_wnlEe=$elem_wnlEeOs="0";
		}

		//Check For Alingment of WNL Values --

		//Toggle WNL
		if((!empty($statusElem)&&strpos($statusElem,"0")===false)||
				(empty($sumOdEE) && empty($elem_wnlEeOd))||
				(empty($sumOsEE) && empty($elem_wnlEeOs))){
			list($elem_wnlEeOd,$elem_wnlEeOs,$elem_wnlEe) = $oWv->toggleWNL($posEe,$sumOdEE,$sumOsEE,$elem_wnlEeOd,$elem_wnlEeOs,$elem_wnlEe,$exmEye);
		}
		
		//WNL
		if(!empty($elem_wnlEe) && !empty($elem_wnlDraw)){
			$elem_wnl = "1";
		}else{
			$elem_wnl = "0";
		}
		
		//if Not carry			
		if($flgCarry==0){
			
			//Modifying Notes----------------
			$modi_note_od=$oWv->getModiNotes($sumOdEE,$wnlEeOd,$sumOdEE,$elem_wnlEeOd,$uid);
			$modi_note_os=$oWv->getModiNotes($sumOsEE,$wnlEeOs,$sumOsEE,$elem_wnlEeOs,$uid);
			//Modifying Notes----------------			
		
		}			
		//End if Not carry
		
		//Status
		$statusElem_EE_prev=$statusElem;
		$statusElem = $this->setEyeStatus($w, $exmEye,$statusElem,0);
		//Add Drawing Status if any
		if(strpos($statusElem_EE_prev, "Draw_Od=1")!==false){	
			$statusElem .= "elem_chng_divDraw_Od=1,";
		}
		if(strpos($statusElem_EE_prev, "Draw_Os=1")!==false){	
			$statusElem .= "elem_chng_divDraw_Os=1,";
		}
		
		//getWnlValue
		if(empty($wnl_value)){
			$wnl_value=$this->getExamWnlStr("External");
			$wnl_phrase = ", wnl_value='".sqlEscStr($wnl_value)."' ";				
		}
		
		$sql = "UPDATE chart_external_exam SET wnl='".$elem_wnl."', wnlEe='".$elem_wnlEe."', wnlEeOd='".$elem_wnlEeOd."', wnlEeOs='".$elem_wnlEeOs."',
				exam_date='".date("Y-m-d H:i:s")."', uid='".$_SESSION["authId"]."',
				statusElem='".$statusElem."',
				modi_note_od = CONCAT('".sqlEscStr($modi_note_od)."',modi_note_od),
				modi_note_os = CONCAT('".sqlEscStr($modi_note_os)."',modi_note_os) ".$wnl_phrase."
				WHERE form_id='".$form_id."' AND patient_id='".$patientId."'  AND purged = '0' ";
		$res = sqlQuery($sql);
	}
	function save_no_change(){
		$form_id = $this->fid;
		$patientId = $this->pid;
		$w = strtoupper($_POST["w"]);
		$exmEye = $_POST["elem_exmEye"];
		
		//$elem_noChangeEe = $_POST["elem_noChangeExternal"];	
		$tmpNC=$elem_noChange=$elem_noChangeEe=$elem_noChangeEe_OD =$elem_noChangeEe_OS=0;
		$ncDraw = 0;
		
		//
		$oEE=$this;
		if(!$oEE->isRecordExists()){			
			$oEE->carryForward();
			$tmpNC=1;				
		}else if(!$oEE->isNoChanged()){
			$tmpNC=1;			
		}else{			
			$oEE->set2PrvVals();
			$tmpNC=0;
		}
		
		//Set NC
		if($tmpNC==1){
			if($exmEye=="OU"){
				//$elem_noChange=1;
				$elem_noChangeEe=1;
				$elem_noChangeEe_OD=1;
				$elem_noChangeEe_OS=1;
				//$ncDraw=1;
			}else if($exmEye=="OD"){
				$elem_noChangeEe_OD=1;
				//$ncDraw=1;
			}else if($exmEye=="OS"){
				$elem_noChangeEe_OS=1;
				//$ncDraw=1;
			}
		}
		
		//Get status string --
		$statusElem="";
		if($elem_noChangeEe_OD==1||$elem_noChangeEe_OS==1){$statusElem=$this->se_elemStatus($w,"1","",$elem_noChangeEe_OD,$elem_noChangeEe_OS,0);}
		//Get status string --
		
		//
		$sql = "UPDATE chart_external_exam SET 
				examined_no_change='".$elem_noChange."', ncEe='".$elem_noChangeEe."',ncDraw='".$ncDraw."',
				nc_od='".$elem_noChangeEe_OD."',nc_os='".$elem_noChangeEe_OS."',
				exam_date='".date("Y-m-d H:i:s")."', uid='".$_SESSION["authId"]."',
				statusElem='".$statusElem."'
				WHERE form_id='".$form_id."' AND patient_id='".$patientId."'  AND purged = '0' ";
		$res = sqlQuery($sql);	
	}
	
}
?>