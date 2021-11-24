<?php
/*
 The MIT License (MIT)
 Distribute, Modify and Contribute under MIT License
 Use this software under MIT License
 
 File:class.cls_review_med_hx.php
 Coded in PHP7
 Purpose: Class for medical history review work
 Access Type: Include
*/
include_once 'class.cls_notifications.php';
$cls_notifications = new core_notifications();
class CLSReviewMedHx 
{
	private $delimiter;
	private $auditIP,$auditMac;
	function __construct(){		
		$this->delimiter = '~|~';
		$this->auditIP = getRealIpAddr();
		$this->auditMac = gethostbyaddr($auditIP);
	}
	function getReviewArrayOcular($result,$elem_chronicDesc_other,$chronicDescOtherOcular,$opreaterId,$action)
	{
		$ocularDataFields = make_field_type_array('ocular');
		$arrReview_Ocular = array();
		$arrReview_Ocular[] = array(
			"Pk_Id" => $result["ocular_id"],
			"Table_Name" => "ocular",
			"UI_Filed_Name" => "u_wear",
			"Data_Base_Field_Name"=> "you_wear",	
			"Data_Base_Field_Type"=>fun_get_field_type($ocularDataFields,"you_wear"),										
			"Field_Text" => "Do you wear",											
			"Operater_Id" => $opreaterId,					
			"Action"=> $action,						
			"Old_Value" => $result["you_wear"]
		);
		
		$arrReview_Ocular[] = array(
			"Pk_Id"=> $result["ocular_id"],
			"Table_Name"=> "ocular",				
			"UI_Filed_Name" => "exam_date",	
			"Data_Base_Field_Name"=> "last_exam_date",	
			"Data_Base_Field_Type"=>fun_get_field_type($ocularDataFields,"last_exam_date"),						
			"Field_Text"=> "Last eye exam date",											
			"Operater_Id"=> $opreaterId,											
			"Action"=> $action,
			"Old_Value"=> html_entity_decode($result['last_exam_date'])
		);
		$arrReview_Ocular[] = array(
			"Pk_Id"=> $result["ocular_id"],
			"Table_Name"=> "ocular",	
			"UI_Filed_Name" => "eye_problem",	
			"Data_Base_Field_Name"=> "eye_problems",	
			"Data_Base_Field_Type"=>fun_get_field_type($ocularDataFields,"eye_problems"),										
			"Field_Text"=> "Eye Problems",											
			"Operater_Id"=> $opreaterId,											
			"Action"=> $action,
			"Old_Value"=> html_entity_decode($result['eye_problems'])
		);
		$arrReview_Ocular[] = array(
			"Pk_Id"=> $result["ocular_id"],
			"Table_Name"=> "ocular",
			"UI_Filed_Name" => "eye_problem_other",	
			"Data_Base_Field_Name"=> "eye_problems_other",	
			"Data_Base_Field_Type"=>fun_get_field_type($ocularDataFields,"eye_problems_other"),										
			"Field_Text"=> "Eye Problems Others",											
			"Operater_Id"=> $opreaterId,											
			"Action"=> $action,
			"Old_Value"=> html_entity_decode($result['eye_problems_other'])
		);
		
		$strAnyConditionsYou = $result['any_conditions_you'];
		$strAnyConditionsYou = $this->get_set_pat_rel_values_retrive_review($strAnyConditionsYou,"pat",$this->delimiter);
		
		$arrReview_Ocular[] = array(
			"Pk_Id"=> $result["ocular_id"],
			"Table_Name"=> "ocular",		
			"UI_Filed_Name" => "any_conditions_u",
			"Data_Base_Field_Name"=> "any_conditions_you",	
			"Data_Base_Field_Type"=>fun_get_field_type($ocularDataFields,"any_conditions_you"),									
			"Field_Text"=> "Any condition you have presently or have had in the past",											
			"Operater_Id"=> $opreaterId,											
			"Action"=> $action,
			"Old_Value"=> html_entity_decode($strAnyConditionsYou)
		);
		$arrReview_Ocular[] = array(
			"Pk_Id"=> $result["ocular_id"],
			"Table_Name"=> "ocular",			
			"UI_Filed_Name" => "rel_any_conditions_relative",
			"Data_Base_Field_Name"=> "any_conditions_relative",	
			"Data_Base_Field_Type"=>fun_get_field_type($ocularDataFields,"any_conditions_relative"),								
			"Field_Text"=> "Any condition relative have presently or have had in the past",											
			"Operater_Id"=> $opreaterId,											
			"Action"=> $action,
			"Old_Value"=> html_entity_decode($result['any_conditions_relative'])
		);
		$arrReview_Ocular[] = array(
			"Pk_Id"=> $result["ocular_id"],
			"Table_Name"=> "ocular",		
			"UI_Filed_Name" => "any_conditions_other_u",
			"Data_Base_Field_Name"=> "any_conditions_others_you",	
			"Data_Base_Field_Type"=>fun_get_field_type($ocularDataFields,"any_conditions_others_you"),									
			"Field_Text"=> "Any other condition you have presently or have had in the past",											
			"Operater_Id"=> $opreaterId,											
			"Action"=> $action,
			"Old_Value"=> ($result['any_conditions_others_you']) ? $result['any_conditions_others_you'] : ""
		);
		
		$OtherDesc = "";
		$OtherDesc = $this->get_set_pat_rel_values_retrive_review($result['OtherDesc'],"pat",$this->delimiter);
		
		$arrReview_Ocular[] = array(
			"Pk_Id"=> $result["ocular_id"],
			"Table_Name"=> "ocular",	
			"UI_Filed_Name"=> "OtherDesc",
			"Data_Base_Field_Name"=> "OtherDesc",	
			"Data_Base_Field_Type"=>fun_get_field_type($ocularDataFields,"OtherDesc"),											
			"Field_Text"=> "Any other condition",											
			"Operater_Id"=> $opreaterId,											
			"Action"=> $action,
			"Old_Value"=> html_entity_decode($OtherDesc)
		);
		$arrReview_Ocular[] = array(
			"Pk_Id"=> $result["ocular_id"],
			"Table_Name"=> "ocular",
			"UI_Filed_Name"=> "elem_chronicDesc_other",	
			"Data_Base_Field_Name"=> "OtherDesc",	
			"Data_Base_Field_Type"=>fun_get_field_type($ocularDataFields,"OtherDesc"),										
			"Field_Text"=> "Any other condition value",											
			"Operater_Id"=> $opreaterId,											
			"Action"=> $action,
			"Old_Value"=> html_entity_decode($elem_chronicDesc_other)
		);
		$arrReview_Ocular[] = array(
			"Pk_Id"=> $result["ocular_id"],
			"Table_Name"=> "ocular",
			"UI_Filed_Name"=> "rel_elem_chronicDesc_other",	
			"Data_Base_Field_Name"=> "OtherDesc",	
			"Data_Base_Field_Type"=>fun_get_field_type($ocularDataFields,"OtherDesc"),										
			"Field_Text"=> "Any other relative condition value",											
			"Operater_Id"=> $opreaterId,											
			"Action"=> $action,
			"Old_Value"=> html_entity_decode($chronicDescOtherOcular)
		);
		
		
		$strDesc = $result['chronicDesc'];
		$strDesc = $this->get_set_pat_rel_values_retrive_review($strDesc,"pat",$this->delimiter);
		
		$arrReview_Ocular[] = array(
			"Pk_Id"=> $result["ocular_id"],
			"Table_Name"=> "ocular",	
			"UI_Filed_Name"=> "elem_chronicDesc",
			"Data_Base_Field_Name"=> "chronicDesc",	
			"Data_Base_Field_Type"=>fun_get_field_type($ocularDataFields,"chronicDesc"),										
			"Field_Text"=> "You condition value",											
			"Operater_Id"=> $opreaterId,											
			"Action"=> $action,
			"Old_Value"=> html_entity_decode($strDesc)
		);

		$strDescRel = $result['chronicDesc'];
		$strDescRel = $this->get_set_pat_rel_values_retrive_review($strDescRel,"rel",$this->delimiter);
		
		$arrReview_Ocular[] = array(
			"Pk_Id"=> $result["ocular_id"],
			"Table_Name"=> "ocular",	
			"UI_Filed_Name"=> "rel_elem_chronicDesc",
			"Data_Base_Field_Name"=> "chronicDesc",	
			"Data_Base_Field_Type"=>fun_get_field_type($ocularDataFields,"chronicDesc"),										
			"Field_Text"=> "Ocular -> Relatives",											
			"Operater_Id"=> $opreaterId,											
			"Action"=> $action,
			"Old_Value"=> html_entity_decode($strDescRel)
			
		);
		
		$arrReview_Ocular[] = array(
			"Pk_Id"=> $result["ocular_id"],
			"Table_Name"=> "ocular",		
			"UI_Filed_Name" => "rel_any_conditions_other_r",
			"Data_Base_Field_Name"=> "any_conditions_other_relative",	
			"Data_Base_Field_Type"=>fun_get_field_type($ocularDataFields,"any_conditions_other_relative"),								
			"Field_Text"=> "Any other condition your family member or blood relative have presently or have had in the past",											
			"Operater_Id"=> $opreaterId,											
			"Action"=> $action,
			"Old_Value"=> ($result['any_conditions_other_relative']) ? $result['any_conditions_other_relative'] : ""
		);
		
		$OtherDesc = "";
		$OtherDesc = $this->get_set_pat_rel_values_retrive_review($result['OtherDesc'],"rel",$this->delimiter);				
		$arrReview_Ocular[] = array(
			"Pk_Id"=> $result["ocular_id"],
			"Table_Name"=> "ocular",	
			"UI_Filed_Name"=> "rel_OtherDesc",
			"Data_Base_Field_Name"=> "relOtherDesc",	
			"Data_Base_Field_Type"=>fun_get_field_type($ocularDataFields,"relOtherDesc"),									
			"Field_Text"=> "Relative any other condition",											
			"Operater_Id"=> $opreaterId,											
			"Action"=> $action,
			"Old_Value"=> html_entity_decode($OtherDesc)
		);

		$arrReview_Ocular[] = array(
			"Pk_Id"=> $result["ocular_id"],
			"Table_Name"=> "ocular",	
			"UI_Filed_Name"=> "elem_chronicRelative",
			"Data_Base_Field_Name"=> "chronicRelative",	
			"Data_Base_Field_Type"=>fun_get_field_type($ocularDataFields,"chronicRelative"),										
			"Field_Text"=> "Relative condition value",											
			"Operater_Id"=> $opreaterId,											
			"Action"=> $action,
			"Old_Value"=> html_entity_decode($result['chronicRelative'])
		);
		
		return $arrReview_Ocular;
	}
	
