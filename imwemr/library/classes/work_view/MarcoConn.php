<?php
/*
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
*/
?>
<?php
/*
File: MarcoConn.php
Coded in PHP7
Purpose: This class provides Functionality of Marco xml processing.
Access Type : Include file
*/
?>
<?php
//include_once("../../globals.php");
//Marco Connector
/*
give path of file

parse to array

classify into section

save in db
*/

//VALUES CAN BE 'MR' OR 'OVER-REF' : uncomment one line from below two lines
//define("MARCO_SUBJECTIVE_DATA","MR");
if(!defined('MARCO_SUBJECTIVE_DATA')){define("MARCO_SUBJECTIVE_DATA","OVER-REF");}

//*
class MarcoConn{
	private $ptId, $fid, $exam_dt, $exam_tm, $ns, $finalize_flag, $flg_pc_only;
	
	public function __construct($pid,$fid,$finalize_flag="0",$pc_only="0"){
		$this->ptId=$pid; 
		$this->fid=$fid;
		$this->finalize_flag=$finalize_flag;
		$this->flg_pc_only=$pc_only;
	}
	
	function formatDate($strDt){		
		if(!empty($strDt)){ 
			$ar = explode(".", $strDt);
			$dt = $ar[0]."-".$ar[1]."-".$ar[2]; 
			$tm = $ar[3];
			return array($dt, $tm);	
		}else{
			return "";
		}		
	}
	
	//--
	function getVisionStatusFlag($ar, $strVal){
		
		if(count($ar)>0){
			foreach($ar as $key=>$val){
				//remove 0
				$tmp=$val."=0";
				if(strpos($strVal, $tmp)!==false){
					$strVal = str_replace(array($tmp.",", $tmp),"",$strVal);
				}
				
				//add 1
				$tmp=$val."=1";
				if(strpos($strVal, $tmp)===false){
					$strVal .=$tmp.",";
				}
			}
		}

		//--
		
		return $strVal;
	}
	
	//0.5 to +0.50,  it is not rounding, adding Plus
	function num_frmt($str){
		$str = trim($str);
		if(!empty($str)){
			$str = floatval($str);
			$ar = explode(".", $str);
			if((isset($ar[1]) && !empty($ar[1]) && strlen($ar[1])<=1)||!isset($ar[1])){
				$str = number_format(floatval($str), 2, '.', '');
			}
			
			if(strpos($str, "+")===false && strpos($str, "-")===false){
				$str = "+".$str;
			}
		}
		return $str;
	}
	
