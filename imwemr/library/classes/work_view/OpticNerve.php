<?php
/*
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
*/
?>
<?php
/*
File: OpticNerve.php
Coded in PHP7
Purpose: This class provides Optic nerve Exam functions.
Access Type : Include file
*/
?>
<?php
//OpticNerve.php
class OpticNerve extends ChartNote{
	private $examName,$tbl,$xmlFileOd,$xmlFileOs;
	public function __construct($pid,$fid=""){
		parent::__construct($pid,$fid);
		$this->tbl="chart_optic";
		
		//$this->xmlFileOd=$GLOBALS['incdir']."/chart_notes/xml/opticNerveDisc_od.xml";
		//$this->xmlFileOs=$GLOBALS['incdir']."/chart_notes/xml/opticNerveDisc_os.xml";
		$oExamXml = new ExamXml();
		$tmp = $oExamXml->getExamXmlFiles("Fundus", "opticNerveDisc");
		$this->xmlFileOd = $tmp["od"];
		$this->xmlFileOs = $tmp["os"];		
		
		$this->examName="OpticNerve";
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
		$wnl_value_Optic = $this->getExamWnlStr("Optic Nerve");
		$sql = "INSERT INTO ".$this->tbl." (optic_id, form_id, patient_id, exam_date, uid, wnl_value_Optic)
				VALUES (NULL, '".$this->fid."','".$this->pid."','".date("Y-m-d H:i:s")."','".$this->uid."', '".sqlEscStr($wnl_value_Optic)."') ";
		$return=sqlInsert($sql);
		
		}else{ $return=0; }
		return $return;
	}

	function carryForward(){
		$res = $this->getLastRecord(" c2.optic_id ","1");
		if($res!=false){
			$Id_LF = $res["optic_id"];
		}
		//Insert
		$insertId = $this->insertNew();
		//CopyLF
		$ignoreFlds = "form_id,exam_date,uid,statusElem,examined_no_change,ut_elem,wnl_value_Optic";
		if(!empty($Id_LF)) $this->carryForwardExe($this->tbl,$insertId,$Id_LF,$ignoreFlds);
	}

	function smartChart($arr){

		if(!$this->isRecordExists()){
			//In
			$this->carryForward();
		}

		$res=$this->getRecord();
		if($res!=false){
			$xmlOd = $res["optic_nerve_od"];
			$xmlOs = $res["optic_nerve_os"];
			$statusElem = $res["statusElem"];
			$wnl = $res["wnl"];
			$wnlOd=$res["wnlOpticOd"];
			$wnlOs=$res["wnlOpticOs"];
		}else{
			$desc = "";
		}
		
		//
		$idSE = "6";
		$statusElemCur=$this->getCurStatusFromPost($arr, "elem_chng_div".$idSE."_Od", "elem_chng_div".$idSE."_Os", $statusElem);

		//Edit Xml --
		$arrIn["xOd"]=$xmlOd;
		$arrIn["xOs"]=$xmlOs;
		$arrIn["xFileOd"]=$this->xmlFileOd;
		$arrIn["xFileOs"]=$this->xmlFileOs;
		$arrIn["arrSc"] = $arr;
		$arrOut= $this->editXml($arrIn);

		$xmlOd=$arrOut["xOd"];
		$xmlOs=$arrOut["xOs"];
		$siteExm=$arrOut["siteExm"];
		$desc.=$arrOut["desc"];
		$sumOd = $arrOut["sumOd"];
		$sumOs = $arrOut["sumOs"];
		//Edit Xml --

		//Set WNL --
		list($wnl,$wnlOd,$wnlOs,$pos) = $this->setExmWnlPos($sumOd,$sumOs,$wnl,$wnlOd,$wnlOs,$pos);
		//Set WNL --

		//Set Status Fields--
		if(!empty($statusElemCur)){ 
			$statusElem=$statusElemCur;			
		}else{
			$statusElem = $this->setStatusElem("6",$statusElem,$sumOd,$sumOs,$siteExm);
		}	
		//Set Status Fields--

		//Update Records--
		$sql="UPDATE ".$this->tbl." ".
			"SET ".
			"exam_date = '".date("Y-m-d H:i:s")."', ".
			"wnl = '".$wnl."', ".
			"optic_nerve_od = '".sqlEscStr($xmlOd)."', ".
			"optic_nerve_os = '".sqlEscStr($xmlOs)."', ".
			"isPositive = '".$pos."', ".
			"optic_nerve_od_summary = '".sqlEscStr($sumOd)."', ".
			"optic_nerve_os_summary = '".sqlEscStr($sumOs)."', ".
			"wnlOpticOd='".$wnlOd."', ".
			"wnlOpticOs='".$wnlOs."', ".
			//"descExternal = '".sqlEscStr($desc)."', ".
			"uid = '".$this->uid."', ".
			"statusElem = '".sqlEscStr($statusElem)."' ".
			"WHERE form_id = '".$this->fid."' AND patient_id = '".$this->pid."' AND purged = '0' ";
		$res=sqlQuery($sql);
	}

	function set2PrvVals(){
		if($this->isRecordExists()){
			$res1 = $this->getRecord(" optic_id ");
			if($res1!=false){
				$Id = $res1["optic_id"];
			}
			
			$res = $this->getLastRecord(" c2.optic_id ","1");		
			if($res!=false){
				$Id_LF = $res["optic_id"];
			}

			//CopyLF
			$ignoreFlds = "form_id,exam_date,uid,statusElem,examined_no_change,ut_elem,wnl_value_Optic";
			if(!empty($Id_LF)&&!empty($Id)){
				$this->carryForwardExe($this->tbl,$Id,$Id_LF,$ignoreFlds);
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
			$res1 = $this->getRecord(" optic_id,statusElem ");
			if($res1!=false){
				$Id = $res1["optic_id"];
				if($is_cryfd==0){$statusElem = $res1["statusElem"];}
			}

			//CopyLF
			$ignoreFlds = "form_id,exam_date,uid,patient_id,";
			if(!empty($Id)){
			
				if(empty($_POST["site"]) || $_POST["site"]=="OU"){ $statusElem = "";  }
				if($_POST["site"]=="OS"){ 
					$ignoreFlds .= "od_text,optic_nerve_od,optic_nerve_od_summary,wnlOpticOd,cd_val_od,ncOpticOd,modi_note_OpticOd,modi_note_CDOd,
						cdr_od,cdr_od_summary,"; 
					if(!empty($statusElem)){ $statusElem = str_replace("_Os=1","_Os=0",$statusElem);  }			
				}else if($_POST["site"]=="OD"){ 
					$ignoreFlds .= "os_text,optic_nerve_os,optic_nerve_os_summary,wnlOpticOs,cd_val_os,ncOpticOs,modi_note_OpticOs,modi_note_CDOs,
						cdr_os,cdr_os_summary,"; 
					if(!empty($statusElem)){ $statusElem = str_replace("_Od=1","_Od=0",$statusElem);  }				
				}
				if($_POST["site"]=="OD" || $_POST["site"]=="OS"){
					$ignoreFlds .= "isPositive,";
					if($is_cryfd==0){$ignoreFlds .= "wnl_value_Optic,";}
				}
				$ignoreFlds = trim($ignoreFlds,",");	
			
				$this->resetValsExe($this->tbl,$Id,$ignoreFlds);
				$this->setStatus($statusElem,$this->tbl);
			}
		//}else{
			//
		//	$this->insertNew();
		//}	
	}
	
	function save_wnl($op="get", $arv=array()){
		$form_id = $this->fid;
		$patientId = $this->pid;
		$w = strtoupper($_POST["w"]);
		$exmEye = $_POST["elem_exmEye"];
		
		//Get Template Procedures ---
		$arrTempProc=array("All");
		if(isset($_POST["artemp"])&&!empty($_POST["artemp"])){
			//$arrTempProc=$_POST["artemp"];
			$arrTempProc = json_decode(gzuncompress(base64_decode($_POST["artemp"])));
		}			
		//Get Template Procedures ---
		
		if($op=="get"){
			$ar_ret=array();
			$wnl_value_Optic="";
			$wnl_value_Optic_phrase="";
			$modi_note_OpticOd=$modi_note_OpticOs="";
			$flgCarry_optic=0;
			$modi_note_OpticOd = $modi_note_OpticOs="";
			$flgCarry=0;
			
			if(!$this->isRecordExists()){
				$this->carryForward();
				$flgCarry_optic=1;
			}
			
			$cQry = "select 
					wnl AS wnl_optic, wnlOpticOd, wnlOpticOs,
					optic_nerve_od_summary,optic_nerve_os_summary,
					isPositive as posOptic,
					statusElem AS statusElem_Optic, uid AS uid_optic,wnl_value_Optic,
					optic_id
					FROM chart_optic WHERE form_id='".$form_id."' AND patient_id='".$patientId."'  AND purged = '0' ";
			$row = sqlQuery($cQry);
			if($row == false){	
				
			}else{
				$ar_ret=$row;
				/*
				extract($row);
				$elem_wnlOptic = $wnl_optic;
				$elem_wnlOpticOd = $wnlOpticOd;
				$elem_wnlOpticOs = $wnlOpticOs;
				$statusElem_Optic = $statusElem_Optic;
				$posOptic = $isPositive;
				$uid_optic=$uid_optic;
				$wnl_value_Optic=$row["wnl_value_Optic"];
				*/
			}
			return $ar_ret;
		}else if($op=="set" && count($arv)){
			extract($arv);
			
			$elem_wnlOptic = $wnl_optic;
			$elem_wnlOpticOd = $wnlOpticOd;
			$elem_wnlOpticOs = $wnlOpticOs;
			$statusElem_Optic = $statusElem_Optic;			
			$uid_optic=$uid_optic;
			$wnl_value_Optic=$row["wnl_value_Optic"];
			
			$oWv = new WorkView();
			
			//Toggle Optic
			if(in_array("Opt. Nev",$arrTempProc)||in_array("All",$arrTempProc)){
			if((!empty($statusElem_Optic)&&strpos($statusElem_Optic,"0")===false) || (empty($optic_nerve_od_summary)&&empty($elem_wnlOpticOd)) || (empty($optic_nerve_os_summary) && empty($elem_wnlOpticOs))){

			list($elem_wnlOpticOd,$elem_wnlOpticOs,$elem_wnlOptic) =
									$oWv->toggleWNL($posOptic,$optic_nerve_od_summary,$optic_nerve_os_summary,
													$elem_wnlOpticOd,$elem_wnlOpticOs,$elem_wnlOptic,$exmEye);
			}
			}
			
			//Status
			$statusElem_Optic = $this->setEyeStatus("OPTIC", $exmEye,$statusElem_Optic);

			//Toggle --
			
			//getWnlValue
			if(empty($wnl_value_Optic)){
				$wnl_value_Optic=$this->getExamWnlStr("Optic Nerve");
				$wnl_value_Optic_phrase = ", wnl_value_Optic='".sqlEscStr($wnl_value_Optic)."' ";				
			}
			
			$sql = "UPDATE chart_optic SET wnl='".$elem_wnlOptic."', wnlOpticOd='".$elem_wnlOpticOd."', wnlOpticOs='".$elem_wnlOpticOs."',			
					exam_date='".date("Y-m-d H:i:s")."', uid='".$_SESSION["authId"]."',
					statusElem='".$statusElem_Optic."',
					modi_note_OpticOd = CONCAT('".sqlEscStr($modi_note_OpticOd)."',modi_note_OpticOd),
					modi_note_OpticOs = CONCAT('".sqlEscStr($modi_note_OpticOs)."',modi_note_OpticOs)
					"." ".$wnl_value_Optic_phrase."
					WHERE form_id='".$form_id."' AND patient_id='".$patientId."'  AND purged = '0' ";
			$res = sqlQuery($sql);
			return $elem_wnlOptic;
			
		}		
		
		/**
		
		//Check For Alingment of WNL Values --
		if(in_array("Opt. Nev",$arrTempProc)||in_array("All",$arrTempProc)){
		$arrAlign[] = array($posOptic,$elem_wnlOpticOd,$elem_wnlOpticOs,$optic_nerve_od_summary,$optic_nerve_os_summary);
		}

		$arrAlign = $oWv->alignWnlVals($arrAlign,$exmEye);

		//print_r($arrAlign);
		//echo $exmEye;

		if(!empty($arrAlign["od"])){
			$elem_wnlVitreous=$elem_wnlVitreousOd="0";
			$elem_wnlRetinal=$elem_wnlRetinalOd="0";
			
			//$elem_wnlDrawRv=$elem_wnlDrawRvOd="0";
			$elem_wnlOptic=$elem_wnlOpticOd="0";
		}

		if(!empty($arrAlign["os"])){
			$elem_wnlVitreous=$elem_wnlVitreousOs="0";
			$elem_wnlRetinal=$elem_wnlRetinalOs="0";
			
			//$elem_wnlDrawRv=$elem_wnlDrawRvOs="0";
			$elem_wnlOptic=$elem_wnlOpticOs="0";
		}
		
		//Check For Alingment of WNL Values --		

		//if Not carry			
		if($flgCarry_optic==0){	
			//Modifying Notes----------------
			$modi_note_OpticOd=$oWv->getModiNotes($optic_nerve_od_summary,$wnlOpticOd,$optic_nerve_od_summary,$elem_wnlOpticOd,$uid_optic);
			$modi_note_OpticOs=$oWv->getModiNotes($optic_nerve_os_summary,$wnlOpticOs,$optic_nerve_os_summary,$elem_wnlOpticOs,$uid_optic);
			//Modifying Notes----------------
		}			
		//End if Not carry
		**/
	}
	
	function isNoChanged(){
		$res= $this->getRecord("examined_no_change,statusElem");
		if($res!=false){
			if( !empty($res["statusElem"]) && strpos($res["statusElem"],"=1")!==false){
				return true;
			}
			if( !empty($res["examined_no_change"]) ){
				return true;
			}		
		}
		return false;		
	}
	
	function save_no_change($tmpNC=0){
		$form_id = $this->fid;
		$patientId = $this->pid;
		$w = strtoupper($_POST["w"]);
		$exmEye = $_POST["elem_exmEye"];
		
		$tmpNC=0;
		$elem_noChangeOptic=$elem_noChangeOptic_od=$elem_noChangeOptic_os="0";
		
		$oFE=$this; //new FundusExam($patientId,$form_id);
		if(!$oFE->isRecordExists()){
			$oFE->carryForward();
			$tmpNC=1;
		}else if(!$oFE->isNoChanged()){
			$tmpNC=1;
		}else{
			$oFE->set2PrvVals();
			$tmpNC=0;
		}
		
		// ---
		//**Master NC or NC on the Fundus exam does not constitute change in drawing.  They must open the drawing page and make a change or select No Change.
		//Set NC
		if($tmpNC==1){
			if($exmEye=="OU"){
				//$elem_noChangeRv=$elem_ncDrawRv=$elem_ncDrawRv_od=$elem_ncDrawRv_os="1";
				$elem_noChangeOptic="1"; 
				$elem_noChangeOptic_od="1"; 
				$elem_noChangeOptic_os="1";
			}else if($exmEye=="OD"){
				//$elem_ncDrawRv_od="1";
				$elem_noChangeOptic_od="1";
			}else if($exmEye=="OS"){
				//$elem_ncDrawRv_os="1";
				$elem_noChangeOptic_os="1";
			}
		}
		// ---	
		
		/*
		//Optic
		$oON=$this; //new OpticNerve($patientId,$form_id);
		if(!$oON->isRecordExists()){
			$oON->carryForward();				
		}
		
		if($tmpNC==0){
			$oON->set2PrvVals();
		}
		*/
		
		//Get status string --
		$statusElem_Optic="";
		if($elem_noChangeOptic_od==1||$elem_noChangeOptic_os==1){ $statusElem_Optic=$this->se_elemStatus("OPTIC","1","",$elem_noChangeOptic_od,$elem_noChangeOptic_os); }
		//Get status--
		
		$sql = "UPDATE chart_optic SET examined_no_change='".$elem_noChangeOptic."',
					ncOpticOd='".$elem_noChangeOptic_od."', ncOpticOs='".$elem_noChangeOptic_os."',
					exam_date='".date("Y-m-d H:i:s")."', uid='".$_SESSION["authId"]."',
					statusElem='".$statusElem_Optic."'
					WHERE form_id='".$form_id."' AND patient_id='".$patientId."'  AND purged = '0' ";
		$res = sqlQuery($sql);
		
	}
	
	function get_chart_info($elem_dos){
		$oExamXml = new ExamXml();
		$patient_id = $this->pid;
		$elem_formId=$form_id = $this->fid;
		$ar_ret = array();
		
		//Optic
		$myflag=false;
		$elem_opticId="";
		
		$sql = "SELECT * FROM ".$this->tbl." WHERE form_id = '".$elem_formId."' AND patient_id='".$patient_id."' AND purged = '0' ";		
		$row=sqlQuery($sql);
		//------ Check in archive tables for chart_slit_lamp_exam/chart_la/chart_rv ---------
		if(!empty($GLOBALS["CHK_ARCHIVE_TABLE"])){
			if($row==false){
				$sql = str_replace($this->tbl,$this->tbl."_archive",$sql);
				$row = sqlQuery($sql);
			}	
		}
		//-------- End ----------------------------------------------------------------------------------
		
		if($row==false){
		//if(mysql_num_rows($res)<=0 && $finalize_flag==0){
			//$res = valNewRecordOptic($patient_id);
			$res = $this->getLastRecord(" * ",0,$elem_dos);
			if($res!=false){$row=$res;}else{$row=false;}	
			$myflag=true;		
		}
		if(isset($_GET["prevVal"]) && ($_GET["prevVal"] == 1)){
			//$res = valNewRecordOptic($patient_id, " * ", "1");
			$res = $this->getLastRecord(" * ",1,$elem_dos);
			if($res!=false){$row=$res;}else{$row=false;}
			$myflag=true;
		}
		
		if($row!=false){
		//if(@mysql_num_rows($res)>0){
			//extract(@mysql_fetch_array($res));
			extract($row);
			if($myflag){
				$elem_editMode=0;		
			}else{
				$elem_editMode=1;
				$ar_ret["elem_examDateOptic"]=$exam_date;
			}
			$ar_ret["elem_opticId_LF"]=$ar_ret["elem_opticId"]=$optic_id;
			$ar_ret["elem_editModeOptic"] = $elem_editMode;
			
					
			$ar_ret["elem_ncOptic"] = ($elem_editMode==1) ? $examined_no_change : 0 ;	
			$ar_ret["elem_notApplicableOptic"]=$not_applicable;
			$ar_ret["elem_wnlOptic"]=$wnl;
			$ar_ret["elem_cdOd"] =$od_text ;
			$ar_ret["elem_cdOs"] =$os_text ;
			
			$optic_nerve_od= stripslashes($optic_nerve_od);
			$optic_nerve_os= stripslashes($optic_nerve_os);
			//$optic_custom_field= stripslashes($custom_field);
			
			$arr_vals_optic_nerve_od = $oExamXml->extractXmlValue($optic_nerve_od);
			$ar_ret = array_merge($ar_ret, $arr_vals_optic_nerve_od);
			//extract($arr_vals_optic_nerve_od);
			$arr_vals_optic_nerve_os = $oExamXml->extractXmlValue($optic_nerve_os);
			$ar_ret = array_merge($ar_ret, $arr_vals_optic_nerve_os);
			//extract($arr_vals_optic_nerve_os);
			
			$ar_ret["elem_posOptic"] = $isPositive;
			$ar_ret["elem_wnlOpticOd"]=$wnlOpticOd;
			$ar_ret["elem_wnlOpticOs"]=$wnlOpticOs;
			$ar_ret["elem_statusElements_optic"] = ($elem_editMode==0) ? "" : $statusElem;
			$ar_ret["elem_cdValOd"]=$cd_val_od;
			$ar_ret["elem_cdValOs"]=$cd_val_os;
			$ar_ret["elem_utElemsOptic"] = ($elem_editMode==1) ? $row["ut_elem"] : "" ;
			
			if(!empty($cdr_od)){
				$elem_cdr_od = $cdr_od;		
				
				$arr_vals_cdr_od_od = $oExamXml->extractXmlValue($elem_cdr_od);
				$ar_ret = array_merge($ar_ret, $arr_vals_cdr_od_od);
				//extract($arr_vals_cdr_od_od);
			}
			
			if(!empty($cdr_os)){
				$elem_cdr_os = $cdr_os;		
				
				$arr_vals_cdr_od_os = $oExamXml->extractXmlValue($elem_cdr_os);
				$ar_ret = array_merge($ar_ret, $arr_vals_cdr_od_os);
				//extract($arr_vals_cdr_od_os);
			}
			
			$ar_ret["optic_nerve_od_summary"]=$optic_nerve_od_summary;
			$ar_ret["optic_nerve_os_summary"]=$optic_nerve_os_summary;			
		}
		return $ar_ret;
	}
	
	public function save_form(){
		$elem_formId = $formId = $this->fid;
		$patientid = $this->pid;
		$oChartNoteSaver = new ChartNoteSaver($patientid, $formId);
		$oExamXml = new ExamXml();	
		$arXmlFiles = $oExamXml->getExamXmlFiles("Fundus");
		
		//GetChangeIndicator
		$arrSe = array();
		$tmpd = "elem_chng_div6_Od";
		$tmps = "elem_chng_div6_Os";
		$$tmpd = $_POST[$tmpd];
		$$tmps = $_POST[$tmps];
		$arrSe[$tmpd] = ($$tmpd == "1") ? "1" : "0";
		$arrSe[$tmps] = ($$tmps == "1") ? "1" : "0";	
		$statusElem = $oChartNoteSaver->getStrSe($arrSe);
		
		//print_r($arrSe);
		//echo "<br>";
		//exit($statusElem);
		
		//Optic	-------------
		$wnlOpticOd = $wnlOpticOs = $wnlOptic = $posOptic = $ncOptic = "0";
		//if(!empty($elem_chng_div6_Od) || !empty($elem_chng_div6_Os)){
		//	if(!empty($elem_chng_div6_Od)){
				$menuName = "opticNerveOd";
				$menuFilePath = $arXmlFiles["opticNerveDisc"]["od"]; //dirname(__FILE__)."/xml/opticNerveDisc_od.xml";
				$elem_opticNerve_od = $oExamXml->newXmlString($menuName,$strMenu,$menuFilePath);
				$wnlOpticOd = $_POST["elem_wnlOpticOd"];
				$cdOd = sqlEscStr($_POST["elem_cdOd"]);
				$cd_val_od = $_POST["elem_cdValOd"];
		//	}

		//	if(!empty($elem_chng_div6_Os)){
				$menuName = "opticNerveOs";
				$menuFilePath = $arXmlFiles["opticNerveDisc"]["os"]; //dirname(__FILE__)."/xml/opticNerveDisc_os.xml";
				$elem_opticNerve_os = $oExamXml->newXmlString($menuName,$strMenu,$menuFilePath);
				$wnlOpticOs = $_POST["elem_wnlOpticOs"];
				$cdOs = sqlEscStr($_POST["elem_cdOs"]);
				$cd_val_os = $_POST["elem_cdValOs"];
		//	}
			//$_POST["elem_wnlOptic"];
			if(!empty($wnlOpticOd) && !empty($wnlOpticOs)){
				$wnlOptic = "1";
			}else{
				$wnlOptic = "0";
			}

			$posOptic = $_POST["elem_posOptic"];
			$ncOptic = $_POST["elem_ncOptic"];
		//}

		$opticId = $_POST["elem_opticId"];
		$examDateOptic = $_POST["elem_examDateOptic"];
		$notApplicable = "";//$_POST["elem_notApplicable"];
		$oOpticNerve = new OpticNerve($patientid,$elem_formId);
		//Optic	-------------

		//C:D ----------------------
			$menuName = "cdrOd";
			$menuFilePath = $arXmlFiles["cd"]["od"]; //dirname(__FILE__)."/xml/cd_od.xml";
			$elem_cdr_od = $oExamXml->newXmlString($menuName,$strMenu,$menuFilePath);
			
			
			$menuName = "cdrOs";
			$menuFilePath = $arXmlFiles["cd"]["os"]; //dirname(__FILE__)."/xml/cd_os.xml";
			$elem_cdr_os = $oExamXml->newXmlString($menuName,$strMenu,$menuFilePath);
				
		//C:D ----------------------
		
		$examDate = wv_dt("now"); //$_POST["elem_examDate"];
		$oUserAp = new UserAp();
		
		// Summary --
		$elem_optic_nerve_od_summary = $elem_optic_nerve_os_summary = "";
		$opticNerve_od = $elem_opticNerve_od; //od
		
		$arrTemp = $this->getExamSummary($opticNerve_od);
		$elem_optic_nerve_od_summary = $arrTemp["Summary"];	
		$arrExmDone_od = $arrTemp["ExmDone"];
		if(!empty($arrSe["elem_chng_div6_Od"])){
			$strExamsAllOd .= $oUserAp->refineByConsoleSymp("Optic Nerve",$arrExmDone_od,$elem_optic_nerve_od_summary);
		}
		$opticNerve_os = $elem_opticNerve_os; //os
		
		$arrTemp = $this->getExamSummary($opticNerve_os);
		$elem_optic_nerve_os_summary = $arrTemp["Summary"];
		$arrExmDone_os = $arrTemp["ExmDone"];
		if(!empty($arrSe["elem_chng_div6_Os"])){
			$strExamsAllOs .= $oUserAp->refineByConsoleSymp("Optic Nerve",$arrExmDone_os,$elem_optic_nerve_os_summary);
		}			
		
		$elem_cdr_od_summary = $elem_cdr_os_summary = "";
		$cdr_od = $elem_cdr_od; //od
		
		$arrTemp = $this->getExamSummary($cdr_od);
		$elem_cdr_od_summary = $arrTemp["Summary"];
		$arrExmDone_od = $arrTemp["ExmDone"];
		$strExamsAllOd .= $oUserAp->refineByConsoleSymp("C:D",$arrExmDone_od,$elem_cdr_od_summary);
		
		$cdr_os = $elem_cdr_os; //os
		$arrTemp = $this->getExamSummary($cdr_os);
		$elem_cdr_os_summary = $arrTemp["Summary"];
		$arrExmDone_os = $arrTemp["ExmDone"];
		$strExamsAllOs .= $oUserAp->refineByConsoleSymp("C:D",$arrExmDone_os,$elem_cdr_os_summary);
		// Summary --
		
		//ut_elems ----------------------
		$elem_utElems = $_POST["elem_utElemsOptic"];
		$elem_utElems_cur = $_POST["elem_utElemsOptic_cur"];
		$ut_elem = $this->getUTElemString($elem_utElems,$elem_utElems_cur);
		//ut_elems ----------------------
		
		//Purge
		if(!empty($_POST["elem_purged"])){			
			//Update
			$sql = "UPDATE ".$this->tbl."
				  SET
				  purged=optic_id,
				  purgerId='".$_SESSION["authId"]."',
				  purgetime='".wv_dt('now')."'
				  WHERE form_id='".$elem_formId."' AND patient_id='".$patientid."' AND purged = '0'
				";
			$row = sqlQuery($sql);
			
		}else{
			$owv = new WorkView();
			
			$elem_opticNerve_od = sqlEscStr($elem_opticNerve_od);
			$elem_opticNerve_os = sqlEscStr($elem_opticNerve_os);

			$elem_cdr_od = sqlEscStr($elem_cdr_od);
			$elem_cdr_os = sqlEscStr($elem_cdr_os);
			
			//check
			$cQry = "select last_opr_id , uid,
						optic_nerve_od_summary, optic_nerve_os_summary, wnlOpticOd, wnlOpticOs, modi_note_OpticArr,wnl_value_Optic, 
						cdr_od_summary, cdr_os_summary, modi_note_CDArr,exam_date
					FROM ".$this->tbl." WHERE form_id='".$elem_formId."' AND patient_id='".$patientid."' AND purged = '0'  ";
			$row = sqlQuery($cQry);
			if($row == false) {
				$elem_editModeOptic = "0";
				$last_opr_id = $_SESSION["authId"];
			}else{
				$elem_editModeOptic = "1";
				$last_opr_id = $owv->get_last_opr_id($row['last_opr_id'],$row["uid"]);
				//Modifying Notes----------------
				//$modi_note_OpticOd=$owv->getModiNotes($row["optic_nerve_od_summary"],$row["wnlOpticOd"],$elem_optic_nerve_od_summary,$wnlOpticOd,$row["uid"],$row["wnl_value_Optic"]);
				//$modi_note_OpticOs=$owv->getModiNotes($row["optic_nerve_os_summary"],$row["wnlOpticOs"],$elem_optic_nerve_os_summary,$wnlOpticOs,$row["uid"],$row["wnl_value_Optic"]);			
				$seri_modi_note_opticArr = $owv->getModiNotesArr($row["optic_nerve_od_summary"],$elem_optic_nerve_od_summary,$last_opr_id,'OD',$row["modi_note_OpticArr"],$row['exam_date']);
				
				//$modi_note_CDOd=$owv->getModiNotes($row["cdr_od_summary"],0,$elem_cdr_od_summary,0,$row["uid"]);
				//$modi_note_CDOs=$owv->getModiNotes($row["cdr_os_summary"],0,$elem_cdr_os_summary,0,$row["uid"]);
				
				$seri_modi_note_CDArr = $owv->getModiNotesArr($row["cdr_od_summary"],$elem_cdr_od_summary,$last_opr_id,'OD',$row["modi_note_CDArr"],$row['exam_date']);
				$seri_modi_note_CDArr = $owv->getModiNotesArr($row["cdr_os_summary"],$elem_cdr_os_summary,$last_opr_id,'OS',$seri_modi_note_CDArr,$row['exam_date']);
				//Modifying Notes----------------
			}
			
			//
			$sql_con = "
				od_text='".$cdOd."',
				 os_text='".$cdOs."',
				 optic_nerve_od = '".$elem_opticNerve_od."',
				 optic_nerve_os = '".$elem_opticNerve_os."',
				 optic_nerve_od_summary = '".$elem_optic_nerve_od_summary."',
				 optic_nerve_os_summary = '".$elem_optic_nerve_os_summary."',
				 examined_no_change='".$ncOptic."',
				 not_applicable='".$notApplicable."',
				 wnl='".$wnlOptic."',
				 isPositive='".$posOptic."',
				 wnlOpticOd='".$wnlOpticOd."',
				 wnlOpticOs='".$wnlOpticOs."',
				 uid = '".$_SESSION["authId"]."',
				 statusElem = '".$statusElem."',
				 cd_val_od = '".$cd_val_od."',
				 cd_val_os = '".$cd_val_os."',
				 cdr_od = '".$cdr_od."',
				 cdr_os = '".$cdr_os."',
				 cdr_od_summary = '".sqlEscStr($elem_cdr_od_summary)."', 
				 cdr_os_summary = '".sqlEscStr($elem_cdr_os_summary)."',
				 last_opr_id = '".$last_opr_id."',
				 modi_note_OpticArr  = '".sqlEscStr($seri_modi_note_opticArr)."',
				 modi_note_CDArr  = '".sqlEscStr($seri_modi_note_CDArr)."'	,
				 ut_elem = '".$ut_elem."'
			";
			
			//
			if($elem_editModeOptic == "0"){
				//WNL
				$wnl_value_Optic = $this->getExamWnlStr("Optic Nerve");
				// Insert
				$sql1 = "INSERT INTO ".$this->tbl."
					 set
					form_id='".$formId."',
					patient_id='".$patientid."',	
					wnl_value_Optic='".sqlEscStr($wnl_value_Optic)."', 
					exam_date='$examDate',	
					 ";
				$sql = $sql1. $sql_con;				
				$insertId = sqlInsert($sql);
				
			}else if($elem_editModeOptic == "1"){
				//Update
				$sql1 = "UPDATE ".$this->tbl." SET ";
				$sql2 = " WHERE form_id='".$formId."' AND patient_id='".$patientid."' AND purged = '0' ";
				$sql = $sql1. $sql_con. $sql2; 
				$res = sqlQuery($sql);
				$insertId = $retinalId;
			}
			
			// Make chart notes valid
			$this->makeChartNotesValid();
			
			//Set Change Date Arc Rec --
			$oChartNoteSaver->setChangeDtArcRec($this->tbl);
			//Set Change Date Arc Rec --
		}
		//combine
		$strExamsAll = $oChartNoteSaver->combineExamFindings($strExamsAllOd, $strExamsAllOs);
		$strExamsAll = $oChartNoteSaver->makeArrString($strExamsAll);		
		
		return $strExamsAll;
	}
	
	
	function getWorkViewSummery($post){	
		$oOnload =  new Onload();
		//object Chart Rec Archive --
		$oChartRecArc = new ChartRecArc($this->pid,$this->fid,$_SESSION['authId']);		
		
		//Get Template Procedures ---
		$arrTempProc=array("All");
		if(isset($post["artemp"])&&!empty($post["artemp"])){
			$arrTempProc=$post["artemp"];
		}			
		//Get Template Procedures ---
		
		$sql ="SELECT ".
			//c8-chart_optic-------
			"c8.isPositive AS flagPosOptic, c8.wnl AS flagWnlOptic, c8.examined_no_change AS chOptic, ".
			"c8.od_text, c8.os_text, c8.optic_nerve_od_summary, ".
			"c8.optic_nerve_os_summary, c8.optic_id, ".
			"c8.wnlOpticOd, c8.wnlOpticOs, ".
			"c8.statusElem AS se_optic, ".
			"c8.cd_val_od, c8.cd_val_os, ".
			"c8.modi_note_OpticOd, c8.modi_note_OpticOs, ".
			"c8.modi_note_CDOd, c8.modi_note_CDOs, ".
			"c8.cdr_od_summary, c8.cdr_os_summary, ".
			"c8.modi_note_OpticArr, c8.modi_note_CDArr, ".
			"c8.wnl_value_Optic  ".			
			"FROM chart_master_table c1 ".
			"INNER JOIN ".$this->tbl." c8 ON c8.form_id = c1.id AND c8.purged='0'  ".			
			"WHERE c1.id = '".$this->fid."' AND c1.patient_id='".$this->pid."' ";			
		$row = sqlQuery($sql);	
		//------ Check in archive tables for chart_slit_lamp_exam/chart_la/chart_rv ---------
		if(!empty($GLOBALS["CHK_ARCHIVE_TABLE"])){
			if($row==false){
				$sql = str_replace($this->tbl,$this->tbl."_archive",$sql);
				$row = sqlQuery($sql);
			}	
		}
		//-------- End ----------------------------------------------------------------------------------	
		if($row != false){
			$elem_noChangeOptic=assignZero($row["chOptic"]);
			$elem_wnlOptic=assignZero($row["flagWnlOptic"]);
			$elem_posOptic=assignZero($row["flagPosOptic"]);

			//Optic
			$elem_wnlOpticOd=assignZero($row["wnlOpticOd"]);
			$elem_wnlOpticOs=assignZero($row["wnlOpticOs"]);
			$elem_se_Optic = $row["se_optic"];
			
			//CD
			$elem_od_text = trim($row["cd_val_od"]." ".$row["od_text"]);
			$elem_os_text = trim($row["cd_val_os"]." ".$row["os_text"]);
			$elem_cdr_od_summary = !empty($row["cdr_od_summary"]) ? $row["cdr_od_summary"] : $elem_od_text ;
			$elem_cdr_os_summary = !empty($row["cdr_os_summary"]) ? $row["cdr_os_summary"] : $elem_os_text ;
			$elem_cd_val_od=trim($row["cd_val_od"]);
			$elem_cd_val_os=trim($row["cd_val_os"]);
			$elem_optic_nerve_od_summary = $row["optic_nerve_od_summary"];
			$elem_optic_nerve_os_summary = $row["optic_nerve_os_summary"];			
			$elem_optic_id = $row["optic_id"];			
			$examdate = wv_formatDate($row["exam_date_RV"]);
			$modi_note_OpticOd=$row["modi_note_OpticOd"];
			$modi_note_OpticOs=$row["modi_note_OpticOs"];
			$modi_note_CDOd=$row["modi_note_CDOd"];
			$modi_note_CDOs=$row["modi_note_CDOs"];
			$modi_note_OpticArr = unserialize($row["modi_note_OpticArr"]);
			$modi_note_CDArr = unserialize($row["modi_note_CDArr"]);
			
				$arrHx = array();
				if(is_array($modi_note_CDArr) && count($modi_note_CDArr)>0 && $row["modi_note_CDArr"]!='')
				$arrHx['CD'] = $modi_note_CDArr;
				if(is_array($modi_note_OpticArr) && count($modi_note_OpticArr)>0 && $row["modi_note_OpticArr"]!='')
				$arrHx['Optic Nerve'] = $modi_note_OpticArr;
				
				
			//wnl			
			$elem_wnl_value_Optic = $row["wnl_value_Optic"];
		}
		
		//Previous
		if(empty($elem_optic_id)){ /* Show Previous values in finalized chart also ~&& ($finalize_flag == 0)~ */
			$tmp = "";
			$tmp .= "c2.isPositive AS flagPosOptic, c2.wnl AS flagWnlOptic, c2.examined_no_change AS chOptic, ";
			$tmp .= "c2.wnlOpticOd, c2.wnlOpticOs, c2.cd_val_od, c2.cd_val_os, ";
			$tmp .= "c2.od_text, c2.os_text, c2.optic_nerve_od_summary, c2.optic_nerve_os_summary, c2.optic_id, ";
			$tmp .= "c2.cdr_od_summary, c2.cdr_os_summary, c2.wnl_value_Optic, ";
			$tmp .= "c2.statusElem AS se_optic ";
			
			//$res = valNewRecordOptic($patient_id, $tmp);
			//for($i=0;$row=sqlFetchArray($res);$i++)	{
			$optic_nerve = new OpticNerve($this->pid, $this->fid);
			$elem_dos=$this->getDos();
			$elem_dos=wv_formatDate($elem_dos,0,0,"insert");
			$res = $optic_nerve->getLastRecord($tmp,0,$elem_dos);
			if($res!=false){$row=$res;}else{$row=false;}

			if($row!=false){
				$elem_cd_val_od=trim($row["cd_val_od"]);
				$elem_cd_val_os=trim($row["cd_val_os"]);
				
				$elem_od_text = $row["cd_val_od"]." ".stripslashes($row["od_text"]);
				$elem_os_text = $row["cd_val_os"]." ".stripslashes($row["os_text"]);
				$elem_od_text = trim($elem_od_text);
				$elem_os_text = trim($elem_os_text);
				$elem_optic_nerve_od_summary = $row["optic_nerve_od_summary"];
				$elem_optic_nerve_os_summary = $row["optic_nerve_os_summary"];
				
				$elem_wnlOptic=assignZero($row["flagWnlOptic"]);
				$elem_posOptic=assignZero($row["flagPosOptic"]);
				$elem_wnlOpticOd=assignZero($row["wnlOpticOd"]);
				$elem_wnlOpticOs=assignZero($row["wnlOpticOs"]);
				$elem_cd_val_od = $row["cd_val_od"];
				$elem_cd_val_os = $row["cd_val_os"];
				//
				$elem_cdr_od_summary = !empty($row["cdr_od_summary"]) ? $row["cdr_od_summary"] : $elem_od_text ;
				$elem_cdr_os_summary = !empty($row["cdr_os_summary"]) ? $row["cdr_os_summary"] : $elem_os_text ;
				//
				$elem_wnl_value_Optic = $row["wnl_value_Optic"];
				$elem_se_Optic_prev = $row["se_optic"];
			}

			//BG
			$bgColor_optic = "bgSmoke";
		}
		
		//is Change is made in new chart -----
		$tmpArrSe = array();
		$flgSe_Optic_Od = $flgSe_Optic_Os = "0";
		if(!isset($bgColor_optic)){
			if(!empty($elem_se_Optic)){
				$tmpArrSe = $this->se_elemStatus("OPTIC","0",$elem_se_Optic);
				$flgSe_Optic_Od = $tmpArrSe["6"]["od"];
				$flgSe_Optic_Os = $tmpArrSe["6"]["os"];
			}
		}else{
			if(!empty($elem_se_Optic_prev)){
				$tmpArrSe_prev = $this->se_elemStatus("OPTIC","0",$elem_se_Optic_prev);
				$flgSe_Optic_Od_prev = $tmpArrSe_prev["6"]["od"];
				$flgSe_Optic_Os_prev = $tmpArrSe_prev["6"]["os"];						
			}
		}
		//is Change is made in new chart -----
		
		//Optic Nerve --
		$wnlString_opticnerve = !empty($elem_wnl_value_Optic) ? $elem_wnl_value_Optic : $this->getExamWnlStr("Optic Nerve"); //"Pink & Sharp"
		$wnlStringOd_opticnerve = $wnlStringOs_opticnerve = $wnlString_opticnerve;
		
		if(empty($flgSe_Optic_Od) && empty($flgSe_Optic_Od_prev) && !empty($elem_wnlOpticOd)){ $tmp = $this->getExamWnlStr_fromPrvExm("Optic Nerve", "OD"); if(!empty($tmp)){ $wnlStringOd_opticnerve = $tmp;}  }
		if(empty($flgSe_Optic_Os) && empty($flgSe_Optic_Os_prev) && !empty($elem_wnlOpticOs)){  $tmp = $this->getExamWnlStr_fromPrvExm("Optic Nerve", "OS"); if(!empty($tmp)){ $wnlStringOs_opticnerve = $tmp;}  }
		
		list($elem_optic_nerve_od_summary,$elem_optic_nerve_os_summary) = $oOnload->setWnlValuesinSumm(array("wValOd"=>$wnlStringOd_opticnerve,"wValOs"=>$wnlStringOs_opticnerve,
																	"wOd"=>$elem_wnlOpticOd,"sOd"=>$elem_optic_nerve_od_summary,
																	"wOs"=>$elem_wnlOpticOs,"sOs"=>$elem_optic_nerve_os_summary));
																	
		//Archive RV --
		
		
		$arrDivArcCmn=array();
		
		//Archive Optic --
		if($bgColor_optic != "bgSmoke"){
		
		$oChartRecArc->setChkTbl("chart_optic");
		$arrInpArc = array("elem_sumOdCD"=>array("cdr_od_summary",$elem_cdr_od_summary,"smof","","",$modi_note_CDOd),
							"elem_sumOsCD"=>array("cdr_os_summary",$elem_cdr_os_summary,"smof","","",$modi_note_CDOs),
				"elem_sumOdOptic" =>array("optic_nerve_od_summary",$elem_optic_nerve_od_summary,"smof","wnlOpticOd", $wnlString_opticnerve, $modi_note_OpticOd),
				"elem_sumOsOptic" =>array("optic_nerve_os_summary",$elem_optic_nerve_os_summary,"smof","wnlOpticOs", $wnlString_opticnerve, $modi_note_OpticOs)
							);

		$arTmpRecArc = $oChartRecArc->getArcRec($arrInpArc);
		//CD --
		//OD
		if(!empty($arTmpRecArc["div"]["elem_sumOdCD"])){
			//echo $arTmpRecArc["div"]["elem_sumOdCD"];
			$arrDivArcCmn["CD"]["OD"]=$arTmpRecArc["div"]["elem_sumOdCD"];
			$moeArc["od"]["CD"] = $arTmpRecArc["js"]["elem_sumOdCD"];
			$flgArcColor["od"]["CD"] = $arTmpRecArc["css"]["elem_sumOdCD"];
			if(!empty($arTmpRecArc["curText"]["elem_sumOdCD"])) 
				$elem_od_text = $arTmpRecArc["curText"]["elem_sumOdCD"];
		}
		//OS
		if(!empty($arTmpRecArc["div"]["elem_sumOsCD"])){
			//echo $arTmpRecArc["div"]["elem_sumOsCD"];
			$arrDivArcCmn["CD"]["OS"]=$arTmpRecArc["div"]["elem_sumOsCD"];
			$moeArc["os"]["CD"] = $arTmpRecArc["js"]["elem_sumOsCD"];
			$flgArcColor["os"]["CD"] = $arTmpRecArc["css"]["elem_sumOsCD"];
			if(!empty($arTmpRecArc["curText"]["elem_sumOsCD"])) 
				$elem_os_text = $arTmpRecArc["curText"]["elem_sumOsCD"];
		}
		//Optic --
		//OD
		if(!empty($arTmpRecArc["div"]["elem_sumOdOptic"])){
			//echo $arTmpRecArc["div"]["elem_sumOdOptic"];
			$arrDivArcCmn["Optic Nerve"]["OD"]=$arTmpRecArc["div"]["elem_sumOdOptic"];
			$moeArc["od"]["Optic"] = $arTmpRecArc["js"]["elem_sumOdOptic"];
			$flgArcColor["od"]["Optic"] = $arTmpRecArc["css"]["elem_sumOdOptic"];
			if(!empty($arTmpRecArc["curText"]["elem_sumOdOptic"])) 
				$elem_optic_nerve_od_summary = $arTmpRecArc["curText"]["elem_sumOdOptic"];
		}
		//OS
		if(!empty($arTmpRecArc["div"]["elem_sumOsOptic"])){
			//echo $arTmpRecArc["div"]["elem_sumOsOptic"];
			$arrDivArcCmn["Optic Nerve"]["OS"]=$arTmpRecArc["div"]["elem_sumOsOptic"];
			$moeArc["os"]["Optic"] = $arTmpRecArc["js"]["elem_sumOsOptic"];
			$flgArcColor["os"]["Optic"] = $arTmpRecArc["css"]["elem_sumOsOptic"];
			if(!empty($arTmpRecArc["curText"]["elem_sumOsOptic"])) 
				$elem_optic_nerve_os_summary = $arTmpRecArc["curText"]["elem_sumOsOptic"];
		}
		
		}//
		//Archive Optic --

		//Nochanged
		if(!empty($elem_se_Optic)&&strpos($elem_se_Optic,"=1")!==false){
			$elem_noChangeOptic=1;
		}
		
		$arr=array();
		
		//if(in_array("Opt. Nev",$arrTempProc) || in_array("All",$arrTempProc)){
			//CD
			$arr["subExm"][] = $oOnload->getArrExms_ms(array("enm"=>"CD",
												"sOd"=>$elem_cdr_od_summary ,"sOs"=>$elem_cdr_os_summary,
												"fOd"=>$flgSe_Optic_Od,"fOs"=>$flgSe_Optic_Os,
												//"arcJsOd"=>$moeArc["od"]["CD"],"arcJsOs"=>$moeArc["os"]["CD"],
												"arcCssOd"=>$flgArcColor["od"]["CD"],"arcCssOs"=>$flgArcColor["os"]["CD"],
												//"mnOd"=>$moeMN["od"]["CD"],"mnOs"=>$moeMN["os"]["CD"],
												"enm_2"=>"CD", "nm_2show"=>"C:D"));

			//Optic Nerve
			$arr["subExm"][] = $oOnload->getArrExms_ms(array("enm"=>"Optic Nerve",
												"sOd"=>$elem_optic_nerve_od_summary,"sOs"=>$elem_optic_nerve_os_summary,
												"fOd"=>$flgSe_Optic_Od,"fOs"=>$flgSe_Optic_Os,"pos"=>$elem_posOptic,
												//"arcJsOd"=>$moeArc["od"]["Optic"],"arcJsOs"=>$moeArc["os"]["Optic"],
												"arcCssOd"=>$flgArcColor["od"]["Optic"],"arcCssOs"=>$flgArcColor["os"]["Optic"],
												//"mnOd"=>$moeMN["od"]["Optic"],"mnOs"=>$moeMN["os"]["Optic"],
												"enm_2"=>"Optic"));

		//}
		
		//Sub Exam List
		$arr["seList"] = 	array("Optic"=>array("enm"=>"Opt.Nev","pos"=>$elem_posOptic,
							"wOd"=>$elem_wnlOpticOd,"wOs"=>$elem_wnlOpticOs)
						);
		$arr["bgColor"] = "".$bgColor_optic;
		$arr["nochange"] = $elem_noChangeOptic;
		$arr["examdate"] = $examdate;		
		$arr["moeMN"] = $moeMN;
		$arr["exm_flg_se"] = array($flgSe_Optic_Od,$flgSe_Optic_Os);
		$arr["elem_rvcd"] = array("od"=>$elem_cd_val_od, "os"=>$elem_cd_val_os);
		
		//$arr["arrDivArcCmn"] = $arrDivArcCmn;
		$arr["arrHx"] = $arrHx;		
		//---------
		
		return $arr;		
	}
	
	function getPurgedExm(){
	
		//Get Template Procedures ---
		$arrTempProc=array("All");
		if(isset($post["artemp"])&&!empty($post["artemp"])){
			$arrTempProc=$post["artemp"];
		}			
		//Get Template Procedures ---
	
		$oOnload =  new Onload();
		$arPurge = array();
		$sql ="SELECT ".
			//c8-chart_optic-------
			"c8.isPositive AS flagPosOptic, c8.wnl AS flagWnlOptic, c8.examined_no_change AS chOptic, ".
			"c8.od_text, c8.os_text, c8.optic_nerve_od_summary, ".
			"c8.optic_nerve_os_summary, c8.optic_id, ".
			"c8.wnlOpticOd, c8.wnlOpticOs, ".
			"c8.statusElem AS se_optic, ".
			"c8.cd_val_od, c8.cd_val_os, c8.purgerId AS purgerId_optic, c8.purgeTime AS purgeTime_optic, ".
			"c8.cdr_od_summary, c8.cdr_os_summary, ".
			"c8.wnl_value_Optic ".			
			"FROM chart_master_table c1 ".
			"INNER JOIN ".$this->tbl." c8 ON c8.form_id = c1.id AND c8.purged!='0'  ".			
			"WHERE c1.id = '".$this->fid."' AND c1.patient_id='".$this->pid."' ".
			"ORDER BY purgeTime_optic DESC ";
		$rez=sqlStatement($sql);
		//------ Check in archive tables for chart_slit_lamp_exam/chart_la/chart_rv ---------
		if(!empty($GLOBALS["CHK_ARCHIVE_TABLE"])){
			if(imw_num_rows($rez) <= 0){
				$sql = str_replace($this->tbl,$this->tbl."_archive",$sql);
				$rez=sqlStatement($sql);
			}	
		}
		//-------- End ----------------------------------------------------------------------------------
		for($i=0;$row=sqlFetchArray($rez);$i++){
			$elem_noChangeOptic=assignZero($row["chOptic"]);
			$elem_wnlOptic=assignZero($row["flagWnlOptic"]);
			$elem_posOptic=assignZero($row["flagPosOptic"]);

			//Optic
			$elem_wnlOpticOd=assignZero($row["wnlOpticOd"]);
			$elem_wnlOpticOs=assignZero($row["wnlOpticOs"]);
			$elem_se_Optic = $row["se_optic"];
			
			//CD
			$elem_od_text = trim($row["cd_val_od"]." ".$row["od_text"]);
			$elem_os_text = trim($row["cd_val_os"]." ".$row["os_text"]);
			$elem_cdr_od_summary = !empty($row["cdr_od_summary"]) ? $row["cdr_od_summary"] : $elem_od_text ;
			$elem_cdr_os_summary = !empty($row["cdr_os_summary"]) ? $row["cdr_os_summary"] : $elem_os_text ;
			$elem_cd_val_od=trim($row["cd_val_od"]);
			$elem_cd_val_os=trim($row["cd_val_os"]);
			$elem_optic_nerve_od_summary = $row["optic_nerve_od_summary"];
			$elem_optic_nerve_os_summary = $row["optic_nerve_os_summary"];			
			$elem_optic_id = $row["optic_id"];			
			$examdate = wv_formatDate($row["exam_date_RV"]);
			$modi_note_OpticOd=$row["modi_note_OpticOd"];
			$modi_note_OpticOs=$row["modi_note_OpticOs"];
			$modi_note_CDOd=$row["modi_note_CDOd"];
			$modi_note_CDOs=$row["modi_note_CDOs"];
			$modi_note_OpticArr = unserialize($row["modi_note_OpticArr"]);
			$modi_note_CDArr = unserialize($row["modi_note_CDArr"]);
			
				$arrHx = array();
				if(is_array($modi_note_CDArr) && count($modi_note_CDArr)>0 && $row["modi_note_CDArr"]!='')
				$arrHx['CD'] = $modi_note_CDArr;
				if(is_array($modi_note_OpticArr) && count($modi_note_OpticArr)>0 && $row["modi_note_OpticArr"]!='')
				$arrHx['Optic Nerve'] = $modi_note_OpticArr;
				
				
			//wnl			
			$elem_wnl_value_Optic = $row["wnl_value_Optic"];
			
			//is Change is made in new chart -----
			$tmpArrSe = array();
			$flgSe_Optic_Od = $flgSe_Optic_Os = "0";
			if(!isset($bgColor_optic)){
				if(!empty($elem_se_Optic)){
					$tmpArrSe = $this->se_elemStatus("OPTIC","0",$elem_se_Optic);
					$flgSe_Optic_Od = $tmpArrSe["6"]["od"];
					$flgSe_Optic_Os = $tmpArrSe["6"]["os"];
				}
			}else{
				if(!empty($elem_se_Optic_prev)){
					$tmpArrSe_prev = $this->se_elemStatus("OPTIC","0",$elem_se_Optic_prev);
					$flgSe_Optic_Od_prev = $tmpArrSe_prev["6"]["od"];
					$flgSe_Optic_Os_prev = $tmpArrSe_prev["6"]["os"];						
				}
			}
			//is Change is made in new chart -----
			
			//Optic Nerve --
			$wnlString_opticnerve = !empty($elem_wnl_value_Optic) ? $elem_wnl_value_Optic : $this->getExamWnlStr("Optic Nerve"); //"Pink & Sharp"
			$wnlStringOd_opticnerve = $wnlStringOs_opticnerve = $wnlString_opticnerve;
			
			if(empty($flgSe_Optic_Od) && empty($flgSe_Optic_Od_prev) && !empty($elem_wnlOpticOd)){ $tmp = $this->getExamWnlStr_fromPrvExm("Optic Nerve", "OD"); if(!empty($tmp)){ $wnlStringOd_opticnerve = $tmp;}  }
			if(empty($flgSe_Optic_Os) && empty($flgSe_Optic_Os_prev) && !empty($elem_wnlOpticOs)){  $tmp = $this->getExamWnlStr_fromPrvExm("Optic Nerve", "OS"); if(!empty($tmp)){ $wnlStringOs_opticnerve = $tmp;}  }
			
			list($elem_optic_nerve_od_summary,$elem_optic_nerve_os_summary) = $oOnload->setWnlValuesinSumm(array("wValOd"=>$wnlStringOd_opticnerve,"wValOs"=>$wnlStringOs_opticnerve,
																		"wOd"=>$elem_wnlOpticOd,"sOd"=>$elem_optic_nerve_od_summary,
																		"wOs"=>$elem_wnlOpticOs,"sOs"=>$elem_optic_nerve_os_summary));
																		
			//Nochanged
			if(!empty($elem_se_Optic)&&strpos($elem_se_Optic,"=1")!==false){
				$elem_noChangeOptic=1;
			}
			
			$arr=array();
			
			if(in_array("Opt. Nev",$arrTempProc) || in_array("All",$arrTempProc)){
				//CD
				$arr["subExm"][] = $oOnload->getArrExms_ms(array("enm"=>"CD",
													"sOd"=>$elem_cdr_od_summary ,"sOs"=>$elem_cdr_os_summary,
													"fOd"=>$flgSe_Optic_Od,"fOs"=>$flgSe_Optic_Os,
													//"arcJsOd"=>$moeArc["od"]["CD"],"arcJsOs"=>$moeArc["os"]["CD"],
													"arcCssOd"=>$flgArcColor["od"]["CD"],"arcCssOs"=>$flgArcColor["os"]["CD"],
													//"mnOd"=>$moeMN["od"]["CD"],"mnOs"=>$moeMN["os"]["CD"],
													"enm_2"=>"CD", "nm_2show"=>"C:D"));

				//Optic Nerve
				$arr["subExm"][] = $oOnload->getArrExms_ms(array("enm"=>"Optic Nerve",
													"sOd"=>$elem_optic_nerve_od_summary,"sOs"=>$elem_optic_nerve_os_summary,
													"fOd"=>$flgSe_Optic_Od,"fOs"=>$flgSe_Optic_Os,"pos"=>$elem_posOptic,
													//"arcJsOd"=>$moeArc["od"]["Optic"],"arcJsOs"=>$moeArc["os"]["Optic"],
													"arcCssOd"=>$flgArcColor["od"]["Optic"],"arcCssOs"=>$flgArcColor["os"]["Optic"],
													//"mnOd"=>$moeMN["od"]["Optic"],"mnOs"=>$moeMN["os"]["Optic"],
													"enm_2"=>"Optic"));

			}
			
			//Sub Exam List
			$arr["seList"] = 	array("Optic"=>array("enm"=>"Opt.Nev","pos"=>$elem_posOptic,
								"wOd"=>$elem_wnlOpticOd,"wOs"=>$elem_wnlOpticOs)
							);
			$arr["bgColor"] = "".$bgColor_optic;
			$arr["nochange"] = $elem_noChangeOptic;
			$arr["examdate"] = $examdate;		
			$arr["moeMN"] = $moeMN;
			$arr["exm_flg_se"] = array($flgSe_Optic_Od,$flgSe_Optic_Os);
			$arr["elem_rvcd"] = array("od"=>$elem_cd_val_od, "os"=>$elem_cd_val_os);
			
			$arr["purgerId"] = $row["purgerId_optic"];
			$arr["purgeTime"] = $row["purgeTime_optic"];		
			//---------															
			$arPurge[] = $arr;
		}
		return $arPurge;
	}
	
	function saveCDRV(){
		$patientId = $this->pid; //$_SESSION["patient"];
		$form_id = $this->fid; //$_REQUEST["fid"];
		$cd_od = $_POST["elem_cdValOd"];
		$cd_os = $_POST["elem_cdValOs"];			
		
		//oUserAP
		$oUserAp = new UserAp();
		$oWv = new WorkView();
		$oON = $this;
		$oExamXml = new ExamXml();
		
		$sql = "SELECT * FROM chart_optic WHERE form_id='".$form_id."' AND patient_id='".$patientId."'  AND purged = '0' ";
		$row = sqlQuery($sql);
		if($row!=false){
			//
			$logged_user_type = (isset($_SESSION["user_role"]) && !empty($_SESSION["user_role"])) ? $_SESSION["user_role"] : $_SESSION["logged_user_type"];
			
			$statusElem_optic = ""; //getStrSe(array("elem_chng_div6_Od"=>1,"elem_chng_div6_Os"=>1));
			if(!empty($cd_od) || strpos($row["statusElem"], "elem_chng_div6_Od=1")!==false){
				$statusElem_optic.="elem_chng_div6_Od=1,";
			}
			if(!empty($cd_os) || strpos($row["statusElem"], "elem_chng_div6_Os=1")!==false){
				$statusElem_optic.="elem_chng_div6_Os=1,";
			}					
			
			//$arCDInfo = $this->getSubExamInfo("CD");
			$arCDInfo_tmp = $oExamXml->getExamXmlFiles("Fundus", "CD");
			$arCDInfo["xmlFile"]["OD"] = $arCDInfo_tmp["od"];
			$arCDInfo["xmlFile"]["OS"] = $arCDInfo_tmp["os"];			
			
			//C:D ----------------------
				$strMenu_cdod = $row["cdr_od"];
				$menuName = "cdrOd";
				$menuFilePath = $arCDInfo["xmlFile"]["OD"];						
				//$elem_cdr_od = newXmlString($menuName,$strMenu_cdod,$menuFilePath);
				$elem_cdr_od = $oON->editXml_v2(array("xStr"=>$strMenu_cdod,"xFile"=>$menuFilePath,"arrVals"=>array("elem_cdValOd"=>$cd_od)));	
				$elem_cdr_od = addslashes($elem_cdr_od);
				
				$strMenu_cdos = $row["cdr_os"];
				$menuName = "cdrOs";
				$menuFilePath = $arCDInfo["xmlFile"]["OS"];
				//$elem_cdr_os = newXmlString($menuName,$strMenu_cdod,$menuFilePath);
				$elem_cdr_os = $oON->editXml_v2(array("xStr"=>$strMenu_cdos,"xFile"=>$menuFilePath,"arrVals"=>array("elem_cdValOs"=>$cd_os)));	
				$elem_cdr_os = addslashes($elem_cdr_os);	
			//C:D ----------------------
			
			$elem_cdr_od_summary = "";
			$elem_cdr_os_summary = "";
			$cdr_od = stripslashes($elem_cdr_od); //od
				
			$arrTemp = $oON->getExamSummary($cdr_od);					
			$elem_cdr_od_summary = $arrTemp["Summary"];
			$arrExmDone_od = $arrTemp["ExmDone"];
			$strExamsAllOd .= $oUserAp->refineByConsoleSymp("C:D",$arrExmDone_od,$elem_cdr_od_summary);					
			
			$cdr_os = stripslashes($elem_cdr_os); //os
			$arrTemp = $oON->getExamSummary($cdr_os);
			$elem_cdr_os_summary = $arrTemp["Summary"];
			$arrExmDone_os = $arrTemp["ExmDone"];
			$strExamsAllOs .= $oUserAp->refineByConsoleSymp("C:D",$arrExmDone_os,$elem_cdr_os_summary);	
			
			$modi_note_CDOd=$oWv->getModiNotes($row["cdr_od_summary"],0,$elem_cdr_od_summary,0,$row["uid"]);
			$modi_note_CDOs=$oWv->getModiNotes($row["cdr_os_summary"],0,$elem_cdr_os_summary,0,$row["uid"]);
			
			$last_opr_id = $oWv->get_last_opr_id($row['last_opr_id'],$row["uid"]);
			$seri_modi_note_CDArr = $oWv->getModiNotesArr($row["cdr_od_summary"],$elem_cdr_od_summary,$last_opr_id,'OD',$row["modi_note_CDArr"],$row['exam_date']);
			$seri_modi_note_CDArr = $oWv->getModiNotesArr($row["cdr_os_summary"],$elem_cdr_os_summary,$last_opr_id,'OS',$seri_modi_note_CDArr,$row['exam_date']);
			
			
			$sql = "UPDATE chart_optic SET 					
					exam_date='".date("Y-m-d H:i:s")."', uid='".$_SESSION["authId"]."' ";
			
			if($_REQUEST["elem_saveForm"] == "saveCDRV")	{
				$sql .= ", cd_val_od = '".sqlEscStr($cd_od)."',
						cd_val_os = '".sqlEscStr($cd_os)."',
						statusElem = '".sqlEscStr($statusElem_optic)."',
						cdr_od_summary = '".sqlEscStr($elem_cdr_od_summary)."',
						cdr_os_summary = '".sqlEscStr($elem_cdr_os_summary)."',
						cdr_od = '".sqlEscStr($cdr_od)."',
						cdr_os = '".sqlEscStr($cdr_os)."',
						last_opr_id = '".$_SESSION["authId"]."',
						modi_note_CDOd = '".sqlEscStr($modi_note_CDOd)."',
						modi_note_CDOs = '".sqlEscStr($modi_note_CDOs)."',
						modi_note_CDArr = '".sqlEscStr($seri_modi_note_CDArr)."',
						ut_elem = CONCAT(ut_elem,\"|".$logged_user_type."@elem_cdValOd,elem_cdValOs,|\")
					";		
			}
			
			$sql .=	"WHERE form_id='".$form_id."' AND patient_id='".$patientId."'  AND purged = '0' ";			
			$res = sqlQuery($sql);
		
		}
	}	
}
?>