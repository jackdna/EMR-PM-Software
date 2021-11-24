<?php
/*
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
*/
?>
<?php
/*
File: process_visit_code.php
Coded in PHP7
Purpose: This file does processing to find the related Visit code for a superbill.
Access Type : Include file
*/
?>
<?php
     
    //is Consult Patient     
    $reason = (($postCcHx == "db")) ? CcHx::refineCcHxStr($row["reason"]): $postCcHx ;
   
   /*
    //17-03-2015: DOCS (5528) � Consult codes for E&M codes have been discontinued (apparently since 2000).  We do not need Consult codes in E&M codes.  All we need existing and new Pt codes.
    $strConsult = '/\breferred\s*by\b/i';
    if(preg_match($strConsult,$reason))
    {
      $patientCategory="Consult";
      $practiceBillCode="992";      
    }
    //*/

    // Level of Service
    $examLevel2 = $examCCHistory = $examNeuroPsych = $examVision = $examLa = $examPupil = $examIop = $examSle = $examOptic = 0;
    $examLevel4 = $examEe = $examEom = $examRv = 0;
    $examCCHistory = (!empty($reason)) ? 1 : 0;
    //echo  "examCCHistory : $examCCHistory<br>";
    
   //RVS 			    
   if($postRvs == "db"){
	//get from Db
		$levelHistory = 0; //"Problem focused";
		$cRvs = 0;
		
		//if(($cRvs >=1)){
			//$levelHistory = 1;	
			if( !empty($row["complaint1Str"]) || !empty($row["complaint2Str"]) || !empty($row["complaint3Str"]) ){
				/* Stoped as per arun sir's guidence 06 november
				//$levelHistory = ($cRvs > 3) ? 3 : 2; //"Detailed and Comprehensive" : "An expanded problem focused";
				*/
				$levelHistory = 1;
				if(!empty($row["complaint1Str"])){
					$cRvs = CcHx::getRvsDoneLevel_php($row["complaint1Str"]);
					if($cRvs > $levelHistory){
						$levelHistory = $cRvs;
					}
				}
				
				if(!empty($row["complaint2Str"])){
					$cRvs = CcHx::getRvsDoneLevel_php($row["complaint2Str"]);
					if($cRvs > $levelHistory){
						$levelHistory = $cRvs;
					}
				}
				
				if(!empty($row["complaint3Str"])){
					$cRvs = CcHx::getRvsDoneLevel_php($row["complaint3Str"]);
					if($cRvs > $levelHistory){
						$levelHistory = $cRvs;
					}
				}
				
			}else{	
				//$cRvs =0;
				$arrRemTag = array("<+O@#+>","<+type%$+>","<+Float*&+>","<+FL@^+>","<+Mig&$+>");
				$arrChkVals = array($row["neuroMigHead"],$row["neuroHeadaches"],$row["neuroVisionLoss"],
								$row["neuroTempArtSymp"],$row["neuroDblVision"],$row["neuroOther"],
								$row["psAmslerGrid"],$row["psFlashingLights"],$row["psFloaters"],
								$row["psSpots"],$row["irrOcular"],$row["irrLidsExternal"],
								$row["vpOther"],$row["vpGlare"],$row["vpNear"],$row["vpMidDistance"],$row["vpDistance"],
								$row["rvspostop"], $row["rvsfollowup"],$row["vpComment"]
							   );
				foreach($arrChkVals as $keyRvs => $valRvs){
					$chkValRvs = str_replace($arrRemTag, "", $valRvs);
					$chkValRvs = trim($chkValRvs);
					//$cRvs = ((!empty($chkValRvs))) ? $cRvs + 1 : $cRvs;
					if(!empty($chkValRvs)){ $cRvs =1;$levelHistory = 1; }
				}
			}
		//}
	
	
   }else{
	$levelHistory = !empty($postRvs) ? $postRvs : 0 ; //HPI
   }
    
    //General Health
    $oMdHx = new MedHx($this->pid);
    $examGenHealth = $oMdHx->isGenHealthDone();  
    list($examMedConds, $examMedROS, $levelPFSH) = $oMdHx->isMedHxDone();	
    
    //$examNeuroPsych = (($row["neuro_ao"] == "1") || ($row["neuro_aff"] == "1")) ? 1 : 0;
    if($postNpsych == "db"){
	$examNeuroPsych = (!empty($row["neuroPsych"])) ? 1 : 0;
    }else{
	$examNeuroPsych = $postNpsych;
    }
    
    //Vision
    if($postVision == "db"){
	//get from db
		// Test Visual Acuity(Vision)
		$examVision =  $oVis->isVisualAcuityDone();
		if($chkRef==true){ $examVision =  1 ;}	
    }else{
		$examVision = !empty($postVision) ? $postVision : 0;
    }
	
   //echo  "examVision : $examVision<br>";
   //Gross VisualField testing by Confrontation (CVF And Amsler Grid)
   $examAmsler = (!empty($row["amslerGridId"])) ? 1 : 0 ;
   $examCvf = (!empty($row["cvf_id"])) ? 1 : 0;
   $examGrossVF = (!empty($examAmsler) && !empty($examCvf)) ? 1 : 0;
   $examCvf_or_Ag = (!empty($examAmsler) || !empty($examCvf)) ? 1 : 0;
   //echo "<br>examGrossVF: ".$examGrossVF;
   
	//Fundus : Dilation, Ophthalmoscopy exam
	//Diation 
	$examDilation = (!empty($row["dia_id"]) &&
				(!empty($row["pheny25"]) ||
				 !empty($row["tropicanide"]) ||
				 !empty($row["cyclogel"]) ||
				 !empty($row["other"]) ||
				 !empty($row["dilated_mm"]) ||
				 !empty($row["warned_n_advised"]) ||
				 !empty($row["patient_not_driving"]) ||
				 !empty($row["patientAllergic"]) ||
				 !empty($row["unableDilation"]) 
				)) ? 1 : 0;
	
	//Ophthamalscopy	
	$examOphtha = 0;
	if(!empty($row["ophthaId"])){
		$examOphtha = 1;			
	}else if(!empty($row["rvId"]) && !empty($row["seRv"]) && strpos($row["seRv"],"5_Od=1") !== false){
		$oCLSD = new CLSDrawingData();
		$apOd = $oCLSD->isAppletModified($row["od_drawing"]);
		$apOs = $oCLSD->isAppletModified($row["os_drawing"]);
		if(($apOd == true) || ($apOs == true) || !empty($row["wnlDrawOd_RV"]) || !empty($row["wnlDrawOs_RV"])){
			$examOphtha = 1;
		}
	}

	//$examFundus = (!empty($examDilation) && !empty($examOphtha)) ? 1 : 0;
	//echo "<br>examFundus: ".$examFundus;	

	//LA
	$lidsId = (!empty($row["lidsId"]) && !empty($row["lesionId"]) && !empty($row["lidPosId"]) && !empty($row["lacSysId"]) && !empty($row["laDrawId"])) ? 1 : 0;
	$seLa = trim($row["seLids"].$row["seLesion"].$row["seLidPos"].$row["seLacSys"].$row["seLaDraw"]);
	$examLa = (!empty($lidsId) && 
				!empty($seLa) && 
				(strpos($row["seLids"],"1_Od=1") !== false || strpos($row["seLids"],"1_Os=1") !== false ||
				 strpos($row["seLesion"],"2_Od=1") !== false || strpos($row["seLesion"],"2_Os=1") !== false ||	
				 strpos($row["seLidPos"],"3_Od=1") !== false || strpos($row["seLidPos"],"3_Os=1") !== false ||	
				 strpos($row["seLacSys"],"4_Od=1") !== false || strpos($row["seLacSys"],"4_Os=1") !== false ||
				 strpos($row["seLaDraw"],"5_Od=1") !== false || strpos($row["seLaDraw"],"5_Os=1") !== false	
				) &&
				( 
				 !empty($row["lid_conjunctiva_summary"]) || 
				 !empty($row["lesion_summary"]) || 
				 !empty($row["lid_deformity_position_summary"]) ||
				 !empty($row["la_od_txt"]) || 
				 !empty($row["la_os_txt"]) ||					 
				 !empty($row["sumLidsOs"]) ||					 
				 !empty($row["sumLidPosOs"]) ||
				 !empty($row["sumLesionOs"]) ||				 
				 !empty($row["posLids"]) || !empty($row["posLesion"]) ||  !empty($row["posLidPos"]) || !empty($row["posLacSys"]) || !empty($row["posDraw"]) ||				 
				 !empty($row["wnlLidsOd_LA"]) ||
				 !empty($row["wnlLidsOs_LA"]) ||
				 !empty($row["wnlLesionOd_LA"]) ||
				 !empty($row["wnlLesionOs_LA"]) ||
				 !empty($row["wnlLidPosOd_LA"]) ||
				 !empty($row["wnlLidPosOs_LA"]) ||				 
				 !empty($row["wnlDrawOd_LA"]) ||
				 !empty($row["wnlDrawOs_LA"])
				)
			    ) ? 1 : 0;
	//echo "<br>examLa: ".$examLa;
	   
	//LA ( Lac Sys )
	$examLaLacSys = (!empty($lidsId) &&
					!empty($seLa) &&
					(strpos($seLa,"4_Od=1") !== false || strpos($seLa,"4_Os=1") !== false) &&
				(!empty($row["lacrimal_system_summary"]) || 
				 !empty($row["sumLacOs"]) ||
				 !empty($row["wnlLacSysOd_LA"]) ||
				 !empty($row["wnlLacSysOs_LA"])
				)
			    ) ? 1 : 0; 
	
	/*
	echo "<br>\nexamLaLacSys: ".$examLaLacSys;	    
    echo  "\nexamLa : $examLa<br>";
	echo  "\n\nexamLa : ".$row["seLa"];	
	exit();	
	*/

    $examPupil = (!empty($row["pupilId"]) &&
					!empty($row["sePupil"]) &&
					(strpos($row["sePupil"],"=1") !== false) &&
			 (!empty($row["apdMinusOdSummary"]) ||
			  !empty($row["apdMinusOsSummary"]) ||
			  !empty($row["apdPlusOdSummary"]) ||
			  !empty($row["reactionOdSummary"]) ||
			  !empty($row["reactionOsSummary"]) ||
			  !empty($row["shapeOdSummary"]) ||
			  !empty($row["shapeOsSummary"])  ||
			  !empty($row["sumOdPupil"])  ||
			  !empty($row["sumOsPupil"]) ||
			  !empty($row["wnlPupil"])  ||
			  !empty($row["wnlPupilOd"])  ||
			  !empty($row["wnlPupilOs"])  ||
			  !empty($row["perrlaPupil"])  ||
			  !empty($row["isPos_Pupil"])  ||
			  !empty($row["examinedNoChange_pupil"])
			 )
			) ? 1 : 0;
    //echo  "examPupil : $examPupil<br>"; 			
    $examIop = (
			!empty($row["multiple_pressure"]) || !empty($row["anesthetic"])||
			!empty($row["sumOdIop"]) || !empty($row["sumOsIop"]) ||
			!empty($row["puff"]) || !empty($row["applanation"]) ||
			!empty($row["puff_od"]) || !empty($row["puff_os_1"]) ||
			!empty($row["puff_trgt_od"]) || !empty($row["puff_trgt_os"]) ||
			!empty($row["app_od"]) || !empty($row["app_os_1"]) ||
			!empty($row["app_trgt_od"]) || !empty($row["app_trgt_os"]) ||
			!empty($row["squeezing"]) || !empty($row["unreliable"]) ||
			!empty($row["unable"]) || !empty($row["squeezingApp"]) ||
			!empty($row["unreliableApp"]) || !empty($row["unableApp"]) ||
			!empty($row["wnlIOP"]) || !empty($row["isPos_IOP"]) ||
			!empty($row["examined_no_change_iop"])
			) ? 1 : 0;
    //echo  "examIop : $examIop<br>";
    //SLE (Conjuctivae)
   $examSleConjuctivae = (!empty($row["conjId"]) &&
			!empty($row["seConj"]) &&
			(strpos($row["seConj"],"1_Od=1") !== false || strpos($row["seConj"],"1_Os=1") !== false) &&
			(!empty($row["conjunctiva_od_summary"]) ||
			 !empty($row["conjunctiva_os_summary"]) ||
			 !empty($row["wnlConj"]) || 
			 !empty($row["posConj"]) ||
			 !empty($row["wnlConjOd_SLE"]) ||
			 !empty($row["wnlConjOs_SLE"])
			)	
		     ) ? 1 : 0;
