<?php
	function getPtGenHealthInfo($pid){		
		$retVal = "";
		if(!empty($pid)){
			$retVal = array();			
			/*$sql = "select you_wear,any_conditions_you,chronicDesc,eye_problems,any_conditions_others_you,eye_problems_other, ".
				 "any_conditions_relative, chronicRelative, OtherDesc, any_conditions_other_relative  ".
				 "from ocular where patient_id='".$pid."' ";				 
			$row = sqlQuery($sql);
			if($row != false){
				$retVal["CLens"] = (($row["you_wear"]=="2") || ($row["you_wear"]=="3")) ? "Contact Lenses" : ""; // 2 or 3
				$chronicDesc = $row["chronicDesc"];
				$acuteProbs = $row["eye_problems"];
				$acuteOther = $row["eye_problems_other"];
				$chronicProbs = $row["any_conditions_you"];
				$any_conditions_others_you = $row["any_conditions_others_you"];				
				$chronicRel = $row["chronicRelative"];
				$any_cond_rel = $row["any_conditions_relative"];				
				$acor = $row["any_conditions_other_relative"];
				$acoy_desc = $row["OtherDesc"];
				
				
				//Acute
				$arrPtActCond = array();
				$arrActCond = array("Blurred or Poor Vision","Poor Night Vision","Gritty Sensation","Trouble Reading Signs","Glare From Lights",
								"Tearing", "Poor Depth Perception", "Halos Around Lights", "Itching or Burning", 
								"Trouble Identifying Colors", "See Spots or Floaters","Eye Pain","Double Vision","See Light Flashes",
								"Redness or Bloodshot");
				$eye_problems_arr=explode(" ",trim(str_replace(","," ",$acuteProbs)));
				
				if( count($eye_problems_arr) > 0 ){					
					$arrPtActCond = array();	
					foreach($eye_problems_arr as $keyTmp => $valTmp){
						if(!empty($arrActCond[$valTmp-1])){
							$arrPtActCond[]= $arrActCond[$valTmp-1];
						}
					}
				}
				if(!empty($acuteOther)){
					$arrPtActCond[]= $acuteOther;				
				}				
				//
				$retVal["ActCond"] = $arrPtActCond;
				
				
				//desc
				$strSep="~!!~~";
				$strSep2=":*:";
				$strDesc = $chronicDesc;				
				$arrDesc = array();
				
				if(!empty($strDesc)){
					$arrDescTmp = explode($strSep, $strDesc);
					if(count($arrDescTmp) > 0){
						foreach($arrDescTmp as $key => $val){
							$arrTmp = explode($strSep2,$val);
							$arrDesc[$arrTmp[0]] = $arrTmp[1];							
						}
					}				
				}
				//chronic relative
				$arrChronicRel = array();
				if(!empty($chronicRel)){
					$arrChronicTmp = explode($strSep, $chronicRel);
					if(count($arrChronicTmp) > 0){
						foreach($arrChronicTmp as $key => $val){
							$arrTmp = explode($strSep2,$val);
							$arrChronicRel[$arrTmp[0]] = $arrTmp[1];
						}
					}
				}
				
				//chronic
				$arrPtChroCond = array();
				$arrChroCond = array("Dry Eyes","Macula Degeneration","Glaucoma","Retinal Detachment","Cataracts" );
				$any_conditions_you_arr = explode(" ",trim(str_replace(","," ",$chronicProbs)));
				
				$any_cond_rel_arr = explode(" ",trim(str_replace(","," ",$any_cond_rel)));				
				
				if( count($any_conditions_you_arr) > 0 ){						
					foreach($any_conditions_you_arr as $keyTmp => $valTmp){
						if(!empty($arrChroCond[$valTmp-1])){
							$relTmp = "";
							if(in_array($valTmp, $any_cond_rel_arr)){
								$relTmp = (!empty($arrChronicRel[$valTmp])) ? " (".$arrChronicRel[$valTmp].") " : " (Relative) ";
							}							
							$strTmp = "";
							$strTmp .= $arrChroCond[$valTmp-1];
							$strTmp .= (!empty($relTmp)) ? $relTmp : "";
							if(!empty($arrDesc[$valTmp])){
								$strTmp .= ((!empty($relTmp))) ? $arrDesc[$valTmp] : " - ".$arrDesc[$valTmp];
							}
							$arrPtChroCond[] = $strTmp;
						}
					}
				}
				
				if( count($any_cond_rel_arr) > 0 ){
					foreach($any_cond_rel_arr as $keyTmp => $valTmp){
						if((!in_array($valTmp, $any_conditions_you_arr)) && !empty($arrChroCond[$valTmp-1])){
							$strTmp = "";
							$strTmp .= $arrChroCond[$valTmp-1];
							$strTmp .= (!empty($arrChronicRel[$valTmp])) ? " (".$arrChronicRel[$valTmp].") " : " (Relative) ";							
							$arrPtChroCond[] = $strTmp;
						}
					}
				}
				
				if((!empty($any_conditions_others_you) || (!empty($acoy_desc))) && !empty($acoy_desc)){
					$strTmp = "";
					$strTmp .= $acoy_desc;
					
					if((!empty($acor))){
						$strTmp .= ( !empty($arrChronicRel["other"]) ) ? " (".$arrChronicRel["other"].") " : " (Relative) ";
					}
					if(!empty($arrDesc["other"])){
						$strTmp .=  ((!empty($acor))) ? $arrDesc["other"] : " - ".$arrDesc["other"];
					}
					$arrPtChroCond[]= $strTmp;
				}
				//
				$retVal["ChroCond"] = $arrPtChroCond;				
			}			
			*/
			$sql = "select any_conditions_you, chk_annual_colorectal_cancer_screenings, chk_receiving_annual_mammogram, chk_received_flu_vaccine, chk_high_risk_for_cardiac, 
						sub_conditions_you, any_conditions_others_both, any_conditions_others, any_conditions_others, diabetes_values, chk_under_control, desc_r, relDescHighBp, 
						relDescStrokeProb, relDescHeartProb, relDescLungProb, relDescThyroidProb, relDescArthritisProb, relDescUlcersProb, relDescCancerProb, relDescLDL, 
						ghRelDescOthers, desc_u, desc_high_bp, desc_arthrities, desc_lung_problem, desc_stroke, desc_thyroid_problems, desc_ulcers, desc_cancer, desc_heart_problem,
						desc_LDL, any_conditions_others, genMedComments, any_conditions_others_both, review_const, review_head, review_resp, review_card, review_gastro, review_genit,
						review_aller, review_neuro, negChkBx, review_const_others, review_head_others, review_resp_others, review_card_others, review_gastro_others	, review_genit_others,
						review_aller_others, review_neuro_others, review_sys	
						from general_medicine where patient_id='".$pid."' ";
			$row = sqlQuery($sql);
			if($row != false){
				//Any Condition
				$arrPtAnyCond = array();
				$arrAnyCond = array("High Blood Pressure","Heart Problem","Diabetes","Lung Problems","Stroke","Thyroid Problems","Arthritis", "Ulcers", "", "", "", "", "LDL", "Cancer" );
				//Patient
				$any_conditions_u1_arr=explode(" ",trim(str_replace(","," ",$row["any_conditions_you"])));		
				
				//Relative
				//$any_conditions_ralative1_arr=explode(" ",trim(str_replace(","," ",$row["any_conditions_relative"])));				
				//$arrTmp = array("You"=>$any_conditions_u1_arr,"Relatives"=>$any_conditions_ralative1_arr);
				
				$arrTmp = array("You"=>$any_conditions_u1_arr);
				foreach( $arrTmp as $key => $val ){
					$tmp = $val;				
					if( count($tmp) > 0 ){					
						$arrPtAnyCond[$key]=array();	
						foreach($tmp as $keyTmp => $valTmp){
							if(!empty($arrAnyCond[$valTmp-1])){
								
								//if($arrAnyCond[$valTmp-1] == "Diabetes"){
								//	$elem_desc_u = !empty($row["desc_u"]) ? " - ".$row["desc_u"] : "" ;
								//	$arrPtAnyCond[$key][]= "<font color=\"red\">".$arrAnyCond[$valTmp-1].$elem_desc_u."</font>";
								//}else{
									$arrPtAnyCond[$key][]=$arrAnyCond[$valTmp-1];
								///}
							}
						}
					}
				}
				
							
				if($row["chk_annual_colorectal_cancer_screenings"]==1){
				  $strAnnual .= "Annual colorectal cancer screenings,";
				}
				
				if($row["chk_receiving_annual_mammogram"]==1){
				  $strAnnual .= "Receiving annual mammogram,";
				}
				
				if($row["chk_received_flu_vaccine"]==1){
				  $strAnnual .= "Received flu vaccine,";
				}
				
				if($row["chk_high_risk_for_cardiac"]==1){
				  $strAnnual .= "High-risk for cardiac events on aspirin prophylaxis";
				}
				$retVal["str_annaual"] = $strAnnual;
				//Sub Conditions
				$arrSubConditions=array();
				$arrSbConText=array("7.1"=>"RA","7.2"=>"OA");
				$elem_subCondition_pat_val = get_set_pat_rel_values_retrive($row["sub_conditions_you"],'pat',"~|~");
				
				$arr_sub_condition_you = explode(",", $elem_subCondition_pat_val);
				$lenSCds = count($arr_sub_condition_you);
				for($i=0;$i<$lenSCds;$i++){
					$arrSubConditions["Arthritis"][]=$arrSbConText[$arr_sub_condition_you[$i]];
				}
				
				$retVal["SubCond"]=$arrSubConditions;
				
				//Other Condition 				
				$any_conditions_others_both_arr=explode(" ",trim(str_replace(","," ",$row["any_conditions_others_both"])));
				$strOthersTxtPat = get_set_pat_rel_values_retrive($row["any_conditions_others"],"pat","~|~");	
				$otherCondition = $strOthersTxtPat;
				if(!empty($otherCondition)){					
					foreach( $any_conditions_others_both_arr as $key => $val ){						
						if($val == "1"){
							$arrPtAnyCond["You"][]= $otherCondition;
							$retVal["patient_other"]= $row["any_conditions_others"];
						}
						if($val == "2")
						{
							$arrPtAnyCond["Relatives"][]= $otherCondition;
						}
					}
				}
				
				$retVal["AnyCond"]= $arrPtAnyCond;
				$strDiabetesIdTxtPat =  get_set_pat_rel_values_retrive($row["diabetes_values"],'pat',"~|~"); 
				$retVal["diabetes_values"]= $strDiabetesIdTxtPat;
				$arrChkUnderControl = explode(',',$row["chk_under_control"]);
				$retVal["chkUnderControl"]= $arrChkUnderControl;
				$retVal["desc_r"]= $row["desc_r"];
				$retVal["relDescHighBp"]= $row["relDescHighBp"];
				$retVal["relDescStrokeProb"]= $row["relDescStrokeProb"];
				$retVal["relDescHeartProb"]= $row["relDescHeartProb"];
				$retVal["relDescLungProb"]= $row["relDescLungProb"];
				$retVal["relDescThyroidProb"]= $row["relDescThyroidProb"];
				$retVal["relDescArthritisProb"]= $row["relDescArthritisProb"];
				$retVal["relDescUlcersProb"]= $row["relDescUlcersProb"];
				$retVal["relDescCancerProb"]= $row["relDescCancerProb"];
				$retVal["relDescLDL"]= $row["relDescLDL"];
				$retVal["ghRelDescOthers"]= $row["ghRelDescOthers"];
				$strDiabetesTxtPat = get_set_pat_rel_values_retrive($row["desc_u"],"pat","~|~");
				$retVal["desc_u"]= $strDiabetesTxtPat;
				$strHighBPTxtPat = get_set_pat_rel_values_retrive($row["desc_high_bp"],"pat","~|~");
				$retVal["desc_high_bp"]= $strHighBPTxtPat;
				$strArthritiesTxtPat = get_set_pat_rel_values_retrive($row["desc_arthrities"],"pat","~|~");
				$retVal["desc_arthrities"]= $strArthritiesTxtPat;
				$strLungProblemTxtPat = get_set_pat_rel_values_retrive($row["desc_lung_problem"],"pat","~|~");
				$retVal["desc_lung_problem"]= $strLungProblemTxtPat;
				$strStrokeTxtPat = get_set_pat_rel_values_retrive($row["desc_stroke"],"pat","~|~");
				$retVal["desc_stroke"]= $strStrokeTxtPat;
				$strThyroidProbTxtPat = get_set_pat_rel_values_retrive($row["desc_thyroid_problems"],"pat","~|~");
				$retVal["desc_thyroid_problems"]= $strThyroidProbTxtPat;
				$strUclearTxtPat = get_set_pat_rel_values_retrive($row["desc_ulcers"],"pat","~|~");
				$retVal["desc_ulcers"]= $strUclearTxtPat;
				$strCancerTxtPat = get_set_pat_rel_values_retrive($row["desc_cancer"],"pat","~|~");
				$retVal["desc_cancer"]= $strCancerTxtPat;
				$strHeartProbTxtPat = get_set_pat_rel_values_retrive($row["desc_heart_problem"],"pat","~|~");
				$retVal["desc_heart_problem"]= $strHeartProbTxtPat;
				$strLDLTxtPat = get_set_pat_rel_values_retrive($row["desc_LDL"],"pat","~|~");
				$retVal["desc_LDL"]= $strLDLTxtPat;
				$strOthersTxtPat = get_set_pat_rel_values_retrive($row["any_conditions_others"],"pat","~|~");
				$retVal["any_conditions_others"]= $strOthersTxtPat;
				$retVal["genMedComments"]= $row["genMedComments"]; 
				$retVal["Other_case"]= $row["any_conditions_others_both"]; 
				//Review Of System				
				$review_const_arr=explode(" ",trim(str_replace(","," ",$row["review_const"])));
				$review_head_arr=explode(" ",trim(str_replace(","," ",$row["review_head"])));
				$review_resp_arr=explode(" ",trim(str_replace(","," ",$row["review_resp"])));
				$review_card_arr=explode(" ",trim(str_replace(","," ",$row["review_card"])));
				$review_gastro_arr=explode(" ",trim(str_replace(","," ",$row["review_gastro"])));
				$review_genit_arr=explode(" ",trim(str_replace(","," ",$row["review_genit"])));
				$review_aller_arr=explode(" ",trim(str_replace(","," ",$row["review_aller"])));
				$review_neuro_arr=explode(" ",trim(str_replace(","," ",$row["review_neuro"])));
				$negChkBxArr = explode(',',$row["negChkBx"]);
				
				$arrROS = array("Constitutional" => array("arr"=>$review_const_arr,"arrNames"=>array("Fever","Weight Loss","Rash", "Skin Disease", "Fatigue"), "Other"=>$row["review_const_others"]),
						        "Ear, Nose, Mouth & Throat" => array("arr"=>$review_head_arr,"arrNames"=>array("Sinus Infection", "Post Nasal Drips", "Runny Nose","Dry Mouth","Deafness"), "Other"=>$row["review_head_others"]),
							"Respiratory" => array("arr"=>$review_resp_arr,"arrNames"=>array("Cough","Bronchitis","Shortness of Breath","Asthma","Emphysema","COPD","TB"), "Other"=>$row["review_resp_others"]),
							"Cardiovascular" => array("arr"=>$review_card_arr,"arrNames"=>array("Chest Pain","Congestive Heart Failure","Irregular Heart beat","Shortness of Breath","High Blood Pressure", "Low Blood Pressure", "Pacemaker/defibrillator"), "Other"=>$row["review_card_others"]),
							"Gastrointestinal" => array("arr"=>$review_gastro_arr,"arrNames"=>array("Vomiting","Ulcers","Diarrhea","Bloody Stools","Hepatitis","Jaundice","Constipation"), "Other"=>$row["review_gastro_others"]),
							"Genitourinary" => array("arr"=>$review_genit_arr,"arrNames"=>array("Genital Ulcers","Discharge","Kidney Stones","Blood in Urine"), "Other"=>$row["review_genit_others"]),
							"Allergic/Immunologic" => array("arr"=>$review_aller_arr,"arrNames"=>array("Seasonal Allergies","Hay Fever"), "Other"=>$row["review_aller_others"]),
							"Neurological" => array("arr"=>$review_neuro_arr,"arrNames"=>array("Headache","Migraines","Paralysis Fever","Joint Ache","Seizures","Numbness","Faints","Stroke","Multiple Sclerosis","Alzheimer's Disease","Parkinson's Disease","Dementia"), "Other"=>$row["review_neuro_others"]) ,
							"negChkBx" => array("arr"=>$negChkBxArr,"arrNames"=>array("Constitutional","Ear, Nose, Mouth & Throat","Respiratory","Cardiovascular","Gastrointestinal","Genitourinary","Allergic/Immunologic","Neurological","Integumentary","Psychiatry","Hemotologic/Lymphatic","Musculoskeletal","Endocrine", "Eyes")) 
							);
				//ros --
				$review_sys = $row["review_sys"];
				if(!empty($review_sys)){
					$ar_review_sys = json_decode($review_sys, true);
					$ar_tmp = array('Integumentary'=>array('review_intgmntr', array("Rashes", "Wounds", "Breast Lumps","Eczema","Dermatitis")),	
								'Psychiatry'=>array('review_psychiatry', array("Depression", "Anxiety", "Paranoia", "Sleep Patterns","Mental and/or emotional factors", "Alzheimer's Disease", "Parkinson's disease","Memory Loss")), 
								'Hemotologic/Lymphatic'=>array('review_blood_lymph', array("Anemia", "Blood Transfusions", "Excessive Bleeding", "Purpura", "Infection")),
								'Musculoskeletal'=>array('review_musculoskeletal', array("Pain", "Joint Ache", "Stiffness", "Swelling","Paralysis Fever")),
								'Endocrine'=>array('review_endocrine', array("Mood Swings", "Constipation", "Polydipsia","Hypothyroidism","Hyperthyroidism")),
								'Eyes'=>array('review_eye', array("Vision loss", "Eye pain", "Double vision", "Headache")));
					foreach($ar_tmp as $k => $arv){
						$v = $arv[0];
						$tmpar = array();
						if(isset($ar_review_sys[$v])){
							$artmp = explode(" ",trim(str_replace(","," ",$ar_review_sys[$v])));
							$tmpar["arr"] = $artmp;
							$tmpar["arrNames"] = $arv[1];
						}
						//
						$vother = $v."_others";
						if(isset($ar_review_sys[$vother])){				
							$tmpar["Other"] = $ar_review_sys[$vother];
						}
						
						if(count($tmpar)){
							$arrROS[$k] = $tmpar;
						}
					}			
				}				
				$arrPtROS=array();
				foreach($arrROS as $key => $val){
					$tmp = $val["arr"];
					$tmpName = $val["arrNames"];
					$otherTmp = $val["Other"];
					
					$arrPtROS[$key]=array();
					if( count($tmp) > 0 ){
						foreach($tmp as $keyTmp => $valTmp){
							if(!empty($tmpName[$valTmp-1])){
								$arrPtROS[$key][]=$tmpName[$valTmp-1];
							}
						}
					}
					
					if(!empty($otherTmp)){
						$arrPtROS[$key][]=$otherTmp;
					}
				}
				
				ksort($arrPtROS);

				$retVal["ROS"]= $arrPtROS;
			}
		}
		return (empty($retVal)) ? false: $retVal;
	}	
	
	if(!function_exists('getPtGenHealthOcularInfo')){
		function getPtGenHealthOcularInfo($pid,$arrLogOc=array()){
			$retVal = "";
			if(!empty($pid)){
				$retVal = array();			
				
				//Get From Achive --
				$flgArc=0;
				if(count($arrLogOc) > 0){ 
					$flgArc=$arrLogOc[0];
					$arr=$arrLogOc[1];
				}
				//Get From Achive --
				
				//Check if previous data exists, else use current data
				if($flgArc==0){
				$sql = "select you_wear,any_conditions_you,chronicDesc,eye_problems,any_conditions_others_you,eye_problems_other, ".
					 "any_conditions_relative, chronicRelative, OtherDesc, any_conditions_other_relative  ".
					 "from ocular where patient_id='".$pid."' ";				 
				$row = sqlQuery($sql);
				}else{
					$row = $arr;
				}
				
				if($row != false){
					switch($row["you_wear"]){
						case "0":
						$retVal["CLens"] = "None";
						break;
						
						case "1":
						$retVal["CLens"] = "Glasses";
						break;
						
						case "2":
						$retVal["CLens"] = "Contact Lenses";
						break;
					
						case "3":
						$retVal["CLens"] = "Glasses And Contact Lenses";
						break;	
					}
					//$retVal["CLens"] = (($row["you_wear"]=="2") || ($row["you_wear"]=="3")) ? "Contact Lenses" : ""; // 2 or 3
					$chronicDesc = get_set_pat_rel_values_retrive($row["chronicDesc"],"pat","~|~");
					$chronicDescRel = get_set_pat_rel_values_retrive($row["chronicDesc"],"rel","~|~");			
					
					$acuteProbs = $row["eye_problems"];
					$acuteOther = $row["eye_problems_other"];
					
					$strAnyConditionsYou = $row["any_conditions_you"];
					$strAnyConditionsYou = get_set_pat_rel_values_retrive($strAnyConditionsYou,"pat","~|~");
					$chronicProbs = $strAnyConditionsYou;
					
					//$chronicProbs = $row["any_conditions_you"];
					$any_conditions_others_you = $row["any_conditions_others_you"];				
					$chronicRel = $row["chronicRelative"];
					$any_cond_rel = $row["any_conditions_relative"];				
					$acor = $row["any_conditions_other_relative"];
					$strOtherDesc = get_set_pat_rel_values_retrive($row["OtherDesc"],"pat","~|~");
					//$acoy_desc = $row["OtherDesc"];
					$acoy_desc = $strOtherDesc;
					
					$strOtherDescRel = get_set_pat_rel_values_retrive($row["OtherDesc"],"rel","~|~");
					
					/*//Acute
					$arrPtActCond = array();
					$arrActCond = array("Blurred or Poor Vision","Poor Night Vision","Gritty Sensation","Trouble Reading Signs","Glare From Lights",
									"Tearing", "Poor Depth Perception", "Halos Around Lights", "Itching or Burning", 
									"Trouble Identifying Colors", "See Spots or Floaters","Eye Pain","Double Vision","See Light Flashes",
									"Redness or Bloodshot");
					$eye_problems_arr=explode(" ",trim(str_replace(","," ",$acuteProbs)));
					
					if( count($eye_problems_arr) > 0 ){					
						$arrPtActCond = array();	
						foreach($eye_problems_arr as $keyTmp => $valTmp){
							if(!empty($arrActCond[$valTmp-1])){
								$arrPtActCond[]= $arrActCond[$valTmp-1];
							}
						}
					}
					if(!empty($acuteOther)){
						$arrPtActCond[]= $acuteOther;				
					}				
					//
					$retVal["ActCond"] = $arrPtActCond;
					*/
					
					//desc
					$strSep="~!!~~";
					$strSep2=":*:";
					$strDesc = $chronicDesc;				
					$arrDesc = array();
					
					if(!empty($strDesc)){
						$arrDescTmp = explode($strSep, $strDesc);
						if(count($arrDescTmp) > 0){
							foreach($arrDescTmp as $key => $val){
								$arrTmp = explode($strSep2,$val);
								$arrDesc[$arrTmp[0]] = $arrTmp[1];							
							}
						}				
					}
					
					//print_r($arrDesc);
					
					$strSepRel="~!!~~";
					$strSep2Rel=":*:";
					$strDescRel = $chronicDescRel;				
					$arrDescRel = array();
					
					if(!empty($strDescRel)){
						$arrDescTmpRel = explode($strSepRel, $strDescRel);
						if(count($arrDescTmpRel) > 0){
							foreach($arrDescTmpRel as $keyRel => $valRel){
								$arrTmpRel = explode($strSep2Rel,$valRel);
								$arrDescRel[$arrTmpRel[0]] = $arrTmpRel[1];							
							}
						}				
					}
					
					//pre($arrDescRel);
					
					//chronic relative
					$arrChronicRel = array();
					if(!empty($chronicRel)){
						$arrChronicTmp = explode($strSep, $chronicRel);
						if(count($arrChronicTmp) > 0){
							foreach($arrChronicTmp as $key => $val){
								$arrTmp = explode($strSep2,$val);
								$arrChronicRel[$arrTmp[0]] = $arrTmp[1];
							}
						}
					}
					//pre($arrChronicRel);
					//chronic
					$arrPtChroCond = $arrRelChroCond = array();
					$arrChroCond = array("Dry Eyes","Macular Degeneration","Glaucoma","Retinal Detachment","Cataracts","Keratoconus");
					$any_conditions_you_arr = explode(" ",trim(str_replace(","," ",$chronicProbs)));				
					$any_cond_rel_arr = explode(" ",trim(str_replace(","," ",$any_cond_rel)));
					if( count($any_conditions_you_arr) > 0 ){						
						foreach($any_conditions_you_arr as $keyTmp => $valTmp){
							if(!empty($arrChroCond[$valTmp-1])){
								$relTmp = "";
								/*if(in_array($valTmp, $any_cond_rel_arr)){
									$relTmp = (!empty($arrChronicRel[$valTmp])) ? " (".$arrChronicRel[$valTmp].") " : " (Relative) ";
								}
								*/							
								$strTmp = "";
								$strTmp .= $arrChroCond[$valTmp-1];
								//$strTmp .= (!empty($relTmp)) ? $relTmp : "";
								if(!empty($arrDesc[$valTmp])){
									$strTmp .= ((!empty($relTmp))) ? $arrDesc[$valTmp] : " - ".html_entity_decode($arrDesc[$valTmp]);
								}
								$arrPtChroCond[] = $strTmp;
							}
						}
					}
					
					if( count($any_cond_rel_arr) > 0 ){
						foreach($any_cond_rel_arr as $keyTmp => $valTmp){
							if(!empty($arrChroCond[$valTmp-1])){
								$strTmp = "";
								$strTmp .= $arrChroCond[$valTmp-1];
								//$strTmp .= (!empty($arrChronicRel[$valTmp])) ? " (".$arrChronicRel[$valTmp].") " : " (Relative) ";							
								//$strTmp .= (!empty($arrDescRel[$valTmp])) ? " - ".$arrDescRel[$valTmp]."" : " (Relative) ";
								$strTmp .= (!empty($arrChronicRel[$valTmp])) ? " (".$arrChronicRel[$valTmp].") " : "";
								$strTmp .= (!empty($arrDescRel[$valTmp])) ? " - ".$arrDescRel[$valTmp]."" : "";							
								$arrRelChroCond[] = $strTmp;
							}
						}
					}
					//pre($arrRelChroCond);
					if(!empty($any_conditions_others_you) || !empty($acoy_desc)){
						$strTmp = "";
						$strTmp .= $acoy_desc;
						
						/*if((!empty($acor))){
							$strTmp .= ( !empty($arrChronicRel["other"]) ) ? " (".$arrChronicRel["other"].") " : " (Relative) ";
						}
						*/
						if(!empty($arrDesc["other"])){
							$strTmp .=  ((!empty($acor))) ? $arrDesc["other"] : " - ".html_entity_decode($arrDesc["other"]);
						}
						$arrPtChroCond[]= $strTmp;
					}				
					
					if(!empty($strOtherDescRel) || !empty($arrChronicRel["other"])||!empty($arrDescRel["other"])){
						$strRelTmp = "";
						//$strRelTmp .= !empty($strOtherDescRel) ? $strOtherDescRel : "Others" ;
						//$strRelTmp .= ( !empty($arrChronicRel["other"]) ) ? " (".$arrChronicRel["other"].") " : " (Relative) ";
						//if(!empty($arrDescRel["other"])){ $strRelTmp .=  " - ".$arrDescRel["other"]; }
						$strRelTmp .= ( !empty($arrChronicRel["other"]) ) ? " (".$arrChronicRel["other"].") " : "";
						if(!empty($arrChronicRel["other"]) && !empty($arrDescRel["other"])){ $strRelTmp .=  " - "; }
						if(!empty($arrDescRel["other"])){ $strRelTmp .=  $arrDescRel["other"]; }
						$arrRelChroCond[]= $strRelTmp;
					}	
					
					//
					$retVal["ChroCond"] = $arrPtChroCond;				
					$retVal["ChroCondRel"] = $arrRelChroCond;				
				}			
				
			}
			return (empty($retVal)) ? false: $retVal;
		
		}
	}
	
	if(!function_exists('getPtLExamInfo')){
		function getPtLExamInfo($pid,$fid=""){
			if(!empty($fid)){
				$strFid = "  AND formid='".$fid."' ";		
			}
			
			$tmp = "";
			$qry = "select operator_id,date_format(created_date,'".getSqlDateFormat('','y')." %h:%i %p') as createdDate
				  from patient_last_examined where patient_id = '$pid' ".$strFid."
				  order by patient_last_examined_id desc limit 0,1";
			$row = sqlQuery($qry);
			if($row != false){
				$operator_id = $row["operator_id"];
				$createdDate = $row['createdDate'];
			}
			
			if(!empty($operator_id)){
				$qry = "select concat(substr(fname from 1 for 1),'',
					  substr(lname from 1 for 1)) as name from users where id = '$operator_id'";
				$row = sqlQuery($qry);
				if($row != false){
					$phyDetail = $row["name"];
				}
				$tmp = " ".$createdDate." ".$phyDetail;	
			}
			return $tmp;
		}
	}
	
	
	function getPtOcularInfo($pid){
		$qry = "SELECT you_wear, eye_problems, any_conditions_you, any_conditions_relative, eye_problems_other, OtherDesc, chronicDesc from ocular where patient_id = '$pid'";
		$row = sqlQuery($qry);
		$retArr = array();
		$arrEyeHistory = array('None','Glasses','Contact Lenses','Glasses And Contact Lenses');
		
		$eyeProblems = array('Blurred or Poor Vision','Poor Night Vision','Gritty Sensation','Trouble Reading Signs',
							   'Glare From Lights','Tearing','Poor Depth Perception','Halos Around Lights','Itching or Burning',
							   'Trouble Identifying Colors','See Spots or Floaters','Eye Pain','Double Vision','See Light Flashes',
							   'Redness or Bloodshot','Others');
							   
		$arrCondition = array('Dry Eyes','Macula Degeneration','Glaucoma','Retinal Detachment','Cataracts', 'Keratoconus');
		
		
		$retArr['eye_history'] = $arrEyeHistory[$row['you_wear']];
		
		$arrEyeProb = explode(',',$row['eye_problems']);		

		$strAnyConditionsYou = $row["any_conditions_you"];
		$strAnyConditionsYou = get_set_pat_rel_values_retrive($strAnyConditionsYou,"pat","~|~");

		$arrYou = explode(',',$strAnyConditionsYou);
		
		$arrRelative = explode(',',$row['any_conditions_relative']);
		
		//===========GET OCULAR MED HX BLOOD RELATIVE DATA===================================
		$arrBldRel = $row['any_conditions_relative'];
		$strAnyConditionsRelative = get_set_pat_rel_values_retrive($arrBldRel,"pat","~|~");
		$arrRel = explode(',',$strAnyConditionsRelative);
		$arrBloodRel = $arrRel;
		$arrBloodRel = array_unique($arrBloodRel);	//	Removes duplicate values from an array
		sort($arrBloodRel);
		//==================================================================================
		
		//$arrYouRel = array_merge($arrYou,$arrRelative);	//	merge relative array into arrYou
		//this is done because of sepration of ocular and family hx tab
		$arrYouRel = $arrYou;
		$arrYouRel = array_unique($arrYouRel);			//	Removes duplicate values from an array
		sort($arrYouRel);
		$k = 0;
		
		if(count($arrEyeProb) > 0){	
			for($i = 0; $i<count($arrEyeProb); $i++)
			{
				$val = (int)$arrEyeProb[$i];
				if($val > 0) {
					$val = $val - 1;
				}
				$retArr['eye_problem'][$i] = $eyeProblems[$val];
			}
		}
//print_r($arrYouRel);
		for($j = 0; $j<count($arrYouRel); $j++)
		{
			
			if($arrYouRel[$j] != ''){
				$arrVal = (int)$arrYouRel[$j];
				$arrVal = $arrVal - 1;
				$retArr['you_rel'][$k] = $arrCondition[$arrVal];
				$k++;
			}
		}
		//========GET OCULAR MED HX BLOOD RELATIVE DATA=======
		for($l = 0; $l<count($arrBloodRel); $l++)
		{
			if($arrBloodRel[$l] != ''){
				$arrValRel = (int)$arrBloodRel[$l];
				$arrValRel = $arrValRel - 1;
				$retArr['blood_rel'][$k] = $arrCondition[$arrValRel];
				$k++;
			}
		}
		//====================================================
		$retArr["eye_problems_other"] = $row["eye_problems_other"];
		
		$strOtherDesc = $row["OtherDesc"];
		$strOtherDesc = get_set_pat_rel_values_retrive($strOtherDesc,"pat","~|~");		
		$retArr["OtherDesc"] = $strOtherDesc;		
		
		// desc --
		$delimiter = '~|~';
		$strSep="~!!~~";
		$strSep2=":*:";
		$strDesc = $row["chronicDesc"];
		
		//========EXPLODE BLOOD RELATIVE OTHER DATA=======
		$strDescRel =explode($delimiter,$row["chronicDesc"]);
		$strDescRel= $strDescRel[1];
		//====================================================
		/*----separating chronicDesc of patient and relative--*/																		
		$strDesc = get_set_pat_rel_values_retrive($strDesc,"pat",$delimiter);
	
		/*---separation end----------*/
		if(!empty($strDesc)){
			$arrDescTmp = explode($strSep, $strDesc);
			if(count($arrDescTmp) > 0){
				foreach($arrDescTmp as $key => $val){
					$arrTmp = explode($strSep2,$val);
					if($arrTmp[0]=="other"){
						$retArr["OtherDesc"] = $arrTmp[1];
					}
					//$fId = "elem_chronicDesc_".$arrTmp[0];
					//$$fId = $arrTmp[1];
				}
			}
		}
		//=====GET OCULAR MEDHX BLOOD RELATIVE OTHER DATA=======
		if(!empty($strDescRel)){ 
			$arrDescTmpRel = explode($strSep, $strDescRel);
			if(count($arrDescTmpRel) > 0){
				foreach($arrDescTmpRel as $key => $val){
					$arrTmpRel = explode($strSep2,$val);
					if($arrTmpRel[0]=="other"){
						$retArr["relOtherDesc"] = $arrTmpRel[1];
					}
				}
			}
		}		
		//====================================================
		// desc --		
		
		return $retArr;		
	}
	
?>