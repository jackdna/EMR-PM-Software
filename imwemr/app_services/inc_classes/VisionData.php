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
File: VisionData.php
Purpose: This file provides chartnote vision data of a patient visit.
Access Type : Include file
*/

//GetWVSummery
set_time_limit(900);
include_once(dirname(__FILE__).'/patient_app.php');
include_once($GLOBALS['srcdir']."/classes/work_view/ChartNote.php");

class VisionData extends patient_app{
	public $ar_req;
	
	private $patient_id;
	private $form_id;
	private $encounter_id;
	
	public function __construct($patient_id, $form_id){
		parent::__construct();
		$this->patient_id= $patient_id;
		$this->form_id = $form_id;		
	}
	
	public function getCPControl(){
		
		$echo="";		
		$sql = "SELECT * FROM chart_icp_color WHERE form_id= '".$this->form_id."' AND patient_id='".$this->patient_id."' AND purged='0' ";
		$row = sqlQuery($sql);		
		if($row==false){
			//GET PAST values
			//Obj
			$oCN = new ChartNote($this->patient_id,$this->form_id);
			$elem_dos = $oCN->getDos();			
			$elem_dos = wv_formatDate($elem_dos,0,0,"insert");
			//$elem_dos = $oCN->formatDate($elem_dos,0,0,"insert");
			$res = $oCN->getLastRecord("chart_icp_color","form_id",0," * ",$elem_dos); //
			if($res!=false){$row=$res->fields;}else{$row=false;}
			$elem_edit_icp=0;
			/*
			$elem_control_css = "inact";
			$elem_controlValueOd_css = "inact";
			$elem_controlValueOd_denom_css = "inact";
			$elem_control_os_css = "inact";
			$elem_controlValueOs_css = "inact";
			$elem_controlValueOs_denom_css = "inact";
			$elem_comm_colorVis_css = "inact";
			*/
		}else{
			$elem_edit_icp=1;
			/*
			$elem_control_css = "active";
			$elem_controlValueOd_css = "active";
			$elem_controlValueOd_denom_css = "active";
			$elem_control_os_css = "active";
			$elem_controlValueOs_css = "active";
			$elem_controlValueOs_denom_css = "active";
			$elem_comm_colorVis_css = "active";
			*/
		}

		if($row!=false){
			$elem_color_sign_od=$row["control_od"];
			$elem_color_od_1 = $row["cval1_od"];
			$elem_color_od_2 = $row["cval2_od"];
			
			$elem_color_sign_os=$row["control_os"];
			$elem_color_os_1 = $row["cval1_os"];
			$elem_color_os_2 = $row["cval2_os"];
			
			$elem_comm_colorVis = $row["icp_desc"];
			$ut_elem=($elem_edit_icp==1)?$row["ut_elem"]:"";
		}
		
		
		return array("OD"=>array("sign"=>"".$elem_color_sign_od, "numerator"=>"".$elem_color_od_1, "denominator"=>"".$elem_color_od_2), 
						"OS"=>array("sign"=>"".$elem_color_sign_os, "numerator"=>"".$elem_color_os_1, "denominator"=>"".$elem_color_os_2), 
							"Desc"=>"".$elem_comm_colorVis );
		
		
		
	}
	public function getStereopsis(){
		//include_once($GLOBALS['incdir']."/chart_notes/common/ChartNote.php");
		$sql = "SELECT * FROM chart_steropsis WHERE form_id= '".$this->form_id."' AND patient_id='".$this->patient_id."' AND purged='0' ";		
		$row = sqlQuery($sql);		
		if($row==false){
			//GET PAST values
			//Obj
			$oCN = new ChartNote($this->patient_id,$this->form_id);
			$elem_dos = $oCN->getDos();			
			$elem_dos = wv_formatDate($elem_dos,0,0,"insert");			
			//$elem_dos = $oCN->formatDate($elem_dos,0,0,"insert");			
			$res = $oCN->getLastRecord("chart_steropsis","form_id",0," * ",$elem_dos); //
			if($res!=false){$row=$res->fields;}else{$row=false;}
			$elem_edit=0;				
			$elem_stereo_SecondsArc_css = "inact";			
			
		}else{
			$elem_edit=1;				
			$elem_stereo_SecondsArc_css = "active";				
		}

		if($row!=false){
			$elem_stereo_SecondsArc=$row["seconds_of_arc"];				
			$ut_elem=($elem_edit==1) ? $row["ut_elem"] : "" ;
		}	
	
		return array("sec of arc"=>"".$elem_stereo_SecondsArc);
		
	}
	public function getW4Dot(){		
		//include_once($GLOBALS['incdir']."/chart_notes/common/ChartNote.php");		
		
		$sql = "SELECT * FROM chart_w4dot WHERE form_id= '".$this->form_id."' AND patient_id='".$this->patient_id."' AND purged='0'  ";		
		$row = sqlQuery($sql);		
		if($row==false){
			//GET PAST values
			//Obj
			$oCN = new ChartNote($this->patient_id,$this->form_id);
			$elem_dos = $oCN->getDos();			
			$elem_dos = wv_formatDate($elem_dos,0,0,"insert");			
			//$elem_dos = $oCN->formatDate($elem_dos,0,0,"insert");			
			$res = $oCN->getLastRecord("chart_w4dot","form_id",0," * ",$elem_dos); //
			if($res!=false){$row=$res->fields;}else{$row=false;}
			$elem_edit=0;				
			
			$elem_w4dot_distance_css = "inact";
			$elem_w4dot_near_css = "inact";
			$elem_worth4dot_css = "inact";
			
		}else{
			$elem_edit=1;				
			$elem_w4dot_distance_css = "active";
			$elem_w4dot_near_css = "active";
			$elem_worth4dot_css = "active";		
		}

		if($row!=false){				
			$elem_w4dot_distance=$row["distance"];
			$elem_w4dot_near =$row["near"];
			$elem_worth4dot=$row["desc_w4dot"];				
			$ut_elem=($elem_edit==1)?$row["ut_elem"]:"";
		}		
	
		return array("Distance"=>"".$elem_w4dot_distance, "Near"=>"".$elem_w4dot_near, "Desc"=>"".$elem_worth4dot);
	}	
	
	public function getAmslerGridCvf(){
		require_once dirname(__FILE__)."/GetWVSummery.php";
		$serviceObj1 = new GetWVSummery($this->patient_id,$this->form_id);
		$arrRet = $serviceObj1->getAmsGrdCVF();
		return array("CVF"=>$arrRet[0]["CVF"],"Amsler Grid"=>$arrRet[1]["Amsler Grid"]);
	}
	
