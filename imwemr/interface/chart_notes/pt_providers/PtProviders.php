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
Purpose: This class provides processing functions to get all the patients who worked on patient chart.
Access Type : Include file

1. Set Patient Id
2. Get All Forms ID - DOS
2.1. Get All Physician(Signer and Co-signers)
2.2. Get All Technicians (CC& History, MR, any Tests forms)
2.3  Get All Scribes ? - what is scribe to patient? where it is stored in system?
3 Get Pdf for printing

WNotes:--
1. DOS, CC Hx - chart_left_cc_history
2. signer, cosigner - chart_assessment_plan
3. MR 1,2,3 - chart_vision
4. Tests Forms - Performed By, Interpreted By
	1. vf
	2. nfa
	3. oct
	4. pachy
	5. ivfa
	6. fundus
	7. external
	8. topography
	9. ophtha
	10. A-scan	
	11. Amsler grid
	12. TEst Other
	13. Test Labs
	14. B-Scan
	15. Cell Count
5. Perscription
6. superbill
*/


class PtProviders extends TCPDF{

	private $db;
	private $pId;
	private $bdr;  // border
	private $arrPhyType;
	private $arrTechType;

	function __construct($pid){
		$this->pId = $pid;
		$this->bdr = "0";
		$this->arrPhyType = $GLOBALS['arrValidCNPhy']; 
		$this->arrTechType = $GLOBALS['arrValidCNTech']; 
		parent::__construct(); // Initialize Test
	}
	
	function get_mr_providers($visid, $chkVis){
		$arr = array();
		$sql = "SELECT provider_id, ex_number FROM chart_pc_mr 
				WHERE id_chart_vis_master='".$visid."' AND ex_type='MR' AND delete_by='0' ";
		$res = sqlStatement($sql);
		for($i=0; $row=sqlFetchArray($res);$i++){
			$c = $row["ex_number"];
			$indx1 = $indx2 = "";
			if($c > 1){
				$indx1 = "Other";
				if($c > 2){
					$indx2 = "_".$c;
				}
			}
			
			if((strpos($chkVis, "elem_visMr".$indx1."OdS".$indx2."=1")!==false) || 
				(strpos($chkVis, "elem_visMr".$indx1."OdC".$indx2."=1")!==false) || 
				(strpos($chkVis, "elem_visMr".$indx1."OdA".$indx2."=1")!==false) || 
				(strpos($chkVis, "elem_visMr".$indx1."OdAdd".$indx2."=1")!==false) || 
				(strpos($chkVis, "elem_visMr".$indx1."OsS".$indx2."=1")!==false) || 
				(strpos($chkVis, "elem_visMr".$indx1."OsC".$indx2."=1")!==false) || 
				(strpos($chkVis, "elem_visMr".$indx1."OsA".$indx2."=1")!==false)){
					$arr["mr".$c] = $row["provider_id"];
			}
		}
		
		
		
		return $arr;
	}