	function getReviewArrayGH($result,$anyConditionsOthersBothArrGenHealth,$anyConditionsOthersBothArrRel,$this_blood_sugar,$this_cholesterol,$opreaterId,$action){
		$socialDataFields = make_field_type_array("social_history");
		$sugarDataFields = make_field_type_array("patient_blood_sugar");
		$cholesDataFields = make_field_type_array("patient_cholesterol");
		$genMedDataFields = make_field_type_array("general_medicine");
		
		$arrReview_GH = array();
		$arrReview_GH[] = array(
			"Pk_Id" => $result["general_id"],
			"Table_Name" => "general_medicine",
			"UI_Filed_Name" => "med_doctor",											
			"Field_Text" => "Primary Care Provider",
			"Operater_Id" => $opreaterId,					
			"Action"=> $action,						
			"Old_Value" => html_entity_decode($result["med_doctor"])
		);
		
		/*$arrReview_GH[] = array(
			"Pk_Id"=> $result["general_id"],
			"Table_Name"=> "general_medicine",				
			"UI_Filed_Name" => "genHealthComments",							
			"Field_Text"=> "General Health Comments",											
			"Operater_Id"=> $opreaterId,											
			"Action"=> $action,
			"Old_Value"=> html_entity_decode($result["genMedComments"])
		);*/
		/*$arrReview_GH[] = array(
			"Pk_Id"=> $result["general_id"],
			"Table_Name"=> "general_medicine",	
			"UI_Filed_Name" => "relDescHighBp",											
			"Field_Text"=> "High Blood Pressure Relative",											
			"Operater_Id"=> $opreaterId,											
			"Action"=> $action,
			"Old_Value"=> html_entity_decode($result["relDescHighBp"])
		);*/
		$strHighBloodPresherTxtPat = $result["desc_high_bp"];
		$strHighBloodPresherTxtPat = $this->get_set_pat_rel_values_retrive_review($strHighBloodPresherTxtPat,"pat",$this->delimiter);				
		$arrReview_GH[] = array(
			"Pk_Id"=> $result["general_id"],
			"Table_Name"=> "general_medicine",
			"UI_Filed_Name" => "txtHighBloodPresher",											
			"Field_Text"=> "High Blood Pressure Text",
			"Data_Base_Field_Name"=> "desc_high_bp",	
			"Data_Base_Field_Type"=>fun_get_field_type($genMedDataFields,"desc_high_bp"),											
			"Operater_Id"=> $opreaterId,											
			"Action"=> $action,
			"Old_Value"=> html_entity_decode($strHighBloodPresherTxtPat)
		);
		/*$arrReview_GH[] = array(
			"Pk_Id"=> $result["general_id"],
			"Table_Name"=> "general_medicine",		
			"UI_Filed_Name" => "relDescHeartProb",									
			"Field_Text"=> "Heart Problem Relative",											
			"Operater_Id"=> $opreaterId,											
			"Action"=> $action,
			"Old_Value"=> html_entity_decode($result["relDescHeartProb"])
		);*/		
		$strHeartTxtPat = $result["desc_heart_problem"];
		$strHeartTxtPat = $this->get_set_pat_rel_values_retrive_review($strHeartTxtPat,"pat",$this->delimiter);	
		$arrReview_GH[] = array(
			"Pk_Id"=> $result["general_id"],
			"Table_Name"=> "general_medicine",			
			"UI_Filed_Name" => "txtHeartProblem",
			"Data_Base_Field_Name"=> "desc_heart_problem",	
			"Data_Base_Field_Type"=>fun_get_field_type($genMedDataFields,"desc_heart_problem"),								
			"Field_Text"=> "Heart Problem Text",											
			"Operater_Id"=> $opreaterId,											
			"Action"=> $action,
			"Old_Value"=> html_entity_decode($strHeartTxtPat)
		);
		/*$arrReview_GH[] = array(
			"Pk_Id"=> $result["general_id"],
			"Table_Name"=> "general_medicine",		
			"UI_Filed_Name" => "relDescArthritisProb",									
			"Field_Text"=> "Arthritis Relative",											
			"Operater_Id"=> $opreaterId,											
			"Action"=> $action,
			"Old_Value"=> html_entity_decode($result["relDescArthritisProb"])
		);*/
		$strArtSubConTxtPat = $result["sub_conditions_you"];
		$strArtSubConTxtPat = $this->get_set_pat_rel_values_retrive_review($strArtSubConTxtPat,"pat",$this->delimiter);	
		$arrReview_GH[] = array(
			"Pk_Id"=> $result["general_id"],
			"Table_Name"=> "general_medicine",	
			"UI_Filed_Name"=> "elem_subCondition_u1",										
			"Field_Text"=> "Arthritis Checkboxes",											
			"Operater_Id"=> $opreaterId,											
			"Action"=> $action,
			"Old_Value"=> html_entity_decode($strArtSubConTxtPat)
		);
		
		$strArthritisTxtPat = $result["desc_arthrities"];
		$strArthritisTxtPat = $this->get_set_pat_rel_values_retrive_review($strArthritisTxtPat,"pat",$this->delimiter);
		$arrReview_GH[] = array(
			"Pk_Id"=> $result["general_id"],
			"Table_Name"=> "general_medicine",
			"UI_Filed_Name"=> "txtArthrities",	
			"Data_Base_Field_Name"=> "desc_arthrities",	
			"Data_Base_Field_Type"=>fun_get_field_type($genMedDataFields,"desc_arthrities"),										
			"Field_Text"=> "Arthritis Text",											
			"Operater_Id"=> $opreaterId,											
			"Action"=> $action,
			"Old_Value"=> html_entity_decode($strArthritisTxtPat)
		);
		/*$arrReview_GH[] = array(
			"Pk_Id"=> $result["general_id"],
			"Table_Name"=> "general_medicine",	
			"UI_Filed_Name"=> "relDescLungProb",										
			"Field_Text"=> "Lung Problems Relative",											
			"Operater_Id"=> $opreaterId,											
			"Action"=> $action,
			"Old_Value"=> html_entity_decode($result["relDescLungProb"])
		);*/
		$strLungProblemTxtPat = $result["desc_lung_problem"];
		$strLungProblemTxtPat = $this->get_set_pat_rel_values_retrive_review($strLungProblemTxtPat,"pat",$this->delimiter);
		$arrReview_GH[] = array(
			"Pk_Id"=> $result["general_id"],
			"Table_Name"=> "general_medicine",	
			"UI_Filed_Name"=> "txtLungProblem",	
			"Data_Base_Field_Name"=> "desc_lung_problem",	
			"Data_Base_Field_Type"=>fun_get_field_type($genMedDataFields,"desc_lung_problem"),									
			"Field_Text"=> "Lung Problems text",											
			"Operater_Id"=> $opreaterId,											
			"Action"=> $action,
			"Old_Value"=> html_entity_decode($strLungProblemTxtPat)
		);
		/*$arrReview_GH[] = array(
			"Pk_Id"=> $result["general_id"],
			"Table_Name"=> "general_medicine",	
			"UI_Filed_Name"=> "relDescStrokeProb",										
			"Field_Text"=> "Stroke Relative",											
			"Operater_Id"=> $opreaterId,											
			"Action"=> $action,
			"Old_Value"=> html_entity_decode($result["relDescStrokeProb"])
		);*/
		$strStrokeTxtPat = $result["desc_stroke"];
		$strStrokeTxtPat = $this->get_set_pat_rel_values_retrive_review($strStrokeTxtPat,"pat",$this->delimiter);
		$arrReview_GH[] = array(
			"Pk_Id"=> $result["general_id"],
			"Table_Name"=> "general_medicine",	
			"UI_Filed_Name"=> "txtStroke",
			"Data_Base_Field_Name"=> "desc_stroke",	
			"Data_Base_Field_Type"=>fun_get_field_type($genMedDataFields,"desc_stroke"),											
			"Field_Text"=> "Stroke Text",											
			"Operater_Id"=> $opreaterId,											
			"Action"=> $action,
			"Old_Value"=> html_entity_decode($strStrokeTxtPat)
		);
		/*$arrReview_GH[] = array(
			"Pk_Id"=> $result["general_id"],
			"Table_Name"=> "general_medicine",	
			"UI_Filed_Name"=> "relDescThyroidProb",										
			"Field_Text"=> "Thyroid Problems Relative",											
			"Operater_Id"=> $opreaterId,											
			"Action"=> $action,
			"Old_Value"=> html_entity_decode($result["relDescThyroidProb"])
		);*/
		$strThyroidProblemsTxtPat = $result["desc_thyroid_problems"];
		$strThyroidProblemsTxtPat = $this->get_set_pat_rel_values_retrive_review($strThyroidProblemsTxtPat,"pat",$this->delimiter);
		$arrReview_GH[] = array(
			"Pk_Id"=> $result["general_id"],
			"Table_Name"=> "general_medicine",	
			"UI_Filed_Name"=> "txtThyroidProblems",	
			"Data_Base_Field_Name"=> "desc_thyroid_problems",	
			"Data_Base_Field_Type"=>fun_get_field_type($genMedDataFields,"desc_thyroid_problems"),									
			"Field_Text"=> "Thyroid Problems Text",											
			"Operater_Id"=> $opreaterId,											
			"Action"=> $action,
			"Old_Value"=> html_entity_decode($strThyroidProblemsTxtPat)
		);
		$strDiabetesIdTxtPat = $result["diabetes_values"];
		$strDiabetesIdTxtPat = $this->get_set_pat_rel_values_retrive_review($strDiabetesIdTxtPat,"pat",$this->delimiter);
		$arrReview_GH[] = array(
			"Pk_Id"=> $result["general_id"],
			"Table_Name"=> "general_medicine",	
			"UI_Filed_Name"=> "text_diabetes_id",										
			"Field_Text"=> "Diabetes",											
			"Operater_Id"=> $opreaterId,											
			"Action"=> $action,
			"Old_Value"=> html_entity_decode($strDiabetesIdTxtPat)
		);
		/*$arrReview_GH[] = array(
			"Pk_Id"=> $result["general_id"],
			"Table_Name"=> "general_medicine",	
			"UI_Filed_Name"=> "elem_desc_r",										
			"Field_Text"=> "Diabetes relation",											
			"Operater_Id"=> $opreaterId,											
			"Action"=> $action,
			"Old_Value"=> html_entity_decode($result["desc_r"])
		);*/
		$strDiabetesTxtPat = $result["desc_u"];
		$strDiabetesTxtPat = $this->get_set_pat_rel_values_retrive_review($strDiabetesTxtPat,"pat",$this->delimiter);
		$arrReview_GH[] = array(
			"Pk_Id"=> $result["general_id"],
			"Table_Name"=> "general_medicine",	
			"UI_Filed_Name"=> "elem_desc_u",										
			"Field_Text"=> "Diabetes Text",											
			"Operater_Id"=> $opreaterId,											
			"Action"=> $action,
			"Old_Value"=> html_entity_decode($strDiabetesTxtPat)
		);
		
		/*$arrReview_GH[] = array(
			"Pk_Id"=> $result["general_id"],
			"Table_Name"=> "general_medicine",	
			"UI_Filed_Name"=> "relDescLDL",										
			"Field_Text"=> "LDL Relative",											
			"Operater_Id"=> $opreaterId,											
			"Action"=> $action,
			"Old_Value"=> html_entity_decode($result["relDescLDL"])
		);*/
		$strLDLTxtPat = $result["desc_LDL"];
		$strLDLTxtPat = $this->get_set_pat_rel_values_retrive_review($strLDLTxtPat,"pat",$this->delimiter);
		
		$arrReview_GH[] = array(
			"Pk_Id"=> $result["general_id"],
			"Table_Name"=> "general_medicine",	
			"UI_Filed_Name"=> "txtLDL",	
			"Data_Base_Field_Name"=> "desc_LDL",	
			"Data_Base_Field_Type"=>fun_get_field_type($genMedDataFields,"desc_LDL"),									
			"Field_Text"=> "LDL description",											
			"Operater_Id"=> $opreaterId,											
			"Action"=> $action,
			"Old_Value"=> html_entity_decode($strLDLTxtPat)
		);
		/*$arrReview_GH[] = array(
			"Pk_Id"=> $result["general_id"],
			"Table_Name"=> "general_medicine",	
			"UI_Filed_Name"=> "relDescUlcersProb",										
			"Field_Text"=> "Ulcers Relative",											
			"Operater_Id"=> $opreaterId,											
			"Action"=> $action,
			"Old_Value"=> html_entity_decode($result["relDescUlcersProb"])
		);*/
		
		$strUlcersTxtPat = $result["desc_ulcers"];
		$strUlcersTxtPat = $this->get_set_pat_rel_values_retrive_review($strUlcersTxtPat,"pat",$this->delimiter);
		$arrReview_GH[] = array(
			"Pk_Id"=> $result["general_id"],
			"Table_Name"=> "general_medicine",	
			"UI_Filed_Name"=> "txtUlcers",
			"Data_Base_Field_Name"=> "desc_ulcers",	
			"Data_Base_Field_Type"=>fun_get_field_type($genMedDataFields,"desc_ulcers"),										
			"Field_Text"=> "Ulcers Text",											
			"Operater_Id"=> $opreaterId,											
			"Action"=> $action,
			"Old_Value"=> html_entity_decode($strUlcersTxtPat)
		);
		
		/*$arrReview_GH[] = array(
			"Pk_Id"=> $result["general_id"],
			"Table_Name"=> "general_medicine",	
			"UI_Filed_Name"=> "relDescCancerProb",										
			"Field_Text"=> "Cancer Relative",											
			"Operater_Id"=> $opreaterId,											
			"Action"=> $action,
			"Old_Value"=> html_entity_decode($result["relDescCancerProb"])
		);*/
		
		$strCancerTxtPat = $result["desc_cancer"];
		$strCancerTxtPat = $this->get_set_pat_rel_values_retrive_review($strCancerTxtPat,"pat",$this->delimiter);
		$arrReview_GH[] = array(
			"Pk_Id"=> $result["general_id"],
			"Table_Name"=> "general_medicine",	
			"UI_Filed_Name"=> "txtCancer",
			"Data_Base_Field_Name"=> "desc_cancer",	
			"Data_Base_Field_Type"=>fun_get_field_type($genMedDataFields,"desc_cancer"),										
			"Field_Text"=> "Cancer Text",											
			"Operater_Id"=> $opreaterId,											
			"Action"=> $action,
			"Old_Value"=> html_entity_decode($strCancerTxtPat)
		);
		
		$arrReview_GH[] = array(
			"Pk_Id"=> $result["general_id"],
			"Table_Name"=> "general_medicine",	
			"UI_Filed_Name"=> "any_conditions_u1",										
			"Field_Text"=> "Any condition you have presently or have had in the past (Yes)",
			"Data_Base_Field_Name"=> "any_conditions_you",	
			"Data_Base_Field_Type"=>fun_get_field_type($genMedDataFields,"any_conditions_you"),											
			"Operater_Id"=> $opreaterId,											
			"Action"=> $action,
			"Old_Value"=> html_entity_decode($result["any_conditions_you"])
		);
		
		$arrReview_GH[] = array(
			"Pk_Id"=> $result["general_id"],
			"Table_Name"=> "general_medicine",	
			"UI_Filed_Name"=> "any_conditions_u1_n",										
			"Field_Text"=> "Any condition you have presently or have had in the past (No)",	
			"Data_Base_Field_Name"=> "any_conditions_you_n",	
			"Data_Base_Field_Type"=>fun_get_field_type($genMedDataFields,"any_conditions_you_n"),										
			"Operater_Id"=> $opreaterId,											
			"Action"=> $action,
			"Old_Value"=> html_entity_decode($result["any_conditions_you_n"])
		);


		/*$arrReview_GH[] = array(
			"Pk_Id"=> $result["general_id"],
			"Table_Name"=> "general_medicine",	
			"UI_Filed_Name"=> "any_conditions_relative1",										
			"Field_Text"=> "Any condition blood relative have presently or have had in the past",											
			"Operater_Id"=> $opreaterId,											
			"Action"=> $action,
			"Old_Value"=> html_entity_decode($result["any_conditions_relative"])
		);*/
		
		$arrReview_GH[] = array(
			"Pk_Id"=> $result["general_id"],
			"Table_Name"=> "general_medicine",	
			"UI_Filed_Name"=> "cbkMasterPtCon",		
			"Data_Base_Field_Name"=> "cbk_master_pt_con",	
			"Data_Base_Field_Type"=>fun_get_field_type($genMedDataFields,"cbk_master_pt_con"),								
			"Field_Text"=> "No known patient medical condition",											
			"Operater_Id"=> $opreaterId,											
			"Action"=> $action,
			"Old_Value"=> html_entity_decode($result["cbk_master_pt_con"])
		);
		
		$chk_under_control_pat = $this->get_set_pat_rel_values_retrive_review($result["chk_under_control"],"pat",$this->delimiter);
		$arrReview_GH[] = array(
			"Pk_Id"=> $result["general_id"],
			"Table_Name"=> "general_medicine",	
			"UI_Filed_Name"=> "chk_under_control",										
			"Field_Text"=> "Any Condition You Have Presently Or Have Had In The Past Under Control",											
			"Operater_Id"=> $opreaterId,											
			"Action"=> $action,
			"Old_Value"=> html_entity_decode($chk_under_control_pat)
		);
		$arrReview_GH[] = array(
			"Pk_Id"=> $result["general_id"],
			"Table_Name"=> "general_medicine",	
			"UI_Filed_Name"=> "any_conditions_others_both",										
			"Field_Text"=> "Any other condition you or blood relative have presently or have had in the past",											
			"Operater_Id"=> $opreaterId,											
			"Action"=> $action,
			"Old_Value"=> html_entity_decode($anyConditionsOthersBothArrGenHealth)
		);
		/*$arrReview_GH[] = array(
			"Pk_Id"=> $result["general_id"],
			"Table_Name"=> "general_medicine",	
			"UI_Filed_Name"=> "ghRelDescOthers",										
			"Field_Text"=> "Other blood relative description",											
			"Operater_Id"=> $opreaterId,											
			"Action"=> $action,
			"Old_Value"=> html_entity_decode($result["ghRelDescOthers"])
		);*/
		
		$strOthersTxtPat = $result["any_conditions_others"];
		$strOthersTxtPat = $this->get_set_pat_rel_values_retrive_review($strOthersTxtPat,"pat",$this->delimiter);
		$arrReview_GH[] = array(
			"Pk_Id"=> $result["general_id"],
			"Table_Name"=> "general_medicine",	
			"UI_Filed_Name"=> "any_conditions_others1",		
			"Data_Base_Field_Name"=> "any_conditions_others",	
			"Data_Base_Field_Type"=>fun_get_field_type($genMedDataFields,"any_conditions_others"),								
			"Field_Text"=> "Any other condition you have presently or have had in the past",											
			"Operater_Id"=> $opreaterId,											
			"Action"=> $action,
			"Old_Value"=> html_entity_decode($strOthersTxtPat)
		);
		$arrReview_GH[] = array(
			"Pk_Id"=> $result["general_id"],
			"Table_Name"=> "general_medicine",	
			"UI_Filed_Name"=> "chk_annual_colorectal_cancer_screenings",
			"Data_Base_Field_Name"=> "chk_annual_colorectal_cancer_screenings",	
			"Data_Base_Field_Type"=>fun_get_field_type($genMedDataFields,"chk_annual_colorectal_cancer_screenings"),										
			"Field_Text"=> "Annual Colorectal cancer screenings",											
			"Operater_Id"=> $opreaterId,											
			"Action"=> $action,
			"Old_Value"=> html_entity_decode($result["chk_annual_colorectal_cancer_screenings"])
		);
		$arrReview_GH[] = array(
			"Pk_Id"=> $result["general_id"],
			"Table_Name"=> "general_medicine",	
			"UI_Filed_Name"=> "chk_receiving_annual_mammogram",	
			"Data_Base_Field_Name"=> "chk_receiving_annual_mammogram",	
			"Data_Base_Field_Type"=>fun_get_field_type($genMedDataFields,"chk_receiving_annual_mammogram"),										
			"Field_Text"=> "Receiving annual mammogram",											
			"Operater_Id"=> $opreaterId,											
			"Action"=> $action,
			"Old_Value"=> html_entity_decode($result["chk_receiving_annual_mammogram"])
		);
		$arrReview_GH[] = array(
			"Pk_Id"=> $result["general_id"],
			"Table_Name"=> "general_medicine",	
			"UI_Filed_Name"=> "chk_received_flu_vaccine",
			"Data_Base_Field_Name"=> "chk_received_flu_vaccine",	
			"Data_Base_Field_Type"=>fun_get_field_type($genMedDataFields,"chk_received_flu_vaccine"),										
			"Field_Text"=> "Received flu vaccine",											
			"Operater_Id"=> $opreaterId,											
			"Action"=> $action,
			"Old_Value"=> html_entity_decode($result["chk_received_flu_vaccine"])
		);
		$arrReview_GH[] = array(
			"Pk_Id"=> $result["general_id"],
			"Table_Name"=> "general_medicine",	
			"UI_Filed_Name"=> "chk_high_risk_for_cardiac",	
			"Data_Base_Field_Name"=> "chk_high_risk_for_cardiac",	
			"Data_Base_Field_Type"=>fun_get_field_type($genMedDataFields,"chk_high_risk_for_cardiac"),									
			"Field_Text"=> "High-risk for cardiac events on aspirin prophylaxis",											
			"Operater_Id"=> $opreaterId,											
			"Action"=> $action,
			"Old_Value"=> html_entity_decode($result["chk_high_risk_for_cardiac"])
		);
		$arrReview_GH[] = array(
			"Pk_Id"=> $result["general_id"],
			"Table_Name"=> "general_medicine",	
			"UI_Filed_Name"=> "con_for_nut",	
			"Data_Base_Field_Name"=> "nutrition_counseling",	
			"Data_Base_Field_Type"=>fun_get_field_type($genMedDataFields,"nutrition_counseling"),									
			"Field_Text"=> "Counseling for Nutrition/Diet ",											
			"Operater_Id"=> $opreaterId,											
			"Action"=> $action,
			"Old_Value"=> html_entity_decode($result["nutrition_counseling"])
		);
		$arrReview_GH[] = array(
			"Pk_Id"=> $result["general_id"],
			"Table_Name"=> "general_medicine",	
			"UI_Filed_Name"=> "con_for_nut_date",	
			"Data_Base_Field_Name"=> "nutrition_counseling_date",	
			"Data_Base_Field_Type"=>fun_get_field_type($genMedDataFields,"nutrition_counseling_date"),									
			"Field_Text"=> "Counseling for Nutrition/Diet Date",											
			"Operater_Id"=> $opreaterId,											
			"Action"=> $action,
			"Old_Value"=> html_entity_decode($result["nutrition_counseling_date"])
		);
		$arrReview_GH[] = array(
			"Pk_Id"=> $result["general_id"],
			"Table_Name"=> "general_medicine",	
			"UI_Filed_Name"=> "con_for_phy",	
			"Data_Base_Field_Name"=> "physical_activity_counseling",	
			"Data_Base_Field_Type"=>fun_get_field_type($genMedDataFields,"physical_activity_counseling"),									
			"Field_Text"=> "Counseling for Physical Activity",											
			"Operater_Id"=> $opreaterId,											
			"Action"=> $action,
			"Old_Value"=> html_entity_decode($result["physical_activity_counseling"])
		);
		$arrReview_GH[] = array(
			"Pk_Id"=> $result["general_id"],
			"Table_Name"=> "general_medicine",	
			"UI_Filed_Name"=> "con_for_phy_date",	
			"Data_Base_Field_Name"=> "physical_activity_counseling_date",	
			"Data_Base_Field_Type"=>fun_get_field_type($genMedDataFields,"physical_activity_counseling_date"),									
			"Field_Text"=> "Counseling for Physical Activity Date",											
			"Operater_Id"=> $opreaterId,											
			"Action"=> $action,
			"Old_Value"=> html_entity_decode($result["physical_activity_counseling_date"])
		);
		$arrReview_GH[] = array(
			"Pk_Id"=> $result["general_id"],
			"Table_Name"=> "general_medicine",	
			"UI_Filed_Name"=> "review_const_others",
			"Data_Base_Field_Name"=> "review_const_others",	
			"Data_Base_Field_Type"=>fun_get_field_type($genMedDataFields,"review_const_others"),										
			"Field_Text"=> "Constitutional other",											
			"Operater_Id"=> $opreaterId,											
			"Action"=> $action,
			"Old_Value"=> html_entity_decode($result["review_const_others"])
		);
		$arrReview_GH[] = array(
			"Pk_Id"=> $result["general_id"],
			"Table_Name"=> "general_medicine",	
			"UI_Filed_Name"=> "review_const",	
			"Data_Base_Field_Name"=> "review_const",	
			"Data_Base_Field_Type"=>fun_get_field_type($genMedDataFields,"review_const"),									
			"Field_Text"=> "Constitutional",											
			"Operater_Id"=> $opreaterId,											
			"Action"=> $action,
			"Old_Value"=> html_entity_decode($result["review_const"])
		);
		$arrReview_GH[] = array(
			"Pk_Id"=> $result["general_id"],
			"Table_Name"=> "general_medicine",	
			"UI_Filed_Name"=> "review_head_others",	
			"Data_Base_Field_Name"=> "review_head_others",	
			"Data_Base_Field_Type"=>fun_get_field_type($genMedDataFields,"review_head_others"),									
			"Field_Text"=> "Ear, Nose, Mouth & Throat other",											
			"Operater_Id"=> $opreaterId,											
			"Action"=> $action,
			"Old_Value"=> html_entity_decode($result["review_head_others"])
		);
		$arrReview_GH[] = array(
			"Pk_Id"=> $result["general_id"],
			"Table_Name"=> "general_medicine",	
			"UI_Filed_Name"=> "review_head",
			"Data_Base_Field_Name"=> "review_head",	
			"Data_Base_Field_Type"=>fun_get_field_type($genMedDataFields,"review_head"),										
			"Field_Text"=> "Ear, Nose, Mouth & Throat",											
			"Operater_Id"=> $opreaterId,											
			"Action"=> $action,
			"Old_Value"=> html_entity_decode($result["review_head"])
		);
		$arrReview_GH[] = array(
			"Pk_Id"=> $result["general_id"],
			"Table_Name"=> "general_medicine",	
			"UI_Filed_Name"=> "review_resp_others",	
			"Data_Base_Field_Name"=> "review_resp_others",	
			"Data_Base_Field_Type"=>fun_get_field_type($genMedDataFields,"review_resp_others"),									
			"Field_Text"=> "Respiratory other",											
			"Operater_Id"=> $opreaterId,											
			"Action"=> $action,
			"Old_Value"=> html_entity_decode($result["review_resp_others"])
		);
		$arrReview_GH[] = array(
			"Pk_Id"=> $result["general_id"],
			"Table_Name"=> "general_medicine",	
			"UI_Filed_Name"=> "review_resp",	
			"Data_Base_Field_Name"=> "review_resp",	
			"Data_Base_Field_Type"=>fun_get_field_type($genMedDataFields,"review_resp"),									
			"Field_Text"=> "Respiratory",											
			"Operater_Id"=> $opreaterId,											
			"Action"=> $action,
			"Old_Value"=> html_entity_decode($result["review_resp"])
		);
		$arrReview_GH[] = array(
			"Pk_Id"=> $result["general_id"],
			"Table_Name"=> "general_medicine",	
			"UI_Filed_Name"=> "review_card_others",
			"Data_Base_Field_Name"=> "review_card_others",	
			"Data_Base_Field_Type"=>fun_get_field_type($genMedDataFields,"review_card_others"),										
			"Field_Text"=> "Cardiovascular other",											
			"Operater_Id"=> $opreaterId,											
			"Action"=> $action,
			"Old_Value"=> html_entity_decode($result["review_card_others"])
		);
		$arrReview_GH[] = array(
			"Pk_Id"=> $result["general_id"],
			"Table_Name"=> "general_medicine",	
			"UI_Filed_Name"=> "review_card",
			"Data_Base_Field_Name"=> "review_card",	
			"Data_Base_Field_Type"=>fun_get_field_type($genMedDataFields,"review_card"),										
			"Field_Text"=> "Cardiovascular",											
			"Operater_Id"=> $opreaterId,											
			"Action"=> $action,
			"Old_Value"=> html_entity_decode($result["review_card"])
		);
		$arrReview_GH[] = array(
			"Pk_Id"=> $result["general_id"],
			"Table_Name"=> "general_medicine",	
			"UI_Filed_Name"=> "review_gastro_others",
			"Data_Base_Field_Name"=> "review_gastro_others",	
			"Data_Base_Field_Type"=>fun_get_field_type($genMedDataFields,"review_gastro_others"),											
			"Field_Text"=> "Gastrointenstinal other",											
			"Operater_Id"=> $opreaterId,											
			"Action"=> $action,
			"Old_Value"=> html_entity_decode($result["review_gastro_others"])
		);
		$arrReview_GH[] = array(
			"Pk_Id"=> $result["general_id"],
			"Table_Name"=> "general_medicine",	
			"UI_Filed_Name"=> "review_gastro",
			"Data_Base_Field_Name"=> "review_gastro",	
			"Data_Base_Field_Type"=>fun_get_field_type($genMedDataFields,"review_gastro"),										
			"Field_Text"=> "Gastrointenstinal",											
			"Operater_Id"=> $opreaterId,											
			"Action"=> $action,
			"Old_Value"=> html_entity_decode($result["review_gastro"])
		);
		$arrReview_GH[] = array(
			"Pk_Id"=> $result["general_id"],
			"Table_Name"=> "general_medicine",	
			"UI_Filed_Name"=> "review_genit_others",
			"Data_Base_Field_Name"=> "review_genit_others",	
			"Data_Base_Field_Type"=>fun_get_field_type($genMedDataFields,"review_genit_others"),										
			"Field_Text"=> "Genitourinary other",											
			"Operater_Id"=> $opreaterId,											
			"Action"=> $action,
			"Old_Value"=> html_entity_decode($result["review_genit_others"])
		);
		$arrReview_GH[] = array(
			"Pk_Id"=> $result["general_id"],
			"Table_Name"=> "general_medicine",	
			"UI_Filed_Name"=> "review_genit",
			"Data_Base_Field_Name"=> "review_genit",	
			"Data_Base_Field_Type"=>fun_get_field_type($genMedDataFields,"review_genit"),										
			"Field_Text"=> "Genitourinary",											
			"Operater_Id"=> $opreaterId,											
			"Action"=> $action,
			"Old_Value"=> html_entity_decode($result["review_genit"])
		);
		$arrReview_GH[] = array(
			"Pk_Id"=> $result["general_id"],
			"Table_Name"=> "general_medicine",	
			"UI_Filed_Name"=> "review_aller_others",
			"Data_Base_Field_Name"=> "review_aller_others",	
			"Data_Base_Field_Type"=>fun_get_field_type($genMedDataFields,"review_aller_others"),										
			"Field_Text"=> "Allergic/Immunologic other",											
			"Operater_Id"=> $opreaterId,											
			"Action"=> $action,
			"Old_Value"=> html_entity_decode($result["review_aller_others"])
		);
		$arrReview_GH[] = array(
			"Pk_Id"=> $result["general_id"],
			"Table_Name"=> "general_medicine",	
			"UI_Filed_Name"=> "review_aller",
			"Data_Base_Field_Name"=> "review_aller",	
			"Data_Base_Field_Type"=>fun_get_field_type($genMedDataFields,"review_aller"),										
			"Field_Text"=> "Allergic/Immunologic",											
			"Operater_Id"=> $opreaterId,											
			"Action"=> $action,
			"Old_Value"=> html_entity_decode($result["review_aller"])
		);
		$arrReview_GH[] = array(
			"Pk_Id"=> $result["general_id"],
			"Table_Name"=> "general_medicine",	
			"UI_Filed_Name"=> "review_neuro_others",
			"Data_Base_Field_Name"=> "review_neuro_others",	
			"Data_Base_Field_Type"=>fun_get_field_type($genMedDataFields,"review_neuro_others"),										
			"Field_Text"=> "Neurological other",											
			"Operater_Id"=> $opreaterId,											
			"Action"=> $action,
			"Old_Value"=> html_entity_decode($result["review_neuro_others"])
		);
		$arrReview_GH[] = array(
			"Pk_Id"=> $result["general_id"],
			"Table_Name"=> "general_medicine",	
			"UI_Filed_Name"=> "review_neuro",
			"Data_Base_Field_Name"=> "review_neuro",	
			"Data_Base_Field_Type"=>fun_get_field_type($genMedDataFields,"review_neuro"),										
			"Field_Text"=> "Neurological",											
			"Operater_Id"=> $opreaterId,											
			"Action"=> $action,
			"Old_Value"=> html_entity_decode($result["review_neuro"])
		);
		//new ROS
		$review_sys = trim($result["review_sys"]); 
		$ar_review_sys = array();
		if(!empty($review_sys)){
			$ar_review_sys = json_decode($review_sys, true);
		}
		
		$ar_tmp = array("Integumentary"=>'review_intgmntr',	"Psychiatry"=>'review_psychiatry', 
					"Hemotologic/Lymphatic"=>'review_blood_lymph',
					"Musculoskeletal"=>'review_musculoskeletal',
					"Endocrine"=>'review_endocrine',"Eyes"=>'review_eye');
		foreach($ar_tmp as $k => $v){
			$$v = (!empty($ar_review_sys[$v])) ? $ar_review_sys[$v] : "" ;
			$vother = $v."_others"; 
			$$vother = (!empty($ar_review_sys[$vother])) ? $ar_review_sys[$vother] : "";
			
			$arrReview_GH[] = array(
				"Pk_Id"=> $result["general_id"],
				"Table_Name"=> "general_medicine",	
				"UI_Filed_Name"=> $vother,
				"Data_Base_Field_Name"=> "review_sys",	
				"Data_Base_Field_Type"=>fun_get_field_type($genMedDataFields,"review_sys"),
				"Field_Text"=> "".$k." other",
				"Operater_Id"=> $opreaterId,
				"Action"=> $action,
				"Old_Value"=> html_entity_decode($$vother)
			);
			
			$arrReview_GH[] = array(
				"Pk_Id"=> $result["general_id"],
				"Table_Name"=> "general_medicine",	
				"UI_Filed_Name"=> "".$v,
				"Data_Base_Field_Name"=> "review_sys",	
				"Data_Base_Field_Type"=>fun_get_field_type($genMedDataFields,"review_sys"),
				"Field_Text"=> "".$k,
				"Operater_Id"=> $opreaterId,
				"Action"=> $action,
				"Old_Value"=> html_entity_decode($$v)
			);
		}		
		
		//End new ROS
		
		$arrReview_GH[] = array(
			"Pk_Id"=> $result["general_id"],
			"Table_Name"=> "general_medicine",	
			"UI_Filed_Name"=> "cbkMasterROS",
			"Data_Base_Field_Name"=> "cbk_master_ROS",	
			"Data_Base_Field_Type"=>fun_get_field_type($genMedDataFields,"cbk_master_ROS"),										
			"Field_Text"=> "No known medical condition",											
			"Operater_Id"=> $opreaterId,											
			"Action"=> $action,
			"Old_Value"=> html_entity_decode($result["cbk_master_ROS"])
		);		
		
		$arrReview_GH[] = array(
			"Pk_Id"=> $result["general_id"],
			"Table_Name"=> "general_medicine",	
			"UI_Filed_Name"=> "negChkBx",
			"Data_Base_Field_Name"=> "negChkBx",	
			"Data_Base_Field_Type"=>fun_get_field_type($genMedDataFields,"negChkBx"),										
			"Field_Text"=> "Negative",											
			"Operater_Id"=> $opreaterId,											
			"Action"=> $action,
			"Old_Value"=> html_entity_decode($result["negChkBx"])
		);		
		
		$arrReview_GH[] = array(
			"Pk_Id" => $this_blood_sugar["id"],
			"Table_Name" => "patient_blood_sugar",
			"UI_Filed_Name" => "this_blood_sugar_date",	
			"Data_Base_Field_Name"=> "creation_date",	
			"Data_Base_Field_Type"=>fun_get_field_type($sugarDataFields,"creation_date"),								
			"Field_Text"=> "Any other condition your family member or blood relative have presently or have had in the past",										
			"Field_Text" => "Blood Sugar Date",											
			"Operater_Id" => $opreaterId,					
			"Action"=> $action,						
			"Old_Value" => html_entity_decode($this_blood_sugar["createdDate"])
		);
		$arrReview_GH[] = array(
			"Pk_Id" => $this_blood_sugar["id"],
			"Table_Name" => "patient_blood_sugar",
			"UI_Filed_Name" => "this_blood_sugar",
			"Data_Base_Field_Name"=> "sugar_value",	
			"Data_Base_Field_Type"=>fun_get_field_type($sugarDataFields,"sugar_value"),											
			"Field_Text" => "Blood Sugar",											
			"Operater_Id" => $opreaterId,					
			"Action"=> $action,						
			"Old_Value" => html_entity_decode($this_blood_sugar["sugar_value"])
		);
		if($action == "update"){
			$this_blood_sugar_fasting_value = ($this_blood_sugar["is_fasting"]) ? html_entity_decode($this_blood_sugar["is_fasting"]) : "0";
		}
		else{
			$this_blood_sugar_fasting_value = html_entity_decode($this_blood_sugar["is_fasting"]);
		}
		$arrReview_GH[] = array(
			"Pk_Id" => $this_blood_sugar["id"],
			"Table_Name" => "patient_blood_sugar",
			"UI_Filed_Name" => "this_blood_sugar_fasting",
			"Data_Base_Field_Name"=> "is_fasting",	
			"Data_Base_Field_Type"=>fun_get_field_type($sugarDataFields,"is_fasting"),											
			"Field_Text" => "Blood Sugar Fasting",											
			"Operater_Id" => $opreaterId,					
			"Action"=> $action,						
			"Old_Value" => $this_blood_sugar_fasting_value
		);
		$arrReview_GH[] = array(
			"Pk_Id" => $this_blood_sugar["id"],
			"Table_Name" => "patient_blood_sugar",
			"UI_Filed_Name" => "this_blood_sugar_hba1c",
			"Data_Base_Field_Name"=> "hba1c",	
			"Data_Base_Field_Type"=>fun_get_field_type($sugarDataFields,"hba1c"),											
			"Field_Text" => "HbA1c",											
			"Operater_Id" => $opreaterId,					
			"Action"=> $action,						
			"Old_Value" =>  html_entity_decode($this_blood_sugar["hba1c"])
		);
		$arrReview_GH[] = array(
			"Pk_Id" => $this_blood_sugar["id"],
			"Table_Name" => "patient_blood_sugar",
			"UI_Filed_Name" => "this_blood_sugar_time",	
			"Data_Base_Field_Name"=> "time_of_day",	
			"Data_Base_Field_Type"=>fun_get_field_type($sugarDataFields,"time_of_day"),										
			"Field_Text" => "Blood Sugar Time of Day",											
			"Operater_Id" => $opreaterId,					
			"Action"=> $action,						
			"Old_Value" => html_entity_decode($this_blood_sugar["time_of_day"])
		);
		$arrReview_GH[] = array(
			"Pk_Id" => $this_blood_sugar["id"],
			"Table_Name" => "patient_blood_sugar",
			"UI_Filed_Name" => "this_blood_sugar_desc",	
			"Data_Base_Field_Name"=> "description",	
			"Data_Base_Field_Type"=>fun_get_field_type($sugarDataFields,"description"),										
			"Field_Text" => "Blood Sugar Description",											
			"Operater_Id" => $opreaterId,					
			"Action"=> $action,						
			"Old_Value" => html_entity_decode($this_blood_sugar["description"])
		);
		
		$arrReview_GH[] = array(
			"Pk_Id" => $this_cholesterol["id"],
			"Table_Name" => "patient_cholesterol",
			"UI_Filed_Name" => "this_cholesterol_date",
			"Data_Base_Field_Name"=> "creation_date",	
			"Data_Base_Field_Type"=>fun_get_field_type($cholesDataFields,"creation_date"),											
			"Field_Text" => "Cholesterol Date",											
			"Operater_Id" => $opreaterId,					
			"Action"=> $action,						
			"Old_Value" => html_entity_decode($this_cholesterol["createdDate"])
		);
		$arrReview_GH[] = array(
			"Pk_Id" => $this_cholesterol["id"],
			"Table_Name" => "patient_cholesterol",
			"UI_Filed_Name" => "this_cholesterol_total",
			"Data_Base_Field_Name"=> "cholesterol_total",	
			"Data_Base_Field_Type"=>fun_get_field_type($cholesDataFields,"cholesterol_total"),												
			"Field_Text" => "Cholesterol Total",											
			"Operater_Id" => $opreaterId,					
			"Action"=> $action,						
			"Old_Value" => html_entity_decode($this_cholesterol["cholesterol_total"])
		);
		$arrReview_GH[] = array(
			"Pk_Id" => $this_cholesterol["id"],
			"Table_Name" => "patient_cholesterol",
			"UI_Filed_Name" => "this_cholesterol_triglycerides",
			"Data_Base_Field_Name"=> "cholesterol_triglycerides",	
			"Data_Base_Field_Type"=>fun_get_field_type($cholesDataFields,"cholesterol_triglycerides"),											
			"Field_Text" => "Cholesterol Trig.",											
			"Operater_Id" => $opreaterId,					
			"Action"=> $action,						
			"Old_Value" => html_entity_decode($this_cholesterol["cholesterol_triglycerides"])
		);
		$arrReview_GH[] = array(
			"Pk_Id" => $this_cholesterol["id"],
			"Table_Name" => "patient_cholesterol",
			"UI_Filed_Name" => "this_cholesterol_LDL",	
			"Data_Base_Field_Name"=> "cholesterol_LDL",	
			"Data_Base_Field_Type"=>fun_get_field_type($cholesDataFields,"cholesterol_LDL"),										
			"Field_Text" => "Cholesterol LDL",											
			"Operater_Id" => $opreaterId,					
			"Action"=> $action,						
			"Old_Value" => html_entity_decode($this_cholesterol["cholesterol_LDL"])
		);
		$arrReview_GH[] = array(
			"Pk_Id" => $this_cholesterol["id"],
			"Table_Name" => "patient_cholesterol",
			"UI_Filed_Name" => "this_cholesterol_HDL",
			"Data_Base_Field_Name"=> "cholesterol_HDL",	
			"Data_Base_Field_Type"=>fun_get_field_type($cholesDataFields,"cholesterol_HDL"),											
			"Field_Text" => "Cholesterol HDL",											
			"Operater_Id" => $opreaterId,					
			"Action"=> $action,						
			"Old_Value" => html_entity_decode($this_cholesterol["cholesterol_HDL"])
		);
		$arrReview_GH[] = array(
			"Pk_Id" => $this_cholesterol["id"],
			"Table_Name" => "patient_cholesterol",
			"UI_Filed_Name" => "this_cholesterol_desc",	
			"Data_Base_Field_Name"=> "description",	
			"Data_Base_Field_Type"=>fun_get_field_type($cholesDataFields,"description"),										
			"Field_Text" => "Cholesterol Description",											
			"Operater_Id" => $opreaterId,					
			"Action"=> $action,						
			"Old_Value" => html_entity_decode($this_cholesterol["description"])
		);
		
		############
		$arrReview_GH[] = array(
			"Pk_Id"=> $result["general_id"],
			"Table_Name"=> "general_medicine",	
			"UI_Filed_Name"=> "cbkMasterFamCon",
			"Data_Base_Field_Name"=> "cbk_master_fam_con",	
			"Data_Base_Field_Type"=>fun_get_field_type($genMedDataFields,"cbk_master_fam_con"),										
			"Field_Text"=> "No known family medical condition",											
			"Operater_Id"=> $opreaterId,											
			"Action"=> $action,
			"Old_Value"=> html_entity_decode($result["cbk_master_fam_con"])
		);
		
		$arrReview_GH[] = array(
			"Pk_Id"=> $result["general_id"],
			"Table_Name"=> "general_medicine",	
			"UI_Filed_Name"=> "any_conditions_relative1",	
			"Data_Base_Field_Name"=> "any_conditions_relative ",	
			"Data_Base_Field_Type"=>fun_get_field_type($genMedDataFields,"any_conditions_relative "),									
			"Field_Text"=> "Any condition your family or blood relative have presently or have had in the past (Yes)",											
			"Operater_Id"=> $opreaterId,											
			"Action"=> $action,
			"Old_Value"=> html_entity_decode($result["any_conditions_relative"])
		);
		
		$arrReview_GH[] = array(
			"Pk_Id"=> $result["general_id"],
			"Table_Name"=> "general_medicine",	
			"UI_Filed_Name"=> "any_conditions_relative1_n",	
			"Data_Base_Field_Name"=> "any_conditions_relative1_n",	
			"Data_Base_Field_Type"=>fun_get_field_type($genMedDataFields,"any_conditions_relative1_n"),									
			"Field_Text"=> "Any condition your family or blood relative have presently or have had in the past (No)",											
			"Operater_Id"=> $opreaterId,											
			"Action"=> $action,
			"Old_Value"=> html_entity_decode($result["any_conditions_relative1_n"])
		);
		
		$arrReview_GH[] = array(
			"Pk_Id"=> $result["general_id"],
			"Table_Name"=> "general_medicine",	
			"UI_Filed_Name" => "relDescHighBp",	
			"Data_Base_Field_Name"=> "relDescHighBp",	
			"Data_Base_Field_Type"=>fun_get_field_type($genMedDataFields,"relDescHighBp"),										
			"Field_Text"=> "High Blood Pressure Relative",											
			"Operater_Id"=> $opreaterId,											
			"Action"=> $action,
			"Old_Value"=> html_entity_decode($result["relDescHighBp"])
		);
		
		$strHighBloodPresherTxtRel = $result["desc_high_bp"];
		$strHighBloodPresherTxtRel = $this->get_set_pat_rel_values_retrive_review($strHighBloodPresherTxtRel,"rel",$this->delimiter);				
		$arrReview_GH[] = array(
			"Pk_Id"=> $result["general_id"],
			"Table_Name"=> "general_medicine",
			"UI_Filed_Name" => "relTxtHighBloodPresher",
			"Data_Base_Field_Name"=> "desc_high_bp",	
			"Data_Base_Field_Type"=>fun_get_field_type($genMedDataFields,"desc_high_bp"),											
			"Field_Text"=> "High Blood Pressure Relative Text",											
			"Operater_Id"=> $opreaterId,											
			"Action"=> $action,
			"Old_Value"=> html_entity_decode($strHighBloodPresherTxtRel)
		);
		
		$arrReview_GH[] = array(
			"Pk_Id"=> $result["general_id"],
			"Table_Name"=> "general_medicine",		
			"UI_Filed_Name" => "relDescHeartProb",	
			"Data_Base_Field_Name"=> "desc_heart_problem",	
			"Data_Base_Field_Type"=>fun_get_field_type($genMedDataFields,"desc_heart_problem"),								
			"Field_Text"=> "Heart Problem Relative",											
			"Operater_Id"=> $opreaterId,											
			"Action"=> $action,
			"Old_Value"=> html_entity_decode($result["relDescHeartProb"])
		);
		$strHeartTxtRel = $result["desc_heart_problem"];
		$strHeartTxtRel = $this->get_set_pat_rel_values_retrive_review($strHeartTxtRel,"rel",$this->delimiter);	
		$arrReview_GH[] = array(
			"Pk_Id"=> $result["general_id"],
			"Table_Name"=> "general_medicine",			
			"UI_Filed_Name" => "relTxtHeartProblem",
			"Data_Base_Field_Name"=> "desc_heart_problem",	
			"Data_Base_Field_Type"=>fun_get_field_type($genMedDataFields,"desc_heart_problem"),								
			"Field_Text"=> "Heart Problem Relative Text",											
			"Operater_Id"=> $opreaterId,											
			"Action"=> $action,
			"Old_Value"=> html_entity_decode($strHeartTxtRel)
		);
		$arrReview_GH[] = array(
			"Pk_Id"=> $result["general_id"],
			"Table_Name"=> "general_medicine",		
			"UI_Filed_Name" => "relDescArthritisProb",	
			"Data_Base_Field_Name"=> "relDescArthritisProb",	
			"Data_Base_Field_Type"=>fun_get_field_type($genMedDataFields,"relDescArthritisProb"),								
			"Field_Text"=> "Arthritis Relative",											
			"Operater_Id"=> $opreaterId,											
			"Action"=> $action,
			"Old_Value"=> html_entity_decode($result["relDescArthritisProb"])
		);
		$strArtSubConTxtRel = $result["sub_conditions_you"];
		$strArtSubConTxtRel = $this->get_set_pat_rel_values_retrive_review($strArtSubConTxtRel,"rel",$this->delimiter);	
		$arrReview_GH[] = array(
			"Pk_Id"=> $result["general_id"],
			"Table_Name"=> "general_medicine",	
			"UI_Filed_Name"=> "rel_elem_subCondition_u1",										
			"Field_Text"=> "Arthritis Relative Checkboxes",											
			"Operater_Id"=> $opreaterId,											
			"Action"=> $action,
			"Old_Value"=> html_entity_decode($strArtSubConTxtRel)
		);
		
		$strArthritisTxtRel = $result["desc_arthrities"];
		$strArthritisTxtRel = $this->get_set_pat_rel_values_retrive_review($strArthritisTxtRel,"rel",$this->delimiter);
		$arrReview_GH[] = array(
			"Pk_Id"=> $result["general_id"],
			"Table_Name"=> "general_medicine",
			"UI_Filed_Name"=> "relTxtArthrities",
			"Data_Base_Field_Name"=> "desc_arthrities",	
			"Data_Base_Field_Type"=>fun_get_field_type($genMedDataFields,"desc_arthrities"),											
			"Field_Text"=> "Arthritis Relative Text",											
			"Operater_Id"=> $opreaterId,											
			"Action"=> $action,
			"Old_Value"=> html_entity_decode($strArthritisTxtRel)
		);
		$arrReview_GH[] = array(
			"Pk_Id"=> $result["general_id"],
			"Table_Name"=> "general_medicine",	
			"UI_Filed_Name"=> "relDescLungProb",
			"Data_Base_Field_Name"=> "relDescLungProb",	
			"Data_Base_Field_Type"=>fun_get_field_type($genMedDataFields,"relDescLungProb"),										
			"Field_Text"=> "Lung Problems Relative",											
			"Operater_Id"=> $opreaterId,											
			"Action"=> $action,
			"Old_Value"=> html_entity_decode($result["relDescLungProb"])
		);
		$strLungProblemTxtRel = $result["desc_lung_problem"];
		$strLungProblemTxtRel = $this->get_set_pat_rel_values_retrive_review($strLungProblemTxtRel,"rel",$this->delimiter);
		$arrReview_GH[] = array(
			"Pk_Id"=> $result["general_id"],
			"Table_Name"=> "general_medicine",	
			"UI_Filed_Name"=> "relTxtLungProblem",
			"Data_Base_Field_Name"=> "desc_lung_problem",	
			"Data_Base_Field_Type"=>fun_get_field_type($genMedDataFields,"desc_lung_problem"),											
			"Field_Text"=> "Lung Problems Relative text",											
			"Operater_Id"=> $opreaterId,											
			"Action"=> $action,
			"Old_Value"=> html_entity_decode($strLungProblemTxtRel)
		);
		$arrReview_GH[] = array(
			"Pk_Id"=> $result["general_id"],
			"Table_Name"=> "general_medicine",	
			"UI_Filed_Name"=> "relDescStrokeProb",
			"Data_Base_Field_Name"=> "relDescStrokeProb",	
			"Data_Base_Field_Type"=>fun_get_field_type($genMedDataFields,"relDescStrokeProb"),										
			"Field_Text"=> "Stroke Relative",											
			"Operater_Id"=> $opreaterId,											
			"Action"=> $action,
			"Old_Value"=> html_entity_decode($result["relDescStrokeProb"])
		);
		$strStrokeTxtRel = $result["desc_stroke"];
		$strStrokeTxtRel = $this->get_set_pat_rel_values_retrive_review($strStrokeTxtRel,"rel",$this->delimiter);
		$arrReview_GH[] = array(
			"Pk_Id"=> $result["general_id"],
			"Table_Name"=> "general_medicine",	
			"UI_Filed_Name"=> "relTxtStroke",
			"Data_Base_Field_Name"=> "desc_stroke",	
			"Data_Base_Field_Type"=>fun_get_field_type($genMedDataFields,"desc_stroke"),										
			"Field_Text"=> "Stroke Relative Text",											
			"Operater_Id"=> $opreaterId,											
			"Action"=> $action,
			"Old_Value"=> html_entity_decode($strStrokeTxtRel)
		);
		$arrReview_GH[] = array(
			"Pk_Id"=> $result["general_id"],
			"Table_Name"=> "general_medicine",	
			"UI_Filed_Name"=> "relDescThyroidProb",	
			"Data_Base_Field_Name"=> "relDescThyroidProb",	
			"Data_Base_Field_Type"=>fun_get_field_type($genMedDataFields,"relDescThyroidProb"),										
			"Field_Text"=> "Thyroid Problems Relative",											
			"Operater_Id"=> $opreaterId,											
			"Action"=> $action,
			"Old_Value"=> html_entity_decode($result["relDescThyroidProb"])
		);
		$strThyroidProblemsTxtRel = $result["desc_thyroid_problems"];
		$strThyroidProblemsTxtRel = $this->get_set_pat_rel_values_retrive_review($strThyroidProblemsTxtRel,"rel",$this->delimiter);
		$arrReview_GH[] = array(
			"Pk_Id"=> $result["general_id"],
			"Table_Name"=> "general_medicine",	
			"UI_Filed_Name"=> "relTxtThyroidProblems",
			"Data_Base_Field_Name"=> "desc_thyroid_problems",	
			"Data_Base_Field_Type"=>fun_get_field_type($genMedDataFields,"desc_thyroid_problems"),										
			"Field_Text"=> "Thyroid Problems Relative Text",											
			"Operater_Id"=> $opreaterId,											
			"Action"=> $action,
			"Old_Value"=> html_entity_decode($strThyroidProblemsTxtRel)
		);
		$strDiabetesIdTxtRel = $result["diabetes_values"];
		$strDiabetesIdTxtRel = $this->get_set_pat_rel_values_retrive_review($strDiabetesIdTxtRel,"rel",$this->delimiter);
		$arrReview_GH[] = array(
			"Pk_Id"=> $result["general_id"],
			"Table_Name"=> "general_medicine",	
			"UI_Filed_Name"=> "rel_text_diabetes_id",										
			"Field_Text"=> "Diabetes Relative",											
			"Operater_Id"=> $opreaterId,											
			"Action"=> $action,
			"Old_Value"=> html_entity_decode($strDiabetesIdTxtRel)
		);
		$arrReview_GH[] = array(
			"Pk_Id"=> $result["general_id"],
			"Table_Name"=> "general_medicine",	
			"UI_Filed_Name"=> "elem_desc_r",
			"Data_Base_Field_Name"=> "desc_r",	
			"Data_Base_Field_Type"=>fun_get_field_type($genMedDataFields,"desc_r"),										
			"Field_Text"=> "Relative Diabetes relation",											
			"Operater_Id"=> $opreaterId,											
			"Action"=> $action,
			"Old_Value"=> html_entity_decode($result["desc_r"])
		);
		$strDiabetesTxtRel = $result["desc_u"];
		$strDiabetesTxtRel = $this->get_set_pat_rel_values_retrive_review($strDiabetesTxtRel,"rel",$this->delimiter);
		$arrReview_GH[] = array(
			"Pk_Id"=> $result["general_id"],
			"Table_Name"=> "general_medicine",	
			"UI_Filed_Name"=> "rel_elem_desc_u",
			"Data_Base_Field_Name"=> "desc_u",	
			"Data_Base_Field_Type"=>fun_get_field_type($genMedDataFields,"desc_u"),											
			"Field_Text"=> "Relative Diabetes Text",											
			"Operater_Id"=> $opreaterId,											
			"Action"=> $action,
			"Old_Value"=> html_entity_decode($strDiabetesTxtRel)
		);
		
		$arrReview_GH[] = array(
			"Pk_Id"=> $result["general_id"],
			"Table_Name"=> "general_medicine",	
			"UI_Filed_Name"=> "relDescLDL",
			"Data_Base_Field_Name"=> "relDescLDL",	
			"Data_Base_Field_Type"=>fun_get_field_type($genMedDataFields,"relDescLDL"),										
			"Field_Text"=> "LDL Relative",											
			"Operater_Id"=> $opreaterId,											
			"Action"=> $action,
			"Old_Value"=> html_entity_decode($result["relDescLDL"])
		);
		$strLDLTxtRel = $result["desc_LDL"];
		$strLDLTxtRel = $this->get_set_pat_rel_values_retrive_review($strLDLTxtRel,"rel",$this->delimiter);
		
		$arrReview_GH[] = array(
			"Pk_Id"=> $result["general_id"],
			"Table_Name"=> "general_medicine",	
			"UI_Filed_Name"=> "reltxtLDL",
			"Data_Base_Field_Name"=> "desc_LDL",	
			"Data_Base_Field_Type"=>fun_get_field_type($genMedDataFields,"desc_LDL"),										
			"Field_Text"=> "LDL Relative description",											
			"Operater_Id"=> $opreaterId,											
			"Action"=> $action,
			"Old_Value"=> html_entity_decode($strLDLTxtRel)
		);
		$arrReview_GH[] = array(
			"Pk_Id"=> $result["general_id"],
			"Table_Name"=> "general_medicine",	
			"UI_Filed_Name"=> "relDescUlcersProb",
			"Data_Base_Field_Name"=> "relDescUlcersProb",	
			"Data_Base_Field_Type"=>fun_get_field_type($genMedDataFields,"relDescUlcersProb"),										
			"Field_Text"=> "Ulcers Relative",											
			"Operater_Id"=> $opreaterId,											
			"Action"=> $action,
			"Old_Value"=> html_entity_decode($result["relDescUlcersProb"])
		);
		
		$strUlcersTxtRel = $result["desc_ulcers"];
		$strUlcersTxtRel = $this->get_set_pat_rel_values_retrive_review($strUlcersTxtRel,"rel",$this->delimiter);
		$arrReview_GH[] = array(
			"Pk_Id"=> $result["general_id"],
			"Table_Name"=> "general_medicine",	
			"UI_Filed_Name"=> "relTxtUlcers",
			"Data_Base_Field_Name"=> "desc_ulcers",	
			"Data_Base_Field_Type"=>fun_get_field_type($genMedDataFields,"desc_ulcers"),										
			"Field_Text"=> "Ulcers Relative Text",											
			"Operater_Id"=> $opreaterId,											
			"Action"=> $action,
			"Old_Value"=> html_entity_decode($strUlcersTxtRel)
		);
		
		$arrReview_GH[] = array(
			"Pk_Id"=> $result["general_id"],
			"Table_Name"=> "general_medicine",	
			"UI_Filed_Name"=> "relDescCancerProb",
			"Data_Base_Field_Name"=> "relDescCancerProb",	
			"Data_Base_Field_Type"=>fun_get_field_type($genMedDataFields,"relDescCancerProb"),											
			"Field_Text"=> "Cancer Relative",											
			"Operater_Id"=> $opreaterId,											
			"Action"=> $action,
			"Old_Value"=> html_entity_decode($result["relDescCancerProb"])
		);
		
		$strCancerTxtRel = $result["desc_cancer"];
		$strCancerTxtRel = $this->get_set_pat_rel_values_retrive_review($strCancerTxtRel,"rel",$this->delimiter);
		$arrReview_GH[] = array(
			"Pk_Id"=> $result["general_id"],
			"Table_Name"=> "general_medicine",	
			"UI_Filed_Name"=> "relTxtCancer",
			"Data_Base_Field_Name"=> "desc_cancer",	
			"Data_Base_Field_Type"=>fun_get_field_type($genMedDataFields,"desc_cancer"),										
			"Field_Text"=> "Cancer Relative Text",											
			"Operater_Id"=> $opreaterId,											
			"Action"=> $action,
			"Old_Value"=> html_entity_decode($strCancerTxtRel)
		);
		
		$arrReview_GH[] = array(
			"Pk_Id"=> $result["general_id"],
			"Table_Name"=> "general_medicine",	
			"UI_Filed_Name"=> "any_conditions_others_rel",										
			"Field_Text"=> "Any other condition family or blood relative have presently or have had in the past",											
			"Operater_Id"=> $opreaterId,											
			"Action"=> $action,
			"Old_Value"=> html_entity_decode($anyConditionsOthersBothArrRel)
		);
		
		$strOthersTxtRel = $result["any_conditions_others"];
		$strOthersTxtRel = $this->get_set_pat_rel_values_retrive_review($strOthersTxtRel,"rel",$this->delimiter);
		$arrReview_GH[] = array(
			"Pk_Id"=> $result["general_id"],
			"Table_Name"=> "general_medicine",	
			"UI_Filed_Name"=> "rel_any_conditions_others1",	
			"Data_Base_Field_Name"=> "any_conditions_others",	
			"Data_Base_Field_Type"=>fun_get_field_type($genMedDataFields,"any_conditions_others"),									
			"Field_Text"=> "Any other condition family or blood relative",											
			"Operater_Id"=> $opreaterId,											
			"Action"=> $action,
			"Old_Value"=> html_entity_decode($strOthersTxtRel)
		);
		###############
		
		return $arrReview_GH;
	}
	
