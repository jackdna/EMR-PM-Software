<?php
/*
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
*/
?>
<?php
/*
File: ChartTestPrev.php
Coded in PHP7
Purpose: This class provides functions for previous test pop up in tests.
Access Type : Include file
*/
?>
<?php

class ChartTestPrev{
	private $db;
	private $pId;		
	private $TN;

	public function __construct($pid,$tn="",$templateId=""){
		$this->db = $GLOBALS["adodb"]["db"];		
		$this->pId = $pid;
		$this->TN = $tn;
		$this->TemplateID = $templateId;
	}
	
	private function getQry($t, $dirs,$tid,$dt,$dc=1,$cedt="",$ctid="",$sel="*"){
		//echo "<br/>\n $t, $dirs,$tid,$dt,$cedt,$ctid <br/>";
		$extra_query1 = "";
		switch($this->TN){
			case "VF":
				$pkid="vf_id";
				$exdt="examDate";
				$tbl="vf";
				$ptId="patientId";
			break;
			case "VF-GL":
				$pkid="vf_gl_id";
				$exdt="examDate";
				$tbl="vf_gl";
				$ptId="patientId";
			break;
			case "HRT":
				$pkid="nfa_id";
				$exdt="examDate";
				$tbl="nfa";
				$ptId="patient_id";
			break;
			case "OCT":
				$pkid="oct_id";
				$exdt="examDate";
				$tbl="oct";
				$ptId="patient_id";
			break;
			case "OCT-RNFL":
				$pkid="oct_rnfl_id";
				$exdt="examDate";
				$tbl="oct_rnfl";
				$ptId="patient_id";
			break;
			case "GDX":
				$pkid="gdx_id";
				$exdt="examDate";
				$tbl="test_gdx";
				$ptId="patient_id";
			break;
			case "PACHY":
				$pkid="pachy_id";
				$exdt="examDate";
				$tbl="pachy";
				$ptId="patientId";
			break;
			case "IVFA":
				$pkid="vf_id";
				$exdt="exam_date";
				$tbl="ivfa";
				$ptId="patient_id";
			break;
			case "ICG":
				$pkid="icg_id";
				$exdt="exam_date";
				$tbl="icg";
				$ptId="patient_id";
			break;

			case "FUNDUS":
				$pkid="disc_id";
				$exdt="examDate";
				$tbl="disc";
				$ptId="patientId";
			break;
			case "EXTERNAL/ANTERIOR":
				$pkid="disc_id";
				$exdt="examDate";
				$tbl="disc_external";
				$ptId="patientId";
			break;
			case "TOPOGRAPHY":
				$pkid="topo_id";
				$exdt="examDate";
				$tbl="topography";
				$ptId="patientId";
			break;
			case "CELL COUNT":
				$pkid="test_cellcnt_id";
				$exdt="examDate";
				$tbl="test_cellcnt";
				$ptId="patientId";
			break;
			case "OPTHALMOSCOPY":
				$pkid="";
				$exdt="examDate";
				$tbl="";
				$ptId="patientId";
			break;	
			case "A/SCAN":
				$pkid="surgical_id";
				$exdt="examDate";
				$tbl="surgical_tbl";
				$ptId="patient_id";
			break;
			case "IOL_Master":
				$pkid="iol_master_id";
				$exdt="examDate";
				$tbl="iol_master_tbl";
				$ptId="patient_id";
			break;
			case "B-SCAN":
				$pkid="test_bscan_id";
				$exdt="examDate";
				$tbl="test_bscan";
				$ptId="patientId";
			break;
			case "LABS":
				$pkid="test_labs_id";
				$exdt="examDate";
				$tbl="test_labs";
				$ptId="patientId";
			break;
			case "OTHER":
				$pkid="test_other_id";
				$exdt="examDate";
				$tbl="test_other";
				$ptId="patientId";
				$extra_query1 = " AND test_template_id = '0' ";
			break;
			case "TEMPLATETESTS":
				$pkid="test_other_id";
				$exdt="examDate";
				$tbl="test_other";
				$ptId="patientId";
				$extra_query1 = " AND test_template_id = '".$this->TemplateID."' ";
			break;
			case "CUSTOM":
				$pkid="test_id";
				$exdt="examDate";
				$tbl="test_custom_patient";
				$ptId="patientId";
                $extra_query1 = " AND test_template_id = '".$this->TemplateID."' ";
			break;
		}
		
		if($sel == "*"){
			$sel = " ".$tbl.".".$sel." ";
		}else if($sel == "PKID"){ //Sel PK id--
			$sel = $pkid." AS pk_id ";
		}		
		
		if($t==1){
			if($dc == 0){
				$sql = "SELECT DATE_FORMAT(".$exdt.",'%m-%d-%Y') as exdt2, ".$sel." FROM ".$tbl." ".
					   "WHERE ".$pkid."='".$tid."' ".
					   "AND ".$ptId." = '".$this->pId."' ".	
					   $extra_query1.					   
					   "LIMIT 0,1 ";
			}else{
				
				$dirs = ($dirs=="Next") ? ">" : "<"; 
				$sort = ($dirs==">") ? "ASC" : "DESC";
				$strEq="";

				//Clause TID --				
				if($tid>0){
					$sql = "SELECT count(*) AS num FROM ".$tbl." ".
						   "WHERE ".$ptId." = '".$this->pId."' ".
						   "AND ".$exdt." = '".$dt."' ".
						   "AND purged = '0' AND del_status='0'  ".$extra_query1;
					$res = imw_query($sql);
					if($res != false && imw_num_rows($res) > 1){
						$phrseTid = "AND ".$pkid." ".$dirs." '".$tid."' " ;
						$strEq = "=";
					}
				}
				//Clause TID --
								
				$sql = "SELECT DATE_FORMAT(".$exdt.",'%m-%d-%Y') as exdt2, ".$sel." FROM ".$tbl." ".
					   "WHERE ".$exdt." ".$dirs.$strEq." '".$dt."' ".
					   $phrseTid.
					   "AND ".$ptId." = '".$this->pId."' ".
					   "AND purged = '0' AND del_status='0'  ".$extra_query1.
					   "ORDER BY ".$exdt." ".$sort.",  ".$pkid." ".$sort." ".
					   "LIMIT 0,1 ";
				//echo $sql;
			}
		}else if($t==2){
			/*
			$sql = "SELECT * FROM ".$tbl." ".			   
				   "WHERE ".$ptId." = '".$this->pId."' ".
				   "AND ".$exdt." <= '".$cedt."' AND ".$pkid." < '".$ctid."' ".
				   "ORDER BY ".$exdt." DESC, ".$pkid." DESC ".
				   "LIMIT 0,1 ";
			*/
			
			$sql = "SELECT ".$exdt." AS exdt, ".$pkid." AS exid FROM ".$tbl." ".
				   "WHERE ".$ptId." = '".$this->pId."' ".
				   "AND purged = '0' AND del_status='0'  ".$extra_query1.
				   "ORDER BY ".$exdt." DESC, ".$pkid." DESC ".
				   " ";
		
		}else if($t==3){
			/*
			$sql = "SELECT * FROM ".$tbl." ".			   
				   "WHERE ".$ptId." = '".$this->pId."' ".
				   "AND ".$exdt." >= '".$cedt."' AND ".$pkid." > '".$ctid."' ".
				   "ORDER BY ".$exdt." DESC, ".$pkid." DESC ".
				   "LIMIT 0,1 ";
			*/	
		}
		return $sql;
	}
	
	private function getJson($arr,$ar2,$ctid,$cedt){
		$arret = array();		
		$ar3 = $ar4 = array();
		//paging --
		if($ctid!="" && $cedt!=""){
			/*
			//prev
			$sql= $this->getQry(2, "","","",$cedt,$ctid);			
			//echo  "$sql<br/>";
			
			$res = $this->db->Execute($sql);		
			if($res != false){
				while(!$res->EOF){
					$ar3["id"]=$ctid;
					$ar3["dt"]=$cedt;
					$ar3["tm"]=$cetm;
					$res->MoveNext();	
				}
			}
		
			//next			
			$sql= $this->getQry(3, "","","",$cedt,$ctid);			
			$res = $this->db->Execute($sql);
			if($res != false){
				while(!$res->EOF){
					$ar4["id"]=$ctid;
					$ar4["dt"]=$cedt;
					$ar4["tm"]=$cetm;
					$res->MoveNext();	
				}
			}
			*/
			
			//Paging
			$sql= $this->getQry(2,"","","");			
			$res = imw_query($sql);
			if($res != false){
				while($rs = imw_fetch_assoc($res)){
					$exdt = $rs["exdt"]; 
					$exid = $rs["exid"]; 				
					
					/*
					echo "<br/>$exdt,$exid,$cedt,$ctid,".strtotime($exdt).", ".strtotime($cedt)." <br/>";
					
					if($exdt<$cedt){
						//echo "<br/>".$exdt." is smaller than ".$cedt;
					}else if($exdt>$cedt){
						//echo "<br/>".$exdt." is Bigger than ".$cedt;
					}
					*/
					
					
					if(!isset($ar3["id"]) || empty($ar3["id"])){
						if($exdt<$cedt || ($cedt==$exdt && $exid<$ctid)){
							$ar3["id"]=$exid;
							$ar3["dt"]=$exdt;
							$ar3["tm"]=$cetm;
						}				
					}
					
					if($exdt>$cedt || ($cedt==$exdt && $exid>$ctid)){
						$ar4["id"]=$exid;
						$ar4["dt"]=$exdt;
						$ar4["tm"]=$cetm;
					}
				}
			}
		}
		//paging --
		//JSON
		$arret["dt"] = $arr;
		$arret["hm"] = $ar2;
		$arret["prv"] = $ar3;
		$arret["nxt"] = $ar4;
		return json_encode($arret);
	}
	
	private function getVF($dt,$tm="",$tid,$dir="Prev",$dc=1){ //		
		$arr = $ar2 = array();
		$ctid=$cedt=$cetm="";
		$ctid = -1;
		$cedt = "0000-00-00";
		$arr["Date1"] = $dt;

		$sql = $this->getQry(1, $dir,$tid, $dt,$dc);
	//	echo $sql."<br/>";		
		$res = imw_query($sql);
		if($res && imw_num_rows($res)>0){
			while($rs = imw_fetch_assoc($res)){
				$ctid = $rs["vf_id"];
				$cedt = $arr["elem_examDate"] = $rs["examDate"];
				//$cetm = $arr["elem_examTime"] = $rs["examTime"];	
				
				$arr2["elem_gla_mac"] = $rs["elem_gla_mac"];
				
				//Tech Comments
				$ar2["Tech.Comments"]= $arr["techComment"] = $rs["techComments"];			
				
				//Date
				$arr["Date1"] = $rs["exdt2"];
				//Normal --
				$arr["Normal_OD_T"] = $rs["Normal_OD_T"];		
				$arr["Normal_OS_T"] = $rs["Normal_OS_T"];		
				$arr["elem_normal_poorStudy_od"] = $rs["Normal_OD_PoorStudy"];
				$arr["elem_normal_poorStudy_os"] = $rs["Normal_OS_PoorStudy"];
				
				$td = $ts = "";
				if(!empty($arr["Normal_OD_T"])) $td .= "T ";
				if(!empty($arr["elem_normal_poorStudy_od"])) $td .= "Poor Study ";
				if(!empty($arr["Normal_OD_T"])) $ts .= "T ";
				if(!empty($arr["elem_normal_poorStudy_od"])) $ts .= "Poor Study ";
				$ar2["Normal"]=array("OD"=>$td,"OS"=>$ts);
				//Normal --
				
				//Border Line Defect --
				$arr["BorderLineDefect_OD_T"] = $rs["BorderLineDefect_OD_T"];
				$arr["BorderLineDefect_OD_1"] = $rs["BorderLineDefect_OD_1"];
				$arr["BorderLineDefect_OD_2"] = $rs["BorderLineDefect_OD_2"];
				$arr["BorderLineDefect_OD_3"] = $rs["BorderLineDefect_OD_3"];
				$arr["BorderLineDefect_OD_4"] = $rs["BorderLineDefect_OD_4"];

				$arr["BorderLineDefect_OS_T"] = $rs["BorderLineDefect_OS_T"];
				$arr["BorderLineDefect_OS_1"] = $rs["BorderLineDefect_OS_1"];
				$arr["BorderLineDefect_OS_2"] = $rs["BorderLineDefect_OS_2"];
				$arr["BorderLineDefect_OS_3"] = $rs["BorderLineDefect_OS_3"];
				$arr["BorderLineDefect_OS_4"] = $rs["BorderLineDefect_OS_4"];

				$td = $ts = "";
				if(!empty($arr["BorderLineDefect_OD_T"])) $td .= "T ";
				if(!empty($arr["BorderLineDefect_OS_T"])) $ts .= "T ";
				for($i=1;$i<=4;$i++){
					if(!empty($arr["BorderLineDefect_OD_".$i])) $td .= "+".$i." ";
					if(!empty($arr["BorderLineDefect_OS_".$i])) $ts .= "+".$i." ";
				}
				$ar2["Border Line Defect"]=array("OD"=>$td,"OS"=>$ts);
				//Border Line Defect --

				//Abnormal --
				$arr["Abnormal_OD_T"] = $rs["Abnormal_OD_T"];
				$arr["Abnormal_OD_1"] = $rs["Abnormal_OD_1"];
				$arr["Abnormal_OD_2"] = $rs["Abnormal_OD_2"];
				$arr["Abnormal_OD_3"] = $rs["Abnormal_OD_3"];
				$arr["Abnormal_OD_4"] = $rs["Abnormal_OD_4"];
				$arr["Abnormal_OS_T"] = $rs["Abnormal_OS_T"];
				$arr["Abnormal_OS_1"] = $rs["Abnormal_OS_1"];
				$arr["Abnormal_OS_2"] = $rs["Abnormal_OS_2"];
				$arr["Abnormal_OS_3"] = $rs["Abnormal_OS_3"];
				$arr["Abnormal_OS_4"] = $rs["Abnormal_OS_4"];
				
				$td = $ts = "";
				if(!empty($arr["Abnormal_OD_T"])) $td .= "T ";
				if(!empty($arr["Abnormal_OS_T"])) $ts .= "T ";
				for($i=1;$i<=4;$i++){
					if(!empty($arr["Abnormal_OD_".$i])) $td .= "+".$i." ";
					if(!empty($arr["Abnormal_OS_".$i])) $ts .= "+".$i." ";
				}
				$ar2["Abnormal"]=array("OD"=>$td,"OS"=>$ts);
				//Abnormal --
				
				//Nasal Step --
				$arr["NasalSteep_OD_Superior"] = $rs["NasalSteep_OD_Superior"];
				$arr["NasalSteep_OD_Inferior"] = $rs["NasalSteep_OD_Inferior"];

				$arr["NasalSteep_OD_S_T"] = $rs["NasalSteep_OD_S_T"];
				$arr["NasalSteep_OD_S_1"] = $rs["NasalSteep_OD_S_1"];
				$arr["NasalSteep_OD_S_2"] = $rs["NasalSteep_OD_S_2"];
				$arr["NasalSteep_OD_S_3"] = $rs["NasalSteep_OD_S_3"];
				$arr["NasalSteep_OD_S_4"] = $rs["NasalSteep_OD_S_4"];
				$arr["NasalSteep_OD_I_T"] = $rs["NasalSteep_OD_I_T"];
				$arr["NasalSteep_OD_I_1"] = $rs["NasalSteep_OD_I_1"];
				$arr["NasalSteep_OD_I_2"] = $rs["NasalSteep_OD_I_2"];
				$arr["NasalSteep_OD_I_3"] = $rs["NasalSteep_OD_I_3"];
				$arr["NasalSteep_OD_I_4"] = $rs["NasalSteep_OD_I_4"];

				$arr["NasalSteep_OS_Superior"] = $rs["NasalSteep_OS_Superior"];
				$arr["NasalSteep_OS_Inferior"] = $rs["NasalSteep_OS_Inferior"];
				$arr["NasalSteep_OS_S_T"] = $rs["NasalSteep_OS_S_T"];
				$arr["NasalSteep_OS_S_1"] = $rs["NasalSteep_OS_S_1"];
				$arr["NasalSteep_OS_S_2"] = $rs["NasalSteep_OS_S_2"];
				$arr["NasalSteep_OS_S_3"] = $rs["NasalSteep_OS_S_3"];
				$arr["NasalSteep_OS_S_4"] = $rs["NasalSteep_OS_S_4"];
				$arr["NasalSteep_OS_I_T"] = $rs["NasalSteep_OS_I_T"];
				$arr["NasalSteep_OS_I_1"] = $rs["NasalSteep_OS_I_1"];
				$arr["NasalSteep_OS_I_2"] = $rs["NasalSteep_OS_I_2"];
				$arr["NasalSteep_OS_I_3"] = $rs["NasalSteep_OS_I_3"];
				$arr["NasalSteep_OS_I_4"] = $rs["NasalSteep_OS_I_4"];

				$td = $ts = "";
				if(!empty($arr["NasalSteep_OD_Superior"])) $td .= "Superior ";
				if(!empty($arr["NasalSteep_OS_Superior"])) $ts .= "Superior ";
				if(!empty($arr["NasalSteep_OD_S_T"])) $td .= "T ";
				if(!empty($arr["NasalSteep_OS_S_T"])) $ts .= "T ";
				for($i=1;$i<=4;$i++){
					if(!empty($arr["NasalSteep_OD_S_".$i])) $td .= "+".$i." ";
					if(!empty($arr["NasalSteep_OS_S_".$i])) $ts .= "+".$i." ";
				}
				$td .= "<br/>";
				$ts .= "<br/>";	
				if(!empty($arr["NasalSteep_OD_Inferior"])) $td .= "Inferior ";
				if(!empty($arr["NasalSteep_OS_Inferior"])) $ts .= "Inferior ";
				if(!empty($arr["NasalSteep_OD_I_T"])) $td .= "T ";
				if(!empty($arr["NasalSteep_OS_I_T"])) $ts .= "T ";
				for($i=1;$i<=4;$i++){
					if(!empty($arr["NasalSteep_OD_I_".$i])) $td .= "+".$i." ";
					if(!empty($arr["NasalSteep_OS_I_".$i])) $ts .= "+".$i." ";
				}

				$ar2["Nasal Step"]=array("OD"=>$td,"OS"=>$ts);
				//Nasal Step --
				
				//Accurate Defect --
				$arr["Arcuatedefect_OD_Superior"] = $rs["Arcuatedefect_OD_Superior"];
				$arr["Arcuatedefect_OD_Inferior"] = $rs["Arcuatedefect_OD_Inferior"];
				$arr["Arcuatedefect_OD_S_T"] = $rs["Arcuatedefect_OD_S_T"];
				$arr["Arcuatedefect_OD_S_1"] = $rs["Arcuatedefect_OD_S_1"];
				$arr["Arcuatedefect_OD_S_2"] = $rs["Arcuatedefect_OD_S_2"];
				$arr["Arcuatedefect_OD_S_3"] = $rs["Arcuatedefect_OD_S_3"];
				$arr["Arcuatedefect_OD_S_4"] = $rs["Arcuatedefect_OD_S_4"];
				$arr["Arcuatedefect_OD_I_T"] = $rs["Arcuatedefect_OD_I_T"];
				$arr["Arcuatedefect_OD_I_1"] = $rs["Arcuatedefect_OD_I_1"];
				$arr["Arcuatedefect_OD_I_2"] = $rs["Arcuatedefect_OD_I_2"];
				$arr["Arcuatedefect_OD_I_3"] = $rs["Arcuatedefect_OD_I_3"];
				$arr["Arcuatedefect_OD_I_4"] = $rs["Arcuatedefect_OD_I_4"];

				$arr["Arcuatedefect_OS_Superior"] = $rs["Arcuatedefect_OS_Superior"];
				$arr["Arcuatedefect_OS_Inferior"] = $rs["Arcuatedefect_OS_Inferior"];
				$arr["Arcuatedefect_OS_S_T"] = $rs["Arcuatedefect_OS_S_T"];
				$arr["Arcuatedefect_OS_S_1"] = $rs["Arcuatedefect_OS_S_1"];
				$arr["Arcuatedefect_OS_S_2"] = $rs["Arcuatedefect_OS_S_2"];
				$arr["Arcuatedefect_OS_S_3"] = $rs["Arcuatedefect_OS_S_3"];
				$arr["Arcuatedefect_OS_S_4"] = $rs["Arcuatedefect_OS_S_4"];
				$arr["Arcuatedefect_OS_I_T"] = $rs["Arcuatedefect_OS_I_T"];
				$arr["Arcuatedefect_OS_I_1"] = $rs["Arcuatedefect_OS_I_1"];
				$arr["Arcuatedefect_OS_I_2"] = $rs["Arcuatedefect_OS_I_2"];
				$arr["Arcuatedefect_OS_I_3"] = $rs["Arcuatedefect_OS_I_3"];
				$arr["Arcuatedefect_OS_I_4"] = $rs["Arcuatedefect_OS_I_4"];

				$td = $ts = "";
				if(!empty($arr["Arcuatedefect_OD_Superior"])) $td .= "Superior ";
				if(!empty($arr["Arcuatedefect_OS_Superior"])) $ts .= "Superior ";
				if(!empty($arr["Arcuatedefect_OD_S_T"])) $td .= "T ";
				if(!empty($arr["Arcuatedefect_OS_S_T"])) $ts .= "T ";
				for($i=1;$i<=4;$i++){
					if(!empty($arr["Arcuatedefect_OD_S_".$i])) $td .= "+".$i." ";
					if(!empty($arr["Arcuatedefect_OS_S_".$i])) $ts .= "+".$i." ";
				}
				$td .= "<br/>";
				$ts .= "<br/>";	
				if(!empty($arr["Arcuatedefect_OD_Inferior"])) $td .= "Inferior ";
				if(!empty($arr["Arcuatedefect_OS_Inferior"])) $ts .= "Inferior ";
				if(!empty($arr["Arcuatedefect_OD_I_T"])) $td .= "T ";
				if(!empty($arr["Arcuatedefect_OS_I_T"])) $ts .= "T ";
				for($i=1;$i<=4;$i++){
					if(!empty($arr["Arcuatedefect_OD_I_".$i])) $td .= "+".$i." ";
					if(!empty($arr["Arcuatedefect_OS_I_".$i])) $ts .= "+".$i." ";
				}

				$ar2["Arcuate defect"]=array("OD"=>$td,"OS"=>$ts);		
				//Accurate Defect --

				//Defect --
				$arr["Defect_OD_Central"] = $rs["Defect_OD_Central"];
				$arr["Defect_OD_Superior"] = $rs["Defect_OD_Superior"];
				$arr["Defect_OD_Inferior"] = $rs["Defect_OD_Inferior"];
				$arr["Defect_OD_Scattered"] = $rs["Defect_OD_Scattered"];	

				$arr["Defect_OS_Central"] = $rs["Defect_OS_Central"];
				$arr["Defect_OS_Superior"] = $rs["Defect_OS_Superior"];
				$arr["Defect_OS_Inferior"] = $rs["Defect_OS_Inferior"];
				$arr["Defect_OS_Scattered"] = $rs["Defect_OS_Scattered"];		
				
				$arr["Defect_OS_T"] = $rs["Defect_OS_T"];
				$arr["Defect_OS_1"] = $rs["Defect_OS_1"];
				$arr["Defect_OS_2"] = $rs["Defect_OS_2"];
				$arr["Defect_OS_3"] = $rs["Defect_OS_3"];
				$arr["Defect_OS_4"] = $rs["Defect_OS_4"];
				$arr["Defect_OD_T"] = $rs["Defect_OD_T"];
				$arr["Defect_OD_1"] = $rs["Defect_OD_1"];
				$arr["Defect_OD_2"] = $rs["Defect_OD_2"];
				$arr["Defect_OD_3"] = $rs["Defect_OD_3"];
				$arr["Defect_OD_4"] = $rs["Defect_OD_4"];

				$arr["elem_noSigChange_OD"] = $rs["NoSigChange_OD"];
				$arr["elem_improved_OD"] = $rs["Improved_OD"];
				$arr["elem_incAbn_OD"] = $rs["IncAbn_OD"];

				$arr["elem_noSigChange_OS"] = $rs["NoSigChange_OS"];
				$arr["elem_improved_OS"] = $rs["Improved_OS"];
				$arr["elem_incAbn_OS"] = $rs["IncAbn_OS"];

				$td = $ts = "";
				$t = array("Central","Superior","Inferior","Scattered");
				foreach($t as $k => $v){
					if(!empty($arr["Defect_OD_".$v])) $td .= $v." ";
					if(!empty($arr["Defect_OS_".$v])) $ts .= $v." ";
				}
				$td .= "<br/>";
				$ts .= "<br/>";	
				if(!empty($arr["Defect_OD_T"])) $td .= "T ";
				if(!empty($arr["Defect_OS_T"])) $ts .= "T ";
				for($i=1;$i<=4;$i++){
					if(!empty($arr["Defect_OD_".$i])) $td .= "+".$i." ";
					if(!empty($arr["Defect_OS_".$i])) $ts .= "+".$i." ";
				}
				$td .= "<br/>";
				$ts .= "<br/>";
				$t = array("noSigChange"=>"No Sig. Change","improved"=>"Improved","incAbn"=>"Inc. Abn");
				foreach($t as $k => $v){
					if(!empty($arr["elem_".$k."_OD"])) $td .= $v." ";
					if(!empty($arr["elem_".$k."_OS"])) $ts .= $v." ";
				}
				$ar2["Defect"]=array("OD"=>$td,"OS"=>$ts);
				//Defect --
				
				//Traget --
				$arr["elem_targetIop_OD"] = $rs["iopTrgtOd"];
				$arr["elem_targetIop_OS"] = $rs["iopTrgtOs"];
				$ar2["IOP Target"]=array("OD"=>$arr["elem_targetIop_OD"],"OS"=>$arr["elem_targetIop_OS"]);
				//Target --

				//Other --
				$arr["Others_OD"] = stripslashes($rs["Others_OD"]);		
				$arr["Others_OS"] = stripslashes($rs["Others_OS"]);
				$ar2["Other"]=array("OD"=>$arr["Others_OD"],"OS"=>$arr["Others_OS"]);
				//Other --

				//Comments
				$ar2["Comments"]=$arr["elem_comments"]	= $rs["comments"];
			}
		}
		$str = $this->getJson($arr,$ar2,$ctid,$cedt);
		return $str;		
	}
	