	function getData(){
		
		$arrRet = array();	
		$arr = array();		
		$sql =  "SELECT ".
				"c1.id,c1.providerId,c1.create_by, c1.finalizerId, c1.provIds, ".
				"c1.date_of_service, c2.time_of_service, c2.pro_id,c2.cosigner_id AS ccHxCoSignerId, ".				
				"c3.doctorId, c3.cosigner_id,c3.scribedBy, ".
				"c4.id as id_chart_vis_master, c4.status_elements, ".
				"c5.performedBy AS performedBy_vf, c5.phyName AS phyName_vf, ".
				"c6.performBy AS performedBy_nfa, c6.phyName AS phyName_nfa, ".
				"c7.performBy AS performedBy_oct, c7.phyName AS phyName_oct, ".
				"c8.performedBy AS performedBy_pachy, c8.phyName AS phyName_pachy, ".
				"c9.performed_by AS performedBy_ivfa, c9.phy AS phyName_ivfa, ".
				"c10.performedBy AS performedBy_disc, c10.phyName AS phyName_disc, ".
				"c11.performedBy AS performedBy_disc_ex, c11.phyName AS phyName_disc_ex, ".
				"c12.performedBy AS performedBy_topo, c12.phyName AS phyName_topo, ".
				"c13.doctor_name AS doctor_name_ophtha, ".
				"c14.doctor_name AS doctor_name_ag, ".
				"c15.signedById, c15.signedByOSId, ".
				"c16.physicianId, c16.refferingPhysician, ".
				"c21.signedById AS signedById_IOLM, c21.signedByOSId AS signedByOSId_IOLM, ".
				
				"c17.performedBy AS performedBy_other, c17.phyName AS phyName_other, ".
				"c18.performedBy AS performedBy_labs, c18.phyName AS phyName_labs, ".
				"c19.performedBy AS performedBy_bscan, c19.phyName AS phyName_bscan, ".
				"c20.performedBy AS performedBy_cellcnt, c20.phyName AS phyName_cellcnt ".

				"FROM chart_master_table c1 ".
				"LEFT JOIN chart_left_cc_history c2 ON c2.form_id = c1.id ".
				"LEFT JOIN chart_assessment_plans c3 ON c3.form_id = c1.id ".
				"LEFT JOIN chart_vis_master c4 ON c4.form_id = c1.id ".
				"LEFT JOIN vf c5 ON c5.formId = c1.id ".
				"LEFT JOIN nfa c6 ON c6.form_id = c1.id ".		
				"LEFT JOIN oct c7 ON c7.form_id = c1.id ".
				"LEFT JOIN pachy c8 ON c8.formId = c1.id ".
				"LEFT JOIN ivfa c9 ON c9.form_id = c1.id ".
				"LEFT JOIN disc c10 ON c10.formId = c1.id ".
				"LEFT JOIN disc_external c11 ON c11.formId = c1.id ".
				"LEFT JOIN topography c12 ON c12.formId = c1.id ".
				"LEFT JOIN ophtha c13 ON c13.form_id = c1.id ".
				"LEFT JOIN amsler_grid c14 ON c14.form_id = c1.id ".
				"LEFT JOIN surgical_tbl c15 ON c15.form_id = c1.id ".
				"LEFT JOIN iol_master_tbl c21 ON c21.form_id = c1.id ".
				"LEFT JOIN superbill c16 ON c16.formId = c1.id ".
				
				"LEFT JOIN test_other c17 ON c17.formId = c1.id ".
				"LEFT JOIN test_labs c18 ON c18.formId = c1.id ".
				"LEFT JOIN test_bscan c19 ON c19.formId = c1.id ".
				"LEFT JOIN test_cellcnt c20 ON c20.formId = c1.id ".				
			    
				"WHERE c1.patient_id='".$this->pId."' ".
				"ORDER BY c2.date_of_service DESC, c1.id DESC ";			
			$qry = imw_query($sql);
			if(imw_num_rows($qry) > 0){
				while($res = imw_fetch_array($qry)){
					$arr[$c]["formId"] = $res["id"]; 				
					$arr[$c]["DOS"] = get_date_format($res["date_of_service"]);
					$arr[$c]["TOS"] = date($res["time_of_service"]);
					
					$arr[$c]["ccHx"]["signer"] = $res["pro_id"];
					$arr[$c]["ccHx"]["coSigner"] = $res["ccHxCoSignerId"];				
					
					$arr[$c]["signer"] = $res["doctorId"];
					$arr[$c]["cosigner"] = $res["cosigner_id"];
					$arr[$c]["scribedBy"] = $res["scribedBy"];
					$arr[$c]["finalizerId"] = $res["finalizerId"];
					$arr[$c]["create_by"] = $res["create_by"];
					$arr[$c]["provIds"] = $res["provIds"];

					//Check for empty Doctor Id
					if(empty($arr[$c]["signer"])){
						if(!empty($res["providerId"])){
							$arr[$c]["signer"] = $res["providerId"];
						}
					}
					//Check for empty Doctor Id
					
					//MR ---
					$ar_mr = $this->get_mr_providers($res["id_chart_vis_master"], $res["status_elements"]);
					$arr[$c] = array_merge($arr[$c], $ar_mr);
					/*
					$chkVis = $res["vis_statusElements"];
					if((strpos($chkVis, "elem_visMrOdS=1")!==false) || 
						(strpos($chkVis, "elem_visMrOdC=1")!==false) || 
						(strpos($chkVis, "elem_visMrOdA=1")!==false) || 
						(strpos($chkVis, "elem_visMrOdAdd=1")!==false) || 
						(strpos($chkVis, "elem_visMrOsS=1")!==false) || 
						(strpos($chkVis, "elem_visMrOsC=1")!==false) || 
						(strpos($chkVis, "elem_visMrOsA=1")!==false)){
							$arr[$c]["mr1"] = $res["provider_id"];
					}
					if((strpos($chkVis, "elem_visMrOtherOdS=1")!==false) || 
						(strpos($chkVis, "elem_visMrOtherOdC=1")!==false) || 
						(strpos($chkVis, "elem_visMrOtherOdA=1")!==false) || 
						(strpos($chkVis, "elem_visMrOtherOdAdd=1")!==false) || 
						(strpos($chkVis, "elem_visMrOtherOsS=1")!==false) || 
						(strpos($chkVis, "elem_visMrOtherOsC=1")!==false) || 
						(strpos($chkVis, "elem_visMrOtherOsA=1")!==false)){
						$arr[$c]["mr2"] = $res["providerIdOther"];
					}
					if((strpos($chkVis, "elem_visMrOtherOdS_3=1")!==false) || 
						(strpos($chkVis, "elem_visMrOtherOdC_3=1")!==false) || 
						(strpos($chkVis, "elem_visMrOtherOdA_3=1")!==false) || 
						(strpos($chkVis, "elem_visMrOtherOdAdd_3=1")!==false) || 
						(strpos($chkVis, "elem_visMrOtherOsS_3=1")!==false) || 
						(strpos($chkVis, "elem_visMrOtherOsC_3=1")!==false) || 
						(strpos($chkVis, "elem_visMrOtherOsA_3=1")!==false)){
						$arr[$c]["mr3"] = $res["providerIdOther_3"];
					}
					*/
					//MR ---

					$arr[$c]["vf"]["perform"] = $res["performedBy_vf"];
					$arr[$c]["vf"]["interpret"] = $res["phyName_vf"];

					$arr[$c]["nfa"]["perform"] = $res["performedBy_nfa"];
					$arr[$c]["nfa"]["interpret"] = $res["phyName_nfa"];
					
					$arr[$c]["oct"]["perform"] = $res["performedBy_oct"];
					$arr[$c]["oct"]["interpret"] = $res["phyName_oct"];
					
					$arr[$c]["pachy"]["perform"] = $res["performedBy_pachy"];
					$arr[$c]["pachy"]["interpret"] = $res["phyName_pachy"];
					
					$arr[$c]["ivfa"]["perform"] = $res["performedBy_ivfa"];
					$arr[$c]["ivfa"]["interpret"] = $res["phyName_ivfa"];

					$arr[$c]["disc"]["perform"] = $res["performedBy_disc"];
					$arr[$c]["disc"]["interpret"] = $res["phyName_disc"];

					$arr[$c]["disc_ex"]["perform"] = $res["performedBy_disc_ex"];
					$arr[$c]["disc_ex"]["interpret"] = $res["phyName_disc_ex"];
				
					$arr[$c]["topo"]["perform"] = $res["performedBy_topo"];
					$arr[$c]["topo"]["interpret"] = $res["phyName_topo"];
					
					$arr[$c]["amsler"] = $res["doctor_name_ag"];
					$arr[$c]["ophtha"] = $res["doctor_name_ophtha"];
					
					$arr[$c]["ascan"]["od"] = $res["signedById"];
					$arr[$c]["ascan"]["os"] = $res["signedByOSId"];
					
					$arr[$c]["iol_master"]["od"] = $res["signedById_IOLM"];
					$arr[$c]["iol_master"]["os"] = $res["signedByOSId_IOLM"];
					
					$arr[$c]["superbill"]["phy"] = $res["physicianId"];
					$arr[$c]["superbill"]["reff_phy"] = $res["refferingPhysician"];

					//--
					$arr[$c]["tother"]["perform"] = $res["performedBy_other"];
					$arr[$c]["tother"]["interpret"] = $res["phyName_other"];

					$arr[$c]["labs"]["perform"] = $res["performedBy_labs"];
					$arr[$c]["labs"]["interpret"] = $res["phyName_labs"];

					$arr[$c]["bscan"]["perform"] = $res["performedBy_bscan"];
					$arr[$c]["bscan"]["interpret"] = $res["phyName_bscan"];

					$arr[$c]["cellcnt"]["perform"] = $res["performedBy_cellcnt"];
					$arr[$c]["cellcnt"]["interpret"] = $res["phyName_cellcnt"];
					//--
					$c=$c+1;
				}
			}			
		
		//Prescriptions
		$sql =  imw_query("SELECT filled_by_id,date_added,refilled_by ".
				"FROM prescriptions ".
			    "WHERE patient_id = '".$this->pId."' ");
		while($res = imw_fetch_array($sql)){
			$arrPres = array();
			$c=0;
			$arrPres[$c]["fillBy"] = $res["filled_by_id"];
			$arrPres[$c]["refillBy"] = $res["refilled_by"];
			$arrPres[$c]["dt_added"] = get_date_format($res["date_added"],"yyyy-mm-dd");
			$c=$c+1;	
		}
		
		$arrRet["forms"] = $arr; //Forms
		$arrRet["Prescriptions"] = $arrPres; //Prescriptions

		return $arrRet;
	}
	