	function getReviewArrayAD($result_ado,$opreaterId,$action){
		$arrReview_AD = array();
		$arrReview_AD[] = array(
			"Pk_Id" => $result_ado["ptID"],
			"Table_Name" => "patient_data",
			"UI_Filed_Name" => "ado_option",											
			"Field_Text" => "Advanced Directive",											
			"Operater_Id" => $opreaterId,					
			"Action"=> $action,						
			"Old_Value" => html_entity_decode($result_ado["ptAdoOption"])
		);
		$arrReview_AD[] = array(
			"Pk_Id" => $result_ado["ptID"],
			"Table_Name" => "patient_data",
			"UI_Filed_Name" => "ado_other_txt",											
			"Field_Text" => "Advanced Directive Other",											
			"Operater_Id" => $opreaterId,					
			"Action"=> $action,						
			"Old_Value" => html_entity_decode($result_ado["ptDescAdoOtherTxt"])
		);
		return $arrReview_AD;
	}
	
	function getReviewArraySocial($result,$opreaterId,$action){
		$arrReview_Social = array();
		$socialHisFields = make_field_type_array("social_history");
		$arrReview_Social[] = array(
			"Pk_Id" => $result["social_id"],
			"Table_Name" => "social_history",
			"UI_Filed_Name" => "SmokingStatus",	
			"Data_Base_Field_Name"=> "smoking_status",	
			"Data_Base_Field_Type"=>fun_get_field_type($socialHisFields,"smoking_status"),										
			"Field_Text" => "Smoking Status",											
			"Operater_Id" => $opreaterId,					
			"Action"=> $action,						
			"Old_Value" => html_entity_decode($result["smoking_status"])
		);
		$arrReview_Social[] = array(
			"Pk_Id" => $result["social_id"],
			"Table_Name" => "social_history",
			"UI_Filed_Name" => "source_of_smoke",
			"Data_Base_Field_Name"=> "source_of_smoke",	
			"Data_Base_Field_Type"=>fun_get_field_type($socialHisFields,"source_of_smoke"),											
			"Field_Text" => "Smoke Type",											
			"Operater_Id" => $opreaterId,					
			"Action"=> $action,						
			"Old_Value" => html_entity_decode($result["source_of_smoke"])
		);
		$arrReview_Social[] = array(
			"Pk_Id" => $result["social_id"],
			"Table_Name" => "social_history",
			"UI_Filed_Name" => "source_of_smoke_other",
			"Data_Base_Field_Name"=> "source_of_smoke_other",	
			"Data_Base_Field_Type"=>fun_get_field_type($socialHisFields,"source_of_smoke_other"),											
			"Field_Text" => "Smoke Type Other",											
			"Operater_Id" => $opreaterId,					
			"Action"=> $action,						
			"Old_Value" => html_entity_decode($result["source_of_smoke_other"])
		);
		$arrReview_Social[] = array(
			"Pk_Id" => $result["social_id"],
			"Table_Name" => "social_history",
			"UI_Filed_Name" => "smoke_perday",	
			"Data_Base_Field_Name"=> "smoke_perday",	
			"Data_Base_Field_Type"=>fun_get_field_type($socialHisFields,"smoke_perday"),											
			"Field_Text" => "Smoke Per day",											
			"Operater_Id" => $opreaterId,					
			"Action"=> $action,						
			"Old_Value" => html_entity_decode($result["smoke_perday"])
		);
		
		$arrReview_Social[] = array(
			"Pk_Id" => $result["social_id"],
			"Table_Name" => "social_history",
			"UI_Filed_Name" => "number_of_years_with_smoke",	
			"Data_Base_Field_Name"=> "number_of_years_with_smoke",	
			"Data_Base_Field_Type"=>fun_get_field_type($socialHisFields,"number_of_years_with_smoke"),
			"Field_Text" => "Number of years/months with smoke",
			"Operater_Id" => $opreaterId,	
			"Action"=> $action,			
			"Old_Value" => html_entity_decode($result["number_of_years_with_smoke"]).' '.$result["smoke_years_months"]
		);
		
		$arrReview_Social[] = array(
			"Pk_Id" => $result["social_id"],
			"Table_Name" => "social_history",
			"UI_Filed_Name" => "offered_cessation_counseling",
			"Data_Base_Field_Name"=> "smoke_counseling",	
			"Data_Base_Field_Type"=>fun_get_field_type($socialHisFields,"smoke_counseling"),											
			"Field_Text" => "Offered Cessation Counseling",											
			"Operater_Id" => $opreaterId,					
			"Action"=> $action,						
			"Old_Value" => ($result["smoke_counseling"]) ? html_entity_decode($result["smoke_counseling"]) : "0"
		);
		
		if($result["offeredCessationCounsellingDate"] == "00-00-0000" || $result["offeredCessationCounsellingDate"] == "--"){
			$dateOfferedCessationCounselling = "";			
		}
		else{
			$dateOfferedCessationCounselling = $result["offeredCessationCounsellingDate"];
		
		}				
		$arrReview_Social[] = array(
			"Pk_Id" => $result["social_id"],
			"Table_Name" => "social_history",
			"UI_Filed_Name" => "txtDateOfferedCessationCounselling",											
			"Field_Text" => "Offered Cessation Counseling Date",											
			"Operater_Id" => $opreaterId,					
			"Action"=> $action,						
			"Old_Value" => html_entity_decode($dateOfferedCessationCounselling)
		);
		
		$arrReview_Social[] = array(
			"Pk_Id" => $result["social_id"],
			"Table_Name" => "social_history",
			"UI_Filed_Name" => "cessationCounselling",	
			"Data_Base_Field_Name"=> "cessation_counselling_option",	
			"Data_Base_Field_Type"=>fun_get_field_type($socialHisFields,"cessation_counselling_option"),										
			"Field_Text" => "Offered Cessation Counselling Option",											
			"Operater_Id" => $opreaterId,					
			"Action"=> $action,						
			"Old_Value" => html_entity_decode($result["cessation_counselling_option"])
		);
		
		$arrReview_Social[] = array(
			"Pk_Id" => $result["social_id"],
			"Table_Name" => "social_history",
			"UI_Filed_Name" => "cessationCounsellingOther",	
			"Data_Base_Field_Name"=> "cessation_counselling_other",	
			"Data_Base_Field_Type"=>fun_get_field_type($socialHisFields,"cessation_counselling_other"),										
			"Field_Text" => "Offered Cessation Counselling Other",											
			"Operater_Id" => $opreaterId,					
			"Action"=> $action,						
			"Old_Value" => html_entity_decode($result["cessation_counselling_other"])
		);
		
		$arrReview_Social[] = array(
			"Pk_Id" => $result["social_id"],
			"Table_Name" => "social_history",
			"UI_Filed_Name" => "alcohal",	
			"Data_Base_Field_Name"=> "alcohal",	
			"Data_Base_Field_Type"=>fun_get_field_type($socialHisFields,"alcohal"),										
			"Field_Text" => "Alcohol Type",											
			"Operater_Id" => $opreaterId,					
			"Action"=> $action,						
			"Old_Value" => html_entity_decode($result["alcohal"])
		);
		$arrReview_Social[] = array(
			"Pk_Id" => $result["social_id"],
			"Table_Name" => "social_history",
			"UI_Filed_Name" => "source_of_alcohal_other",	
			"Data_Base_Field_Name"=> "source_of_alcohal_other",	
			"Data_Base_Field_Type"=>fun_get_field_type($socialHisFields,"source_of_alcohal_other"),										
			"Field_Text" => "Alcohol Type Other",											
			"Operater_Id" => $opreaterId,					
			"Action"=> $action,						
			"Old_Value" => html_entity_decode($result["source_of_alcohal_other"])
		);
		$arrReview_Social[] = array(
			"Pk_Id" => $result["social_id"],
			"Table_Name" => "social_history",
			"UI_Filed_Name" => "alcohal_quentity",
			"Data_Base_Field_Name"=> "consumption",	
			"Data_Base_Field_Type"=>fun_get_field_type($socialHisFields,"consumption"),											
			"Field_Text" => "Alcohol consumption",											
			"Operater_Id" => $opreaterId,					
			"Action"=> $action,						
			"Old_Value" => html_entity_decode($result["consumption"])
		);
		$arrReview_Social[] = array(
			"Pk_Id" => $result["social_id"],
			"Table_Name" => "social_history",
			"UI_Filed_Name" => "alcohal_time",	
			"Data_Base_Field_Name"=> "alcohal_time",	
			"Data_Base_Field_Type"=>fun_get_field_type($socialHisFields,"alcohal_time"),										
			"Field_Text" => "Alcohol Frequency",											
			"Operater_Id" => $opreaterId,					
			"Action"=> $action,						
			"Old_Value" => html_entity_decode($result["alcohal_time"])
		);
		$arrReview_Social[] = array(
			"Pk_Id" => $result["social_id"],
			"Table_Name" => "social_history",
			"UI_Filed_Name" => "list_drugs",
			"Data_Base_Field_Name"=> "list_drugs",	
			"Data_Base_Field_Type"=>fun_get_field_type($socialHisFields,"list_drugs"),												
			"Field_Text" => "List any Drugs",											
			"Operater_Id" => $opreaterId,					
			"Action"=> $action,						
			"Old_Value" => html_entity_decode($result["list_drugs"])
		);
		$arrReview_Social[] = array(
			"Pk_Id" => $result["social_id"],
			"Table_Name" => "social_history",
			"UI_Filed_Name" => "elem_otherSocial",	
			"Data_Base_Field_Name"=> "otherSocial",	
			"Data_Base_Field_Type"=>fun_get_field_type($socialHisFields,"otherSocial"),											
			"Field_Text" => "Social More Information",											
			"Operater_Id" => $opreaterId,					
			"Action"=> $action,						
			"Old_Value" => html_entity_decode($result["otherSocial"])
		);
		###############
		$arrReview_Social[] = array(
			"Pk_Id"=> $result["social_id"],
			"Table_Name"=> "social_history",	
			"UI_Filed_Name"=> "radio_family_smoke",	
			"Data_Base_Field_Name"=> "family_smoke",	
			"Data_Base_Field_Type"=>fun_get_field_type($socialHisFields,"family_smoke"),									
			"Field_Text"=> "Family Hx of Smoking",											
			"Operater_Id"=> $opreaterId,											
			"Action"=> $action,
			"Old_Value"=> ($result["family_smoke"]) ? html_entity_decode($result["family_smoke"]) : "0"
		);
		
		$arrReview_Social[] = array(
			"Pk_Id"=> $result["social_id"],
			"Table_Name"=> "social_history",	
			"UI_Filed_Name"=> "smokers_in_relatives",
			"Data_Base_Field_Name"=> "smokers_in_relatives",	
			"Data_Base_Field_Type"=>fun_get_field_type($socialHisFields,"smokers_in_relatives"),										
			"Field_Text"=> "Family Hx of Smoking  Relation",											
			"Operater_Id"=> $opreaterId,											
			"Action"=> $action,
			"Old_Value"=> html_entity_decode($result["smokers_in_relatives"])
		);
		
		$arrReview_Social[] = array(
			"Pk_Id"=> $result["social_id"],
			"Table_Name"=> "social_history",	
			"UI_Filed_Name"=> "smoke_description",
			"Data_Base_Field_Name"=> "smoke_description",	
			"Data_Base_Field_Type"=>fun_get_field_type($socialHisFields,"smoke_description"),										
			"Field_Text"=> "Family Hx of Smoking Description",											
			"Operater_Id"=> $opreaterId,											
			"Action"=> $action,
			"Old_Value"=> html_entity_decode($result["smoke_description"])
		);
		###########################3
		return $arrReview_Social;
	}
	
