<?php
/*
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
*/
?>
<?php
/*
File: Gonio.php
Coded in PHP7
Purpose: This class provides Gonio exam related functions.
Access Type : Include file
*/
?>
<?php
//Gonio.php
class Gonio extends ChartNote{
	private $examName,$tbl,$xmlFileOd,$xmlFileOs;
	public function __construct($pid,$fid=""){
		parent::__construct($pid,$fid);
		$this->tbl="chart_gonio";
		//$this->xmlFileOd=$GLOBALS['incdir']."/chart_notes/xml/iopGon_od.xml";
		//$this->xmlFileOs=$GLOBALS['incdir']."/chart_notes/xml/iopGon_os.xml";
		$oExamXml = new ExamXml();
		$tmp = $oExamXml->getExamXmlFiles("Gonio");
		$this->xmlFileOd = $tmp["od"];
		$this->xmlFileOs = $tmp["os"];
		
		$this->examName="Gonio";
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
		$wnl_value = $this->getExamWnlStr($this->examName);
		$sql = "INSERT INTO ".$this->tbl." (gonio_id, form_id, patient_id, examDateGonio, uid, wnl_value)
				VALUES (NULL, '".$this->fid."','".$this->pid."','".date("Y-m-d H:i:s")."','".$this->uid."', '".$wnl_value."') ";
		$return=sqlInsert($sql);
		
		}else{ $return=0; }
		return $return;
	}

	function carryForward(){
		$res = $this->getLastRecord(" c2.gonio_id ","1");
		if($res!=false){
			$Id_LF = $res["gonio_id"];
		}
		//Insert
		$insertId = $this->insertNew();
		//CopyLF
		$ignoreFlds = "form_id,examDateGonio,uid,statusElem,examined_no_change, noChange_drawing,".
					"ncGonio_od,ncGonio_os,".
					"ncDraw_od,ncDraw_os,".
					//"ut_elem,".
					"modi_note_GonioOd,modi_note_GonioOs,modi_note_Draw,wnl_value";
		if(!empty($Id_LF)) $this->carryForwardExe($this->tbl,$insertId,$Id_LF,$ignoreFlds,'IOP_GONIO','gonio_id');
	}

	function smartChart($arr){

		if(!$this->isRecordExists()){
			//In
			$this->carryForward();
		}

		$res=$this->getRecord();
		if($res!=false){
			$xmlOd = $res["iopGon_od"];
			$xmlOs = $res["iopGon_os"];
			$statusElem = $res["statusElem"];
			$wnl = $res["wnl"];
			$wnlGonio = $res["wnlGonio"];
			$wnlOd=$res["wnlOd"];
			$wnlOs=$res["wnlOs"];
			$posGonio=$res["posGonio"];
			$pos=$res["isPositive"];
			$ut_elem=$res["ut_elem"];
		}else{
			$desc = "";
		}

		//
		$statusElemCur=$this->getCurStatusFromPost($arr, "elem_chng_divIop_Od", "elem_chng_divIop_Os", $statusElem);		

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
		list($wnlGonio,$wnlOd,$wnlOs,$pos,$wnl,$posGonio) = $this->setExmWnlPos($sumOd,$sumOs,$wnlGonio,$wnlOd,$wnlOs,$pos,$wnl,$posGonio);
		//Set WNL --

		//Set Status Fields--
		if(!empty($statusElemCur)){ 
			$statusElem=$statusElemCur;			
		}else{		
			$statusElem = $this->setStatusElem("Iop",$statusElem,$sumOd,$sumOs,$siteExm);
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
			"examDateGonio = '".date("Y-m-d H:i:s")."', ".
			"wnl = '".$wnl."', ".
			"iopGon_od  = '".sqlEscStr($xmlOd)."', ".
			"iopGon_os = '".sqlEscStr($xmlOs)."', ".
			"isPositive = '".$pos."', ".
			"gonio_od_summary = '".sqlEscStr($sumOd)."', ".
			"gonio_os_summary = '".sqlEscStr($sumOs)."', ".
			"wnlOd='".$wnlOd."', ".
			"wnlOs='".$wnlOs."', ".
			"wnlGonio='".$wnlGonio."', ".
			"posGonio='".$posGonio."', ".
			"desc_ig = '".sqlEscStr($desc)."', ".
			"uid = '".$this->uid."', ".
			"statusElem = '".sqlEscStr($statusElem)."', ".
			"ut_elem = '".sqlEscStr($ut_elem)."' ".
			"WHERE form_id = '".$this->fid."' AND patient_id = '".$this->pid."' ";
		$res=sqlQuery($sql);
	}

	function set2PrvVals(){
		if($this->isRecordExists()){
			$res1 = $this->getRecord(" gonio_id ");
			if($res1!=false){
				$gonioId = $res1["gonio_id"];
			}
			
			$res = $this->getLastRecord(" c2.gonio_id ","1");		
			if($res!=false){
				$gonioId_LF = $res["gonio_id"];
			}

			//CopyLF
			$ignoreFlds = "form_id,examDateGonio,uid,statusElem,examined_no_change, noChange_drawing,".
						"ncGonio_od,ncGonio_os,".
						"ncDraw_od,ncDraw_os,".
						//"ut_elem,".
						"modi_note_GonioOd,modi_note_GonioOs,modi_note_Draw,wnl_value";
			if(!empty($gonioId_LF)&&!empty($gonioId)){
				$this->carryForwardExe($this->tbl,$gonioId,$gonioId_LF,$ignoreFlds,'IOP_GONIO','gonio_id');
				$this->setStatus("",$this->tbl);
			}else if(!empty($gonioId)){ //when no previous exam
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
			$res1 = $this->getRecord(" gonio_id, statusElem ");
			if($res1!=false){
				$Id = $res1["gonio_id"];
				if($is_cryfd==0){$statusElem = $res1["statusElem"];}
			}
			//CopyLF
			$ignoreFlds = "form_id,examDateGonio,uid,patient_id,idoc_drawing_id,";
			if(!empty($Id)){
			
				if(empty($_POST["site"]) || $_POST["site"]=="OU"){ $statusElem = "";  }
				if($_POST["site"]=="OS"){ 
					$ignoreFlds .= "gonio_od,gonio_od_summary,gonio_od_drawing,gonio_od_desc,iopGon_od,wnlDrawOd,wnlOd,ncGonio_od,ncDraw_od,modi_note_GonioOd,"; 
					if(!empty($statusElem)){ $statusElem = str_replace("_Os=1","_Os=0",$statusElem);  }			
				}else if($_POST["site"]=="OD"){ 
					$ignoreFlds .= "gonio_os,gonio_os_summary,gonio_os_drawing,gonio_os_desc,iopGon_os,wnlDrawOs,wnlOs,ncGonio_os,ncDraw_os,modi_note_GonioOs,"; 
					if(!empty($statusElem)){ $statusElem = str_replace("_Od=1","_Od=0",$statusElem);  }				
				}
			
				if($_POST["site"]=="OD" || $_POST["site"]=="OS"){
					$ignoreFlds .= "isPositive,posGonio,posDraw,";
					if($is_cryfd==0){	$ignoreFlds .= "wnl_value,ut_elem,";}
				}
				$ignoreFlds = trim($ignoreFlds,",");
			
				$this->resetValsExe($this->tbl,$Id,$ignoreFlds,'IOP_GONIO','gonio_id');
				$this->setStatus($statusElem,$this->tbl);
			}
		//}else{
			//
		//	$this->insertNew();
		//}	
	}
	
	function isNoChanged(){
		$res= $this->getRecord("examined_no_change, noChange_drawing,statusElem");
		if($res!=false){
			if( !empty($res["statusElem"]) && strpos($res["statusElem"],"=1")!==false){
				return true;
			}
			if( !empty($res["examined_no_change"]) &&
			    !empty($res["noChange_drawing"])){
				return true;
			}		
		}
		return false;
	}
	
	function save_no_change(){
		$form_id = $this->fid;
		$patientId = $this->pid;
		$w = strtoupper($_POST["w"]);
		$exmEye = $_POST["elem_exmEye"];
		
		$tmpNC=0;
		$elem_noChangeIop = $elem_noChangeGonio =  $elem_noChangeDraw =  0;
		$elem_noChangeIop_od=$elem_wnlDraw_od= 0;
		$elem_noChangeIop_os=$elem_wnlDraw_os= 0;
		
		$oGonio= $this; // new Gonio($patientId,$form_id);
		if(!$oGonio->isRecordExists()){
			$oGonio->carryForward();
			$tmpNC=1;
		}else if(!$oGonio->isNoChanged()){
			$tmpNC=1;
		}else{
			$oGonio->set2PrvVals();
			$tmpNC=0;
		}
		
		//--
		//Set NC
		if($tmpNC==1){
			if($exmEye=="OU"){
				//$elem_noChangeIop=$elem_noChangeDraw=$elem_wnlDraw_od=$elem_wnlDraw_os=1;
				$elem_noChangeGonio=1;
				$elem_noChangeIop_od=1;
				$elem_noChangeIop_os=1;
			}else if($exmEye=="OD"){
				//$elem_wnlDraw_od=1;
				$elem_noChangeIop_od=1;
			}else if($exmEye=="OS"){
				//$elem_wnlDraw_os=1;
				$elem_noChangeIop_os=1;
			}
		}
		//--
		
		//Get status string --
		$statusElem="";
		if($elem_noChangeIop_od==1||$elem_noChangeIop_os==1) {$statusElem=$this->se_elemStatus($w,"1","",$elem_noChangeIop_od,$elem_noChangeIop_os,0);}
		//Get status--
		
		$sql = "UPDATE chart_gonio SET examined_no_change='".$elem_noChangeGonio."', noChange_drawing='".$elem_noChangeDraw."',
					ncGonio_od='".$elem_noChangeIop_od."',ncGonio_os='".$elem_noChangeIop_os."',
					ncDraw_od='".$elem_wnlDraw_od."', ncDraw_os='".$elem_wnlDraw_os."',
					examDateGonio='".date("Y-m-d H:i:s")."', uid='".$_SESSION["authId"]."',
					statusElem='".$statusElem."'
					WHERE form_id='".$form_id."' AND patient_id='".$patientId."' AND purged='0' ";
		$res = sqlQuery($sql);
		
	}
	
	
	
}
?>