	function getUserName($id,$flgType=0){
		$sql = imw_query("SELECT fname,mname,lname,user_type,pro_suffix FROM users WHERE id = '".$id."' ");
		while($res = imw_fetch_array($sql)){
			$fname = $res["fname"];
			$mname = $res["mname"];
			$lname = $res["lname"];
			$usrType = $res["user_type"];
			$usuff = $res["pro_suffix"];
		
			$nm = "";
			$nm .= (!empty($fname)) ? $fname." " : "";
			$nm .= (!empty($mname)) ? $mname." " : "";
			$nm .= (!empty($lname)) ? $lname." " : "";		
			$nm .= (!empty($usuff)) ? $usuff." " : "";		
		}
		return ($flgType == 1) ? array("name"=>$nm,"type"=>$usrType) : $nm; 
	}

	function getUsrInfo($id,$atch){
		$strPhy = "";
		$strTech = "";
		$tmp = $this->getUserName($id,1);
		if(in_array($tmp["type"],$this->arrPhyType)){	//physician
				$strPhy = trim($tmp["name"]." ".$atch."<br>"); 
		}else if(in_array($tmp["type"],$this->arrTechType)){ // technician
				$strTech = trim($tmp["name"]." ".$atch."<br>");
		}
		return array($strPhy, $strTech);
	}