	private function getVF_GL($dt,$tm="",$tid,$dir="Prev",$dc=1){ //
		$arr = $ar2 = array();
		$ctid=$cedt=$cetm="";
		$ctid = -1;
		$cedt = "0000-00-00";
		$arr["Date1"] = $dt;

		$sql = $this->getQry(1, $dir,$tid, $dt,$dc);
	//	echo $sql."<br/>";		
		$res = imw_query($sql);
		if($res){
			while($rs = imw_fetch_assoc($res)){
				$ctid = $rs["vf_gl_id"];
				$cedt = $arr["elem_examDate"] = $rs["examDate"];
				//$cetm = $arr["elem_examTime"] = $rs["examTime"];	
				
				$arr2["elem_gla_mac"] = $rs["elem_gla_mac"];
				
				//Tech Comments
				$ar2["Tech.Comments"]= $arr["techComment"] = $rs["techComments"];			
				
				//Date
				$arr["Date1"] = $rs["exdt2"];
				
				//details_high_od
				$ar2["Details:"]=array("OD"=>$rs["details_high_od"],"OS"=>$rs["details_high_od"]);	
				//poor_study_od
				$ar2["Poor Study"]=array("OD"=>$rs["poor_study_od"],"OS"=>$rs["poor_study_os"]);	
				//intratest_fluctuation_od
				$ar2["Intratest Fluctuation"]=array("OD"=>$rs["intratest_fluctuation_od"],"OS"=>$rs["intratest_fluctuation_os"]);	
				//artifact_od 
				$ar2["Artifact"]=array("OD"=>$rs["artifact_od"],"OS"=>$rs["artifact_os"]);	
				//details_lids_od
				$ar2["Details:"]=array("OD"=>$rs["details_lids_od"],"OS"=>$rs["details_lids_os"]);	
				//normal_od
				$ar2["Normal / Full"]=array("OD"=>$rs["normal_od"],"OS"=>$rs["normal_os"]);	
				//nonspecific_od
				$ar2["Nonspecific"]=array("OD"=>$rs["nonspecific_od"],"OS"=>$rs["nonspecific_os"]);	
				//nasal_step_od 
				$ar2["Nasal Step"]=array("OD"=>$rs["nasal_step_od"],"OS"=>$rs["nasal_step_os"]);	
				//arcuate_od
				$ar2["Arcuate"]=array("OD"=>$rs["arcuate_od"],"OS"=>$rs["arcuate_os"]);	
				//hemifield_od
				$ar2["Hemifield"]=array("OD"=>$rs["hemifield_od"],"OS"=>$rs["hemifield_os"]);	
				//paracentral_od
				$ar2["Paracentral"]=array("OD"=>$rs["paracentral_od"],"OS"=>$rs["paracentral_os"]);	
				//into_fixation_od
				$ar2["Into Fixation"]=array("OD"=>$rs["into_fixation_od"],"OS"=>$rs["into_fixation_os"]);	
				//central_island_od 
				$ar2["Central Island"]=array("OD"=>$rs["central_island_od"],"OS"=>$rs["central_island_os"]);	
				//enlarged_blind_spot_od
				$ar2["Enlarged Blind Spot"]=array("OD"=>$rs["enlarged_blind_spot_od"],"OS"=>$rs["enlarged_blind_spot_os"]);	
				//cecocentral_scotone_od
				$ar2["Cecocentral Scotone"]=array("OD"=>$rs["cecocentral_scotone_od"],"OS"=>$rs["cecocentral_scotone_os"]);	
				//central_scotoma_od
				$ar2["Central Scotoma"]=array("OD"=>$rs["central_scotoma_od"],"OS"=>$rs["central_scotoma_os"]);	
				//hemianopsia_od 
				$ar2["Hemianopsia"]=array("OD"=>$rs["hemianopsia_od"],"OS"=>$rs["hemianopsia_os"]);	
				//quadrantanopsia_od
				$ar2["Quadrantanopsia"]=array("OD"=>$rs["quadrantanopsia_od"],"OS"=>$rs["quadrantanopsia_os"]);	
				//congruent_od 
				$ar2["Congruent"]=array("OD"=>$rs["congruent_od"],"OS"=>$rs["congruent_os"]);	
				//incongruent_od
				$ar2["Incongruent"]=array("OD"=>$rs["incongruent_od"],"OS"=>$rs["incongruent_os"]);	
				//synthesis_od			
				$ar2["Synthesis"]=array("OD"=>$rs["synthesis_od"],"OS"=>$rs["synthesis_os"]);	

				//Comments
				$ar2["Comments"]=$arr["elem_comments"]	= $rs["comments"];
				
				
				//--			
				//od
				$arr["elem_reliabilityOd0"]=($rs["reliabilityOd"] == "Good") ? "checked" : "";
				$arr["elem_reliabilityOd1"]=($rs["reliabilityOd"] == "Fair") ? "checked" : "";
				$arr["elem_reliabilityOd2"]=($rs["reliabilityOd"] == "Poor") ? "checked" : "";
				
				
				$arr["elem_mdOd"]=$rs["mdOd"];
				$arr["elem_psdOd"]=$rs["psdOd"];
				$arr["elem_vfiOd"]=$rs["vfiOd"];

				$arr["elem_detailOd_hgtFixLoss"]= (strpos($rs["details_high_od"],"High Fixation Loss")!==false) ? "1" : "" ; 
				$arr["elem_detailOd_hgtFalsePos"]=(strpos($rs["details_high_od"],"High False Positive")!==false) ? "1" : "" ; 
				$arr["elem_detailOd_hgtFalseNeg"]=(strpos($rs["details_high_od"],"High False Negatives")!==false) ? "1" : "" ; 

				$arr["elem_poorStudyOd"]=$rs["poor_study_od"];
				$arr["elem_poorStudyOd_desc"]=$rs["poor_study_od_desc"];

				$arr["elem_poorStudyOd_descOther"]=$rs["poor_study_od_desc_other"];

				$arr["elem_intraFluctOd"]=$rs["intratest_fluctuation_od"];
				$arr["elem_artifactOd"]=$rs["artifact_od"];

				$arr["elem_detailOd_lid"]=(strpos($rs["details_lids_od"],"Lid")!==false) ? "checked" : "" ;
				$arr["elem_detailOd_lens_rim"]=(strpos($rs["details_lids_od"],"Lens Rim")!==false) ? "checked" : "" ;
				$arr["elem_detailOd_lens_power"]=(strpos($rs["details_lids_od"],"Lens Power")!==false) ? "checked" : "" ;
				$arr["elem_detailOd_cloverleaf"]=(strpos($rs["details_lids_od"],"Cloverleaf")!==false) ? "checked" : "" ;

				$arr["elem_normalFullOd"]=(strpos($rs["normal_od"],"Normal / Full")!==false) ? "checked" : "" ; 
				$arr["elem_nonspecificOd"]=(strpos($rs["nonspecific_od"],"Nonspecific Details")!==false) ? "checked" : "" ; 

				$arr["elem_nasalStepOd_Sup"]=(strpos($rs["nasal_step_od"],"Superior")!==false) ? "checked" : "" ; 
				$arr["elem_nasalStepOd_Inf"]=(strpos($rs["nasal_step_od"],"Inferior")!==false) ? "checked" : "" ; 

				$arr["elem_arcuateOd_Sup"]=(strpos($rs["arcuate_od"],"Superior")!==false) ? "checked" : "" ; 
				$arr["elem_arcuateOd_Inf"]=(strpos($rs["arcuate_od"],"Inferior")!==false) ? "checked" : "" ; 

				$arr["elem_hemifieldOd_Sup"]=(strpos($rs["hemifield_od"],"Superior")!==false) ? "checked" : "" ; 
				$arr["elem_hemifieldOd_Inf"]=(strpos($rs["hemifield_od"],"Inferior")!==false) ? "checked" : "" ; 

				$arr["elem_paracentralOd_Sup"]=(strpos($rs["paracentral_od"],"Superior")!==false) ? "checked" : "" ; 
				$arr["elem_paracentralOd_Inf"]=(strpos($rs["paracentral_od"],"Inferior")!==false) ? "checked" : "" ; 

				$arr["elem_intoFixOd_Sup"]=(strpos($rs["into_fixation_od"],"Superior")!==false) ? "checked" : "" ; 
				$arr["elem_intoFixOd_Inf"]=(strpos($rs["into_fixation_od"],"Inferior")!==false) ? "checked" : "" ; 

				$arr["elem_centralIslandOd"]=$rs["central_island_od"];

				$arr["elem_enlargeBlindSpotOd"]=$rs["enlarged_blind_spot_od"];
				$arr["elem_cecoScotoneOd"]=$rs["cecocentral_scotone_od"];
				$arr["elem_cenScotomaOd"]=$rs["central_scotoma_od"];

				$arr["elem_hemianopsiaOd_Right"]=(strpos($rs["hemianopsia_od"],"Right")!==false) ? "checked" : "" ; 
				$arr["elem_hemianopsiaOd_Left"]=(strpos($rs["hemianopsia_od"],"Left")!==false) ? "checked" : "" ; 
				$arr["elem_hemianopsiaOd_Bitemporal"]=(strpos($rs["hemianopsia_od"],"Bitemporal")!==false) ? "checked" : "" ; 
				
				$arr["elem_QuadrantanopsiaOd_RightSup"]=(strpos($rs["quadrantanopsia_od"],"Right Superior")!==false) ? "checked" : "" ; 
				$arr["elem_QuadrantanopsiaOd_LeftSup"]=(strpos($rs["quadrantanopsia_od"],"Left Superior")!==false) ? "checked" : "" ; 
				$arr["elem_QuadrantanopsiaOd_RightInf"]=(strpos($rs["quadrantanopsia_od"],"Right Inferior")!==false) ? "checked" : "" ; 
				$arr["elem_QuadrantanopsiaOd_LeftInf"]=(strpos($rs["quadrantanopsia_od"],"Left Inferior")!==false) ? "checked" : "" ; 
				$arr["elem_HomonomousOd_Congruent"]=(strpos($rs["homonomous_od"],"Congruent")!==false) ? "checked" : "" ; 
				$arr["elem_HomonomousOd_Incongruent"]=(strpos($rs["homonomous_od"],"Incongruent")!==false) ? "checked" : "" ; 

				$arr["elem_synthesisOd"]=$rs["synthesis_od"];
				
				//os
				$arr["elem_reliabilityOs0"]=($rs["reliabilityOs"] == "Good") ? "checked" : "";
				$arr["elem_reliabilityOs1"]=($rs["reliabilityOs"] == "Fair") ? "checked" : "";
				$arr["elem_reliabilityOs2"]=($rs["reliabilityOs"] == "Poor") ? "checked" : "";
				
				
				$arr["elem_mdOs"]=$rs["mdOs"];
				$arr["elem_psdOs"]=$rs["psdOs"];
				$arr["elem_vfiOs"]=$rs["vfiOs"];

				$arr["elem_detailOs_hgtFixLoss"]= (strpos($rs["details_high_os"],"High Fixation Loss")!==false) ? "1" : "" ; 
				$arr["elem_detailOs_hgtFalsePos"]=(strpos($rs["details_high_os"],"High False Positive")!==false) ? "1" : "" ; 
				$arr["elem_detailOs_hgtFalseNeg"]=(strpos($rs["details_high_os"],"High False Negatives")!==false) ? "1" : "" ; 

				$arr["elem_poorStudyOs"]=$rs["poor_study_os"];
				$arr["elem_poorStudyOs_desc"]=$rs["poor_study_os_desc"];

				$arr["elem_poorStudyOs_descOther"]=$rs["poor_study_os_desc_other"];

				$arr["elem_intraFluctOs"]=$rs["intratest_fluctuation_os"];
				$arr["elem_artifactOs"]=$rs["artifact_os"];

				$arr["elem_detailOs_lid"]=(strpos($rs["details_lids_os"],"Lid")!==false) ? "checked" : "" ;
				$arr["elem_detailOs_lens_rim"]=(strpos($rs["details_lids_os"],"Lens Rim")!==false) ? "checked" : "" ;
				$arr["elem_detailOs_lens_power"]=(strpos($rs["details_lids_os"],"Lens Power")!==false) ? "checked" : "" ;
				$arr["elem_detailOs_cloverleaf"]=(strpos($rs["details_lids_os"],"Cloverleaf")!==false) ? "checked" : "" ;

				$arr["elem_normalFullOs"]=(strpos($rs["normal_os"],"Normal / Full")!==false) ? "checked" : "" ; 
				$arr["elem_nonspecificOs"]=(strpos($rs["nonspecific_os"],"Nonspecific Details")!==false) ? "checked" : "" ; 

				$arr["elem_nasalStepOs_Sup"]=(strpos($rs["nasal_step_os"],"Superior")!==false) ? "checked" : "" ; 
				$arr["elem_nasalStepOs_Inf"]=(strpos($rs["nasal_step_os"],"Inferior")!==false) ? "checked" : "" ; 

				$arr["elem_arcuateOs_Sup"]=(strpos($rs["arcuate_os"],"Superior")!==false) ? "checked" : "" ; 
				$arr["elem_arcuateOs_Inf"]=(strpos($rs["arcuate_os"],"Inferior")!==false) ? "checked" : "" ; 

				$arr["elem_hemifieldOs_Sup"]=(strpos($rs["hemifield_os"],"Superior")!==false) ? "checked" : "" ; 
				$arr["elem_hemifieldOs_Inf"]=(strpos($rs["hemifield_os"],"Inferior")!==false) ? "checked" : "" ; 

				$arr["elem_paracentralOs_Sup"]=(strpos($rs["paracentral_os"],"Superior")!==false) ? "checked" : "" ; 
				$arr["elem_paracentralOs_Inf"]=(strpos($rs["paracentral_os"],"Inferior")!==false) ? "checked" : "" ; 

				$arr["elem_intoFixOs_Sup"]=(strpos($rs["into_fixation_os"],"Superior")!==false) ? "checked" : "" ; 
				$arr["elem_intoFixOs_Inf"]=(strpos($rs["into_fixation_os"],"Inferior")!==false) ? "checked" : "" ; 

				$arr["elem_centralIslandOs"]=$rs["central_island_os"];

				$arr["elem_enlargeBlindSpotOs"]=$rs["enlarged_blind_spot_os"];
				$arr["elem_cecoScotoneOs"]=$rs["cecocentral_scotone_os"];
				$arr["elem_cenScotomaOs"]=$rs["central_scotoma_os"];

				$arr["elem_hemianopsiaOs_Right"]=(strpos($rs["hemianopsia_os"],"Right")!==false) ? "checked" : "" ; 
				$arr["elem_hemianopsiaOs_Left"]=(strpos($rs["hemianopsia_os"],"Left")!==false) ? "checked" : "" ; 
				$arr["elem_hemianopsiaOs_Bitemporal"]=(strpos($rs["hemianopsia_os"],"Bitemporal")!==false) ? "checked" : "" ; 
				
				$arr["elem_QuadrantanopsiaOs_RightSup"]=(strpos($rs["quadrantanopsia_os"],"Right Superior")!==false) ? "checked" : "" ; 
				$arr["elem_QuadrantanopsiaOs_LeftSup"]=(strpos($rs["quadrantanopsia_os"],"Left Superior")!==false) ? "checked" : "" ; 
				$arr["elem_QuadrantanopsiaOs_RightInf"]=(strpos($rs["quadrantanopsia_os"],"Right Inferior")!==false) ? "checked" : "" ; 
				$arr["elem_QuadrantanopsiaOs_LeftInf"]=(strpos($rs["quadrantanopsia_os"],"Left Inferior")!==false) ? "checked" : "" ; 
				$arr["elem_HomonomousOs_Congruent"]=(strpos($rs["homonomous_os"],"Congruent")!==false) ? "checked" : "" ; 
				$arr["elem_HomonomousOs_Incongruent"]=(strpos($rs["homonomous_os"],"Incongruent")!==false) ? "checked" : "" ; 

				$arr["elem_synthesisOs"]=$rs["synthesis_os"];
				
				$elem_comments = stripslashes($rs["comments"]);
				$ar_elem_comments = explode("!~!",$elem_comments);
				$elem_comments_od = $ar_elem_comments[0];
				$elem_comments_os = $ar_elem_comments[1];
				$arr["elem_comments_od"]=$elem_comments_od;
				$arr["elem_comments_os"]=$elem_comments_os;
			}
		}
		
		$str = $this->getJson($arr,$ar2,$ctid,$cedt);
		return $str;

	}
	