	function getReviewArrayMedication($med_id,$i,$arrMedData,$opreaterId,$action){
		$arrReview_Medication = array();
		$arrReview_Medication[] = array(
			"Pk_Id" => ($med_id) ? trim($med_id) : "",		
			"Table_Name" => "lists",
			"UI_Filed_Name" => "md_occular".$i,										
			"Field_Text" => "Patient Medication Ocular ".$i,								
			"Operater_Id" => $opreaterId,					
			"Action"=> $action,						
			"Old_Value" => $arrMedData[0]			
		);		
		$arrReview_Medication[] = array(
			"Pk_Id"=> ($med_id) ? trim($med_id) : "",
			"Table_Name"=> "lists",				
			"UI_Filed_Name" => "md_medication".$i,					
			"Field_Text"=> "Patient Medication ".$i,									
			"Operater_Id"=> $opreaterId,											
			"Action"=> $action,
			"Old_Value"=> html_entity_decode($arrMedData[1])
		);
		$arrReview_Medication[] = array(
			"Pk_Id"=> ($med_id) ? trim($med_id) : "",
			"Table_Name"=> "lists",				
			"UI_Filed_Name" => "md_dosage".$i,					
			"Field_Text"=> "Patient Dosage ".$i,									
			"Operater_Id"=> $opreaterId,											
			"Action"=> $action,
			"Old_Value"=> html_entity_decode($arrMedData[2])
		);
		$arrReview_Medication[] = array(
			"Pk_Id"=> ($med_id) ? trim($med_id) : "",
			"Table_Name"=> "lists",	
			"UI_Filed_Name" => "md_sig".$i,										
			"Field_Text"=> "Patient Medication Sig ".$i,									
			"Operater_Id"=> $opreaterId,											
			"Action"=> $action,
			"Old_Value"=> html_entity_decode($arrMedData[3])
		);
		$arrReview_Medication[] = array(
			"Pk_Id"=> ($med_id) ? trim($med_id) : "",
			"Table_Name"=> "lists",
			"UI_Filed_Name" => "md_qty".$i,									
			"Field_Text"=> "Patient Medication Qty ".$i,										
			"Operater_Id"=> $opreaterId,											
			"Action"=> $action,
			"Old_Value"=> html_entity_decode($arrMedData[4])
		);
		$arrReview_Medication[] = array(
			"Pk_Id"=> ($med_id) ? trim($med_id) : "",
			"Table_Name"=> "lists",		
			"UI_Filed_Name" => "md_refills".$i,								
			"Field_Text"=> "Patient Medication Refills ".$i,										
			"Operater_Id"=> $opreaterId,											
			"Action"=> $action,
			"Old_Value"=> html_entity_decode($arrMedData[5])
		);
		$arrReview_Medication[] = array(
			"Pk_Id"=> ($med_id) ? trim($med_id) : "",
			"Table_Name"=> "lists",			
			"UI_Filed_Name" => "md_prescribedby".$i,						
			"Field_Text"=> "Patient Medication Prescribed By ".$i,										
			"Operater_Id"=> $opreaterId,											
			"Action"=> $action,
			"Old_Value"=> html_entity_decode($arrMedData[6])
		);
		$arrReview_Medication[] = array(
			"Pk_Id"=> ($med_id) ? trim($med_id) : "",
			"Table_Name"=> "lists",		
			"UI_Filed_Name" => "md_begindate".$i,								
			"Field_Text"=> "Patient Medication Begin Date ".$i,										
			"Operater_Id"=> $opreaterId,											
			"Action"=> $action,
			"Old_Value"=> ($arrMedData[7]!="" && $arrMedData[7]!="0000-00-00") ? html_entity_decode($arrMedData[7]) : ""
		);
		$arrReview_Medication[] = array(
			"Pk_Id"=> ($med_id) ? trim($med_id) : "",
			"Table_Name"=> "lists",	
			"UI_Filed_Name"=> "md_enddate".$i,									
			"Field_Text"=> "Patient Medication End Date ".$i,										
			"Operater_Id"=> $opreaterId,											
			"Action"=> $action,
			"Old_Value"=> ($arrMedData[8]!="" && $arrMedData[8]!="0000-00-00") ? html_entity_decode($arrMedData[8]) : ""
		);		
		$arrReview_Medication[] = array(
			"Pk_Id"=> ($med_id) ? trim($med_id) : "",
			"Table_Name"=> "lists",
			"UI_Filed_Name"=> "md_comments".$i,									
			"Field_Text"=> "Patient Medication Comments ".$i,									
			"Operater_Id"=> $opreaterId,											
			"Action"=> $action,
			"Old_Value"=> html_entity_decode($arrMedData[9])
		);
		$arrReview_Medication[] = array(
			"Pk_Id"=> ($med_id) ? trim($med_id) : "",
			"Table_Name"=> "lists",	
			"UI_Filed_Name"=> "cbMedicationStatus".$i,									
			"Field_Text"=> "Patient Medication Status ".$i,										
			"Operater_Id"=> $opreaterId,											
			"Action"=> $action,
			"Old_Value"=> ($arrMedData[10]!="") ? html_entity_decode($arrMedData[10]) : ""
		);
		return $arrReview_Medication;
	}
	