	function getProInfo(){		

		//Get Data	
		$arr = $this->getData();

		//Check if Form not empty
		if(count($arr["forms"]) > 0){
			$arrForms = array();
			//Loop--
			foreach($arr["forms"] as $key => $val){			
				$arrForms[$key]["DOS"] = (!empty($val["DOS"]) && ($val["DOS"] != "&nbsp;")) ? $val["DOS"] : "";
				$arrForms[$key]["TOS"] = (!empty($val["TOS"]) && ($val["TOS"] != "00:00:00")) ? $val["TOS"] : "";
				
				$arrForms[$key]["scribedBy"][] = (!empty($val["scribedBy"]) && 
													($val["scribedBy"] != "&nbsp;")) ? $val["scribedBy"] : "";
				$arrForms[$key]["phy"] = "";
				$arrForms[$key]["tech"] = "";
				

				$arrPhy =  array();
				$arrTech =  array();
				$arrOthr =  array();
				
				//Tests---
				$arrTest = array("vf","nfa","oct","pachy",
								 "ivfa","disc","disc_ex","topo",
								 "tother","labs","bscan","cellcnt");

				$arrTestTitle = array("VF","NFA","OCT","Pachy",
									  "IVFA","Fundus","External/Anterior","Topography",
									  "Test Other", "Laboratories", "B-Scan", "Cell Count" );
				foreach($arrTest as $k => $v){					
					if(!empty($val[$v]["perform"]) || !empty($val[$v]["interpret"])){
						//Perform
						if(!empty($val[$v]["perform"])){	
							$tid = $val[$v]["perform"];
							$tmpkey = $arrTestTitle[$k];	
							$tmp = $this->getUserName($tid, 1);						
							
							if(in_array($tmp["type"],$this->arrPhyType)){	
								$arrPhy[$tid]["name"] = $tmp["name"];
								$arrPhy[$tid]["roles"]["tests"]["performed"][] = $tmpkey;
							}else if(in_array($tmp["type"],$this->arrTechType)){
								$arrTech[$tid]["name"] = $tmp["name"];
								$arrTech[$tid]["roles"]["tests"]["performed"][] = $tmpkey;		
							}else{
								$arrOthr[$tid]["name"] = $tmp["name"];
								$arrOthr[$tid]["roles"]["tests"]["performed"][] = $tmpkey;		
							}

						}
						//---

						//Interpreted
						if(!empty($val[$v]["interpret"])){	
							$tid = $val[$v]["interpret"];
							$tmpkey = $arrTestTitle[$k];	
							$tmp = $this->getUserName($tid, 1);
							if(in_array($tmp["type"],$this->arrPhyType)){	
								$arrPhy[$tid]["name"] = $tmp["name"];
								$arrPhy[$tid]["roles"]["tests"]["interpreted"][] = $tmpkey;		
							
							}else if(in_array($tmp["type"],$this->arrTechType)){
								$arrTech[$tid]["name"] = $tmp["name"];
								$arrTech[$tid]["roles"]["tests"]["interpreted"][] = $tmpkey;	
							}else{
								$arrOthr[$tid]["name"] = $tmp["name"];
								$arrOthr[$tid]["roles"]["tests"]["interpreted"][] = $tmpkey;	
							}
						}
						//---
					}						
				}
				//---Tests
				//SuperBill---
				// phy						
				if(!empty($val["superbill"]["phy"])){
					$tid = $val["superbill"]["phy"];
					$tmpkey = "Physician";	
					$tmp = $this->getUserName($tid, 1);
					if(in_array($tmp["type"],$this->arrPhyType)){	
						$arrPhy[$tid]["name"] = $tmp["name"];
						$arrPhy[$tid]["roles"]["Superbill"][] = $tmpkey;							
					
					}else if(in_array($tmp["type"],$this->arrTechType)){
						$arrTech[$tid]["name"] = $tmp["name"];
						$arrTech[$tid]["roles"]["Superbill"][] = $tmpkey;
					}else{
						$arrOthr[$tid]["name"] = $tmp["name"];
						$arrOthr[$tid]["roles"]["Superbill"][] = $tmpkey;
					}
				}
				
				//---SuperBill
				//Signer---
				if(!empty($val["signer"])){
					$tid = $val["signer"];
					$tmpkey = "Signer";	
					$tmp = $this->getUserName($tid, 1);					
					if(in_array($tmp["type"],$this->arrPhyType)){	
						$arrPhy[$tid]["name"] = $tmp["name"];
						$arrPhy[$tid]["roles"]["Chart"][] = $tmpkey;
					}else if(in_array($tmp["type"],$this->arrTechType)){
						$arrTech[$tid]["name"] = $tmp["name"];
						$arrTech[$tid]["roles"]["Chart"][] = $tmpkey;							
					}else{
						$arrOthr[$tid]["name"] = $tmp["name"];
						$arrOthr[$tid]["roles"]["Chart"][] = $tmpkey;							
					}
				}
				//---Signer
				//Cosigner---
				if(!empty($val["cosigner"])){
					$tid = $val["cosigner"];
					$tmpkey = "Co-signer";	
					$tmp = $this->getUserName($tid, 1);
					if(in_array($tmp["type"],$this->arrPhyType)){	
						$arrPhy[$tid]["name"] = $tmp["name"];
						$arrPhy[$tid]["roles"]["Chart"][] = $tmpkey;							
					
					}else if(in_array($tmp["type"],$this->arrTechType)){
						$arrTech[$tid]["name"] = $tmp["name"];
						$arrTech[$tid]["roles"]["Chart"][] = $tmpkey;							
					}else{
						$arrOthr[$tid]["name"] = $tmp["name"];
						$arrOthr[$tid]["roles"]["Chart"][] = $tmpkey;							
					}
				}
				//---Cosigner
				//MR---
				for($i=1;$i<=10;$i++){
					if(!empty($val["mr".$i])){
						
						$tid = $val["mr".$i];
						$tmpkey = "MR ".$i;	
						$tmp = $this->getUserName($tid, 1);
						if(in_array($tmp["type"],$this->arrPhyType)){	
							$arrPhy[$tid]["name"] = $tmp["name"];
							$arrPhy[$tid]["roles"]["MR"][] = $tmpkey;							
						
						}else if(in_array($tmp["type"],$this->arrTechType)){
							$arrTech[$tid]["name"] = $tmp["name"];
							$arrTech[$tid]["roles"]["MR"][] = $tmpkey;							
						}else{
							$arrOthr[$tid]["name"] = $tmp["name"];
							$arrOthr[$tid]["roles"]["MR"][] = $tmpkey;							
						}
					}
				}
				
				//---MR
				//Amsler Grid---				
				if(!empty($val["amsler"])){
					$tid = $val["amsler"];
					$tmpkey = "Amsler Grid";	
					$tmp = $this->getUserName($tid, 1);
					if(in_array($tmp["type"],$this->arrPhyType)){	
						$arrPhy[$tid]["name"] = $tmp["name"];
						$arrPhy[$tid]["roles"]["Exams"][] = $tmpkey;							
					
					}else if(in_array($tmp["type"],$this->arrTechType)){
						$arrTech[$tid]["name"] = $tmp["name"];
						$arrTech[$tid]["roles"]["Exams"][] = $tmpkey;							
					}else{
						$arrOthr[$tid]["name"] = $tmp["name"];
						$arrOthr[$tid]["roles"]["Exams"][] = $tmpkey;							
					}
				}
				//---Amsler Grid
				//Ophtha---				
				if(!empty($val["ophtha"])){
					$tid = $val["ophtha"];
					$tmpkey = "Ophtha";	
					$tmp = $this->getUserName($tid, 1);
					if(in_array($tmp["type"],$this->arrPhyType)){	
						$arrPhy[$tid]["name"] = $tmp["name"];
						$arrPhy[$tid]["roles"]["Exams"][] = $tmpkey;							
					
					}else if(in_array($tmp["type"],$this->arrTechType)){
						$arrTech[$tid]["name"] = $tmp["name"];
						$arrTech[$tid]["roles"]["Exams"][] = $tmpkey;							
					}else{
						$arrOthr[$tid]["name"] = $tmp["name"];
						$arrOthr[$tid]["roles"]["Exams"][] = $tmpkey;							
					}
				}
				//---Ophtha

				//A-scan---
				//OD
				if(!empty($val["ascan"]["od"])){
					$tid = $val["ascan"]["od"];
					$tmpkey = "OD";	
					$tmp = $this->getUserName($tid, 1);
					if(in_array($tmp["type"],$this->arrPhyType)){	
						$arrPhy[$tid]["name"] = $tmp["name"];
						$arrPhy[$tid]["roles"]["Ascan"][] = $tmpkey;							
					
					}else if(in_array($tmp["type"],$this->arrTechType)){
						$arrTech[$tid]["name"] = $tmp["name"];
						$arrTech[$tid]["roles"]["Ascan"][] = $tmpkey;							
					}else{
						$arrOthr[$tid]["name"] = $tmp["name"];
						$arrOthr[$tid]["roles"]["Ascan"][] = $tmpkey;							
					}
				}
				//OS
				if(!empty($val["ascan"]["os"])){
					$tid = $val["ascan"]["os"];
					$tmpkey = "OS";	
					$tmp = $this->getUserName($tid, 1);
					if(in_array($tmp["type"],$this->arrPhyType)){	
						$arrPhy[$tid]["name"] = $tmp["name"];
						$arrPhy[$tid]["roles"]["Ascan"][] = $tmpkey;							
					
					}else if(in_array($tmp["type"],$this->arrTechType)){
						$arrTech[$tid]["name"] = $tmp["name"];
						$arrTech[$tid]["roles"]["Ascan"][] = $tmpkey;							
					}else{
						$arrOthr[$tid]["name"] = $tmp["name"];
						$arrOthr[$tid]["roles"]["Ascan"][] = $tmpkey;							
					}
				}
				//---A-scan		
				
				//IOL_Master---
				//OD
				if(!empty($val["iol_master"]["od"])){
					$tid = $val["iol_master"]["od"];
					$tmpkey = "OD";	
					$tmp = $this->getUserName($tid, 1);
					if(in_array($tmp["type"],$this->arrPhyType)){	
						$arrPhy[$tid]["name"] = $tmp["name"];
						$arrPhy[$tid]["roles"]["IOL_Master"][] = $tmpkey;							
					
					}else if(in_array($tmp["type"],$this->arrTechType)){
						$arrTech[$tid]["name"] = $tmp["name"];
						$arrTech[$tid]["roles"]["IOL_Master"][] = $tmpkey;							
					}else{
						$arrOthr[$tid]["name"] = $tmp["name"];
						$arrOthr[$tid]["roles"]["IOL_Master"][] = $tmpkey;							
					}
				}
				//OS
				if(!empty($val["iol_master"]["os"])){
					$tid = $val["iol_master"]["os"];
					$tmpkey = "OS";	
					$tmp = $this->getUserName($tid, 1);
					if(in_array($tmp["type"],$this->arrPhyType)){	
						$arrPhy[$tid]["name"] = $tmp["name"];
						$arrPhy[$tid]["roles"]["IOL_Master"][] = $tmpkey;							
					
					}else if(in_array($tmp["type"],$this->arrTechType)){
						$arrTech[$tid]["name"] = $tmp["name"];
						$arrTech[$tid]["roles"]["IOL_Master"][] = $tmpkey;							
					}else{
						$arrOthr[$tid]["name"] = $tmp["name"];
						$arrOthr[$tid]["roles"]["IOL_Master"][] = $tmpkey;							
					}
				}
				//---IOL_Master		

				//CCHx---
				//Signer
				if(!empty($val["ccHx"]["signer"])){
					$tid = $val["ccHx"]["signer"];
					$tmpkey = "Signer";	
					$tmp = $this->getUserName($tid, 1);
					if(in_array($tmp["type"],$this->arrPhyType)){	
						$arrPhy[$tid]["name"] = $tmp["name"];
						$arrPhy[$tid]["roles"]["CCHx."][] = $tmpkey;							
					
					}else if(in_array($tmp["type"],$this->arrTechType)){
						$arrTech[$tid]["name"] = $tmp["name"];
						$arrTech[$tid]["roles"]["CCHx."][] = $tmpkey;
					}else{
						$arrOthr[$tid]["name"] = $tmp["name"];
						$arrOthr[$tid]["roles"]["CCHx."][] = $tmpkey;
					}
				}
				//Cosigner
				if(!empty($val["ccHx"]["coSigner"])){
					$tid = $val["ccHx"]["coSigner"];
					$tmpkey = "Co-signer";	
					$tmp = $this->getUserName($tid, 1);
					if(in_array($tmp["type"],$this->arrPhyType)){	
						$arrPhy[$tid]["name"] = $tmp["name"];
						$arrPhy[$tid]["roles"]["CCHx."][] = $tmpkey;							
					
					}else if(in_array($tmp["type"],$this->arrTechType)){
						$arrTech[$tid]["name"] = $tmp["name"];
						$arrTech[$tid]["roles"]["CCHx."][] = $tmpkey;
					}else{
						$arrOthr[$tid]["name"] = $tmp["name"];
						$arrOthr[$tid]["roles"]["CCHx."][] = $tmpkey;
					}
				}
				//---CCHx
				//create_by
				if(!empty($val["create_by"])){
					$tid = $val["create_by"];
					$tmpkey = "Creator";
					$tmp = $this->getUserName($tid, 1);
					if(in_array($tmp["type"],$this->arrPhyType)){	
						$arrPhy[$tid]["name"] = $tmp["name"];
						$arrPhy[$tid]["roles"]["Chart"][] = $tmpkey;							
					
					}else if(in_array($tmp["type"],$this->arrTechType)){
						$arrTech[$tid]["name"] = $tmp["name"];
						$arrTech[$tid]["roles"]["Chart"][] = $tmpkey;							
					}else{
						$arrOthr[$tid]["name"] = $tmp["name"];
						$arrOthr[$tid]["roles"]["Chart"][] = $tmpkey;							
					}
				}
				//finalizerId
				if(!empty($val["finalizerId"])){
					$tid = $val["finalizerId"];
					$tmpkey = "Finalizer";
					$tmp = $this->getUserName($tid, 1);
					if(in_array($tmp["type"],$this->arrPhyType)){	
						$arrPhy[$tid]["name"] = $tmp["name"];
						$arrPhy[$tid]["roles"]["Chart"][] = $tmpkey;							
					
					}else if(in_array($tmp["type"],$this->arrTechType)){
						$arrTech[$tid]["name"] = $tmp["name"];
						$arrTech[$tid]["roles"]["Chart"][] = $tmpkey;							
					}else{
						$arrOthr[$tid]["name"] = $tmp["name"];
						$arrOthr[$tid]["roles"]["Chart"][] = $tmpkey;							
					}
				}				
				
				//provids
				if(!empty($val["provIds"])){					
					$stid = $val["provIds"];
					$stid = preg_replace("/,\s*/",",",$stid);
					$artid=explode(",", $stid);
					if(count($artid)>0){
						$artid=array_unique($artid);
						$tmpkey = "Worker";
						foreach($artid as $k => $v){
							if(!empty($v)){								
								$tid = $v;
								$tmp = $this->getUserName($tid, 1);
								if(empty($tmp["name"])){ continue; }
								
								//Role --
								$oRoleAs = new RoleAs($tid);
								$ar_usr_roles = $oRoleAs->get_user_roles($val["formId"]);
								if(!in_array($tid, $arrForms[$key]["scribedBy"]) && in_array("13", $ar_usr_roles)){
									//add scribe
									$arrForms[$key]["scribedBy"][]=$tid;
									if(count($ar_usr_roles)==1){	continue; }
								}								
								//Role --								
								
								if($this->recursive_array_search($tmp["name"],$arrPhy)!==false ||
									$this->recursive_array_search($tmp["name"],$arrTech)!==false ||
									$this->recursive_array_search($tmp["name"],$arrOthr)!==false){continue;}
								if(in_array($tmp["type"],$this->arrPhyType)){	
									$arrPhy[$tid]["name"] = $tmp["name"];
									$arrPhy[$tid]["roles"]["Chart"][] = $tmpkey;
								}else if(in_array($tmp["type"],$this->arrTechType)){
									$arrTech[$tid]["name"] = $tmp["name"];
									$arrTech[$tid]["roles"]["Chart"][] = $tmpkey;							
								}else{
									$arrOthr[$tid]["name"] = $tmp["name"];
									$arrOthr[$tid]["roles"]["Chart"][] = $tmpkey;							
								}								
							}
						}
					}
				}				
				
				//Set Values 
				$arrForms[$key]["phy"] = $arrPhy;
				$arrForms[$key]["tech"] = $arrTech;
				$arrForms[$key]["other"] = $arrOthr;
			}
			//---Loop
		}
		//---Check Forms
		return array("forms"=>$arrForms,"Prescriptions"=>$arrPerscription);
	}