	private function getHRT($dt,$tm="",$tid,$dir="Prev",$dc=1){
		$arr = $ar2 = array();
		$ctid=$cedt=$cetm="";		
		$ctid = -1;
		$cedt = "0000-00-00";
		$arr["Date1"] = $dt;
		
		$sql = $this->getQry(1, $dir,$tid, $dt,$dc);
		$res = imw_query($sql);
		if($res){
			while($rs = imw_fetch_assoc($res)){
				$ctid = $rs["nfa_id"];
				$cedt = $arr["elem_examDate"] = $rs["examDate"];
				//$cetm = $arr["elem_examTime"] = $rs["examTime"];
				//Tech Comments
				$ar2["Tech.Comments"]=$arr["techComment"] = $rs["techComments"];				

				//Date
				$arr["Date1"] = $rs["exdt2"];
				
				//Normal --
				$arr["Normal_OD_T"] = $rs["Normal_OD_T"];				
				$arr["Normal_OS_T"] = $rs["Normal_OS_T"];
				$arr["elem_normal_poorStudy_od"] = $rs["Normal_OD_PoorStudy"];
				$arr["elem_normal_poorStudy_os"] = $rs["Normal_OS_PoorStudy"];
				
				$td = $ts = "";
				if(!empty($arr["Normal_OD_T"])) $td .= "T ";
				if(!empty($arr["elem_normal_poorStudy_od"])) $td .= "Poor Study ";
				if(!empty($arr["Normal_OD_T"])) $ts .= "T ";
				if(!empty($arr["elem_normal_poorStudy_od"])) $ts .= "Poor Study ";
				$ar2["Normal"]=array("OD"=>$td,"OS"=>$ts);
				//Normal --

				//Border Line Defect --
				$arr["BorderLineDefect_OD_T"] = $rs["BorderLineDefect_OD_T"];
				$arr["BorderLineDefect_OD_1"] = $rs["BorderLineDefect_OD_1"];
				$arr["BorderLineDefect_OD_2"] = $rs["BorderLineDefect_OD_2"];
				$arr["BorderLineDefect_OD_3"] = $rs["BorderLineDefect_OD_3"];
				$arr["BorderLineDefect_OD_4"] = $rs["BorderLineDefect_OD_4"];
				$arr["BorderLineDefect_OS_T"] = $rs["BorderLineDefect_OS_T"];
				$arr["BorderLineDefect_OS_1"] = $rs["BorderLineDefect_OS_1"];
				$arr["BorderLineDefect_OS_2"] = $rs["BorderLineDefect_OS_2"];
				$arr["BorderLineDefect_OS_3"] = $rs["BorderLineDefect_OS_3"];
				$arr["BorderLineDefect_OS_4"] = $rs["BorderLineDefect_OS_4"];
				
				$td = $ts = "";
				if(!empty($arr["BorderLineDefect_OD_T"])) $td .= "T ";
				if(!empty($arr["BorderLineDefect_OS_T"])) $ts .= "T ";
				for($i=1;$i<=4;$i++){
					if(!empty($arr["BorderLineDefect_OD_".$i])) $td .= "+".$i." ";
					if(!empty($arr["BorderLineDefect_OS_".$i])) $ts .= "+".$i." ";
				}
				$ar2["Border Line Defect"]=array("OD"=>$td,"OS"=>$ts);

				//Border Line Defect --

				//Abnormal --
				$arr["Abnorma_OD_T"] = $rs["Abnorma_OD_T"];
				$arr["Abnorma_OD_1"] = $rs["Abnorma_OD_1"];
				$arr["Abnorma_OD_2"] = $rs["Abnorma_OD_2"];
				$arr["Abnorma_OD_3"] = $rs["Abnorma_OD_3"];
				$arr["Abnorma_OD_4"] = $rs["Abnorma_OD_4"];
				$arr["Abnorma_OS_T"] = $rs["Abnorma_OS_T"];
				$arr["Abnorma_OS_1"] = $rs["Abnorma_OS_1"];
				$arr["Abnorma_OS_2"] = $rs["Abnorma_OS_2"];
				$arr["Abnorma_OS_3"] = $rs["Abnorma_OS_3"];
				$arr["Abnorma_OS_4"] = $rs["Abnorma_OS_4"];
				
				$td = $ts = "";
				if(!empty($arr["Abnorma_OD_T"])) $td .= "T ";
				if(!empty($arr["Abnorma_OS_T"])) $ts .= "T ";
				for($i=1;$i<=4;$i++){
					if(!empty($arr["Abnorma_OD_".$i])) $td .= "+".$i." ";
					if(!empty($arr["Abnorma_OS_".$i])) $ts .= "+".$i." ";
				}
				$ar2["Abnormal"]=array("OD"=>$td,"OS"=>$ts);

				//Abnormal --

				//Decreased --
				$arr["decreased_OD"] = !empty($rs["decreaseOd"]) ? explode(",",$rs["decreaseOd"]) : array();
				$arr["decreased_OS"] = !empty($rs["decreaseOs"]) ? explode(",",$rs["decreaseOs"]) : array();				
				
				$ta=array("RA","RV","HVC","CSM","NFL","MC");
				foreach($ta as $key=>$val){
				
					$tmp = $val;
					if($tmp=="RA"){ $tmp = "Rim Area"; }
					if($tmp=="RV"){ $tmp = "Rim Volumn"; }
					if($tmp=="HVC"){ $tmp = "Height Variation Contour"; }
					if($tmp=="CSM"){ $tmp = "Cup Shape Measure"; }
					if($tmp=="MC"){ $tmp = "MC Intact RIM X 360"; }				
				
					$arr["decreased_OD_".$val] = in_array($tmp,$arr["decreased_OD"]) ? "1" : "";
					$arr["decreased_OS_".$val] = in_array($tmp,$arr["decreased_OS"]) ? "1" : "";
				}				
				$td = $ts = "";
				$td = implode("<br/>",$arr["decreased_OD"]);
				$ts = implode("<br/>",$arr["decreased_OS"]);
				$ar2["Decreased"]=array("OD"=>$td,"OS"=>$ts);
				//Decreased --

				//Thin --
				$arr["thin_OD"] = !empty($rs["thinOd"]) ? explode(",",$rs["thinOd"]) : array();
				$arr["thin_OS"] = !empty($rs["thinOs"]) ? explode(",",$rs["thinOs"]) : array();
				$ta=array("Temp","ST","SN","N","IN","IT");
				foreach($ta as $key=>$val){
					$arr["thin_OD_".$val] = in_array($val,$arr["thin_OD"]) ? "1" : "";
					$arr["thin_OS_".$val] = in_array($val,$arr["thin_OS"]) ? "1" : "";
				}

				$td = $ts = "";
				$td = implode(", ",$arr["thin_OD"]);
				$ts = implode(", ",$arr["thin_OS"]);
				$ar2["Thin"]=array("OD"=>$td,"OS"=>$ts);
				//Thin --

				//Total Thin --
				$arr["total_thin_OD"] = $rs["totalThinOd"];
				$arr["total_thin_OS"] = $rs["totalThinOs"];
				$arr["elem_noSigChange_OD"] = $rs["NoSigChange_OD"];
				$arr["elem_improved_OD"] = $rs["Improved_OD"];
				$arr["elem_incAbn_OD"] = $rs["IncAbn_OD"];
				$arr["elem_noSigChange_OS"] = $rs["NoSigChange_OS"];
				$arr["elem_improved_OS"] = $rs["Improved_OS"];
				$arr["elem_incAbn_OS"] = $rs["IncAbn_OS"];	
				
				$td = $ts = "";
				$td .= $arr["total_thin_OD"];
				$ts .= $arr["total_thin_OS"];
				$td .= "<br/>";
				$ts .= "<br/>";
				$t = array("noSigChange"=>"No Sig. Change","improved"=>"Improved","incAbn"=>"Inc. Abn");
				foreach($t as $k => $v){
					if(!empty($arr["elem_".$k."_OD"])) $td .= $v." ";
					if(!empty($arr["elem_".$k."_OS"])) $ts .= $v." ";
				}
				$ar2["Total Thin"]=array("OD"=>$td, "OS"=>$ts);

				//Total Thin --

				//Iop Target --
				$arr["elem_targetIop_OD"] = $rs["iopTrgtOd"];
				$arr["elem_targetIop_OS"] = $rs["iopTrgtOs"];
				$ar2["IOP Target"]=array("OD"=>$arr["elem_targetIop_OD"],"OS"=>$arr["elem_targetIop_OS"]);
				//Iop Target --

				// Other --
				$arr["Others_OD"] = stripslashes($rs["Others_OD"]);
				$arr["Others_OS"] = stripslashes($rs["Others_OS"]);
				$ar2["Other"]=array("OD"=>$arr["Others_OD"],"OS"=>$arr["Others_OS"]);
				// Other --
				
				//Comments
				$ar2["Comments"]=$arr["elem_comments"]	= $rs["comments"];
			}
		}	
		$str = $this->getJson($arr,$ar2,$ctid,$cedt);
		return $str;
	}
	
	private function getOCT($dt,$tm="",$tid,$dir="Prev",$dc=1){
		$arr = $ar2 = array();
		$ctid=$cedt=$cetm="";		
		$ctid = -1;
		$cedt = "0000-00-00";
		$arr["Date1"] = $dt;
		
		$sql = $this->getQry(1, $dir,$tid, $dt,$dc);
		$res = imw_query($sql);
		if($res){
			while($rs = imw_fetch_assoc($res)){
				$ctid = $rs["oct_id"];
				$cedt = $arr["elem_examDate"] = $rs["examDate"];
				//$cetm = $arr["elem_examTime"] = $rs["examTime"];
				//Tech Comments
				$ar2["Tech.Comments"]=$arr["techComment"] = $rs["techComments"];				

				//Date
				$arr["Date1"] = $rs["exdt2"];
				
				//Normal --
				$arr["Normal_OD_T"] = $rs["Normal_OD_T"];				
				$arr["Normal_OS_T"] = $rs["Normal_OS_T"];				
				$arr["elem_normal_poorStudy_od"] = $rs["Normal_OD_PoorStudy"];
				$arr["elem_normal_poorStudy_os"] = $rs["Normal_OS_PoorStudy"];
				
				$td = $ts = "";
				if(!empty($arr["Normal_OD_T"])) $td .= "T ";
				if(!empty($arr["elem_normal_poorStudy_od"])) $td .= "Poor Study ";
				if(!empty($arr["Normal_OD_T"])) $ts .= "T ";
				if(!empty($arr["elem_normal_poorStudy_od"])) $ts .= "Poor Study ";
				$ar2["Normal"]=array("OD"=>$td,"OS"=>$ts);
				//Normal --

				//Border Line Defect --
				$arr["BorderLineDefect_OD_T"] = $rs["BorderLineDefect_OD_T"];
				$arr["BorderLineDefect_OD_1"] = $rs["BorderLineDefect_OD_1"];
				$arr["BorderLineDefect_OD_2"] = $rs["BorderLineDefect_OD_2"];
				$arr["BorderLineDefect_OD_3"] = $rs["BorderLineDefect_OD_3"];
				$arr["BorderLineDefect_OD_4"] = $rs["BorderLineDefect_OD_4"];
				$arr["BorderLineDefect_OS_T"] = $rs["BorderLineDefect_OS_T"];
				$arr["BorderLineDefect_OS_1"] = $rs["BorderLineDefect_OS_1"];
				$arr["BorderLineDefect_OS_2"] = $rs["BorderLineDefect_OS_2"];
				$arr["BorderLineDefect_OS_3"] = $rs["BorderLineDefect_OS_3"];
				$arr["BorderLineDefect_OS_4"] = $rs["BorderLineDefect_OS_4"];

				$td = $ts = "";
				if(!empty($arr["BorderLineDefect_OD_T"])) $td .= "T ";
				if(!empty($arr["BorderLineDefect_OS_T"])) $ts .= "T ";
				for($i=1;$i<=4;$i++){
					if(!empty($arr["BorderLineDefect_OD_".$i])) $td .= "+".$i." ";
					if(!empty($arr["BorderLineDefect_OS_".$i])) $ts .= "+".$i." ";
				}
				$ar2["Border Line Defect"]=array("OD"=>$td,"OS"=>$ts);

				//Border Line Defect --

				//Abnormal --
				$arr["Abnorma_OD_T"] = $rs["Abnorma_OD_T"];
				$arr["Abnorma_OD_1"] = $rs["Abnorma_OD_1"];
				$arr["Abnorma_OD_2"] = $rs["Abnorma_OD_2"];
				$arr["Abnorma_OD_3"] = $rs["Abnorma_OD_3"];
				$arr["Abnorma_OD_4"] = $rs["Abnorma_OD_4"];
				$arr["Abnorma_OS_T"] = $rs["Abnorma_OS_T"];
				$arr["Abnorma_OS_1"] = $rs["Abnorma_OS_1"];
				$arr["Abnorma_OS_2"] = $rs["Abnorma_OS_2"];
				$arr["Abnorma_OS_3"] = $rs["Abnorma_OS_3"];
				$arr["Abnorma_OS_4"] = $rs["Abnorma_OS_4"];
				
				$td = $ts = "";
				if(!empty($arr["Abnorma_OD_T"])) $td .= "T ";
				if(!empty($arr["Abnorma_OS_T"])) $ts .= "T ";
				for($i=1;$i<=4;$i++){
					if(!empty($arr["Abnorma_OD_".$i])) $td .= "+".$i." ";
					if(!empty($arr["Abnorma_OS_".$i])) $ts .= "+".$i." ";
				}				

				$arr["testRes_OD"] = !empty($rs["test_res_od"]) ? explode(",",$rs["test_res_od"]) : array();
				$arr["testRes_OS"] = !empty($rs["test_res_os"]) ? explode(",",$rs["test_res_os"]) : array();				
				
				$ta=array("CME","IRF","ERM","Drusen","VMT","SRetF","SRF","CNV","RPEDetach","RPERip");
				foreach($ta as $key=>$val){
					
					$tmp = $val;
					if($tmp=="IRF"){ $tmp = "Intra Retinal Fluid"; }
					if($tmp=="VMT"){ $tmp = "Vitreous Macula Traction"; }
					if($tmp=="SRetF"){ $tmp = "Sub Retinal Fluid"; }
					if($tmp=="SRF"){ $tmp = "Sub RPE Foveal"; }
					if($tmp=="RPEDetach"){ $tmp = "RPE Detach"; }
					if($tmp=="RPERip"){ $tmp = "RPE Rip"; }					
				
					$arr["testRes_OD_".$val] = in_array($tmp,$arr["testRes_OD"]) ? "1" : "";
					$arr["testRes_OS_".$val] = in_array($tmp,$arr["testRes_OS"]) ? "1" : "";
				}
				
				$td .= "<br/>";
				$ts .= "<br/>";
				$td .= $rs["test_res_od"];
				$ts .= $rs["test_res_os"];

				$arr["elem_noSigChange_OD"] = $rs["NoSigChange_OD"];
				$arr["elem_improved_OD"] = $rs["Improved_OD"];
				$arr["elem_incAbn_OD"] = $rs["IncAbn_OD"];

				$arr["elem_noSigChange_OS"] = $rs["NoSigChange_OS"];
				$arr["elem_improved_OS"] = $rs["Improved_OS"];
				$arr["elem_incAbn_OS"] = $rs["IncAbn_OS"];
				
				$td .= "<br/>";
				$ts .= "<br/>";
				$t = array("noSigChange"=>"No Sig. Change","improved"=>"Improved","incAbn"=>"Inc. Abn");
				foreach($t as $k => $v){
					if(!empty($arr["elem_".$k."_OD"])) $td .= $v." ";
					if(!empty($arr["elem_".$k."_OS"])) $ts .= $v." ";
				}

				$ar2["Abnormal"]=array("OD"=>$td,"OS"=>$ts);
				//Abnormal --

				//Fovea Thickness --
				$arr["elem_foveaThick_OD"] = $rs["fovea_thick_OD"];
				$arr["elem_foveaThick_OS"] = $rs["fovea_thick_OS"];
				$ar2["Foveal Thickness"]=array("OD"=>$arr["elem_foveaThick_OD"],"OS"=>$arr["elem_foveaThick_OS"]);
				//Fovea Thickness --
				
				//---- AVG NFL Thickness ----
				$arr["elem_avg_nfl_Thick_OD"] = $rs["avg_nfl_Thick_OD"];
				$arr["elem_avg_nfl_Thick_OS"] = $rs["avg_nfl_Thick_OS"];
				$ar2["AVG NFL Thickness"]=array("OD"=>$arr["elem_avg_nfl_Thick_OD"],"OS"=>$arr["elem_avg_nfl_Thick_OS"]);
				//---- AVG NFL Thickness ----

				//Iop Target --
				$arr["elem_targetIop_OD"] = $rs["iopTrgtOd"];
				$arr["elem_targetIop_OS"] = $rs["iopTrgtOs"];
				$ar2["IOP Target"]=array("OD"=>$arr["elem_targetIop_OD"],"OS"=>$arr["elem_targetIop_OS"]);
				//Iop Target --

				//Other --
				$arr["Others_OD"] = stripslashes($rs["Others_OD"]);
				$arr["Others_OS"] = stripslashes($rs["Others_OS"]);
				$ar2["Other"]=array("OD"=>$arr["Others_OD"],"OS"=>$arr["Others_OS"]);
				//Other --

				//Comments
				$ar2["Comments"]=$arr["elem_comments"]	= $rs["comments"];
			}
		}	
		$str = $this->getJson($arr,$ar2,$ctid,$cedt);
		return $str;		
	}
	