	function getReviewArrayMedicationDelete($med_id,$medName,$opreaterId,$action){
		$arrReview_Medication_Delete = array();
		$arrReview_Medication_Delete[] = array(
			"Pk_Id" => $med_id,		
			"Table_Name" => "lists",												
			"Field_Text" => "Patient Medication",								
			"Operater_Id" => $opreaterId,					
			"Action"=> $action,						
			"Old_Value" => $medName			
		);				
		return $arrReview_Medication_Delete;
	}
	
	function getReviewArraySxProcedures($sg_id,$i,$arrSXData,$opreaterId,$action){
		$arrReview_SxProcedures = array();
		$arrReview_SxProcedures[] = array(
			"Pk_Id" => ($sg_id) ? trim($sg_id) : "",		
			"Table_Name" => "lists",
			"UI_Filed_Name" => "sg_occular".$i,										
			"Field_Text" => "Patient Sx/Procedures Ocular ".$i,							
			"Operater_Id" => $opreaterId,					
			"Action"=> $action,						
			"Old_Value" => $arrSXData[0]			
		);		
		$arrReview_SxProcedures[] = array(
			"Pk_Id"=> ($sg_id) ? trim($sg_id) : "",
			"Table_Name"=> "lists",				
			"UI_Filed_Name" => "sg_title".$i,
			"Field_Text"=> "Patient Sx/Procedures Name ".$i,							
			"Operater_Id"=> $opreaterId,											
			"Action"=> $action,
			"Old_Value"=> html_entity_decode($arrSXData[1])
		);
		$arrReview_SxProcedures[] = array(
			"Pk_Id"=> ($sg_id) ? trim($sg_id) : "",
			"Table_Name"=> "lists",				
			"UI_Filed_Name" => "sg_begindate".$i,	
			"Field_Text"=> "Patient Sx/Procedures Date of Surgery ".$i,							
			"Operater_Id"=> $opreaterId,											
			"Action"=> $action,
			"Old_Value"=> ($arrSXData[2]!="" && $arrSXData[2]!="0000-00-00") ? html_entity_decode($arrSXData[2]) : ""
		);
		$arrReview_SxProcedures[] = array(
			"Pk_Id"=> ($sg_id) ? trim($sg_id) : "",
			"Table_Name"=> "lists",	
			"UI_Filed_Name" => "sg_referredby".$i,										
			"Field_Text"=> "Patient Sx/Procedures Physician ".$i,									
			"Operater_Id"=> $opreaterId,											
			"Action"=> $action,
			"Old_Value"=> html_entity_decode($arrSXData[3])
		);
		$arrReview_SxProcedures[] = array(
			"Pk_Id"=> ($sg_id) ? trim($sg_id) : "",
			"Table_Name"=> "lists",
			"UI_Filed_Name" => "sg_comments".$i,								
			"Field_Text"=> "Patient Sx/Procedures Comments ".$i,									
			"Operater_Id"=> $opreaterId,											
			"Action"=> $action,
			"Old_Value"=> html_entity_decode($arrSXData[4])
		);
				
		return $arrReview_SxProcedures;
	}

	function getReviewArraySxProcDelete($med_id,$surgeryName,$opreaterId,$action){
		$arrReview_SxProcedures_Delete = array();
		$arrReview_SxProcedures_Delete[] = array(
			"Pk_Id" => $med_id,		
			"Table_Name" => "lists",												
			"Field_Text" => "Patient Sx/Procedures",								
			"Operater_Id" => $opreaterId,					
			"Action"=> $action,						
			"Old_Value" => $surgeryName			
		);				
		return $arrReview_SxProcedures_Delete;
	}
	