	function recursive_array_search($needle,$haystack) {
		foreach($haystack as $key=>$value) {
			$current_key=$key;
			if($needle==$value || (is_array($value) && $this->recursive_array_search($needle,$value) !== false)) {
				return $current_key;
			}
		}
		return false;
	}	

	function getHTML($flgBdr=1){	
		
		$str = "";
		//Get Provider Info	
		$arr = $this->getProInfo();
		if(count($arr["forms"]) > 0){

			//Note: set same width in below row also
			$str .= "<table border=\"".$flgBdr."\" cellpadding=\"1\" cellspacing=\"0\" class=\"table table-bordered table-striped\">
					 <tr class=\"grythead\">
					 <th><b>DOS</b></th>
					 <th><b>Physician</b></th>
					 <th><b>Technicians</b></th>
					 <th><b>Scribe</b></th>
					 <th><b>Other</b></th>
					 </tr>
					";			
			
			foreach($arr["forms"] as $key=> $val){				

				$tmpDt = $val["DOS"];
				$tmpTm = $val["TOS"];

				if(!empty($tmpDt) && !empty($tmpTm)){
					$tmpDt = $tmpDt." ".$tmpTm;
				}				
				
				$tmpPhy = "";
				$arrPhy = $val["phy"];
				$tmpTech = "";
				$arrTech = $val["tech"];
				
				$tmpScribe ="";
				if(count($val["scribedBy"])>0){
					foreach($val["scribedBy"] as $kscrb => $vscrb){
						$tScribe = $this->getUserName($vscrb);
						if(!empty($tScribe)){ 
							if(!empty($tmpScribe)){ $tmpScribe .= ", "; }
							$tmpScribe .="".$tScribe;				
						}
					}
				}
				
				$tmpOther = "";
				$arrOther = $val["other"];

				//Formatting---
				//Check ArrPhy and Arrtech
				// make strings and asign
				//Debugging
					//print("<br>PHY<br><pre>");
					//print_r($arrPhy);
					//print("<br>Tech<br>");
					//print_r($arrTech);
				//Debugging

				//Now Values are organised in array
				//Make a String of Roles of Physicians and Technicians
				
				$arrUsr = array($arrPhy,$arrTech,$arrOther);
				$strUsr = array();
				
				for($i=0;$i<3;$i++){				

					//Usr
					$strUsr[$i] = "";
					if(count($arrUsr[$i]) > 0){
						
						if($i == 0){
							
							$strUsr[$i] = "<table width=\"100%\"><tr>";
							
							$strSigner = $strCosigner = "";
							foreach($arrUsr[$i] as $k=>$v){
								if(count($v["roles"]["Chart"]) > 0){
									if(in_array("Signer",$v["roles"]["Chart"])){
										$tmp = "";
										$tmp .= "<div>".implode(", ",$v["roles"]["Chart"])."</div>";
										$strSigner = "<td>".$v["name"]."".$tmp.
														"</td>";
										
									}else if(in_array("Co-signer",$v["roles"]["Chart"])){
										$tmp = "";
										$tmp .= "<div>".implode(", ",$v["roles"]["Chart"])."</div>";
										$strCosigner = "<td>".$v["name"].$tmp.
														"</td>";
									}
									//
								}								
							}
							
							//Signer
							if(!empty($strSigner)){
								$strUsr[$i] .= $strSigner;
							}

							//Co - signer
							if(!empty($strCosigner)){
								$strUsr[$i] .= $strCosigner;
							}							
							
							$strUsr[$i] .= "</tr></table>";


						}else{

							$c=1;
							$strUsr[$i] = "<table>";
							foreach($arrUsr[$i] as $k=>$v){
								$tmp = "";

								//Roles						
								//Signer
								//Consigner
								if(count($v["roles"]["Chart"]) > 0){
									$tmp .= "<div>".implode(", ",$v["roles"]["Chart"])."</div>";
								}

								$strUsr[$i] .= "<tr valign=\"top\">".
												"<td>".$v["name"].
												"<i>".$tmp."</i></td></tr>";
								$c++;
							}
							$strUsr[$i] .= "</table>";
						
						}

						// signer - cosigner
					}
				}				
				
				
				//Set Values 
				$tmpPhy = $strUsr[0];
				$tmpTech = $strUsr[1];
				$tmpOther = $strUsr[2];
				//---Formatting
				
				$str .= "
					<tr valign=\"top\">
					<!-- DOS -->
					<td >".$tmpDt."</td> 	
					<!-- DOS -->
					<!-- Physicians -->
					<td >".$tmpPhy."</td>	
					<!-- Physicians -->
					<!-- Technician -->
					<td >".$tmpTech."</td>
					<!-- Technician -->
					<!-- scribe -->
					<td >".$tmpScribe."</td>
					<!-- scribe -->
					<!-- other -->
					<td >".$tmpOther."</td>
					<!-- other -->
					</tr>		
				";

			}

			$str .= "</table>";

		}

		//Prescriptions
		if(count($arr["Prescriptions"]) > 0){
			$str .= "";
		}
	
		return $str;

	}

