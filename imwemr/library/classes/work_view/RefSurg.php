<?php
/*
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
*/
?>
<?php
/*
File: RefSurg.php
Coded in PHP7
Purpose: This class file provides functions for Refractive Surgery.
Access Type : Include file
*/
?>
<?php
//RefSurg
class RefSurg extends ChartNote {

	private $examName,$tbl,$xmlFileOd,$xmlFileOs;
	public $arrMR=false;
	public $arrAblation=array();
	
	public function __construct($pid,$fid=""){
		parent::__construct($pid,$fid);
		$this->tbl="chart_ref_surgery";
		
		//$this->xmlFileOd=$GLOBALS['incdir']."/chart_notes/xml/ref_surg_od.xml";
		//$this->xmlFileOs=$GLOBALS['incdir']."/chart_notes/xml/ref_surg_os.xml";
		$oExamXml = new ExamXml();
		$tmp = $oExamXml->getExamXmlFiles("RefSurg");
		$this->xmlFileOd = $tmp["od"];
		$this->xmlFileOs = $tmp["os"];		
		
		$this->examName="Refractive Surgery";
		
		$this->arrAblation["5"]=array("-1.00"=>"-8.3","-2.00"=>"-16.7","-3.00"=>"-25.0",
							"-4.00"=>"-33.3","-5.00"=>"-41.7","-6.00"=>"-50.0",
							"-7.00"=>"-58.3","-8.00"=>"-66.7","-9.00"=>"-75.0",
							"-10.00"=>"-83.3","-11.00"=>"-91.7","-12.00"=>"-100.0");
		$this->arrAblation["6"]=array("-1.00"=>"-12.0","-2.00"=>"-24.0","-3.00"=>"-36.0",
							"-4.00"=>"-48.0","-5.00"=>"-60.0","-6.00"=>"-72.0",
							"-7.00"=>"-84.0","-8.00"=>"-96.0","-9.00"=>"-108.0",
							"-10.00"=>"-120.0","-11.00"=>"-132.0","-12.00"=>"-144.0");
		$this->arrAblation["7"]=array("-1.00"=>"-16.3","-2.00"=>"-32.7","-3.00"=>"-49.0",
							"-4.00"=>"-65.3","-5.00"=>"-81.7","-6.00"=>"-98.0",
							"-7.00"=>"-114.3","-8.00"=>"-130.7","-9.00"=>"-147.0",
							"-10.00"=>"-163.3","-11.00"=>"-179.7","-12.00"=>"-196.0");
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
		$sql = "INSERT INTO ".$this->tbl." (id_ref_surg, form_id, patient_id, exam_date, uid)
				VALUES (NULL, '".$this->fid."','".$this->pid."','".date("Y-m-d H:i:s")."','".$this->uid."') ";
		$return=sqlInsert($sql);
		//$return= $this->db->Insert_ID();
		}else{ $return=0; }
		return $return;
	}

	function carryForward(){
		$res = $this->getLastRecord(" c2.id_ref_surg ","1");
		if($res!=false){
			$Id_LF = $res["id_ref_surg"];
		}
		//Insert
		$insertId = $this->insertNew();
		//CopyLF
		$ignoreFlds = "form_id,exam_date,uid,statusElem,examined_no_change,".
					//"ut_elem,".
					"modi_note_RefSurgOd,modi_note_RefSurgOs";
		if(!empty($Id_LF)) $this->carryForwardExe($this->tbl,$insertId,$Id_LF,$ignoreFlds);
	}

	function set2PrvVals(){
		if($this->isRecordExists()){
			$res1 = $this->getRecord(" id_ref_surg ");
			if($res1!=false){
				$Id = $res1["id_ref_surg"];
			}
			
			$res = $this->getLastRecord(" c2.id_ref_surg ","1");		
			if($res!=false){
				$Id_LF = $res["id_ref_surg"];
			}

			//CopyLF
			$ignoreFlds = "form_id,exam_date,uid,statusElem,examined_no_change,".
						//"ut_elem,".
						"modi_note_RefSurgOd,modi_note_RefSurgOs";
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
			$res1 = $this->getRecord(" id_ref_surg,statusElem ");
			if($res1!=false){
				$Id = $res1["id_ref_surg"];
				if($is_cryfd==0){$statusElem = $res1["statusElem"];}
			}

			//CopyLF
			$ignoreFlds = "form_id,exam_date,uid,patient_id,";
			if(!empty($Id)){
				
				if(empty($_POST["site"]) || $_POST["site"]=="OU"){ $statusElem = "";  }
				if($_POST["site"]=="OS"){ 
					$ignoreFlds .= "wnlRefSurgOd,refSurgOd,sumOdRefSurg,modi_note_RefSurgOd,"; 
					if(!empty($statusElem)){ $statusElem = str_replace("_Os=1","_Os=0",$statusElem);  }			
				}else if($_POST["site"]=="OD"){ 
					$ignoreFlds .= "wnlRefSurgOs,refSurgOs,sumOsRefSurg,modi_note_RefSurgOs,"; 
					if(!empty($statusElem)){ $statusElem = str_replace("_Od=1","_Od=0",$statusElem);  }				
				}
				if($_POST["site"]=="OD" || $_POST["site"]=="OS"){
					$ignoreFlds .= "isPositive,";
					if($is_cryfd==0){$ignoreFlds .= "ut_elem,";}
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
				$attr_hidden = $field->getAttribute("hidden");
				if(empty($attr_examName_summary)) $attr_examName_summary = $attr_examName;
			}
			
			if(!empty($attr_hidden)){ //hidden fields: not to include in summmary
				continue;
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
							$summ_tmp_3 = $attr_examName_summary_2.": ".trim($summ_tmp_3)." ";
							$summ_tmp_2 .= $summ_tmp_3;
							if(strpos($summ_tmp_3,"-ve")===false){
								$tmpEm=$attr_examName."/".$attr_examName_2;
								$arrExmDone[$tmpEm]=$attr_examName;
							}
						}
					}
				}

				if(!empty($summ_tmp)){
					$summ_tmp = $attr_examName_summary.": ".trim($summ_tmp)."<br/> ";
					$summ.=$summ_tmp;
					if(strpos($summ_tmp,"-ve")===false){
						$arrExmDone[$attr_examName]=$attr_examName;
					}
				}
				
				if(!empty($summ_tmp_2)){
					$summ.= $summ_tmp_2."<br/> ";
				}

			}else{
				
				if($field->childNodes->length>1){
					$summ_tmp_2="";
					foreach($field->childNodes as $field_2){ //Loop2
						$attr_examName_2=$attr_examName_summary_2="";
						if($field_2->hasAttributes()){
							$attr_examName_2 = $field_2->getAttribute("examname");
							$attr_examName_summary_2 = $field_2->getAttribute("examname_summary");
							if(empty($attr_examName_summary_2)) $attr_examName_summary_2 = $attr_examName_2;
						}
						
						if(!empty($field_2->nodeValue)){
							$summ_tmp_2 .= (!empty($attr_examName_summary_2)) ? $attr_examName_summary_2.": ".$field_2->nodeValue." " : $field_2->nodeValue." ";
						}
					}	
					
					if(!empty($summ_tmp_2)){
						$summ.= $summ_tmp_2."<br/> ";
					}
				
				}else{
					if(!empty($field->nodeValue)){
						$summ.=$field->nodeValue."<br/> ";
					}
				}
			}
		}
		//Process --
		
		$summ = wv_strReplace($summ,"LASTDOT");
		return ($flgRet=="1") ? array("Summary"=>$summ,"ExmDone"=>$arrExmDone) : $summ ;
	}
	
	//GET GIVEN MR
	// if MR1 and MR2 are given, use MR2
	function getMRInfo(){		
		$sql = "
			SELECT 
			c1.mr_none_given, c1.ex_number,
			c2.sph as sph_r, c2.cyl as cyl_r, 
			c3.sph as sph_l, c3.cyl as cyl_l
			FROM chart_vis_master c0
			LEFT JOIN chart_pc_mr c1 ON c1.id_chart_vis_master = c0.id	
			LEFT JOIN chart_pc_mr_values c2 ON c1.id = c2.chart_pc_mr_id AND c2.site='OD'
			LEFT JOIN chart_pc_mr_values c3 ON c1.id = c3.chart_pc_mr_id AND c3.site='OS'	
			WHERE c0.form_id='".$this->fid."' AND c0.patient_id = '".$this->pid."' AND c1.ex_type='MR' AND c1.delete_by='0'  
			Order By c1.ex_number DESC
		";		
		$res=sqlStatement($sql);
		for($i=1;$row!=sqlFetchArray;$i++){
			$mrGiven = $row["mr_none_given"];
			$mrnum = $row["ex_number"];
			if(!empty($mrGiven)){
				//initialized
				$this->arrMR=array();
				if(strpos($mrGiven,"MR ".$mrnum)!==false){
					$this->arrMR["S"]["od"]=$res["sph_r"];
					$this->arrMR["S"]["os"]=$res["sph_l"];
					$this->arrMR["C"]["od"]=$res["cyl_r"];
					$this->arrMR["C"]["os"]=$res["cyl_l"];
				}
			}
		}	
	}
	
	//Get IOP Pachy
	function getPachy(){
		$cor_val_od=$cor_val_os="";
		$sql = "SELECT reading_od, avg_od, cor_val_od, reading_os, avg_os, cor_val_os FROM chart_correction_values 
				WHERE form_id='".$this->fid."' 
				AND patient_id='".$this->pid."' ";
		$res=sqlQuery($sql);
		if($res!=false){
			//$cor_val_od = $res->fields["cor_val_od"];
			//$cor_val_os = $res->fields["cor_val_os"];	
			$reading_od =  (!empty($res["avg_od"])) ? $res["avg_od"] : $res["reading_od"];
			$reading_os =  (!empty($res["avg_os"])) ? $res["avg_os"] : $res["reading_os"];
		}
		return array($reading_od,$reading_os);
	} 

	function getSphareColoum($s){
		
		//Closer to column
		if($s>6){
			$s="+6.0";
		}else if($s<-14){
			$s="-14.0";
		}else{				
			$tmp=abs($s);
			if($tmp>0.5){					
				$c=0;
				while($tmp>0.5){
					$tmp = $tmp-0.5;
					$c++;
					if($c>300)break;
				}
			}
			
			if($tmp>0){
				$tmp = 0.5-$tmp;
			}else{
				$tmp = 0;
			}

			if($s<0){
				$tmp = "-".$tmp;
			}

			$s = $s+$tmp;
		}		
		
		return $s;
	}

	//From the Given MR use � Cylinder + Spherical value
	function getSurgEqi(){
		if(!$this->arrMR)$this->getMRInfo();
		
		if($this->arrMR){
		$s_od = (!empty($this->arrMR["S"]["od"])) ? $this->arrMR["S"]["od"] : 0;
		$s_os = (!empty($this->arrMR["S"]["os"])) ? $this->arrMR["S"]["os"] : 0;
		$c_od = (!empty($this->arrMR["C"]["od"])) ? $this->arrMR["C"]["od"] : 0;
		$c_os = (!empty($this->arrMR["C"]["os"])) ? $this->arrMR["C"]["os"] : 0;

		$srg_od = $s_od + (1/2*$c_od);
		$srg_os = $s_os + (1/2*$c_os);
		}else{
			$srg_od = $srg_os = "";
		}
		return array($srg_od,$srg_os);
	}
	
	//Laser Entry: Use the Sphere value on the left hand corner and 
	//	the patient Age closer to the appropriate column to get to the correct value
	function getLaserEn() {
		if(!$this->arrMR)$this->getMRInfo();		
		
		if($this->arrMR){
			$s_od = (!empty($this->arrMR["S"]["od"])) ? $this->arrMR["S"]["od"] : 0;
			$s_os = (!empty($this->arrMR["S"]["os"])) ? $this->arrMR["S"]["os"] : 0;
			$oPt = new Patient($this->pid);
			$ptAge = $oPt->getAge();
			
			//Adjust Pt Age
			if($ptAge<=20){
				$ptAge=20;
			}else if($ptAge>=65){
				$ptAge=65;
			}else if($ptAge>20&&$ptAge<65){
				$tmp = $ptAge%5;			
				if($tmp>0)$tmp=5-$tmp;
				$ptAge = $ptAge + $tmp;
			}

			//percision to 1
			if(!empty($s_od)){
				$s_od = round($s_od,1);			
				//$s_od = $this->getSphareColoum($s_od);
				$s_od =	number_format($s_od, 1, '.', '');
			}

			if(!empty($s_os)){
				$s_os = round($s_os,1);			
				//$s_os = $this->getSphareColoum($s_os);			
				$s_os =	number_format($s_os, 1, '.', '');
			}

			// Add +
			if($s_od>0){
				if(strpos($s_od,"+")===false&&strpos($s_od,"-")===false){
					$s_od="+".$s_od;
				}
			}

			if($s_os>0){
				if(strpos($s_os,"+")===false&&strpos($s_os,"-")===false){
					$s_os="+".$s_os;
				}
			}
		}else{
			$s_od = $s_os = "";
		}
		
		$arrNomogram=array();
		$arrNomogram["20"]=array("+6.0"=>"+5.3",   "+5.5"=>"+4.8",   "+5.0"=>"+4.3",   "+4.5"=>"+3.9",   "+4.0"=>"+3.4",   "+3.5"=>"+2.9",   
								 "+3.0"=>"+2.4",   "+2.5"=>"+2.0",   "+2.0"=>"+1.5",   "+1.5"=>"+1.0",   "+1.0"=>"+0.5",   "+0.5"=>"+0.1",   
								 "0"=>"-0.3",		"-0.5"=>"-0.9",   "-1.0"=>"-1.4",   "-1.5"=>"-1.8",   "-2.0"=>"-2.2",   "-2.5"=>"-2.6",   
								 "-3.0"=>"-3.1",   "-3.5"=>"-3.5",   "-4.0"=>"-3.9",   "-4.5"=>"-4.3",   "-5.0"=>"-4.8",   "-5.5"=>"-5.2",   
								 "-6.0"=>"-5.6",   "-6.5"=>"-6.0",   "-7.0"=>"-6.5",   "-7.5"=>"-6.9",   "-8.0"=>"-7.3",   "-8.5"=>"-7.7",   
								 "-9.0"=>"-8.2",   "-9.5"=>"-8.6",   "-10.0"=>"-9.0",   "-10.5"=>"-9.4",   "-11.0"=>"-9.9",   "-11.5"=>"-10.3",   
								 "-12.0"=>"-10.7", "-12.5"=>"-11.1",   "-13.0"=>"-11.6",   "-13.5"=>"-12.0",   "-14.0"=>"-12.4" 	
								);
		$arrNomogram["25"]=array("+6.0"=>"+5.4",   "+5.5"=>"+4.9",   "+5.0"=>"+4.4",   "+4.5"=>"+3.9",   "+4.0"=>"+3.5",   "+3.5"=>"+3.0",   
								 "+3.0"=>"+2.5",   "+2.5"=>"+2.0",   "+2.0"=>"+1.6",   "+1.5"=>"+1.1",   "+1.0"=>"+0.6",   "+0.5"=>"+0.1",   
								 "0"=>"-0.3",	   "-0.5"=>"-0.9",   "-1.0"=>"-1.3",   "-1.5"=>"-1.7",   "-2.0"=>"-2.2",   "-2.5"=>"-2.6",   
								 "-3.0"=>"-3.0",   "-3.5"=>"-3.4",   "-4.0"=>"-3.9",   "-4.5"=>"-4.3",   "-5.0"=>"-4.7",   "-5.5"=>"-5.1",   
								 "-6.0"=>"-5.6",   "-6.5"=>"-6.0",   "-7.0"=>"-6.4",   "-7.5"=>"-6.8",   "-8.0"=>"-7.3",   "-8.5"=>"-7.7",   
								 "-9.0"=>"-8.1",   "-9.5"=>"-8.5",   "-10.0"=>"-9.0",   "-10.5"=>"-9.4",   "-11.0"=>"-9.8",   "-11.5"=>"-10.2",   
								 "-12.0"=>"-10.7", "-12.5"=>"-11.1",   "-13.0"=>"-11.5",   "-13.5"=>"-11.9",   "-14.0"=>"-12.4" 	);

		$arrNomogram["30"]=array("+6.0"=>"+5.5",   "+5.5"=>"+5.0",   "+5.0"=>"+4.5",   "+4.5"=>"+4.0",   "+4.0"=>"+3.6",   "+3.5"=>"+3.1",   
								 "+3.0"=>"+2.6",   "+2.5"=>"+2.1",   "+2.0"=>"+1.7",   "+1.5"=>"+1.2",   "+1.0"=>"+0.7",   "+0.5"=>"+0.2",   
								 "0"=>"-0.2",	   "-0.5"=>"-0.8",   "-1.0"=>"-1.2",   "-1.5"=>"-1.6",   "-2.0"=>"-2.0",   "-2.5"=>"-2.5",   
								 "-3.0"=>"-2.9",   "-3.5"=>"-3.3",   "-4.0"=>"-3.7",   "-4.5"=>"-4.2",   "-5.0"=>"-4.6",   "-5.5"=>"-5.0",   
								 "-6.0"=>"-5.4",   "-6.5"=>"-5.9",   "-7.0"=>"-6.3",   "-7.5"=>"-6.7",   "-8.0"=>"-7.1",   "-8.5"=>"-7.6",   
								 "-9.0"=>"-8.0",   "-9.5"=>"-8.4",   "-10.0"=>"-8.8",   "-10.5"=>"-9.3",   "-11.0"=>"-9.7",   "-11.5"=>"-10.1",   
								 "-12.0"=>"-10.5", "-12.5"=>"-11.0",   "-13.0"=>"-11.4",   "-13.5"=>"-11.8",   "-14.0"=>"-12.2" 	);
		
		$arrNomogram["35"]=array("+6.0"=>"+5.6",   "+5.5"=>"+5.1",   "+5.0"=>"+4.7",   "+4.5"=>"+4.2",   "+4.0"=>"+3.7",   "+3.5"=>"+3.2",   
								 "+3.0"=>"+2.8",   "+2.5"=>"+2.3",   "+2.0"=>"+1.8",   "+1.5"=>"+1.3",   "+1.0"=>"+0.9",   "+0.5"=>"+0.4",   
								 "0"=>"-0.2",	   "-0.5"=>"-0.7",   "-1.0"=>"-1.1",   "-1.5"=>"-1.6",   "-2.0"=>"-2.0",   "-2.5"=>"-2.4",   
								 "-3.0"=>"-2.8",   "-3.5"=>"-3.3",   "-4.0"=>"-3.7",   "-4.5"=>"-4.1",   "-5.0"=>"-4.5",   "-5.5"=>"-5.0",   
								 "-6.0"=>"-5.4",   "-6.5"=>"-5.8",   "-7.0"=>"-6.2",   "-7.5"=>"-6.7",   "-8.0"=>"-7.1",   "-8.5"=>"-7.5",   
								 "-9.0"=>"-7.9",   "-9.5"=>"-8.4",   "-10.0"=>"-8.8",   "-10.5"=>"-9.2",   "-11.0"=>"-9.6",   "-11.5"=>"-10.1",   
								 "-12.0"=>"-10.5", "-12.5"=>"-10.9",   "-13.0"=>"-11.3",   "-13.5"=>"-11.8",   "-14.0"=>"-12.2" 	);
		
		$arrNomogram["40"]=array("+6.0"=>"+5.7",   "+5.5"=>"+5.2",   "+5.0"=>"+4.8",   "+4.5"=>"+4.3",   "+4.0"=>"+3.8",   "+3.5"=>"+3.3",   
								 "+3.0"=>"+2.9",   "+2.5"=>"+2.4",   "+2.0"=>"+1.9",   "+1.5"=>"+1.4",   "+1.0"=>"+1.0",   "+0.5"=>"+0.5",   
								 "0"=>"-0.1",	   "-0.5"=>"-0.6",   "-1.0"=>"-1.0",   "-1.5"=>"-1.5",   "-2.0"=>"-1.9",   "-2.5"=>"-2.3",   
								 "-3.0"=>"-2.7",   "-3.5"=>"-3.2",   "-4.0"=>"-3.6",   "-4.5"=>"-4.0",   "-5.0"=>"-4.4",   "-5.5"=>"-4.9",   
								 "-6.0"=>"-5.3",   "-6.5"=>"-5.7",   "-7.0"=>"-6.1",   "-7.5"=>"-6.6",   "-8.0"=>"-7.0",   "-8.5"=>"-7.4",   
								 "-9.0"=>"-7.8",   "-9.5"=>"-8.3",   "-10.0"=>"-8.7",   "-10.5"=>"-9.1",   "-11.0"=>"-9.5",   "-11.5"=>"-10.0",   
								 "-12.0"=>"-10.4", "-12.5"=>"-10.8",   "-13.0"=>"-11.2",   "-13.5"=>"-11.7",   "-14.0"=>"-12.1" 	);
		
		$arrNomogram["45"]=array("+6.0"=>"+5.7",   "+5.5"=>"+5.2",   "+5.0"=>"+4.8",   "+4.5"=>"+4.3",   "+4.0"=>"+3.8",   "+3.5"=>"+3.3",   
								 "+3.0"=>"+2.9",   "+2.5"=>"+2.4",   "+2.0"=>"+1.9",   "+1.5"=>"+1.4",   "+1.0"=>"+1.0",   "+0.5"=>"+0.5",   
								 "0"=>"0",		   "-0.5"=>"-0.6",   "-1.0"=>"-1.0",   "-1.5"=>"-1.5",   "-2.0"=>"-1.9",   "-2.5"=>"-2.3",   
								 "-3.0"=>"-2.7",   "-3.5"=>"-3.2",   "-4.0"=>"-3.6",   "-4.5"=>"-4.0",   "-5.0"=>"-4.4",   "-5.5"=>"-4.9",   
								 "-6.0"=>"-5.3",   "-6.5"=>"-5.7",   "-7.0"=>"-6.1",   "-7.5"=>"-6.6",   "-8.0"=>"-7.0",   "-8.5"=>"-7.4",   
								 "-9.0"=>"-7.8",   "-9.5"=>"-8.3",   "-10.0"=>"-8.7",   "-10.5"=>"-9.1",   "-11.0"=>"-9.5",   "-11.5"=>"-10.0",   
								 "-12.0"=>"-10.4", "-12.5"=>"-10.8",   "-13.0"=>"-11.2",   "-13.5"=>"-11.7",   "-14.0"=>"-12.1" 	);
		
		$arrNomogram["50"]=array("+6.0"=>"+5.7",   "+5.5"=>"+5.2",   "+5.0"=>"+4.8",   "+4.5"=>"+4.3",   "+4.0"=>"+3.8",   "+3.5"=>"+3.3",   
								 "+3.0"=>"+2.9",   "+2.5"=>"+2.4",   "+2.0"=>"+1.9",   "+1.5"=>"+1.4",   "+1.0"=>"+1.0",   "+0.5"=>"+0.5",   
								 "0"=>"0",		   "-0.5"=>"-0.6",   "-1.0"=>"-1.0",   "-1.5"=>"-1.4",   "-2.0"=>"-1.9",   "-2.5"=>"-2.3",   
								 "-3.0"=>"-2.7",   "-3.5"=>"-3.1",   "-4.0"=>"-3.6",   "-4.5"=>"-4.0",   "-5.0"=>"-4.4",   "-5.5"=>"-4.8",   
								 "-6.0"=>"-5.3",   "-6.5"=>"-5.7",   "-7.0"=>"-6.1",   "-7.5"=>"-6.5",   "-8.0"=>"-7.0",   "-8.5"=>"-7.4",   
								 "-9.0"=>"-7.8",   "-9.5"=>"-8.2",   "-10.0"=>"-8.7",   "-10.5"=>"-9.1",   "-11.0"=>"-9.5",   "-11.5"=>"-9.9",   
								 "-12.0"=>"-10.4", "-12.5"=>"-10.8",   "-13.0"=>"-11.2",   "-13.5"=>"-11.6",   "-14.0"=>"-12.1" 	);
		
		$arrNomogram["55"]=array("+6.0"=>"+5.7",   "+5.5"=>"+5.2",   "+5.0"=>"+4.8",   "+4.5"=>"+4.3",   "+4.0"=>"+3.8",   "+3.5"=>"+3.3",   
								 "+3.0"=>"+2.9",   "+2.5"=>"+2.4",   "+2.0"=>"+1.9",   "+1.5"=>"+1.4",   "+1.0"=>"+1.0",   "+0.5"=>"+0.5",   
								 "0"=>"0",		   "-0.5"=>"-0.6",   "-1.0"=>"-1.0",   "-1.5"=>"-1.4",   "-2.0"=>"-1.9",   "-2.5"=>"-2.3",   
								 "-3.0"=>"-2.7",   "-3.5"=>"-3.1",   "-4.0"=>"-3.6",   "-4.5"=>"-4.0",   "-5.0"=>"-4.4",   "-5.5"=>"-4.8",   
								 "-6.0"=>"-5.3",   "-6.5"=>"-5.7",   "-7.0"=>"-6.1",   "-7.5"=>"-6.5",   "-8.0"=>"-7.0",   "-8.5"=>"-7.4",   
								 "-9.0"=>"-7.8",   "-9.5"=>"-8.2",   "-10.0"=>"-8.7",   "-10.5"=>"-9.1",   "-11.0"=>"-9.5",   "-11.5"=>"-9.9",   
								 "-12.0"=>"-10.4", "-12.5"=>"-10.8",   "-13.0"=>"-11.2",   "-13.5"=>"-11.6",   "-14.0"=>"-12.1" 	);
		
		$arrNomogram["60"]=array("+6.0"=>"+5.7",   "+5.5"=>"+5.2",   "+5.0"=>"+4.8",   "+4.5"=>"+4.3",   "+4.0"=>"+3.8",   "+3.5"=>"+3.3",   
								 "+3.0"=>"+2.9",   "+2.5"=>"+2.4",   "+2.0"=>"+1.9",   "+1.5"=>"+1.4",   "+1.0"=>"+1.0",   "+0.5"=>"+0.5",   
								 "0"=>"0",		   "-0.5"=>"-0.6",   "-1.0"=>"-1.0",   "-1.5"=>"-1.4",   "-2.0"=>"-1.9",   "-2.5"=>"-2.3",   
								 "-3.0"=>"-2.7",   "-3.5"=>"-3.1",   "-4.0"=>"-3.6",   "-4.5"=>"-4.0",   "-5.0"=>"-4.4",   "-5.5"=>"-4.8",   
								 "-6.0"=>"-5.3",   "-6.5"=>"-5.7",   "-7.0"=>"-6.1",   "-7.5"=>"-6.5",   "-8.0"=>"-7.0",   "-8.5"=>"-7.4",   
								 "-9.0"=>"-7.8",   "-9.5"=>"-8.2",   "-10.0"=>"-8.7",   "-10.5"=>"-9.1",   "-11.0"=>"-9.5",   "-11.5"=>"-9.9",   
								 "-12.0"=>"-10.4", "-12.5"=>"-10.8",   "-13.0"=>"-11.2",   "-13.5"=>"-11.6",   "-14.0"=>"-12.1" 	);
		
		$arrNomogram["65"]=array("+6.0"=>"+5.7",   "+5.5"=>"+5.2",   "+5.0"=>"+4.8",   "+4.5"=>"+4.3",   "+4.0"=>"+3.8",   "+3.5"=>"+3.3",   
								 "+3.0"=>"+2.9",   "+2.5"=>"+2.4",   "+2.0"=>"+1.9",   "+1.5"=>"+1.4",   "+1.0"=>"+1.0",   "+0.5"=>"+0.5",   
								 "0"=>"0",		   "-0.5"=>"-0.6",   "-1.0"=>"-1.0",   "-1.5"=>"-1.4",   "-2.0"=>"-1.8",   "-2.5"=>"-2.3",   
								 "-3.0"=>"-2.7",   "-3.5"=>"-3.1",   "-4.0"=>"-3.5",   "-4.5"=>"-4.0",   "-5.0"=>"-4.4",   "-5.5"=>"-4.8",   
								 "-6.0"=>"-5.2",   "-6.5"=>"-5.7",   "-7.0"=>"-6.1",   "-7.5"=>"-6.5",   "-8.0"=>"-6.9",   "-8.5"=>"-7.4",   
								 "-9.0"=>"-7.8",   "-9.5"=>"-8.2",   "-10.0"=>"-8.6",   "-10.5"=>"-9.1",   "-11.0"=>"-9.5",   "-11.5"=>"-9.9",   
								 "-12.0"=>"-10.3", "-12.5"=>"-10.8",   "-13.0"=>"-11.2",   "-13.5"=>"-11.6",   "-14.0"=>"-12.0" 	);
		/*
		// adjtPPP
		$ret_od = !empty($arrNomogram["".$ptAge]["".$s_od]) ? $arrNomogram["".$ptAge]["".$s_od] : "";
		$ret_os = !empty($arrNomogram["".$ptAge]["".$s_os]) ? $arrNomogram["".$ptAge]["".$s_os] : "";
		*/
		$ret_od = $ret_os = "";
		$arrVals = $arrNomogram["".$ptAge];
		if(count($arrVals)>0){
			
			if(!empty($s_od)){
				$dec_od = round(fmod($s_od,1),2);
			}
			
			if(!empty($s_os)){
				$dec_os = round(fmod($s_os,1),2);
			}		
			
			$mn_od = $mx_od = "";
			$mn_os = $mx_os = "";
			foreach($arrVals as $key=>$val){
				
				if(!empty($s_od)&&empty($ret_od)){
					if($s_od==$key){
						$ret_od = $val;
					}else if($key>$s_od){					
						$mx_od= $val;
					}else if($key<$s_od){					
						$mn_od= $val;
						if($dec_od=="0.50"){
							//mean
							$ret_od = ($mx_od+$mn_od)/2;
						}else if($dec_od<"0.50"){
							$ret_od = $mn_od;
						}else if($dec_od>"0.50"){
							$ret_od = $mx_od;
						}
					}
				}
				
				if(!empty($s_os)&&empty($ret_os)){
					if($s_os==$key){
						$ret_os = $val;
					}else if($key>$s_os){					
						$mx_os= $val;
					}else if($key<$s_os){					
						$mn_os= $val;						
						if($dec_os=="0.50"){
							//mean
							$ret_os = ($mx_os+$mn_os)/2;
						}else if($dec_os<"0.50"){
							$ret_os = $mn_os;
						}else if($dec_os>"0.50"){
							$ret_os = $mx_os;
						}
					}
				}
			}
		}
		
		if($ret_od!="")$ret_od = number_format($ret_od,2);
		if($ret_os!="")$ret_os = number_format($ret_os,2);
		
		return array($ret_od,$ret_os);
	}

	//Adjustment % per nomo: Show % difference(rounded up) between Laser Entry and Spherical Equivalent
	//For Example Spherical Equivalent above is -4.25 and the Laser Entry is -3.6 then the Adj. % nomo is 15%
	function getAdjstPPP(){
		if(!$this->arrMR)$this->getMRInfo();
		list($surgod,$surgos) = $this->getSurgEqi();
		list($laserod,$laseros) = $this->getLaserEn();
		$tmpod = $tmpos = "";
		if(!empty($surgod)) $tmpod = round((round($surgod - $laserod,1)/$surgod)*100);
		if(!empty($surgos)) $tmpos = round((round($surgos - $laseros,1)/$surgos)*100);
		
		return array($tmpod,$tmpos);		
	}
	/*
	//Physician % Adj value = the percentage difference between the Phy % Adj in Adj % Nomo of the calculated vale.  
	//For example if the physicians enters 20% in Phy % Adj Nomo and the spherical equivalent was -4.25 then the physician % Adj for Laser point is  80% of Spherical equivalent i.e. -3.4
	function getPhyPAdj(){
		list($surgod,$surgos) = $this->getSurgEqi();
		list($apppOd,$apppOs)=$this->getAdjstPPP();
		$tmpOd = 100-$apppOd;
		$tmpOs = 100-$apppOs;
		
		$retod= round(($surgod*$tmpOd)/100);
		$retos= round(($surgos*$tmpOs)/100);	
		return array($retod,$retos);
	}
	*/
	
	function save_no_change(){
		$form_id = $this->fid;
		$patientId = $this->pid;
		$w = strtoupper($_POST["w"]);
		$exmEye = $_POST["elem_exmEye"];
		
		//Check and Add record
		$oRefSurg = $this; //new RefSurg($patientId,$form_id);
		if(!$oRefSurg->isRecordExists()){
			$oRefSurg->carryForward();
			$elem_noChangeRef_Surg=1;
		}else if(!$oRefSurg->isNoChanged()){
			$elem_noChangeRef_Surg=1;
		}else{
			$oRefSurg->set2PrvVals();
			$elem_noChangeRef_Surg=0;
		}
		
		//Get status string --
		$statusElem="";
		if($elem_noChangeRef_Surg==1) $statusElem=$this->se_elemStatus($w,"1","","1","1",0);
		//Get status--
		
		//
		$sql = "UPDATE chart_ref_surgery SET examined_no_change='".$elem_noChangeRef_Surg."',
				exam_date ='".date("Y-m-d H:i:s")."', uid='".$_SESSION["authId"]."',
				statusElem='".$statusElem."'
				WHERE form_id='".$form_id."' AND patient_id='".$patientId."'  AND purged = '0' ";
		$res = sqlQuery($sql);
		
	}
	
	function recalculateRefSurg_handler(){
		$patientId = $this->pid;
		$fid = $this->fid ; //$_POST["elem_fid"];
		$sod = $_POST["sod"];
		$sos = $_POST["sos"];
		$cod = $_POST["cod"];
		$cos = $_POST["cos"];
		
		//$oRefSurg = new RefSurg($patientId,$fid);
		
		$this->arrMR["S"]["od"]=$sod;
		$this->arrMR["S"]["os"]=$sos;
		$this->arrMR["C"]["od"]=$cod;
		$this->arrMR["C"]["os"]=$cos;
		
		$arr=array();
		list($arr["elem_sphericalEqOd"],$arr["elem_sphericalEqOs"])=$this->getSurgEqi();
		list($arr["elem_adjstpppOd"],$arr["elem_adjstpppOs"])=$this->getAdjstPPP();
		list($arr["elem_laserEntryOd"],$arr["elem_laserEntryOs"])=$this->getLaserEn();
		//list($arr["elem_laserEnPhypadjstOd"],$arr["elem_laserEnPhypadjstOs"])=$oRefSurg->getPhyPAdj();
		list($arr["elem_corrOd"],$arr["elem_corrOs"])=$this->getPachy();
		echo json_encode($arr);
	}
	
	
}
?>