	private function getOCT_RNFL($dt,$tm="",$tid,$dir="Prev",$dc=1){
		$arr = $ar2 = array();
		$ctid=$cedt=$cetm="";		
		$ctid = -1;
		$cedt = "0000-00-00";
		$arr["Date1"] = $dt;
		
		$sql = $this->getQry(1, $dir,$tid, $dt,$dc);
		$res = imw_query($sql);
		if($res){
			while($rs = imw_fetch_assoc($res)){
				$ctid = $rs["oct_rnfl_id"];
				$cedt = $arr["elem_examDate"] = $rs["examDate"];
				//$cetm = $arr["elem_examTime"] = $rs["examTime"];
				//Tech Comments
				$ar2["Tech.Comments"]=$arr["techComment"] = $rs["techComments"];				

				//Date
				$arr["Date1"] = $rs["exdt2"];
				
				//Reliability
				$ar2["Reliability"]=array("OD"=>$rs["reliabilityOd"],"OS"=>$rs["reliabilityOs"]);
				//Signal Strength
				$ar2["Signal Strength"]=array("OD"=>$rs["signal_strength_od"],"OS"=>$rs["signal_strength_os"]);
				//Quality :
				$ar2["Quality"]=array("OD"=>$rs["quality_od"],"OS"=>$rs["quality_os"]);
				//Details :
				$ar2["Details"]=array("OD"=>str_replace("!,!",",", $rs["details_od"]),"OS"=>str_replace("!,!",",", $rs["details_os"]));
				//Disc area :
				$ar2["Disc area"]=array("OD"=>$rs["disc_area_od"],"OS"=>$rs["disc_area_os"]);
				//Disc size :
				$ar2["Disc size"]=array("OD"=>$rs["disc_size_od"],"OS"=>$rs["disc_size_os"]);
				//Vertical C:D
				$ar2["Vertical C:D"]=array("OD"=>$rs["verti_cd_od"],"OS"=>$rs["verti_cd_os"]);
				//Disc edema :
				$ar2["Disc edema "]=array("OD"=>$rs["disc_edema_od"],"OS"=>$rs["disc_edema_os"]);
				//RNFL
				$ar2["RNFL"]=array("OD"=>$rs["rnfl_od"],"OS"=>$rs["rnfl_os"]);
				//Contour
				$ar2["Contour"]=array("OD"=>"","OS"=>"");
				
				//Overall
				$ar2["Overall"]=array("OD"=>$rs["contour_overall_od"],"OS"=>$rs["contour_overall_os"]);
				//Superior
				$ar2["Superior"]=array("OD"=>$rs["contour_superior_od"],"OS"=>$rs["contour_superior_os"]);
				//Inferior
				$ar2["Inferior"]=array("OD"=>$rs["contour_inferior_od"],"OS"=>$rs["contour_inferior_os"]);
				//Temporal
				$ar2["Temporal"]=array("OD"=>$rs["contour_temporal_od"],"OS"=>$rs["contour_temporal_os"]);
				//Nasal
				$ar2["Nasal"]=array("OD"=>$rs["contour_nasal_od"],"OS"=>$rs["contour_nasal_os"]);
				//GCC
				$ar2["GCC"]=array("OD"=>$rs["contour_gcc_od"],"OS"=>$rs["contour_gcc_os"]);
				
				//Symmetric
				$ar2["Symmetric"]=array("OD"=>$rs["symmetric_od"],"OS"=>$rs["symmetric_os"]);
				//GPA
				$ar2["GPA"]=array("OD"=>$rs["gpa_od"],"OS"=>$rs["gpa_os"]);
				//Synthesis
				$ar2["Synthesis"]=array("OD"=>$rs["synthesis_od"],"OS"=>$rs["synthesis_os"]);				

				$arr["testRes_OD"] = !empty($rs["test_res_od"]) ? explode(",",$rs["test_res_od"]) : array();
				$arr["testRes_OS"] = !empty($rs["test_res_os"]) ? explode(",",$rs["test_res_os"]) : array();
				
				$td .= "<br/>";
				$ts .= "<br/>";
				$td .= $rs["test_res_od"];
				$ts .= $rs["test_res_os"];
				
				//Comments
				$ar2["Comments"]=$arr["elem_comments"]	= $rs["comments"];
				
				//--
				//Od
				$arr["elem_reliabilityOd0"]=($rs["reliabilityOd"] == "Good") ? "checked" : "";
				$arr["elem_reliabilityOd1"]=($rs["reliabilityOd"] == "Fair") ? "checked" : "";
				$arr["elem_reliabilityOd2"]=($rs["reliabilityOd"] == "Poor") ? "checked" : "";

				$arr["elem_signal_strength_od"] = $rs["signal_strength_od"];
				$arr["elem_quality_od_gd"] = strpos($rs["quality_od"],"Good")!==false ? "Good" : ""; 
				$arr["elem_quality_od_adequate"] = strpos($rs["quality_od"],"Adequate")!==false ? "Adequate" : ""; 
				$arr["elem_quality_od_poor"] = strpos($rs["quality_od"],"Poor")!==false ? "Poor" : ""; 
				
				$ar_details_od = explode("!,!", $rs["details_od"]);
				$ar_details_od=array_map('trim',$ar_details_od);	
				$ar_details_od_len = count($ar_details_od);
				$ar_details_od_last = trim($ar_details_od[$ar_details_od_len-1]);
				$elem_detail_od_AlgoFail=in_array("Algorithm Fail", $ar_details_od) ? "Algorithm Fail" : ""; 
				$elem_detail_od_MediaOpacity=in_array("Media Opacity", $ar_details_od) ? "Media Opacity" : ""; 
				$elem_detail_od_Artifact=in_array("Artifact", $ar_details_od) ? "Artifact" : ""; 	
				if(!empty($ar_details_od_last) && $ar_details_od_last!="Algorithm Fail" && $ar_details_od_last!="Media Opacity" && $ar_details_od_last!="Artifact" ){
				$elem_details_od_other=$ar_details_od_last;
				}
				
				$arr["elem_details_od_AlgoFail"] = $elem_detail_od_AlgoFail;
				$arr["elem_details_od_MediaOpacity"] = $elem_detail_od_MediaOpacity;
				$arr["elem_details_od_Artifact"] = $elem_detail_od_Artifact;
				$arr["elem_details_od_other"] = $elem_details_od_other;

				$arr["elem_discarea_od"] = $rs["disc_area_od"];

				$arr["elem_discsize_od_Large"] = strpos($rs["disc_size_od"],"Large")!==false ? "Large" : ""; 
				$arr["elem_discsize_od_Avg"] = strpos($rs["disc_size_od"],"Avg")!==false ? "Avg" : ""; 
				$arr["elem_discsize_od_Small"] = strpos($rs["disc_size_od"],"Small")!==false ? "Small" : ""; 
				
				$arr["elem_verti_cd_od"] = $rs["verti_cd_od"];

				$arr["elem_discedema_od_No"] = strpos($rs["disc_edema_od"],"No")!==false ? "No" : ""; 
				$arr["elem_discedema_od_Mild"] = strpos($rs["disc_edema_od"],"Mild")!==false ? "Mild" : ""; 
				$arr["elem_discedema_od_Md"] = strpos($rs["disc_edema_od"],"Mod")!==false ? "Mod" : ""; 
				$arr["elem_discedema_od_Severe"] = strpos($rs["disc_edema_od"],"Severe")!==false ? "Severe" : ""; 
				$arr["elem_discedema_od_Sup"] = strpos($rs["disc_edema_od"],"Superior")!==false ? "Superior" : ""; 
				$arr["elem_discedema_od_Inf"] = strpos($rs["disc_edema_od"],"Inferior")!==false ? "Inferior" : ""; 

				$arr["elem_rnfl_od_Avg"] = $rs["rnfl_od"];

				$arr["elem_contour_overall_od_NL"] = strpos($rs["contour_overall_od"],"NL")!==false ? "NL" : ""; 
				$arr["elem_contour_overall_od_Thin"] = strpos($rs["contour_overall_od"],"Thin")!==false ? "Thin" : ""; 
				$arr["elem_contour_overall_od_VeryThin"] = strpos($rs["contour_overall_od"],"Very Thin")!==false ? "Very Thin" : ""; 
				$arr["elem_contour_overall_od_Thick"] = strpos($rs["contour_overall_od"],"Thick")!==false ? "Thick" : ""; 
				$arr["elem_contour_overall_od_BL"] = strpos($rs["contour_overall_od"],"Borderline")!==false ? "Borderline" : "";				

				$arr["elem_contour_superior_od_NL"] = strpos($rs["contour_superior_od"],"NL")!==false ? "NL" : ""; 
				$arr["elem_contour_superior_od_Thin"] = strpos($rs["contour_superior_od"],"Thin")!==false ? "Thin" : ""; 
				$arr["elem_contour_superior_od_VeryThin"] = strpos($rs["contour_superior_od"],"Very Thin")!==false ? "Very Thin" : ""; 
				$arr["elem_contour_superior_od_Thick"] = strpos($rs["contour_superior_od"],"Thick")!==false ? "Thick" : ""; 
				$arr["elem_contour_superior_od_BL"] = strpos($rs["contour_superior_od"],"Borderline")!==false ? "Borderline" : ""; 

				$arr["elem_contour_inferior_od_NL"] = strpos($rs["contour_inferior_od"],"NL")!==false ? "NL" : ""; 
				$arr["elem_contour_inferior_od_Thin"] = strpos($rs["contour_inferior_od"],"Thin")!==false ? "Thin" : ""; 
				$arr["elem_contour_inferior_od_VeryThin"] = strpos($rs["contour_inferior_od"],"Very Thin")!==false ? "Very Thin" : ""; 
				$arr["elem_contour_inferior_od_Thick"] = strpos($rs["contour_inferior_od"],"Thick")!==false ? "Thick" : ""; 
				$arr["elem_contour_inferior_od_BL"] = strpos($rs["contour_inferior_od"],"Borderline")!==false ? "Borderline" : ""; 

				$arr["elem_contour_temporal_od_NL"] = strpos($rs["contour_temporal_od"],"NL")!==false ? "NL" : ""; 
				$arr["elem_contour_temporal_od_Thin"] = strpos($rs["contour_temporal_od"],"Thin")!==false ? "Thin" : ""; 
				$arr["elem_contour_temporal_od_VeryThin"] = strpos($rs["contour_temporal_od"],"Very Thin")!==false ? "Very Thin" : ""; 
				$arr["elem_contour_temporal_od_Thick"] = strpos($rs["contour_temporal_od"],"Thick")!==false ? "Thick" : ""; 
				$arr["elem_contour_temporal_od_BL"] = strpos($rs["contour_temporal_od"],"Borderline")!==false ? "Borderline" : ""; 
				
				$arr["elem_contour_nasal_od_NL"] = strpos($rs["contour_nasal_od"],"NL")!==false ? "NL" : ""; 
				$arr["elem_contour_nasal_od_Thin"] = strpos($rs["contour_nasal_od"],"Thin")!==false ? "Thin" : ""; 
				$arr["elem_contour_nasal_od_VeryThin"] = strpos($rs["contour_nasal_od"],"Very Thin")!==false ? "Very Thin" : ""; 
				$arr["elem_contour_nasal_od_Thick"] = strpos($rs["contour_nasal_od"],"Thick")!==false ? "Thick" : ""; 
				$arr["elem_contour_nasal_od_BL"] = strpos($rs["contour_nasal_od"],"Borderline")!==false ? "Borderline" : ""; 
				
				$arr["elem_contour_gcc_od_NL"] = strpos($rs["contour_gcc_od"],"NL")!==false ? "NL" : ""; 
				$arr["elem_contour_gcc_od_Thin"] = strpos($rs["contour_gcc_od"],"Thin")!==false ? "Thin" : ""; 
				$arr["elem_contour_gcc_od_VeryThin"] = strpos($rs["contour_gcc_od"],"Very Thin")!==false ? "Very Thin" : ""; 
				$arr["elem_contour_gcc_od_Thick"] = strpos($rs["contour_gcc_od"],"Thick")!==false ? "Thick" : ""; 
				$arr["elem_contour_gcc_od_BL"] = strpos($rs["contour_gcc_od"],"Borderline")!==false ? "Borderline" : "";

				$arr["elem_symmertric_od_Yes"] = ($rs["symmetric_od"]=="Yes") ? "Yes" : ""; 
				$arr["elem_symmertric_od_No"] = ($rs["symmetric_od"]=="No") ? "No" : ""; 
				
				$arr["elem_gpa_od_No"] = ($rs["gpa_od"]=="No") ? "No" : ""; 
				$arr["elem_gpa_od_pos"] = ($rs["gpa_od"]=="Possible") ? "Possible" : ""; 
				$arr["elem_gpa_od_lp"] = ($rs["gpa_od"]=="Like Progression") ? "Like Progression" : ""; 

				$arr["elem_interpret_systhesis_od"] = $rs["synthesis_od"];
				
				//Os
				$arr["elem_reliabilityOs0"]=($rs["reliabilityOs"] == "Good") ? "checked" : "";
				$arr["elem_reliabilityOs1"]=($rs["reliabilityOs"] == "Fair") ? "checked" : "";
				$arr["elem_reliabilityOs2"]=($rs["reliabilityOs"] == "Poor") ? "checked" : "";

				$arr["elem_signal_strength_os"] = $rs["signal_strength_os"];
				$arr["elem_quality_os_gd"] = strpos($rs["quality_os"],"Good")!==false ? "Good" : ""; 
				$arr["elem_quality_os_adequate"] = strpos($rs["quality_os"],"Adequate")!==false ? "Adequate" : ""; 
				$arr["elem_quality_os_poor"] = strpos($rs["quality_os"],"Poor")!==false ? "Poor" : ""; 
				
				$ar_details_os = explode("!,!", $rs["details_os"]);
				$ar_details_os=array_map('trim',$ar_details_os);	
				$ar_details_os_len = count($ar_details_os);
				$ar_details_os_last = trim($ar_details_os[$ar_details_os_len-1]);
				$elem_detail_os_AlgoFail=in_array("Algorithm Fail", $ar_details_os) ? "Algorithm Fail" : ""; 
				$elem_detail_os_MediaOpacity=in_array("Media Opacity", $ar_details_os) ? "Media Opacity" : ""; 
				$elem_detail_os_Artifact=in_array("Artifact", $ar_details_os) ? "Artifact" : ""; 	
				if(!empty($ar_details_os_last) && $ar_details_os_last!="Algorithm Fail" && $ar_details_os_last!="Media Opacity" && $ar_details_os_last!="Artifact" ){
				$elem_details_os_other=$ar_details_os_last;
				}
				
				$arr["elem_details_os_AlgoFail"] = $elem_detail_os_AlgoFail;
				$arr["elem_details_os_MediaOpacity"] = $elem_detail_os_MediaOpacity;
				$arr["elem_details_os_Artifact"] = $elem_detail_os_Artifact;
				$arr["elem_details_os_other"] = $elem_details_os_other;

				$arr["elem_discarea_os"] = $rs["disc_area_os"];

				$arr["elem_discsize_os_Large"] = strpos($rs["disc_size_os"],"Large")!==false ? "Large" : ""; 
				$arr["elem_discsize_os_Avg"] = strpos($rs["disc_size_os"],"Avg")!==false ? "Avg" : ""; 
				$arr["elem_discsize_os_Small"] = strpos($rs["disc_size_os"],"Small")!==false ? "Small" : ""; 
				
				$arr["elem_verti_cd_os"] = $rs["verti_cd_os"];

				$arr["elem_discedema_os_No"] = strpos($rs["disc_edema_os"],"No")!==false ? "No" : ""; 
				$arr["elem_discedema_os_Mild"] = strpos($rs["disc_edema_os"],"Mild")!==false ? "Mild" : ""; 
				$arr["elem_discedema_os_Md"] = strpos($rs["disc_edema_os"],"Mod")!==false ? "Mod" : ""; 
				$arr["elem_discedema_os_Severe"] = strpos($rs["disc_edema_os"],"Severe")!==false ? "Severe" : ""; 
				$arr["elem_discedema_os_Sup"] = strpos($rs["disc_edema_os"],"Superior")!==false ? "Superior" : ""; 
				$arr["elem_discedema_os_Inf"] = strpos($rs["disc_edema_os"],"Inferior")!==false ? "Inferior" : ""; 

				$arr["elem_rnfl_os_Avg"] = $rs["rnfl_os"];

				$arr["elem_contour_overall_os_NL"] = strpos($rs["contour_overall_os"],"NL")!==false ? "NL" : ""; 
				$arr["elem_contour_overall_os_Thin"] = strpos($rs["contour_overall_os"],"Thin")!==false ? "Thin" : ""; 
				$arr["elem_contour_overall_os_VeryThin"] = strpos($rs["contour_overall_os"],"Very Thin")!==false ? "Very Thin" : ""; 
				$arr["elem_contour_overall_os_Thick"] = strpos($rs["contour_overall_os"],"Thick")!==false ? "Thick" : ""; 
				$arr["elem_contour_overall_os_BL"] = strpos($rs["contour_overall_os"],"Borderline")!==false ? "Borderline" : "";

				$arr["elem_contour_superior_os_NL"] = strpos($rs["contour_superior_os"],"NL")!==false ? "NL" : ""; 
				$arr["elem_contour_superior_os_Thin"] = strpos($rs["contour_superior_os"],"Thin")!==false ? "Thin" : ""; 
				$arr["elem_contour_superior_os_VeryThin"] = strpos($rs["contour_superior_os"],"Very Thin")!==false ? "Very Thin" : ""; 
				$arr["elem_contour_superior_os_Thick"] = strpos($rs["contour_superior_os"],"Thick")!==false ? "Thick" : ""; 
				$arr["elem_contour_superior_os_BL"] = strpos($rs["contour_superior_os"],"Borderline")!==false ? "Borderline" : "";				

				$arr["elem_contour_inferior_os_NL"] = strpos($rs["contour_inferior_os"],"NL")!==false ? "NL" : ""; 
				$arr["elem_contour_inferior_os_Thin"] = strpos($rs["contour_inferior_os"],"Thin")!==false ? "Thin" : ""; 
				$arr["elem_contour_inferior_os_VeryThin"] = strpos($rs["contour_inferior_os"],"Very Thin")!==false ? "Very Thin" : ""; 
				$arr["elem_contour_inferior_os_Thick"] = strpos($rs["contour_inferior_os"],"Thick")!==false ? "Thick" : ""; 
				$arr["elem_contour_inferior_os_BL"] = strpos($rs["contour_inferior_os"],"Borderline")!==false ? "Borderline" : "";

				$arr["elem_contour_temporal_os_NL"] = strpos($rs["contour_temporal_os"],"NL")!==false ? "NL" : ""; 
				$arr["elem_contour_temporal_os_Thin"] = strpos($rs["contour_temporal_os"],"Thin")!==false ? "Thin" : ""; 
				$arr["elem_contour_temporal_os_VeryThin"] = strpos($rs["contour_temporal_os"],"Very Thin")!==false ? "Very Thin" : ""; 
				$arr["elem_contour_temporal_os_Thick"] = strpos($rs["contour_temporal_os"],"Thick")!==false ? "Thick" : ""; 
				$arr["elem_contour_temporal_os_BL"] = strpos($rs["contour_temporal_os"],"Borderline")!==false ? "Borderline" : "";
				
				$arr["elem_contour_nasal_os_NL"] = strpos($rs["contour_nasal_os"],"NL")!==false ? "NL" : ""; 
				$arr["elem_contour_nasal_os_Thin"] = strpos($rs["contour_nasal_os"],"Thin")!==false ? "Thin" : ""; 
				$arr["elem_contour_nasal_os_VeryThin"] = strpos($rs["contour_nasal_os"],"Very Thin")!==false ? "Very Thin" : ""; 
				$arr["elem_contour_nasal_os_Thick"] = strpos($rs["contour_nasal_os"],"Thick")!==false ? "Thick" : ""; 
				$arr["elem_contour_nasal_os_BL"] = strpos($rs["contour_nasal_os"],"Borderline")!==false ? "Borderline" : ""; 
				
				$arr["elem_contour_gcc_os_NL"] = strpos($rs["contour_gcc_os"],"NL")!==false ? "NL" : ""; 
				$arr["elem_contour_gcc_os_Thin"] = strpos($rs["contour_gcc_os"],"Thin")!==false ? "Thin" : ""; 
				$arr["elem_contour_gcc_os_VeryThin"] = strpos($rs["contour_gcc_os"],"Very Thin")!==false ? "Very Thin" : ""; 
				$arr["elem_contour_gcc_os_Thick"] = strpos($rs["contour_gcc_os"],"Thick")!==false ? "Thick" : ""; 
				$arr["elem_contour_gcc_os_BL"] = strpos($rs["contour_gcc_os"],"Borderline")!==false ? "Borderline" : "";

				$arr["elem_symmertric_os_Yes"] = ($rs["symmetric_os"]=="Yes") ? "Yes" : ""; 
				$arr["elem_symmertric_os_No"] = ($rs["symmetric_os"]=="No") ? "No" : ""; 
				
				$arr["elem_gpa_os_No"] = ($rs["gpa_os"]=="No") ? "No" : ""; 
				$arr["elem_gpa_os_pos"] = ($rs["gpa_os"]=="Possible") ? "Possible" : ""; 
				$arr["elem_gpa_os_lp"] = ($rs["gpa_os"]=="Like Progression") ? "Like Progression" : ""; 

				$arr["elem_interpret_systhesis_os"] = $rs["synthesis_os"];
				//--
				
				$elem_comments = stripslashes($rs["comments"]);
				$ar_elem_comments = explode("!~!",$elem_comments);
				$elem_comments_od = $ar_elem_comments[0];
				$elem_comments_os = $ar_elem_comments[1];
				$arr["elem_comments_od"] = $elem_comments_od;
				$arr["elem_comments_os"] = $elem_comments_os;
			}
		}	

		$str = $this->getJson($arr,$ar2,$ctid,$cedt);
		return $str;		
	}
	
