<?php
/*
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
*/
?>
<?php
/*
File: AmslerGrid.php
Coded in PHP7
Purpose: This is a class file for Amsler grid test providing some operations like insert, previous values and other.
Access Type : Include file
*/
?>
<?php
//AmslerGrid.php
class AmslerGrid extends ChartNote{
	private $examName,$tbl,$xmlFileOd,$xmlFileOs,$divSE;
	public function __construct($pid,$fid=""){
		parent::__construct($pid,$fid);
		$this->tbl="amsler_grid";
		$this->examName="Amsler Grid";
	}

	function isRecordExists($a="",$b="",$c="",$d=""){
		return parent::isRecordExists($this->tbl);
	}

	function getRecord($sel=" * ",$a="",$b="",$c="",$d=""){
		return parent::getRecord($this->tbl,$sel);
	}

	function getLastRecord($sel=" * ",$LF="0",$dt="",$a="",$b="", $c="" ){
		return parent::getLastRecord($this->tbl,"form_id",$LF,$sel,$dt);
	}

	function insertNew(){
		if(!empty($this->pid) && !empty($this->fid)){
		$sql = "INSERT INTO ".$this->tbl." (id, form_id, patient_id, exam_date)
				VALUES (NULL, '".$this->fid."','".$this->pid."','".date("Y-m-d")."') ";
		$res=sqlInsert($sql);
		$return= $res;
		}else{ $return=0; }
		return $return;
	}

	function carryForward(){
		$res = $this->getLastRecord(" c2.id AS agId ","1");
		if($res!=false){
			$Id_LF = $res["agId"];
		}		
		//Insert
		$insertId = $this->insertNew();
		//CopyLF
		$ignoreFlds = "form_id,nochange,wnl_value";
		if(!empty($Id_LF)) $this->carryForwardExe($this->tbl,$insertId,$Id_LF,$ignoreFlds,$this->examName,'id');
	}
	