//echo "<br>examSleConjuctivae: ".$examSleConjuctivae;		     
		     
   //Sle (Anterior Chambers)
   $examSleAntChm = (!empty($row["antId"]) &&
				!empty($row["seAnt"]) &&
				(strpos($row["seAnt"],"3_Od=1") !== false || strpos($row["seAnt"],"3_Os=1") !== false) &&
				(!empty($row["anf_chamber_od_summary"]) ||
				 !empty($row["anf_chamber_os_summary"]) ||
				 !empty($row["wnlAnt"]) || 
				 !empty($row["posAnt"]) ||
				 !empty($row["wnlAntOd_SLE"]) ||
				 !empty($row["wnlAntOs_SLE"])
				)	
			     ) ? 1 : 0;
//echo "<br>examSleAntChm: ".$examSleAntChm;

    //Sle (Cornea)
    $examSleCornea = (!empty($row["cornId"]) &&
			!empty($row["seCorn"]) &&
			(strpos($row["seCorn"],"2_Od=1") !== false || strpos($row["seCorn"],"2_Os=1") !== false) &&
			(!empty($row["cornea_od_summary"]) ||
			 !empty($row["cornea_os_summary"]) ||
			 !empty($row["wnlCorn"]) || 
			 !empty($row["posCorn"]) ||
			 !empty($row["wnlCornOd_SLE"]) ||
			 !empty($row["wnlCornOs_SLE"])
			)	
		     ) ? 1 : 0;