	private function getGDX($dt,$tm="",$tid,$dir="Prev",$dc=1){
		$arr = $ar2 = array();
		$ctid=$cedt=$cetm="";		
		$ctid = -1;
		$cedt = "0000-00-00";
		$arr["Date1"] = $dt;
		
		$sql = $this->getQry(1, $dir,$tid, $dt,$dc);
		$res = imw_query($sql);
		if($res){
			while($rs = imw_fetch_assoc($res)){
				
				$ctid = $rs["gdx_id"];
				$cedt = $arr["elem_examDate"] = $rs["examDate"];
				//$cetm = $arr["elem_examTime"] = $rs["examTime"];
				//Tech Comments
				$ar2["Tech.Comments"]=$arr["techComment"] = $rs["techComments"];				

				//Date
				$arr["Date1"] = $rs["exdt2"];
				
				//Normal --
				$arr["Normal_OD_T"]=(strpos($rs["normal_OD"], "Normal")!==false) ? "Normal" : "";
				$arr["elem_normal_poorStudy_od"]=(strpos($rs["normal_OD"], "Poor Study")!==false) ? "Poor Study":"";
				$arr["Normal_OS_T"]= (strpos($rs["normal_OS"], "Normal")!==false) ? "Normal" : "" ;
				$arr["elem_normal_poorStudy_os"]= (strpos($rs["normal_OS"], "Poor Study")!==false) ? "Poor Study" : "";
				
				$td = $ts = "";
				if(!empty($rs["normal_OD"])) $td = "".$rs["normal_OD"];
				if(!empty($rs["normal_OS"])) $ts = "".$rs["normal_OS"];
				$ar2["Normal"]=array("OD"=>$td,"OS"=>$ts);
				//Normal --

				//Nerve Fiber Thickness Map --
				$arr["elem_normal_app_OD"]= (strpos($rs["nf_Thick_OD"], "Normal Appearing Nerve Fiber Layer")!==false) ? "Normal Appearing Nerve Fiber Layer" : "";
				$arr["elem_sus_nrv_OD"]= (strpos($rs["nf_Thick_OD"], "Suspicious Nerve Fiber Layer Thinning")!==false) ? "Suspicious Nerve Fiber Layer Thinning" : "";
				$arr["elem_def_nrv_OD"]= (strpos($rs["nf_Thick_OD"], "Definite Nerve Fiber Layer Thinning")!==false) ? "Definite Nerve Fiber Layer Thinning" : "";
				$arr["elem_normal_app_OS"]= (strpos($rs["nf_Thick_OS"], "Normal Appearing Nerve Fiber Layer")!==false) ? "Normal Appearing Nerve Fiber Layer" : "";
				$arr["elem_sus_nrv_OS"]= (strpos($rs["nf_Thick_OS"], "Suspicious Nerve Fiber Layer Thinning")!==false) ? "Suspicious Nerve Fiber Layer Thinning" : "";
				$arr["elem_def_nrv_OS"]= (strpos($rs["nf_Thick_OS"], "Definite Nerve Fiber Layer Thinning")!==false) ? "Definite Nerve Fiber Layer Thinning" : "";
				
				$td = $ts = "";
				if(!empty($rs["nf_Thick_OD"])) $td .= str_replace(",","<br/>",$rs["nf_Thick_OD"]);
				if(!empty($rs["nf_Thick_OS"])) $ts .= str_replace(",","<br/>",$rs["nf_Thick_OS"]);				
				$ar2["Nerve Fiber Thickness Map"]=array("OD"=>$td,"OS"=>$ts);

				//Quadrant Deviation Map Outside Normal--
				$arr["elem_sus_quad_OD"]= (strpos($rs["quad_devi_OD"], "Superior Quardrant")!==false) ? "Superior Quardrant" : "";
				$arr["elem_nas_quad_OD"]= (strpos($rs["quad_devi_OD"], "Nasal Quadrant")!==false) ? "Nasal Quadrant" : "";
				$arr["elem_temp_quad_OD"]= (strpos($rs["quad_devi_OD"], "Temporal Quadrant")!==false) ? "Temporal Quadrant" : "";
				$arr["elem_inf_quad_OD"]= (strpos($rs["quad_devi_OD"], "Inferior Quadrant")!==false) ? "Inferior Quadrant" : "";
				
				$arr["elem_sus_quad_OS"]= (strpos($rs["quad_devi_OS"], "Superior Quardrant")!==false) ? "Superior Quardrant" : "";
				$arr["elem_nas_quad_OS"]= (strpos($rs["quad_devi_OS"], "Nasal Quadrant")!==false) ? "Nasal Quadrant" : "";
				$arr["elem_temp_quad_OS"]= (strpos($rs["quad_devi_OS"], "Temporal Quadrant")!==false) ? "Temporal Quadrant" : "";
				$arr["elem_inf_quad_OS"]= (strpos($rs["quad_devi_OS"], "Inferior Quadrant")!==false) ? "Inferior Quadrant" : "";
				
				$td = $ts = "";
				if(!empty($rs["quad_devi_OD"])) $td = str_replace(",","<br/>",$rs["quad_devi_OD"]);
				if(!empty($rs["quad_devi_OS"])) $ts = str_replace(",","<br/>",$rs["quad_devi_OS"]);
				$ar2["Quadrant Deviation Map Outside Normal"]=array("OD"=>$td,"OS"=>$ts);
				
				//Nerve Fiber Indicator --
				$arr["elem_30_normal_OD"]= (strpos($rs["nf_Indic_OD"], "0-30 Normal (Low risk of Glaucoma)")!==false) ?  "0-30 Normal (Low risk of Glaucoma)" : "";
				$arr["elem_50_normal_OD"]= (strpos($rs["nf_Indic_OD"], "31-50 Borderline")!==false) ? "31-50 Borderline" : "";
				$arr["elem_51_normal_OD"]= (strpos($rs["nf_Indic_OD"], "51+ (Abnormal risk of Glaucoma)")!==false) ?  "51+ (Abnormal risk of Glaucoma)" : "";
				$arr["elem_30_normal_OS"]= (strpos($rs["nf_Indic_OS"], "0-30 Normal (Low risk of Glaucoma)")!==false) ? "0-30 Normal (Low risk of Glaucoma)" : "";
				$arr["elem_50_normal_OS"]= (strpos($rs["nf_Indic_OS"], "31-50 Borderline")!==false) ? "31-50 Borderline" : "";
				$arr["elem_51_normal_OS"]= (strpos($rs["nf_Indic_OS"], "51+ (Abnormal risk of Glaucoma)")!==false) ? "51+ (Abnormal risk of Glaucoma)" : "";
				
				$td = $ts = "";
				if(!empty($rs["nf_Indic_OD"])) $td = str_replace(",","<br/>",$rs["nf_Indic_OD"]);
				if(!empty($rs["nf_Indic_OS"])) $ts = str_replace(",","<br/>",$rs["nf_Indic_OS"]);
				$ar2["Nerve Fiber Indicator"]=array("OD"=>$td,"OS"=>$ts);			
				
				$td .= "<br/>";
				
				/*
				//Iop Target --
				$arr["elem_targetIop_OD"] = $rs["iopTrgtOd"];
				$arr["elem_targetIop_OS"] = $rs["iopTrgtOs"];
				$ar2["IOP Target"]=array("OD"=>$arr["elem_targetIop_OD"],"OS"=>$arr["elem_targetIop_OS"]);
				//Iop Target --
				*/
				
				//Other --
				$arr["Others_OD"] = stripslashes($rs["others_OD"]);
				$arr["Others_OS"] = stripslashes($rs["others_OS"]);
				$ar2["Other"]=array("OD"=>$arr["Others_OD"],"OS"=>$arr["Others_OS"]);
				//Other --

				//Comments
				$ar2["Comments"]=$arr["elem_comments"] = $rs["comments"];
			}
		}	
		$str = $this->getJson($arr,$ar2,$ctid,$cedt);
		return $str;		
	}
	
	private function getPACHY($dt,$tm="",$tid,$dir="Prev",$dc=1){
		$arr = $ar2 = array();
		$ctid=$cedt=$cetm="";		
		$ctid = -1;
		$cedt = "0000-00-00";
		$arr["Date1"] = $dt;
		
		$sql = $this->getQry(1, $dir,$tid, $dt,$dc);
		$res = imw_query($sql);
		if($res){
			while($rs = imw_fetch_assoc($res)){
				$ctid = $rs["pachy_id"];
				$cedt = $arr["elem_examDate"] = $rs["examDate"];
				//$cetm = $arr["elem_examTime"] = $rs["examTime"];
				//Tech Comments
				$ar2["Tech.Comments"]=$arr["techComment"] = $rs["techComments"];
				
				//Date
				$arr["Date1"] = $rs["exdt2"];
				
				// Pachy --
				$arr["Central_OD"] = $rs["Central_OD"];				
				$arr["Nasal_OD"] = $rs["Nasal_OD"];
				$arr["Central_OS"] = $rs["Central_OS"];
				$arr["Nasal_OS"] = $rs["Nasal_OS"];
				
				$td = $ts = "";
				if(!empty($arr["Central_OD"])) $td .= "Central ";
				if(!empty($arr["Central_OS"])) $ts .= "Central ";
				if(!empty($arr["Nasal_OD"])) $td .= "Nasal ";
				if(!empty($arr["Nasal_OS"])) $ts .= "Nasal ";

				$arr["elem_pachy_od_readings"] = $rs["pachy_od_readings"];
				$arr["elem_pachy_od_average"] = $rs["pachy_od_average"];
				$arr["elem_pachy_od_correction_value"] = $rs["pachy_od_correction_value"];
				$arr["elem_pachy_os_readings"] = $rs["pachy_os_readings"];
				$arr["elem_pachy_os_average"] = $rs["pachy_os_average"];
				$arr["elem_pachy_os_correction_value"] = $rs["pachy_os_correction_value"];

				$td .= $arr["elem_pachy_od_readings"]." ";
				$td .= $arr["elem_pachy_od_average"]." ";
				$td .= $arr["elem_pachy_od_correction_value"]." ";
				$ts .= $arr["elem_pachy_os_readings"]." ";
				$ts .= $arr["elem_pachy_os_average"]." ";
				$ts .= $arr["elem_pachy_os_correction_value"]." ";				

				$arr["Inferior_OD"] = $rs["Inferior_OD"];
				$arr["Temporal_OD"] = $rs["Temporal_OD"];
				$arr["Superior_OD"] = $rs["Superior_OD"];
				$arr["Inferior_OS"] = $rs["Inferior_OS"];
				$arr["Temporal_OS"] = $rs["Temporal_OS"];
				$arr["Superior_OS"] = $rs["Superior_OS"];
				
				$td .= "<br/>";
				$ts .= "<br/>";

				if(!empty($arr["Inferior_OD"])) $td .= "Inferior ";
				if(!empty($arr["Temporal_OD"])) $td .= "Temporal ";
				if(!empty($arr["Superior_OD"])) $td .= "Superior ";
				if(!empty($arr["Inferior_OS"])) $ts .= "Inferior ";
				if(!empty($arr["Temporal_OS"])) $ts .= "Temporal ";
				if(!empty($arr["Superior_OS"])) $ts .= "Superior ";
				$ar2["Pachy"]=array("OD"=>$td,"OS"=>$ts);
				// Pachy --
				
				//--
				$arr["elem_iris_iridec_od"] = stripslashes($rs["iris_iridec_od"]);
				$arr["elem_iris_iridec_os"] = stripslashes($rs["iris_iridec_os"]);
				$ar2["Iris/Iridectomy"]=array("OD"=>$arr["elem_iris_iridec_od"],"OS"=>$arr["elem_iris_iridec_os"]);
				//--

				//Description --
				$arr["elem_descOd"] = stripslashes($rs["descOd"]);
				$arr["elem_descOs"] = stripslashes($rs["descOs"]);
				$ar2["Description"]=array("OD"=>$arr["elem_descOd"],"OS"=>$arr["elem_descOs"]);				
				//Description --

				//Comments
				$ar2["Comments"]=$arr["elem_comments"]	= $rs["comments"];
			}
		}
		$str = $this->getJson($arr,$ar2,$ctid,$cedt);
		return $str;
	
	}
	private function getIVFA($dt,$tm="",$tid,$dir="Prev",$dc=1){
		$arr = $ar2 = array();
		$ctid=$cedt=$cetm="";		
		$ctid = -1;
		$cedt = "0000-00-00";
		$arr["Date1"] = $dt;
		
		$sql = $this->getQry(1, $dir,$tid, $dt,$dc);
		$res = imw_query($sql);
		if($res){
			while($rs = imw_fetch_assoc($res)){
				$ctid = $rs["vf_id"];
				$cedt = $arr["elem_examDate"] = $rs["exam_date"];
				//$cetm = $arr["elem_examTime"] = $rs["examTime"];
				//Tech Comments
				$ar2["Tech.Comments"]=$arr["techComment"] = $rs["comments_ivfa"];
				
				//Date
				$arr["Date1"] = $rs["exdt2"];

				//Disc --
				$arr["Sharp_Pink_OD"] =$rs["Sharp_Pink_OD"];
				$arr["Pale_OD"] =$rs["Pale_OD"];
				$arr["Large_Cap_OD"] =$rs["Large_Cap_OD"];
				$arr["Sloping_OD"] =$rs["Sloping_OD"];
				$arr["Notch_OD"] =$rs["Notch_OD"];
				$arr["NVD_OD"] =$rs["NVD_OD"];
				$arr["Leakage_OD"] =$rs["Leakage_OD"];

				$arr["Sharp_Pink_OS"] =$rs["Sharp_Pink_OS"];
				$arr["Pale_OS"] =$rs["Pale_OS"];
				$arr["Large_Cap_OS"] =$rs["Large_Cap_OS"];
				$arr["Sloping_OS"] =$rs["Sloping_OS"];
				$arr["Notch_OS"] =$rs["Notch_OS"];
				$arr["NVD_OS"] =$rs["NVD_OS"];
				$arr["Leakage_OS"] =$rs["Leakage_OS"];

				$td = $ts = "";
				$ta = array("Sharp_Pink"=>"Sharp & Pink","Pale"=>"Pale","Large_Cap"=>"Large Cap",
							"Sloping"=>"Sloping","Notch"=>"Notch","NVD"=>"NVD",
							"Leakage"=>"Leakage");
				foreach($ta as $key => $val){
					if($arr[$key."_OD"] == "1") $td .= $val.", ";
					if($arr[$key."_OS"] == "1") $ts .= $val.", ";
				}
				$ar2["Disc"]=array("OD"=>$td,"OS"=>$ts);
				//Disc --

				//Retina --
				$arr["Retina_Hemorrhage_OD"] =$rs["Retina_Hemorrhage_OD"];
				$arr["Retina_Microaneurysms_OD"] =$rs["Retina_Microaneurysms_OD"];
				$arr["Retina_Exudates_OD"] =$rs["Retina_Exudates_OD"];
				$arr["Retina_Laser_Scars_OD"] =$rs["Retina_Laser_Scars_OD"];
				$arr["Retina_NEVI_OD"] =$rs["Retina_NEVI_OD"];
				$arr["Retina_SRVNM_OD"] =$rs["Retina_SRVNM_OD"];
				$arr["Retina_Edema_OD"] =$rs["Retina_Edema_OD"];
				$arr["Retina_Nevus_OD"] =$rs["Retina_Nevus_OD"];
				
				$arr["Retina_Ischemia_OD"] =$rs["Retina_Ischemia_OD"];
				$arr["Retina_BRVO_OD"] =$rs["Retina_BRVO_OD"];
				$arr["Retina_CRVO_OD"] =$rs["Retina_CRVO_OD"];
				
				$arr["Retina_BDR_OD_T"] =$rs["Retina_BDR_OD_T"];
				$arr["Retina_BDR_OD_1"] =$rs["Retina_BDR_OD_1"];
				$arr["Retina_BDR_OD_2"] =$rs["Retina_BDR_OD_2"];
				$arr["Retina_BDR_OD_3"] =$rs["Retina_BDR_OD_3"];
				$arr["Retina_BDR_OD_4"] =$rs["Retina_BDR_OD_4"];
				$arr["Retina_Druse_OD_T"] =$rs["Retina_Druse_OD_T"];
				$arr["Retina_Druse_OD_1"] =$rs["Retina_Druse_OD_1"];
				$arr["Retina_Druse_OD_2"] =$rs["Retina_Druse_OD_2"];
				$arr["Retina_Druse_OD_3"] =$rs["Retina_Druse_OD_3"];
				$arr["Retina_Druse_OD_4"] =$rs["Retina_Druse_OD_4"];
				$arr["Retina_RPE_Change_OD_T"] =$rs["Retina_RPE_Change_OD_T"];
				$arr["Retina_RPE_Change_OD_1"] =$rs["Retina_RPE_Change_OD_1"];
				$arr["Retina_RPE_Change_OD_2"] =$rs["Retina_RPE_Change_OD_2"];
				$arr["Retina_RPE_Change_OD_3"] =$rs["Retina_RPE_Change_OD_3"];
				$arr["Retina_RPE_Change_OD_4"] =$rs["Retina_RPE_Change_OD_4"];

				$arr["Retina_Hemorrhage_OS"] =$rs["Retina_Hemorrhage_OS"];
				$arr["Retina_Microaneurysms_OS"] =$rs["Retina_Microaneurysms_OS"];
				$arr["Retina_Exudates_OS"] =$rs["Retina_Exudates_OS"];
				$arr["Retina_Laser_Scars_OS"] =$rs["Retina_Laser_Scars_OS"];
				$arr["Retina_NEVI_OS"] =$rs["Retina_NEVI_OS"];
				$arr["Retina_SRVNM_OS"] =$rs["Retina_SRVNM_OS"];
				$arr["Retina_Edema_OS"] =$rs["Retina_Edema_OS"];
				$arr["Retina_Nevus_OS"] =$rs["Retina_Nevus_OS"];

				$arr["Retina_Ischemia_OS"] =$rs["Retina_Ischemia_OS"];				
				$arr["Retina_BRVO_OS"] =$rs["Retina_BRVO_OS"];
				$arr["Retina_CRVO_OS"] =$rs["Retina_CRVO_OS"];
				
				$arr["Retina_BDR_OS_T"] =$rs["Retina_BDR_OS_T"];
				$arr["Retina_BDR_OS_1"] =$rs["Retina_BDR_OS_1"];
				$arr["Retina_BDR_OS_2"] =$rs["Retina_BDR_OS_2"];
				$arr["Retina_BDR_OS_3"] =$rs["Retina_BDR_OS_3"];
				$arr["Retina_BDR_OS_4"] =$rs["Retina_BDR_OS_4"];
				$arr["Retina_Druse_OS_T"] =$rs["Retina_Druse_OS_T"];
				$arr["Retina_Druse_OS_1"] =$rs["Retina_Druse_OS_1"];
				$arr["Retina_Druse_OS_2"] =$rs["Retina_Druse_OS_2"];
				$arr["Retina_Druse_OS_3"] =$rs["Retina_Druse_OS_3"];
				$arr["Retina_Druse_OS_4"] =$rs["Retina_Druse_OS_4"];
				$arr["Retina_RPE_Change_OS_T"] =$rs["Retina_RPE_Change_OS_T"];
				$arr["Retina_RPE_Change_OS_1"] =$rs["Retina_RPE_Change_OS_1"];
				$arr["Retina_RPE_Change_OS_2"] =$rs["Retina_RPE_Change_OS_2"];
				$arr["Retina_RPE_Change_OS_3"] =$rs["Retina_RPE_Change_OS_3"];
				$arr["Retina_RPE_Change_OS_4"] =$rs["Retina_RPE_Change_OS_4"];

				
				$td = $ts = "";
				$ta = array("Hemorrhage"=>"Hemorrhage","Microaneurysms"=>"Microaneurysms",
							"Exudates"=>"Exudates","Laser_Scars"=>"Laser Scars",
							"NEVI"=>"NEVI","SRVNM"=>"SRVNM",
							"Edema"=>"Edema","Nevus"=>"Nevus","Ischemia"=>"Ischemia","BRVO"=>"BRVO","CRVO"=>"CRVO");
				foreach($ta as $key => $val){
					if($arr["Retina_".$key."_OD"] == "1") $td .= $val.", ";
					if($arr["Retina_".$key."_OS"] == "1") $ts .= $val.", ";
				}
				
				$td .= "<br/>"; 
				$ts .= "<br/>";
				//BDR --
				$ta = array("BDR"=>"BDR","Druse"=>"Druse","RPE_Change"=>"RPE Change");
				foreach($ta as $key=>$val){
					$bd=$bs="";
					if(!empty($arr["Retina_".$key."_OD_T"])) $bd .= "T ";
					if(!empty($arr["Retina_".$key."_OS_T"])) $bs .= "T ";
					for($i=1;$i<=4;$i++){
						if(!empty($arr["Retina_".$key."_OD_".$i])) $bd .= "+".$i." ";
						if(!empty($arr["Retina_".$key."_OD_".$i])) $bs .= "+".$i." ";
					}	
					if(!empty($bd) || !empty($bs)){
						$td .= $val." ".$bd."<br/>"; 
						$ts .= $val." ".$bs."<br/>";
					}
				}
				//BDR --
				
				$ar2["Retina"]=array("OD"=>$td,"OS"=>$ts);				
				//Retina --
				
				//Macula --
				$arr["Druse_OD"] =$rs["Druse_OD"];
				$arr["RPE_Changes_OD"] =$rs["RPE_Changes_OD"];
				$arr["SRNVM_OD"] =$rs["SRNVM_OD"];
				$arr["Edema_OD"] =$rs["Edema_OD"];
				$arr["Scars_OD"] =$rs["Scars_OD"];
				$arr["Hemorrhage_OD"] =$rs["Hemorrhage_OD"];
				$arr["Microaneurysms_OD"] =$rs["Microaneurysms_OD"];
				$arr["Exudates_OD"] =$rs["Exudates_OD"];
				$arr["Macula_BDR_OD_T"] =$rs["Macula_BDR_OD_T"];
				$arr["Macula_BDR_OD_1"] =$rs["Macula_BDR_OD_1"];
				$arr["Macula_BDR_OD_2"] =$rs["Macula_BDR_OD_2"];
				$arr["Macula_BDR_OD_3"] =$rs["Macula_BDR_OD_3"];
				$arr["Macula_BDR_OD_4"] =$rs["Macula_BDR_OD_4"];
				$arr["Macula_SMD_OD_T"] =$rs["Macula_SMD_OD_T"];
				$arr["Macula_SMD_OD_1"] =$rs["Macula_SMD_OD_1"];
				$arr["Macula_SMD_OD_2"] =$rs["Macula_SMD_OD_2"];
				$arr["Macula_SMD_OD_3"] =$rs["Macula_SMD_OD_3"];
				$arr["Macula_SMD_OD_4"] =$rs["Macula_SMD_OD_4"];
				
				$arr["SR_Heme_OD"] =$rs["SR_Heme_OD"];				
				$arr["Classic_CNV_OD"] =$rs["Classic_CNV_OD"];				
				$arr["Occult_CNV_OD"] =$rs["Occult_CNV_OD"];				

				$arr["Druse_OS"] =$rs["Druse_OS"];
				$arr["RPE_Changes_OS"] =$rs["RPE_Changes_OS"];
				$arr["SRNVM_OS"] =$rs["SRNVM_OS"];
				$arr["Edema_OS"] =$rs["Edema_OS"];
				$arr["Scars_OS"] =$rs["Scars_OS"];
				$arr["Hemorrhage_OS"] =$rs["Hemorrhage_OS"];
				$arr["Microaneurysms_OS"] =$rs["Microaneurysms_OS"];
				$arr["Exudates_OS"] =$rs["Exudates_OS"];
				$arr["Macula_BDR_OS_T"] =$rs["Macula_BDR_OS_T"];
				$arr["Macula_BDR_OS_1"] =$rs["Macula_BDR_OS_1"];
				$arr["Macula_BDR_OS_2"] =$rs["Macula_BDR_OS_2"];
				$arr["Macula_BDR_OS_3"] =$rs["Macula_BDR_OS_3"];
				$arr["Macula_BDR_OS_4"] =$rs["Macula_BDR_OS_4"];
				$arr["Macula_SMD_OS_T"] =$rs["Macula_SMD_OS_T"];
				$arr["Macula_SMD_OS_1"] =$rs["Macula_SMD_OS_1"];
				$arr["Macula_SMD_OS_2"] =$rs["Macula_SMD_OS_2"];
				$arr["Macula_SMD_OS_3"] =$rs["Macula_SMD_OS_3"];
				$arr["Macula_SMD_OS_4"] =$rs["Macula_SMD_OS_4"];
				
				
				$arr["PED_OD"]=$rs["PED_OD"];
				$arr["PED_OS"]=$rs["PED_OS"];
				
				$arr["SR_Heme_OS"]=$rs["SR_Heme_OS"];
				$arr["Classic_CNV_OS"]=$rs["Classic_CNV_OS"];
				$arr["Occult_CNV_OS"]=$rs["Occult_CNV_OS"];
				
				$td = $ts = "";
				$ta=array("Druse"=>"Druse","RPE_Changes"=>"RPE Changes","SRNVM"=>"SRNVM","Edema"=>"Edema","Scars"=>"Scars",
						  "Hemorrhage"=>"Hemorrhage","Microaneurysms"=>"Microaneurysms","Exudates"=>"Exudates","PED"=>"PED",
						  "SR_Heme"=>"SR_Heme","Classic_CNV"=>"Classic_CNV","Occult_CNV"=>"Occult_CNV");
				foreach($ta as $key => $val){
					if($arr["".$key."_OD"] == "1") $td .= $val.", ";
					if($arr["".$key."_OS"] == "1") $ts .= $val.", ";
				}
				$td .= "<br/>"; 
				$ts .= "<br/>";

				//BDR --
				$ta = array("BDR"=>"BDR","SMD"=>"SMD");
				foreach($ta as $key=>$val){
					$bd=$bs="";
					if(!empty($arr["Macula_".$key."_OD_T"])) $bd .= "T ";
					if(!empty($arr["Macula_".$key."_OS_T"])) $bs .= "T ";
					for($i=1;$i<=4;$i++){
						if(!empty($arr["Macula_".$key."_OD_".$i])) $bd .= "+".$i." ";
						if(!empty($arr["Macula_".$key."_OD_".$i])) $bs .= "+".$i." ";
					}	
					if(!empty($bd) || !empty($bs)){
						$td .= $val." ".$bd."<br/>"; 
						$ts .= $val." ".$bs."<br/>";
					}
				}
				//BDR --

				$ar2["Macula"]=array("OD"=>$td,"OS"=>$ts);

				//Macula --

				//Other --
				$arr["elem_testresults_desc_od"]=stripslashes($rs["testresults_desc_od"]);
				$arr["elem_testresults_desc_os"]=stripslashes($rs["testresults_desc_os"]);
				$ar2["Other"]=array("OD"=>$arr["elem_testresults_desc_od"],"OS"=>$arr["elem_testresults_desc_os"]);
				//Other --

				//Comments
				$ar2["Comments"]=$arr["ivfaComments"]	= $rs["ivfaComments"];
			}
		}
		$str = $this->getJson($arr,$ar2,$ctid,$cedt);
		return $str;
	
	}
	