	function getReviewArrayAllergies($ag_id,$i,$arrAllergiesData,$opreaterId,$action){
		$arrReview_Allergies = array();
		$arrReview_Allergies[] = array(
			"Pk_Id" => ($ag_id) ? trim($ag_id) : "",		
			"Table_Name" => "lists",
			"UI_Filed_Name" => "ag_occular_drug".$i,									
			"Field_Text" => "Patient Allergy Drug ".$i,		
			"Operater_Id" => $opreaterId,					
			"Action"=> $action,						
			"Old_Value" => $arrAllergiesData[0]			
		);		
		$arrReview_Allergies[] = array(
			"Pk_Id"=> ($ag_id) ? trim($ag_id) : "",
			"Table_Name"=> "lists",				
			"UI_Filed_Name" => "ag_title".$i,
			"Field_Text"=> "Patient Allergy Name ".$i,						
			"Operater_Id"=> $opreaterId,											
			"Action"=> $action,
			"Old_Value"=> html_entity_decode($arrAllergiesData[1])
		);
		$arrReview_Allergies[] = array(
			"Pk_Id"=> ($ag_id) ? trim($ag_id) : "",
			"Table_Name"=> "lists",				
			"UI_Filed_Name" => "ag_begindate".$i,
			"Field_Text"=> "Patient Allergy Begin Date ".$i,					
			"Operater_Id"=> $opreaterId,											
			"Action"=> $action,
			"Old_Value"=> ($arrAllergiesData[2]!="" && $arrAllergiesData[2]!="0000-00-00") ? html_entity_decode($arrAllergiesData[2]) : ""
		);
		$arrReview_Allergies[] = array(
			"Pk_Id"=> ($ag_id) ? trim($ag_id) : "",
			"Table_Name"=> "lists",	
			"UI_Filed_Name" => "ag_reactions".$i,								
			"Field_Text"=> "Patient Allergy Reactions ".$i,								
			"Operater_Id"=> $opreaterId,											
			"Action"=> $action,
			"Old_Value"=> html_entity_decode($arrAllergiesData[3])
		);
		$arrReview_Allergies[] = array(
			"Pk_Id"=> ($ag_id) ? trim($ag_id) : "",
			"Table_Name"=> "lists",
			"UI_Filed_Name" => "ag_comments".$i,							
			"Field_Text"=> "Patient Allergy Comments ".$i,								
			"Operater_Id"=> $opreaterId,											
			"Action"=> $action,
			"Old_Value"=> html_entity_decode($arrAllergiesData[4])
		);
				
		return $arrReview_Allergies;
	}
	
	function getReviewArrayAllergiesDelete($med_id,$medName,$opreaterId,$action){
		$arrReview_Allergies_Delete = array();
		$arrReview_Allergies_Delete[] = array(
			"Pk_Id" => $med_id,		
			"Table_Name" => "lists",												
			"Field_Text" => "Patient Allergie",								
			"Operater_Id" => $opreaterId,					
			"Action"=> $action,						
			"Old_Value" => $medName			
		);				
		return $arrReview_Allergies_Delete;
	}
	
	function getReviewArrayAllergiesActiveInactive($ag_id,$agName,$opreaterId,$action){
		$arrReview_Allergies_Active_Inactive = array();
		$arrReview_Allergies_Active_Inactive[] = array(
			"Pk_Id" => $ag_id,		
			"Table_Name" => "lists",												
			"Field_Text" => "Patient Allergie $action",								
			"Operater_Id" => $opreaterId,					
			"Action"=> $action,						
			"Old_Value" => $agName			
		);				
		return $arrReview_Allergies_Active_Inactive;
	}
	
	function getReviewArrayImmunization($imm_id,$i,$arrImmData,$opreaterId,$action){
		$arrReview_Immunization = array();
		$arrReview_Immunization[] = array(
			"Pk_Id" => ($imm_id) ? trim($imm_id) : "",		
			"Table_Name" => "immunizations",
			"UI_Filed_Name" => "immunization_name".$i,									
			"Field_Text" => "Patient Immunization Name ".$i,		
			"Operater_Id" => $opreaterId,					
			"Action"=> $action,						
			"Old_Value" => $arrImmData[0]			
		);		
		$arrReview_Immunization[] = array(
			"Pk_Id"=> ($imm_id) ? trim($imm_id) : "",
			"Table_Name"=> "immunizations",				
			"UI_Filed_Name" => "immunization_type".$i,
			"Field_Text"=> "Patient Immunization Type ".$i,						
			"Operater_Id"=> $opreaterId,											
			"Action"=> $action,
			"Old_Value"=> html_entity_decode($arrImmData[1])
		);
		$arrReview_Immunization[] = array(
			"Pk_Id"=> ($imm_id) ? trim($imm_id) : "",
			"Table_Name"=> "immunizations",				
			"UI_Filed_Name" => "immunization_child".$i,
			"Field_Text"=> "Patient Child Immunization ".$i,					
			"Operater_Id"=> $opreaterId,											
			"Action"=> $action,
			"Old_Value"=> ($arrImmData[2]!="" && $arrImmData[2]!="0000-00-00") ? html_entity_decode($arrImmData[2]) : ""
		);
		$arrReview_Immunization[] = array(
			"Pk_Id"=> ($imm_id) ? trim($imm_id) : "",
			"Table_Name"=> "immunizations",	
			"UI_Filed_Name" => "immunization_dose".$i,								
			"Field_Text"=> "Patient Immunization Dose ".$i,								
			"Operater_Id"=> $opreaterId,											
			"Action"=> $action,
			"Old_Value"=> html_entity_decode($arrImmData[3])
		);
		$arrReview_Immunization[] = array(
			"Pk_Id"=> ($imm_id) ? trim($imm_id) : "",
			"Table_Name"=> "immunizations",
			"UI_Filed_Name" => "immunization_Route_and_site".$i,							
			"Field_Text"=> "Patient Immunization Route and Site ".$i,								
			"Operater_Id"=> $opreaterId,											
			"Action"=> $action,
			"Old_Value"=> html_entity_decode($arrImmData[4])
		);
		$arrReview_Immunization[] = array(
			"Pk_Id"=> ($imm_id) ? trim($imm_id) : "",
			"Table_Name"=> "immunizations",
			"UI_Filed_Name" => "immunization_Lot".$i,							
			"Field_Text"=> "Patient Immunization lot# ".$i,								
			"Operater_Id"=> $opreaterId,											
			"Action"=> $action,
			"Old_Value"=> html_entity_decode($arrImmData[5])
		);
		$arrReview_Immunization[] = array(
			"Pk_Id"=> ($imm_id) ? trim($imm_id) : "",
			"Table_Name"=> "immunizations",
			"UI_Filed_Name" => "immunization_Expiration_Date".$i,							
			"Field_Text"=> "Patient expiration date ".$i,								
			"Operater_Id"=> $opreaterId,											
			"Action"=> $action,
			"Old_Value"=> ($arrImmData[6]!="" && $arrImmData[6]!="00-00-0000") ? html_entity_decode($arrImmData[6]) : ""			
		);
		$arrReview_Immunization[] = array(
			"Pk_Id"=> ($imm_id) ? trim($imm_id) : "",
			"Table_Name"=> "immunizations",
			"UI_Filed_Name" => "immunization_Manufacturer".$i,							
			"Field_Text"=> "Patient Immunization manufacturer ".$i,								
			"Operater_Id"=> $opreaterId,											
			"Action"=> $action,
			"Old_Value"=> html_entity_decode($arrImmData[7])
		);
		$arrReview_Immunization[] = array(
			"Pk_Id"=> ($imm_id) ? trim($imm_id) : "",
			"Table_Name"=> "immunizations",
			"UI_Filed_Name" => "immunization_Admin_date".$i,							
			"Field_Text"=> "Patient Immunization Administrated Date ".$i,								
			"Operater_Id"=> $opreaterId,											
			"Action"=> $action,
			"Old_Value"=> ($arrImmData[8]!="" && $arrImmData[8]!="00-00-0000") ? html_entity_decode($arrImmData[8]) : ""			
		);
		$arrReview_Immunization[] = array(
			"Pk_Id"=> ($imm_id) ? trim($imm_id) : "",
			"Table_Name"=> "immunizations",
			"UI_Filed_Name" => "immunization_Admin_time".$i,							
			"Field_Text"=> "Patient Immunization administered time ".$i,								
			"Operater_Id"=> $opreaterId,											
			"Action"=> $action,
			"Old_Value"=> ($arrImmData[9]!="" && $arrImmData[9]!="00:00:00") ? html_entity_decode($arrImmData[9]) : ""			
		);
		$arrReview_Immunization[] = array(
			"Pk_Id"=> ($imm_id) ? trim($imm_id) : "",
			"Table_Name"=> "immunizations",
			"UI_Filed_Name" => "administered_by_id".$i,							
			"Field_Text"=> "Patient Immunization administered by id ".$i,								
			"Operater_Id"=> $opreaterId,
			"Depend_Select"=> "select CONCAT_WS(',',lname,fname) as provider" ,
			"Depend_Table"=> "users" ,
			"Depend_Search"=> "id" ,											
			"Action"=> $action,
			"Old_Value"=> html_entity_decode($arrImmData[10])
		);
		$arrReview_Immunization[] = array(
			"Pk_Id"=> ($imm_id) ? trim($imm_id) : "",
			"Table_Name"=> "immunizations",
			"UI_Filed_Name" => "immunization_consent_date".$i,							
			"Field_Text"=> "Patient Immunization Consent Date ".$i,								
			"Operater_Id"=> $opreaterId,											
			"Action"=> $action,
			"Old_Value"=> ($arrImmData[11]!="" && $arrImmData[11]!="00-00-0000") ? html_entity_decode($arrImmData[11]) : ""				
		);
		$arrReview_Immunization[] = array(
			"Pk_Id"=> ($imm_id) ? trim($imm_id) : "",
			"Table_Name"=> "immunizations",
			"UI_Filed_Name" => "immunization_reaction".$i,							
			"Field_Text"=> "Patient Immunization Reaction ".$i,								
			"Operater_Id"=> $opreaterId,											
			"Action"=> $action,
			"Old_Value"=> html_entity_decode($arrImmData[12])
		);
		$arrReview_Immunization[] = array(
			"Pk_Id"=> ($imm_id) ? trim($imm_id) : "",
			"Table_Name"=> "immunizations",
			"UI_Filed_Name" => "immunization_reaction_date".$i,							
			"Field_Text"=> "Patient Immunization Reaction Date ".$i,								
			"Operater_Id"=> $opreaterId,											
			"Action"=> $action,
			"Old_Value"=> ($arrImmData[13]!="" && $arrImmData[13]!="00-00-0000") ? html_entity_decode($arrImmData[13]) : ""			
		);
		$arrReview_Immunization[] = array(
			"Pk_Id"=> ($imm_id) ? trim($imm_id) : "",
			"Table_Name"=> "immunizations",
			"UI_Filed_Name" => "immunization_reaction_time".$i,							
			"Field_Text"=> "Patient Immunization Reaction Time ".$i,								
			"Operater_Id"=> $opreaterId,											
			"Action"=> $action,
			"Old_Value"=> ($arrImmData[14]!="" && $arrImmData[14]!="00:00:00") ? html_entity_decode($arrImmData[14]) : ""				
		);
		$arrReview_Immunization[] = array(
			"Pk_Id"=> ($imm_id) ? trim($imm_id) : "",
			"Table_Name"=> "immunizations",
			"UI_Filed_Name" => "immunization_comments".$i,							
			"Field_Text"=> "Patient Immunization comments ".$i,								
			"Operater_Id"=> $opreaterId,											
			"Action"=> $action,
			"Old_Value"=> html_entity_decode($arrImmData[15])
		);
				
		return $arrReview_Immunization;
	}
	
	function getReviewArrayImmunizationDelete($med_id,$medName,$opreaterId,$action){
		$getReviewArrayImmunizationDelete = array();
		$getReviewArrayImmunizationDelete[] = array(
			"Pk_Id" => $med_id,		
			"Table_Name" => "lists",												
			"Field_Text" => "Patient Immunization",								
			"Operater_Id" => $opreaterId,					
			"Action"=> $action,						
			"Old_Value" => $medName			
		);				
		return $getReviewArrayImmunizationDelete;
	}
	