	//Objective_Data  -- AR
	function saveObjectiveData($ds){
		
		//od
		$dc = (isset($this->ns['nsREF'])) ? $ds->children($this->ns['nsREF']) : '';
		if(is_object($dc) && isset($dc->Measure->REF->R->Median)){
			$sph_od = "".$dc->Measure->REF->R->Median->Sphere;
			$cyl_od = "".$dc->Measure->REF->R->Median->Cylinder;
			$axis_od = "".$dc->Measure->REF->R->Median->Axis;
			
		}else if(isset($ds->RM_Data_OD)){
			
			$ds_d =$ds->RM_Data_OD; 
			$sph_od = "".$ds_d->Sphere_OD;
			$cyl_od = "".$ds_d->Cylinder_OD;
			$axis_od = "".$ds_d->Axis_OD;
			$vis_ar_od_sel_1="";
			
		}else if(isset($ds->R)){
			
			$ds_d =$ds->R->AR->ARMedian; 
			$sph_od = "".$ds_d->Sphere;
			$cyl_od = "".$ds_d->Cylinder;
			$axis_od = "".$ds_d->Axis;
			$vis_ar_od_sel_1="";
			
			//Note: XML file do not give ConfidenceIndices
			/*
			if(!empty($ds_d->ConfidenceIndices)){
				$tmp = explode(",",$ds_d->ConfidenceIndices);
				if(in_array("10",$tmp)||in_array("8",$tmp)||in_array("9",$tmp)){
				$vis_ar_od_sel_1="High";	
				}elseif(in_array("6",$tmp)||in_array("7",$tmp)){
				$vis_ar_od_sel_1="Med";	
				}else{
				$vis_ar_od_sel_1="Low";
				}
			}*/			
			
		}else{

			$ds_d =$ds->Objective_Data_OD; 
			$sph_od = "".$ds_d->Sphere_OD;
			$cyl_od = "".$ds_d->Cylinder_OD;
			$axis_od = "".$ds_d->Axis_OD;		
			$vis_ar_od_sel_1="";
			$confidenceIndices_od = "".$ds_d->ConfidenceIndices;
			if(!empty($confidenceIndices_od)){
				$tmp = explode(",",$confidenceIndices_od);
				if(in_array("10",$tmp)||in_array("8",$tmp)||in_array("9",$tmp)){
				$vis_ar_od_sel_1="High";	
				}elseif(in_array("6",$tmp)||in_array("7",$tmp)){
				$vis_ar_od_sel_1="Med";	
				}else{
				$vis_ar_od_sel_1="Low";
				}
			}
		}
		
		//		
		
		//os
		if(is_object($dc) && isset($dc->Measure->REF->L->Median)){
			$sph_os = "".$dc->Measure->REF->L->Median->Sphere;
			$cyl_os = "".$dc->Measure->REF->L->Median->Cylinder;
			$axis_os = "".$dc->Measure->REF->L->Median->Axis;
			
		}else	if(isset($ds->RM_Data_OS)){
			$ds_d =$ds->RM_Data_OS; 
			$sph_os = "".$ds_d->Sphere_OS;
			$cyl_os = "".$ds_d->Cylinder_OS;
			$axis_os = "".$ds_d->Axis_OS;
			$vis_ar_os_sel_1="";
			
		}else	if(isset($ds->L->AR->ARMedian)){
			$ds_d =$ds->L->AR->ARMedian; 
			$sph_os = "".$ds_d->Sphere;
			$cyl_os = "".$ds_d->Cylinder;
			$axis_os = "".$ds_d->Axis;
			$vis_ar_os_sel_1="";
			
			//Note: XML file do not give ConfidenceIndices
			/*
			if(!empty($ds_d->ConfidenceIndices)){
				$tmp = explode(",",$ds_d->ConfidenceIndices);
				if(in_array("10",$tmp)||in_array("8",$tmp)||in_array("9",$tmp)){
				$vis_ar_os_sel_1="High";	
				}elseif(in_array("6",$tmp)||in_array("7",$tmp)){
				$vis_ar_os_sel_1="Med";	
				}else{
				$vis_ar_os_sel_1="Low";
				}
			}*/
			
			
		}else{
			$ds_s =$ds->Objective_Data_OS;
			$sph_os = "".$ds_s->Sphere_OS;
			$cyl_os = "".$ds_s->Cylinder_OS;
			$axis_os = "".$ds_s->Axis_OS;	
			$vis_ar_os_sel_1="";
			$confidenceIndices_os = "".$ds_s->ConfidenceIndices;
			if(!empty($confidenceIndices_os)){
				$tmp = explode(",",$confidenceIndices_os);
				if(in_array("10",$tmp)||in_array("8",$tmp)||in_array("9",$tmp)){
				$vis_ar_os_sel_1="High";	
				}elseif(in_array("6",$tmp)||in_array("7",$tmp)){
				$vis_ar_os_sel_1="Med";	
				}else{
				$vis_ar_os_sel_1="Low";
				}
			}
		}
		
		//remove spaces
		$sph_od = str_replace(" ", "", $sph_od);
		$cyl_od = str_replace(" ", "", $cyl_od);
		$sph_os = str_replace(" ", "", $sph_os);
		$cyl_os = str_replace(" ", "", $cyl_os);
		
		//remove leading zero
		if(!empty($sph_od)){ $sph_od = floatval($sph_od); }
		if(!empty($cyl_od)){ $cyl_od = floatval($cyl_od); }
		if(!empty($sph_os)){ $sph_os = floatval($sph_os); }
		if(!empty($cyl_os)){ $cyl_os = floatval($cyl_os); }
		
		if(!empty($sph_od) || !empty($cyl_od) || !empty($axis_od) ||
			!empty($sph_os) || !empty($cyl_os) || !empty($axis_os) ){
			
			if(!empty($this->ptId) && !empty($this->fid)){
				
				//
				if(!empty($sph_od)){ $sph_od = $this->num_frmt($sph_od); }
				if(!empty($cyl_od)){ $cyl_od = $this->num_frmt($cyl_od); }
				if(!empty($sph_os)){ $sph_os = $this->num_frmt($sph_os); }
				if(!empty($cyl_os)){ $cyl_os = $this->num_frmt($cyl_os); }
				
				//
				//include_once(dirname(__FILE__)."/Vision.php");
				
				//Carry Forward prev
				$oVision = new Vision($this->ptId, $this->fid);
				if(!$oVision->isRecordExists()){
					$oVision->carryForward();
				}			
				
				/*
				$sttsAR="elem_visArOdS=1,elem_visArOdC=1,elem_visArOdA=1,elem_visArOdSel1=1,".
						"elem_visArOsS=1,elem_visArOsC=1,elem_visArOsA=1,elem_visArOsSel1=1,";
				$ut_elemAR="".$_SESSION["authId"]."@elem_visArOdS,elem_visArOdC,elem_visArOdA,elem_visArOdSel1,".
							"elem_visArOsS,elem_visArOsC,elem_visArOsA,elem_visArOsSel1,|";
				*/			
				#arr Elements Info--
				$arrElemInfo = array("elem_visArOdS","elem_visArOdC","elem_visArOdA","elem_visArOdSel1",
								"elem_visArOsS","elem_visArOsC","elem_visArOsA","elem_visArOsSel1"); 
				
				$id_chart_vis_master=0;
				//
				$sql = "SELECT id, status_elements, ut_elem FROM chart_vis_master c1						
						WHERE c1.patient_id = '".$this->ptId."' AND c1.form_id = '".$this->fid."' ";
				$row = sqlQuery($sql);
				if($row!=false){
					$id_chart_vis_master=$row["id"];
					///*
					//
					$vis_statusElements_prev=$row["status_elements"];
					$vis_statusElements_prev=trim($vis_statusElements_prev.$sttsAR);
					
					//
					$ut_elem_prev = $row["ut_elem"];
					$ut_elem_prev = $oVision->getUTElemString($ut_elem_prev,implode(",",$arrElemInfo));
					//$ut_elem_prev = trim($ut_elem_prev.$ut_elemAR);
					//*/					
					
					$vis_statusElements_prev = $this->getVisionStatusFlag($arrElemInfo, $row["status_elements"]);
					
					//update
					$sql = "UPDATE chart_vis_master SET ".							
							"status_elements='".sqlEscStr($vis_statusElements_prev)."', ".
							"ut_elem='".sqlEscStr($ut_elem_prev)."' ".
							"WHERE patient_id = '".$this->ptId."' AND form_id = '".$this->fid."' ";
					sqlQuery($sql);
					
				}else{
					
					$ut_elemAR = $oVision->getUTElemString($ut_elems,implode(",",$arrElemInfo));					
					$sttsAR=implode("=1,",$arrElemInfo);
				
					//insert
					$sql = "INSERT INTO chart_vis_master (patient_id, form_id, status_elements, ut_elem ) ".
							"VALUES('".$this->ptId."','".$this->fid."','".sqlEscStr($sttsAR)."','".sqlEscStr($ut_elemAR)."' );";
					$id_chart_vis_master=sqlInsert($sql);		
				}
				
				if(!empty($id_chart_vis_master)){
					$sql = "SELECT * FROM chart_sca WHERE id_chart_vis_master = '".$id_chart_vis_master."' AND sec_name='AR' ";
					$row = sqlQuery($sql);
					if($row!=false){
						//update
						$sql = "UPDATE chart_sca SET ".
								"s_od='".sqlEscStr($sph_od)."', c_od='".sqlEscStr($cyl_od)."', a_od='".sqlEscStr($axis_od)."', sel_od='".sqlEscStr($vis_ar_od_sel_1)."', ".
								"s_os='".sqlEscStr($sph_os)."', c_os='".sqlEscStr($cyl_os)."', a_os='".sqlEscStr($axis_os)."', sel_os='".sqlEscStr($vis_ar_os_sel_1)."', ".
								"exam_date='".wv_dt('now')."', uid='".$_SESSION["authId"]."' ".
								"WHERE id_chart_vis_master = '".$id_chart_vis_master."' AND sec_name='AR' ";
						sqlQuery($sql);
					}else{	
						//insert
						$sql = "INSERT INTO chart_sca ( exam_date, uid, id_chart_vis_master, sec_name, 
											s_od, c_od, a_od, sel_od,
											s_os, c_os, a_os, sel_os ) ".
								"VALUES('".wv_dt('now')."','".$_SESSION["authId"]."','".$id_chart_vis_master."','AR',
									'".sqlEscStr($sph_od)."','".sqlEscStr($cyl_od)."','".sqlEscStr($axis_od)."','".sqlEscStr($vis_ar_od_sel_1)."',
									'".sqlEscStr($sph_os)."','".sqlEscStr($cyl_os)."','".sqlEscStr($axis_os)."','".sqlEscStr($vis_ar_os_sel_1)."'
								);";
						sqlQuery($sql);
					}
				}				
			}
		}
	}	
	
	//Objective_Data : Distance
	function saveObjectiveData_Dis($ds){}
	
	//Unaided visual acuity -   Vision without correction (sc)
	function saveUnaidedVisualAcuity_Dis($ds){
		if(isset($ds->UnaidedVisualAcuity_Data)){
			
			if($ds->UnaidedVisualAcuity_Data->DIST_OD){ $dis_od =trim("".$ds->UnaidedVisualAcuity_Data->DIST_OD); $ds_acuity="SC"; }
			if($ds->UnaidedVisualAcuity_Data->DIST_OS){ $dis_os =trim("".$ds->UnaidedVisualAcuity_Data->DIST_OS); $ds_acuity="SC"; }
			
			if($ds->UnaidedVisualAcuity_Data->NEAR_OD){ $nr_od =trim("".$ds->UnaidedVisualAcuity_Data->NEAR_OD); $nr_acuity="SC"; }
			if($ds->UnaidedVisualAcuity_Data->NEAR_OS){ $nr_os =trim("".$ds->UnaidedVisualAcuity_Data->NEAR_OS); $nr_acuity="SC"; }
			
		}

		if(!empty($dis_od) || !empty($dis_os) || !empty($nr_od) || !empty($nr_os)){
			
			if(!empty($this->ptId) && !empty($this->fid)){
				
				//
				//include_once(dirname(__FILE__)."/Vision.php");
				
				//Carry Forward prev
				$oVision = new Vision($this->ptId, $this->fid);
				if(!$oVision->isRecordExists()){
					$oVision->carryForward();
				}
				
				#arr Elements Info--
				$arrElemInfo = array(); 
				if(!empty($dis_od)){  $arrElemInfo[] = "elem_visDisOdSel1"; $arrElemInfo[] = "elem_visDisOdTxt1";}
				if(!empty($dis_os)){  $arrElemInfo[] = "elem_visDisOsSel1"; $arrElemInfo[] = "elem_visDisOsTxt1";}
				if(!empty($nr_od)){  $arrElemInfo[] = "elem_visNearOdSel1"; $arrElemInfo[] = "elem_visNearOdTxt1"; }
				if(!empty($nr_os)){  $arrElemInfo[] = "elem_visNearOsSel1"; $arrElemInfo[] = "elem_visNearOsTxt1"; }
				
				$id_chart_vis_master=0;
				//
				$sql = "SELECT id, status_elements, ut_elem FROM chart_vis_master
						WHERE patient_id = '".$this->ptId."' AND form_id = '".$this->fid."' ";
				$row = sqlQuery($sql);
				if($row!=false){
					$id_chart_vis_master=$row["id"];
					///*
					//
					$vis_statusElements_prev=$row["status_elements"];
					$vis_statusElements_prev=trim($vis_statusElements_prev);
					
					//
					$ut_elem_prev = $row["ut_elem"];
					$ut_elem_prev = $oVision->getUTElemString($ut_elem_prev,implode(",",$arrElemInfo));
					//$ut_elem_prev = trim($ut_elem_prev.$ut_elemAR);
					//*/					
					
					//update
					if(!empty($sql)){
					$sql = "UPDATE chart_vis_master SET  ";
					$sql .=	"status_elements='".sqlEscStr($vis_statusElements_prev)."', ".
							"ut_elem='".sqlEscStr($ut_elem_prev)."' ".
							"WHERE patient_id = '".$this->ptId."' AND form_id = '".$this->fid."' ";
					sqlQuery($sql);
					}
					
				}else{
					
					$ut_elemAR = $oVision->getUTElemString($ut_elems,implode(",",$arrElemInfo));					
					$sttsAR=implode("=1,",$arrElemInfo);
				
					//insert					
					if(!empty($sql)){
						$sql = "INSERT INTO chart_vis_master SET ";
						$sql .=	"status_elements='".sqlEscStr($vis_statusElements_prev)."', ".
								"ut_elem='".sqlEscStr($ut_elem_prev)."' ".
								"patient_id = '".$this->ptId."' , form_id = '".$this->fid."' ";
						$id_chart_vis_master=sqlInsert($sql);
					}
				}
				
				//
				if(!empty($id_chart_vis_master)){
					
					$arr_Vals_loops = array(
							"Distance" => array($ds_acuity,$dis_od,$ds_acuity, $dis_os ),
							"Near" => array($nr_acuity, $nr_od, $nr_acuity, $nr_os )
					);
					
					foreach($arr_Vals_loops as $sec_nm => $vals){					
						$sql = "SELECT * FROM chart_acuity WHERE id_chart_vis_master='".$id_chart_vis_master."' AND sec_name='".$sec_nm."' AND sec_indx='1'  ";
						$row = sqlQuery($sql);
						if($row != false){
							$sql = "UPDATE chart_acuity SET exam_date='".wv_dt('now')."', uid='".$_SESSION["authId"]."',
									sel_od = '".sqlEscStr($vals[0])."', txt_od = '".sqlEscStr($vals[1])."',
									sel_os = '".sqlEscStr($vals[2])."', txt_os = '".sqlEscStr($vals[3])."'
									WHERE id_chart_vis_master='".$id_chart_vis_master."' AND sec_name='".$sec_nm."' AND sec_indx='1'
								";	
							sqlQuery($sql);	
						}else{
							$sql = "INSERT INTO chart_acuity (id, id_chart_vis_master, exam_date, uid, sec_name, sec_indx, 
												sel_od, txt_od, sel_os, txt_os 
											) VALUES (NULL,'".$id_chart_vis_master."','".wv_dt('now')."','".$_SESSION["authId"]."','".$sec_nm."','1',
												'".sqlEscStr($vals[0])."', '".sqlEscStr($vals[1])."', '".sqlEscStr($vals[2])."', '".sqlEscStr($vals[3])."' 	
											) ";
							sqlQuery($sql);
						}					
					}
					
				}				
			}
		}	
	}
	
	//ContactLens_Data
	function getNewWorkSheetId($pid){
		$returnId=1;  
		$qry="SELECT currentWorksheetid from contactlensmaster where patient_id='".$pid."'  ORDER BY clws_id DESC limit 0,1";
		$res=imw_query($qry);
		if($res){
			
			$numRows=imw_num_rows($res);
			if($numRows>0){
			$resRow=imw_fetch_array($res);
				$returnId= $resRow["currentWorksheetid"]+1;
			}
		}
		return $returnId;
	}
	
	function saveCLData($ds,$clws_type=""){	
		
		if(empty($clws_type)){
			$clws_type = "Current Trial";
		}
		
		if($clws_type=="CL Check"){
		
			if(isset($ds->R->AR->ContactLens)){			
				
				$sphr_od="".$ds->R->AR->ContactLens->Sphere;
				$cyl_od="".$ds->R->AR->ContactLens->Cylinder;
				$axs_od="".$ds->R->AR->ContactLens->Axis;
				
				$sphr_os="".$ds->L->AR->ContactLens->Sphere;
				$cyl_os="".$ds->L->AR->ContactLens->Cylinder;
				$axs_os="".$ds->L->AR->ContactLens->Axis;
				
				$clws_trial_number="0";
			
			}else{		
		
				$sphr_od="".$ds->ContactLens_Data_OD->Sphere_OD;
				$cyl_od="".$ds->ContactLens_Data_OD->Cylinder_OD;
				$axs_od="".$ds->ContactLens_Data_OD->Axis_OD;
				//$se_od=$ds->ContactLens_Data_OD->SE;
				
				$sphr_os="".$ds->ContactLens_Data_OS->Sphere_OS;
				$cyl_os="".$ds->ContactLens_Data_OS->Cylinder_OS;
				$axs_os="".$ds->ContactLens_Data_OS->Axis_OS;
				//$se_os=	
				$clws_trial_number="0";	
			
			}
		
		}else if($clws_type=="Current Trial"){
		
				if(isset($ds->R->AR->TrialLens)){			
				$sphr_od="".$ds->R->AR->TrialLens->Sphere;
				$cyl_od="".$ds->R->AR->TrialLens->Cylinder;
				$axs_od="".$ds->R->AR->TrialLens->Axis;
				//$se_od=$ds->TrialLens_Data_OD->SE;
				
				$sphr_os="".$ds->L->AR->TrialLens->Sphere;
				$cyl_os="".$ds->L->AR->TrialLens->Cylinder;
				$axs_os="".$ds->L->AR->TrialLens->Axis;
				//$se_os=
			
			}else{
				$sphr_od="".$ds->TrialLens_Data_OD->Sphere_OD;
				$cyl_od="".$ds->TrialLens_Data_OD->Cylinder_OD;
				$axs_od="".$ds->TrialLens_Data_OD->Axis_OD;
				//$se_od=$ds->TrialLens_Data_OD->SE;
				
				$sphr_os="".$ds->TrialLens_Data_OS->Sphere_OS;
				$cyl_os="".$ds->TrialLens_Data_OS->Cylinder_OS;
				$axs_os="".$ds->TrialLens_Data_OS->Axis_OS;
				//$se_os=
			}
			
			$clws_trial_number="1";
		}
		
		//
		$sphr_od = trim($sphr_od);
		$cyl_od = trim($cyl_od);
		$axs_od = trim($axs_od);
		$sphr_os = trim($sphr_os);
		$cyl_os = trim($cyl_os);
		$axs_os = trim($axs_os);
		
		if(!empty($sphr_od) || !empty($cyl_od) || !empty($axs_od) || 
			!empty($sphr_od) || !empty($cyl_od) || !empty($axs_od) 
			){
			
			//data
			$patient_id=$this->ptId;
			$provider_id=$_SESSION["authId"];
			
			$clGrp="OU";			
			$currentWorksheetid=$this->getNewWorkSheetId($patient_id);
			$form_id=$this->fid;
			
			//data
			
			
			//Insert Into Contactlensmaster
			
			$sql = "
				INSERT INTO contactlensmaster (
				clws_id, patient_id, provider_id,
				dos, clws_savedatetime, clGrp, clws_type,
				clws_trial_number, currentWorksheetid, form_id
				)
				VALUES(NULL, '".$patient_id."', '".$provider_id."',
				CURDATE(), '".wv_dt('now')."', '".sqlEscStr($clGrp)."', '".sqlEscStr($clws_type)."',
				'".sqlEscStr($clws_trial_number)."', '".sqlEscStr($currentWorksheetid)."', '".$form_id."'
				);
			";
			$insertId = sqlInsert($sql);
			
			if(!empty($insertId)){ //clws_id
				
				$clType="scl";
				
				//od
				$sql = "
					INSERT INTO contactlensworksheet_det (
						id, clws_id, clEye, clType, 
						SclsphereOD,SclCylinderOD, SclaxisOD
						)
						VALUES(
						NULL, '".$insertId."', 'OD', '".sqlEscStr($clType)."', 
						'".sqlEscStr($sphr_od)."','".sqlEscStr($cyl_od)."', '".sqlEscStr($axs_od)."'
						)
				";
				$row = sqlQuery($sql);
				
				//os
				$sql = "
					INSERT INTO contactlensworksheet_det (
						id, clws_id, clEye, clType, 
						SclsphereOS,SclCylinderOS, SclaxisOS
						)
						VALUES(
						NULL, '".$insertId."', 'OS', '".sqlEscStr($clType)."', 
						'".sqlEscStr($sphr_os)."','".sqlEscStr($cyl_os)."', '".sqlEscStr($axs_os)."'
						)
				";
				$row = sqlQuery($sql);
			}
		}
	}
	
	//TrialLens_Data
	function saveTLData($ds){		
		$this->saveCLData($ds, "CL Check");
	}
	
	//PD_Data
	function savePDData($ds){}
	
	//CornealSize_Data
	
	function getCornSizeXml($corn,$cornxmlStr,$cornxml,$eye){	
		
		if(empty($cornxmlStr)){
			
			$xml = simplexml_load_file($cornxml);		
		}else{
			
			$xml = simplexml_load_string($cornxmlStr);			
		}
		
		if($eye=="od"){
			$xml->od->cornea_diameter->text=$corn;
		}else if($eye=="os"){
			$xml->os->cornea_diameter->text=$corn;
		}
		
		$ret= $xml->saveXML();		
		//echo "<pre>".$ret."</pre>";
		//exit();
		return $ret;
	}
	
	function saveCornSizeData($ds){
		//CornealSize_OD
		//CornealSize_OS	
		
		$corn_od = "".$ds->CornealSize_Data->CornealSize_OD;
		$corn_os = "".$ds->CornealSize_Data->CornealSize_OS;		
		
		$corn_od = trim($corn_od);
		$corn_os = trim($corn_os);
		
		//$corn_od="11";
		//$corn_os="12";		
		
		if(!empty($corn_od) || !empty($corn_os)){
			
			//include(dirname(__FILE__)."/SLE.php");
			$oSLE = new SLE($this->ptId,$this->fid);
			$oExamXml = new ExamXml();
			$arXmlFiles = $oExamXml->getExamXmlFiles("SLE");
			$owv = new WorkView();
			
			//SAVE DATA --
			$posCorn=$isPositive=1;
			$uid=$_SESSION["authId"];
			$statusElem="elem_chng_div2_Od=1,elem_chng_div2_Os=1,";
			//$ut_elem="|".$uid."@elem_corDiaOd,elem_corDiaOs,|";
			
			#arr Elements Info--
			$arrElemInfo = array("elem_corDiaOd","elem_corDiaOs");
			
			//SAVE DATA--
			
			//Carry Forward from previous visit
			if(!$oSLE->isRecordExists()){
				$oSLE->carryForward();
			}			
			
			//check again 
			$sql = "SELECT cornea_od,cornea_os,
					statusElem, 
					cornea_os_summary,cornea_od_summary,
					uid,wnlCornOs,wnlCornOd,ut_elem
					FROM chart_cornea 
					WHERE form_id='".$this->fid."' AND patient_id='".$this->ptId."' ";
			$row = sqlQuery($sql);
			if($row == false){ //if NOT exists
				
				$ut_elem = $oSLE->getUTElemString($ut_elems,implode(",",$arrElemInfo));
				
				//
				$statusElem="elem_chng_div2_Od=1,elem_chng_div2_Os=1";
				
				//
				$cornea_od=$this->getCornSizeXml($corn_od,"",$arXmlFiles["cornea"]["od"],"od");
				$cornea_os=$this->getCornSizeXml($corn_os,"",$arXmlFiles["cornea"]["os"],"os");
				
				//Summary - od				
				$arrTemp = $oSLE->getExamSummary($cornea_od);
				$cornea_od_summary = $arrTemp["Summary"];
				//$arrExmDone_os = $arrTemp["ExmDone"];
				
				//Summary - os
				$arrTemp = $oSLE->getExamSummary($cornea_os);
				$cornea_os_summary = $arrTemp["Summary"];
				//$arrExmDone_os = $arrTemp["ExmDone"];
				
				//INSERT
				$sql = "INSERT INTO chart_cornea 
									(id, 
									cornea_od, cornea_od_summary, 
									cornea_os, cornea_os_summary,
									form_id, patient_id, exam_date,
									posCorn, 
									uid, statusElem,
									ut_elem)
						VALUES ( NULL, 
								'".sqlEscStr($cornea_od)."', '".sqlEscStr($cornea_od_summary)."', 
								'".sqlEscStr($cornea_os)."', '".sqlEscStr($cornea_os_summary)."',
								'".$this->fid."', '".$this->ptId."', '".wv_dt('now')."',
								'".$posCorn."', 
								'".$uid."', '".sqlEscStr($statusElem)."',
								'".sqlEscStr($ut_elem)."' )";
					
				$insertId = sqlInsert($sql);
				
			}else{		
			
				//echo "IN UPDATE";
				
				//
				$ut_elem = $oSLE->getUTElemString($row["ut_elem"],implode(",",$arrElemInfo));
				
				//
				$statusElem_prev=$row["statusElem"];
				
				$statusElem_prev = str_replace(array("elem_chng_div2_Od=0,","elem_chng_div2_Os=0,","elem_chng_div2_Od=0","elem_chng_div2_Os=0"),"",$statusElem_prev);
				if(strpos($statusElem_prev, "elem_chng_div2_Od=1")===false){
					$statusElem_prev.="elem_chng_div2_Od=1,";
				}
				if(strpos($statusElem_prev, "elem_chng_div2_Os=1")===false){
					$statusElem_prev.="elem_chng_div2_Os=1,";
				}
				
				//
				$cornea_od=$this->getCornSizeXml($corn_od,$row["cornea_od"],$arXmlFiles["cornea"]["od"],"od");
				$cornea_os=$this->getCornSizeXml($corn_os,$row["cornea_os"],$arXmlFiles["cornea"]["os"],"os");
				
				//Summary - od				
				$arrTemp = $oSLE->getExamSummary($cornea_od);
				$cornea_od_summary = $arrTemp["Summary"];
				//$arrExmDone_os = $arrTemp["ExmDone"];
				
				//Summary - os
				$arrTemp = $oSLE->getExamSummary($cornea_os);
				$cornea_os_summary = $arrTemp["Summary"];
				//$arrExmDone_os = $arrTemp["ExmDone"];				
				
				//modify				
				//$modi_note_CornOd=$owv->getModiNotes($row["cornea_od_summary"],$row["wnlCornOd"],$cornea_od_summary,$wnlCornOd=0,$row["uid"],"Clear");
				//$modi_note_CornOs=$owv->getModiNotes($row["cornea_os_summary"],$row["wnlCornOs"],$cornea_os_summary,$wnlCornOs=0,$row["uid"],"Clear");
				
				//UPDATE
				$sql = "UPDATE chart_cornea 
						SET 
						cornea_od='".sqlEscStr($cornea_od)."', cornea_od_summary='".sqlEscStr($cornea_od_summary)."', 
						cornea_os='".sqlEscStr($cornea_os)."', cornea_os_summary='".sqlEscStr($cornea_os_summary)."',
						exam_date='".wv_dt('now')."',
						posCorn='".$posCorn."',
						wnlCorn='0', wnlCornOd='0', wnlCornOs='0',
						uid='".$uid."', statusElem='".sqlEscStr($statusElem_prev)."',
						ut_elem=CONCAT(ut_elem, '".sqlEscStr($ut_elem)."')
						WHERE form_id='".$this->fid."' AND patient_id='".$this->ptId."'
						";
				
				$row = sqlQuery($sql);
			}
		}
	}
	
	//PupilSize_Data
	function getPupilSizeXml($corn,$cornxmlStr,$cornxml,$eye){
		if(empty($cornxmlStr)){
			
			$xml = simplexml_load_file($cornxml);		
		}else{
			
			$xml = simplexml_load_string($cornxmlStr);			
		}
		
		if($eye=="od"){
			$xml->od->pupil_desc=$corn;
		}else if($eye=="os"){
			$xml->os->pupil_desc=$corn;
		}
		
		$ret= $xml->saveXML();		
		//echo "<pre>".$ret."</pre>";
		//exit();
		return $ret;
	}
	
	function savePupilSizeData($ds){
	
		$ppl_od  = "".$ds->PupilSize_Data->PupilSize_OD;
		$ppl_os  = "".$ds->PupilSize_Data->PupilSize_OS;
		
		$ppl_od = trim($ppl_od);
		$ppl_os = trim($ppl_os);
		
		if(!empty($ppl_od) || !empty($ppl_os) ){			
			$ppl_od=(!empty($ppl_od)) ? "Pupil Size = ".sqlEscStr($ppl_od) : "";
			$ppl_os=(!empty($ppl_os)) ? "Pupil Size = ".sqlEscStr($ppl_os) : "";
			
			
			//include(dirname(__FILE__)."/Pupil.php");
			$oPupil = new Pupil($this->ptId,$this->fid);
			$oExamXml = new ExamXml();
			$arXmlFiles = $oExamXml->getExamXmlFiles("Pupil");
			$owv = new WorkView();

			//SAVE DATA --
			$isPositive=1;
			$uid=$_SESSION["authId"];
			$statusElem="elem_chng_divPupil_Od=1,elem_chng_divPupil_Os=1,";
			//$ut_elem="|".$uid."@elem_pupilDescOd,elem_pupilDescOs,|";
			
			#arr Elements Info--
			$arrElemInfo = array("elem_pupilDescOd","elem_pupilDescOs");
			
			//SAVE DATA--
			
			//Carry Forward from previous visit
			if(!$oPupil->isRecordExists()){
				$oPupil->carryForward();
			}			
			
			//Check in Pupil
			$sql = "SELECT pupilOd, pupilOs, isPositive, 
					sumOdPupil, sumOsPupil, wnlPupilOd, wnlPupilOs, 
					uid, statusElem ,
					ut_elem, modi_note_od , modi_note_os, wnl_value 
					FROM chart_pupil WHERE formId='".$this->fid."' AND patientId='".$this->ptId."' ";
			$row=sqlQuery($sql);
			if($row==false ){				
				
				//
				$ut_elem = $oPupil->getUTElemString($ut_elems,implode(",",$arrElemInfo));
				
				//
				$pupilOd=$this->getPupilSizeXml($ppl_od,"",$arXmlFiles["od"],"od");
				$pupilOs=$this->getPupilSizeXml($ppl_os,"",$arXmlFiles["os"],"os");				
				
				//Summary - od				
				$arrTemp = $oPupil->getExamSummary($pupilOd);
				$sumOdPupil = $arrTemp["Summary"];
				//$arrExmDone_os = $arrTemp["ExmDone"];
				
				//Summary - os
				$arrTemp = $oPupil->getExamSummary($pupilOs);
				$sumOsPupil = $arrTemp["Summary"];
				//$arrExmDone_os = $arrTemp["ExmDone"];
				
				//wnl_value
				$wnl_value = $oPupil->getExamWnlStr("Pupil");
				
				//INSERT
				$sql = "INSERT INTO chart_pupil 
									(pupil_id, 
									pupilOd, sumOdPupil, 
									pupilOs, sumOsPupil,
									formId, patientId, examDate,
									isPositive, 
									uid, statusElem,
									ut_elem, wnl_value)
						VALUES ( NULL, 
								'".sqlEscStr($pupilOd)."', '".sqlEscStr($sumOdPupil)."', 
								'".sqlEscStr($pupilOs)."', '".sqlEscStr($sumOsPupil)."',
								'".$this->fid."', '".$this->ptId."', '".wv_dt('now')."',
								'".$isPositive."',  
								'".$uid."', '".sqlEscStr($statusElem)."',
								'".sqlEscStr($ut_elem)."', '".$wnl_value."' )";
				
				$insertId = sqlInsert($sql);
			
			}else{
			
				//update
				$ut_elem = $oPupil->getUTElemString($row["ut_elem"],implode(",",$arrElemInfo));	

				//
				$statusElem_prev=$row["statusElem"];
				$statusElem_prev = str_replace(array("elem_chng_divPupil_Od=0,","elem_chng_divPupil_Os=0,","elem_chng_divPupil_Od=1,","elem_chng_divPupil_Os=1,"),"",$statusElem_prev);
				$statusElem_prev.=$statusElem;
				
				//
				$pupilOd=$this->getPupilSizeXml($ppl_od,$row["pupilOd"],$arXmlFiles["od"],"od");
				$pupilOs=$this->getPupilSizeXml($ppl_os,$row["pupilOs"],$arXmlFiles["os"],"os");
				
				//Summary - od				
				$arrTemp = $oPupil->getExamSummary($pupilOd);
				$sumOdPupil = $arrTemp["Summary"];
				//$arrExmDone_os = $arrTemp["ExmDone"];
				
				//Summary - os
				$arrTemp = $oPupil->getExamSummary($pupilOs);
				$sumOsPupil = $arrTemp["Summary"];
				//$arrExmDone_os = $arrTemp["ExmDone"];				
				
				//modify				
				$wnl_string = !empty($row["wnl_value"]) ? $row["wnl_value"] : $oPupil->getExamWnlStr("Pupil") ; //"PERRLA, -ve APD";
				$modi_note_od =$owv->getModiNotes($row["sumOdPupil"],$row["wnlPupilOd"],$sumOdPupil,$wnlPupilOd=0,$row["uid"],$wnl_string);
				$modi_note_os =$owv->getModiNotes($row["sumOsPupil"],$row["wnlPupilOs"],$sumOsPupil,$wnlPupilOs=0,$row["uid"],$wnl_string);
				
				//UPDATE
				$sql = "UPDATE chart_pupil  
						SET 
						pupilOd='".sqlEscStr($pupilOd)."', sumOdPupil='".sqlEscStr($sumOdPupil)."', 
						pupilOs='".sqlEscStr($pupilOs)."', sumOsPupil='".sqlEscStr($sumOsPupil)."',
						examDate='".wv_dt('now')."',
						isPositive='".$isPositive."', 
						wnl='0', wnlPupilOd='0', wnlPupilOs='0',
						uid='".$uid."', statusElem='".sqlEscStr($statusElem)."',
						ut_elem=CONCAT(ut_elem, '".sqlEscStr($ut_elem)."'),
						modi_note_od = CONCAT('".sqlEscStr($modi_note_od)."',modi_note_od),
						modi_note_os = CONCAT('".sqlEscStr($modi_note_os)."',modi_note_os)
						WHERE formId='".$this->fid."' AND patientId='".$this->ptId."'
						";
				$row = sqlQuery($sql);
			
			}		
		}	
	}
	
	//KM_Data
	function saveKMData($ds){
		
		$dc = (isset($this->ns['nsKM'])) ? $ds->children($this->ns['nsKM']) : ''; 
		if(is_object($dc) && isset($dc->Measure->KM->R->Median)){			
			$flatAxs_od = "".$dc->Measure->KM->R->Median->R2->Axis;
			$flatAxs_os = "".$dc->Measure->KM->L->Median->R2->Axis;
			
			$diopt_r1_od="".$dc->Measure->KM->R->Median->R1->Power;
			$diopt_r2_od="".$dc->Measure->KM->R->Median->R2->Power;

			$diopt_r1_os="".$dc->Measure->KM->L->Median->R1->Power;
			$diopt_r2_os="".$dc->Measure->KM->L->Median->R2->Power;

		}else if(isset($ds->R->KM->KMMedian)){
//			$flatAxs_od = $ds->R->KM->KMMedian->KMCylinder->Axis;
//			$flatAxs_os = $ds->L->KM->KMMedian->KMCylinder->Axis;
			//R2-use R2->axis values
			$flatAxs_od = "".$ds->R->KM->KMMedian->R2->Axis;
			$flatAxs_os = "".$ds->L->KM->KMMedian->R2->Axis;
			
			$diopt_r1_od="".$ds->R->KM->KMMedian->R1->Power;
			$diopt_r2_od="".$ds->R->KM->KMMedian->R2->Power;

			$diopt_r1_os="".$ds->L->KM->KMMedian->R1->Power;
			$diopt_r2_os="".$ds->L->KM->KMMedian->R2->Power;			
			
		}else{
	
			if(isset($GLOBALS["marcoUseFlat"]) && $GLOBALS["marcoUseFlat"]==1){
				$flatAxs_od = "".$ds->KM_Data_OD->KM_mm_FlatAXS_OD;
				$flatAxs_os = "".$ds->KM_Data_OS->KM_mm_FlatAXS_OS;	
			}else{
				$flatAxs_od = "".$ds->KM_Data_OD->KM_mm_SteepAXIS_OD;
				$flatAxs_os = "".$ds->KM_Data_OS->KM_mm_SteepAXIS_OS;	
			}
			
			$diopt_r1_od="".$ds->KM_Data_OD->KM_Diopt_R1_OD;
			$diopt_r2_od="".$ds->KM_Data_OD->KM_Diopt_R2_OD;		
			
			$diopt_r1_os="".$ds->KM_Data_OS->KM_Diopt_R1_OS;
			$diopt_r2_os="".$ds->KM_Data_OS->KM_Diopt_R2_OS;		
		}
		
		//trim
		$flatAxs_od = trim($flatAxs_od);
		$diopt_r1_od = trim($diopt_r1_od);
		$diopt_r2_od = trim($diopt_r2_od);
		$flatAxs_os = trim($flatAxs_os);
		$diopt_r1_os = trim($diopt_r1_os);
		$diopt_r2_os = trim($diopt_r2_os);
		
		if(!empty($flatAxs_od) || !empty($diopt_r1_od) || !empty($diopt_r2_od) ||
			!empty($flatAxs_os) || !empty($diopt_r1_os) || !empty($diopt_r2_os)  ){
			
			//
			//include_once(dirname(__FILE__)."/Vision.php");			
			
			//Carry Forward prev
			$oVision = new Vision($this->ptId, $this->fid);
			if(!$oVision->isRecordExists()){
				$oVision->carryForward();
			}
			
			/*
			$stts="elem_visAkOdK=1,elem_visAkOdSlash=1,elem_visAkOdX=1,".
					"elem_visAkOsK=1,elem_visAkOsSlash=1,elem_visAkOsX=1,";
			$ut_elem="".$_SESSION["authId"]."@elem_visAkOdK,elem_visAkOdSlash,elem_visAkOdX,".
						"elem_visAkOsK,elem_visAkOsSlash,elem_visAkOsX,|";
			*/
			#arr Elements Info--
			$arrElemInfo = array("elem_visAkOdK", "elem_visAkOdSlash", "elem_visAkOdX", 
							"elem_visAkOsK", "elem_visAkOsSlash", "elem_visAkOsX" );
			
			$id_chart_vis_master=0;
			//			
			$sql = "SELECT id, status_elements, ut_elem FROM chart_vis_master 
					WHERE patient_id = '".$this->ptId."' AND form_id = '".$this->fid."' ";
			$row = sqlQuery($sql);
			if($row!=false){
				$id_chart_vis_master=$row["id"];
				//
				$vis_statusElements_prev=$row["status_elements"];
				//$vis_statusElements_prev=trim($vis_statusElements_prev.$stts);
				$vis_statusElements_prev = $this->getVisionStatusFlag($arrElemInfo, $row["status_elements"]);
				
				//
				$ut_elem_prev = $row["ut_elem"];
				//$ut_elem_prev = trim($ut_elem_prev.$ut_elem);
				$ut_elem_prev = $oVision->getUTElemString($ut_elem_prev,implode(",",$arrElemInfo));
				
				
				//update
				$sql = "UPDATE chart_vis_master SET ".
						"status_elements='".sqlEscStr($vis_statusElements_prev)."', ".
						"ut_elem='".sqlEscStr($ut_elem_prev)."' ".
						"WHERE patient_id = '".$this->ptId."' AND form_id = '".$this->fid."' ";
				sqlQuery($sql);
			}else{
			
				$ut_elem = $oVision->getUTElemString($ut_elems,implode(",",$arrElemInfo));
				$stts=implode("=1,",$arrElemInfo);
			
				//insert				
				$sql = "INSERT INTO chart_vis_master (patient_id, form_id, status_elements, ut_elem  ) ".
						"VALUES('".$this->ptId."','".$this->fid."','".sqlEscStr($stts)."','".sqlEscStr($ut_elem)."' );";
				$id_chart_vis_master=sqlInsert($sql);		
			}

			if(!empty($id_chart_vis_master)){
				$sql = "SELECT * FROM chart_ak where id_chart_vis_master='".$id_chart_vis_master."' ";
				$row = sqlQuery($sql);
				if($row!=false){	
				//update
					$sql = "UPDATE chart_ak SET ".
							"x_od='".sqlEscStr($flatAxs_od)."', slash_od='".sqlEscStr($diopt_r2_od)."', k_od='".sqlEscStr($diopt_r1_od)."',  ".
							"x_os='".sqlEscStr($flatAxs_os)."', slash_os='".sqlEscStr($diopt_r2_os)."', k_os='".sqlEscStr($diopt_r1_os)."', ".
							"exam_date='".wv_dt('now')."', uid='".$_SESSION["authId"]."' ".							
							"WHERE id_chart_vis_master = '".$id_chart_vis_master."'  ";
					sqlQuery($sql);
				}else{
				//insert				
					$sql = "INSERT INTO chart_ak (id, id_chart_vis_master, exam_date, uid, 
										x_od, slash_od, k_od, 
										x_os, slash_os, k_os  ) ".
							"VALUES(NULL, '".$id_chart_vis_master."','".wv_dt('now')."','".$_SESSION["authId"]."',
								'".sqlEscStr($flatAxs_od)."','".sqlEscStr($diopt_r2_od)."','".sqlEscStr($diopt_r1_od)."',
								'".sqlEscStr($flatAxs_os)."','".sqlEscStr($diopt_r2_os)."','".sqlEscStr($diopt_r1_os)."'
							);";
					sqlQuery($sql);		
				}
			}
		}
	}
	
	//KM_Peripheral
	function saveKM_Peri_Data($ds){}
	
	//SAGITTAL_Data
	function saveSagi_Data($ds){}
	
	//Topometry
	function saveTopo_Data($ds){}
	
	//Eccentricity
	function saveEccen_Data($ds){}
	
	//Subjective data
	function saveSubjective_Data($ds){
	
		$dc = (isset($this->ns['nsSBJ'])) ? $ds->children($this->ns['nsSBJ']) : '';
		if(is_object($dc) && isset($dc->Measure->RefractionTest->Type->ExamDistance->RefractionData->R)){
			
			$sphere_od = "".$dc->Measure->RefractionTest->Type->ExamDistance->RefractionData->R->Sph;
			$cylinder_od =  "".$dc->Measure->RefractionTest->Type->ExamDistance->RefractionData->R->Cyl;
			$axis_od = "".$dc->Measure->RefractionTest->Type->ExamDistance->RefractionData->R->Axis;
			$add_od="";
			$acuity_od = "".$dc->Measure->RefractionTest->Type->ExamDistance->VA->R;	
			$acuity_od = $this->prefix20s_VA($acuity_od);
			
			$sphere_os = "".$dc->Measure->RefractionTest->Type->ExamDistance->RefractionData->L->Sph;
			$cylinder_os =  "".$dc->Measure->RefractionTest->Type->ExamDistance->RefractionData->L->Cyl;
			$axis_os = "".$dc->Measure->RefractionTest->Type->ExamDistance->RefractionData->L->Axis;
			$add_os="";
			$acuity_os = "".$dc->Measure->RefractionTest->Type->ExamDistance->VA->L;		
			$acuity_os = $this->prefix20s_VA($acuity_os);
			
		}else{
		
		/*
		if($ds->Subjective_Data_OD->Dist){
			$sphere_od = "".$ds->Subjective_Data_OD->Dist->Sphere_OD;
			$cylinder_od =  "".$ds->Subjective_Data_OD->Dist->Cylinder_OD;
			$axis_od = "".$ds->Subjective_Data_OD->Dist->Axis_OD;
			//$se_od = "".$ds->Subjective_Data_OD->Dist->SE_;
			$add_od = "".$ds->Subjective_Data_OD->Dist->Add_OD;
			
			$sphere_os = "".$ds->Subjective_Data_OS->Dist->Sphere_OS;
			$cylinder_os =  "".$ds->Subjective_Data_OS->Dist->Cylinder_OS;
			$axis_os = "".$ds->Subjective_Data_OS->Dist->Axis_OS;
			//$se_os = "".$ds->Subjective_Data_OD->Dist->SE_;
			$add_os = "".$ds->Subjective_Data_OS->Dist->Add_OS;
			
			
			
			

		}else{
		*/
		
		if($ds->Subjective_Data_OD->Sphere_OD){
			$sphere_od = "".$ds->Subjective_Data_OD->Sphere_OD;
			$cylinder_od =  "".$ds->Subjective_Data_OD->Cylinder_OD;
			$axis_od = "".$ds->Subjective_Data_OD->Axis_OD;			
			$add_od = "".$ds->Subjective_Data_OD->Add_OD;
			$acuity_od = "".$ds->Subjective_Data_OD->DistVA_OD;
			$acuity_od = $this->prefix20s_VA($acuity_od);
			
			$sphere_os = "".$ds->Subjective_Data_OS->Sphere_OS;
			$cylinder_os =  "".$ds->Subjective_Data_OS->Cylinder_OS;
			$axis_os = "".$ds->Subjective_Data_OS->Axis_OS;			
			$add_os = "".$ds->Subjective_Data_OS->Add_OS;
			$acuity_os = "".$ds->Subjective_Data_OS->DistVA_OS;
			$acuity_os = $this->prefix20s_VA($acuity_os);
		
		}else{
		
			$sphere_od = "".$ds->Subjective_Data_OD->Sphere;
			$cylinder_od =  "".$ds->Subjective_Data_OD->Cylinder;
			$axis_od = "".$ds->Subjective_Data_OD->Axis;
			$se_od = "".$ds->Subjective_Data_OD->SE;
			$add_od = "".$ds->Subjective_Data_OD->Add;
			
			$sphere_os = "".$ds->Subjective_Data_OS->Sphere;
			$cylinder_os =  "".$ds->Subjective_Data_OS->Cylinder;
			$axis_os = "".$ds->Subjective_Data_OS->Axis;
			$se_os = "".$ds->Subjective_Data_OS->SE;
			$add_os = "".$ds->Subjective_Data_OS->Add;
			$acuity_od=$acuity_os="";
		
		}
		
		//}		
		
		//trim
		$sphere_od=trim($sphere_od);
		$sphere_os=trim($sphere_os);
		$cylinder_od =trim($cylinder_od);
		$cylinder_os =trim($cylinder_os);
		$axis_od = trim($axis_od);
		$axis_os = trim($axis_os);
		$add_od = trim($add_od);
		$add_os = trim($add_os);
		
		//* //stopped on 04-10-2014
		if(constant("MARCO_SUBJECTIVE_DATA") == "MR"){
		///Change the present logic so that they these fields map to MR and NOT to PC/over-refraction.
		if(empty($sphere_od) && empty($cylinder_od) && empty($axis_od) && empty($add_od) &&
			empty($sphere_os) && empty($cylinder_os) && empty($axis_os) && empty($add_os)
		){
			if($ds->Subjective_Data_OD->Dist){
				$sphere_od = "".$ds->Subjective_Data_OD->Dist->Sphere_OD;
				$cylinder_od =  "".$ds->Subjective_Data_OD->Dist->Cylinder_OD;
				$axis_od = "".$ds->Subjective_Data_OD->Dist->Axis_OD;
				$add_od="".$ds->Subjective_Data_OD->Dist->Add_OD;
				$acuity_od = "".$ds->Subjective_Data_OD->Dist->DistVA_OD;				
				$acuity_od = $this->prefix20s_VA($acuity_od);
				
				$sphere_os = "".$ds->Subjective_Data_OS->Dist->Sphere_OS;
				$cylinder_os =  "".$ds->Subjective_Data_OS->Dist->Cylinder_OS;
				$axis_os = "".$ds->Subjective_Data_OS->Dist->Axis_OS;
				$add_os="".$ds->Subjective_Data_OS->Dist->Add_OS;
				$acuity_os = "".$ds->Subjective_Data_OS->Dist->DistVA_OS;
				$acuity_os = $this->prefix20s_VA($acuity_os);
				
			}
		}
		}
		//*/
		
		} //end else
		
		//-------------------	

		//No add of 0.00, it should be blank		
		if($sphere_od=="0.00"||$sphere_od=="0"){$sphere_od = "Plano";}
		if($sphere_os=="0.00"||$sphere_os=="0"){$sphere_os = "Plano";}
		
		if($cylinder_od=="0.00"){$cylinder_od = "";}
		if($cylinder_os=="0.00"){$cylinder_os = "";}
		
		//when the cylinder is 0 or blank, the axis should be blank
		if(empty($cylinder_od) && trim($axis_od)=="0"){ $axis_od=""; }
		if(empty($cylinder_os) && trim($axis_os)=="0"){ $axis_os=""; }
		
		//r7: 0 axis should be converted to180 automatically
		if(trim($axis_od)=="0"){ $axis_od="180"; }
		if(trim($axis_os)=="0"){ $axis_os="180"; }
		
		
		if($axis_od=="0.00"){$axis_od = "";}
		if($axis_os=="0.00"){$axis_os = "";}
		
		if($add_od=="0.00"){$add_od = "";}
		if($add_os=="0.00"){$add_os = "";}
		
		//trim
		$sphere_od=trim($sphere_od);
		$sphere_os=trim($sphere_os);
		$cylinder_od =trim($cylinder_od);
		$cylinder_os =trim($cylinder_os);
		$axis_od = trim($axis_od);
		$axis_os = trim($axis_os);
		$add_od = trim($add_od);
		$add_os = trim($add_os);
		
		if(!empty($sphere_od) || !empty($cylinder_od) || !empty($axis_od) || !empty($add_od) || 
			!empty($sphere_os) || !empty($cylinder_os) || !empty($axis_os) || !empty($add_os)  ){
				
			//
			//include_once(dirname(__FILE__)."/Vision.php");			
			
			//Carry Forward prev
			$oVision = new Vision($this->ptId, $this->fid);
			if(!$oVision->isRecordExists()){
				$oVision->carryForward();
			}			
			
			/*
			$stts="elem_providerId=1,elem_visMrOdS=1,elem_visMrOdC=1,elem_visMrOdA=1,elem_visMrOdAdd=1,".
					"elem_visMrOsS=1,elem_visMrOsC=1,elem_visMrOsA=1,elem_visMrOsAdd=1,";
			$ut_elem="".$_SESSION["authId"]."@elem_providerId,elem_visMrOdS,elem_visMrOdC,elem_visMrOdA,elem_visMrOdAdd,".
						"elem_visMrOsS,elem_visMrOsC,elem_visMrOsA,elem_visMrOsAdd,|";
			*/
			#arr Elements Info--
			$arrElemInfo = array("elem_providerId","elem_providerName","elem_visMrOdS","elem_visMrOdC","elem_visMrOdA","elem_visMrOdAdd","elem_visMrOdTxt1",
							"elem_visMrOsS","elem_visMrOsC","elem_visMrOsA","elem_visMrOsAdd","elem_visMrOsTxt1" );
			$id_chart_vis_master=0;
			//
			$sql = "SELECT id, status_elements, ut_elem FROM chart_vis_master 
					WHERE patient_id = '".$this->ptId."' AND form_id = '".$this->fid."' ";
			$row = sqlQuery($sql);
			if($row!=false){
				$id_chart_vis_master=$row["id"];
				//
				$vis_statusElements_prev=$row["status_elements"];
				//$vis_statusElements_prev=trim($vis_statusElements_prev.$stts);
				$vis_statusElements_prev = $this->getVisionStatusFlag($arrElemInfo, $row["status_elements"]);
				
				//
				$ut_elem_prev = $row["ut_elem"];
				//$ut_elem_prev = trim($ut_elem_prev.$ut_elem);
				$ut_elem_prev = $oVision->getUTElemString($ut_elem_prev,implode(",",$arrElemInfo));
				
				
				//update
				$sql = "UPDATE chart_vis_master SET ".
						"status_elements='".sqlEscStr($vis_statusElements_prev)."', ".
						"ut_elem='".sqlEscStr($ut_elem_prev)."' ".
						"WHERE patient_id = '".$this->ptId."' AND form_id = '".$this->fid."' ";
				sqlQuery($sql);
			}else{
				
				$ut_elem = $oVision->getUTElemString($ut_elems,implode(",",$arrElemInfo));					
				$stts=implode("=1,",$arrElemInfo);
			
				//insert
				$sql = "INSERT INTO chart_vis_master (patient_id, form_id,  status_elements, ut_elem	) ".
						"VALUES('".$this->ptId."','".$this->fid."','".sqlEscStr($stts)."','".sqlEscStr($ut_elem)."' );";
				$id_chart_vis_master=sqlInsert($sql);		
			}	
		
		
			if(!empty($id_chart_vis_master)){	
				$sql = "SELECT * FROM chart_pc_mr where id_chart_vis_master='".$id_chart_vis_master."' AND ex_type='MR' AND ex_number='1' ";
				$row = sqlQuery($sql);
				if($row!=false){
					$chart_pc_mr_id = $row["id"];
					//update
					$sql = "UPDATE chart_pc_mr SET ".						
							"provider_id='".sqlEscStr($_SESSION["authId"])."', ".
							"exam_date='".wv_dt('now')."', uid='".$_SESSION["authId"]."' ".							
							"WHERE id_chart_vis_master='".$id_chart_vis_master."' ";
					sqlQuery($sql);					
					
					//update
					$sql = "UPDATE chart_pc_mr_values SET ".
							"sph='".sqlEscStr($sphere_od)."', cyl='".sqlEscStr($cylinder_od)."', ".
							"axs='".sqlEscStr($axis_od)."', ad='".sqlEscStr($add_od)."', ".
							"txt_1='".sqlEscStr($acuity_od)."' ".							
							"WHERE chart_pc_mr_id='".$chart_pc_mr_id."' AND site='OD' ";
					sqlQuery($sql);					
					
					//update
					$sql = "UPDATE chart_pc_mr_values SET ".
							"sph='".sqlEscStr($sphere_os)."', cyl='".sqlEscStr($cylinder_os)."', ".
							"axs='".sqlEscStr($axis_os)."', ad='".sqlEscStr($add_os)."', ".
							"txt_1='".sqlEscStr($acuity_os)."' ".							
							"WHERE chart_pc_mr_id='".$chart_pc_mr_id."' AND site='OS' ";
					sqlQuery($sql);					
					
				}else{
				//insert
					$sql = "INSERT INTO chart_pc_mr (id, id_chart_vis_master, provider_id, exam_date, uid, ex_type, ex_number  ) ".
							"VALUES(NULL, '".$id_chart_vis_master."', '".sqlEscStr($_SESSION["authId"])."', 
							exam_date='".wv_dt('now')."', uid='".$_SESSION["authId"]."', 'MR', '1' );";
					$chart_pc_mr_id = sqlInsert($sql);
					
					$sql = "INSERT INTO chart_pc_mr_values (id, chart_pc_mr_id, sph, cyl, axs, ad, txt_1, site)
							VALUES(NULL, '".$chart_pc_mr_id."', '".sqlEscStr($sphere_od)."', '".sqlEscStr($cylinder_od)."',
									'".sqlEscStr($axis_od)."', '".sqlEscStr($add_od)."', '".sqlEscStr($acuity_od)."', 'OD'),
									(NULL, '".$chart_pc_mr_id."', '".sqlEscStr($sphere_os)."', '".sqlEscStr($cylinder_os)."',
									'".sqlEscStr($axis_os)."', '".sqlEscStr($add_os)."', '".sqlEscStr($acuity_os)."', 'OS')
					";
					sqlQuery($sql);					
				}
			}
		}
	}
	
	
	//Subjective data : pc overrefraction
	function saveSubjective_Data_PC($ds){		
		
		//PC
		if(!isset($GLOBALS["STOP_SUBJECTIVE_DATA"]) || $GLOBALS["STOP_SUBJECTIVE_DATA"] != "1"){
		$sphere_od = "".$ds->Presenting_Data_OD->Sphere_OD;
		$cylinder_od =  "".$ds->Presenting_Data_OD->Cylinder_OD;
		$axis_od = "".$ds->Presenting_Data_OD->Axis_OD;
		$add_od = "".$ds->Presenting_Data_OD->Add_OD;
		
		$sphere_os = "".$ds->Presenting_Data_OS->Sphere_OS;
		$cylinder_os =  "".$ds->Presenting_Data_OS->Cylinder_OS;
		$axis_os = "".$ds->Presenting_Data_OS->Axis_OS;
		$add_os = "".$ds->Presenting_Data_OS->Add_OS;
		}//
		
		//over ref
		if(constant("MARCO_SUBJECTIVE_DATA") == "OVER-REF" || !empty($this->flg_pc_only)){		
		// Activated again on 04-10-2014 /*//Stopped by email from Gerald on 12-02-2014 17:20 : Change the present logic so that they these fields map to MR and NOT to PC/over-refraction.
		$sphere_overref_od = "".$ds->Subjective_Data_OD->Dist->Sphere_OD;
		$cylinder_overref_od =  "".$ds->Subjective_Data_OD->Dist->Cylinder_OD;
		$axis_overref_od = "".$ds->Subjective_Data_OD->Dist->Axis_OD;
		$v_overref_od = "".$ds->Subjective_Data_OD->Dist->DistVA_OD;
		$v_overref_od = $this->prefix20s_VA($v_overref_od);
		
		$sphere_overref_os = "".$ds->Subjective_Data_OS->Dist->Sphere_OS;
		$cylinder_overref_os =  "".$ds->Subjective_Data_OS->Dist->Cylinder_OS;
		$axis_overref_os = "".$ds->Subjective_Data_OS->Dist->Axis_OS;
		$v_overref_os = "".$ds->Subjective_Data_OS->Dist->DistVA_OS;
		$v_overref_os = $this->prefix20s_VA($v_overref_os);
		}
		//*/
		
		//trim
		$sphere_od = trim($sphere_od);
		$sphere_os = trim($sphere_os);
		$cylinder_od = trim($cylinder_od);
		$cylinder_os = trim($cylinder_os);
		$axis_od = trim($axis_od);
		$axis_os = trim($axis_os);
		$add_od = trim($add_od);
		$add_os = trim($add_os);
		$sphere_overref_od = trim($sphere_overref_od);
		$cylinder_overref_od = trim($cylinder_overref_od);
		$axis_overref_od = trim($axis_overref_od);
		$v_overref_od = trim($v_overref_od);
		$sphere_overref_os = trim($sphere_overref_os);
		$cylinder_overref_os = trim($cylinder_overref_os);
		$axis_overref_os = trim($axis_overref_os);
		$v_overref_os = trim($v_overref_os);		
		
		
		$flg_pc = (!empty($sphere_od) || !empty($cylinder_od) || !empty($axis_od) || !empty($add_od) || 
			!empty($sphere_os) || !empty($cylinder_os) || !empty($axis_os) || !empty($add_os) ) ? 1 : 0;
			
		$flg_pc_ovrf = (!empty($sphere_overref_od) || !empty($cylinder_overref_od) || !empty($axis_overref_od) || !empty($v_overref_od) || 
			!empty($sphere_overref_os) || !empty($cylinder_overref_os) || !empty($axis_overref_os) || !empty($v_overref_os) )	? 1 : 0;
		
		if($flg_pc || $flg_pc_ovrf ){
			
			
			
			//
			//include_once(dirname(__FILE__)."/Vision.php");			
			
			//Carry Forward prev
			$oVision = new Vision($this->ptId, $this->fid);
			if(!$oVision->isRecordExists()){
				$oVision->carryForward();
			}			
			
			#arr Elements Info--
			$arrElemInfo = array();
			if($flg_pc==1){
				$arrElemInfo = array("elem_visPcOdS", "elem_visPcOdC", "elem_visPcOdA", "elem_visPcOdAdd", 
								"elem_visPcOsS", "elem_visPcOsC", "elem_visPcOsA", "elem_visPcOsAdd");
			}
			
			if($flg_pc_ovrf==1){
				$arrElemInfo = array_merge($arrElemInfo, array("elem_visPcOdOverrefS", "elem_visPcOdOverrefC", "elem_visPcOdOverrefA", "elem_visPcOdOverrefV",
													"elem_visPcOsOverrefS", "elem_visPcOsOverrefC", "elem_visPcOsOverrefA", "elem_visPcOsOverrefV" ));	
			}					
			
			$id_chart_vis_master=0;
			//
			$sql = "SELECT id, status_elements, ut_elem FROM chart_vis_master 
					WHERE patient_id = '".$this->ptId."' AND form_id = '".$this->fid."' ";
			$row = sqlQuery($sql);
			if($row!=false){
				$id_chart_vis_master=$row["id"];
				//
				$vis_statusElements_prev=$row["status_elements"];
				//$vis_statusElements_prev=trim($vis_statusElements_prev.$stts);
				$vis_statusElements_prev = $this->getVisionStatusFlag($arrElemInfo, $row["status_elements"]);
				
				//
				$ut_elem_prev = $row["ut_elem"];
				//$ut_elem_prev = trim($ut_elem_prev.$ut_elem);
				$ut_elem_prev = $oVision->getUTElemString($ut_elem_prev,implode(",",$arrElemInfo));
				
				//update
				$sql = "UPDATE chart_vis_master SET ";
				$sql .=	" status_elements='".sqlEscStr($vis_statusElements_prev)."', ".
						"ut_elem='".sqlEscStr($ut_elem_prev)."' ".
						"WHERE patient_id = '".$this->ptId."' AND form_id = '".$this->fid."' ";
				sqlQuery($sql);
				
			}else{
				
				$ut_elem = $oVision->getUTElemString($ut_elems,implode(",",$arrElemInfo));					
				$stts=implode("=1,",$arrElemInfo);			
				//insert
				$sql = "INSERT INTO chart_vis_master (patient_id, form_id, status_elements, ut_elem ";
				$sql .=	") ".
						"VALUES('".$this->ptId."','".$this->fid."','".sqlEscStr($stts)."','".sqlEscStr($ut_elem)."' ";				
				$sql .=	");";
				$id_chart_vis_master=sqlInsert($sql);
				
			}		
			
			if(!empty($id_chart_vis_master)){				
				$sql = " SELECT * FROM chart_pc_mr where id_chart_vis_master='".$id_chart_vis_master."' AND ex_type='PC' AND ex_number='1' ";				
				$row = sqlQuery($sql);
				if($row!=false){					
					$chart_pc_mr_id = $row["id"];
					
					$sql = "UPDATE chart_pc_mr SET exam_date='".wv_dt('now')."', uid='".$_SESSION["authId"]."' WHERE id = '".$chart_pc_mr_id."'  ";
					sqlQuery($sql);
					
					$sql_od = $sql_os = "";
					$sql1 = "UPDATE chart_pc_mr_values SET ";
					if($flg_pc==1){
						$sql_od .= "sph='".sqlEscStr($sphere_od)."', cyl='".sqlEscStr($cylinder_od)."', axs='".sqlEscStr($axis_od)."', ad='".sqlEscStr($add_od)."',";
						$sql_os .= "sph='".sqlEscStr($sphere_os)."', cyl='".sqlEscStr($cylinder_os)."', axs='".sqlEscStr($axis_os)."', ad='".sqlEscStr($add_od)."',";
					}
					
					if($flg_pc_ovrf==1){
						//if($flg_pc==1){ $sql .= ", "; }
						$sql_od .="ovr_s='".sqlEscStr($sphere_overref_od)."', ovr_c='".sqlEscStr($cylinder_overref_od)."', ovr_a='".sqlEscStr($axis_overref_od)."', ovr_v='".sqlEscStr($v_overref_od)."',";
						$sql_os .="ovr_s='".sqlEscStr($sphere_overref_os)."', ovr_c='".sqlEscStr($cylinder_overref_os)."', ovr_a='".sqlEscStr($axis_overref_os)."', ovr_v='".sqlEscStr($v_overref_os)."',";
					}
					
					$sql_od = trim($sql_od,",");
					$sql_os = trim($sql_os,",");
					
					$sql = $sql1." ".$sql_od." WHERE chart_pc_mr_id = '".$chart_pc_mr_id."' AND site='OD' ";
					sqlQuery($sql);
					$sql = $sql1." ".$sql_os." WHERE chart_pc_mr_id = '".$chart_pc_mr_id."' AND site='OS' ";
					sqlQuery($sql);
				
				}else{
					
					$sql = "INSERT INTO chart_pc_mr (id,id_chart_vis_master,ex_type,ex_number,exam_date,uid) 
										VALUES (NULL,'".$id_chart_vis_master."','PC','1','".wv_dt('now')."','".$_SESSION["authId"]."') ";
					$chart_pc_mr_id = sqlInsert($sql);
					
					$sql_od=$sql_os="";
					if($flg_pc==1){
						$sql_od .= "'".sqlEscStr($sphere_od)."','".sqlEscStr($cylinder_od)."','".sqlEscStr($axis_od)."','".sqlEscStr($add_od)."',";
						$sql_os .= "'".sqlEscStr($sphere_os)."','".sqlEscStr($cylinder_os)."','".sqlEscStr($axis_os)."','".sqlEscStr($add_os)."',";
					}else{
						$sql_od .= "'','','','',";
						$sql_os .= "'','','','',";
					}
					
					if($flg_pc_ovrf==1){
						//if($flg_pc==1){ $sql .= ", "; }
						$sql_od .="'".sqlEscStr($sphere_overref_od)."','".sqlEscStr($cylinder_overref_od)."','".sqlEscStr($axis_overref_od)."','".sqlEscStr($v_overref_od)."',";
						$sql_os .="'".sqlEscStr($sphere_overref_os)."','".sqlEscStr($cylinder_overref_os)."','".sqlEscStr($axis_overref_os)."','".sqlEscStr($v_overref_os)."',";
					}else{
						$sql_od .= "'','','','',";
						$sql_os .= "'','','','',";
					}
					
					$sql_od .= "'OD'"; 
					$sql_os .= "'OS'";				
					
					$sql = "INSERT INTO chart_pc_mr_values (id,chart_pc_mr_id,sph,cyl,axs,ad,ovr_s,ovr_c,ovr_a,ovr_v,site) 
							VALUES (NULL,'".$chart_pc_mr_id."',".$sql_od."),(NULL,'".$chart_pc_mr_id."',".$sql_os.")";
					sqlQuery($sql);
				}
			}		
		}
		
	}
	
	//Add Diopters data
	function saveDiopters_Data($ds){
	
		//
		$add_diop_od = "".$ds->AddDiopters_Data->Add_Diop_OD;
		$add_diop_os = "".$ds->AddDiopters_Data->Add_Diop_OS;
		
		$add_diop_near_od = "".$ds->AddDiopters_Data->Add_Diop_Near_OD;
		$add_diop_near_os = "".$ds->AddDiopters_Data->Add_Diop_Near_OS;
		
		//added
		//r7: 0 axis should be converted to180 automatically
		if(trim($add_diop_od)=="0"){ $add_diop_od="180"; }
		if(trim($add_diop_os)=="0"){ $add_diop_os="180"; }
		if($add_diop_od=="0.00"){ $add_diop_od=""; }
		if($add_diop_os=="0.00"){ $add_diop_os=""; }
		if($add_diop_near_od=="0.00"){ $add_diop_near_od=""; }
		if($add_diop_near_os=="0.00"){ $add_diop_near_os=""; }			
		
		//trim
		$add_diop_od = trim($add_diop_od);
		$add_diop_os = trim($add_diop_os);
		$add_diop_near_od = trim($add_diop_near_od);
		$add_diop_near_os = trim($add_diop_near_os);
		
		if(!empty($add_diop_od) || !empty($add_diop_os) || !empty($add_diop_near_od) || !empty($add_diop_near_os) ){			
			
			//
			//include_once(dirname(__FILE__)."/Vision.php");			
			
			//Carry Forward prev
			$oVision = new Vision($this->ptId, $this->fid);
			if(!$oVision->isRecordExists()){
				$oVision->carryForward();
			}
			
			/*
			$stts="elem_providerIdOther=1,elem_visMrOtherOdS=1,elem_visMrOtherOdC=1,elem_visMrOtherOdA=1,elem_visMrOtherOdAdd=1,".
					"elem_visMrOtherOsS=1,elem_visMrOtherOsC=1,elem_visMrOtherOsA=1,elem_visMrOtherOsAdd=1,";
			$ut_elem="".$_SESSION["authId"]."@elem_providerIdOther,elem_visMrOtherOdS,elem_visMrOtherOdC,elem_visMrOtherOdA,elem_visMrOtherOdAdd,".
						"elem_visMrOtherOsS,elem_visMrOtherOsC,elem_visMrOtherOsA,elem_visMrOtherOsAdd,|";
			*/
			
			#arr Elements Info--
			$arrElemInfo = array("elem_providerIdOther","elem_visMrOtherOdS","elem_visMrOtherOdC","elem_visMrOtherOdA","elem_visMrOtherOdAdd",
								"elem_visMrOtherOsS","elem_visMrOtherOsC","elem_visMrOtherOsA","elem_visMrOtherOsAdd");
			
			$id_chart_vis_master=0;
			//
			$sql = "SELECT id, status_elements, ut_elem FROM chart_vis_master
					WHERE patient_id = '".$this->ptId."' AND form_id = '".$this->fid."' ";
			$row = sqlQuery($sql);
			if($row!=false){
				$id_chart_vis_master=$row["id"];
				//
				$vis_statusElements_prev=$row["status_elements"];
				//$vis_statusElements_prev=trim($vis_statusElements_prev.$stts);
				$vis_statusElements_prev = $this->getVisionStatusFlag($arrElemInfo, $row["status_elements"]);
				
				//
				$ut_elem_prev = $row["ut_elem"];
				//$ut_elem_prev = trim($ut_elem_prev.$ut_elem);
				$ut_elem_prev = $oVision->getUTElemString($ut_elem_prev,implode(",",$arrElemInfo));				
				
				//update
				$sql = "UPDATE chart_vis_master SET ".
						"status_elements='".sqlEscStr($vis_statusElements_prev)."', ".
						"ut_elem='".sqlEscStr($ut_elem_prev)."' ".
						"WHERE patient_id = '".$this->ptId."' AND form_id = '".$this->fid."' ";
				sqlQuery($sql);
			}else{
			
				$ut_elem = $oVision->getUTElemString($ut_elems,implode(",",$arrElemInfo));					
				$stts =implode("=1,",$arrElemInfo);
			
				//insert
				$sql = "INSERT INTO chart_vis_master (patient_id, form_id, status_elements, ut_elem ) ".
						"VALUES('".$this->ptId."','".$this->fid."','".sqlEscStr($stts)."','".sqlEscStr($ut_elem)."' );";
				$id_chart_vis_master=sqlInsert($sql);		
			}
			
			if(!empty($id_chart_vis_master)){
				$sql = "SELECT * FROM chart_pc_mr where id_chart_vis_master='".$id_chart_vis_master."' AND ex_type='MR' AND ex_number='2' ";
				$row = sqlQuery($sql);
				if($row!=false){
					$chart_pc_mr_id = $row["id"];
					$sql = "UPDATE chart_pc_mr SET exam_date='".wv_dt('now')."', uid='".$_SESSION["authId"]."',provider_id='".$_SESSION["authId"]."'
							WHERE id='".$chart_pc_mr_id."' ";
					$row = sqlQuery($sql);
					
					$sql = "SELECT c2.* FROM chart_pc_mr c1
							INNER JOIN chart_pc_mr_values c2 ON c2.chart_pc_mr_id=c1.id
							where c1.id_chart_vis_master='".$id_chart_vis_master."' AND c1.ex_type='MR' AND c1.ex_number='1'
					"; 
					$rez = sqlStatement($sql);		
					for($i=1;$row=sqlFetchArray($rez);$i++){
						if($row["site"]="OD"){
							$sql = "UPDATE chart_pc_mr_values SET sph='".$row["sph"]."', cyl='".$row["cyl"]."', axs='".$row["axs"]."', ad='".sqlEscStr($add_diop_od)."'
									WHERE chart_pc_mr_id='".$chart_pc_mr_id."' AND site='OD' ";
							$row = sqlQuery($sql);
						}else if($row["site"]="OS"){
							$sql = "UPDATE chart_pc_mr_values SET sph='".$row["sph"]."', cyl='".$row["cyl"]."', axs='".$row["axs"]."', ad='".sqlEscStr($add_diop_os)."'
									WHERE chart_pc_mr_id='".$chart_pc_mr_id."' AND site='OS' ";
							$row = sqlQuery($sql);
						}
					}					
				}else{
					$sql = "INSERT INTO chart_pc_mr (id, exam_date, provider_id, uid, ex_type, ex_number,id_chart_vis_master ) 
							VALUES(NULL, '".wv_dt('now')."', '".$_SESSION["authId"]."', '".$_SESSION["authId"]."', 'MR', '2', '".$id_chart_vis_master."' ) ";
					$chart_pc_mr_id =sqlInsert($sql);
					
					$sql = "SELECT c2.* FROM chart_pc_mr c1
							INNER JOIN chart_pc_mr_values c2 ON c2.chart_pc_mr_id=c1.id
							where c1.id_chart_vis_master='".$id_chart_vis_master."' AND c1.ex_type='MR' AND c1.ex_number='1'
					"; 
					$sql_in = "";
					$rez = sqlStatement($sql);		
					for($i=1;$row=sqlFetchArray($rez);$i++){
						if($row["site"]="OD"){
							$sql_in .= "(NULL,'".$row["sph"]."', '".$row["cyl"]."', '".$row["axs"]."', '".sqlEscStr($add_diop_od)."', '".$chart_pc_mr_id."', 'OD'   ),";							
						}else if($row["site"]="OS"){
							$sql_in .= "(NULL,'".$row["sph"]."', '".$row["cyl"]."', '".$row["axs"]."', '".sqlEscStr($add_diop_os)."', '".$chart_pc_mr_id."', 'OS'   ),";
						}
					}
					$sql_in = trim($sql_in,",");
					$sql = "INSERT INTO chart_pc_mr_values (id, sph, cyl, axs, ad,chart_pc_mr_id, site)
								VALUES  ".$sql_in;
					$sqlQuery($sql);
				}
			}
		}
	}
	
	function prefix20s_VA($ds){
		$ds = trim($ds);
		if(!empty($ds)){
			if(strpos($ds,"20/")===false){	$ds = "20/".$ds;		}
		}
		return $ds;
	}
	
	//Dist
	function savePresenting_Data_Dist($ds){
		
		$dist_od = "".$ds->Presenting_Data_OD->DistVA_OD;
		$dist_os = "".$ds->Presenting_Data_OS->DistVA_OS;
		
		$dist_od = trim($dist_od);
		$dist_os = trim($dist_os);
		
		if(!empty($dist_od) || !empty($dist_os)){
			$dist_od = $this->prefix20s_VA($dist_od);
			$dist_os = $this->prefix20s_VA($dist_os);			
			
			//
			//include_once(dirname(__FILE__)."/Vision.php");			
			
			//Carry Forward prev
			$oVision = new Vision($this->ptId, $this->fid);
			if(!$oVision->isRecordExists()){
				$oVision->carryForward();
			}
			
			#arr Elements Info--
			$arrElemInfo = array("elem_visDisOdTxt1", "elem_visDisOsTxt1", "elem_visDisOdSel1", "elem_visDisOsSel1");
			//
			$ds_acuity = "CC";
			$id_chart_vis_master=0;	
			//
			$sql = "SELECT id, status_elements, ut_elem FROM chart_vis_master
					WHERE patient_id = '".$this->ptId."' AND form_id = '".$this->fid."' ";
			$row = sqlQuery($sql);
			if($row!=false){
				$id_chart_vis_master=$row["id"];
				//
				$vis_statusElements_prev=$row["status_elements"];
				//$vis_statusElements_prev=trim($vis_statusElements_prev.$stts);
				$vis_statusElements_prev = $this->getVisionStatusFlag($arrElemInfo, $row["status_elements"]);
				
				//
				$ut_elem_prev = $row["ut_elem"];
				//$ut_elem_prev = trim($ut_elem_prev.$ut_elem);
				$ut_elem_prev = $oVision->getUTElemString($ut_elem_prev,implode(",",$arrElemInfo));
				
				
				//update
				$sql = "UPDATE chart_vis_master SET ".
						" status_elements='".sqlEscStr($vis_statusElements_prev)."', ".
						"ut_elem='".sqlEscStr($ut_elem_prev)."' ".
						"WHERE patient_id = '".$this->ptId."' AND form_id = '".$this->fid."' ";
				sqlQuery($sql);
			}else{
			
				$ut_elem = $oVision->getUTElemString($ut_elems,implode(",",$arrElemInfo));					
				$stts =implode("=1,",$arrElemInfo);
			
				//insert
				$sql = "INSERT INTO chart_vis_master (patient_id, form_id, vis_statusElements, ut_elem	 ) ".
						"VALUES('".$this->ptId."','".$this->fid."','".sqlEscStr($stts)."','".sqlEscStr($ut_elem)."' );";
				$id_chart_vis_master = sqlInsert($sql);		
			}
			
			if(!empty($id_chart_vis_master)){
				
				$sec_nm = "Distance";	
				$vals = array($ds_acuity,$dist_od,$ds_acuity, $dist_os);	
				$sql = "SELECT * FROM chart_acuity WHERE id_chart_vis_master='".$id_chart_vis_master."' AND sec_name='".$sec_nm."' AND sec_indx='1'  ";
				$row = sqlQuery($sql);
				if($row != false){
					$sql = "UPDATE chart_acuity SET exam_date='".wv_dt('now')."', uid='".$_SESSION["authId"]."',
							sel_od = '".sqlEscStr($vals[0])."', txt_od = '".sqlEscStr($vals[1])."',
							sel_os = '".sqlEscStr($vals[2])."', txt_os = '".sqlEscStr($vals[3])."'
							WHERE id_chart_vis_master='".$id_chart_vis_master."' AND sec_name='".$sec_nm."' AND sec_indx='1'
						";	
					sqlQuery($sql);	
				}else{
					$sql = "INSERT INTO chart_acuity (id, id_chart_vis_master, exam_date, uid, sec_name, sec_indx, 
										sel_od, txt_od, sel_os, txt_os 
									) VALUES (NULL,'".$id_chart_vis_master."','".wv_dt('now')."','".$_SESSION["authId"]."','".$sec_nm."','1',
										'".sqlEscStr($vals[0])."', '".sqlEscStr($vals[1])."', '".sqlEscStr($vals[2])."', '".sqlEscStr($vals[3])."' 	
									) ";
					sqlQuery($sql);
				}
				
			}
		
		}
	
	}
	
	////NT_Data
	function saveNTData($ds){
		
		//IOP
		$tx_iop_od = "".$ds->NT_Data->NT_Data_OD;
		$tx_iop_os = "".$ds->NT_Data->NT_Data_OS;
		$tx_time = "".$this->exam_tm;
		$examDate="".$this->exam_dt;		
		
		//
		$tx_iop_od = trim($tx_iop_od);
		$tx_iop_os = trim($tx_iop_os);
		
		if(!empty($tx_iop_od) || !empty($tx_iop_os)){		
		
			$tx_iop_od_ar = explode("/",$tx_iop_od);
			$tx_iop_od = $tx_iop_od_ar[0];
			
			$tx_iop_os_ar = explode("/",$tx_iop_os);
			$tx_iop_os = $tx_iop_os_ar[0];
			
			$oIopGonioSaver = new IopGonioSaver($this->ptId, $this->fid);			
			$js_reloadIOP= $oIopGonioSaver->addNewIOPMain($this->finalize_flag, "TX", $tx_iop_od, $tx_iop_os, $tx_time, $examDate);
		
			/*
			$sql="SELECT iop_id, multiplePressuer FROM chart_iop WHERE patient_id = '".$this->ptId."' AND form_id = '".$this->fid."' ";
			$row=sqlQuery($sql);
			if($row!=false){
				$FlgDone=0;
				$iop_id=$row["iop_id"];
				$multiplePressuer = $row["multiplePressuer"];	
				if(!empty($multiplePressuer)){				
					$arr_MP = unserialize($multiplePressuer);
					$len = count($arr_MP);
					if($len>0){					
						$indx = ($len>1) ? $len-1 : "";
						$tx = $arr_MP["multiplePressuer".$len]["elem_tx".$indx];
						$tx_od = $arr_MP["multiplePressuer".$len]["elem_appTrgtOd".$indx];
						$tx_os = $arr_MP["multiplePressuer".$len]["elem_appTrgtOs".$indx];
						
						//Check if empty
						if(empty($tx_od)&&empty($tx_os)){
							$arr_MP["multiplePressuer".$len]["elem_tx".$indx]=1;
							$arr_MP["multiplePressuer".$len]["elem_appTrgtOd".$indx]=$tx_od;
							$arr_MP["multiplePressuer".$len]["elem_appTrgtOs".$indx]=$tx_os;
							$FlgDone=1;
						}
						
					}
					
				}
				
				//UPDATE The Records
				
				
			}else{
				//Insert A Record
			
				$arrMP = array();
				$arrMP["multiplePressuer"]=array(
								    "elem_applanation" => "",
								    "elem_puff" =>  "",
								    "elem_tx" => 1,
								    "elem_appOd" =>  "",
								    "elem_appOs" =>  "",
								    "elem_appTime" =>  "",
								    "elem_descTa" =>  "",
								    "elem_puffOd" =>  "",
								    "elem_puffOs" =>  "",
								    "elem_puffTime" =>  "",
								    "elem_descTp" =>  "",
								    "elem_appTrgtOd" => $tx_od,
								    "elem_appTrgtOs" => $tx_od, 
								    "elem_xTime" => $tx_time,
								    "elem_descTx" =>  "",
								    "elem_tt" =>  "",
								    "elem_tactTrgtOd" =>  "",
								    "elem_tactTrgtOs" =>  "",
								    "elem_ttTime" =>  "",
								    "elem_descTt" =>  "",
								);
				$str_MP=serialize($arrMP);		
				$str_MP=sqlEscStr($str_MP);
				
				$strSumTx_d=getPrsrSum($tx,$tx_od,"");
				$strSumTx_s=getPrsrSum($tx,$tx_os,"");
				
				$ut_elem = "";
				
				
				//Summary
				$iop_result_od=$iop_result_os="";
				//Test
				$arrTLbl = array("T<sub>A</sub>: ","T<sub>p</sub>: ","T<sub>x</sub>: ","T<sub>t</sub>: ");
				$arrTOd = array("","",$strSumTx_d,"");
				$arrTOs = array("","",$strSumTx_s,"");
				$arrTdesc = array("","","","");
				
				for($i=0;$i<4;$i++){

					$tmp=trim($arrTOd[$i]); //Od
					if(!empty($tmp)){
						$tmp = preg_replace('/;(\&nbsp;)*$/', '', $tmp);// remove last ';'
						$iop_result_od.=$arrTLbl[$i].$tmp."<br/>";
					}
					$tmp=trim($arrTOs[$i]); //Os
					if(!empty($tmp)){
						$tmp = preg_replace('/;(\&nbsp;)*$/', '', $tmp); // remove last ';'
						$iop_result_os.=$arrTLbl[$i].$tmp."<br/>";
					}
					$tmp=trim($arrTdesc[$i]); //desc
					if(!empty($tmp)){
						$tmp = preg_replace('/;(\&nbsp;)*$/', '', $tmp); // remove last ';'
						$iop_result_od.="Desc ".$arrTLbl[$i]." ".$tmp."<br/>";
						$iop_result_os.="Desc ".$arrTLbl[$i]." ".$tmp."<br/>";
					}
					
				}
				//Test				
				
				//---
				
				$sql = "INSERT INTO chart_iop set
				  patient_id = '".$this->ptId."',
				  form_id = '".$this->fid."',  ".
				  "exam_date='$examDate', ".
				  //"iop_time = '$time_up', ".
				  "isPositive = '1',
				  sumOdIop = '".$strSumTx_d."',
				  sumOsIop = '".$strSumTx_s."',".
				  "multiple_pressure = '".$serialMultipPress."',
				   fieldCount = '1',
				   uid = '".$_SESSION["authId"]."',
				   statusElem='1',
				   ut_elem = '".$ut_elem."'				   
				  ";
				$insertId = sqlInsert($sql);			
				
				//---
			
			}
			*/	
		
		}		
		
	}
	
	function savePhoria_RA($ds){
		//Phoria
		//H
		$h_prism_od = "".$ds->Subjective_Data_OD->Dist->H_Prism_OD;
		$h_base_od = "".$ds->Subjective_Data_OD->Dist->H_Base_OD;
		$h_prism_os = "".$ds->Subjective_Data_OS->Dist->H_Prism_OS;
		$h_base_os = "".$ds->Subjective_Data_OS->Dist->H_Base_OS;
		
		//V
		$v_prism_od = "".$ds->Subjective_Data_OD->Dist->V_Prism_OD;
		$v_base_od = "".$ds->Subjective_Data_OD->Dist->V_Base_OD;
		$v_prism_os = "".$ds->Subjective_Data_OS->Dist->V_Prism_OS;
		$v_base_os = "".$ds->Subjective_Data_OS->Dist->V_Base_OS;
		
		$h_prism_od=trim($h_prism_od);
		$h_base_od=trim($h_base_od);
		$h_prism_os=trim($h_prism_os);
		$h_base_os=trim($h_base_os);
		
		$v_prism_od=trim($v_prism_od);
		$v_base_od=trim($v_base_od);
		$v_prism_os=trim($v_prism_os);
		$v_base_os=trim($v_base_os);
		
		
		//Hstr
		//if(!empty($h_prism_od) || !empty($h_base_od) || !empty($h_prism_os) || !empty($h_base_os)){ //
		$str_phoria_h="";
		if(!empty($h_prism_od) || !empty($h_base_od)){
			if(!empty($h_base_od)){$h_base_od = "B".$h_base_od;}			
			$str_phoria_h = "OD: ".$h_prism_od."/".$h_base_od."";
		}
		
		if(!empty($h_prism_os) || !empty($h_base_os)){
			if(!empty($str_phoria_h)){$str_phoria_h .= ", ";}
			if(!empty($h_base_os)){$h_base_os = "B".$h_base_os;}
			$str_phoria_h .= "OS: ".$h_prism_os."/".$h_base_os."";
		}
		if(!empty($str_phoria_h)){ $str_phoria_h="Phoria ".$str_phoria_h;  }
		
		//Vstr--
		$str_phoria_v="";
		if(!empty($v_prism_od) || !empty($v_base_od)){
			if(!empty($v_base_od)){$v_base_od = "B".$v_base_od;}
			$str_phoria_v = "OD: ".$v_prism_od."/".$v_base_od."";
		}
		if(!empty($v_prism_os) || !empty($v_base_os)){
			if(!empty($str_phoria_v)){$str_phoria_v .= ", ";}
			if(!empty($v_base_os)){$v_base_os = "B".$v_base_os;}
			$str_phoria_v .= "OS: ".$v_prism_os."/".$v_base_os."";
		}
		if(!empty($str_phoria_v)){ $str_phoria_v="Phoria ".$str_phoria_v;  }
		
		//}
		
		//NRA
		$str_nra="";
		$nra = trim("".$ds->Binocular_Vision_Tests->NegRelAccommodation_Blur_OU);
		if(!empty($nra)){  $str_nra.="NRA = ".$nra."";  }
		
		//PRA
		$str_pra="";
		$pra = trim("".$ds->Binocular_Vision_Tests->PosRelAccomodation_Blur_OU);
		if(!empty($pra)){  $str_pra.="PRA = ".$pra."";  }
		
		//--
		if(!empty($str_nra) || !empty($str_pra) || !empty($str_phoria_h) || !empty($str_phoria_v)){
		
			$comments_gen="";
			$ut_elem="";
			$statusElem="";
			$sumEom="";
			$eom_verti_desc="";
			$eom_hori_desc="";			
			
			//include_once(dirname(__FILE__)."/EOM.php");
			$oEOM=new EOM($this->ptId,$this->fid);
			if(!$oEOM->isRecordExists()){	$oEOM->carryForward();}
		
			$sql = "SELECT 
					comments_gen, ut_elem, statusElem, sumEom, eom_verti_desc, eom_hori_desc  
					FROM chart_eom 
					WHERE patient_id = '".$this->ptId."' AND form_id = '".$this->fid."' ";
			$row = sqlQuery($sql);
			if($row!=false){
				// do nothing --
				$comments_gen_o=$row["comments_gen"];
				$ut_elem_o=$row["ut_elem"];
				$statusElem_o=$row["statusElem"];
				$sumEom_o=$row["sumEom"];
				$eom_verti_desc_o=$row["eom_verti_desc"];
				$eom_hori_desc_o=$row["eom_hori_desc"];				
			}
			//else{
			
			//--
			$tmp_ut_elem = "";
			if(!empty($str_nra) && strpos($comments_gen_o,$str_nra)===false){
				if(!empty($comments_gen_o)){ $comments_gen_o.=", "; }
				$comments_gen = $comments_gen_o."".$str_nra;				
			}
			
			if(!empty($str_pra) && strpos($comments_gen,$str_pra)===false){
				if(!empty($comments_gen)){ $comments_gen.=", "; }
				$comments_gen = $comments_gen."".$str_pra;
			}
			
			if(!empty($str_nra) && strpos($comments_gen_o,$str_nra)===false || !empty($str_pra) && strpos($comments_gen,$str_pra)===false){ $tmp_ut_elem .= "elem_comments_gen,"; }
			
			
			if(!empty($str_phoria_h) && strpos($eom_hori_desc_o,$str_phoria_h)===false){
				if(!empty($eom_hori_desc_o)){ $eom_hori_desc_o.=", "; }
				$eom_hori_desc=$eom_hori_desc_o."".$str_phoria_h;
				$tmp_ut_elem .= "elem_eomHoriDesc,";
			}
			
			if(!empty($str_phoria_v) && strpos($eom_verti_desc_o,$str_phoria_v)===false ){
				if(!empty($eom_verti_desc_o)){ $eom_verti_desc_o.=", "; }
				$eom_verti_desc=$eom_verti_desc_o."".$str_phoria_v;
				$tmp_ut_elem .= "elem_eomVertiDesc,";
			}
			
			//
			$str_sumEom="";
			if(!empty($sumEom_o)){
				$str_sumEom=str_replace(array("$comments_gen_o,","$comments_gen_o","$eom_verti_desc_o,","$eom_verti_desc_o", "$eom_hori_desc_o,","$eom_hori_desc_o"),"",$sumEom_o);
				$str_sumEom=trim($str_sumEom);
			}	
			if(!empty($comments_gen)){
				if(!empty($str_sumEom)){ $str_sumEom.=", ";  }
				$str_sumEom.=$comments_gen;
			}
			if(!empty($eom_hori_desc)){
				if(!empty($str_sumEom)){ $str_sumEom.=", ";  }
				$str_sumEom.=$eom_hori_desc;
			}
			if(!empty($eom_verti_desc)){
				if(!empty($str_sumEom)){ $str_sumEom.=", ";  }
				$str_sumEom.=$eom_verti_desc;
			}
			
			//
			if(!empty($statusElem_o)){
				$statusElem=str_replace(array("elem_chng_divEom=1,","elem_chng_divEom=0,","elem_chng_divEom=1","elem_chng_divEom=0"),"",$statusElem_o);
				$statusElem=trim($statusElem);
			}
			if(!empty($statusElem)) { $statusElem.=",";}
			$statusElem.="elem_chng_divEom=1";
			
			//ut_elem
			$user_type = (isset($_SESSION["user_role"]) && !empty($_SESSION["user_role"])) ? $_SESSION["user_role"] : $_SESSION["logged_user_type"];
			$ut_elem = $ut_elem_o."|".$user_type."@".$tmp_ut_elem."|";			
			
			//			
			$sql = "UPDATE chart_eom SET ".
					"eom_hori_desc='".sqlEscStr($eom_hori_desc)."', eom_verti_desc='".sqlEscStr($eom_verti_desc)."', ".
					"sumEom='".sqlEscStr($str_sumEom)."', isPositive_3='1', ".
					"statusElem='".$statusElem."',ut_elem='".$ut_elem."', comments_gen='".sqlEscStr($comments_gen)."', exam_date='".wv_dt('now')."'  ".
					"WHERE patient_id = '".$this->ptId."' AND form_id = '".$this->fid."' ";
			$row = sqlQuery($sql);
			//--
			
			//}
		}
		//--		
	}
	
	//--
	
	
	function saveData($ar){
		if($ar==false||(count($ar)<=0&&count($this->ns)<=0)){return 1;}
		
		if(isset($this->ns['nsCommon'])){
			$dc = $ar->children($this->ns['nsCommon']); 
		}		
		
		$file_mode = (is_object($dc) && isset($dc->Common->ModelName)) ? "".$dc->Common->ModelName : "".$ar->Model;
		
		if(isset($ar->ExamDate)){	
			list($exam_date,$exam_time) = $this->formatDate($ar->ExamDate);
		}else if(isset($ar->Date)){
			$exam_date = str_replace("/", "-", $ar->Date);
			$exam_time = $ar->Time;
		}else if(is_object($dc)){
			if(isset($dc->Common->Date)) { $exam_date = str_replace("/", "-", "".$dc->Common->Date);}
			if(isset($dc->Common->Time)) { $exam_time = "".$dc->Common->Time;}
		}
		
		$this->exam_dt = $exam_date;
		$this->exam_tm = $exam_time;
		
		//AK: 27-02-2013: xml file may not have patientId block.		
		if(isset($ar->PatientID)){
			$file_ptId = $ar->PatientID;
		}else if(isset($ar->Patient->ID)){
			$file_ptId = $ar->Patient->ID;
		}else if(is_object($dc) && isset($dc->Common->Patient->ID)){
			$file_ptId = "".$dc->Common->Patient->ID;
		}
		
		$file_ptId=$this->ptId;		
		
		if(empty($this->ptId) || empty($this->fid) || ($file_ptId!=$this->ptId)  ){return 1;} // No patient Id so no save //
		
		if(isset($ar->DataSet)){
			$ds = $ar->DataSet;	
		}else{
			$ds = $ar;
		}
		
		//*
		if(empty($this->flg_pc_only)){
		//Objective_Data
		$this->saveObjectiveData($ds);			
		
		
		//Objective_Data : Distance
		$this->saveObjectiveData_Dis($ds);		
		
		//ContactLens_Data
		$this->saveCLData($ds);
		
		//TrialLens_Data
		$this->saveTLData($ds);
		
		//PD_Data
		$this->savePDData($ds);
		
		//CornealSize_Data
		$this->saveCornSizeData($ds);
		
		//PupilSize_Data
		$this->savePupilSizeData($ds);
		
		//KM_Data
		$this->saveKMData($ds);
		
		//KM_Peripheral
		$this->saveKM_Peri_Data($ds);
		
		//SAGITTAL_Data
		$this->saveSagi_Data($ds);
		
		//Topometry
		$this->saveTopo_Data($ds);
		
		//Eccentricity
		$this->saveEccen_Data($ds);

		//Subjective data
		$this->saveSubjective_Data($ds);
		
		//Add Diopters data
		$this->saveDiopters_Data($ds);	
		
		//NT_Data
		$this->saveNTData($ds);		
		}
		
		//PC - over ref
		$this->saveSubjective_Data_PC($ds);
		
		if(empty($this->flg_pc_only)){
		//Distance : 
		$this->savePresenting_Data_Dist($ds);
		
		//saveUnaidedVisualAcuity_Dis: Distance + near
		$this->saveUnaidedVisualAcuity_Dis($ds);
		
		//
		//savePhoria
		$this->savePhoria_RA($ds);
		}
		//*/			
		
		
		return 0;
	}	
	
	function parseData($path){		
		//Define parsing xml
		$ar = simplexml_load_file($path);		
		$this->ns = $ar->getNameSpaces(true);//
		return $ar;
	}	
	
	function saveFile($path){		
		if(!file_exists($path)){return false;}			
		$ar = $this->parseData($path);
		$res = $this->saveData($ar);		
		
		return $res;		
	}
	
}

//*/

//TEst

//$obj = new MarcoConn();
//$obj->saveFile("macro-xml-data/ARK510AOutput.xml");

//echo "<br/><br/>DONE";

?>