	private function getICG($dt,$tm="",$tid,$dir="Prev",$dc=1){
		$arr = $ar2 = array();
		$ctid=$cedt=$cetm="";		
		$ctid = -1;
		$cedt = "0000-00-00";
		$arr["Date1"] = $dt;
		
		$sql = $this->getQry(1, $dir,$tid, $dt,$dc);
		$res = imw_query($sql);
		if($res){
			while($rs = imw_fetch_assoc($res)){
				$ctid = $rs["vf_id"];
				$cedt = $arr["elem_examDate"] = $rs["exam_date"];
				//$cetm = $arr["elem_examTime"] = $rs["examTime"];
				//Tech Comments
				$ar2["Tech.Comments"]=$arr["techComment"] = $rs["comments_icg"];
				
				//Date
				$arr["Date1"] = $rs["exdt2"];

				//Disc --
				$arr["Sharp_Pink_OD"] =$rs["Sharp_Pink_OD"];
				$arr["Pale_OD"] =$rs["Pale_OD"];
				$arr["Large_Cap_OD"] =$rs["Large_Cap_OD"];
				$arr["Sloping_OD"] =$rs["Sloping_OD"];
				$arr["Notch_OD"] =$rs["Notch_OD"];
				$arr["NVD_OD"] =$rs["NVD_OD"];
				$arr["Leakage_OD"] =$rs["Leakage_OD"];

				$arr["Sharp_Pink_OS"] =$rs["Sharp_Pink_OS"];
				$arr["Pale_OS"] =$rs["Pale_OS"];
				$arr["Large_Cap_OS"] =$rs["Large_Cap_OS"];
				$arr["Sloping_OS"] =$rs["Sloping_OS"];
				$arr["Notch_OS"] =$rs["Notch_OS"];
				$arr["NVD_OS"] =$rs["NVD_OS"];
				$arr["Leakage_OS"] =$rs["Leakage_OS"];

				$td = $ts = "";
				$ta = array("Sharp_Pink"=>"Sharp & Pink","Pale"=>"Pale","Large_Cap"=>"Large Cap",
							"Sloping"=>"Sloping","Notch"=>"Notch","NVD"=>"NVD",
							"Leakage"=>"Leakage");
				foreach($ta as $key => $val){
					if($arr[$key."_OD"] == "1") $td .= $val.", ";
					if($arr[$key."_OS"] == "1") $ts .= $val.", ";
				}
				$ar2["Disc"]=array("OD"=>$td,"OS"=>$ts);
				//Disc --

				//Retina --
				$arr["Retina_Hemorrhage_OD"] =$rs["Retina_Hemorrhage_OD"];
				$arr["Retina_Microaneurysms_OD"] =$rs["Retina_Microaneurysms_OD"];
				$arr["Retina_Exudates_OD"] =$rs["Retina_Exudates_OD"];
				$arr["Retina_Laser_Scars_OD"] =$rs["Retina_Laser_Scars_OD"];
				$arr["Retina_NEVI_OD"] =$rs["Retina_NEVI_OD"];
				$arr["Retina_SRVNM_OD"] =$rs["Retina_SRVNM_OD"];
				$arr["Retina_Edema_OD"] =$rs["Retina_Edema_OD"];
				$arr["Retina_Nevus_OD"] =$rs["Retina_Nevus_OD"];
				
				$arr["Retina_Ischemia_OD"] =$rs["Retina_Ischemia_OD"];
				$arr["Retina_BRVO_OD"] =$rs["Retina_BRVO_OD"];
				$arr["Retina_CRVO_OD"] =$rs["Retina_CRVO_OD"];
				
				$arr["Retina_BDR_OD_T"] =$rs["Retina_BDR_OD_T"];
				$arr["Retina_BDR_OD_1"] =$rs["Retina_BDR_OD_1"];
				$arr["Retina_BDR_OD_2"] =$rs["Retina_BDR_OD_2"];
				$arr["Retina_BDR_OD_3"] =$rs["Retina_BDR_OD_3"];
				$arr["Retina_BDR_OD_4"] =$rs["Retina_BDR_OD_4"];
				$arr["Retina_Druse_OD_T"] =$rs["Retina_Druse_OD_T"];
				$arr["Retina_Druse_OD_1"] =$rs["Retina_Druse_OD_1"];
				$arr["Retina_Druse_OD_2"] =$rs["Retina_Druse_OD_2"];
				$arr["Retina_Druse_OD_3"] =$rs["Retina_Druse_OD_3"];
				$arr["Retina_Druse_OD_4"] =$rs["Retina_Druse_OD_4"];
				$arr["Retina_RPE_Change_OD_T"] =$rs["Retina_RPE_Change_OD_T"];
				$arr["Retina_RPE_Change_OD_1"] =$rs["Retina_RPE_Change_OD_1"];
				$arr["Retina_RPE_Change_OD_2"] =$rs["Retina_RPE_Change_OD_2"];
				$arr["Retina_RPE_Change_OD_3"] =$rs["Retina_RPE_Change_OD_3"];
				$arr["Retina_RPE_Change_OD_4"] =$rs["Retina_RPE_Change_OD_4"];

				$arr["Retina_Hemorrhage_OS"] =$rs["Retina_Hemorrhage_OS"];
				$arr["Retina_Microaneurysms_OS"] =$rs["Retina_Microaneurysms_OS"];
				$arr["Retina_Exudates_OS"] =$rs["Retina_Exudates_OS"];
				$arr["Retina_Laser_Scars_OS"] =$rs["Retina_Laser_Scars_OS"];
				$arr["Retina_NEVI_OS"] =$rs["Retina_NEVI_OS"];
				$arr["Retina_SRVNM_OS"] =$rs["Retina_SRVNM_OS"];
				$arr["Retina_Edema_OS"] =$rs["Retina_Edema_OS"];
				$arr["Retina_Nevus_OS"] =$rs["Retina_Nevus_OS"];

				$arr["Retina_Ischemia_OS"] =$rs["Retina_Ischemia_OS"];				
				$arr["Retina_BRVO_OS"] =$rs["Retina_BRVO_OS"];
				$arr["Retina_CRVO_OS"] =$rs["Retina_CRVO_OS"];
				
				$arr["Retina_BDR_OS_T"] =$rs["Retina_BDR_OS_T"];
				$arr["Retina_BDR_OS_1"] =$rs["Retina_BDR_OS_1"];
				$arr["Retina_BDR_OS_2"] =$rs["Retina_BDR_OS_2"];
				$arr["Retina_BDR_OS_3"] =$rs["Retina_BDR_OS_3"];
				$arr["Retina_BDR_OS_4"] =$rs["Retina_BDR_OS_4"];
				$arr["Retina_Druse_OS_T"] =$rs["Retina_Druse_OS_T"];
				$arr["Retina_Druse_OS_1"] =$rs["Retina_Druse_OS_1"];
				$arr["Retina_Druse_OS_2"] =$rs["Retina_Druse_OS_2"];
				$arr["Retina_Druse_OS_3"] =$rs["Retina_Druse_OS_3"];
				$arr["Retina_Druse_OS_4"] =$rs["Retina_Druse_OS_4"];
				$arr["Retina_RPE_Change_OS_T"] =$rs["Retina_RPE_Change_OS_T"];
				$arr["Retina_RPE_Change_OS_1"] =$rs["Retina_RPE_Change_OS_1"];
				$arr["Retina_RPE_Change_OS_2"] =$rs["Retina_RPE_Change_OS_2"];
				$arr["Retina_RPE_Change_OS_3"] =$rs["Retina_RPE_Change_OS_3"];
				$arr["Retina_RPE_Change_OS_4"] =$rs["Retina_RPE_Change_OS_4"];

				
				$td = $ts = "";
				$ta = array("Hemorrhage"=>"Hemorrhage","Microaneurysms"=>"Microaneurysms",
							"Exudates"=>"Exudates","Laser_Scars"=>"Laser Scars",
							"NEVI"=>"NEVI","SRVNM"=>"SRVNM",
							"Edema"=>"Edema","Nevus"=>"Nevus","Ischemia"=>"Ischemia","BRVO"=>"BRVO","CRVO"=>"CRVO");
				foreach($ta as $key => $val){
					if($arr["Retina_".$key."_OD"] == "1") $td .= $val.", ";
					if($arr["Retina_".$key."_OS"] == "1") $ts .= $val.", ";
				}
				
				$td .= "<br/>"; 
				$ts .= "<br/>";
				//BDR --
				$ta = array("BDR"=>"BDR","Druse"=>"Druse","RPE_Change"=>"RPE Change");
				foreach($ta as $key=>$val){
					$bd=$bs="";
					if(!empty($arr["Retina_".$key."_OD_T"])) $bd .= "T ";
					if(!empty($arr["Retina_".$key."_OS_T"])) $bs .= "T ";
					for($i=1;$i<=4;$i++){
						if(!empty($arr["Retina_".$key."_OD_".$i])) $bd .= "+".$i." ";
						if(!empty($arr["Retina_".$key."_OD_".$i])) $bs .= "+".$i." ";
					}	
					if(!empty($bd) || !empty($bs)){
						$td .= $val." ".$bd."<br/>"; 
						$ts .= $val." ".$bs."<br/>";
					}
				}
				//BDR --
				
				$ar2["Retina"]=array("OD"=>$td,"OS"=>$ts);				
				//Retina --
				
				//Macula --
				$arr["Druse_OD"] =$rs["Druse_OD"];
				$arr["RPE_Changes_OD"] =$rs["RPE_Changes_OD"];
				$arr["SRNVM_OD"] =$rs["SRNVM_OD"];
				$arr["Edema_OD"] =$rs["Edema_OD"];
				$arr["Scars_OD"] =$rs["Scars_OD"];
				$arr["Hemorrhage_OD"] =$rs["Hemorrhage_OD"];
				$arr["Microaneurysms_OD"] =$rs["Microaneurysms_OD"];
				$arr["Exudates_OD"] =$rs["Exudates_OD"];
				$arr["Macula_BDR_OD_T"] =$rs["Macula_BDR_OD_T"];
				$arr["Macula_BDR_OD_1"] =$rs["Macula_BDR_OD_1"];
				$arr["Macula_BDR_OD_2"] =$rs["Macula_BDR_OD_2"];
				$arr["Macula_BDR_OD_3"] =$rs["Macula_BDR_OD_3"];
				$arr["Macula_BDR_OD_4"] =$rs["Macula_BDR_OD_4"];
				$arr["Macula_SMD_OD_T"] =$rs["Macula_SMD_OD_T"];
				$arr["Macula_SMD_OD_1"] =$rs["Macula_SMD_OD_1"];
				$arr["Macula_SMD_OD_2"] =$rs["Macula_SMD_OD_2"];
				$arr["Macula_SMD_OD_3"] =$rs["Macula_SMD_OD_3"];
				$arr["Macula_SMD_OD_4"] =$rs["Macula_SMD_OD_4"];
				
				$arr["Feeder_Vessel_OD"] =$rs["Feeder_Vessel_OD"];
				$arr["Central_OD"] =$rs["Central_OD"];
				$arr["Nasal_OD"] =$rs["Nasal_OD"];
				$arr["Temporal_OD"] =$rs["Temporal_OD"];
				$arr["Inferior_OD"] =$rs["Inferior_OD"];
				$arr["Superior_OD"] =$rs["Superior_OD"];
				$arr["Hot_Spot_OD"] =$rs["Hot_Spot_OD"];
				$arr["Hot_Spot_Val_OD"] =$rs["Hot_Spot_Val_OD"];
				
				$arr["SR_Heme_OD"] =$rs["SR_Heme_OD"];				
				$arr["Classic_CNV_OD"] =$rs["Classic_CNV_OD"];				
				$arr["Occult_CNV_OD"] =$rs["Occult_CNV_OD"];				

				$arr["Druse_OS"] =$rs["Druse_OS"];
				$arr["RPE_Changes_OS"] =$rs["RPE_Changes_OS"];
				$arr["SRNVM_OS"] =$rs["SRNVM_OS"];
				$arr["Edema_OS"] =$rs["Edema_OS"];
				$arr["Scars_OS"] =$rs["Scars_OS"];
				$arr["Hemorrhage_OS"] =$rs["Hemorrhage_OS"];
				$arr["Microaneurysms_OS"] =$rs["Microaneurysms_OS"];
				$arr["Exudates_OS"] =$rs["Exudates_OS"];
				$arr["Macula_BDR_OS_T"] =$rs["Macula_BDR_OS_T"];
				$arr["Macula_BDR_OS_1"] =$rs["Macula_BDR_OS_1"];
				$arr["Macula_BDR_OS_2"] =$rs["Macula_BDR_OS_2"];
				$arr["Macula_BDR_OS_3"] =$rs["Macula_BDR_OS_3"];
				$arr["Macula_BDR_OS_4"] =$rs["Macula_BDR_OS_4"];
				$arr["Macula_SMD_OS_T"] =$rs["Macula_SMD_OS_T"];
				$arr["Macula_SMD_OS_1"] =$rs["Macula_SMD_OS_1"];
				$arr["Macula_SMD_OS_2"] =$rs["Macula_SMD_OS_2"];
				$arr["Macula_SMD_OS_3"] =$rs["Macula_SMD_OS_3"];
				$arr["Macula_SMD_OS_4"] =$rs["Macula_SMD_OS_4"];
				
				$arr["Feeder_Vessel_OS"] =$rs["Feeder_Vessel_OS"];
				$arr["Central_OS"] =$rs["Central_OS"];
				$arr["Nasal_OS"] =$rs["Nasal_OS"];
				$arr["Temporal_OS"] =$rs["Temporal_OS"];
				$arr["Inferior_OS"] =$rs["Inferior_OS"];
				$arr["Superior_OS"] =$rs["Superior_OS"];
				$arr["Hot_Spot_OS"] =$rs["Hot_Spot_OS"];
				$arr["Hot_Spot_Val_OS"] =$rs["Hot_Spot_Val_OS"];
				
				$arr["PED_OD"]=$rs["PED_OD"];
				$arr["PED_OS"]=$rs["PED_OS"];
				
				$arr["SR_Heme_OS"]=$rs["SR_Heme_OS"];
				$arr["Classic_CNV_OS"]=$rs["Classic_CNV_OS"];
				$arr["Occult_CNV_OS"]=$rs["Occult_CNV_OS"];
				
				$td = $ts = "";
				$ta=array("Druse"=>"Druse","RPE_Changes"=>"RPE Changes","SRNVM"=>"SRNVM","Edema"=>"Edema","Scars"=>"Scars",
						  "Hemorrhage"=>"Hemorrhage","Microaneurysms"=>"Microaneurysms","Exudates"=>"Exudates","PED"=>"PED",
						  "SR_Heme"=>"SR_Heme","Classic_CNV"=>"Classic_CNV","Occult_CNV"=>"Occult_CNV"
						  ,"Feeder_Vessel"=>"Feeder Vessel","Central"=>"Central","Nasal"=>"Nasal","Temporal"=>"Temporal","Inferior"=>"Inferior","Superior"=>"Superior","Hot_Spot"=>"Hot_Spot"
						  );
				foreach($ta as $key => $val){
					if($arr["".$key."_OD"] == "1") $td .= $val.", ";
					if($arr["".$key."_OS"] == "1") $ts .= $val.", ";
				}
				$td .= "<br/>"; 
				$ts .= "<br/>";

				//BDR --
				$ta = array("BDR"=>"BDR","SMD"=>"SMD");
				foreach($ta as $key=>$val){
					$bd=$bs="";
					if(!empty($arr["Macula_".$key."_OD_T"])) $bd .= "T ";
					if(!empty($arr["Macula_".$key."_OS_T"])) $bs .= "T ";
					for($i=1;$i<=4;$i++){
						if(!empty($arr["Macula_".$key."_OD_".$i])) $bd .= "+".$i." ";
						if(!empty($arr["Macula_".$key."_OD_".$i])) $bs .= "+".$i." ";
					}	
					if(!empty($bd) || !empty($bs)){
						$td .= $val." ".$bd."<br/>"; 
						$ts .= $val." ".$bs."<br/>";
					}
				}
				//BDR --

				$ar2["Macula"]=array("OD"=>$td,"OS"=>$ts);

				//Macula --

				//Other --
				$arr["elem_testresults_desc_od"]=stripslashes($rs["testresults_desc_od"]);
				$arr["elem_testresults_desc_os"]=stripslashes($rs["testresults_desc_os"]);
				$ar2["Other"]=array("OD"=>$arr["elem_testresults_desc_od"],"OS"=>$arr["elem_testresults_desc_os"]);
				//Other --

				//Comments
				$ar2["Comments"]=$arr["icgComments"]	= $rs["icgComments"];
			}
		}
		$str = $this->getJson($arr,$ar2,$ctid,$cedt);
		return $str;
	}
		