	// new function for app //
	public function getVision_app(){	
	
		$arrMainRet=array();
		$patient_id = $this->patient_id;
		$form_id = $this->form_id;
		
		$arrGet = $this->getAmslerGridCvf();
		$arrMainRet= array_merge($arrGet, $arrMainRet);	
		
		
		
		//Vision--
		//include_once($GLOBALS['incdir']."/chart_notes/common/ChartNote.php");		
		include_once($GLOBALS['srcdir']."/classes/work_view/Vision.php");			
		//include_once($GLOBALS['srcdir']."/chart_notes/common/functions.php");		
		//include_once($GLOBALS['srcdir']."/main/main_functions.php");		
		//include_once($GLOBALS['incdir']."/Billing/billing_admin/chart_more_functions.php");
		
		//Vision ------------------
		//Set Default Records
		$elem_providerId = $elem_providerIdOther = $elem_providerIdOther_3 = $elem_providerIdOther_4 = ""; //$_SESSION['authId'];
		//$elem_providerName = $elem_providerNameOther = $elem_providerNameOther_3 = $elem_providerNameOther_4 = showDoctorName($_SESSION['authId']);
		$elem_visNearOdSel1 = $elem_visNearOsSel1 = "CC";
		$elem_visNearOdSel2 = $elem_visNearOsSel2 = "SC";
		$elem_visDisOdTxt1 = $elem_visDisOdTxt2 = $elem_visDisOsTxt1 = $elem_visDisOsTxt2 = $elem_visNearOdTxt1= "20/";
		$elem_visNearOdTxt2 = $elem_visNearOsTxt1 = $elem_visNearOsTxt2 = $elem_visPcOdNearTxt2 = $elem_visPcOsNearTxt2 = "20/";
		$elem_visPcOdNearTxt3=$elem_visPcOsNearTxt3=$elem_visMrOdTxt1=$elem_visMrOdTxt2=$elem_visMrOsTxt1="20/";
		$elem_visMrOsTxt2=$elem_visMrOtherOdTxt1=	$elem_visMrOtherOdTxt2=$elem_visMrOtherOsTxt1=$elem_visMrOtherOsTxt2="20/";
		$elem_visPcOdOverrefV=$elem_visPcOsOverrefV=$elem_visPcOdOverrefV2=$elem_visPcOsOverrefV2=$elem_visPcOdOverrefV3="20/";
		$elem_visPcOsOverrefV3=$elem_visBatNlOd=$elem_visBatLowOd=$elem_visBatMedOd=$elem_visBatHighOd=$elem_visBatNlOs="20/";
		$elem_visBatLowOs=$elem_visBatMedOs=$elem_visBatHighOs="20/";
		$elem_mrNoneGiven1 = "None";
		$elem_visMrOtherOdTxt1_3 = $elem_visMrOtherOdTxt2_3 = $elem_visMrOtherOsTxt1_3 = $elem_visMrOtherOsTxt2_3 = "20/";
		//$elem_visMrOtherOdTxt1_4 = $elem_visMrOtherOdTxt2_4 = $elem_visMrOtherOsTxt1_4 = $elem_visMrOtherOsTxt2_4 = "20/";
		$elem_visMrOdSel2Vision = $elem_visMrOtherOdSel2Vision = $elem_visMrOtherOdSel2Vision_3 = "20/";
		$elem_visMrOsSel2Vision = $elem_visMrOtherOsSel2Vision = $elem_visMrOtherOsSel2Vision_3 = "20/";
		$todate = date('m-d-Y');
		//$elem_control = "plus";
		//$elem_control_os = "plus";
		$elem_visSnellan = $elem_visSnellan_near	= "Snellen";
		
		//Obj
		$oVis = new Vision($patient_id,$form_id);

		//GET $dos_ymd
		$dos_ymd = $oVis->getDos($unformatted=1);
		
		
		//Get Past Records
		//if(isset($_GET["visId"]) && !empty($_GET["visId"]))
		//{
			//Get Form id based on patient id
			$sql = "SELECT * FROM chart_vis_master WHERE patient_id = '".$patient_id."' AND form_id = '".$form_id."' ";
			$row = sqlQuery($sql);
			if(($row == false)){ /* Show Previous values in finalized chart also ~&& ($finalize_flag == 0)~ */
				// New
				$elem_editModeVis = "0";
				$vision_edid = "";
				//New Records
				//$row = valuesNewRecordsVision($patient_id);
				$res = $oVis->getLastRecord(" * ",0,$dos_ymd); /*$dos_ymd in getchartinfo.nic.php*/
				if($res!=false){$row=$res->fields;}else{$row=false;}
				$isNewRecord = true;
			}else{
				// Update
				$elem_editModeVis = "1";
				$elem_visId = $row["vis_id"];
				//Default
				if(isset($ar_req["defaultValsVis"]) && ($ar_req["defaultValsVis"] == 1)){
					//New Records
					//$row = valuesNewRecordsVision($patient_id);
					$res = $oVis->getLastRecord(" * ",1,$dos_ymd); /*$dos_ymd in getchartinfo.nic.php*/
					if($res!=false){$row=$res->fields;}else{$row=false;}
					$isNewRecord = true;
				}
			}
			
			//Last Finalized Vision Id
			$resLF = $oVis->getLastRecord(" * ",1,$dos_ymd); /*$dos_ymd in getchartinfo.nic.php*/
			if($resLF!=false){
				$elem_visMasIdLF = $rowLF["id"];
			}
			
			//
			if($row != false){
				
				$elem_visMasId = $row["id"];
				$ar_dis = $oVis->getDistance($elem_visMasId, $elem_editModeVis, $elem_visMasIdLF);
				extract($ar_dis);	
				
				
				$ar_pam = $oVis->getPam($elem_visMasId, $elem_editModeVis);
				extract($ar_pam);	
				
				$ar_sca = $oVis->getSCA($elem_visMasId, $elem_editModeVis);
				extract($ar_sca);	
				
				$ar_ak = $oVis->getAK($elem_visMasId, $elem_editModeVis);
				extract($ar_ak);	
				
				$ar_exo = $oVis->getEXO($elem_visMasId, $elem_editModeVis);			
				extract($ar_exo);	
				
				$ar_bat = $oVis->getBAT($elem_visMasId, $elem_editModeVis);
				extract($ar_bat);	
				
				//Multiple MR --
				list($ar_multiple_mr,$tmp_mr_len)=$oVis->get_mutli_mr_pc("MR",$dos_ymd,$elem_visMasId, $elem_visMasIdLF);	
				extract($ar_multiple_mr);		
				if(!empty($tmp_mr_len)){ $len_mr = $tmp_mr_len; $inclss_mr="in";}					
				//End Multiple MR --				
				
				//Multiple PC --
				list($ar_multiple_pc,$tmp_pc_len)=$oVis->get_mutli_mr_pc("PC",$dos_ymd,$elem_visMasId, $elem_visMasIdLF);	
				extract($ar_multiple_pc);		
				if(!empty($tmp_pc_len)){ $len_pc = $tmp_pc_len; $inclss_pc = "in"; }		
				//End Multiple PC --				
				
				$elem_statusElements = (!isset($isNewRecord) || ($isNewRecord != true)) ? $row["status_elements"] : "" ;
				
				//$elem_noChangeVision = (($elem_editModeVis == "1") && ($isNewRecord != true)) ? $row["examinedNoChange"] : "0" ;
				//Comments
				//$elem_visComments = stripslashes($row["vis_comments"]);
				//Comments			
				//UT Elems //
				$elem_utElems = (!isset($isNewRecord) || ($isNewRecord != true)) ? $row["ut_elem"] : "" ;  
			}
		//}
		
		// Correct Old Data
		$elem_examDateDistance = $elem_examDateARAK = $elem_examDatePC = $elem_examDateMR = $elem_examDateCR = $todate;

		// Separate NoChange Value
		$isClubbed = strpos($elem_visAkDesc,"(*|*)");
		if($isClubbed !== false){
			$elem_examinedNoChange = substr($elem_visAkDesc,$isClubbed+5);
			$elem_visAkDesc = "".trim(substr($elem_visAkDesc,0,$isClubbed));
		}
		// Separate NoChange Value
		$isClubbed = strpos($elem_disDesc,"(*|*)");

		if($isClubbed !== false){
			$elem_disDesc = trim(substr($elem_disDesc,0,$isClubbed));
		}

		if(isset($isNewRecord) && ($isNewRecord == true)){
			$elem_noChangeVision = "0";
		}

		// Separate Dates
		list($elem_visNearDesc,$elem_examDateDistance) = extractDate($elem_visNearDesc);
		list($elem_visArDesc,$elem_examDateARAK) = extractDate($elem_visArDesc);
		list($elem_visPcDesc,$elem_examDatePC) = extractDate($elem_visPcDesc);
		list($elem_visMrDesc,$elem_examDateMR) = extractDate($elem_visMrDesc);
		list($elem_visBatDesc,$elem_examDateCR) = extractDate($elem_visBatDesc);

		$elem_examDateDistance = (!empty($elem_examDateDistance)) ? formatDate4display($elem_examDateDistance) : $todate;
		$elem_examDateARAK = (!empty($elem_examDateARAK)) ? formatDate4display($elem_examDateARAK) : $todate;
		$elem_examDatePC = (!empty($elem_examDatePC)) ? formatDate4display($elem_examDatePC) : $todate;
		$elem_examDateMR = (!empty($elem_examDateMR)) ? formatDate4display($elem_examDateMR) : $todate;
		$elem_examDateCR = (!empty($elem_examDateCR)) ? formatDate4display($elem_examDateCR) : $todate;

		// Desc Default --------
		// Distance 
		$elem_disDescLF="";
		if(empty($elem_disDesc)) // || ($isNewRecord == true)
		{
			$elem_disDesc = ""; //"Description: ";
			if(!empty($elem_visDisOdSel1LF)){
				//$elem_disDesc .= "Distance\r\n";
				$elem_disDesc .= "OD: ".$elem_visDisOdSel1LF;
				$elem_disDesc .= (!empty($elem_visDisOdTxt1LF) && ($elem_visDisOdTxt1LF != "20/")) ? ", ".$elem_visDisOdTxt1LF."" : "" ;//", ".$elem_visDisOdSel2LF.", ".$elem_visDisOdTxt2LF." ";
				$elem_disDesc .= " ";
			}

			if(!empty($elem_visDisOsSel1LF)){
				$elem_disDesc .= "OS: ".$elem_visDisOsSel1LF;
				$elem_disDesc .= (!empty($elem_visDisOsTxt1LF) && ($elem_visDisOsTxt1LF != "20/")) ? ", ".$elem_visDisOsTxt1LF." " : "" ;//", ".$elem_visDisOsSel2LF.", ".$elem_visDisOsTxt2LF." ";
			}

			/*
			if($elem_visNear == "1")
			{
				$elem_disDesc .="\r\n";
				$elem_disDesc .= "OD: ".$elem_visNearOdSel1LF.", ".$elem_visNearOdTxt1LF." "; //", ".$elem_visNearOdSel2LF.", ".$elem_visNearOdTxt2LF." ";
				$elem_disDesc .= "OS: ".$elem_visNearOsSel1LF.", ".$elem_visNearOsTxt1LF." "; //", ".$elem_visNearOsSel2LF.", ".$elem_visNearOsTxt2LF." ";
			}*/
			$elem_disDescLF = $elem_disDesc;
		}
		//Pc+MR Desc default
		if(empty($elem_visPcDesc) || empty($elem_visPcDesc_2) || empty($elem_visPcDesc_3) ||
			empty($elem_visMrDesc) || empty($elem_visMrDescOther) || empty($elem_mr_desc_3) ){
			for($i=1;$i<=3;$i++){
				
				//PC -----
				$j1 = ($i>1) ? "_".$i : "";		
				$zDsc = "elem_visPcDesc".$j1;

				//if(empty($$zDsc) || ($isNewRecord == true)){ //($isNewRecord == true) ||
				if((empty($$zDsc) && (strpos($elem_statusElements, $zDsc."=1") === false))){
					$j2 = ($i>1) ? $i : "";
					
					$zDscLf = "elem_visPcDesc".$j1."LF";
					$$zDscLf = "";
				
					$$zDsc = "";
					$zsel1Lf = "elem_visPcOdS".$j2."LF";
					if(!empty($$zsel1Lf)){
						$$zDsc .= "OD: ".$$zsel1Lf.", ";
						$zsLf = "elem_visPcOdS".$j2."LF";
						$$zDsc .= (!empty($$zsLf)) ? $$zsLf.", " : "";
						$zcLf = "elem_visPcOdC".$j2."LF";
						$$zDsc .= (!empty($$zcLf)) ? $$zcLf.", " : "";
						$zaLf = "elem_visPcOdA".$j2."LF";
						$$zDsc .= (!empty($$zaLf)) ? $$zaLf.", " : "";
						$$zDsc = substr($$zDsc, 0, -2);
					}
					$zsel1Lf = "elem_visPcOsS".$j2."LF";
					if(!empty($$zsel1Lf)){
						$$zDsc .= " OS: ".$$zsel1Lf.", ";
						$zsLf = "elem_visPcOsS".$j2."LF";
						$$zDsc .= (!empty($$zsLf)) ? $$zsLf.", " : "";
						$zcLf = "elem_visPcOsC".$j2."LF";
						$$zDsc .= (!empty($$zcLf)) ? $$zcLf.", " : "";
						$zaLf = "elem_visPcOsA".$j2."LF";
						$$zDsc .= (!empty($$zaLf)) ? $$zaLf.", " : "";
						$$zDsc = substr($$zDsc, 0, -2);
					}
					$$zDscLf = $$zDsc;
				}
				
				//PC-----
				//MR -----
				
				$j2="";	
				if($i >= 2){
					$j2 = "Other";
				}

				$zDsc = ($i==3) ? "elem_mr_desc_3" : "elem_visMrDesc".$j2;	
				//$$zDscLf = "";

				//if(empty($$zDsc) || ($isNewRecord == true)){ //($isNewRecord == true) ||
				if((empty($$zDsc) && (strpos($elem_statusElements, $zDsc."=1") === false))){
					$$zDsc = "";
					$j3="";
					if($i == 3){
						$j3="_".$i;
					}
					$zDscLf = $zDsc."LF";
					
					
					//25june12 :: MR1 - If they type in the comment the only carry the comments forward do not show the previous MR values.
					// Notes : 26-10-2012 it will work now because isnewRecord check is removed
					/*
					if(!empty($$zDscLf)){
					
						$$zDsc = $$zDscLf;
					
					}else{
					*/				
						$tmpzDt="";
						$zsLf = "elem_visMr".$j2."OdS".$j3."LF";
						if(!empty($$zsLf)){
							$$zDsc .= formatDate4display($elem_examDateMRLF)." ";
							$$zDsc .= "OD: ".$$zsLf.", ";
							$zcLf = "elem_visMr".$j2."OdC".$j3."LF";
							$$zDsc .= (!empty($$zcLf)) ? $$zcLf.", " : "";
							$zaLf = "elem_visMr".$j2."OdA".$j3."LF";
							$$zDsc .= (!empty($$zaLf)) ? $$zaLf.", " : "";
							$zaddLf = "elem_visMr".$j2."OdAdd".$j3."LF";
							$$zDsc .= (!empty($$zaddLf)) ? $$zaddLf.", " : "";
							$$zDsc = substr($$zDsc, 0, -2);
						}
						$zsLf = "elem_visMr".$j2."OsS".$j3."LF";
						if(!empty($$zsLf)){
							if(empty($$zDsc)){ $$zDsc .= formatDate4display($elem_examDateMRLF)." ";  }
							$$zDsc .= " OS: ".$$zsLf.", ";
							$zcLf = "elem_visMr".$j2."OsC".$j3."LF";
							$$zDsc .= (!empty($$zcLf)) ? $$zcLf.", " : "";
							$zaLf = "elem_visMr".$j2."OsA".$j3."LF";
							$$zDsc .= (!empty($$zaLf)) ? $$zaLf.", " : "";
							$zaddLf = "elem_visMr".$j2."OsAdd".$j3."LF";
							$$zDsc .= (!empty($$zaddLf)) ? $$zaddLf.", " : "";
							$$zDsc = substr($$zDsc, 0, -2);
						}
						
						$$zDscLf = $$zDsc;
					//}
					
				}		
				
				//MR------		
				
			}
		}
		//
		// Desc Default --------
		
		//?reqModule=chart_notes&service=getVision&phyId=209&patId=60316&form_id=40179
		
		$arrMainRet["Distance"]=array("OD"=>array("".'type_od_1'=>$elem_visDisOdSel1, "".'value_od_1'=>$elem_visDisOdTxt1, "".'type_od_2'=>$elem_visDisOdSel2, "".'value_od_2'=>$elem_visDisOdTxt2), 
		
	"OS"=>array("".'type_os_1'=>$elem_visDisOsSel1, "".'value_os_1'=>$elem_visDisOsTxt1, "".'type_os_2'=>$elem_visDisOsSel2, "".'value_os_2'=>$elem_visDisOsTxt2),
		 
	"DropDown"=>"".$elem_visSnellan, 
	
	"Desc"=>"".$elem_disDesc);
								
		$arrMainRet["AdditionalAcuity"]= array(
					"OD"=>array("".'type_od_1'=>$elem_visDisOdSel3, "".'value_od_1'=>$elem_visDisOdTxt3),
					"OS"=>array("".'type_os_1'=>$elem_visDisOsSel3, "".'value_os_1'=>$elem_visDisOsTxt3), 
					"Desc"=>"".$elem_visDisAct3);
										
		$arrMainRet["Near"]=array(
					"OD"=>array("".'type_od_1'=>$elem_visNearOdSel1, "".'value_od_1'=>$elem_visNearOdTxt1, "".'type_od_2'=>$elem_visNearOdSel2, "".'value_od_2'=>$elem_visNearOdTxt2), 
					"OS"=>array("".'type_os_1'=>$elem_visNearOsSel1, "".'value_os_1'=>$elem_visNearOsTxt1, "".'type_os_2'=>$elem_visNearOsSel2, "".'value_os_2'=>$elem_visNearOsTxt2),
							"DropDown"=>"".$elem_visSnellan_near, "Desc"=>"".$elem_visNearDesc);		
		$arrMainRet["AR"]=array("OD"=>array("S"=>"".$elem_visArOdS, "C"=>"".$elem_visArOdC, "A"=>"".$elem_visArOdA, "Sel1"=>"".$elem_visArOdSel1), 
							"OS"=>array("S"=>"".$elem_visArOsS, "C"=>"".$elem_visArOsC, "A"=>"".$elem_visArOsA, "Sel1"=>"".$elem_visArOsSel1),
							"DropDown"=>"".$elem_visArRefPlace, "Desc"=>"".$elem_visArDesc);			
		$arrMainRet["Cycloplegic AR"]=array("OD"=>array("S"=>"".$elem_visCycArOdS, "C"=>"".$elem_visCycArOdC, "A"=>"".$elem_visCycArOdA, "Sel1"=>"".$elem_visCycArOdSel1), 
									"OS"=>array("S"=>"".$elem_visCycArOsS, "C"=>"".$elem_visCycArOsC, "A"=>"".$elem_visCycArOsA, "Sel1"=>"".$elem_visCycArOsSel1),
									"Desc"=>"".$elem_visCycArDesc);									
		$arrMainRet["K"]=array("OD"=>array("K"=>"".$elem_visAkOdK, "Slash"=>"".$elem_visAkOdSlash, "X"=>"".$elem_visAkOdX), 
							"OS"=>array("K"=>"".$elem_visAkOsK, "Slash"=>"".$elem_visAkOsSlash, "X"=>"".$elem_visAkOsX), 
							"Desc"=>"".$elem_visAkDesc, "KType"=>"".$elem_kType);
		
		$arrMainRet["PC"]["1"]["Initial"] = array("OD"=>array("S"=>"".$elem_visPcOdS, "C"=>"".$elem_visPcOdC, "A"=>"".$elem_visPcOdA, "Add"=>"".$elem_visPcOdAdd, "Sel1"=>"".$elem_visPcOdSel1), 
										"OS"=>array("S"=>"".$elem_visPcOsS, "C"=>"".$elem_visPcOsC, "A"=>"".$elem_visPcOsA, "Add"=>"".$elem_visPcOsAdd, "Sel1"=>"".$elem_visPcOsSel1),
										"Desc"=>"".$elem_visPcDesc, "Near"=>"".$elem_pcNear1);
										
		$arrMainRet["PC"]["1"]["Over Refraction"] = array("OD"=>array("S"=>"".$elem_visPcOdOverrefS, "C"=>"".$elem_visPcOdOverrefC, "A"=>"".$elem_visPcOdOverrefA, "V"=>"".$elem_visPcOdOverrefV), 
												"OS"=>array("S"=>"".$elem_visPcOsOverrefS, "C"=>"".$elem_visPcOsOverrefC, "A"=>"".$elem_visPcOsOverrefA, "V"=>"".$elem_visPcOsOverrefV));
		$arrMainRet["PC"]["1"]["Prism"] = array("OD"=>array("P"=>"".$elem_visPcOdP, "Prism"=>"".$elem_visPcOdPrism, "Slash"=>"".$elem_visPcOdSlash, "Sel2"=>"".$elem_visPcOdSel2), 
										"OS"=>array("P"=>"".$elem_visPcOsP, "Prism"=>"".$elem_visPcOsPrism, "Slash"=>"".$elem_visPcOsSlash, "Sel2"=>"".$elem_visPcOsSel2), 
										"Desc"=>"".$elem_visPcPrismDesc_1 );
		
		$arrMainRet["PC"]["2"]["Initial"] = array("OD"=>array("S"=>"".$elem_visPcOdS2, "C"=>"".$elem_visPcOdC2, "A"=>"".$elem_visPcOdA2, "Add"=>"".$elem_visPcOdAdd2, "Sel1"=>"".$elem_visPcOdSel12), 
										"OS"=>array("S"=>"".$elem_visPcOsS2, "C"=>"".$elem_visPcOsC2, "A"=>"".$elem_visPcOsA2, "Add"=>"".$elem_visPcOsAdd2, "Sel1"=>"".$elem_visPcOsSel12), 
										"Desc"=>"".$elem_visPcDesc_2, 
										"Near"=>"".$elem_pcNear2);
		$arrMainRet["PC"]["2"]["Over Refraction"] = array("OD"=>array("S"=>"".$elem_visPcOdOverrefS2, "C"=>"".$elem_visPcOdOverrefC2, "A"=>"".$elem_visPcOdOverrefA2, "V"=>"".$elem_visPcOdOverrefV2),
												"OS"=>array("S"=>"".$elem_visPcOsOverrefS2, "C"=>"".$elem_visPcOsOverrefC2, "A"=>"".$elem_visPcOsOverrefA2, "V"=>"".$elem_visPcOsOverrefV2));
		$arrMainRet["PC"]["2"]["Prism"] = array("OD"=>array("P"=>"".$elem_visPcOdP2, "Prism"=>"".$elem_visPcOdPrism2, "Slash"=>"".$elem_visPcOdSlash2, "Sel2"=>"".$elem_visPcOdSel22), 
										"OS"=>array("P"=>"".$elem_visPcOsP2, "Prism"=>"".$elem_visPcOsPrism2, "Slash"=>"".$elem_visPcOsSlash2, "Sel2"=>"".$elem_visPcOsSel22), 
										"Desc"=>"".$elem_visPcPrismDesc_2 );		
		$arrMainRet["PC"]["3"]["Initial"] = array("OD"=>array("S"=>"".$elem_visPcOdS3, "C"=>"".$elem_visPcOdC3, "A"=>"".$elem_visPcOdA3, "Add"=>"".$elem_visPcOdAdd3, "Sel1"=>"".$elem_visPcOdSel13), 
										"OS"=>array("S"=>"".$elem_visPcOsS3, "C"=>"".$elem_visPcOsC3, "A"=>"".$elem_visPcOsA3, "Add"=>"".$elem_visPcOsAdd3, "Sel1"=>"".$elem_visPcOsSel13), 
										"Desc"=>"".$elem_visPcDesc_3, "Near"=>"".$elem_pcNear3);
		$arrMainRet["PC"]["3"]["Over Refraction"] = array("OD"=>array("S"=>"".$elem_visPcOdOverrefS3, "C"=>"".$elem_visPcOdOverrefC3, "A"=>"".$elem_visPcOdOverrefA3, "V"=>"".$elem_visPcOdOverrefV3), 
												"OS"=>array("S"=>"".$elem_visPcOsOverrefS3, "C"=>"".$elem_visPcOsOverrefC3, "A"=>"".$elem_visPcOsOverrefA3, "V"=>"".$elem_visPcOsOverrefV3));
		$arrMainRet["PC"]["3"]["Prism"] = array("OD"=>array("P"=>"".$elem_visPcOdP3, "Prism"=>"".$elem_visPcOdPrism3, "Slash"=>"".$elem_visPcOdSlash3, "Sel2"=>"".$elem_visPcOdSel23), 
										"OS"=>array("P"=>"".$elem_visPcOsP3, "Prism"=>"".$elem_visPcOsPrism3, "Slash"=>"".$elem_visPcOsSlash3, "Sel2"=>"".$elem_visPcOsSel23), 
										"Desc"=>"".$elem_visPcPrismDesc_3 );
		
		$arrMainRet["MR"]["1"]["Initial"] = array("OD"=>array("S"=>"".$elem_visMrOdS, "C"=>"".$elem_visMrOdC, "A"=>"".$elem_visMrOdA, "Txt1"=>"".$elem_visMrOdTxt1, "Add"=>"".$elem_visMrOdAdd, "Txt2"=>"".$elem_visMrOdTxt2), 
										
										"OS"=>array("S"=>"".$elem_visMrOsS, "C"=>"".$elem_visMrOsC, "A"=>"".$elem_visMrOsA, "Txt1"=>"".$elem_visMrOsTxt1, "Add"=>"".$elem_visMrOsAdd, "Txt2"=>"".$elem_visMrOsTxt2),
										
										"Desc"=>"".$elem_visMrDesc, "Given"=>"".$elem_mrNoneGiven1,"Given Date"=>"".$elem_mr_pres_dt_1, "Cyclopegic"=>"".$elem_mrCyclopegic1, "Provider"=>"".$elem_providerName);
		$arrMainRet["MR"]["1"]["GL/PH"] = array("OD"=>array("type_od_1"=>"".$elem_visMrOdSel2, "value_od_1"=>"".$elem_visMrOdSel2Vision), 
										"OS"=>array("type_os_1"=>"".$elem_visMrOsSel2,"value_os_1"=>"".$elem_visMrOsSel2Vision));
										
		$arrMainRet["MR"]["1"]["Prism"] = array("OD"=>array("P"=>"".$elem_visMrOdP, "Sel1"=>"".$elem_visMrOdSel1, "Slash"=>"".$elem_visMrOdSlash, "Prism"=>"".$elem_visMrOdPrism), 
										"OS"=>array("P"=>"".$elem_visMrOsP, "Sel1"=>"".$elem_visMrOsSel1, "Slash"=>"".$elem_visMrOsSlash, "Prism"=>"".$elem_visMrOsPrism), 
										"Desc"=>"".$elem_visMrPrismDesc_1 );
		
		$arrMainRet["MR"]["2"]["Initial"] = array("OD"=>array("S"=>"".$elem_visMrOtherOdS, "C"=>"".$elem_visMrOtherOdC, "A"=>"".$elem_visMrOtherOdA, "Txt1"=>"".$elem_visMrOtherOdTxt1, "Add"=>"".$elem_visMrOtherOdAdd, "Txt2"=>"".$elem_visMrOtherOdTxt2), 
										"OS"=>array("S"=>"".$elem_visMrOtherOsS, "C"=>"".$elem_visMrOtherOsC, "A"=>"".$elem_visMrOtherOsA, "Txt1"=>"".$elem_visMrOtherOsTxt1, "Add"=>"".$elem_visMrOtherOsAdd, "Txt2"=>"".$elem_visMrOtherOsTxt2),
											"Desc"=>"".$elem_visMrDescOther, "Given"=>"".$elem_mrNoneGiven2, "Given Date"=>"".$elem_mr_pres_dt_2, "Cyclopegic"=>"".$elem_mrCyclopegic2, "Provider"=>"".$elem_providerNameOther);
		$arrMainRet["MR"]["2"]["GL/PH"] = array("OD"=>array("type_od_1"=>"".$elem_visMrOtherOdSel2, "value_od_1"=>"".$elem_visMrOtherOdSel2Vision), 
										"OS"=>array("type_os_1"=>"".$elem_visMrOtherOsSel2, "value_os_2"=>"".$elem_visMrOtherOsSel2Vision));
		$arrMainRet["MR"]["2"]["Prism"] = array("OD"=>array("P"=>"".$elem_visMrOtherOdP, "Sel1"=>"".$elem_visMrOtherOdSel1, "Slash"=>"".$elem_visMrOtherOdSlash, "Prism"=>"".$elem_visMrOtherOdPrism), 
										"OS"=>array("P"=>"".$elem_visMrOtherOsP, "Sel1"=>"".$elem_visMrOtherOsSel1, "Slash"=>"".$elem_visMrOtherOsSlash, "Prism"=>"".$elem_visMrOtherOsPrism), 
										"Desc"=>"".$elem_visMrPrismDesc_2 );
		
		$arrMainRet["MR"]["3"]["Initial"] = array("OD"=>array("S"=>"".$elem_visMrOtherOdS_3, "C"=>"".$elem_visMrOtherOdC_3, "A"=>"".$elem_visMrOtherOdA_3, "Txt1"=>"".$elem_visMrOtherOdTxt1_3, "Add"=>"".$elem_visMrOtherOdAdd_3, "Txt2"=>"".$elem_visMrOtherOdTxt2_3), 
										"OS"=>array("S"=>"".$elem_visMrOtherOsS_3, "C"=>"".$elem_visMrOtherOsC_3, "A"=>"".$elem_visMrOtherOsA_3,"Txt1"=>"".$elem_visMrOtherOsTxt1_3,"Add"=>"".$elem_visMrOtherOsAdd_3,"Txt2"=>"".$elem_visMrOtherOsTxt2_3),
										"Desc"=>"".$elem_visMrDescOther_3, "Given"=>"".$elem_mrNoneGiven3, "Given Date"=>"".$elem_mr_pres_dt_3, "Cyclopegic"=>"".$elem_mrCyclopegic3, "Provider"=>"".$elem_providerNameOther_3);
										
$arrMainRet["MR"]["3"]["GL/PH"] = array(
			"OD"=>array("type_od_1"=>"".$elem_visMrOtherOdSel2_3, "value_od_1"=>"".$elem_visMrOtherOdSel2Vision_3), 
			"OS"=>array("type_os_1"=>"".$elem_visMrOtherOsSel2_3, "value_os_1"=>"".$elem_visMrOtherOsSel2Vision_3));
			
		$arrMainRet["MR"]["3"]["Prism"] = array("OD"=>array("P"=>"".$elem_visMrOtherOdP_3, "Sel1"=>"".$elem_visMrOtherOdSel1_3, "Slash"=>"".$elem_visMrOtherOdSlash_3, "Prism"=>"".$elem_visMrOtherOdPrism_3), 
										"OS"=>array("P"=>"".$elem_visMrOtherOsP_3, "Sel1"=>"".$elem_visMrOtherOsSel1_3, "Slash"=>"".$elem_visMrOtherOsSlash_3, "Prism"=>"".$elem_visMrOtherOsPrism_3),
										"Desc"=>"".$elem_visMrPrismDesc_3 );
		
		
		$arrMainRet["BAT"] = array("OD"=>array("NL"=>"".$elem_visBatNlOd, "L"=>"".$elem_visBatLowOd, "M"=>"".$elem_visBatMedOd, "H"=>"".$elem_visBatHighOd), 
							"OS"=>array("NL"=>"".$elem_visBatNlOs, "L"=>"".$elem_visBatLowOs, "M"=>"".$elem_visBatMedOs, "H"=>"".$elem_visBatHighOs), 
						"Desc"=>"".$elem_visBatDesc );
		
		$arrMainRet["CP Control"] = $this->getCPControl();								
		
		$arrMainRet["Stereopsis"] = $this->getStereopsis(); 
		
		$arrMainRet["W4Dot"] = $this->getW4Dot(); 
		
		
		$arrMainRet["Retinoscopy"] = array("OD"=>array("S"=>"".$elem_visExoOdS, "C"=>"".$elem_visExoOdC, "A"=>"".$elem_visExoOdA), 
									"OS"=>array("S"=>"".$elem_visExoOsS, "C"=>"".$elem_visExoOsC, "A"=>"".$elem_visExoOsA)
									 );
		
		$arrMainRet["Cycloplegic Retinoscopy"] = array("OD"=>array("S"=>"".$elem_visCycloOdS, "C"=>"".$elem_visCycloOdC, "A"=>"".$elem_visCycloOdA), 
											"OS"=>array("S"=>"".$elem_visCycloOsS, "C"=>"".$elem_visCycloOsC, "A"=>"".$elem_visCycloOsA)
											 );
		
		$arrMainRet["Exophthalmometer"] = array("OD"=>"".$elem_visRetOd,	"OS"=>"".$elem_visRetOs , "PD"=>"".$elem_visRetPD );	
		
		
		
		return $arrMainRet;	
	}
	public function getVision(){	
	
		$arrMainRet=array();
		$patient_id = $this->patient_id;
		$form_id = $this->form_id;
		
		$arrGet = $this->getAmslerGridCvf();
		$arrMainRet= array_merge($arrGet, $arrMainRet);		
		
		//Vision--
		//include_once($GLOBALS['incdir']."/chart_notes/common/ChartNote.php");		
		include_once($GLOBALS['srcdir']."/classes/work_view/Vision.php");			
		//include_once($GLOBALS['srcdir']."/chart_notes/common/functions.php");		
		//include_once($GLOBALS['srcdir']."/main/main_functions.php");		
		//include_once($GLOBALS['incdir']."/Billing/billing_admin/chart_more_functions.php");
		
		//Vision ------------------
		//Set Default Records
		$elem_providerId = $elem_providerIdOther = $elem_providerIdOther_3 = $elem_providerIdOther_4 = ""; //$_SESSION['authId'];
		//$elem_providerName = $elem_providerNameOther = $elem_providerNameOther_3 = $elem_providerNameOther_4 = showDoctorName($_SESSION['authId']);
		$elem_visNearOdSel1 = $elem_visNearOsSel1 = "CC";
		$elem_visNearOdSel2 = $elem_visNearOsSel2 = "SC";
		$elem_visDisOdTxt1 = $elem_visDisOdTxt2 = $elem_visDisOsTxt1 = $elem_visDisOsTxt2 = $elem_visNearOdTxt1= "20/";
		$elem_visNearOdTxt2 = $elem_visNearOsTxt1 = $elem_visNearOsTxt2 = $elem_visPcOdNearTxt2 = $elem_visPcOsNearTxt2 = "20/";
		$elem_visPcOdNearTxt3=$elem_visPcOsNearTxt3=$elem_visMrOdTxt1=$elem_visMrOdTxt2=$elem_visMrOsTxt1="20/";
		$elem_visMrOsTxt2=$elem_visMrOtherOdTxt1=	$elem_visMrOtherOdTxt2=$elem_visMrOtherOsTxt1=$elem_visMrOtherOsTxt2="20/";
		$elem_visPcOdOverrefV=$elem_visPcOsOverrefV=$elem_visPcOdOverrefV2=$elem_visPcOsOverrefV2=$elem_visPcOdOverrefV3="20/";
		$elem_visPcOsOverrefV3=$elem_visBatNlOd=$elem_visBatLowOd=$elem_visBatMedOd=$elem_visBatHighOd=$elem_visBatNlOs="20/";
		$elem_visBatLowOs=$elem_visBatMedOs=$elem_visBatHighOs="20/";
		$elem_mrNoneGiven1 = "None";
		$elem_visMrOtherOdTxt1_3 = $elem_visMrOtherOdTxt2_3 = $elem_visMrOtherOsTxt1_3 = $elem_visMrOtherOsTxt2_3 = "20/";
		//$elem_visMrOtherOdTxt1_4 = $elem_visMrOtherOdTxt2_4 = $elem_visMrOtherOsTxt1_4 = $elem_visMrOtherOsTxt2_4 = "20/";
		$elem_visMrOdSel2Vision = $elem_visMrOtherOdSel2Vision = $elem_visMrOtherOdSel2Vision_3 = "20/";
		$elem_visMrOsSel2Vision = $elem_visMrOtherOsSel2Vision = $elem_visMrOtherOsSel2Vision_3 = "20/";
		$todate = date('m-d-Y');
		//$elem_control = "plus";
		//$elem_control_os = "plus";
		$elem_visSnellan = $elem_visSnellan_near	= "Snellen";
		
		//Obj
		$oVis = new Vision($patient_id,$form_id);

		//GET $dos_ymd
		$dos_ymd = $oVis->getDos($unformatted=1);
		
		
		//Get Past Records
		//if(isset($_GET["visId"]) && !empty($_GET["visId"]))
		//{
			//Get Form id based on patient id
			$sql = "SELECT * FROM chart_vis_master WHERE patient_id = '".$patient_id."' AND form_id = '".$form_id."' ";
			$row = sqlQuery($sql);
			if(($row == false)){ /* Show Previous values in finalized chart also ~&& ($finalize_flag == 0)~ */
				// New
				$elem_editModeVis = "0";
				$vision_edid = "";
				//New Records
				//$row = valuesNewRecordsVision($patient_id);
				$res = $oVis->getLastRecord(" * ",0,$dos_ymd); /*$dos_ymd in getchartinfo.nic.php*/
				if($res!=false){$row=$res->fields;}else{$row=false;}
				$isNewRecord = true;
			}else{
				// Update
				$elem_editModeVis = "1";
				$elem_visId = $row["vis_id"];
				//Default
				if(isset($ar_req["defaultValsVis"]) && ($ar_req["defaultValsVis"] == 1)){
					//New Records
					//$row = valuesNewRecordsVision($patient_id);
					$res = $oVis->getLastRecord(" * ",1,$dos_ymd); /*$dos_ymd in getchartinfo.nic.php*/
					if($res!=false){$row=$res->fields;}else{$row=false;}
					$isNewRecord = true;
				}
			}
			
			//Last Finalized Vision Id
			$resLF = $oVis->getLastRecord(" * ",1,$dos_ymd); /*$dos_ymd in getchartinfo.nic.php*/
			if($resLF!=false){
				$elem_visMasIdLF = $rowLF["id"];
			}
			
			//
			if($row != false){
				
				$elem_visMasId = $row["id"];
				$ar_dis = $oVis->getDistance($elem_visMasId, $elem_editModeVis, $elem_visMasIdLF);
				extract($ar_dis);	
				
				
				$ar_pam = $oVis->getPam($elem_visMasId, $elem_editModeVis);
				extract($ar_pam);	
				
				$ar_sca = $oVis->getSCA($elem_visMasId, $elem_editModeVis);
				extract($ar_sca);	
				
				$ar_ak = $oVis->getAK($elem_visMasId, $elem_editModeVis);
				extract($ar_ak);	
				
				$ar_exo = $oVis->getEXO($elem_visMasId, $elem_editModeVis);			
				extract($ar_exo);	
				
				$ar_bat = $oVis->getBAT($elem_visMasId, $elem_editModeVis);
				extract($ar_bat);	
				
				//Multiple MR --
				list($ar_multiple_mr,$tmp_mr_len)=$oVis->get_mutli_mr_pc("MR",$dos_ymd,$elem_visMasId, $elem_visMasIdLF);	
				extract($ar_multiple_mr);		
				if(!empty($tmp_mr_len)){ $len_mr = $tmp_mr_len; $inclss_mr="in";}					
				//End Multiple MR --				
				
				//Multiple PC --
				list($ar_multiple_pc,$tmp_pc_len)=$oVis->get_mutli_mr_pc("PC",$dos_ymd,$elem_visMasId, $elem_visMasIdLF);	
				extract($ar_multiple_pc);		
				if(!empty($tmp_pc_len)){ $len_pc = $tmp_pc_len; $inclss_pc = "in"; }		
				//End Multiple PC --				
				
				$elem_statusElements = (!isset($isNewRecord) || ($isNewRecord != true)) ? $row["status_elements"] : "" ;
				
				//$elem_noChangeVision = (($elem_editModeVis == "1") && ($isNewRecord != true)) ? $row["examinedNoChange"] : "0" ;
				//Comments
				//$elem_visComments = stripslashes($row["vis_comments"]);
				//Comments			
				//UT Elems //
				$elem_utElems = (!isset($isNewRecord) || ($isNewRecord != true)) ? $row["ut_elem"] : "" ;
			}
		//}		
		
		// Correct Old Data
		$elem_examDateDistance = $elem_examDateARAK = $elem_examDatePC = $elem_examDateMR = $elem_examDateCR = $todate;

		// Separate NoChange Value
		$isClubbed = strpos($elem_visAkDesc,"(*|*)");
		if($isClubbed !== false){
			$elem_examinedNoChange = substr($elem_visAkDesc,$isClubbed+5);
			$elem_visAkDesc = "".trim(substr($elem_visAkDesc,0,$isClubbed));
		}
		// Separate NoChange Value
		$isClubbed = strpos($elem_disDesc,"(*|*)");

		if($isClubbed !== false){
			$elem_disDesc = trim(substr($elem_disDesc,0,$isClubbed));
		}

		if(isset($isNewRecord) && ($isNewRecord == true)){
			$elem_noChangeVision = "0";
		}

		// Separate Dates
		list($elem_visNearDesc,$elem_examDateDistance) = extractDate($elem_visNearDesc);
		list($elem_visArDesc,$elem_examDateARAK) = extractDate($elem_visArDesc);
		list($elem_visPcDesc,$elem_examDatePC) = extractDate($elem_visPcDesc);
		list($elem_visMrDesc,$elem_examDateMR) = extractDate($elem_visMrDesc);
		list($elem_visBatDesc,$elem_examDateCR) = extractDate($elem_visBatDesc);

		$elem_examDateDistance = (!empty($elem_examDateDistance)) ? formatDate4display($elem_examDateDistance) : $todate;
		$elem_examDateARAK = (!empty($elem_examDateARAK)) ? formatDate4display($elem_examDateARAK) : $todate;
		$elem_examDatePC = (!empty($elem_examDatePC)) ? formatDate4display($elem_examDatePC) : $todate;
		$elem_examDateMR = (!empty($elem_examDateMR)) ? formatDate4display($elem_examDateMR) : $todate;
		$elem_examDateCR = (!empty($elem_examDateCR)) ? formatDate4display($elem_examDateCR) : $todate;

		// Desc Default --------
		// Distance 
		$elem_disDescLF="";
		if(empty($elem_disDesc)) // || ($isNewRecord == true)
		{
			$elem_disDesc = ""; //"Description: ";
			if(!empty($elem_visDisOdSel1LF)){
				//$elem_disDesc .= "Distance\r\n";
				$elem_disDesc .= "OD: ".$elem_visDisOdSel1LF;
				$elem_disDesc .= (!empty($elem_visDisOdTxt1LF) && ($elem_visDisOdTxt1LF != "20/")) ? ", ".$elem_visDisOdTxt1LF."" : "" ;//", ".$elem_visDisOdSel2LF.", ".$elem_visDisOdTxt2LF." ";
				$elem_disDesc .= " ";
			}

			if(!empty($elem_visDisOsSel1LF)){
				$elem_disDesc .= "OS: ".$elem_visDisOsSel1LF;
				$elem_disDesc .= (!empty($elem_visDisOsTxt1LF) && ($elem_visDisOsTxt1LF != "20/")) ? ", ".$elem_visDisOsTxt1LF." " : "" ;//", ".$elem_visDisOsSel2LF.", ".$elem_visDisOsTxt2LF." ";
			}

			/*
			if($elem_visNear == "1")
			{
				$elem_disDesc .="\r\n";
				$elem_disDesc .= "OD: ".$elem_visNearOdSel1LF.", ".$elem_visNearOdTxt1LF." "; //", ".$elem_visNearOdSel2LF.", ".$elem_visNearOdTxt2LF." ";
				$elem_disDesc .= "OS: ".$elem_visNearOsSel1LF.", ".$elem_visNearOsTxt1LF." "; //", ".$elem_visNearOsSel2LF.", ".$elem_visNearOsTxt2LF." ";
			}*/
			$elem_disDescLF = $elem_disDesc;
		}
		//Pc+MR Desc default
		if(empty($elem_visPcDesc) || empty($elem_visPcDesc_2) || empty($elem_visPcDesc_3) ||
			empty($elem_visMrDesc) || empty($elem_visMrDescOther) || empty($elem_mr_desc_3) ){
			for($i=1;$i<=3;$i++){
				
				//PC -----
				$j1 = ($i>1) ? "_".$i : "";		
				$zDsc = "elem_visPcDesc".$j1;

				//if(empty($$zDsc) || ($isNewRecord == true)){ //($isNewRecord == true) ||
				if((empty($$zDsc) && (strpos($elem_statusElements, $zDsc."=1") === false))){
					$j2 = ($i>1) ? $i : "";
					
					$zDscLf = "elem_visPcDesc".$j1."LF";
					$$zDscLf = "";
				
					$$zDsc = "";
					$zsel1Lf = "elem_visPcOdS".$j2."LF";
					if(!empty($$zsel1Lf)){
						$$zDsc .= "OD: ".$$zsel1Lf.", ";
						$zsLf = "elem_visPcOdS".$j2."LF";
						$$zDsc .= (!empty($$zsLf)) ? $$zsLf.", " : "";
						$zcLf = "elem_visPcOdC".$j2."LF";
						$$zDsc .= (!empty($$zcLf)) ? $$zcLf.", " : "";
						$zaLf = "elem_visPcOdA".$j2."LF";
						$$zDsc .= (!empty($$zaLf)) ? $$zaLf.", " : "";
						$$zDsc = substr($$zDsc, 0, -2);
					}
					$zsel1Lf = "elem_visPcOsS".$j2."LF";
					if(!empty($$zsel1Lf)){
						$$zDsc .= " OS: ".$$zsel1Lf.", ";
						$zsLf = "elem_visPcOsS".$j2."LF";
						$$zDsc .= (!empty($$zsLf)) ? $$zsLf.", " : "";
						$zcLf = "elem_visPcOsC".$j2."LF";
						$$zDsc .= (!empty($$zcLf)) ? $$zcLf.", " : "";
						$zaLf = "elem_visPcOsA".$j2."LF";
						$$zDsc .= (!empty($$zaLf)) ? $$zaLf.", " : "";
						$$zDsc = substr($$zDsc, 0, -2);
					}
					$$zDscLf = $$zDsc;
				}
				
				//PC-----
				//MR -----
				
				$j2="";	
				if($i >= 2){
					$j2 = "Other";
				}

				$zDsc = ($i==3) ? "elem_mr_desc_3" : "elem_visMrDesc".$j2;	
				//$$zDscLf = "";

				//if(empty($$zDsc) || ($isNewRecord == true)){ //($isNewRecord == true) ||
				if((empty($$zDsc) && (strpos($elem_statusElements, $zDsc."=1") === false))){
					$$zDsc = "";
					$j3="";
					if($i == 3){
						$j3="_".$i;
					}
					$zDscLf = $zDsc."LF";
					
					
					//25june12 :: MR1 - If they type in the comment the only carry the comments forward do not show the previous MR values.
					// Notes : 26-10-2012 it will work now because isnewRecord check is removed
					/*
					if(!empty($$zDscLf)){
					
						$$zDsc = $$zDscLf;
					
					}else{
					*/				
						$tmpzDt="";
						$zsLf = "elem_visMr".$j2."OdS".$j3."LF";
						if(!empty($$zsLf)){
							$$zDsc .= formatDate4display($elem_examDateMRLF)." ";
							$$zDsc .= "OD: ".$$zsLf.", ";
							$zcLf = "elem_visMr".$j2."OdC".$j3."LF";
							$$zDsc .= (!empty($$zcLf)) ? $$zcLf.", " : "";
							$zaLf = "elem_visMr".$j2."OdA".$j3."LF";
							$$zDsc .= (!empty($$zaLf)) ? $$zaLf.", " : "";
							$zaddLf = "elem_visMr".$j2."OdAdd".$j3."LF";
							$$zDsc .= (!empty($$zaddLf)) ? $$zaddLf.", " : "";
							$$zDsc = substr($$zDsc, 0, -2);
						}
						$zsLf = "elem_visMr".$j2."OsS".$j3."LF";
						if(!empty($$zsLf)){
							if(empty($$zDsc)){ $$zDsc .= formatDate4display($elem_examDateMRLF)." ";  }
							$$zDsc .= " OS: ".$$zsLf.", ";
							$zcLf = "elem_visMr".$j2."OsC".$j3."LF";
							$$zDsc .= (!empty($$zcLf)) ? $$zcLf.", " : "";
							$zaLf = "elem_visMr".$j2."OsA".$j3."LF";
							$$zDsc .= (!empty($$zaLf)) ? $$zaLf.", " : "";
							$zaddLf = "elem_visMr".$j2."OsAdd".$j3."LF";
							$$zDsc .= (!empty($$zaddLf)) ? $$zaddLf.", " : "";
							$$zDsc = substr($$zDsc, 0, -2);
						}
						
						$$zDscLf = $$zDsc;
					//}
					
				}		
				
				//MR------		
				
			}
		}
		//
		// Desc Default --------
		
		//?reqModule=chart_notes&service=getVision&phyId=209&patId=60316&form_id=40179
		
		$arrMainRet["Distance"]=array("OD"=>array("".$elem_visDisOdSel1, "".$elem_visDisOdTxt1, "".$elem_visDisOdSel2, "".$elem_visDisOdTxt2), 
								"OS"=>array("".$elem_visDisOsSel1, "".$elem_visDisOsTxt1, "".$elem_visDisOsSel2, "".$elem_visDisOsTxt2), 
								"DropDown"=>"".$elem_visSnellan, "Desc"=>"".$elem_disDesc);
								
		$arrMainRet["AdditionalAcuity"]=array("OD"=>array("".$elem_visDisOdSel3, "".$elem_visDisOdTxt3),
										"OS"=>array("".$elem_visDisOsSel3, "".$elem_visDisOsTxt3), "Desc"=>"".$elem_visDisAct3);
										
		$arrMainRet["Near"]=array("OD"=>array("".$elem_visNearOdSel1, "".$elem_visNearOdTxt1, "".$elem_visNearOdSel2, "".$elem_visNearOdTxt2), 
							"OS"=>array("".$elem_visNearOsSel1, "".$elem_visNearOsTxt1, "".$elem_visNearOsSel2, "".$elem_visNearOsTxt2),
							"DropDown"=>"".$elem_visSnellan_near, "Desc"=>"".$elem_visNearDesc);		
		$arrMainRet["AR"]=array("OD"=>array("S"=>"".$elem_visArOdS, "C"=>"".$elem_visArOdC, "A"=>"".$elem_visArOdA, "Sel1"=>"".$elem_visArOdSel1), 
							"OS"=>array("S"=>"".$elem_visArOsS, "C"=>"".$elem_visArOsC, "A"=>"".$elem_visArOsA, "Sel1"=>"".$elem_visArOsSel1),
							"DropDown"=>"".$elem_visArRefPlace, "Desc"=>"".$elem_visArDesc);			
		$arrMainRet["Cycloplegic AR"]=array("OD"=>array("S"=>"".$elem_visCycArOdS, "C"=>"".$elem_visCycArOdC, "A"=>"".$elem_visCycArOdA, "Sel1"=>"".$elem_visCycArOdSel1), 
									"OS"=>array("S"=>"".$elem_visCycArOsS, "C"=>"".$elem_visCycArOsC, "A"=>"".$elem_visCycArOsA, "Sel1"=>"".$elem_visCycArOsSel1),
									"Desc"=>"".$elem_visCycArDesc);									
		$arrMainRet["K"]=array("OD"=>array("K"=>"".$elem_visAkOdK, "Slash"=>"".$elem_visAkOdSlash, "X"=>"".$elem_visAkOdX), 
							"OS"=>array("K"=>"".$elem_visAkOsK, "Slash"=>"".$elem_visAkOsSlash, "X"=>"".$elem_visAkOsX), 
							"Desc"=>"".$elem_visAkDesc, "KType"=>"".$elem_kType);
		
		$arrMainRet["PC"]["1"]["Initial"] = array("OD"=>array("S"=>"".$elem_visPcOdS, "C"=>"".$elem_visPcOdC, "A"=>"".$elem_visPcOdA, "Add"=>"".$elem_visPcOdAdd, "Sel1"=>"".$elem_visPcOdSel1), 
										"OS"=>array("S"=>"".$elem_visPcOsS, "C"=>"".$elem_visPcOsC, "A"=>"".$elem_visPcOsA, "Add"=>"".$elem_visPcOsAdd, "Sel1"=>"".$elem_visPcOsSel1),
										"Desc"=>"".$elem_visPcDesc, "Near"=>"".$elem_pcNear1);
										
		$arrMainRet["PC"]["1"]["Over Refraction"] = array("OD"=>array("S"=>"".$elem_visPcOdOverrefS, "C"=>"".$elem_visPcOdOverrefC, "A"=>"".$elem_visPcOdOverrefA, "V"=>"".$elem_visPcOdOverrefV), 
												"OS"=>array("S"=>"".$elem_visPcOsOverrefS, "C"=>"".$elem_visPcOsOverrefC, "A"=>"".$elem_visPcOsOverrefA, "V"=>"".$elem_visPcOsOverrefV));
		$arrMainRet["PC"]["1"]["Prism"] = array("OD"=>array("P"=>"".$elem_visPcOdP, "Prism"=>"".$elem_visPcOdPrism, "Slash"=>"".$elem_visPcOdSlash, "Sel2"=>"".$elem_visPcOdSel2), 
										"OS"=>array("P"=>"".$elem_visPcOsP, "Prism"=>"".$elem_visPcOsPrism, "Slash"=>"".$elem_visPcOsSlash, "Sel2"=>"".$elem_visPcOsSel2), 
										"Desc"=>"".$elem_visPcPrismDesc_1 );
		
		$arrMainRet["PC"]["2"]["Initial"] = array("OD"=>array("S"=>"".$elem_visPcOdS2, "C"=>"".$elem_visPcOdC2, "A"=>"".$elem_visPcOdA2, "Add"=>"".$elem_visPcOdAdd2, "Sel1"=>"".$elem_visPcOdSel12), 
										"OS"=>array("S"=>"".$elem_visPcOsS2, "C"=>"".$elem_visPcOsC2, "A"=>"".$elem_visPcOsA2, "Add"=>"".$elem_visPcOsAdd2, "Sel1"=>"".$elem_visPcOsSel12), 
										"Desc"=>"".$elem_visPcDesc_2, 
										"Near"=>"".$elem_pcNear2);
		$arrMainRet["PC"]["2"]["Over Refraction"] = array("OD"=>array("S"=>"".$elem_visPcOdOverrefS2, "C"=>"".$elem_visPcOdOverrefC2, "A"=>"".$elem_visPcOdOverrefA2, "V"=>"".$elem_visPcOdOverrefV2),
												"OS"=>array("S"=>"".$elem_visPcOsOverrefS2, "C"=>"".$elem_visPcOsOverrefC2, "A"=>"".$elem_visPcOsOverrefA2, "V"=>"".$elem_visPcOsOverrefV2));
		$arrMainRet["PC"]["2"]["Prism"] = array("OD"=>array("P"=>"".$elem_visPcOdP2, "Prism"=>"".$elem_visPcOdPrism2, "Slash"=>"".$elem_visPcOdSlash2, "Sel2"=>"".$elem_visPcOdSel22), 
										"OS"=>array("P"=>"".$elem_visPcOsP2, "Prism"=>"".$elem_visPcOsPrism2, "Slash"=>"".$elem_visPcOsSlash2, "Sel2"=>"".$elem_visPcOsSel22), 
										"Desc"=>"".$elem_visPcPrismDesc_2 );		
		$arrMainRet["PC"]["3"]["Initial"] = array("OD"=>array("S"=>"".$elem_visPcOdS3, "C"=>"".$elem_visPcOdC3, "A"=>"".$elem_visPcOdA3, "Add"=>"".$elem_visPcOdAdd3, "Sel1"=>"".$elem_visPcOdSel13), 
										"OS"=>array("S"=>"".$elem_visPcOsS3, "C"=>"".$elem_visPcOsC3, "A"=>"".$elem_visPcOsA3, "Add"=>"".$elem_visPcOsAdd3, "Sel1"=>"".$elem_visPcOsSel13), 
										"Desc"=>"".$elem_visPcDesc_3, "Near"=>"".$elem_pcNear3);
		$arrMainRet["PC"]["3"]["Over Refraction"] = array("OD"=>array("S"=>"".$elem_visPcOdOverrefS3, "C"=>"".$elem_visPcOdOverrefC3, "A"=>"".$elem_visPcOdOverrefA3, "V"=>"".$elem_visPcOdOverrefV3), 
												"OS"=>array("S"=>"".$elem_visPcOsOverrefS3, "C"=>"".$elem_visPcOsOverrefC3, "A"=>"".$elem_visPcOsOverrefA3, "V"=>"".$elem_visPcOsOverrefV3));
		$arrMainRet["PC"]["3"]["Prism"] = array("OD"=>array("P"=>"".$elem_visPcOdP3, "Prism"=>"".$elem_visPcOdPrism3, "Slash"=>"".$elem_visPcOdSlash3, "Sel2"=>"".$elem_visPcOdSel23), 
										"OS"=>array("P"=>"".$elem_visPcOsP3, "Prism"=>"".$elem_visPcOsPrism3, "Slash"=>"".$elem_visPcOsSlash3, "Sel2"=>"".$elem_visPcOsSel23), 
										"Desc"=>"".$elem_visPcPrismDesc_3 );
		
		$arrMainRet["MR"]["1"]["Initial"] = array("OD"=>array("S"=>"".$elem_visMrOdS, "C"=>"".$elem_visMrOdC, "A"=>"".$elem_visMrOdA, "Txt1"=>"".$elem_visMrOdTxt1, "Add"=>"".$elem_visMrOdAdd, "Txt2"=>"".$elem_visMrOdTxt2), 
										"OS"=>array("S"=>"".$elem_visMrOsS, "C"=>"".$elem_visMrOsC, "A"=>"".$elem_visMrOsA, "Txt1"=>"".$elem_visMrOsTxt1, "Add"=>"".$elem_visMrOsAdd, "Txt2"=>"".$elem_visMrOsTxt2),
										"Desc"=>"".$elem_visMrDesc, "Given"=>"".$elem_mrNoneGiven1,"Given Date"=>"".$elem_mr_pres_dt_1, "Cyclopegic"=>"".$elem_mrCyclopegic1, "Provider"=>"".$elem_providerName);
		$arrMainRet["MR"]["1"]["GL/PH"] = array("OD"=>array("0"=>"".$elem_visMrOdSel2, "1"=>"".$elem_visMrOdSel2Vision), 
										"OS"=>array("0"=>"".$elem_visMrOsSel2,"1"=>"".$elem_visMrOsSel2Vision));
		$arrMainRet["MR"]["1"]["Prism"] = array("OD"=>array("P"=>"".$elem_visMrOdP, "Sel1"=>"".$elem_visMrOdSel1, "Slash"=>"".$elem_visMrOdSlash, "Prism"=>"".$elem_visMrOdPrism), 
										"OS"=>array("P"=>"".$elem_visMrOsP, "Sel1"=>"".$elem_visMrOsSel1, "Slash"=>"".$elem_visMrOsSlash, "Prism"=>"".$elem_visMrOsPrism), 
										"Desc"=>"".$elem_visMrPrismDesc_1 );
		
		$arrMainRet["MR"]["2"]["Initial"] = array("OD"=>array("S"=>"".$elem_visMrOtherOdS, "C"=>"".$elem_visMrOtherOdC, "A"=>"".$elem_visMrOtherOdA, "Txt1"=>"".$elem_visMrOtherOdTxt1, "Add"=>"".$elem_visMrOtherOdAdd, "Txt2"=>"".$elem_visMrOtherOdTxt2), 
										"OS"=>array("S"=>"".$elem_visMrOtherOsS, "C"=>"".$elem_visMrOtherOsC, "A"=>"".$elem_visMrOtherOsA, "Txt1"=>"".$elem_visMrOtherOsTxt1, "Add"=>"".$elem_visMrOtherOsAdd, "Txt2"=>"".$elem_visMrOtherOsTxt2),
											"Desc"=>"".$elem_visMrDescOther, "Given"=>"".$elem_mrNoneGiven2, "Given Date"=>"".$elem_mr_pres_dt_2, "Cyclopegic"=>"".$elem_mrCyclopegic2, "Provider"=>"".$elem_providerNameOther);
		$arrMainRet["MR"]["2"]["GL/PH"] = array("OD"=>array("0"=>"".$elem_visMrOtherOdSel2, "1"=>"".$elem_visMrOtherOdSel2Vision), 
										"OS"=>array("0"=>"".$elem_visMrOtherOsSel2, "1"=>"".$elem_visMrOtherOsSel2Vision));
		$arrMainRet["MR"]["2"]["Prism"] = array("OD"=>array("P"=>"".$elem_visMrOtherOdP, "Sel1"=>"".$elem_visMrOtherOdSel1, "Slash"=>"".$elem_visMrOtherOdSlash, "Prism"=>"".$elem_visMrOtherOdPrism), 
										"OS"=>array("P"=>"".$elem_visMrOtherOsP, "Sel1"=>"".$elem_visMrOtherOsSel1, "Slash"=>"".$elem_visMrOtherOsSlash, "Prism"=>"".$elem_visMrOtherOsPrism), 
										"Desc"=>"".$elem_visMrPrismDesc_2 );
		
		$arrMainRet["MR"]["3"]["Initial"] = array("OD"=>array("S"=>"".$elem_visMrOtherOdS_3, "C"=>"".$elem_visMrOtherOdC_3, "A"=>"".$elem_visMrOtherOdA_3, "Txt1"=>"".$elem_visMrOtherOdTxt1_3, "Add"=>"".$elem_visMrOtherOdAdd_3, "Txt2"=>"".$elem_visMrOtherOdTxt2_3), 
										"OS"=>array("S"=>"".$elem_visMrOtherOsS_3, "C"=>"".$elem_visMrOtherOsC_3, "A"=>"".$elem_visMrOtherOsA_3,"Txt1"=>"".$elem_visMrOtherOsTxt1_3,"Add"=>"".$elem_visMrOtherOsAdd_3,"Txt2"=>"".$elem_visMrOtherOsTxt2_3),
										"Desc"=>"".$elem_visMrDescOther_3, "Given"=>"".$elem_mrNoneGiven3, "Given Date"=>"".$elem_mr_pres_dt_3, "Cyclopegic"=>"".$elem_mrCyclopegic3, "Provider"=>"".$elem_providerNameOther_3);
		$arrMainRet["MR"]["3"]["GL/PH"] = array("OD"=>array("0"=>"".$elem_visMrOtherOdSel2_3, "1"=>"".$elem_visMrOtherOdSel2Vision_3), 
										"OS"=>array("0"=>"".$elem_visMrOtherOsSel2_3, "1"=>"".$elem_visMrOtherOsSel2Vision_3));
		$arrMainRet["MR"]["3"]["Prism"] = array("OD"=>array("P"=>"".$elem_visMrOtherOdP_3, "Sel1"=>"".$elem_visMrOtherOdSel1_3, "Slash"=>"".$elem_visMrOtherOdSlash_3, "Prism"=>"".$elem_visMrOtherOdPrism_3), 
										"OS"=>array("P"=>"".$elem_visMrOtherOsP_3, "Sel1"=>"".$elem_visMrOtherOsSel1_3, "Slash"=>"".$elem_visMrOtherOsSlash_3, "Prism"=>"".$elem_visMrOtherOsPrism_3),
										"Desc"=>"".$elem_visMrPrismDesc_3 );
		
		
		$arrMainRet["BAT"] = array("OD"=>array("NL"=>"".$elem_visBatNlOd, "L"=>"".$elem_visBatLowOd, "M"=>"".$elem_visBatMedOd, "H"=>"".$elem_visBatHighOd), 
							"OS"=>array("NL"=>"".$elem_visBatNlOs, "L"=>"".$elem_visBatLowOs, "M"=>"".$elem_visBatMedOs, "H"=>"".$elem_visBatHighOs), 
						"Desc"=>"".$elem_visBatDesc );
		
		$arrMainRet["CP Control"] = $this->getCPControl();								
		
		$arrMainRet["Stereopsis"] = $this->getStereopsis(); 
		
		$arrMainRet["W4Dot"] = $this->getW4Dot(); 
		
		
		$arrMainRet["Retinoscopy"] = array("OD"=>array("S"=>"".$elem_visExoOdS, "C"=>"".$elem_visExoOdC, "A"=>"".$elem_visExoOdA), 
									"OS"=>array("S"=>"".$elem_visExoOsS, "C"=>"".$elem_visExoOsC, "A"=>"".$elem_visExoOsA)
									 );
		
		$arrMainRet["Cycloplegic Retinoscopy"] = array("OD"=>array("S"=>"".$elem_visCycloOdS, "C"=>"".$elem_visCycloOdC, "A"=>"".$elem_visCycloOdA), 
											"OS"=>array("S"=>"".$elem_visCycloOsS, "C"=>"".$elem_visCycloOsC, "A"=>"".$elem_visCycloOsA)
											 );
		
		$arrMainRet["Exophthalmometer"] = array("OD"=>"".$elem_visRetOd,	"OS"=>"".$elem_visRetOs , "PD"=>"".$elem_visRetPD );	
		
		return $arrMainRet;	
	}
	
	
}

?>






