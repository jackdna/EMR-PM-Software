<?php
/*
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
*/
?>
<?php
/*
File: EOM.php
Coded in PHP7
Purpose: This class provides EOM exam related basic functions.
Access Type : Include file
*/
?>
<?php
//EOM.php
class EOM extends ChartNote{
	private $examName,$tbl,$xmlFileOd,$xmlFileOs,$divSE;
	public function __construct($pid,$fid=""){
		parent::__construct($pid,$fid);
		$this->tbl="chart_eom";
		$this->examName="EOM";
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
		$sql = "INSERT INTO ".$this->tbl." (eom_id, form_id, patient_id, exam_date, uid)
				VALUES (NULL, '".$this->fid."','".$this->pid."','".date("Y-m-d H:i:s")."','".$this->uid."') ";
		$return=sqlInsert($sql);		
		}else{ $return=0; }
		return $return;
	}

	function carryForward(){
		$res = $this->getLastRecord(" c2.eom_id ","1");
		if($res!=false){
			$Id_LF = $res["eom_id"];
		}
		//Insert
		$insertId = $this->insertNew();
		//CopyLF
		$ignoreFlds = "form_id,exam_date,uid,statusElem,examined_no_change,examined_no_change2,examined_no_change3,".//ut_elem,
					"modi_note,modi_note_Draw,wnl_value";
		if(!empty($Id_LF)) $this->carryForwardExe($this->tbl,$insertId,$Id_LF,$ignoreFlds,"EOM",'eom_id');
	}
	
	function set2PrvVals(){
		if($this->isRecordExists()){
			$res1 = $this->getRecord(" eom_id ");
			if($res1!=false){
				$Id = $res1["eom_id"];
			}
			
			$res = $this->getLastRecord(" c2.eom_id ","1");		
			if($res!=false){
				$Id_LF = $res["eom_id"];
			}

			//CopyLF
			$ignoreFlds = "form_id,exam_date,uid,statusElem,examined_no_change,examined_no_change2,examined_no_change3,".//ut_elem,
						"modi_note,modi_note_Draw,wnl_value";
			if(!empty($Id_LF)&&!empty($Id)){
				$this->carryForwardExe($this->tbl,$Id,$Id_LF,$ignoreFlds,"EOM",'eom_id');
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
			$res1 = $this->getRecord(" eom_id,statusElem ");
			if($res1!=false){
				$Id = $res1["eom_id"];
				if($is_cryfd==0){$statusElem = $res1["statusElem"];}
			}

			//CopyLF
			$ignoreFlds = "form_id,exam_date,uid,patient_id,idoc_drawing_id";
			if(!empty($Id)){
				$this->resetValsExe($this->tbl,$Id,$ignoreFlds,"EOM",'eom_id');
				$this->setStatus($statusElem,$this->tbl);
			}
		//}else{
			//
		//	$this->insertNew();
		//}	
	}
	
	function isNoChanged(){
		$res= $this->getRecord("examined_no_change,examined_no_change2,examined_no_change3,statusElem");
		if($res!=false){
			if( !empty($res["statusElem"]) && strpos($res["statusElem"],"=1")!==false){
				return true;
			}
			if(!empty($res["examined_no_change"]) &&
			   !empty($res["examined_no_change2"]) &&
			   !empty($res["examined_no_change3"]) ){
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
		$modi_note="";	
		$flgCarry=0;
		
		//
		if(!$this->isRecordExists()){
			$this->carryForward();
			$flgCarry=1;
		}
		
		$cQry = "select eom_id,wnl,isPositive,wnl_2,isPositive_2,wnl_3,isPositive_3,statusElem,sumEom,uid, wnl_value 
					FROM chart_eom WHERE form_id='".$form_id."' AND patient_id='".$patientId."'  AND purged = '0' ";
		$row = sqlQuery($cQry);
		if($row == false){
			
		}else{
			
			$wnl = $row["wnl"];
			$pos = $row["isPositive"];

			$wnl_2 = $row["wnl_2"];
			$pos_2 = $row["isPositive_2"];

			$wnl_3 = $row["wnl_3"];
			$pos_3 = $row["isPositive_3"];
			
			$statusElem=trim($row["statusElem"]);
			
			$sumEom=$row["sumEom"];
			$uid=$row["uid"];				
			$wnl_value=$row["wnl_value"];
		}
		
		if((!empty($statusElem)&&strpos($statusElem,"0")===false)||(empty($wnl)&&empty($wnl_2)&&empty($wnl_3))){ 
			//Toggle
			if(empty($pos)){
				$elem_wnlEom = !empty($wnl) ? "0" : "1";
			}else if(!empty($pos)){
				$elem_wnlEom="0";
			}

			//Toggle
			if(empty($pos_2)){
				$elem_wnlEom_2 = !empty($wnl_2) ? "0" : "1";
			}else if(!empty($pos_2)){
				$elem_wnlEom_2="0";
			}
			
			/*
			//Toggle
			if(empty($pos_3)){
				$elem_wnlEom_3 = !empty($wnl_3) ? "0" : "1";
			}else if(!empty($pos_3)){
				$elem_wnlEom_3="0";
			}
			*/
		}else {
			$elem_wnlEom=$wnl;
			$elem_wnlEom_2=$wnl_2;
			$elem_wnlEom_3=$wnl_3;
		}
		
		//if Not carry			
		if($flgCarry==0){
			
			//Modifying Notes----------------
			$owv =  new WorkView();
			$modi_note=$owv->getModiNotes($sumEom,$wnl,$sumEom,$elem_wnlEom,$uid);				
			//Modifying Notes----------------			
		
		}			
		//End if Not carry
		
		//end Toggle
		
		//Status
		$statusElem_EOM_prev=$statusElem;
		$statusElem = $this->se_elemStatus("EOM",1,"","0","0",0);
		//Add Drawing Status if any
		if(strpos($statusElem_EOM_prev, "Eom3=1")!==false){	
			$statusElem .= "elem_chng_divEom3=1,";
		}
		//--	
		
		//getWnlValue
		if(empty($wnl_value)){
			$wnl_value=$this->getExamWnlStr("EOM");
			$wnl_phrase = ", wnl_value='".sqlEscStr($wnl_value)."' ";				
		}

		//Updates
		$sql = "UPDATE chart_eom SET wnl='".$elem_wnlEom."',wnl_2='".$elem_wnlEom_2."',wnl_3='".$elem_wnlEom_3."', 
				exam_date='".date("Y-m-d H:i:s")."', uid='".$_SESSION["authId"]."',statusElem='".$statusElem."', 
				modi_note = CONCAT('".sqlEscStr($modi_note)."',modi_note) ".$wnl_phrase."
				WHERE form_id='".$form_id."' AND patient_id='".$patientId."'  AND purged = '0' ";
		$res = sqlQuery($sql);		
	}
	function save_no_change(){
		$form_id = $this->fid;
		$patientId = $this->pid;
		$w = strtoupper($_POST["w"]);
		$exmEye = $_POST["elem_exmEye"];
		
		$oEOM=$this;
		if(!$oEOM->isRecordExists()){
			$oEOM->carryForward();
			$elem_noChangeEom=1;
		}else if(!$oEOM->isNoChanged()){
			$elem_noChangeEom=1;
		}else{
			$oEOM->set2PrvVals();
			$elem_noChangeEom=0;
		}
		
		//Get status string --
		$statusElem="";
		if($elem_noChangeEom==1)$statusElem=$this->se_elemStatus($w,"1","","1","1",0);
		//Get status--
		
		//updates
		$sql = "UPDATE chart_eom SET examined_no_change='".$elem_noChangeEom."',
				examined_no_change2='".$elem_noChangeEom."',examined_no_change3='0',
				exam_date='".date("Y-m-d H:i:s")."', uid='".$_SESSION["authId"]."',
				statusElem='".$statusElem."'
				WHERE form_id='".$form_id."' AND patient_id='".$patientId."'  AND purged = '0' ";
		$res = sqlQuery($sql);		
	}
}

?>