	private function getFUNDUS($dt,$tm="",$tid,$dir="Prev",$dc=1){
		$arr = $ar2 = array();
		$ctid=$cedt=$cetm="";		
		$ctid = -1;
		$cedt = "0000-00-00";
		$arr["Date1"] = $dt;
		
		$sql = $this->getQry(1, $dir,$tid, $dt,$dc);
		$res = imw_query($sql);
		if($res){
			while($rs = imw_fetch_assoc($res)){
				$ctid = $rs["disc_id"];
				$cedt = $arr["elem_examDate"] = $rs["examDate"];
				//$cetm = $arr["elem_examTime"] = $rs["examTime"];
				//Tech Comments
				$ar2["Tech.Comments"]=$arr["techComment"] = $rs["discDesc"];
				
				//Date
				$arr["Date1"] = $rs["exdt2"];

				//C:D --
				$arr["elem_cdOd"] = stripslashes($rs["cdOd"]);
				$arr["elem_cdOs"] = stripslashes($rs["cdOs"]);
				$ar2["C:D"]=array("OD"=>$arr["elem_cdOd"],"OS"=>$arr["elem_cdOs"]);
				//C:D --

				//Disc --
				$arr["Sharp_Pink_OD"] =$rs["Sharp_Pink_OD"];
				$arr["Pale_OD"] =$rs["Pale_OD"];
				$arr["Large_Cap_OD"] =$rs["Large_Cap_OD"];
				$arr["Sloping_OD"] =$rs["Sloping_OD"];
				$arr["Notch_OD"] =$rs["Notch_OD"];
				$arr["NVD_OD"] =$rs["NVD_OD"];
				$arr["Leakage_OD"] =$rs["Leakage_OD"];

				$arr["Sharp_Pink_OS"] =$rs["Sharp_Pink_OS"];
				$arr["Pale_OS"] =$rs["Pale_OS"];
				$arr["Large_Cap_OS"] =$rs["Large_Cap_OS"];
				$arr["Sloping_OS"] =$rs["Sloping_OS"];
				$arr["Notch_OS"] =$rs["Notch_OS"];
				$arr["NVD_OS"] =$rs["NVD_OS"];
				$arr["Leakage_OS"] =$rs["Leakage_OS"];		
				
				$td = $ts = "";
				$ta = array("Sharp_Pink"=>"Sharp & Pink","Pale"=>"Pale","Large_Cap"=>"Large Cap",
							"Sloping"=>"Sloping","Notch"=>"Notch","NVD"=>"NVD",
							"Leakage"=>"Leakage");
				foreach($ta as $key => $val){
					if($arr[$key."_OD"] == "1") $td .= $val.", ";
					if($arr[$key."_OS"] == "1") $ts .= $val.", ";
				}
				$ar2["Disc"]=array("OD"=>$td,"OS"=>$ts);
				//Disc --

				//Macula --
				$arr["Macula_Rpe_OD_T"] =$rs["Macula_Rpe_OD_T"];
				$arr["Macula_Rpe_OD_1"] =$rs["Macula_Rpe_OD_1"];		
				$arr["Macula_Rpe_OD_2"] =$rs["Macula_Rpe_OD_2"];
				$arr["Macula_Rpe_OD_3"] =$rs["Macula_Rpe_OD_3"];
				$arr["Macula_Rpe_OD_4"] =$rs["Macula_Rpe_OD_4"];
				$arr["Macula_Edema_OD_T"] =$rs["Macula_Edema_OD_T"];
				$arr["Macula_Edema_OD_1"] =$rs["Macula_Edema_OD_1"];
				$arr["Macula_Edema_OD_2"] =$rs["Macula_Edema_OD_2"];
				$arr["Macula_Edema_OD_3"] =$rs["Macula_Edema_OD_3"];
				$arr["Macula_Edema_OD_4"] =$rs["Macula_Edema_OD_4"];
				$arr["Macula_SRNVM_OD"] =$rs["Macula_SRNVM_OD"];
				$arr["Macula_Scars_OD"] =$rs["Macula_Scars_OD"];
				$arr["Macula_Hemorrhage_OD"] =$rs["Macula_Hemorrhage_OD"];
				$arr["Macula_Microaneurysm_OD"] =$rs["Macula_Microaneurysm_OD"];
				$arr["Macula_Exudates_OD"] =$rs["Macula_Exudates_OD"];
				$arr["Macula_Normal_OD"] =$rs["Macula_Normal_OD"];

				$arr["Macula_BDR_OD_T"] =$rs["Macula_BDR_OD_T"];
				$arr["Macula_BDR_OD_1"] =$rs["Macula_BDR_OD_1"];
				$arr["Macula_BDR_OD_2"] =$rs["Macula_BDR_OD_2"];
				$arr["Macula_BDR_OD_3"] =$rs["Macula_BDR_OD_3"];
				$arr["Macula_BDR_OD_4"] =$rs["Macula_BDR_OD_4"];
				
				$arr["Macula_Rpe_OS_T"] =$rs["Macula_Rpe_OS_T"];
				$arr["Macula_Rpe_OS_1"] =$rs["Macula_Rpe_OS_1"];
				$arr["Macula_Rpe_OS_2"] =$rs["Macula_Rpe_OS_2"];
				$arr["Macula_Rpe_OS_3"] =$rs["Macula_Rpe_OS_3"];
				$arr["Macula_Rpe_OS_4"] =$rs["Macula_Rpe_OS_4"];
				$arr["Macula_Edema_OS_T"] =$rs["Macula_Edema_OS_T"];
				$arr["Macula_Edema_OS_1"] =$rs["Macula_Edema_OS_1"];
				$arr["Macula_Edema_OS_2"] =$rs["Macula_Edema_OS_2"];
				$arr["Macula_Edema_OS_3"] =$rs["Macula_Edema_OS_3"];
				$arr["Macula_Edema_OS_4"] =$rs["Macula_Edema_OS_4"];
				$arr["Macula_SRNVM_OS"] =$rs["Macula_SRNVM_OS"];
				$arr["Macula_Scars_OS"] =$rs["Macula_Scars_OS"];
				$arr["Macula_Hemorrhage_OS"] =$rs["Macula_Hemorrhage_OS"];
				$arr["Macula_Microaneurysm_OS"] =$rs["Macula_Microaneurysm_OS"];
				$arr["Macula_Exudates_OS"] =$rs["Macula_Exudates_OS"];
				$arr["Macula_Normal_OS"] =$rs["Macula_Normal_OS"];

				$arr["Macula_BDR_OS_T"] =$rs["Macula_BDR_OS_T"];
				$arr["Macula_BDR_OS_1"] =$rs["Macula_BDR_OS_1"];
				$arr["Macula_BDR_OS_2"] =$rs["Macula_BDR_OS_2"];
				$arr["Macula_BDR_OS_3"] =$rs["Macula_BDR_OS_3"];
				$arr["Macula_BDR_OS_4"] =$rs["Macula_BDR_OS_4"];
				
				$arr["elem_maculaOd_drusen"] = str_replace("Drusen;","",$rs["maculaOd_drusen"]);
				$arr["elem_maculaOs_drusen"] = str_replace("Drusen;","",$rs["maculaOs_drusen"]);

				$td = $ts = "";
				$ta = array("BDR"=>"BDR","Rpe"=>"Rpe change","Edema"=>"Edema");
				$ba = array("T","1","2","3","4");
				foreach($ta as $key => $val){
					$bd=$bs="";
					foreach($ba as $key1 => $val1){
						$sgn = ($val1=="T")?"":"+";
						if($arr["Macula_".$key."_OD_".$val1]) $bd.=$sgn.$val1." ";
						if($arr["Macula_".$key."_OS_".$val1]) $bs.=$sgn.$val1." ";
					}
					if($bd != "" || $bs != ""){
						$td .=$val." ".$bd."<br/>";
						$ts .=$val." ".$bs."<br/>";
					}
				}
				
				foreach($ba as $key1 => $val1){
					$sgn = ($val1=="T")?"":"+";
					$arr["Macula_Drusen_OD_".$val1] = (strpos($arr["elem_maculaOd_drusen"],$sgn.$val1) !== false) ? "1" : "0";
					$arr["Macula_Drusen_OS_".$val1] = (strpos($arr["elem_maculaOs_drusen"],$sgn.$val1) !== false) ? "1" : "0";
				}			
				
				if(!empty($arr["elem_maculaOd_drusen"])||!empty($arr["elem_maculaOs_drusen"])){
					$td .="Drusen ".$arr["elem_maculaOd_drusen"]."<br/>";
					$ts .="Drusen ".$arr["elem_maculaOs_drusen"]."<br/>";
				}
				
				$ta = array("SRNVM"=>"SRNVM","Scars"=>"Scars","Hemorrhage"=>"Hemorrhage",
							"Microaneurysm"=>"Microaneurysm","Exudates"=>"Exudates","Normal"=>"Normal");
				foreach($ta as $key => $val){
					if($arr["Macula_".$key."_OD"] == "1") $td .= $val.", ";
					if($arr["Macula_".$key."_OS"] == "1") $ts .= $val.", ";
				}
				$ar2["Macula"]=array("OD"=>$td,"OS"=>$ts);
				//Macula --

				//Periphery --
				$arr["Periphery_Hemorrhage_OD"] =$rs["Periphery_Hemorrhage_OD"];
				$arr["Periphery_Microaneurysms_OD"] =$rs["Periphery_Microaneurysms_OD"];
				$arr["Periphery_Exudates_OD"] =$rs["Periphery_Exudates_OD"];
				$arr["Periphery_Cr_Scars_OD"] =$rs["Periphery_Cr_Scars_OD"];
				$arr["Periphery_NV_OD"] =$rs["Periphery_NV_OD"];
				$arr["Periphery_Nevus_OD"] =$rs["Periphery_Nevus_OD"];
				$arr["Periphery_Edema_OD"] =$rs["Periphery_Edema_OD"];
				
				$arr["Periphery_Hemorrhage_OS"] =$rs["Periphery_Hemorrhage_OS"];
				$arr["Periphery_Microaneurysms_OS"] =$rs["Periphery_Microaneurysms_OS"];
				$arr["Periphery_Exudates_OS"] =$rs["Periphery_Exudates_OS"];
				$arr["Periphery_Cr_Scars_OS"] =$rs["Periphery_Cr_Scars_OS"];
				$arr["Periphery_NV_OS"] =$rs["Periphery_NV_OS"];
				$arr["Periphery_Nevus_OS"] =$rs["Periphery_Nevus_OS"];
				$arr["Periphery_Edema_OS"] =$rs["Periphery_Edema_OS"];
				
				$td = $ts = "";
				$ta = array("Hemorrhage"=>"Hemorrhage","Microaneurysms"=>"Microaneurysms","Exudates"=>"Exudates",
							"Cr_Scars"=>"Cr Scars","NV"=>"NV","Nevus"=>"Nevus","Edema"=>"Edema");
				foreach($ta as $key => $val){
					if($arr["Periphery_".$key."_OD"] == "1") $td .= $val.", ";
					if($arr["Periphery_".$key."_OS"] == "1") $ts .= $val.", ";	
				}
				$ar2["Periphery"]=array("OD"=>$td,"OS"=>$ts);
				//Periphery --

				//Description --
				$arr["elem_resDescOd"] = $rs["resDescOd"];
				$arr["elem_resDescOs"] = $rs["resDescOs"];				
				$ar2["Description"]=array("OD"=>$arr["elem_resDescOd"],"OS"=>$arr["elem_resDescOs"]);
				//Description --

				//Comments
				$ar2["Comments"]=$arr["discComments"]	= $rs["discComments"];
			}
		}
		$str = $this->getJson($arr,$ar2,$ctid,$cedt);
		return $str;
	}
	private function getEXTER($dt,$tm="",$tid,$dir="Prev",$dc=1){
		$arr = $ar2 = array();
		$ctid=$cedt=$cetm="";		
		$ctid = -1;
		$cedt = "0000-00-00";
		$arr["Date1"] = $dt;
		
		$sql = $this->getQry(1, $dir,$tid, $dt,$dc);
		$res = imw_query($sql);
		if($res){
			while($rs = imw_fetch_assoc($res)){
				$ctid = $rs["disc_id"];
				$cedt = $arr["elem_examDate"] = $rs["examDate"];
				//$cetm = $arr["elem_examTime"] = $rs["examTime"];
				//Tech Comments
				$ar2["Tech.Comments"]=$arr["techComment"] = $rs["discDesc"];
				
				//Date
				$arr["Date1"] = $rs["exdt2"];

				//--	
				
				//Ptosis --
				$arr["elem_ptosisOd_neg"] =$rs["ptosisOd_neg"];
				$arr["elem_ptosisOd_T"] =$rs["ptosisOd_T"];
				$arr["elem_ptosisOd_pos1"] =$rs["ptosisOd_pos1"];
				$arr["elem_ptosisOd_pos2"] =$rs["ptosisOd_pos2"];
				$arr["elem_ptosisOd_pos3"] =$rs["ptosisOd_pos3"];
				$arr["elem_ptosisOd_pos4"] =$rs["ptosisOd_pos4"];
				$arr["elem_ptosisOd_rul"] =$rs["ptosisOd_rul"];
				$arr["elem_ptosisOd_rll"] =$rs["ptosisOd_rll"];
				
				$arr["elem_ptosisOs_neg"] =$rs["ptosisOs_neg"];
				$arr["elem_ptosisOs_T"] =$rs["ptosisOs_T"];
				$arr["elem_ptosisOs_pos1"] =$rs["ptosisOs_pos1"];
				$arr["elem_ptosisOs_pos2"] =$rs["ptosisOs_pos2"];
				$arr["elem_ptosisOs_pos3"] =$rs["ptosisOs_pos3"];
				$arr["elem_ptosisOs_pos4"] =$rs["ptosisOs_pos4"];
				$arr["elem_ptosisOs_rul"] =$rs["ptosisOs_rul"];
				$arr["elem_ptosisOs_rll"] =$rs["ptosisOs_rll"];

				$td = $ts = "";
				$ta = array("neg"=>"-ve","T"=>"T","pos1"=>"+1","pos2"=>"+2","pos3"=>"+3","pos4"=>"+4","rul"=>"RUL","rll"=>"RLL");
				foreach($ta as $key => $val){
					if($arr["elem_ptosisOd_".$key] == "1") $td.="".$val." ";
					if($arr["elem_ptosisOs_".$key] == "1") $ts.="".$val." ";
				}
				$ar2["Ptosis"]=array("OD"=>$td,"OS"=>$ts);
				//Ptosis --

				//Dematochalasis --
				$arr["elem_dermaOd_neg"] =$rs["dermaOd_neg"];
				$arr["elem_dermaOd_T"] =$rs["dermaOd_T"];		
				$arr["elem_dermaOd_pos1"] =$rs["dermaOd_pos1"];
				$arr["elem_dermaOd_pos2"] =$rs["dermaOd_pos2"];
				$arr["elem_dermaOd_pos3"] =$rs["dermaOd_pos3"];
				$arr["elem_dermaOd_pos4"] =$rs["dermaOd_pos4"];
				$arr["elem_dermaOd_rul"] =$rs["dermaOd_rul"];
				$arr["elem_dermaOd_rll"] =$rs["dermaOd_rll"];

				$arr["elem_dermaOs_neg"] =$rs["dermaOs_neg"];
				$arr["elem_dermaOs_T"] =$rs["dermaOs_T"];
				$arr["elem_dermaOs_pos1"] =$rs["dermaOs_pos1"];
				$arr["elem_dermaOs_pos2"] =$rs["dermaOs_pos2"];
				$arr["elem_dermaOs_pos3"] =$rs["dermaOs_pos3"];
				$arr["elem_dermaOs_pos4"] =$rs["dermaOs_pos4"];
				$arr["elem_dermaOs_rul"] =$rs["dermaOs_rul"];
				$arr["elem_dermaOs_rll"] =$rs["dermaOs_rll"];
				
				$td = $ts = "";
				$ta = array("neg"=>"-ve","T"=>"T","pos1"=>"+1","pos2"=>"+2","pos3"=>"+3","pos4"=>"+4","rul"=>"RUL","rll"=>"RLL");
				foreach($ta as $key => $val){
					if($arr["elem_dermaOd_".$key] == "1") $td.="".$val." ";
					if($arr["elem_dermaOs_".$key] == "1") $ts.="".$val." ";
				}
				$ar2["Dematochalasis"]=array("OD"=>$td,"OS"=>$ts);
				//Dematochalasis --

				//Pterygium --
				$arr["elem_pterygium1mmOd"] =$rs["pterygium1mmOd"];
				$arr["elem_pterygium2mmOd"] =$rs["pterygium2mmOd"];
				$arr["elem_pterygium3mmOd"] =$rs["pterygium3mmOd"];
				$arr["elem_pterygium4mmOd"] =$rs["pterygium4mmOd"];
				$arr["elem_pterygium5mmOd"] =$rs["pterygium5mmOd"];
				$arr["elem_pterygiumNasalOd"] =$rs["pterygiumNasalOd"];
				$arr["elem_pterygiumTemporalOd"] =$rs["pterygiumTemporalOd"];
				
				$arr["elem_pterygium1mmOs"] =$rs["pterygium1mmOs"];
				$arr["elem_pterygium2mmOs"] =$rs["pterygium2mmOs"];
				$arr["elem_pterygium3mmOs"] =$rs["pterygium3mmOs"];
				$arr["elem_pterygium4mmOs"] =$rs["pterygium4mmOs"];
				$arr["elem_pterygium5mmOs"] =$rs["pterygium5mmOs"];
				$arr["elem_pterygiumNasalOs"] =$rs["pterygiumNasalOs"];
				$arr["elem_pterygiumTemporalOs"] =$rs["pterygiumTemporalOs"];
				
				$td = $ts = "";
				$ta = array("1mm"=>"1mm","2mm"=>"2mm","3mm"=>"3mm","4mm"=>"4mm","5mm"=>"5mm",
							"Nasal"=>"Nasal","Temporal"=>"Temporal");
				foreach($ta as $key => $val){
					if($arr["elem_pterygium".$key."Od"] == "1") $td.="".$val." ";
					if($arr["elem_pterygium".$key."Os"] == "1") $ts.="".$val." ";
				}
				$ar2["Pterygium"]=array("OD"=>$td,"OS"=>$ts);
				//Pterygium --

				//Vascularization --
				$arr["elem_vascOd_SubEpithelial"] = $rs["vascOd_SubEpithelial"];
				$arr["elem_vascOd_Stromal"] = $rs["vascOd_Stromal"];
				$arr["elem_vascOd_Superficial"] =$rs["vascOd_Superficial"];
				$arr["elem_vascOd_Deep"] =$rs["vascOd_Deep"];
				$arr["elem_vascOd_Endothelial"] =$rs["vascOd_Endothelial"];
				$arr["elem_vascOd_Peripheral"] =$rs["vascOd_Peripheral"];
				$arr["elem_vascOd_Central"] =$rs["vascOd_Central"];
				$arr["elem_vascOd_Pannus"] =$rs["vascOd_Pannus"];
				$arr["elem_vascOd_GhostBV"] =$rs["vascOd_GhostBV"];
				$arr["elem_vascOd_Inferior"] =$rs["vascOd_Inferior"];
				$arr["elem_vascOd_Nasal"] =$rs["vascOd_Nasal"];
				$arr["elem_vascOd_Temporal"] =$rs["vascOd_Temporal"];
				$arr["elem_vascOd_Superior"] = $rs["vascOd_Superior"];				

				$arr["elem_vascOs_SubEpithelial"] =$rs["vascOs_SubEpithelial"];
				$arr["elem_vascOs_Stromal"] =$rs["vascOs_Stromal"];
				$arr["elem_vascOs_Superficial"] =$rs["vascOs_Superficial"];
				$arr["elem_vascOs_Deep"] =$rs["vascOs_Deep"];
				$arr["elem_vascOs_Endothelial"] =$rs["vascOs_Endothelial"];
				$arr["elem_vascOs_Peripheral"] =$rs["vascOs_Peripheral"];
				$arr["elem_vascOs_Central"] =$rs["vascOs_Central"];
				$arr["elem_vascOs_Pannus"] =$rs["vascOs_Pannus"];
				$arr["elem_vascOs_GhostBV"] =$rs["vascOs_GhostBV"];
				$arr["elem_vascOs_Superior"] =$rs["vascOs_Superior"];
				$arr["elem_vascOs_Inferior"] =$rs["vascOs_Inferior"];
				$arr["elem_vascOs_Nasal"] = $rs["vascOs_Nasal"];
				$arr["elem_vascOs_Temporal"] = $rs["vascOs_Temporal"];
				
				$td = $ts = "";
				$ta = array("SubEpithelial"=>"Sub-Epithelial",
							"Stromal"=>"Stromal","Superficial"=>"Superficial","Deep"=>"Deep",
							"Endothelial"=>"Endothelial","Peripheral"=>"Peripheral","Central"=>"Central",
							"Pannus"=>"Pannus","GhostBV"=>"Ghost BV",
							"Superior"=>"Superior","Inferior"=>"Inferior","Nasal"=>"Nasal","Temporal"=>"Temporal");
				foreach($ta as $key => $val){
					if($arr["elem_vascOd_".$key]=="1") $td.="".$val." ";
					if($arr["elem_vascOs_".$key]=="1") $ts.="".$val." ";
					if($key == "SubEpithelial" || $key=="Deep" || $key=="Central"||$key=="GhostBV"){
						$td.="<br/>";
						$ts.="<br/>";
					}
				}
				$ar2["Vascularization"]=array("OD"=>$td,"OS"=>$ts);
				//Vascularization --

				//Nevus --
				$arr["elem_NevusOd_neg"] =$rs["NevusOd_neg"];
				$arr["elem_NevusOd_Pos"] =$rs["NevusOd_Pos"];
				$arr["elem_NevusOd_Inferior"] =$rs["NevusOd_Inferior"];
				$arr["elem_NevusOd_Superior"] =$rs["NevusOd_Superior"];
				$arr["elem_NevusOd_Temporal"] =$rs["NevusOd_Temporal"];
				$arr["elem_NevusOd_Nasal"] =$rs["NevusOd_Nasal"];
		
				$arr["elem_irisNevusOs_neg"] = $rs["irisNevusOs_neg"];
				$arr["elem_irisNevusOs_Pos"] = $rs["irisNevusOs_Pos"];
				$arr["elem_irisNevusOs_Inferior"] = $rs["irisNevusOs_Inferior"];
				$arr["elem_irisNevusOs_Temporal"] = $rs["irisNevusOs_Temporal"];
				$arr["elem_irisNevusOs_Nasal"] = $rs["irisNevusOs_Nasal"];
				$arr["elem_irisNevusOs_Superior"] = $rs["irisNevusOs_Superior"];

				$td = $ts = "";				
				$ta = array("neg"=>"-ve","Pos"=>"+ve","Inferior"=>"Inferior",
							"Superior"=>"Superior","Temporal"=>"Temporal","Nasal"=>"Nasal");
				foreach($ta as $key => $val){
					if($arr["elem_NevusOd_".$key.""] == "1") $td.="".$val." ";
					if($arr["elem_irisNevusOs_".$key.""] == "1") $ts.="".$val." ";
				}
				$ar2["Nevus"]=array("OD"=>$td,"OS"=>$ts);
				//Nevus --

				//Description--
				$arr["elem_resDescOd"] = stripslashes($rs["resDescOd"]);
				$arr["elem_resDescOs"] = stripslashes($rs["resDescOs"]);
				$ar2["Description"]=array("OD"=>$arr["elem_resDescOd"],"OS"=>$arr["elem_resDescOs"]);
				//Description--

				//Comments
				$ar2["Comments"]=$arr["discComments"] = $rs["discComments"];
			}
		}
		$str = $this->getJson($arr,$ar2,$ctid,$cedt);
		return $str;
	}
	