//echo "<br>examSleCornea: ".$examSleCornea;
    
    //Sle(Others)
    $examSle = (!empty($row["irisId"]) && !empty($row["lensId"]) &&
			!empty($row["seIris"]) && !empty($row["seLens"]) &&
			(strpos($row["seIris"],"4_Od=1") !== false || strpos($row["seIris"],"4_Os=1") !== false ||
			 strpos($row["seLens"],"5_Od=1") !== false || strpos($row["seLens"],"5_Os=1") !== false ) &&
			(!empty($row["iris_pupil_od_summary"]) ||
			 !empty($row["iris_pupil_os_summary"]) ||
			 !empty($row["lens_od_summary"]) ||
			 !empty($row["lens_os_summary"]) ||
			 !empty($row["wnlIris"]) || 
			 !empty($row["posIris"]) ||
			 !empty($row["wnlLens"]) || 
			 !empty($row["posLens"]) ||
			 !empty($row["wnlIrisOd_SLE"]) ||
			 !empty($row["wnlIrisOs_SLE"]) ||
			 !empty($row["wnlLensOd_SLE"]) ||
			 !empty($row["wnlLensOs_SLE"])
			)	
		     ) ? 1 : 0;
//echo "<br>examSle: ".$examSle;

    $examOptic = (!empty($row["opticId"]) &&
				  !empty($row["seOptic"]) &&
				  (strpos($row["seOptic"],"=1") !== false) &&
				(!empty($row["optic_nerve_od_summary"]) ||
				 !empty($row["optic_nerve_os_summary"]) ||
				 !empty($row["od_text"]) ||
				 !empty($row["os_text"]) ||
				 !empty($row["wnlOptic"])  ||
				 !empty($row["examined_no_change_optic"])  ||
				 !empty($row["isPos_Optic"]) ||
				 !empty($row["wnlOpticOd"]) ||
				 !empty($row["wnlOpticOs"])
				)
			) ? 1 : 0;
    //echo "SEE: -- ".$row["opticId"]." : ".$row["optic_nerve_od_summary"]." : ".$row["optic_nerve_os_summary"]."<br>";
    
    //echo  "examOptic : $examOptic<br>";			
    $examEe = (!empty($row["eeId"]) &&
			   !empty($row["seEe"]) &&
			   (strpos($row["seEe"],"=1") !== false) &&
			(!empty($row["external_exam_summary"]) ||
			 !empty($row["sumOsEE"]) ||
			 !empty($row["ee_desc"]) ||
			 !empty($row["wnlEE"]) ||
			 !empty($row["wnlEeOd"]) ||
			 !empty($row["wnlEeOs"]) ||
			 !empty($row["isPos_EE"]) ||
			 !empty($row["examined_no_change_ee"])
			)   
		    ) ? 1 : 0;
    //echo  "examEe : $examEe<br>";		    
    $examEom = (!empty($row["eomId"]) &&
			  (!empty($row["npc_wnl_abn"]) ||
			   !empty($row["npc_cm"]) ||
			   !empty($row["eom_full"]) ||
			   !empty($row["eom_ortho"]) ||
			   !empty($row["eom_abn_right_left_alter"]) ||
			   !empty($row["eom_abn_near_far_both"]) ||
			   !empty($row["eom_abn_desc"]) ||
			   !empty($row["eom_hori_eso_exo"]) ||
			   !empty($row["eom_hori_trophia_phoria"]) ||
			   !empty($row["eom_hori_near_far_both"]) ||
			   !empty($row["eom_hori_desc"]) ||
			   !empty($row["eom_verti_hyper_hypo"]) ||
			   !empty($row["eom_verti_near_far_both"]) ||
			   !empty($row["eom_verti_trophia_phoria"]) ||
			   !empty($row["eom_verti_desc"]) ||
			   !empty($row["diplopia_summary"]) ||
			   !empty($row["cvf_summary"]) ||
			   !empty($row["wnlEOM"])  ||
			   !empty($row["examined_no_change_eom"])  ||
			   !empty($row["isPos_EOM"]) 		
			  )
			) ? 1 : 0;
    //echo  "examEom : $examEom<br>";
    //exit;
    $examRv = (!empty($row["rvId"]) &&
			   ((!empty($row["seRv"]) &&
			   (strpos($row["seRv"],"=1") !== false) &&	
				(!empty($row["vitreous_od_summary"]) ||
				 !empty($row["vitreous_os_summary"]) ||
				 !empty($row["blood_vessels_od_summary"]) ||
				 !empty($row["blood_vessels_os_summary"]) ||
				 !empty($row["macula_od_summary"]) ||
				 !empty($row["macula_os_summary"]) ||
				 !empty($row["retina_od_summary"]) ||
				 !empty($row["retina_os_summary"]) ||
				 !empty($row["od_desc"]) ||
				 !empty($row["os_desc"]) ||
				 !empty($row["periphery_od_summary"]) ||
				 !empty($row["periphery_os_summary"]) ||
				 !empty($row["wnlRV"]) || 	
				 !empty($row["isPos_RV"]) ||

				 !empty($row["wnlVitreousOd_RV"]) ||
				 !empty($row["wnlVitreousOs_RV"]) ||
				 !empty($row["wnlMaculaOd_RV"]) ||
				 !empty($row["wnlMaculaOs_RV"]) ||
				 !empty($row["wnlPeriOd_RV"]) ||
				 !empty($row["wnlPeriOs_RV"]) ||
				 !empty($row["wnlBVOd_RV"]) ||
				 !empty($row["wnlBVOs_RV"]) ||
				 !empty($row["wnlDrawOd_RV"]) ||
				 !empty($row["wnlDrawOs_RV"]) ||
				  	
				 !empty($row["examined_no_change_rv"]) 
				)) ||
				(!empty($row["periNotExamined"]) && !empty($row["peri_ne_eye"]))) 
		    ) ? 1 : 0;
    //echo  "examRv : $examRv<br>";
	
	//Fundus Exam,Ophthalmoscopy,Optic Final Check
	//if any one is Done Than this requirement is satisfied -----
	if($examRv == 1 || $examOphtha == 1 || $examOptic == 1 ){
		$examRv = $examOphtha = $examOptic = 1;
	}
	//-------------

    
   //Assessment & Plan 
   if($postAssess == "db"){
		//get From db
		$examAssessmentPlan = (!empty($row["assess_plan"])&&strpos($row["assess_plan"],"<assessment>")!==false) ? 1 : 0 ;
   }else{
		$examAssessmentPlan = (!empty($postAssess)) ? $postAssess : 0;
   }
   
   //test
	//GET Level For Eye Code		
	$arrCodingExmInter=array("HPI", "Medical History - Medical Conditions", "Vision", "External Exam",
						"L &amp; A","Assessment &amp; Plan"); //"Medical History - ROS",	//"Chief Complaint &amp; History", "General Health",						
	$arrCodingExmComp=array("CVF", "EOM", "Fundus Exam" );	//"CVF or Amsler 	Grid"	, "IOP", "Dilation",	, "Ophthalmoscopy", "Fundus Exams - Optic Nerve", "Fundus Exam (any one) - Optic Nerve; Macula; Vitreous; Periphery; Blood Vessels; Retinal"
	$arrCodingExmDoneEye=array();
	$arrCodingExmNotDoneEye=array();	
	$arrCodingExmValsInter=array($levelHistory, $examMedConds, $examVision, $examEe,
						$examLa, $examAssessmentPlan); //$examMedROS, //$examCCHistory, $examGenHealth,
	$arrCodingExmValsComp= array($examCvf,$examEom,$examRv); //$examCvf_or_Ag, $examIop,$examDilation,,$examOphtha, $examOptic,
	
	$levelExamEye="";
	$flag_Amsler_Cvf=0;
	
	$cExamsInter=0;
	    for($i=0;$i<6;$i++){ 
		    if(!empty($arrCodingExmValsInter[$i])) {			
			$cExamsInter =  $cExamsInter + 1;						
			$arrCodingExmDoneEye[] = $arrCodingExmInter[$i];
		    }else{
			if($arrCodingExmInter[$i]=="HPI"){ $cExamsInter =  $cExamsInter + 1; } //AK02-11-2015: Basically just display HPI points required but not meet for Intermediate and Comprehensive codes but do not enforce to do it
			$arrCodingExmNotDoneEye[] = $arrCodingExmInter[$i];
		    }
	    }			

	if($cExamsInter >= 6){				
		$levelExamEye=2; //"Intermediate";
	}else{
		/*
		if( $cExamsInter >=1 ){
			$levelExamEye=1;
		}
		*/
	}
	
	//
	//check if code is coming from front
	$levelExamEye_flg = 0;
	if(isset($_POST["lb_qlfy_lvl_code920"]) && !empty($_POST["lb_qlfy_lvl_code920"]) ){
		$tmp_sel_cd_eye = trim($_POST["lb_qlfy_lvl_code920"]);
		$tmp_sel_cd_eye_lvl = substr($tmp_sel_cd_eye,-1,1);
		if($tmp_sel_cd_eye_lvl>2){
			$levelExamEye_flg = 2;	
		}	
	}
	
	
	if( $levelExamEye == 2 || $levelExamEye_flg == 2){
		
		if($levelExamEye_flg > 2){
			$arrCodingExmDoneEye = array();
			$arrCodingExmNotDoneEye = array();
		}
		
		//Check Comp
		$cExamsComp=0;
		for($i=0;$i<3;$i++){ 			    
			if(!empty($arrCodingExmValsComp[$i])) {					
				//if( array_search($arrCodingExmComp[$i], array("Ophthalmoscopy")) === false ){
					$cExamsComp =  $cExamsComp + 1;			
				//}
				$arrCodingExmDoneEye[] = $arrCodingExmComp[$i];
			}else{
				$arrCodingExmNotDoneEye[] = $arrCodingExmComp[$i];
			}
		}
		
		//As Opthamoscopy is optional, so only 7 procedures make level to Comprehensive.
		if( $cExamsComp >= 3 ){
			$levelExamEye = 4; //"Comprehensive";
		}  
	}
	
	//Set Level of Service:-- 
	$patientLevelofServiceEye = $this->getLevelofService( $reason, $levelExamEye,920 );
	
	
	//check if code is coming from front
	if(isset($_POST["lb_qlfy_lvl_code920"]) && !empty($_POST["lb_qlfy_lvl_code920"]) ){
		
		if($tmp_sel_cd_eye_lvl>1 && $tmp_sel_cd_eye_lvl<=5){
			
			if($patientLevelofServiceEye<$tmp_sel_cd_eye_lvl){
				$nxtLevelOfServiceEye_find = $tmp_sel_cd_eye_lvl;
			}
			
			$patientLevelofServiceEye = $tmp_sel_cd_eye_lvl;
			
		}
	}
	
   
	//GET Level For E/M Code ---		
	//echo "<br>examRv: ".$examRv;
	$arrCodingExm=array("Vision", "CVF", "Pupil", "EOM", "L &amp; A",
						"SLE - Conjunctiva", "SLE - Cornea", "SLE - Anterior Chambers", "SLE - Lenses", 
						"IOP", 
						"Fundus Exams - Optic Nerve", "Fundus Exams - Vitreous;Retinal Exam","Neuro Psych");//Periphery;Blood Vessels;Macula
						//"L&amp;A - Lacrimal Systems",  "Dilation","Ophthalmoscopy", 
	$arrCodingExmDoneEM=array();
	$arrCodingExmNotDoneEM=array();
	$arrCodingExmVals= array($examVision,$examCvf,$examPupil,$examEom,$examLa,
						$examSleConjuctivae,$examSleCornea,$examSleAntChm,$examSle,
						$examIop,
						$examOptic,$examRv,$examNeuroPsych);	//$examLaLacSys,$examDilation,$examOphtha,$examCvf_or_Ag
	
	//Exams EM
	$cExams=0;
	for($i=0;$i<13;$i++){ 			    
		if(!empty($arrCodingExmVals[$i])) {				
			if( array_search($arrCodingExm[$i], array("Neuro Psych")) !== false ){//,"Ophthalmoscopy"				
				$cExams =  $cExams + 2; // Neuro is 2
			}else{
				$cExams =  $cExams + 1; 
			}		
			$arrCodingExmDoneEM[] = $arrCodingExm[$i];
		}else{
			$arrCodingExmNotDoneEM[] = $arrCodingExm[$i];
		}
	}
	
	/*
	//GrossVF 
	$cExams= !empty($examGrossVF) ? $cExams + 1 : $cExams;
	//Fundus
	$cExams= !empty($examFundus) ? $cExams + 1 : $cExams;
	*/
	
	//Level of EM Exam
	$levelExamEM="";
	if($cExams >= 1){		
		if($elem_flgIsPtNew=="Yes_confirmed"){
			// Problem Focused			
			if($cExams >= 14){
				$levelExamEM = 5;//(!empty($examNeuroPsych)) ? 4:3; //"Comprehensive" : "Detailed";	
			}else if(($cExams >= 9)){
				$levelExamEM = 3;
			}else if(( $cExams >= 6 )){
				$levelExamEM = 2;
			}else{
				$levelExamEM = 1; //"Problem focused";
			}
		}else{
			// Problem Focused
			$levelExamEM = 1; //"Problem focused";				
			if($cExams >= 14){ //Comprehensive
				$levelExamEM = 5;//(!empty($examNeuroPsych)) ? 4:3; //"Comprehensive" : "Detailed";	
			}else if(($cExams >= 9)){
				$levelExamEM = 4;
			}else if(($cExams >= 6)){
				$levelExamEM = 3;
			}else if(($cExams >= 1)){
				$levelExamEM = 2;
			}
		}
	}	
	
	## Debugging		
		//echo "Cexm:".$cExams;		
		//echo "<br>";
		//echo "LvlEM: ".$levelExamEM;
		//echo "<br>";
		//print_r($arrCodingExmDoneEM);
		//echo "<br>";
		//print_r($arrCodingExmNotDoneEM);
		//exit;
	## Debugging
	
	//Set Level of Service:-- 
	$patientLevelofServiceEM = $this->getLevelofService($levelHistory,$levelExamEM,992,$levelComplexity,$examMedROS, $levelPFSH, $elem_flgIsPtNew);
	
	//check if code is coming from front
	if(isset($_POST["lb_qlfy_lvl_code992"]) && !empty($_POST["lb_qlfy_lvl_code992"]) ){
		
		$tmp_sel_cd_em = trim($_POST["lb_qlfy_lvl_code992"]);
		$tmp_sel_cd_em_lvl = substr($tmp_sel_cd_em,-1,1);
		
		if($tmp_sel_cd_em_lvl>=1 && $tmp_sel_cd_em_lvl<=5){
			
			//echo "$patientLevelofServiceEM<$tmp_sel_cd_em_lvl,";
			
			if($patientLevelofServiceEM<$tmp_sel_cd_em_lvl){
				$nxtLevelOfServiceEM_find = $tmp_sel_cd_em_lvl;
			}
			
			$patientLevelofServiceEM = $tmp_sel_cd_em_lvl;
			
		}
	}	
	
	//Next Level of Service
	$nxtLevelOfServiceEM = ($patientLevelofServiceEM + 1);
	if(isset($nxtLevelOfServiceEM_find) && !empty($nxtLevelOfServiceEM_find)){
		$nxtLevelOfServiceEM = $nxtLevelOfServiceEM_find;
	}else{	
		if($nxtLevelOfServiceEM<=1){ $nxtLevelOfServiceEM=2; } //start level is 2
	}
	
	//
	//echo $nxtLevelOfServiceEM." - ".$patientLevelofServiceEM." - ".$nxtLevelOfServiceEM_find;
	//exit;
	//
	
	//EM exam to done for next level
	if(in_array("Neuro Psych", $arrCodingExmNotDoneEM) && count($arrCodingExmNotDoneEM)<=2){ $min1=1;  }else{ $min1=0; }
	$emExamMsg4NotDone = "";//Df: $cExams
	if($elem_flgIsPtNew=="Yes_confirmed"){	
		if(($nxtLevelOfServiceEM == "5"||$nxtLevelOfServiceEM == "4")&&$cExams<14){
			$emExamMsg4NotDone = "Exam - ".(14-$cExams-$min1)." elements from below";
		}else if($nxtLevelOfServiceEM == "3"&&$cExams<9){
			$emExamMsg4NotDone = "Exam - ".(9-$cExams-$min1)." elements from below";
		}else if($nxtLevelOfServiceEM == "2"&&$cExams<6){
			$emExamMsg4NotDone = "Exam - ".(6-$cExams-$min1)." elements from below ";
		}
		
	}else{
		if($nxtLevelOfServiceEM == "5"&&$cExams<14){
			$emExamMsg4NotDone = "Exam - ".(14-$cExams-$min1)." elements from below";
		}else if($nxtLevelOfServiceEM == "4"&&$cExams<9){
			$emExamMsg4NotDone = "Exam - ".(9-$cExams-$min1)." elements from below";
		}else if($nxtLevelOfServiceEM == "3"&&$cExams<6){
			$emExamMsg4NotDone = "Exam - ".(6-$cExams-$min1)." elements from below";
		}else if($nxtLevelOfServiceEM == "2"&&$cExams<1){
			$emExamMsg4NotDone = "Exam - 1 to 5 elements from below";
		}
	}	
	
	//History EM
	$emHisNotDone = "";	
	if($elem_flgIsPtNew=="Yes_confirmed"){	
		if( $levelHistory < 3 ){					
			if( ($nxtLevelOfServiceEM == "5") || ($nxtLevelOfServiceEM == "4") || ($nxtLevelOfServiceEM == "3") ){
				if($levelHistory < 3){
					$emHisNotDone = "Select 4 items detailing a complaint in HPI";
				}
			}else{
				if($levelHistory < 2){
					$emHisNotDone = "Select 1 to 3 items detailing a complaint in HPI";
				}
			}
		}
	}else{
		if( $levelHistory < 3 ){					
			if( ($nxtLevelOfServiceEM == "5") || ($nxtLevelOfServiceEM == "4") ){
				if($levelHistory < 3){
					$emHisNotDone = "Select 4 items detailing a complaint in HPI";
				}
			}else{
				if($levelHistory < 2){
					$emHisNotDone = "Select 1 to 3 items detailing a complaint in HPI";
				}
			}
		}
	}	
	
	//Medical Decision Complexity
	$emMedDecCompNotDone = "";
	if($elem_flgIsPtNew=="Yes_confirmed"){	
		if($levelComplexity < 4){
			if(($nxtLevelOfServiceEM == "5")){
				$emMedDecCompNotDone = "Medical Decision making of high complexity";	
			}else if(($nxtLevelOfServiceEM == "4") && ($levelComplexity < 3)){
				$emMedDecCompNotDone = "Medical Decision making of moderate complexity";
			}else if(($nxtLevelOfServiceEM == "3") && ($levelComplexity < 2)){
				$emMedDecCompNotDone = "Medical Decision making of low complexity";
			}else if(($nxtLevelOfServiceEM == "2") && $levelComplexity < 1){
				$emMedDecCompNotDone = "Straight forward medical decision making"; //"Straight forward medical decision making";
			}else if($levelComplexity < 1){
				$emMedDecCompNotDone = "Straight forward medical decision making";
			}
		}
	}else{
		if($levelComplexity < 4){
			if(($nxtLevelOfServiceEM == "5") ){
				$emMedDecCompNotDone = "Medical Decision making of high complexity";	
			}else if(($nxtLevelOfServiceEM == "4") && ($levelComplexity < 3)){
				$emMedDecCompNotDone = "Medical Decision making of moderate complexity";
			}else if(($nxtLevelOfServiceEM == "3") && ($levelComplexity < 2)){
				$emMedDecCompNotDone = "Medical Decision making of low complexity";
			}else if(($nxtLevelOfServiceEM == "2") && $levelComplexity < 1){
				$emMedDecCompNotDone = "Straight forward medical decision making"; //"Straight forward medical decision making";
			}else if($levelComplexity < 1){
				$emMedDecCompNotDone = "Straight forward medical decision making";
			}
		}
	}
	
	//pfsh
	$emPFSHNotDone = "";
	if($elem_flgIsPtNew=="Yes_confirmed"){	
		if(empty($levelPFSH) && $nxtLevelOfServiceEM > "2"){
			$emPFSHNotDone = "PFSH";
		}
	}else{
		if(empty($levelPFSH) && $nxtLevelOfServiceEM > "3"){
			$emPFSHNotDone = "PFSH";
		}
	}
	
	//ROS
	$emROSNotDone = "";
	if($elem_flgIsPtNew=="Yes_confirmed"){	
		if(($nxtLevelOfServiceEM == "2") && $examMedROS<1){
			$emROSNotDone = "ROS - 1 element";
		}else if(($nxtLevelOfServiceEM == "3") && $examMedROS<2){
			$emROSNotDone = "ROS - 2-9 elements";
		}else if((($nxtLevelOfServiceEM == "4")||($nxtLevelOfServiceEM == "5")) && $examMedROS<10){
			$emROSNotDone = "ROS - 10 elements";
		}
	}else{
		if(($nxtLevelOfServiceEM == "2")){
			//$emROSNotDone = "ROS - 0 element";
		}else if(($nxtLevelOfServiceEM == "3") && $examMedROS<1){
			$emROSNotDone = "ROS - 1 or more elements";
		}else if(($nxtLevelOfServiceEM == "4") && $examMedROS<2){
			$emROSNotDone = "ROS - 2 or more elements";
		}else if((($nxtLevelOfServiceEM == "5")) && $examMedROS<10){
			$emROSNotDone = "ROS - 10 elements";
		}
	}	
	
	// if Eye Code
	$levelExam = "";		
	if( $practiceBillCode == "920" ){
		$levelExam = $levelExamEye;
		$arrCodingExmDone=$arrCodingExmDoneEye;
		$arrCodingExmNotDone=$arrCodingExmNotDoneEye;
		$patientLevelofService = $patientLevelofServiceEye;
		
	}else if( $practiceBillCode == "992" ){ //E/M
		$levelExam = $levelExamEM;
		$arrCodingExmDone=$arrCodingExmDoneEM;
		$arrCodingExmNotDone=$arrCodingExmNotDoneEM;
		$patientLevelofService = $patientLevelofServiceEM;
		
		
		
	}
   
   //test

	//make strings from array
	$strExamDone  = (count($arrCodingExmDone) > 0) ? implode(",", $arrCodingExmDone) : "";
	$strExamNotDone = (count($arrCodingExmNotDone) > 0) ? implode(",", $arrCodingExmNotDone) : "";
	$strExamDone_eye = (count($arrCodingExmDoneEye) > 0) ? implode(",", $arrCodingExmDoneEye) : "";
	$strExamNotDone_eye = (count($arrCodingExmNotDoneEye) > 0) ? implode(",", $arrCodingExmNotDoneEye) : "";
	$strExamDone_em = (count($arrCodingExmDoneEM) > 0) ? implode(",", $arrCodingExmDoneEM) : "";
	$strExamNotDone_em = (count($arrCodingExmNotDoneEM) > 0 && !empty($emExamMsg4NotDone) ) ? implode(",", $arrCodingExmNotDoneEM) : "";		//
	
	//$strExamNotDone_em="(Done exam ".$cExams." :-),"."[$levelHistory,$levelExamEM,992,$levelComplexity,$examMedROS, $levelPFSH, $elem_flgIsPtNew,$patientLevelofServiceEM]".$strExamNotDone_em;
	//$strExamNotDone_eye = "[".$levelExamEye." - ".$cExamsComp." - ".$patientLevelofServiceEye."]".$strExamNotDone_eye;	
	//exam
	if(!empty($emExamMsg4NotDone)){  
		$strExamNotDone_em = $emExamMsg4NotDone.",".$strExamNotDone_em;
	}
	
	//Medical Decision Complexity
	if(!empty($emMedDecCompNotDone)){
		$strExamNotDone_em = $emMedDecCompNotDone.",".$strExamNotDone_em;
	}
	
	//PFSH
	if(!empty($emPFSHNotDone)){
		$strExamNotDone_em = $emPFSHNotDone.",".$strExamNotDone_em;
	}
	
	//ROS
	if(!empty($emROSNotDone)){
		$strExamNotDone_em = $emROSNotDone.",".$strExamNotDone_em;
	}
	//History
	if(!empty($emHisNotDone)){
		$strExamNotDone_em = $emHisNotDone.",".$strExamNotDone_em;
	}
	
		

?>