	function getReviewArrayFamilyHx($rsOcular,$chronicDescOtherOcular,$actionOccular,$rsGenHealth,$actionGenHealth,$anyConditionsOthersBothArrFamilyHx,$rsSocial,$actionSocial,$opreaterId){
		$arrReview_FamilyHx = array();		
		$arrReview_FamilyHx[] = array(
			"Pk_Id"=> $rsOcular["ocular_id"],
			"Table_Name"=> "ocular",			
			"UI_Filed_Name" => "rel_any_conditions_relative",								
			"Field_Text"=> "Ocular -> Any condition your family member or blood relative have presently or have had in the past",					 									
			"Operater_Id"=> $opreaterId,											
			"Action"=> $actionOccular,
			"Old_Value"=> html_entity_decode($rsOcular['any_conditions_relative'])
		);
		
		$arrReview_FamilyHx[] = array(
			"Pk_Id"=> $rsOcular["ocular_id"],
			"Table_Name"=> "ocular",	
			"UI_Filed_Name"=> "elem_chronicRelative",										
			"Field_Text"=> "Ocular -> Relatives",											
			"Operater_Id"=> $opreaterId,											
			"Action"=> $actionOccular,
			"Old_Value"=> html_entity_decode($rsOcular['chronicRelative'])
		);
				
		$strDesc = $rsOcular['chronicDesc'];
		$strDesc = $this->get_set_pat_rel_values_retrive_review($strDesc,"rel",$this->delimiter);		
		$arrReview_FamilyHx[] = array(
			"Pk_Id"=> $rsOcular["ocular_id"],
			"Table_Name"=> "ocular",	
			"UI_Filed_Name"=> "rel_elem_chronicDesc",										
			"Field_Text"=> "Ocular -> Relative condition value",											
			"Operater_Id"=> $opreaterId,											
			"Action"=> $actionOccular,
			"Old_Value"=> html_entity_decode($strDesc)
		);
		
		$arrReview_FamilyHx[] = array(
			"Pk_Id"=> $rsOcular["ocular_id"],
			"Table_Name"=> "ocular",		
			"UI_Filed_Name" => "rel_any_conditions_other_r",									
			"Field_Text"=> "Ocular -> Any other condition your family member or blood relative have presently or have had in the past",											
			"Operater_Id"=> $opreaterId,											
			"Action"=> $actionOccular,
			"Old_Value"=> ($rsOcular['any_conditions_other_relative']) ? $rsOcular['any_conditions_other_relative'] : ""
		);
		
		$OtherDesc = "";
		$OtherDesc = $this->get_set_pat_rel_values_retrive_review($rsOcular['OtherDesc'],"rel",$this->delimiter);				
		$arrReview_FamilyHx[] = array(
			"Pk_Id"=> $rsOcular["ocular_id"],
			"Table_Name"=> "ocular",	
			"UI_Filed_Name"=> "rel_OtherDesc",										
			"Field_Text"=> "Ocular -> Any other condition",											
			"Operater_Id"=> $opreaterId,											
			"Action"=> $actionOccular,
			"Old_Value"=> html_entity_decode($OtherDesc)
		);
				
		$arrReview_FamilyHx[] = array(
			"Pk_Id"=> $rsOcular["ocular_id"],
			"Table_Name"=> "ocular",
			"UI_Filed_Name"=> "rel_elem_chronicDesc_other",											
			"Field_Text"=> "Ocular -> Any other condition value",											
			"Operater_Id"=> $opreaterId,											
			"Action"=> $actionOccular,
			"Old_Value"=> html_entity_decode($chronicDescOtherOcular)
		);
		
		####################
		$arrReview_FamilyHx[] = array(
			"Pk_Id"=> $rsGenHealth["general_id"],
			"Table_Name"=> "general_medicine",	
			"UI_Filed_Name"=> "any_conditions_relative1",										
			"Field_Text"=> "General Health -> Any condition your family or blood relative have presently or have had in the past",											
			"Operater_Id"=> $opreaterId,											
			"Action"=> $actionGenHealth,
			"Old_Value"=> html_entity_decode($rsGenHealth["any_conditions_relative"])
		);
		
		$arrReview_FamilyHx[] = array(
			"Pk_Id"=> $rsGenHealth["general_id"],
			"Table_Name"=> "general_medicine",	
			"UI_Filed_Name" => "relDescHighBp",											
			"Field_Text"=> "General Health -> High Blood Pressure Relative",											
			"Operater_Id"=> $opreaterId,											
			"Action"=> $actionGenHealth,
			"Old_Value"=> html_entity_decode($rsGenHealth["relDescHighBp"])
		);
		
		$strHighBloodPresherTxtRel = $rsGenHealth["desc_high_bp"];
		$strHighBloodPresherTxtRel = $this->get_set_pat_rel_values_retrive_review($strHighBloodPresherTxtRel,"rel",$this->delimiter);				
		$arrReview_FamilyHx[] = array(
			"Pk_Id"=> $rsGenHealth["general_id"],
			"Table_Name"=> "general_medicine",
			"UI_Filed_Name" => "relTxtHighBloodPresher",
			"Data_Base_Field_Name"=> "desc_high_bp",	
			"Data_Base_Field_Type"=>fun_get_field_type($genMedDataFields,"desc_high_bp"),											
			"Field_Text"=> "General Health -> High Blood Pressure Text",											
			"Operater_Id"=> $opreaterId,											
			"Action"=> $actionGenHealth,
			"Old_Value"=> html_entity_decode($strHighBloodPresherTxtRel)
		);
		
		$arrReview_FamilyHx[] = array(
			"Pk_Id"=> $rsGenHealth["general_id"],
			"Table_Name"=> "general_medicine",		
			"UI_Filed_Name" => "relDescHeartProb",
			"Data_Base_Field_Name"=> "desc_heart_problem",	
			"Data_Base_Field_Type"=>fun_get_field_type($genMedDataFields,"desc_heart_problem"),									
			"Field_Text"=> "General Health -> Heart Problem Relative",											
			"Operater_Id"=> $opreaterId,											
			"Action"=> $actionGenHealth,
			"Old_Value"=> html_entity_decode($rsGenHealth["relDescHeartProb"])
		);
		$strHeartTxtRel = $rsGenHealth["desc_heart_problem"];
		$strHeartTxtRel = $this->get_set_pat_rel_values_retrive_review($strHeartTxtRel,"rel",$this->delimiter);	
		$arrReview_FamilyHx[] = array(
			"Pk_Id"=> $rsGenHealth["general_id"],
			"Table_Name"=> "general_medicine",			
			"UI_Filed_Name" => "relTxtHeartProblem",
			"Data_Base_Field_Name"=> "desc_heart_problem",	
			"Data_Base_Field_Type"=>fun_get_field_type($genMedDataFields,"desc_heart_problem"),								
			"Field_Text"=> "General Health -> Heart Problem Text",											
			"Operater_Id"=> $opreaterId,											
			"Action"=> $actionGenHealth,
			"Old_Value"=> html_entity_decode($strHeartTxtRel)
		);
		$arrReview_FamilyHx[] = array(
			"Pk_Id"=> $rsGenHealth["general_id"],
			"Table_Name"=> "general_medicine",		
			"UI_Filed_Name" => "relDescArthritisProb",									
			"Field_Text"=> "General Health -> Arthritis Relative",											
			"Operater_Id"=> $opreaterId,											
			"Action"=> $actionGenHealth,
			"Old_Value"=> html_entity_decode($rsGenHealth["relDescArthritisProb"])
		);
		$strArtSubConTxtRel = $rsGenHealth["sub_conditions_you"];
		$strArtSubConTxtRel = $this->get_set_pat_rel_values_retrive_review($strArtSubConTxtRel,"rel",$this->delimiter);	
		$arrReview_FamilyHx[] = array(
			"Pk_Id"=> $rsGenHealth["general_id"],
			"Table_Name"=> "general_medicine",	
			"UI_Filed_Name"=> "rel_elem_subCondition_u1",										
			"Field_Text"=> "General Health -> Arthritis Checkboxes",											
			"Operater_Id"=> $opreaterId,											
			"Action"=> $actionGenHealth,
			"Old_Value"=> html_entity_decode($strArtSubConTxtRel)
		);
		
		$strArthritisTxtRel = $rsGenHealth["desc_arthrities"];
		$strArthritisTxtRel = $this->get_set_pat_rel_values_retrive_review($strArthritisTxtRel,"rel",$this->delimiter);
		$arrReview_FamilyHx[] = array(
			"Pk_Id"=> $rsGenHealth["general_id"],
			"Table_Name"=> "general_medicine",
			"UI_Filed_Name"=> "relTxtArthrities",
			"Data_Base_Field_Name"=> "desc_arthrities",	
			"Data_Base_Field_Type"=>fun_get_field_type($genMedDataFields,"desc_arthrities"),											
			"Field_Text"=> "General Health -> Arthritis Text",											
			"Operater_Id"=> $opreaterId,											
			"Action"=> $actionGenHealth,
			"Old_Value"=> html_entity_decode($strArthritisTxtRel)
		);
		$arrReview_FamilyHx[] = array(
			"Pk_Id"=> $rsGenHealth["general_id"],
			"Table_Name"=> "general_medicine",	
			"UI_Filed_Name"=> "relDescLungProb",										
			"Field_Text"=> "General Health -> Lung Problems Relative",											
			"Operater_Id"=> $opreaterId,											
			"Action"=> $actionGenHealth,
			"Old_Value"=> html_entity_decode($rsGenHealth["relDescLungProb"])
		);
		$strLungProblemTxtRel = $rsGenHealth["desc_lung_problem"];
		$strLungProblemTxtRel = $this->get_set_pat_rel_values_retrive_review($strLungProblemTxtRel,"rel",$this->delimiter);
		$arrReview_FamilyHx[] = array(
			"Pk_Id"=> $rsGenHealth["general_id"],
			"Table_Name"=> "general_medicine",	
			"UI_Filed_Name"=> "relTxtLungProblem",	
			"Data_Base_Field_Name"=> "desc_lung_problem",	
			"Data_Base_Field_Type"=>fun_get_field_type($genMedDataFields,"desc_lung_problem"),										
			"Field_Text"=> "General Health -> Lung Problems text",											
			"Operater_Id"=> $opreaterId,											
			"Action"=> $actionGenHealth,
			"Old_Value"=> html_entity_decode($strLungProblemTxtRel)
		);
		$arrReview_FamilyHx[] = array(
			"Pk_Id"=> $rsGenHealth["general_id"],
			"Table_Name"=> "general_medicine",	
			"UI_Filed_Name"=> "relDescStrokeProb",										
			"Field_Text"=> "General Health -> Stroke Relative",											
			"Operater_Id"=> $opreaterId,											
			"Action"=> $actionGenHealth,
			"Old_Value"=> html_entity_decode($rsGenHealth["relDescStrokeProb"])
		);
		$strStrokeTxtRel = $rsGenHealth["desc_stroke"];
		$strStrokeTxtRel = $this->get_set_pat_rel_values_retrive_review($strStrokeTxtRel,"rel",$this->delimiter);
		$arrReview_FamilyHx[] = array(
			"Pk_Id"=> $rsGenHealth["general_id"],
			"Table_Name"=> "general_medicine",	
			"UI_Filed_Name"=> "relTxtStroke",
			"Data_Base_Field_Name"=> "desc_stroke",	
			"Data_Base_Field_Type"=>fun_get_field_type($genMedDataFields,"desc_stroke"),										
			"Field_Text"=> "General Health -> Stroke Text",											
			"Operater_Id"=> $opreaterId,											
			"Action"=> $actionGenHealth,
			"Old_Value"=> html_entity_decode($strStrokeTxtRel)
		);
		$arrReview_FamilyHx[] = array(
			"Pk_Id"=> $rsGenHealth["general_id"],
			"Table_Name"=> "general_medicine",	
			"UI_Filed_Name"=> "relDescThyroidProb",										
			"Field_Text"=> "General Health -> Thyroid Problems Relative",											
			"Operater_Id"=> $opreaterId,											
			"Action"=> $actionGenHealth,
			"Old_Value"=> html_entity_decode($rsGenHealth["relDescThyroidProb"])
		);
		$strThyroidProblemsTxtRel = $rsGenHealth["desc_thyroid_problems"];
		$strThyroidProblemsTxtRel = $this->get_set_pat_rel_values_retrive_review($strThyroidProblemsTxtRel,"rel",$this->delimiter);
		$arrReview_FamilyHx[] = array(
			"Pk_Id"=> $rsGenHealth["general_id"],
			"Table_Name"=> "general_medicine",	
			"UI_Filed_Name"=> "relTxtThyroidProblems",	
			"Data_Base_Field_Name"=> "desc_thyroid_problems",	
			"Data_Base_Field_Type"=>fun_get_field_type($genMedDataFields,"desc_thyroid_problems"),									
			"Field_Text"=> "General Health -> Thyroid Problems Text",											
			"Operater_Id"=> $opreaterId,											
			"Action"=> $actionGenHealth,
			"Old_Value"=> html_entity_decode($strThyroidProblemsTxtRel)
		);
		$strDiabetesIdTxtRel = $rsGenHealth["diabetes_values"];
		$strDiabetesIdTxtRel = $this->get_set_pat_rel_values_retrive_review($strDiabetesIdTxtRel,"rel",$this->delimiter);
		$arrReview_FamilyHx[] = array(
			"Pk_Id"=> $rsGenHealth["general_id"],
			"Table_Name"=> "general_medicine",	
			"UI_Filed_Name"=> "rel_text_diabetes_id",										
			"Field_Text"=> "General Health -> Diabetes",											
			"Operater_Id"=> $opreaterId,											
			"Action"=> $actionGenHealth,
			"Old_Value"=> html_entity_decode($strDiabetesIdTxtRel)
		);
		$arrReview_FamilyHx[] = array(
			"Pk_Id"=> $rsGenHealth["general_id"],
			"Table_Name"=> "general_medicine",	
			"UI_Filed_Name"=> "elem_desc_r",										
			"Field_Text"=> "General Health -> Diabetes relation",											
			"Operater_Id"=> $opreaterId,											
			"Action"=> $actionGenHealth,
			"Old_Value"=> html_entity_decode($rsGenHealth["desc_r"])
		);
		$strDiabetesTxtRel = $rsGenHealth["desc_u"];
		$strDiabetesTxtRel = $this->get_set_pat_rel_values_retrive_review($strDiabetesTxtRel,"rel",$this->delimiter);
		$arrReview_FamilyHx[] = array(
			"Pk_Id"=> $rsGenHealth["general_id"],
			"Table_Name"=> "general_medicine",	
			"UI_Filed_Name"=> "rel_elem_desc_u",
			"Data_Base_Field_Name"=> "desc_u",	
			"Data_Base_Field_Type"=>fun_get_field_type($genMedDataFields,"desc_u"),										
			"Field_Text"=> "General Health -> Diabetes Text",											
			"Operater_Id"=> $opreaterId,											
			"Action"=> $actionGenHealth,
			"Old_Value"=> html_entity_decode($strDiabetesTxtRel)
		);
		
		$arrReview_FamilyHx[] = array(
			"Pk_Id"=> $rsGenHealth["general_id"],
			"Table_Name"=> "general_medicine",	
			"UI_Filed_Name"=> "relDescLDL",										
			"Field_Text"=> "General Health -> LDL Relative",											
			"Operater_Id"=> $opreaterId,											
			"Action"=> $actionGenHealth,
			"Old_Value"=> html_entity_decode($rsGenHealth["relDescLDL"])
		);
		$strLDLTxtRel = $rsGenHealth["desc_LDL"];
		$strLDLTxtRel = $this->get_set_pat_rel_values_retrive_review($strLDLTxtRel,"rel",$this->delimiter);
		
		$arrReview_FamilyHx[] = array(
			"Pk_Id"=> $rsGenHealth["general_id"],
			"Table_Name"=> "general_medicine",	
			"UI_Filed_Name"=> "reltxtLDL",
			"Data_Base_Field_Name"=> "desc_LDL",	
			"Data_Base_Field_Type"=>fun_get_field_type($genMedDataFields,"desc_LDL"),										
			"Field_Text"=> "General Health -> LDL description",											
			"Operater_Id"=> $opreaterId,											
			"Action"=> $actionGenHealth,
			"Old_Value"=> html_entity_decode($strLDLTxtRel)
		);
		$arrReview_FamilyHx[] = array(
			"Pk_Id"=> $rsGenHealth["general_id"],
			"Table_Name"=> "general_medicine",	
			"UI_Filed_Name"=> "relDescUlcersProb",										
			"Field_Text"=> "General Health -> Ulcers Relative",											
			"Operater_Id"=> $opreaterId,											
			"Action"=> $actionGenHealth,
			"Old_Value"=> html_entity_decode($rsGenHealth["relDescUlcersProb"])
		);
		
		$strUlcersTxtRel = $rsGenHealth["desc_ulcers"];
		$strUlcersTxtRel = $this->get_set_pat_rel_values_retrive_review($strUlcersTxtRel,"rel",$this->delimiter);
		$arrReview_FamilyHx[] = array(
			"Pk_Id"=> $rsGenHealth["general_id"],
			"Table_Name"=> "general_medicine",	
			"UI_Filed_Name"=> "relTxtUlcers",
			"Data_Base_Field_Name"=> "desc_ulcers",	
			"Data_Base_Field_Type"=>fun_get_field_type($genMedDataFields,"desc_ulcers"),										
			"Field_Text"=> "General Health -> Ulcers Text",											
			"Operater_Id"=> $opreaterId,											
			"Action"=> $actionGenHealth,
			"Old_Value"=> html_entity_decode($strUlcersTxtRel)
		);
		
		$arrReview_FamilyHx[] = array(
			"Pk_Id"=> $rsGenHealth["general_id"],
			"Table_Name"=> "general_medicine",	
			"UI_Filed_Name"=> "relDescCancerProb",										
			"Field_Text"=> "General Health -> Cancer Relative",											
			"Operater_Id"=> $opreaterId,											
			"Action"=> $actionGenHealth,
			"Old_Value"=> html_entity_decode($rsGenHealth["relDescCancerProb"])
		);
		
		$strCancerTxtRel = $rsGenHealth["desc_cancer"];
		$strCancerTxtRel = $this->get_set_pat_rel_values_retrive_review($strCancerTxtRel,"rel",$this->delimiter);
		$arrReview_FamilyHx[] = array(
			"Pk_Id"=> $rsGenHealth["general_id"],
			"Table_Name"=> "general_medicine",	
			"UI_Filed_Name"=> "relTxtCancer",
			"Data_Base_Field_Name"=> "desc_cancer",	
			"Data_Base_Field_Type"=>fun_get_field_type($genMedDataFields,"desc_cancer"),										
			"Field_Text"=> "General Health -> Cancer Text",											
			"Operater_Id"=> $opreaterId,											
			"Action"=> $actionGenHealth,
			"Old_Value"=> html_entity_decode($strCancerTxtRel)
		);
				
		/*$arrReview_FamilyHx[] = array(
			"Pk_Id"=> $rsGenHealth["general_id"],
			"Table_Name"=> "general_medicine",	
			"UI_Filed_Name"=> "chk_under_control",										
			"Field_Text"=> "Any Condition You Have Presently Or Have Had In The Past Under Control",											
			"Operater_Id"=> $opreaterId,											
			"Action"=> $actionGenHealth,
			"Old_Value"=> html_entity_decode($rsGenHealth["chk_under_control"])
		);*/
		$arrReview_FamilyHx[] = array(
			"Pk_Id"=> $rsGenHealth["general_id"],
			"Table_Name"=> "general_medicine",	
			"UI_Filed_Name"=> "any_conditions_others_rel",										
			"Field_Text"=> "General Health -> Any other condition family or blood relative have presently or have had in the past",											
			"Operater_Id"=> $opreaterId,											
			"Action"=> $actionGenHealth,
			"Old_Value"=> html_entity_decode($anyConditionsOthersBothArrFamilyHx)
		);
		$arrReview_FamilyHx[] = array(
			"Pk_Id"=> $rsGenHealth["general_id"],
			"Table_Name"=> "general_medicine",	
			"UI_Filed_Name"=> "ghRelDescOthers",										
			"Field_Text"=> "General Health -> Other blood relative description",											
			"Operater_Id"=> $opreaterId,											
			"Action"=> $actionGenHealth,
			"Old_Value"=> html_entity_decode($rsGenHealth["ghRelDescOthers"])
		);
		
		$strOthersTxtRel = $rsGenHealth["any_conditions_others"];
		$strOthersTxtRel = $this->get_set_pat_rel_values_retrive_review($strOthersTxtRel,"rel",$this->delimiter);
		$arrReview_FamilyHx[] = array(
			"Pk_Id"=> $rsGenHealth["general_id"],
			"Table_Name"=> "general_medicine",	
			"UI_Filed_Name"=> "rel_any_conditions_others1",	
			"Data_Base_Field_Name"=> "any_conditions_others",	
			"Data_Base_Field_Type"=>fun_get_field_type($genMedDataFields,"any_conditions_others"),									
			"Field_Text"=> "General Health -> Any other condition family or blood relative",											
			"Operater_Id"=> $opreaterId,											
			"Action"=> $actionGenHealth,
			"Old_Value"=> html_entity_decode($strOthersTxtRel)
		);
		################################
		
		$arrReview_FamilyHx[] = array(
			"Pk_Id"=> $rsSocial["social_id"],
			"Table_Name"=> "social_history",	
			"UI_Filed_Name"=> "radio_family_smoke",										
			"Field_Text"=> "Social -> Family Hx of Smoking",											
			"Operater_Id"=> $opreaterId,											
			"Action"=> $actionSocial,
			"Old_Value"=> ($rsSocial["family_smoke"]) ? html_entity_decode($rsSocial["family_smoke"]) : "0"
		);
		
		$arrReview_FamilyHx[] = array(
			"Pk_Id"=> $rsSocial["social_id"],
			"Table_Name"=> "social_history",	
			"UI_Filed_Name"=> "smokers_in_relatives",										
			"Field_Text"=> "Social -> Relation",											
			"Operater_Id"=> $opreaterId,											
			"Action"=> $actionSocial,
			"Old_Value"=> html_entity_decode($rsSocial["smokers_in_relatives"])
		);
		
		$arrReview_FamilyHx[] = array(
			"Pk_Id"=> $rsSocial["social_id"],
			"Table_Name"=> "social_history",	
			"UI_Filed_Name"=> "smoke_description",										
			"Field_Text"=> "Social -> Description",											
			"Operater_Id"=> $opreaterId,											
			"Action"=> $actionSocial,
			"Old_Value"=> html_entity_decode($rsSocial["smoke_description"])
		);
		return $arrReview_FamilyHx;
	}
	Public function gethost( $ip )
	{
		//Make sure the input is not going to do anything unexpected
		//IPs must be in the form x.x.x.x with each x as a number
	
		if( preg_match( '/^(?:25[0-5]|2[0-4]\d|1\d\d|[1-9]\d|\d)(?:[.](?:25[0-5]|2[0-4]\d|1\d\d|[1-9]\d|\d)){3}$/', $ip ) )
		{
			$host = `host -s -W 1 $ip`;
			$host = ( $host ? end( explode( ' ', trim( trim( $host ), '.' ) ) ) : $ip );
			if( in_array( $host, Array( 'reached', 'record', '2(SERVFAIL)', '3(NXDOMAIN)' ) ) )
			{
				return sprintf( '(error fetching domain name for %s)', $ip );
			}
			else
			{
				return $host;
			}
		}
		else
		{
			return '(invalid IP address)';
		}
	}
	function reviewMedHx($arrReview,$opreaterId,$sectionName,$patientId,$debug = 0,$debugArray = 0){
	//$debug = 1;
		global $cls_notifications;		
		$readyToInsert = false;
		$auditOpreaterId = $opreaterId;			
		$auditIP = $this->auditIP;
		$auditURL = $_SERVER['PHP_SELF'];													 
		$auditOS = getOS();
		$auditBrowserInfoArr = array();
		$auditBrowserInfoArr = _browser();
		$auditBrowserInfo = $auditBrowserInfoArr['browser'] . "-" .$auditBrowserInfoArr['version'];
		$auditBrowserName = str_replace(";","",$auditBrowserInfo);	
		$policyStatus = (int)$_SESSION['AUDIT_POLICIES']['Patient_record_Created_Viewed_Updated'];
		if($policyStatus == 1){				
		// $auditMachineName = $this->gethost($_SERVER['REMOTE_ADDR']) ? $this->gethost($_SERVER['REMOTE_ADDR']) : $this->auditMac;
		$auditMachineName = gethostbyaddr($_SERVER['REMOTE_ADDR']);		
		}
		$auditOpType = get_operator_type($opreaterId);
		$auditPatId = $patientId;
		$auditSectionName = "";
		switch ($sectionName):
			case "Ocular Hx":
				$auditSectionName = "ocular";
				break;
			case "General Health":
				$auditSectionName = "general_medicine";
				break;
			case "Medications":
			case "Medication":
				$auditSectionName = "medication";
				break;
			case "Sx/Procedure":
				$auditSectionName = "surgeries";
				break;
			case "Allergies":
				$auditSectionName = "allergies";
				break;
			case "Immunizations":
				$auditSectionName = "immunizations";
				break;
			case "Problem List":
				$auditSectionName = "problems";
				break;										
			case "Lab":
				$auditSectionName = "Lab";
				break;						
			case "Radiology":
				$auditSectionName = "Radiology";
				break;
			case "Vital Sign":
				$auditSectionName = "Vital Sign";
				break;									
		endswitch;
		
		
		$insertQryReview = "insert into patient_last_examined_child (master_pat_last_exam_id,field_text,field_name,date_time,section_table_name,section_table_primary_key,old_value,new_value,Depend_Select,Depend_Table,Depend_Search,action,operator_id) VALUES ";
		$insertQryAuditTrail = "insert into audit_trail (Pk_Id,Table_Name,Field_Label,Old_Value,New_Value,Operater_Id,IP,URL,Category,Category_Desc,Action,Date_Time,Query_Success,
									Operater_Type,MAC_Address,Browser_Type,OS,Machine_Name,Depend_Select,Depend_Table,Depend_Search,Filed_Text,pid,Data_Base_Field_Name,Data_Base_Field_Type) VALUES ";						
		foreach ((array)$arrReview as $key => $value) {	
			if(trim($arrReview[$key]["New_Value"])==""){
				$oldVal = trim(strtolower($arrReview[$key]["Old_Value"]));
				$field_val = trim(html_entity_decode($_REQUEST[$arrReview[$key]["UI_Filed_Name"]]));
				if($field_val=='00-00-0000' || $field_val=='0000-00-00'){
					$field_val='';
				}
				if(preg_match("/([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})/",$oldVal)>0){
					$field_val = getDateFormatDB($field_val);
					
				}elseif(preg_match("/([0-9]{1,2})-([0-9]{1,2})-([0-9]{4})/",$oldVal)>0){
					$field_val = get_date_format($field_val,inter_date_format(),'mm-dd-yyyy');
				}
				$arrReview[$key]["New_Value"] = $field_val;
			}
			
			$action = false;
			if(trim($arrReview[$key]["Old_Value"])=="" && trim($arrReview[$key]["New_Value"])!="" && trim($arrReview[$key]["Action"])=="update") {
				$arrReview[$key]["Action"] = "add";	
			}
			if(trim($arrReview[$key]["Action"]) == "update"){
				$oldVal = "";
				$newval = "";
				$oldVal = trim(strtolower($arrReview[$key]["Old_Value"]));
				$newval = trim(strtolower($arrReview[$key]["New_Value"]));

				if($oldVal != $newval){
					if(strcmp($oldVal,$newval) != 0){						
						$action = true;
						$readyToInsert = true;
					}
					if((trim($oldVal) == '' && $newval == "0") || ($oldVal =='0000-00-00' && $newval == "")){
						$action = false;
						$readyToInsert = false;
					}
					elseif($oldVal == "0" && $newval == ''){
						$action = false;
						$readyToInsert = false;
					}else{
						$action = true;
						$readyToInsert = true;
					}
					if($arrReview[$key]["Data_Base_Field_Name"] == "chk_child_immunization"){
					}
				}
				elseif(is_int(trim($arrReview[$key]["Old_Value"]) == true) && is_int(trim($arrReview[$key]["New_Value"]) == true)){
					$oldValue = (int)trim($arrReview[$key]["Old_Value"]);
					$newValue = (int)trim($arrReview[$key]["New_Value"]);
					if($oldValue != $newValue){
						$action = true;
						$readyToInsert = true;
					}
				}
			}
			elseif(trim($arrReview[$key]["Action"]) == "add" || trim($arrReview[$key]["Action"]) == "consolidate"){
				$oldVal = "";
				$newval = "";
				if(trim($arrReview[$key]["Action"]) == "add") {
					$arrReview[$key]["Old_Value"]="";
				}
				$oldVal = trim(strtolower($arrReview[$key]["Old_Value"]));
				$newval = trim(strtolower($arrReview[$key]["New_Value"]));
				if(trim($arrReview[$key]["New_Value"])!=""){			
					$action = true;
					$readyToInsert = true;
					
					if((trim($oldVal) == '' && $newval == "0") || ($oldVal =='0000-00-00' && $newval == "")){
						$action = false;
						$readyToInsert = false;
					}
					elseif($oldVal == "0"  && $newval == ''){
						$action = false;
						$readyToInsert = false;
					}
					else{
						$action = true;
						$readyToInsert = true;
					}
					
				}
				
			}
			elseif(trim($arrReview[$key]["Action"]) == "InActive" || trim($arrReview[$key]["Action"]) == "Active"){
				$action = true;
				$readyToInsert = true;
			}
			elseif(trim($arrReview[$key]["Action"]) == "delete"){
				$action = true;
				$readyToInsert = true;
			}
			elseif(trim($arrReview[$key]["Action"]) == "view"){			
				$action = true;
				$readyToInsert = true;
			}
			elseif(trim($arrReview[$key]["Action"]) == "app_start"){			
				$action = true;
				$readyToInsert = true;
			}
			elseif(trim($arrReview[$key]["Action"]) == "user_login_s"){			
				$action = true;
				$readyToInsert = true;
			}
			elseif(trim($arrReview[$key]["Action"]) == "user_logout_s"){			
				$action = true;
				$readyToInsert = true;
			}
			elseif(trim($arrReview[$key]["Action"]) == "app_stop"){			
				$action = true;
				$readyToInsert = true;
			}
			elseif(trim($arrReview[$key]["Action"]) == "user_login_f"){			
				$action = true;
				$readyToInsert = true;
			}
			elseif(trim($arrReview[$key]["Action"]) == "user_session_timeout_s"){			
				$action = true;
				$readyToInsert = true;
			}
			elseif(trim($arrReview[$key]["Action"]) == "query_search"){			
				$action = true;
				$readyToInsert = true;
			}
			elseif(trim($arrReview[$key]["Action"]) == "phi_export"){			
				$action = true;
				$readyToInsert = true;
			}
			elseif(trim($arrReview[$key]["Action"]) == "sig_create"){			
				$action = true;
				$readyToInsert = true;
			}
			if($action == true){
				$strPHPDAteTime = date("Y-m-d H:i:s");	
				
				$insertQryReview .= "(
										'master_pat_last_exam_id_val',
										'".imw_real_escape_string(htmlentities($arrReview[$key]["Field_Text"]))."',
										'".imw_real_escape_string(htmlentities($arrReview[$key]["UI_Filed_Name"]))."',
										'".$strPHPDAteTime."',
										'".imw_real_escape_string(htmlentities($arrReview[$key]["Table_Name"]))."',
										'".imw_real_escape_string(htmlentities($arrReview[$key]["Pk_Id"]))."',
										'".imw_real_escape_string(htmlentities($arrReview[$key]["Old_Value"]))."',
										'".imw_real_escape_string(htmlentities($arrReview[$key]["New_Value"]))."',
										'".trim(imw_real_escape_string(htmlentities($arrReview[$key]["Depend_Select"])))."',
										'".trim(imw_real_escape_string(htmlentities($arrReview[$key]["Depend_Table"])))."',
										'".trim(imw_real_escape_string(htmlentities($arrReview[$key]["Depend_Search"])))."',
										'".$arrReview[$key]["Action"]."',
										'".$opreaterId."'
									),								
									";
									
					
				$insertQryAuditTrail .= "(
										 '".$arrReview[$key]["Pk_Id"]."',
										 '".imw_real_escape_string(htmlentities($arrReview[$key]["Table_Name"]))."',
										 '".imw_real_escape_string(htmlentities($arrReview[$key]["UI_Filed_Name"]))."',
										 '".imw_real_escape_string(htmlentities($arrReview[$key]["Old_Value"]))."',
										 '".imw_real_escape_string(htmlentities($arrReview[$key]["New_Value"]))."',
										 '".$auditOpreaterId."',
										 '".$auditIP."',
										 '".$auditURL."',
										 'patient_info-medical_history',
										 '".$auditSectionName."',
										 '".$arrReview[$key]["Action"]."',
										 '".$strPHPDAteTime."',
										 '0',										 										 
										 '".$auditOpType."',
										 '".$_REQUEST['macaddrs']."',
										 '".$auditBrowserName."',
										 '".$auditOS."',
										 '".$auditMachineName."',
										 '".trim(addslashes($arrReview[$key]["Depend_Select"]))."',
										 '".trim(addslashes($arrReview[$key]["Depend_Table"]))."',
										 '".trim(addslashes($arrReview[$key]["Depend_Search"]))."',
										 '".trim(addslashes($arrReview[$key]["Field_Text"]))."',
										 '".$auditPatId."',
										 '".trim(addslashes($arrReview[$key]["Data_Base_Field_Name"]))."',
										 '".trim(addslashes($arrReview[$key]["Data_Base_Field_Type"]))."'
									 ),								
									";	

			}	
		}
		$masterPatLastExamId = 0;
		if($debug == 0){
			if($insertQryAuditTrail != ''){				
				$qrySelPatLastExamined = "select patient_last_examined_id from patient_last_examined 
											where patient_id = '".$patientId."' and operator_id = '".$opreaterId."' and section_name = '".$sectionName."' and save_or_review = '1'";
				$rsSelPatLastExamined = imw_query($qrySelPatLastExamined);				
				if($rsSelPatLastExamined){
					if(imw_num_rows($rsSelPatLastExamined) > 0){
						$rowSelPatLastExamined = imw_fetch_array($rsSelPatLastExamined);
						$masterPatLastExamId = $rowSelPatLastExamined['patient_last_examined_id']; 
						$strPHPDAteTime = date("Y-m-d H:i:s");
						$qryUpdatePatLastExamined = "update patient_last_examined set created_date = '".$strPHPDAteTime."' where patient_last_examined_id = '".$masterPatLastExamId."'";
						$rsUpdatePatLastExamined = imw_query($qryUpdatePatLastExamined);
					}
					else{
						$strPHPDAteTime = date("Y-m-d H:i:s");
						$qryInsertPatLastExamined = "insert into patient_last_examined 
											(patient_id,operator_id,section_name,created_date,status,save_or_review) 
											VALUES 
											('".$patientId."','".$opreaterId."','".$sectionName."','".$strPHPDAteTime."','0','1')";
						$rsInsertPatLastExamined = imw_query($qryInsertPatLastExamined);
						$masterPatLastExamId = imw_insert_id();																
					}		
					imw_free_result($rsSelPatLastExamined);			
				}											
				if($masterPatLastExamId > 0){
					$insertQryReview = str_replace("master_pat_last_exam_id_val",$masterPatLastExamId,$insertQryReview);
					$insertQryReview = substr(trim($insertQryReview), 0, -1);									
					$rsinsertQryReview = imw_query($insertQryReview);		
					//Audit Query
					$policyStatus = 0;
					/*$qryGetAuditPolicies = "select policy_status as policyStatus from audit_policies where policy_id = 5";
					$rsGetAuditPolicies = imw_query($qryGetAuditPolicies);
					if($rsGetAuditPolicies){
						if(imw_num_rows($rsGetAuditPolicies) > 0){
							$rowGetAuditPolicies = imw_fetch_array($rsGetAuditPolicies);
							$policyStatus = (int)$rowGetAuditPolicies['policyStatus'];
						}
					}*/	
					$policyStatus = (int)$_SESSION['AUDIT_POLICIES']['Patient_record_Created_Viewed_Updated'];
					if($policyStatus == 1){
						$insertQryAuditTrail = substr(trim($insertQryAuditTrail), 0, -1);  									
						$rsInsertQryAuditTrail = imw_query($insertQryAuditTrail);								
					}
				}
			}
		}
		elseif($debug == 1){
			if($debugArray == 1){
				echo '<pre>';
				print_r($arrReview);	
			}	
			if($insertQryAuditTrail != ''){	
				$insertQryAuditTrail = substr(trim($insertQryAuditTrail), 0, -1);  
			}
			die("<br>Debug Status");
		}
		//echo $insertQryAuditTrail;
		$cls_notifications->update_medHx_status();//updating iconbar status;
	}
	
	function isDate($i_sDate)
	{	
		if(preg_match("/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/", $i_sDate))
		{		
			$arrDate = explode("-", $i_sDate); 		
			$intYear = $arrDate[0]; 
			$intMonth = $arrDate[1];
			$intDay = $arrDate[2];
			$intIsDate = checkdate($intMonth, $intDay, $intYear);
			if($intIsDate){
				$date = $intMonth."-".$intDay."-".$intYear;
			}
			return ($date);
		}
		else{
			return $i_sDate;
		}  	
	   
	} 
	
	function getOrignalValComa($Old_Value,$New_Value,$arrMEDHXGenHealth,$Field_Label)
	{
		$OldValueOrignal = "";
		$arrOldVal = explode(',',$Old_Value);										
		foreach ($arrOldVal as $key => $value) {
			if ($arrOldVal[$key] == "") {unset($arrOldVal[$key]);}
		}										
		foreach ($arrMEDHXGenHealth as $key => $value)
		{
			if($arrMEDHXGenHealth[$key]['Filed_Label'] == $Field_Label)
			{
				foreach ($arrOldVal as $keyOld => $valueOld)
				{
					if($arrOldVal[$keyOld] == $arrMEDHXGenHealth[$key]['Filed_Label_Val'])
					{
						$OldValueOrignal .= $arrMEDHXGenHealth[$key]['Filed_Label_Og_Val']."<br>";
						unset($arrOldVal[$keyOld]);
						break;
					}
				}
			}
		}
		$Old_Value = $OldValueOrignal;

		$newValueOrignal = "";
		$arrNewVal = explode(',',$New_Value);										
		foreach ($arrNewVal as $key => $value)
		{
			if($arrNewVal[$key] == "") {unset($arrNewVal[$key]);}
		}										
		foreach ($arrMEDHXGenHealth as $key => $value)
		{
			if($arrMEDHXGenHealth[$key]['Filed_Label'] == $Field_Label)
			{
				foreach ($arrNewVal as $keyNew => $valueNew)
				{
					if ($arrNewVal[$keyNew] == $arrMEDHXGenHealth[$key]['Filed_Label_Val'])
					{
						$newValueOrignal .= $arrMEDHXGenHealth[$key]['Filed_Label_Og_Val']."<br>";
						unset($arrNewVal[$keyNew]);
						break;
					}
				}																								
			}
		}										
		$New_Value = $newValueOrignal;
		return $Old_Value."~~~~".$New_Value;
	}
	
	function getOrignalValWt2Sep($Old_Value,$New_Value,$arrMEDHXOculer,$Field_Label)
	{
		$arrTmp = array();								
		$OldValueOrignal = "";
		$strSep="~!!~~";
		$strSep2=":*:";
		$arrOldVal = explode($strSep, $Old_Value);
		foreach($arrOldVal as $keyOld => $valueOld)
		{
			$arrTmp[] = explode($strSep2,$valueOld);											
		}										
		foreach ($arrTmp as $key => $value)
		{	
			foreach ($value as $keyInner => $valueInner)
			{	
				if ($value[$keyInner] == "") {unset($arrTmp[$key]);}																							
			}																					
		}	
				
		foreach ($arrMEDHXOculer as $key => $value)
		{
			if($arrMEDHXOculer[$key]['Filed_Label'] == $Field_Label)
			{	
				foreach ($arrTmp as $keyOld => $valueOld) 
				{
					foreach ($valueOld as $keyInner => $valueInner)
					{	
						if ($valueOld[$keyInner] == $arrMEDHXOculer[$key]['Filed_Label_Val'])
						{
							$OldValueOrignal .= $arrMEDHXOculer[$key]['Filed_Label_Og_Val']."&nbsp;".$valueOld[$keyInner+1]."<br>";											
							unset($arrTmp[$keyOld]);																																							
							break;
						} 																							
					}													
				}																								
			}
		}
		
		$Old_Value = $OldValueOrignal;
		
		$newValueOrignal = "";
		$arrTmp = array();																		
		$strSep="~!!~~";
		$strSep2=":*:";
		$arrOldVal = explode($strSep, $New_Value);
		foreach($arrOldVal as $keyOld => $valueOld){
			$arrTmp[] = explode($strSep2,$valueOld);											
		}										
		foreach ($arrTmp as $key => $value)
		{	
			foreach ($value as $keyInner => $valueInner)
			{
				if ($value[$keyInner] == "") {unset($arrTmp[$key]);}																							
			}																					
		}	
		
		foreach ($arrMEDHXOculer as $key => $value) 
		{
			if($arrMEDHXOculer[$key]['Filed_Label'] == $Field_Label)
			{	
				foreach ($arrTmp as $keyOld => $valueOld) 
				{
					foreach ($valueOld as $keyInner => $valueInner) 
					{
						if ($valueOld[$keyInner] == $arrMEDHXOculer[$key]['Filed_Label_Val'])
						{
							$newValueOrignal .= $arrMEDHXOculer[$key]['Filed_Label_Og_Val']."&nbsp;".$valueOld[$keyInner+1]."<br>";
							unset($arrTmp[$keyOld]);
							break;
						}
					}
				}
			}
		}
		
		$New_Value = $newValueOrignal;
		return $Old_Value."~~~~".$New_Value;
	}
	function get_set_pat_rel_values_save_review($dbValue,$postValue,$methodFor,$delimiter){
		$dbValue 	= trim($dbValue);
		$postValue 	= trim($postValue);
		$methodFor 	= trim($methodFor);
		$delimiter	= trim($delimiter);
		
		if($methodFor == "pat"){
			if(stristr($dbValue,$delimiter)){
				list($strTxtPat,$strTxtRel) = explode($delimiter,$dbValue);				
				$postValue = $postValue.$delimiter.$strTxtRel;
			}
			else{
				$postValue = $postValue.$delimiter;
			}
		}
		elseif($methodFor == "rel"){
			if(stristr($dbValue,$delimiter)){
				list($strTxtPat,$strTxtRel) = explode($delimiter,$dbValue);
				$postValue = $strTxtPat.$delimiter.$postValue;
			}
			else{				
				$postValue = $dbValue.$delimiter.$postValue;
			}
		}
		
		return $postValue;
	}	
	
	function get_set_pat_rel_values_retrive_review($dbValue,$methodFor,$delimiter){	
		$dbValue 	= trim($dbValue);		
		$methodFor 	= trim($methodFor);
		$delimiter	= trim($delimiter);
		
		if($methodFor == "pat"){			
			if(stristr($dbValue,$delimiter)){
				//$aa = explode($delimiter,$dbValue);				
				list($strTxtPat,$strTxtRel) = explode($delimiter,$dbValue);
						
				$valueToShow = $strTxtPat;	
				
			}
			else{
				$valueToShow = $dbValue;
			}
			
		}
		elseif($methodFor == "rel"){
			if(stristr($dbValue,$delimiter)){
				list($strTxtPat,$strTxtRel) = explode($delimiter,$dbValue);
				$valueToShow = $strTxtRel;
			}
			else{				
				$valueToShow = "";
			}
		}		
		return $valueToShow;
	}
}	
?>