	private function getTOPO($dt,$tm="",$tid,$dir="Prev",$dc=1){
		$arr = $ar2 = array();
		$ctid=$cedt=$cetm="";		
		$ctid = -1;
		$cedt = "0000-00-00";
		$arr["Date1"] = $dt;
		
		$sql = $this->getQry(1, $dir,$tid, $dt,$dc);
		$res = imw_query($sql);
		if($res){
			while($rs = imw_fetch_assoc($res)){
				$ctid = $rs["topo_id"];
				$cedt = $arr["elem_examDate"] = $rs["examDate"];
				//$cetm = $arr["elem_examTime"] = $rs["examTime"];
				//Tech Comments
				$ar2["Tech.Comments"]=$arr["techComment"] = $rs["techComments"];
				
				//Date
				$arr["Date1"] = $rs["exdt2"];
				//Description
				$arr["elem_descOd"] = stripslashes($rs["descOd"]);
				$arr["elem_descOs"] = stripslashes($rs["descOs"]);
				$ar2["Description"]=array("OD"=>$arr["elem_descOd"],"OS"=>$arr["elem_descOs"]);

				//Comments
				$ar2["Comments"]=$arr["elem_comments"]	= $rs["comments"];
			}
		}
		$str = $this->getJson($arr,$ar2,$ctid,$cedt);
		return $str;
	}

	private function getCC($dt,$tm="",$tid,$dir="Prev",$dc=1){
		$arr = $ar2 = array();
		$ctid=$cedt=$cetm="";		
		$ctid = -1;
		$cedt = "0000-00-00";
		$arr["Date1"] = $dt;
		
		$sql = $this->getQry(1, $dir,$tid, $dt,$dc);
		$res = imw_query($sql);
		if($res){
			while($rs = imw_fetch_assoc($res)){
				$ctid = $rs["test_cellcnt_id"];
				$cedt = $arr["elem_examDate"] = $rs["examDate"];
				//$cetm = $arr["elem_examTime"] = $rs["examTime"];
				//Tech Comments
				$ar2["Tech.Comments"]=$arr["techComment"] = $rs["techComments"];

				//Date
				$arr["Date1"] = $rs["exdt2"];
				
				//NUM
				$arr["elem_numOd"] = $rs["numod"];
				$arr["elem_numOs"] = $rs["numos"];
				$ar2["NUM"]=array("OD"=>$arr["elem_numOd"],"OS"=>$arr["elem_numOs"]);

				//CD
				$arr["elem_cdOd"] = $rs["cdod"];
				$arr["elem_cdOs"] = $rs["cdos"];
				$ar2["CD"]=array("OD"=>$arr["elem_cdOd"],"OS"=>$arr["elem_cdOs"]);

				//AVG
				$arr["elem_avgOd"] = $rs["avgod"];
				$arr["elem_avgOs"] = $rs["avgos"];
				$ar2["AVG"]=array("OD"=>$arr["elem_avgOd"],"OS"=>$arr["elem_avgOs"]);
				
				//SD
				$arr["elem_sdOd"] = $rs["sdod"];
				$arr["elem_sdOs"] = $rs["sdos"];
				$ar2["SD"]=array("OD"=>$arr["elem_sdOd"],"OS"=>$arr["elem_sdOs"]);
				
				//CV
				$arr["elem_cvOd"] = $rs["cvod"];
				$arr["elem_cvOs"] = $rs["cvos"];
				$ar2["CV"]=array("OD"=>$arr["elem_cvOd"],"OS"=>$arr["elem_cvOs"]);
				
				//Max
				$arr["elem_mxOd"] = $rs["mxod"];
				$arr["elem_mxOs"] = $rs["mxos"];
				$ar2["Max"]=array("OD"=>$arr["elem_mxOd"],"OS"=>$arr["elem_mxOs"]);
				
				//Min
				$arr["elem_mnOd"] = $rs["mnod"];
				$arr["elem_mnOs"] = $rs["mnos"];
				$ar2["Min"]=array("OD"=>$arr["elem_mnOd"],"OS"=>$arr["elem_mnOs"]);
				
				//6A
				$arr["elem_6aOd"] = $rs["e6aod"];
				$arr["elem_6aOs"] = $rs["e6aos"];
				$ar2["6A"]=array("OD"=>$arr["elem_6aOd"],"OS"=>$arr["elem_6aOs"]);
				
				//CCT
				$arr["elem_cctOd"] = $rs["cctod"];
				$arr["elem_cctOs"] = $rs["cctos"];
				$ar2["CCT"]=array("OD"=>$arr["elem_cctOd"],"OS"=>$arr["elem_cctOs"]);
				
				//Pleomorphism Present 
				$arr["elem_ppOd"] = $rs["ppod"];
				$arr["elem_ppOs"] = $rs["ppos"];
				
				$arr["elem_ppyOd"] = ($arr["elem_ppOd"]=="Y") ? "1" : "0";
				$arr["elem_ppnOd"] = ($arr["elem_ppOd"]=="Y") ? "0" : "1";
				$arr["elem_ppyOs"] = ($arr["elem_ppOs"]=="Y") ? "1" : "0";
				$arr["elem_ppnOs"] = ($arr["elem_ppOs"]=="Y") ? "0" : "1";
				$ar2["Pleomorphism Present"]=array("OD"=>$arr["elem_ppOd"],"OS"=>$arr["elem_ppOs"]);
				
				//Desc
				$arr["elem_descOd"] = stripslashes($rs["descOd"]);
				$arr["elem_descOs"] = stripslashes($rs["descOs"]);
				$ar2["Description"]=array("OD"=>$arr["elem_descOd"],"OS"=>$arr["elem_descOs"]);
			}
		}
		$str = $this->getJson($arr,$ar2,$ctid,$cedt);
		return $str;	
	}
	/*
	private function getOPTH($dt,$tm="",$tid,$dir="Prev",$dc=1){
		$arr = $ar2 = array();
		$ctid=$cedt=$cetm="";		
		$ctid = $tid;
		$cedt = $arr["Date1"] = $dt;
		
		$sql = $this->getQry(1, $dir,$tid, $dt,$dc);
		$res = $this->db->Execute($sql);
		if($res != false){
			while(!$res->EOF){
				$res->MoveNext();
			}
		}	

		$str = $this->getJson($arr,$ar2,$ctid,$cedt);
		return $str;	
	}
	*/
	private function getASCAN($dt,$tm="",$tid,$dir="Prev",$dc=1){
	
	}
	private function getIOL_Master($dt,$tm="",$tid,$dir="Prev",$dc=1){
	
	}
	
	private function getBSCAN($dt,$tm="",$tid,$dir="Prev",$dc=1){
		$arr = $ar2 = array();
		$ctid=$cedt=$cetm="";		
		$ctid = -1;
		$cedt = "0000-00-00";
		$arr["Date1"] = $dt;
		
		$sql = $this->getQry(1, $dir,$tid, $dt,$dc);
		$res = imw_query($sql);
		if($res){
			while($rs = imw_fetch_assoc($res)){
				$ctid = $rs["test_bscan_id"];
				$cedt = $arr["elem_examDate"] = $rs["examDate"];
				//$cetm = $arr["elem_examTime"] = $rs["examTime"];
				//Tech Comments
				$ar2["Tech.Comments"]=$arr["techComment"] = $rs["techComments"];
				//Date
				$arr["Date1"] = $rs["exdt2"];
				
				//Normal
				$arr["elem_tstod"] = $rs["tstod"];
				$arr["elem_tstos"] = $rs["tstos"];
				$ta = array("nsc"=>"Normal Scan","noeve"=>"No evidence of retinal detachment",
							"rdet"=>"Retinal Detachment","vhem"=>"Vitreous Hemorrhage",
							"rtum"=>"Retinal Tumor/Mass","oner"=>"Optic nerve drusen/Mass");
				foreach($ta as $key => $val){
					$arr["elem_".$key."od_1"] = (strpos($arr["elem_tstod"],$val)!==false) ? "1" : "0" ;	
					$arr["elem_".$key."os_1"] = (strpos($arr["elem_tstos"],$val)!==false) ? "1" : "0" ;	
				}
				$ta=$ts="";
				$ta = str_replace(",","<br/>",$arr["elem_tstod"]);
				$ts = str_replace(",","<br/>",$arr["elem_tstos"]);
				$ar2["Normal"]=array("OD"=>$ta,"OS"=>$ts);

				//Comments
				$arr["elem_descOd"] = stripslashes($rs["descOd"]);
				$arr["elem_descOs"] = stripslashes($rs["descOs"]);
				$ar2["Comments"]=array("OD"=>$arr["elem_descOd"],"OS"=>$arr["elem_descOs"]);
			}
		}	

		$str = $this->getJson($arr,$ar2,$ctid,$cedt);
		return $str;
	}
		
	private function getLABS($dt,$tm="",$tid,$dir="Prev",$dc=1){
		$arr = $ar2 = array();
		$ctid=$cedt=$cetm="";		
		$ctid = -1;
		$cedt = "0000-00-00";
		$arr["Date1"] = $dt;
		
		$sql = $this->getQry(1, $dir,$tid, $dt,$dc);
		$res = imw_query($sql);
		if($res){
			while($rs = imw_fetch_assoc($res)){
				$ctid = $rs["test_labs_id"];
				$cedt = $arr["elem_examDate"] = $rs["examDate"];
				//$cetm = $arr["elem_examTime"] = $rs["examTime"];
				//Tech Comments
				$ar2["Tech.Comments"]=$arr["techComment"] = $rs["techComments"];
				//Date
				$arr["Date1"] = $rs["exdt2"];
				
				//lAB nM
				$ar2["Lab. Name: ".$rs["test_labs"]] = array("OD"=>"","OS"=>"");
				
				//Comments
				$arr["elem_descOd"] = stripslashes($rs["descOd"]);
				$arr["elem_descOs"] = stripslashes($rs["descOs"]);
				$ar2["Comments"]=array("OD"=>$arr["elem_descOd"],"OS"=>$arr["elem_descOs"]);

				//Interpretation
				$arr["elem_inter_pret_od"] = stripslashes($rs["inter_pret_od"]);
				$arr["elem_inter_pret_os"] = stripslashes($rs["inter_pret_os"]);
				$ar2["Interpretation"]=array("OD"=>$arr["elem_inter_pret_od"],"OS"=>$arr["elem_inter_pret_os"]);
			}
		}
		$str = $this->getJson($arr,$ar2,$ctid,$cedt);
		return $str;
	}

	private function getOTHER($dt,$tm="",$tid,$dir="Prev",$dc=1){
		$arr = $ar2 = array();
		$ctid=$cedt=$cetm="";		
		$ctid = -1;
		$cedt = "0000-00-00";
		$arr["Date1"] = $dt;
		
		$sql = $this->getQry(1, $dir,$tid, $dt,$dc);
		$res = imw_query($sql);
		if($res){
			while($rs = imw_fetch_assoc($res)){
				$ctid = $rs["test_other_id"];
				$cedt = $arr["elem_examDate"] = $rs["examDate"];
				//$cetm = $arr["elem_examTime"] = $rs["examTime"];
				//Tech Comments
				$ar2["Tech.Comments"]=$arr["techComment"] = $rs["techComments"];
				//Date
				$arr["Date1"] = $rs["exdt2"];

				//Test nM
				$ar2["Test. Name: ".$rs["test_other"]] = array("OD"=>"","OS"=>"");
				
				//Comments
				$arr["elem_descOd"] = stripslashes($rs["descOd"]);
				$arr["elem_descOs"] = stripslashes($rs["descOs"]);
				$ar2["Comments"]=array("OD"=>$arr["elem_descOd"],"OS"=>$arr["elem_descOs"]);

				//Interpretation
				$arr["elem_inter_pret_od"] = stripslashes($rs["inter_pret_od"]);
				$arr["elem_inter_pret_os"] = stripslashes($rs["inter_pret_os"]);
				$ar2["Interpretation"]=array("OD"=>$arr["elem_inter_pret_od"],"OS"=>$arr["elem_inter_pret_os"]);
			}
		}
		$str = $this->getJson($arr,$ar2,$ctid,$cedt);
		return $str;
	}

	public function getPrevval($dt,$tm,$tid,$dir,$flgDC){
		$str = "";
		switch($this->TN){
			case "VF":
				$str = $this->getVF($dt,$tm,$tid,$dir,$flgDC);
			break;
			case "VF-GL":
				$str = $this->getVF_GL($dt,$tm,$tid,$dir,$flgDC);
			break;
			case "HRT":
				$str = $this->getHRT($dt,$tm,$tid,$dir,$flgDC);
			break;
			case "OCT":
				$str = $this->getOCT($dt,$tm,$tid,$dir,$flgDC);
			break;
			case "OCT-RNFL":
				$str = $this->getOCT_RNFL($dt,$tm,$tid,$dir,$flgDC);
			break;			
			case "GDX":
				$str = $this->getGDX($dt,$tm,$tid,$dir,$flgDC);
			break;
			case "PACHY":
				$str = $this->getPACHY($dt,$tm,$tid,$dir,$flgDC);
			break;
			case "IVFA":
				$str = $this->getIVFA($dt,$tm,$tid,$dir,$flgDC);
			break;
			case "ICG":
				$str = $this->getICG($dt,$tm,$tid,$dir,$flgDC);
			break;
			case "FUNDUS":
				$str = $this->getFUNDUS($dt,$tm,$tid,$dir,$flgDC);
			break;
			case "EXTERNAL/ANTERIOR":
				$str = $this->getEXTER($dt,$tm,$tid,$dir,$flgDC);
			break;
			case "TOPOGRAPHY":
				$str = $this->getTOPO($dt,$tm,$tid,$dir,$flgDC);
			break;
			case "CELL COUNT":
				$str = $this->getCC($dt,$tm,$tid,$dir,$flgDC);
			break;
			
			case "OPTHALMOSCOPY":
			break;
			case "A/SCAN":
			break;
			case "IOL_Master":
			break;

			case "B-SCAN":
				$str = $this->getBSCAN($dt,$tm,$tid,$dir,$flgDC);
			break;
			case "LABS":
				$str = $this->getLABS($dt,$tm,$tid,$dir,$flgDC);
			break;
			case "OTHER":
				$str = $this->getOTHER($dt,$tm,$tid,$dir,$flgDC);
			break;
			case "TEMPLATETESTS":
				$str = $this->getOTHER($dt,$tm,$tid,$dir,$flgDC);
			break;			
		}
		return $str;
	}

	public function getPrevId($dt,$tid){
		$ctid = "";
		$sql = $this->getQry(1, "Prev",$tid, $dt,1,"","","PKID");
		$res = imw_query($sql);
		if($res){
			$rs = imw_fetch_assoc($res);
			$ctid = $rs["pk_id"];
		}
		return $ctid;
	}

	public function getNxtId($dt,$tid){
		$ctid = "";
		$sql = $this->getQry(1, "Next",$tid, $dt,1,"","","PKID");
		$res = imw_query($sql);
		if($res){
			$rs = imw_fetch_assoc($res);
			$ctid = $rs["pk_id"];
		}
		return $ctid;
	}

	public function showdiv(){
		echo "<!-- prev Val -->
			 <div id=\"idprvhtm\" style=\"display:none;z-index:+1000 !important;\"></div>
			 <div id=\"divProcessing\"></div>
			 ";	
	}
}


?>