	//PDF --	
	function setPdfHeader($nm){
		//times 12
		$this->SetFont('helvetica','',12);
		//Background color
		$this->SetFillColor(200,220,255);
		//Title
		$this->Cell(70,6,"List Patient Providers",$this->bdr,0,'L',1);
		$this->Cell(0,6,"".$nm,$this->bdr,1,'L',1);
		//Line break
		$this->Ln(1);
	}
	
	public function MultiRow($tmDt, $tmPhy, $tmTech, $tmScr, $tmOthr) {
		//MultiCell($w, $h, $txt, $border=0, $align='J', $fill=0, $ln=1, $x='', $y='', $reseth=true, $stretch=0)
		/*
		void writeHTMLCell( float $w, float $h, float $x, float $y, [string $html = ''], [mixed $border = 0], [int $ln = 0], [int $fill = 0], [boolean $reseth = true], [string $align = ''], [boolean $autopadding = true]) 
		*/
		
		//Column width
		$cw1=20;
		$cw2=50;
		$cw3=45;
		$cw4=30;
		$cw5=45;
		//--

		$page_start = $this->getPage();
		$y_start = $this->GetY();
		
		// write the first cell		
		$this->MultiCell($cw1, 0, $tmDt, $this->bdr, 'L', 0, 2, '', '', true, 0);		
		$page_end_1 = $this->getPage();
		$y_end_1 = $this->GetY();
		$x_end_1 = $this->GetX();
		$this->setPage($page_start);
		
		// write the second cell
		//$this->MultiCell($cw2, 0, $tmPhy, $this->bdr, 'L', 0, 2, $x_end_1 ,$y_start, true, 0);		
		$this->writeHTMLCell($cw2, 0,"", "", $tmPhy, $this->bdr, 2, 0, true, 'L', true); //$x_end_1 ,$y_start, true, 0);		

		$page_end_2 = $this->getPage();
		$y_end_2 = $this->GetY();
		$x_end_2 = $this->GetX();
		$this->setPage($page_start);

		// write the Third cell
		$this->MultiCell($cw3, 0, $tmTech, $this->bdr, 'L', 0, 2, $x_end_2 ,$y_start, true, 0);		
		$page_end_3 = $this->getPage();
		$y_end_3 = $this->GetY();
		$x_end_3 = $this->GetX();
		$this->setPage($page_start);

		// write the forth cell
		$this->MultiCell($cw4, 0, $tmScr, $this->bdr, 'L', 0, 2, $x_end_3 ,$y_start, true, 0);
		$page_end_4 = $this->getPage();
		$y_end_4 = $this->GetY();
		$x_end_4 = $this->GetX();
		$this->setPage($page_start);

		// write the fifth cell
		$this->MultiCell($cw5, 0, $tmOthr, $this->bdr, 'L', 0, 1, $x_end_4 ,$y_start, true, 0);
		$page_end_5 = $this->getPage();
		$y_end_5 = $this->GetY();
		$x_end_5 = $this->GetX();
		$this->setPage($page_start);

		
		// set the new row position by case	
		$mxPage = max($page_end_1,$page_end_2,$page_end_3,$page_end_4,$page_end_5);
		$mxY = max($y_end_1,$y_end_2,$y_end_3,$y_end_4,$y_end_5);

		if ($mxPage == $page_start) { //end first page
			$ynew = $mxY;			
		} elseif (($page_end_1 == $page_end_2) && ($page_end_1 == $page_end_3) &&
					($page_end_1 == $page_end_4) && ($page_end_1 == $page_end_5)	
					) { //end same page but not first page
			$ynew = $mxY;
		}elseif (($page_end_1 > $page_end_2) && ($page_end_1 > $page_end_3)
					&& ($page_end_1 > $page_end_4) && ($page_end_1 > $page_end_5)	
					) { //1 ends bigger page			
			$ynew = $y_end_1;
		} else if(($page_end_2 > $page_end_1) && ($page_end_2 > $page_end_3)
					&& ($page_end_2 > $page_end_4) && ($page_end_2 > $page_end_5) 
			) {			
			$ynew = $y_end_2; //2 ends bigger page
		} else if(($page_end_3 > $page_end_1) && ($page_end_3 > $page_end_2)
					&& ($page_end_3 > $page_end_4) && ($page_end_3 > $page_end_5) 
			) {			
			$ynew = $y_end_3; //3 ends bigger page
		} else if(($page_end_4 > $page_end_1) && ($page_end_4 > $page_end_2)
					&& ($page_end_4 > $page_end_3) && ($page_end_4 > $page_end_5) 
			) {			
			$ynew = $y_end_4; //4 ends bigger page
		} else{			
			$ynew = $y_end_5; //5 ends bigger page
		}
		
		
		$this->setPage($mxPage);
		$this->SetXY($this->GetX(),$ynew);
	}	
	
