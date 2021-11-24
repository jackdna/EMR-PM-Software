<?php
$date = date(''.phpDateFormat().'');

$pdfFileContent="";
$ARR_CSV_EXPORT=array();

if (isset($_POST['form_submitted']) && $_POST['form_submitted'] == "1") {
//DATE RANGE ARRAY WEEKLY/MONTHLY/QUARTERLY
	$arrDateRange= $CLSCommonFunction->changeDateSelection();

	if($dayReport=='Daily'){
		$Start_date = $End_date= date($phpDateFormat);
	}else if($dayReport=='Weekly'){
		$Start_date = $arrDateRange['WEEK_DATE'];
		$End_date= date($phpDateFormat);
	}else if($dayReport=='Monthly'){
		$Start_date = $arrDateRange['MONTH_DATE'];
		$End_date= date($phpDateFormat);
	}else if($dayReport=='Quarterly'){
		$Start_date = $arrDateRange['QUARTER_DATE_START'];
		$End_date = $arrDateRange['QUARTER_DATE_END'];
	}
	$Start_date = getDateFormatDB($Start_date);
	$End_date = getDateFormatDB($End_date);
	
	$Pt_check = $_POST['process']; 
	
	$provider_array = $_POST['provider_id']; 
	$provider_id = implode(",",$provider_array);
	if(count($provider_array) == 0){
		$array_providerID = '';
	}else{
		$array_providerID ="AND sa.providerID IN ($provider_id)";
	}
	$procedure_array = $_POST['procedureType']; 
	$procedure_id = implode(",",$procedure_array);
	if(count($procedure_array) == 0){
		 $array_procedureid = '';
	 }else{
		$array_procedureid =  "AND sa.procedure_id IN ($procedure_id)";
	}
	list($main_set_id,$request_survey_id)= explode("-",$_POST['survey_id']); 
	
		$qry_survey_display="SELECT survey_id_set, survey_name, gender, age_group_start,age_group_end,problem_list,appointment_proc,appt_start_date,appt_end_date,survey_message FROM survey_tbl_set WHERE id='$main_set_id'"; 
		$res_survey_display=imw_query($qry_survey_display);
		$row_survey_display=imw_fetch_assoc($res_survey_display); 
		$survey_id_set=$row_survey_display["survey_id_set"];
		$request_survey_name=$row_survey_display["survey_name"];
		$gender=$row_survey_display["gender"];
		$age_group_start=$row_survey_display["age_group_start"];
		$age_group_end=$row_survey_display["age_group_end"];
		$appointment_proc=$row_survey_display["appointment_proc"];
		$appt_start_date=$row_survey_display["appt_start_date"];				
		$appt_end_date=$row_survey_display["appt_end_date"];
		
		$qry_gender=$qry_start=$qry_end=$qry_proc="";
		if($gender=="male" || $gender=="female"){
			$qry_gender=" AND pd.gender='".ucwords($gender)."'";
		}
		if($age_group_start){
			$qry_start=" AND TIMESTAMPDIFF(YEAR,pd.DOB, CURDATE())>='".$age_group_start."'";
		}
		if($age_group_end){
			$qry_end=" AND TIMESTAMPDIFF(YEAR,pd.DOB, CURDATE())<'".$age_group_end."'";
		}
		
		$proc_id = (explode('|~~|',$appointment_proc));
		$proc_value = array();
		foreach($proc_id as $proc_val){
			list($proc_id,$proc_name) = (explode('-||-',$proc_val));
			$proc_value[]=$proc_id;	
		}
		$all_procs="";
		if(count($proc_value)>0){
			$qry_proc=" AND sa.procedureid IN(".implode(",",$proc_value).")";
		}
		$allocate_survey="";
		if($appt_start_date && $appt_end_date){
			$qry_sa_appt ="SELECT sa_patient_id FROM schedule_appointments as sa inner join patient_data as pd on (pd.id=sa.sa_patient_id) where sa_app_start_date>='$appt_start_date' AND sa_app_start_date<='$appt_end_date' ".$qry_gender.$qry_start.$qry_end.$qry_proc;
			$res_sa_appt=imw_query($qry_sa_appt);
			$allocate_survey=imw_num_rows($res_sa_appt);
		}
	
		//===========================ARRAY_SURVEY_DETAILS================================//
		$ARRAY_SURVEY=$ARRAY_SURVEY_QUESTION=$ARRAY_SURVEY_OPTION=array();
		$qry_survey="Select survey_id,survey_title FROM survey_tbl";
		$res_survey=imw_query($qry_survey);
		while($row_survey=imw_fetch_assoc($res_survey)){
			$survey_id=$row_survey["survey_id"];
			$survey_title=$row_survey["survey_title"];
			$ARRAY_SURVEY[$survey_id]=$survey_title;		
		}
		
		$qry_survey_question="SELECT question_id,survey_id,question_description FROM survey_question";
		$res_survey_question=imw_query($qry_survey_question);
		while($row_survey_question=imw_fetch_assoc($res_survey_question)){
			$question_id=$row_survey_question["question_id"];
			$survey_id=$row_survey_question["survey_id"];
			$question_description=$row_survey_question["question_description"];
			$ARRAY_SURVEY_QUESTION[$question_id][$survey_id]=$question_description;
		}
		
		$qry_survey_option="SELECT survey_option_id,survey_option_description,question_id,survey_id	FROM survey_option order by survey_option_id";
		$res_survey_option=imw_query($qry_survey_option);
		while($row_survey_option=imw_fetch_assoc($res_survey_option)){
			$survey_option_id=$row_survey_option["survey_option_id"];
			$survey_option_description=$row_survey_option["survey_option_description"];
			$question_id=$row_survey_option["question_id"];
			$survey_id=$row_survey_option["survey_id"];
			$ARRAY_SURVEY_OPTION[$question_id][$survey_id][$survey_option_id]=$survey_option_description;
		}
		//=============================================================================//	
		
		//=============================================================================//	
			$HTML_SHOW="";
			$arr_respone=$MAIN_ARRAY=$arr_option_count=$arr_survey_pat=$arr_option_count_name=$gender_arr=$responsearr=$pat_gender_data=$pat_age_data=$arr_survey_pat_gender=$arr_survey_comment=array();
			$qrySurvey="SELECT sa.survey_answer_id as survey_answer_id,sa.patient_id as patient_id,DATE_FORMAT(sa.date_of_answer,'%Y-%m-%d') as answer_date,sa.survey_id as survey_id,sad.question_id as question_id,sad.option_id as option_id, sa.procedure_id, sa.survey_answer_comment FROM survey_answer as sa inner join survey_answer_details as sad on(sad.survey_answer_id=sa.survey_answer_id) INNER JOIN schedule_appointments as sapt on sa.patient_id = sapt.sa_patient_id WHERE sa.survey_id='".$request_survey_id."' AND DATE_FORMAT(sa.date_of_answer,'%Y-%m-%d')>='".$Start_date."' AND DATE_FORMAT(sa.date_of_answer,'%Y-%m-%d')<='".$End_date."' $array_providerID $array_procedureid ORDER BY sad.question_id, sa.date_of_answer";
			$resSurvey=imw_query($qrySurvey) or die(imw_error().$qrySurvey);
			while($rowSurvey=imw_fetch_assoc($resSurvey)){
				$survey_answer_id=$rowSurvey["survey_answer_id"];
				$patient_id=$rowSurvey["patient_id"];
				$answer_date=$rowSurvey["answer_date"];
				$arr_respone[$patient_id][$answer_date]=$patient_id;
				$responsearr[$patient_id] = $answer_date;
				$survey_id=$rowSurvey["survey_id"];
				$question_id=$rowSurvey["question_id"];
				$option_id=$rowSurvey["option_id"];
				$survey_comment=$rowSurvey["survey_answer_comment"];
				$survey_name=$ARRAY_SURVEY[$survey_id];
				$question_name=$ARRAY_SURVEY_QUESTION[$question_id][$survey_id];
				$options_arr=array();
				foreach($ARRAY_SURVEY_OPTION[$question_id][$survey_id] as $option_id=> $option_val){
					$options_arr[$option_id]=$option_val;
				}
				$MAIN_ARRAY[$question_id]["survey_name"]=$survey_name;
				$MAIN_ARRAY[$question_id]["question_name"]=$question_name;
				$MAIN_ARRAY[$question_id]["option_name"]=$options_arr;
				
				$qry="Select cmt.id as formid, pt_dt.DOB, concat(pt_dt.lname,', ',pt_dt.fname,' - ',pt_dt.id) as name, pt_dt.sex as sex, pt_dt.id, pt_dt.city, pt_dt.state, pt_dt.postal_code, TIMESTAMPDIFF(YEAR, pt_dt.DOB, CURDATE()) AS age, cmt.date_of_service from patient_data AS pt_dt LEFT OUTER JOIN chart_master_table cmt ON pt_dt.id = cmt.patient_id where pt_dt.id='".$patient_id."' LIMIT 0,1";
				$res=imw_query($qry);
				$row=imw_fetch_assoc($res);
				$pt_DOB = $row['DOB'];
				$pt_DOB = date(''.phpDateFormat().'',strtotime($pt_DOB));
				$pt_age = $row['age'];
				$final_dob = $pt_DOB." (".$pt_age.")";
				$pt_id = $row['id'];
				$pt_name = $row['name'];
				$pt_gender = $row['sex'];	
				if($pt_gender!="Male" && $pt_gender!="Female"){
					$pt_gender = "Unknown";
				}
				$pt_city = $row['city'];
				$pt_state = $row['state'];
				$pt_postal_code = $row['postal_code'];
				$pt_DOS = $row['date_of_service'];
				
				$pt_formid = $row['formid'];
				
				$qry_chart_vision = "
					SELECT
			
			c3.sel_od as vis_dis_od_sel_1, c3.txt_od as vis_dis_od_txt_1, 
			c3.sel_os as vis_dis_os_sel_1, c3.txt_os as vis_dis_os_txt_1, 
			c4.sel_od as vis_dis_od_sel_2, c4.txt_od as vis_dis_od_txt_2, 
			c4.sel_os as vis_dis_os_sel_2, c4.txt_os as vis_dis_os_txt_2
				
			FROM chart_vis_master c2			
			LEFT JOIN chart_acuity c3 ON c3.id_chart_vis_master = c2.id AND c3.sec_name = 'Distance' AND c3.sec_indx=1
			LEFT JOIN chart_acuity c4 ON c4.id_chart_vis_master = c2.id AND c4.sec_name = 'Distance' AND c4.sec_indx=2
			WHERE c2.form_id = '".$pt_formid."' AND c2.patient_id = '".$pt_id."'
				";				
				$vision="";
				$vision1="";
				$visionOD= "";
				$visionOD1= "";
				$visionOS1= "";
				$visionOS= "";
				$res_vision=imw_query($qry_chart_vision);
				$row_vision=imw_fetch_assoc($res_vision);
				
				if($row_vision['vis_dis_od_sel_1']=='SC' || $row_vision['vis_dis_od_sel_1']=='CC'){
					$visionOD = ($row_vision["vis_dis_od_sel_1"] != "") ? $row_vision["vis_dis_od_sel_1"]." " : "";
					$visionOD .= ($row_vision["vis_dis_od_txt_1"] != "" && $row_vision["vis_dis_od_txt_1"] != "20/") ? $row_vision["vis_dis_od_txt_1"] : "";
					$visionOS = ($row_vision["vis_dis_os_sel_1"] != "") ? $row_vision["vis_dis_os_sel_1"]." " : "";
					$visionOS .= ($row_vision["vis_dis_os_txt_1"] != "" && $row_vision["vis_dis_os_txt_1"] != "20/") ? $row_vision["vis_dis_os_txt_1"] : "";
				}
				if($row_vision['vis_dis_od_sel_2']=='SC' || $row_vision['vis_dis_od_sel_2']=='CC'){
					$visionOD1 = ($row_vision["vis_dis_od_sel_2"] != "") ? $row_vision["vis_dis_od_sel_2"]." " : "";
					$visionOD1 .= ($row_vision["vis_dis_od_txt_2"] != "" && $row_vision["vis_dis_od_txt_2"] != "20/") ? $row_vision["vis_dis_od_txt_2"] : "";
					$visionOS1 = ($row_vision["vis_dis_os_sel_2"] != "") ? $row_vision["vis_dis_os_sel_2"]." " : "";
					$visionOS1 .= ($row_vision["vis_dis_os_txt_2"] != "" && $row_vision["vis_dis_os_txt_2"] != "20/") ? $row_vision["vis_dis_os_txt_2"] : "";
				}
				if(!empty($visionOD) || !empty($visionOS)){
					$vision = "<font color=Blue><b>OD</b></font>:".$visionOD."<br><font color=Green><b>OS</b></font>:".$visionOS."<br>";
				} 					
				if(!empty($visionOD1) || !empty($visionOS1)){
					$vision1 = "<font color=Blue><b>OD</b></font>:".$visionOD1."<br><font color=Green><b>OS</b></font>:".$visionOS1;
				}
				$arr_survey_pat_vision[$survey_answer_id]=$vision;
				$arr_survey_pat_vision1[$survey_answer_id]=$vision1;
				//$array_vision[$survey_answer_id]=
				//	pre($row_vision);
				
				$sql = "SELECT sumOdIop, sumOsIop,trgtOd,trgtOs,multiple_pressure FROM chart_iop WHERE form_id = '".$pt_formid."' AND patient_id = '".$pt_id."' AND purged='0'  ";
				$res=imw_query($sql);
				$row=imw_fetch_assoc($res);
				if($res !== false){
					$sumOd     = $row["sumOdIop"];
					$sumOs     = $row["sumOsIop"];
					$trtOd     = $row ["trgtOd"];
					$trtOs     = $row ["trgtOs"];
					$multiple_pressure  = $row ["multiple_pressure"];
					$arrReplace = array("/Trgt:","/ Trgt:",$trtOd."/ ",$trtOs."/ ","Trgt:",$trtOd,$trtOs);
					$sumOd = str_ireplace($arrReplace,"",$sumOd);
					$sumOs = str_ireplace($arrReplace,"",$sumOs);
					$sum = (!empty($sumOd)) ? "<font color=Blue><b>OD</b></font>: ".$sumOd.'\n' : "";   
					$sum .= (!empty($sumOs) && !empty($sumOd)) ? "" : "";   
					$sum .= (!empty($sumOs)) ? "<font color=Green><b>OS</b></font>: ".$sumOs.'\n' : "";
					$arr_survey_pat_IOP[$survey_answer_id]=htmlspecialchars($sum);
				}
			
				$pt_DOS = date(''.phpDateFormat().'',strtotime($pt_DOS));
				$arr_survey_pat[$survey_answer_id]=$pt_name;
				$arr_survey_pat_gender[$survey_answer_id]=$pt_gender;
				$arr_survey_pat_age[$survey_answer_id]=$final_dob;
				$arr_survey_pat_city[$survey_answer_id]=$pt_city;
				$arr_survey_pat_state[$survey_answer_id]=$pt_state;
				$arr_survey_pat_postal_code[$survey_answer_id]=$pt_postal_code;
				$arr_survey_pat_DOS[$survey_answer_id]=htmlspecialchars($pt_DOS);
				$gender_arr[$pt_id] = $pt_gender;
				$pat_gender_data[$pt_id]=$pt_gender;
				$pat_age_data[$pt_id]=$pt_age;
				$arr_survey_comment[$survey_answer_id]=$survey_comment;
			}
						
			$qry_option_count="SELECT count( sad.survey_answer_details_id ) AS opt_count, sad.option_id, group_concat( sad.survey_answer_id ) AS survey_answer_id FROM survey_answer_details AS sad INNER JOIN survey_answer sa ON sad.survey_answer_id = sa.survey_answer_id WHERE sad.survey_id = '".$request_survey_id."' AND DATE_FORMAT(sa.date_of_answer,'%Y-%m-%d')>='".$Start_date."' AND DATE_FORMAT(sa.date_of_answer,'%Y-%m-%d')<='".$End_date."' $array_providerID $array_procedureid GROUP BY sad.option_id HAVING count( sad.survey_answer_details_id ) >0";
			$res_option_count=imw_query($qry_option_count);
			while($row_option_count=imw_fetch_assoc($res_option_count)){
				$opt_count=$row_option_count["opt_count"];
				$option_id=$row_option_count["option_id"];
				$survey_answer_id=explode(",",$row_option_count["survey_answer_id"]);
				$pat_name_concat=array();
				$pat_gender_concat=array();
				foreach($survey_answer_id as $survey_answer_id_val){
					
					$pat_name_concat[]="<tr><td valign=top>".$arr_survey_pat[$survey_answer_id_val]."</td><td valign=top>".$arr_survey_pat_DOS[$survey_answer_id_val]."</td><td valign=top>".$arr_survey_pat_age[$survey_answer_id_val]."</td><td valign=top>".$arr_survey_pat_gender[$survey_answer_id_val]."</td><td valign=top>".$arr_survey_pat_vision[$survey_answer_id_val].$arr_survey_pat_vision1[$survey_answer_id_val]."</td><td valign=top>".$arr_survey_pat_IOP[$survey_answer_id_val]."</td><td valign=top>".$arr_survey_pat_city[$survey_answer_id_val]."</td><td valign=top>".$arr_survey_pat_state[$survey_answer_id_val]."</td><td valign=top>".$arr_survey_pat_postal_code[$survey_answer_id_val]."</td><td valign=top>".$arr_survey_comment[$survey_answer_id_val]."</td></tr>";
					$pat_gender_concat[]=$arr_survey_pat_gender[$survey_answer_id_val];
					//pre($pat_gender_concat);
				}
				sort($pat_name_concat);
				$arr_option_count_name[$option_id]="<table border=1 BORDERCOLOR=#000 cellpadding=5 cellspacing=1 style=\'border-collapse:collapse;width:100%;\' class=\'rpt_table rpt rpt_table-bordered rpt_padding\'><tr><td width=190><b>Pt Name – ID</b></td><td width=100><b>Last DOS</b></td><td width=140><b>DOB (Age)</b></td><td width=60><b>Gender</b></td><td width=120><b>Vision</b></td><td width=160><b>IOP</b></td><td width=80><b>City</b></td><td width=50><b>State</b></td><td width=80><b>ZIP Code</b></td><td width=230><b>Comment</b></td></tr>".implode($pat_name_concat)."</table>";
				$arr_option_count[$option_id]=$opt_count;
				$arr_option_count_gender[$option_id]= array_count_values($pat_gender_concat);
					
					
			}
			
			$arr_respone = count($arr_respone);
			if(count($MAIN_ARRAY)>0){
			$HTML_SHOW.="<table class=\"rpt rpt_table rpt_table-bordered rpt_padding\" width=\"1050\">
					<tr class=\"rpt_headers\">
						<td class=\"rptbx1\" width=\"350\">
							Survey: $request_survey_name
						</td>
						<td class=\"rptbx2\" width=\"350\">
							Total Response: $arr_respone
						</td>
						<td class=\"rptbx3\" width=\"350\">
							Total Allocation: $allocate_survey
						</td>
					</tr>
			</table>";
			
			$pdfFileContent.="<table class=\"rpt rpt_table rpt_table-bordered rpt_padding\" width=\"1050\">
				<tr class=\"rpt_headers\">
					<td class=\"rptbx1\" width=\"350\">
						Survey: $request_survey_name
					</td>
					<td class=\"rptbx2\" width=\"350\">
						Total Response: $arr_respone
					</td>
					<td class=\"rptbx3\" width=\"350\">
						Total Allocation: $allocate_survey
					</td>
				</tr>
			</table>";
			
			$content_lbl='"Survey: '.$request_survey_name.'"';
			$ARR_CSV_EXPORT[]=$content_lbl;
			$content_lbl_='"Total Response: '.count($arr_respone).'"'.','.'"Total Allocation: '.$allocate_survey.'"';
			$ARR_CSV_EXPORT[]=$content_lbl_;
			//foreach($MAIN_ARRAY as $answer_date=> $MAIN_ARRAY_DATE){
				//pre($MAIN_ARRAY_DATE);
				//pre($MAIN_ARRAY_QUESTION);
					$question_no = 1;
					foreach($MAIN_ARRAY as $qus_key=>$survey_details_arr){
						$HTML_SHOW.="<table class=\"rpt rpt_table rpt_table-bordered\" width=\"100%\" bgcolor=\"#FFF3E8\"><tr><td class=\"text_b_w\"  style=\"width:60%;min-height:40px;\"><b>Question $question_no: </b>".$survey_details_arr["question_name"]."</td><td class=\"text_b_w\" style=\"width:10%;text-align:center;padding-left:0px;\">Male</td><td class=\"text_b_w\"  style=\"width:10%;text-align:center;padding-left:0px;\">Female</td><td class=\"text_b_w\" style=\"width:10%;text-align:center;padding-left:0px;\">Unknown</td><td class=\"text_b_w\" style=\"width:10%;text-align:center;padding-left:0px;\">Total</td></tr>";
						
						$content_lbl1='"Question '.$question_no.': '.$survey_details_arr["question_name"].'"'.','.'"Male"'.','.'"Female"'.','.'"Unknown"'.','.'"Total"';
						$ARR_CSV_EXPORT[]=$content_lbl1;
						
						//Heading Section
						$pdfFileContent.="<table style=\"width:100%;\" cellpadding=\"1\" cellspacing=\"1\"  bgcolor=\"#FFF3E8\" class=\"rpt_table rpt rpt_table-bordered rpt_padding\">
											<tr>
												<td class=\"text_b_w\" style=\"width:60%;\"><b>Question $question_no: </b>".$survey_details_arr["question_name"]."</td>
												<td class=\"text_b_w\" style=\"width:10%;text-align:center;padding-left:0px;\">Male</td>
												<td class=\"text_b_w\" style=\"width:10%;text-align:center;padding-left:0px;\">Female</td>
												<td class=\"text_b_w\" style=\"width:10%;text-align:center;padding-left:0px;\">Unknown</td>
												<td class=\"text_b_w\" style=\"width:10%;text-align:center;padding-left:0px;\">Total</td>
											</tr>";
						
						//Ques Option Section
						$qus_ans_arr=array();
						foreach($survey_details_arr["option_name"] as $option_id=> $option_name){
							$option_count=($arr_option_count[$option_id]>0)?($arr_option_count[$option_id]):"";
							$option_count_name=($arr_option_count_name[$option_id])?$arr_option_count_name[$option_id]:"";
							$option_count_gender=($arr_option_count_gender[$option_id])?$arr_option_count_gender[$option_id]:"";
							//pre($option_count_gender);
							$male= $option_count_gender['Male'];
							$female= $option_count_gender['Female'];
							$unknown= $option_count_gender['Unknown'];
							$HTML_SHOW.= "<tr style='line-height:20px;'><td class=\"text_10\" bgcolor=\"#FFFFFF\"><b>&nbsp;&nbsp;Option:</b> $option_name</td><td class=\"text_10\" bgcolor=\"#FFFFFF\" style=\"text-align:center;\">$male</td><td class=\"text_10\" bgcolor=\"#FFFFFF\" style=\"text-align:center;\">$female</td><td class=\"text_10\" bgcolor=\"#FFFFFF\" style=\"text-align:center;\">$unknown</td><td class=\"text_10 text_purpule\" bgcolor=\"#FFFFFF\" style=\"cursor:pointer;text-align:center;color: #5c2a79; font-family: 'robotobold'; \" onClick=\"top.fAlert('<div style=\'max-height:500px;overflow-y:auto;\'>$option_count_name</div>','','','1200px')\"> ".$option_count."</td>";
							$HTML_SHOW.= "</tr>";
							$pdfFileContent.= "<tr>
												<td class=\"text_10\" bgcolor=\"#FFFFFF\"><b>&nbsp;Option:</b>".$option_name."</td>
												<td class=\"text_10\" bgcolor=\"#FFFFFF\" style=\"width:10%;text-align:center;padding-left:0px;\">$male</td>
												<td class=\"text_10\" bgcolor=\"#FFFFFF\" style=\"width:10%;text-align:center;padding-left:0px;\">$female</td>
												<td class=\"text_10\" bgcolor=\"#FFFFFF\" style=\"width:10%;text-align:center;padding-left:0px;\">$unknown</td>
												<td class=\"text_10 text_purple\" bgcolor=\"#FFFFFF\" style=\"width:10%;text-align:center;padding-left:0px;\"> ".$option_count."</td>
											</tr>"; 
							if($HTML_SHOW_div!=""){
								//$HTML_SHOW.= $HTML_SHOW_div;
							}
							$content_val='"&nbsp;&nbsp;Option: '.$option_name.'"'.','.'"'.$male.'"'.','.'"'.$female.'"'.','.'"'.$unknown.'"'.','.'"'.$option_count.'"';
							$ARR_CSV_EXPORT[]=$content_val;
							$qus_ans_arr[$option_id][$option_name]=$option_count;
						}	
						
						//Option Chart Section
						$pdfFileContent.='</table>';
						
						ksort($qus_ans_arr);
						//$final_qus_ans_arr=array();
						foreach($qus_ans_arr as $qus_ans_key=>$qus_ans_val){
							foreach($qus_ans_arr[$qus_ans_key] as $qus_ans_sec_key=>$qus_ans_sec_val){
								$final_qus_ans_arr[$qus_key][$qus_ans_sec_key]=$qus_ans_sec_val;
							}
						}//print_r($final_qus_ans_arr);
						//$ans_bar_data_arr[$qus_key]=bar_chart_fun($final_qus_ans_arr,false);
						//print_r($ans_bar_data_arr);
						$HTML_SHOW.= "</table>";
						$HTML_SHOW.= $HTML_SHOW_div."<div style='clear:both;'></div>";
						$question_no++;
					}
					// Added code for display Patient Details Table
					
				if ($Pt_check == "Detail"){
					$qrySurveyTbl ="SELECT * FROM survey_answer AS sa Left JOIN patient_data  AS pt_dt ON sa.patient_id = pt_dt.id WHERE sa.survey_id = '".$request_survey_id."' AND DATE_FORMAT( sa.date_of_answer, '%Y-%m-%d' ) >= '".$Start_date."' AND DATE_FORMAT( sa.date_of_answer, '%Y-%m-%d' ) <= '".$End_date."' $array_providerID $array_procedureid ORDER BY pt_dt.lname , pt_dt.fname ASC";
					$resSurveyTbl=imw_query($qrySurveyTbl) or die(imw_error().$qrySurveyTbl);
					$HTML_SHOW.="<table class=\"rpt rpt_table rpt_table-bordered\" width=\"100%\" bgcolor=\"#FFF3E8\"><tr><td colspan=\"10\" class=\"text_b_w\" style=\"width:100%; \">Patient Details</td></tr><tr><td width=190 class=\"text_b_w\" ><b>Pt Name – ID</b></td><td width=100 class=\"text_b_w\"><b>Last DOS</b></td><td width=140 class=\"text_b_w\" ><b>DOB (Age)</b></td><td width=60 class=\"text_b_w\" ><b>Gender</b></td><td width=120 class=\"text_b_w\" ><b>Vision</b></td><td width=160 class=\"text_b_w\" ><b>IOP</b></td><td width=80 class=\"text_b_w\" ><b>City</b></td><td width=50 class=\"text_b_w\" ><b>State</b></td><td width=80 class=\"text_b_w\" ><b>ZIP Code</b></td><td width=230 class=\"text_b_w\" ><b>Comment</b></td></tr>";
					
					$pdfFileContent.= "</br><table style=\"width:100%\" cellpadding=\"1\" cellspacing=\"1\" bgcolor=\"#FFF3E8\" class=\"rpt_padding\"><tr><td class=\"text_b_w\" style=\"width:100%\"><b>Patient Details</b></td></tr></table>";
					$pdfFileContent.= "<table cellpadding=\"1\" cellspacing=\"1\" style=\"width:100%;\" bgcolor=\"#FFF3E8\" class=\"rpt_padding\"><tr><td style=\"width:10%\" class=\"text_b_w\" ><b>Pt Name ID</b></td><td style=\"width:10%;\" class=\"text_b_w\" ><b>Last DOS</b></td><td style=\"width:10%;\" class=\"text_b_w\" ><b>DOB (Age)</b></td><td style=\"width:10%;\" class=\"text_b_w\" ><b>Gender</b></td><td style=\"width:10%;\" class=\"text_b_w\" ><b>Vision</b></td><td style=\"width:10%;\" class=\"text_b_w\" ><b>IOP</b></td><td style=\"width:10%;\" class=\"text_b_w\" ><b>City</b></td><td style=\"width:10%;\" class=\"text_b_w\" ><b>State</b></td><td style=\"width:10%;\" class=\"text_b_w\" ><b>ZIP Code</b></td><td style=\"width:10%;\" class=\"text_b_w\" ><b>Comment</b></td></tr></table>";
					
					$content_val_table='"Patient Details"';
					$ARR_CSV_EXPORT[]=$content_val_table;
					$content_lbl11='"Pt Name - ID"'.','.'"Last DOS"'.','.'"DOB (Age)"'.','.'"Gender"'.','.'"Vision"'.','.'"IOP"'.','.'"City"'.','.'"State"'.','.'"ZIP Code"'.','.'"Comment"';
					$ARR_CSV_EXPORT[]=$content_lbl11;
					while($rowSurveyTbl=imw_fetch_assoc($resSurveyTbl)){
						$survey_answer_comment=$rowSurveyTbl["survey_answer_comment"];
						$patient_id=$rowSurveyTbl["patient_id"];
						$qryRes="Select cmt.id as formid, pt_dt.DOB, concat(pt_dt.lname,', ',pt_dt.fname,' - ',pt_dt.id) as name, pt_dt.sex as sex, pt_dt.id, pt_dt.city, pt_dt.state, pt_dt.postal_code, TIMESTAMPDIFF(YEAR, pt_dt.DOB, CURDATE()) AS age, cmt.date_of_service from patient_data AS pt_dt LEFT OUTER JOIN chart_master_table cmt ON pt_dt.id = cmt.patient_id where pt_dt.id='".$patient_id."' ORDER BY name LIMIT 0,1";
						$res=imw_query($qryRes);
						$rowRes=imw_fetch_assoc($res);
						$pt_DOB = $rowRes['DOB'];
						$pt_DOB = date(''.phpDateFormat().'',strtotime($pt_DOB));
						$pt_age = $rowRes['age'];
						$final_dob = $pt_DOB." (".$pt_age.")";
						$pt_id = $rowRes['id'];
						$pt_name = $rowRes['name'];
						$pt_gender = $rowRes['sex'];	
						if($pt_gender!="Male" && $pt_gender!="Female"){
							$pt_gender = "Unknown";
						}
						$pt_city = $rowRes['city'];
						$pt_state = $rowRes['state'];
						$pt_postal_code = $rowRes['postal_code'];
						$pt_DOS = $rowRes['date_of_service'];
						$pt_DOS = date(''.phpDateFormat().'',strtotime($pt_DOS));
						$pt_formid = $rowRes['formid'];

						$qry_chart_vision_res = "
							SELECT
			
			c3.sel_od as vis_dis_od_sel_1, c3.txt_od as vis_dis_od_txt_1, 
			c3.sel_os as vis_dis_os_sel_1, c3.txt_os as vis_dis_os_txt_1, 
			c4.sel_od as vis_dis_od_sel_2, c4.txt_od as vis_dis_od_txt_2, 
			c4.sel_os as vis_dis_os_sel_2, c4.txt_os as vis_dis_os_txt_2
				
			FROM chart_vis_master c2			
			LEFT JOIN chart_acuity c3 ON c3.id_chart_vis_master = c2.id AND c3.sec_name = 'Distance' AND c3.sec_indx=1
			LEFT JOIN chart_acuity c4 ON c4.id_chart_vis_master = c2.id AND c4.sec_name = 'Distance' AND c4.sec_indx=2
			WHERE c2.form_id = '".$pt_formid."' AND c2.patient_id = '".$pt_id."'
						";				
						$vision="";
						$vision1="";
						$visionOD= "";
						$visionOD1= "";
						$visionOS1= "";
						$visionOS= "";
						$res_vision=imw_query($qry_chart_vision_res);
						$row_vision=imw_fetch_assoc($res_vision);
				
						if($row_vision['vis_dis_od_sel_1']=='SC' || $row_vision['vis_dis_od_sel_1']=='CC'){
							$visionOD = ($row_vision["vis_dis_od_sel_1"] != "") ? $row_vision["vis_dis_od_sel_1"]." " : "";
							$visionOD .= ($row_vision["vis_dis_od_txt_1"] != "" && $row_vision["vis_dis_od_txt_1"] != "20/") ? $row_vision["vis_dis_od_txt_1"] : "";
							$visionOS = ($row_vision["vis_dis_os_sel_1"] != "") ? $row_vision["vis_dis_os_sel_1"]." " : "";
							$visionOS .= ($row_vision["vis_dis_os_txt_1"] != "" && $row_vision["vis_dis_os_txt_1"] != "20/") ? $row_vision["vis_dis_os_txt_1"] : "";
						}
						if($row_vision['vis_dis_od_sel_2']=='SC' || $row_vision['vis_dis_od_sel_2']=='CC'){
							$visionOD1 = ($row_vision["vis_dis_od_sel_2"] != "") ? $row_vision["vis_dis_od_sel_2"]." " : "";
							$visionOD1 .= ($row_vision["vis_dis_od_txt_2"] != "" && $row_vision["vis_dis_od_txt_2"] != "20/") ? $row_vision["vis_dis_od_txt_2"] : "";
							$visionOS1 = ($row_vision["vis_dis_os_sel_2"] != "") ? $row_vision["vis_dis_os_sel_2"]." " : "";
							$visionOS1 .= ($row_vision["vis_dis_os_txt_2"] != "" && $row_vision["vis_dis_os_txt_2"] != "20/") ? $row_vision["vis_dis_os_txt_2"] : "";
						}
						if(!empty($visionOD) || !empty($visionOS)){
							$vision = "<font color=Blue><b>OD</b></font>:".$visionOD."<br><font color=Green><b>OS</b></font>:".$visionOS."<br>";
						} 					
						if(!empty($visionOD1) || !empty($visionOS1)){
							$vision1 = "<font color=Blue><b>OD</b></font>:".$visionOD1."<br><font color=Green><b>OS</b></font>:".$visionOS1;
						}
						$sqlRes = "SELECT sumOdIop, sumOsIop,trgtOd,trgtOs,multiple_pressure FROM chart_iop WHERE form_id = '".$pt_formid."' AND patient_id = '".$pt_id."' AND purged='0'  ";
						$res=imw_query($sqlRes);
						$row=imw_fetch_assoc($res);
						if($res !== false){
							$sumOd     = $row["sumOdIop"];
							$sumOs     = $row["sumOsIop"];
							$trtOd     = $row ["trgtOd"];
							$trtOs     = $row ["trgtOs"];
							$multiple_pressure  = $row ["multiple_pressure"];
							$arrReplace = array("/Trgt:","/ Trgt:",$trtOd."/ ",$trtOs."/ ","Trgt:",$trtOd,$trtOs);
							$sumOd = str_ireplace($arrReplace,"",$sumOd);
							$sumOs = str_ireplace($arrReplace,"",$sumOs);
							$sum = (!empty($sumOd)) ? "<font color=Blue><b>OD</b></font>: ".$sumOd.' ' : "";   
							$sum .= (!empty($sumOs) && !empty($sumOd)) ? "" : "";   
							$sum .= (!empty($sumOs)) ? "<font color=Green><b>OS</b></font>: ".$sumOs.' ' : "";
							$sum = $sum;
						}
						$HTML_SHOW.="<tr><td class=\"text_10\" bgcolor=\"#FFFFFF\">$pt_name</td><td class=\"text_10\" bgcolor=\"#FFFFFF\">$pt_DOS</td><td class=\"text_10\" bgcolor=\"#FFFFFF\">$final_dob</td><td class=\"text_10\" bgcolor=\"#FFFFFF\">$pt_gender</td><td class=\"text_10\" bgcolor=\"#FFFFFF\">$vision $vision1</td><td class=\"text_10\" bgcolor=\"#FFFFFF\">$sum</td><td class=\"text_10\" bgcolor=\"#FFFFFF\">$pt_city</td><td class=\"text_10\" bgcolor=\"#FFFFFF\">$pt_state</td><td class=\"text_10\" bgcolor=\"#FFFFFF\">$pt_postal_code</td><td class=\"text_10\" bgcolor=\"#FFFFFF\">$survey_answer_comment</td></tr>";
						
						$pdfFileContent.="<table cellpadding=\"1\" cellspacing=\"1\" bgcolor=\"#FFF3E8\" style=\"width:100%;font-size:12px;\" class=\"rpt_table rpt rpt_table-bordered rpt_padding\"><tr><td class=\"text_10\" style=\"width:10%;vertical-align:baseline;vertical-align:baseline;\" bgcolor=\"#FFFFFF\">$pt_name</td><td class=\"text_10\" style=\"width:10%;vertical-align:baseline;\" bgcolor=\"#FFFFFF\">$pt_DOS</td><td class=\"text_10\" style=\"width:10%;vertical-align:baseline;\" bgcolor=\"#FFFFFF\">$final_dob</td><td class=\"text_10\" style=\"width:10%;vertical-align:baseline;\" bgcolor=\"#FFFFFF\">$pt_gender</td><td class=\"text_10\" style=\"width:10%;vertical-align:baseline;\" bgcolor=\"#FFFFFF\">$vision $vision1</td><td class=\"text_10\" style=\"width:10%;vertical-align:baseline;\" bgcolor=\"#FFFFFF\">$sum</td><td class=\"text_10\" style=\"width:10%;vertical-align:baseline;\" bgcolor=\"#FFFFFF\">$pt_city</td><td class=\"text_10\" style=\"width:10%;vertical-align:baseline;\" bgcolor=\"#FFFFFF\">$pt_state</td><td class=\"text_10\" style=\"width:10%;vertical-align:baseline;\" bgcolor=\"#FFFFFF\">$pt_postal_code</td><td class=\"text_10\"  style=\"width:10%;vertical-align:baseline;\"bgcolor=\"#FFFFFF\">$survey_answer_comment</td></tr></table>";
						
						$content_val_tbl='"'.$pt_name.'"'.','.'"'.$pt_DOS.'"'.','.'"'.$final_dob.'"'.','.'"'.$pt_gender.'"'.','.'"'.strip_tags($vision).' '.strip_tags($vision1).'"'.','.'"'.strip_tags($sum).'"'.','.'"'.$pt_city.'"'.','.'"'.$pt_state.'"'.','.'"'.$pt_postal_code.'"'.','.'"'.$survey_answer_comment.'"';
						$ARR_CSV_EXPORT[]=$content_val_tbl;
					}
				}	
				$HTML_SHOW.="</table>";					
				$hasData = 1;
				$stylePDF = '<style>'.file_get_contents('css/reports_pdf.css').'</style>';
				$strHTML = $stylePDF.$pdfFileContent;
				$file_location =  write_html($strHTML);
			}else{
				$HTML_SHOW.='<div class="text-center alert alert-info">No record found.</div>';
		}
		echo $HTML_SHOW;
}


?>