	function set2PrvVals(){
		if($this->isRecordExists()){
			$res1 = $this->getRecord(" id ");
			if($res1!=false){
				$Id = $res1["id"];
			}
			
			$res = $this->getLastRecord(" c2.id AS agId ","1");		
			if($res!=false){
				$Id_LF = $res["agId"];
			}

			//CopyLF
			$ignoreFlds = "form_id,nochange,wnl_value";
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
		if($this->isRecordExists()){
			$res1 = $this->getRecord(" id ");
			if($res1!=false){
				$Id = $res1["id"];
			}

			//CopyLF
			$ignoreFlds = "form_id,patient_id";
			if(!empty($Id)){
				$this->resetValsExe($this->tbl,$Id,$ignoreFlds,$this->examName,'id');
				$this->setStatus("",$this->tbl);
			}
		}else{
			//
			$this->insertNew();
		}	
	}
	
	//
	function isNoChanged(){
		$res= $this->getRecord("nochange");
		if($res!=false){
			if( !empty($res["nochange"]) ){
				return true;
			}		
		}
		return false;
	}
	
	function getAGSection($finalize_flag){		
		$bggrey=" bggrey "; //default bg color

		$patient_id = $this->pid;
		$form_id = $this->fid;
		
		if(empty($patient_id) || empty($form_id)){return "";}
		
		$elem_examDate = wv_formatDate(date('Y-m-d'));
		//Is Reviewable
		$isReviewable = $this->isChartReviewable($_SESSION["authId"]);
		$elem_per_vo =  ChartPtLock::is_viewonly_permission();
		
		//wnl
		$wnl_flag="";
		$wnl_flagOd=$wnl_flagOs=$elem_isPositive="0";
		
		//Obj
		//$oAG = new AmslerGrid($patient_id,$form_id);
		$oSaveFile = new SaveFile($patient_id);
		
		//DOS
		$elem_dos=$this->getDos(1);
		
		$sql = "SELECT * FROM amsler_grid WHERE patient_id = '".$patient_id."' AND form_id = '".$form_id."' ";
		$row = sqlQuery($sql);		
		if(($row == false)) /* Show Previous values in finalized chart also ~&& ($finalize_flag == 0)~ */
		{
			$amsler_mode = "new";
			$amsler_edid = "";
			//New Records
			//$row = valuesNewRecordsAmslar($patient_id);
			$res = $this->getLastRecord(" * ",0,$elem_dos);
			if($res!=false){$row=$res;}else{$row=false;}
		}else
		{
			$amsler_mode = "update";
			$amsler_edid = $row["id"];
			$bggrey="";
			if(isset($_GET["prevVal"]) && ($_GET["prevVal"] == 1))
			{
				//New Records
				//$row = valuesNewRecordsAmslar($patient_id, " * ", "1");
				$res = $this->getLastRecord(" * ",1,$elem_dos);
				if($res!=false){$row=$res;}else{$row=false;}
			}
		}
		
		if($row != false)
		{
			$elem_amslerOs = $row["amsler_os"];
			$elem_amslerOd = $row["amsler_od"];
			$elem_doctorSign = $row["doctor_sign"];
			$sel_facility_id = $row["facility_id"];	
			$elem_examDate = wv_formatDate(date('Y-m-d')); //$row["exam_date"];		
			$elem_doctorName = $row["doctor_name"];
			$chk_amslergrid_no_change = ($row["no_change"] == 1) ? "CHECKED" : "";
			$elem_noChange_amsler = $row["no_change"];
			$elem_notes = $row["notes"];
			$wnl_flag = $row["wnl_flag"];
			$elem_wnlOd = $row["wnl_flagOd"];
			$elem_wnlOs = $row["wnl_flagOs"];	
			
			$sig_path_od = (!empty($row["drwpth_od"])) ? $oSaveFile->getFilePath($row["drwpth_od"],"w") : "" ;
			$sig_path_os = (!empty($row["drwpth_os"])) ? $oSaveFile->getFilePath($row["drwpth_os"],"w") : "" ;
			
		}

		//Performed Id
		if(empty($elem_doctorName)){
			$elem_doctorName = $_SESSION["authId"];
		}
		//
		$oUser = new User($elem_doctorName);
		$elem_doctorName_show_amsler = $oUser->getName(1);
		$elem_doctorName_amsler = $_SESSION["authId"];
		
		
		$flg_src = ($wnl_flag == 'yes') ? 'pos_flg' : 'wnl_flg'; 
		$flg_dis = ($wnl_flag == 'yes' || ($elem_wnlOd == "1" && $elem_wnlOs == "1")) ? '' : 'hidden';
		
		//include --
		//ob_start();
		$tmp = str_replace("\x", "\\x", $GLOBALS['incdir']);		
		include($tmp."/chart_notes/view/amsler_grid.php");
		//$out2 = ob_get_contents();
		//ob_end_clean();
		//return $out2;
	}

	function getWorkViewSummery($post=array()){
		$echo="";		
		$patient_id = $this->pid;
		$form_id = $this->fid;
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
			$elem_dos=$this->getDos();
			$elem_dos=wv_formatDate($elem_dos,0,0,"insert");
			$res = $this->getLastRecord($tmp,0,$elem_dos);
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
		else if($wnl_flag == 'no' || ($wnl_flagOd == "1")) $var1= !empty($elem_wnl_value) ? $elem_wnl_value : $this->getExamWnlStr("Amsler Grid");
		else $var1= '';
		
		if($elem_amsler_os||!empty($drwpth_os))	$var2= 'ABN'; 
		else if($wnl_flag == 'no' || ($wnl_flagOs == "1")) $var2= !empty($elem_wnl_value) ? $elem_wnl_value : $this->getExamWnlStr("Amsler Grid");
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
		$strNC = (!empty($nochange)) ? "UNDO" : "NC";
		
		//app
		//app
		if(isset($post) && $post["webservice"] == "1"){	
			$tmp=array();
			$oSaveFile = new SaveFile($patient_id);
			$drwpth_od = $oSaveFile->getFilePath($drwpth_od, 'http');
			$drwpth_os = $oSaveFile->getFilePath($drwpth_os, 'http');	
			$tmp["Amsler Grid"] = array("show_label_od"=>$var1, "show_label_os"=>$var2, "pos"=>$pos, "wnlod"=>$wnl_flagOd, "wnlos"=>$wnl_flagOs, "draw_od"=>$drwpth_od, "draw_os"=>$drwpth_os, "desc" => $elem_notes, "exam_date"=>$exam_date);
			//$tmp["htm"] = $str;
			return serialize($tmp);
			
		}else{	
			$str = '';
			$str = "<div class=\"".$pos."\">
					<div class=\"header\">
						<div class=\"hdr form-inline\">
							<ul class=\"list-inline\">
								<li><h2 class=\"clickable\" onclick=\"openPW('AG')\" title=\"Amsler Grid\">AG</h2></li>
								<li ><input type=\"button\" id=\"elem_btnWNLAG\" class=\"wnl btn btn-xs\" value=\"WNL\" onClick=\"setWnlValues('AG')\"></li>
								<li><input type=\"button\" id=\"elem_btnNoChangeAG\" class=\"nc btn btn-xs\" value=\"".$strNC."\" onClick=\"autoSaveNoChange('AG')\"></li>
							</ul>
						</div>
					</div>
					<div class=\"clearfix\"></div>
					<div class=\"exampd default text-center\">".	
						"<ul class=\"list-unstyled list-inline ".$pos."\" onClick=\"openPW('AG')\" title=\"Amsler Grid\">".
						"<li id=\"amslerImgD\" class=\"text-center\">".						
						$var1_s.
						"</li>".
						"<li id=\"amslerImgD1\" class=\"text-center\">".	
						$var2_s.
						"</li>".
						"</ul>".
					"</div>							
				</div>";
			return $str;			
		}	
	}
	
	function save_wnl(){
		//$elem_noChangeAG = $_POST["elem_noChangeAG"];
		//include(dirname(__FILE__)."/common/AmslerGrid.php");
		$patientId = $this->pid;
		$form_id = $this->fid;
		
		$wnl_value=$wnl_phrase="";
		//Check and Add record
		//$oAG = new AmslerGrid($patientId,$form_id);
		if(!$this->isRecordExists()){	$this->carryForward();	 }
		
		$sql= "SELECT  wnl_flag,
					wnl_flagOd,
					wnl_flagOs,						
					amsler_os,
					amsler_od,
					wnl_value
			FROM amsler_grid 
			WHERE form_id='".$form_id."' AND patient_id='".$patientId."' ";
		$row=sqlQuery($sql);
		if($row!=false){
			
			$wnl_flag=$row["wnl_flag"];
			$wnl_flagOd=$row["wnl_flagOd"];
			$wnl_flagOs=$row["wnl_flagOs"];				
			$amsler_os=$this->isAppletDrawn($row["amsler_os"]);
			$amsler_od=$this->isAppletDrawn($row["amsler_od"]);				
			$wnl_value=$row["wnl_value"];
			
			//
			$pos="0";
			//check od
			if(!empty($amsler_od)){
				//$wnl_flagOd = "0";
				$pos="1";
			}
			
			//check os
			if(!empty($amsler_os)){
				//$wnl_flagOs = "0";
				$pos="1";
			}
			
			//WNL
			$wnl = (!empty($wnl_flagOd)&&!empty($wnl_flagOs)) ? "1" : "0";				
			
		}
		
		if($pos=="0"){//Do not Toggle od and os separate
			$owv = new WorkView();
			//Toggle Conj
			list($wnl_flagOd,$wnl_flagOs,$wnl) =
									$owv->toggleWNL($pos,$amsler_od,$amsler_os,
													$wnl_flagOd,$wnl_flagOs,$wnl,"OU");
			if(!empty($wnl_flagOd)&&!empty($wnl_flagOs)){
				$wnl_flag="no";
			}else if(empty($wnl_flagOd)&&empty($wnl_flagOs)){
				$wnl_flag="";
			}
		}
		
		if(empty($wnl_value)){
			$wnl_value=$this->getExamWnlStr("Amsler Grid");
			$wnl_phrase = ", wnl_value='".imw_real_escape_string($wnl_value)."' ";				
		}
		
		//
		$sql = "UPDATE amsler_grid SET 
				wnl_flag='".$wnl_flag."',
				wnl_flagOs='".$wnl_flagOs."', wnl_flagOd='".$wnl_flagOd."',
				exam_date ='".date("Y-m-d")."' ".$wnl_phrase."
				WHERE form_id='".$form_id."' AND patient_id='".$patientId."' ";
		
		$res = sqlQuery($sql);	
	}

	function save_no_change(){
		
		$patientId = $this->pid;
		$form_id = $this->fid;
		
		//Check and Add record		
		if(!$this->isRecordExists()){
			$this->carryForward();
			$elem_noChangeAG=1;
		}else if(!$this->isNoChanged()){
			$elem_noChangeAG=1;
		}else{
			$this->set2PrvVals();
			$elem_noChangeAG=0;
		}		
		
		//
		$sql = "UPDATE amsler_grid SET nochange='".$elem_noChangeAG."',
				exam_date ='".date("Y-m-d")."'
				WHERE form_id='".$form_id."' AND patient_id='".$patientId."' ";
		
		$res = sqlQuery($sql);
	}
	
	function save_form(){
		$patient_id = $patientId = $this->pid; //$_POST["elem_patientId"];
		$form_id = $formId = $this->fid; //$_POST["elem_formId"];
		//	
		if(empty($patientId) || empty($formId)){ return; }
		
		$oSaveFile = new SaveFile($patient_id);
		
		$amsler_os = $_POST["elem_amslerOs"];
		$amsler_od = $_POST["elem_amslerOd"];
		
		//--
		$sql_sig_path_od=$sql_sig_path_os=$sig_path_od=$sig_path_os="";		
		if(isset($_POST["sig_dataapp_ams_od"]) && !empty($_POST["sig_dataapp_ams_od"])){
			$sig_path_od = $oSaveFile->mkHx2Img($_POST["sig_dataapp_ams_od"],"AmslerGrid","OD");
			///$sql_sig_path_od =", drwpth_od='".$sig_path_od."'";	
		}
		if(isset($_POST["sig_dataapp_ams_os"]) && !empty($_POST["sig_dataapp_ams_os"])){
			$sig_path_os = $oSaveFile->mkHx2Img($_POST["sig_dataapp_ams_os"],"AmslerGrid","OS");
			//$sql_sig_path_os =", drwpth_os='".$sig_path_os."'";
		}
		//--
		
		$doctor_sign = $_POST["elem_doctorSign_amsler"];	
		$hd_amsler_mode = $_POST["hd_amsler_mode"];
		$id = $_POST["elem_amslerid"];
		$notes = sqlEscStr($_POST["elem_notes_ag"]);
		$doctor_name = $_POST["elem_doctorName_amsler"];
		$exam_date = getDateFormatDB($_POST["elem_examDate_amsler"]);
		$wnl_flag = $_POST['wnl_flag_amsler'];
		$wnl_flagOd = $_POST["elem_wnlOd_amsler"];
		$wnl_flagOs = $_POST["elem_wnlOs_amsler"];
		
		//check
		$cQry = "select id, drwpth_od,drwpth_os, wnl_value FROM amsler_grid WHERE form_id='".$form_id."' AND patient_id='".$patient_id."' ";
		$row = sqlQuery($cQry);
		$hd_amsler_mode = ($row == false) ? "new" : "1";
		
		if($hd_amsler_mode == "new"){
			$elem_wnl_value = $this->getExamWnlStr("Amsler Grid");
			$sql = "INSERT INTO amsler_grid ".
				 "( ".
				 "id, patient_id, amsler_os, amsler_od, doctor_name, doctor_sign, exam_date, ".
				 "form_id, notes, wnl_flag, wnl_flagOd, wnl_flagOs, drwpth_od, drwpth_os, wnl_value ".
				 ")".
				 "VALUES ".
				 "(".
				 "NULL, '".$patient_id."', '".$amsler_os."', '".$amsler_od."', '".$doctor_name."', '".$doctor_sign."', '".$exam_date."', ".
				 "'".$form_id."', '".$notes."', '".$wnl_flag."', '".$wnl_flagOd."', '".$wnl_flagOs."', '".$sig_path_od."', '".$sig_path_os."',  ".
				 "'".sqlEscStr($elem_wnl_value)."' ".
				 ")";
			$insertId = sqlInsert($sql);
			$op=1;
		}else{
				
			//
			$elem_wnl_value =$row["wnl_value"];
			
			//Unlink prev drawings			
			$oSaveFile->unlinkfile($row["drwpth_od"]);
			$oSaveFile->unlinkfile($row["drwpth_os"]);
		
			$sql = "UPDATE amsler_grid ".
				 "SET ".
				 "amsler_os='".$amsler_os."',".
				 "amsler_od='".$amsler_od."',".
				 "doctor_name='".$doctor_name."',".
				 "doctor_sign='".$doctor_sign."',".
				 "exam_date='".$exam_date."',".
				 "notes='".$notes."', ".
				 "wnl_flag='".$wnl_flag."', ".
				 "wnl_flagOd='".$wnl_flagOd."', ".
				 "wnl_flagOs='".$wnl_flagOs."', ".
				 "drwpth_od='".$sig_path_od."', ".
				 "drwpth_os='".$sig_path_os."' ".
				 "WHERE form_id='".$form_id."' AND patient_id='".$patient_id."' ";
			$res = sqlQuery($sql);
			$op=1;
		}
		
		// Make chart notes valid
		$this->makeChartNotesValid();
		echo "0";
	}
}

?>