	function getPdf(){
		$str = $this->getHTML();
		if(!empty($str)){
			// remove default header/footer
			$this->SetFont('helvetica','',7);
			$this->writeHTML($str, true, 0, true, 0);
			$this->lastPage();
		}
	}
	
	function getPdf2(){
		
		//Get Provider Info	
		$arr = $this->getProInfo();			
		
		if(count($arr["forms"]) > 0){
			//MultiCell($w, $h, $txt, $border=0, $align='J', $fill=0, $ln=1, $x='', $y='', $reseth=true, $stretch=0)			
			
			//Titles 
			$this->SetFont('helvetica','b',9);			
			$this->MultiRow("DOS", "Physician", "Technicians", "Scribe", "Other");
			//--

			//Loop
			foreach($arr["forms"] as $key=> $val){				

				$tmpDt = $val["DOS"];
				$tmpTm = $val["TOS"];

				if(!empty($tmpDt) && !empty($tmpTm)){
					$tmpDt = $tmpDt." ".$tmpTm;
				}	
				
				$tmpPhy = "";
				$arrPhy = $val["phy"];
				$tmpTech = "";
				$arrTech = $val["tech"];
				$tmpScribe = $this->getUserName($val["scribedBy"]);
				$tmpOther = "";
				$arrOther = $val["other"];

				//Now Values are organised in array
				//Make a String of Roles of Physicians and Technicians
				$strPhy = $strTech = $strOther = "";
				$arrUsr = array($arrPhy,$arrTech,$arrOther);
				$strUsr = array();

				for($i=0;$i<3;$i++){					
					//Usr
					if(count($arrUsr[$i]) > 0){
						
						if($i==0){

							$strSigner = $strCosigner = "";
							foreach($arrUsr[$i] as $k=>$v){
								if(count($v["roles"]["Chart"]) > 0){
									if(in_array("Signer",$v["roles"]["Chart"])){
										$strSigner .= "".$v["name"].
													  "\n".$tmp."\n";

									}else if(in_array("Co-signer",$v["roles"]["Chart"])){
										$strCosigner .= "".$v["name"].
														"\n".$tmp."\n";

									}
								}
							}
							
							//Signer
							if(!empty($strSigner)){
								$strUsr[$i] .= $strSigner;
							}

							//Co - signer
							if(!empty($strCosigner)){
								$strUsr[$i] .= $strCosigner;
							}

						}else{
							$c=1;					
							$strUsr[$i] = "";
							foreach($arrUsr[$i] as $k=>$v){
								$tmp = "";
								//$tmp .= $val["name"];

								//Roles						
								//Signer
								//Consigner
								if(count($v["roles"]["Chart"]) > 0){
									$tmp .= implode(", ",$v["roles"]["Chart"])."\n";
								}						
								
								$strUsr[$i] .= "".
											//"".$c.".".
											"".$v["name"].
											"\n".$tmp."\n";
								$c++;
							}
							$strUsr[$i] .= "";
						}
					}
				}
				
				//Set Values 
				$tmpPhy = (!empty($strUsr[0])) ? $strUsr[0] : "";
				$tmpTech = (!empty($strUsr[1])) ? $strUsr[1] : "";
				$tmpOther = (!empty($strUsr[2])) ? $strUsr[2] : "";

				//---Formatting

				//Add Row
				//$tmpDt= $tmpPhy= $tmpTech= $tmpScribe = "HELLO";				
				if(!empty($tmpDt) || !empty($tmpPhy) || !empty($tmpTech) || 
					!empty($tmpScribe) || !empty($tmpOther)){
					$this->SetFont('times','',9);
					$this->MultiRow($tmpDt, $tmpPhy, $tmpTech, $tmpScribe,$tmpOther);
				}
			}
			//Loop --
		}			
		//if
	}	

	//PDF --
}

?>