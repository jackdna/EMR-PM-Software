<?php


function show_med_list6($pid)
{
	$pid = (int) $pid;
	if( !$pid)  return;

	$getArrStr = "select title,if((DAY(begdate)='00' OR DAY(begdate)='0') && YEAR(begdate)='0000' && (MONTH(begdate)='00' OR MONTH(begdate)='0'),'',
								if((DAY(begdate)='00' OR DAY(begdate)='0') && (MONTH(begdate)='00' OR MONTH(begdate)='0'),date_format(begdate, '%Y'),
								if(MONTH(begdate)='00' OR MONTH(begdate)='0',date_format(begdate,'%Y'),
								if(DAY(begdate)='00' or DAY(begdate)='0',date_format(begdate,'%m-%Y'),
								date_format(begdate,'".get_sql_date_format()."')
								))))as begdate_2,referredby,comments,sites from lists where type IN ('6') and pid = '".$pid."'
								and allergy_status = 'Active' ORDER BY begdate DESC ";

	$getArrQry = imw_query($getArrStr);
	$html = '';

	$html	.= '<div id="surgeryDiv" class="modal" role="dialog" style="top:60px;max-width:60%; margin:0 auto; bottom:auto;">';

	$html .= '<div class="modal-dialog modal-lg" style="width:100%;margin:0;">';
	$html .= '<div class="modal-content">';

	$html .= '<div class="modal-header bg-primary">';
	$html .= '<button type="button" class="close" onClick="hideMedList(6);">×</button>';
	$html .= '<h4 class="modal-title" id="modal_title">Ocular Surgery</h4>';
	$html .= '</div>';

	$html	.= '<div class="modal-body pd5" style="max-height:400px; overflow:hidden; overflow-y:auto;">';
	$html	.= '<table id="tbl_ocu_grid" class="table table-bordered table-striped table-hover">';
	$html	.= '<thead class="grythead">';
	$html	.= '<tr >';
	$html	.= '<th width="30%">Name</td>';
	$html	.= '<th width="4%">Site</td>';
	$html	.= '<th width="13%">Date of Surgery</td>';
	$html	.= '<th width="15%">Physician</td>';
	$html	.= '<th width="38%">Comments</td>';
	$html	.= '</tr>';
	$html	.= '</thead>';
	$html	.= '<tbody>';

	while( $getArrRows = imw_fetch_assoc($getArrQry))
	{
		extract($getArrRows);
		$site = ''; $site_class = '';
		if( $sites == 3) { $site = 'OU'; $site_class = 'text_purple'; }
		elseif( $sites == 2) { $site = 'OD'; $site_class = 'text-primary'; }
		elseif( $sites == 1) { $site = 'OS'; $site_class = 'text-success'; }

		$surgery_date = ($begdate_2 <> '' &&  get_number($begdate_2) <> '00000000' && $begdate_2 <> '--') ? $begdate_2 : '';

		$html	.= '<tr>';
		$html	.= '<td >'.($title ? $title : '').'</td>';
		$html	.= '<td class="'.$site_class.'"><b>'.$site.'</b></td>';
		$html	.= '<td>'.$surgery_date.'</td>';
		$html	.= '<td>'.($referredby ? $referredby :'').'</td>';
		$html	.= '<td>'.($comments ? $comments :'').'</td>';
		$html	.= '</tr>';
	}

	if( imw_num_rows($getArrQry) <= 0 )
	{
		$html	.= '<tr><td colspan="5" class="bg-info text-center">No Record Found.</td></tr>';
	}
	$html	.= '</tbody>';
	$html	.= '</table>';

	$html	.= '</div>';

	$html	.= '</div>';
	$html	.= '</div>';

	$html	.= '</div>';

	return $html;
}

function show_allergies($pid)
{
	$pid = (int) $pid;
	if( !$pid)  return;

	$query = "select title,reactions,begdate,enddate,comments from lists where pid='".$pid."' and type in (3,7)
				and allergy_status = 'Active'";

	$sql   = imw_query($query);
	$html  = '';

	$html	.= '<div id="allergies_patient" class="modal" role="dialog" style="top:60px;" >';

	$html .= '<div class="modal-dialog modal-lg">';
	$html .= '<div class="modal-content">';

	$html .= '<div class="modal-header bg-primary">';
	$html .= '<button type="button" class="close" onClick="showAllergy();">×</button>';
	$html .= '<h4 class="modal-title" id="modal_title">Allergies</h4>';
	$html .= '</div>';

	$html	.= '<div class="modal-body pd5" style="max-height:400px; overflow:hidden; overflow-y:auto;">';
	$html	.= '<table id="tbl_allergies_grid" class="table table-bordered table-striped table-hover">';
	$html	.= '<thead class="grythead">';
	$html	.= '<tr >';
	$html	.= '<th class="col-xs-3">Name</td>';
	$html	.= '<th class="col-xs-2">Reactions</td>';
	$html	.= '<th class="col-xs-5">Comments</td>';
	$html	.= '<th class="col-xs-2">Start Date</td>';
	$html	.= '</tr>';
	$html	.= '</thead>';
	$html	.= '<tbody>';

	while( $row = imw_fetch_assoc($sql))
	{
		$start_date = ($row['begdate'] <> '' &&  get_number($row['begdate']) <> '00000000' && $row['begdate'] <> '--') ? $row['begdate'] : '';

		$html	.= '<tr>';
		$html	.= '<td>'.$row['title'].'</td>';
		$html	.= '<td><b>'.$row['reactions'].'</b></td>';
		$html	.= '<td>'.$row['comments'].'</td>';
		$html	.= '<td>'.$start_date.'</td>';
		$html	.= '</tr>';
	}

	if( imw_num_rows($sql) <= 0 )
	{
		$html	.= '<tr><td colspan="4" align="center" class="alert alert-info margin_0">Allergies not recorded</td></tr>';
	}
	$html	.= '</tbody>';
	$html	.= '</table>';

	$html	.= '</div>';

	$html	.= '</div>';
	$html	.= '</div>';

	$html	.= '</div>';

	return $html;
}


function getPatient_Chart_Search($pid,$term){

	$arr=array();
	$str = "";

	//CC HX
	$sql = "	SELECT
			c1.id AS form_id, c1.finalize, c1.releaseNumber, c1.date_of_service,
			c2.reason, c2.ccompliant,
			c3.assess_plan,
			c4.summaryOd AS summaryOd_cvf, c4.summaryOs AS summaryOs_cvf,
			c6.sumEom,
			c7.summaryOd, c7.summaryOs,
			c8.gonio_od_summary, c8.gonio_os_summary,
			c9.sumOdIop, c9.sumOsIop,

			c10.lid_conjunctiva_summary, c21.lesion_summary, c22.lid_deformity_position_summary,c23.lacrimal_system_summary,
			c10.sumLidsOs, c22.sumLidPosOs, c21.sumLesionOs, c23.sumLacOs,

			c13.sumOOD_od, c13.sumOOD_os,
			c14.optic_nerve_od_summary, c14.optic_nerve_os_summary,
			c16.sumOdPupil, c16.sumOsPupil,
			c17.sumOdRefSurg, c17.sumOsRefSurg,

			c18.vitreous_od_summary, c18.vitreous_os_summary, c25.blood_vessels_od_summary, c25.blood_vessels_os_summary,
			c27.macula_od_summary, c27.macula_os_summary, c26.periphery_od_summary, c26.periphery_os_summary,
			c24.retinal_od_summary, c24.retinal_os_summary,

			c19.conjunctiva_od_summary, c19.conjunctiva_os_summary,
			c28.cornea_od_summary, c28.cornea_os_summary,
			c29.anf_chamber_od_summary, c29.anf_chamber_os_summary,
			c30.iris_pupil_od_summary, c30.iris_pupil_os_summary,
			c31.lens_od_summary, c31.lens_os_summary,

			c20.memo_id

			FROM chart_master_table c1
			LEFT JOIN chart_left_cc_history c2 ON c1.id = c2.form_id
			LEFT JOIN chart_assessment_plans c3 ON c1.id = c3.form_id
			LEFT JOIN chart_cvf  c4 ON c1.id = c4.formId
			LEFT JOIN chart_dialation  c5 ON c1.id = c5.form_id
			LEFT JOIN chart_eom  c6 ON c1.id = c6.form_id
			LEFT JOIN chart_diplopia c7 ON c1.id = c7.formId
			LEFT JOIN chart_gonio c8 ON c1.id = c8.form_id
			LEFT JOIN chart_iop c9 ON c1.id = c9.form_id
			LEFT JOIN chart_lids c10 ON c1.id = c10.form_id
			LEFT JOIN chart_lid_pos c22 ON c1.id = c22.form_id
			LEFT JOIN chart_lac_sys c23 ON c1.id = c23.form_id
			LEFT JOIN chart_lesion c21 ON c1.id = c21.form_id
			".
			/*"LEFT JOIN chart_memo_text c11 ON c1.id = c11.form_id
			LEFT JOIN chart_objective_notes c12 ON c1.id = c12.form_id".*/
			"LEFT JOIN chart_ood c13 ON c1.id = c13.form_id ".
			"LEFT JOIN chart_optic c14 ON c1.id = c14.form_id ".
			//"LEFT JOIN chart_procedures c15 ON c1.id = c15.form_id".
			"LEFT JOIN chart_pupil c16 ON c1.id = c16.formId
			LEFT JOIN chart_ref_surgery c17 ON c1.id = c17.form_id

			LEFT JOIN chart_vitreous c18 ON c1.id = c18.form_id
			LEFT JOIN chart_retinal_exam c24 ON c1.id = c24.form_id
			LEFT JOIN chart_blood_vessels c25 ON c1.id = c25.form_id
			LEFT JOIN chart_periphery c26 ON c1.id = c26.form_id
			LEFT JOIN chart_macula c27 ON c1.id = c27.form_id

			LEFT JOIN chart_conjunctiva c19 ON c1.id = c19.form_id
			LEFT JOIN chart_cornea c28 ON c1.id = c28.form_id
			LEFT JOIN chart_ant_chamber c29 ON c1.id = c29.form_id
			LEFT JOIN chart_iris c30 ON c1.id = c30.form_id
			LEFT JOIN chart_lens c31 ON c1.id = c31.form_id

			LEFT JOIN memo_tbl c20 ON c1.id = c20.form_id
			WHERE c1.patient_id = '".$pid."' AND c1.delete_status = '0'
		";
	$rez = sqlStatement($sql);
	for($i=0; $row = sqlFetchArray($rez);$i++){

		$form_id = $row["form_id"];
		$chartStatus = ($row["finalize"] == "1") ? "Final" : "Active"; //"Finalized"
		$releaseNum = $row["releaseNumber"];

		$str2 = "";

		if(stripos($row["reason"], $term)!== false||stripos($row["ccompliant"], $term)!== false){
			$tmp="";
			if(stripos($row["reason"], $term)!== false){
				$tmp = setPatient_Chart_Search_Bold($row["reason"], $term);
			}else if(stripos($row["ccompliant"], $term)!== false){
				$tmp = setPatient_Chart_Search_Bold($row["ccompliant"], $term);
			}

			$str2 .= "<li><label>CC HX. - </label><label class=\"lbl_exmp\">".$tmp."</label></li>";
		}

		if(stripos($row["assess_plan"], $term)!== false){
			$oChartApXml = new ChartAP();
			$arr_tmp = $oChartApXml->getVal_Str($row["assess_plan"]);
			$tmp="";
			if(count($arr_tmp["data"]["ap"]) > 0){
				foreach($arr_tmp["data"]["ap"] as $key=> $val){
					if(stripos($arr_tmp["data"]["ap"][$key]["assessment"], $term)!== false){
						$tmp = setPatient_Chart_Search_Bold($arr_tmp["data"]["ap"][$key]["assessment"], $term);
						$str2 .= "<li><label>Assessment & Plan - </label><label class=\"lbl_exmp\">".$tmp."</label></li>";

					}else	if(stripos($arr_tmp["data"]["ap"][$key]["plan"], $term)!== false){
						$tmp = setPatient_Chart_Search_Bold($arr_tmp["data"]["ap"][$key]["plan"], $term);
						$str2 .= "<li><label>Assessment & Plan - </label><label class=\"lbl_exmp\">".$tmp."</label></li>";
					}
				}
			}

		}

		if(stripos($row["summaryOd_cvf"], $term)!== false || stripos($row["summaryOs_cvf"], $term)!== false){
			$tmp="";
			if(stripos($row["summaryOd_cvf"], $term)!== false){ $tmp= setPatient_Chart_Search_Bold($row["summaryOd_cvf"], $term);}
			else if(stripos($row["summaryOs_cvf"], $term)!== false){ $tmp= setPatient_Chart_Search_Bold($row["summaryOs_cvf"], $term);}
			$str2 .= "<li><label>CVF - </label><label class=\"lbl_exmp\">".$tmp."</label></li>";
		}

		if(stripos($row["sumEom"], $term)!== false){
			$tmp="";
			$tmp= setPatient_Chart_Search_Bold($row["sumEom"], $term);
			$str2 .= "<li><label>EOM - </label><label class=\"lbl_exmp\">".$tmp."</label></li>";
		}

		if(stripos($row["summaryOd"], $term)!== false || stripos($row["summaryOs"], $term)!== false){
			$tmp="";
			if(stripos($row["summaryOd"], $term)!== false){ $tmp= setPatient_Chart_Search_Bold($row["summaryOd"], $term);}
			else if(stripos($row["summaryOs"], $term)!== false){ $tmp= setPatient_Chart_Search_Bold($row["summaryOs"], $term);}
			$str2 .= "<li><label>Diplopia - </label><label class=\"lbl_exmp\">".$tmp."</label></li>";
		}

		if(stripos($row["gonio_od_summary"], $term)!== false || stripos($row["gonio_os_summary"], $term)!== false){
			$tmp="";
			if(stripos($row["gonio_od_summary"], $term)!== false){ $tmp= setPatient_Chart_Search_Bold($row["gonio_od_summary"], $term);}
			if(stripos($row["gonio_os_summary"], $term)!== false){ $tmp= setPatient_Chart_Search_Bold($row["gonio_os_summary"], $term);}
			$str2 .= "<li><label>Gonio - </label><label class=\"lbl_exmp\">".$tmp."</label></li>";
		}

		if(stripos($row["sumOdIop"], $term)!== false || stripos($row["sumOsIop"], $term)!== false){
			$tmp="";
			if(stripos($row["sumOdIop"], $term)!== false){ $tmp= setPatient_Chart_Search_Bold($row["sumOdIop"], $term);}
			if(stripos($row["sumOsIop"], $term)!== false){ $tmp= setPatient_Chart_Search_Bold($row["sumOsIop"], $term);}
			$str2 .= "<li><label>IOP - </label><label class=\"lbl_exmp\">".$tmp."</label></li>";
		}

		if(stripos($row["lid_conjunctiva_summary"], $term)!== false || stripos($row["sumLidsOs"], $term)!== false){
			$tmp="";
			if(stripos($row["lid_conjunctiva_summary"], $term)!== false){ $tmp= setPatient_Chart_Search_Bold($row["lid_conjunctiva_summary"], $term);}
			if(stripos($row["sumLidsOs"], $term)!== false){ $tmp= setPatient_Chart_Search_Bold($row["sumLidsOs"], $term);}
			$str2 .= "<li><label>Conjunctiva - </label><label class=\"lbl_exmp\">".$tmp."</label></li>";
		}

		if(stripos($row["lesion_summary"], $term)!== false || stripos($row["sumLesionOs"], $term)!== false){
			$tmp="";
			if(stripos($row["lesion_summary"], $term)!== false){ $tmp= setPatient_Chart_Search_Bold($row["lesion_summary"], $term);}
			if(stripos($row["sumLesionOs"], $term)!== false){ $tmp= setPatient_Chart_Search_Bold($row["sumLesionOs"], $term);}
			$str2 .= "<li><label>Lesion - </label><label class=\"lbl_exmp\">".$tmp."</label></li>";
		}

		if(stripos($row["lid_deformity_position_summary"], $term)!== false || stripos($row["sumLidPosOs"], $term)!== false){
			$tmp="";
			if(stripos($row["lid_deformity_position_summary"], $term)!== false){ $tmp= setPatient_Chart_Search_Bold($row["lid_deformity_position_summary"], $term);}
			if(stripos($row["sumLidPosOs"], $term)!== false){ $tmp= setPatient_Chart_Search_Bold($row["sumLidPosOs"], $term);}
			$str2 .= "<li><label>Lid Position - </label><label class=\"lbl_exmp\">".$tmp."</label></li>";
		}

		if(stripos($row["lacrimal_system_summary"], $term)!== false || stripos($row["sumLacOs"], $term)!== false){
			$tmp="";
			if(stripos($row["lacrimal_system_summary"], $term)!== false){ $tmp= setPatient_Chart_Search_Bold($row["lacrimal_system_summary"], $term);}
			if(stripos($row["sumLacOs"], $term)!== false){ $tmp= setPatient_Chart_Search_Bold($row["sumLacOs"], $term);}
			$str2 .= "<li><label>Lacrimal System - </label><label class=\"lbl_exmp\">".$tmp."</label></li>";
		}

		if(stripos($row["sumOOD_od"], $term)!== false || stripos($row["sumOOD_os"], $term)!== false){
			$tmp="";
			if(stripos($row["sumOOD_od"], $term)!== false){ $tmp= setPatient_Chart_Search_Bold($row["sumOOD_od"], $term);}
			if(stripos($row["sumOOD_os"], $term)!== false){ $tmp= setPatient_Chart_Search_Bold($row["sumOOD_os"], $term);}
			$str2 .= "<li><label>OOD - </label><label class=\"lbl_exmp\">".$tmp."</label></li>";
		}

		if(stripos($row["optic_nerve_od_summary"], $term)!== false || stripos($row["optic_nerve_os_summary"], $term)!== false){
			$tmp="";
			if(stripos($row["optic_nerve_od_summary"], $term)!== false){ $tmp= setPatient_Chart_Search_Bold($row["optic_nerve_od_summary"], $term);}
			if(stripos($row["optic_nerve_os_summary"], $term)!== false){ $tmp= setPatient_Chart_Search_Bold($row["optic_nerve_os_summary"], $term);}
			$str2 .= "<li><label>Optic Nerve - </label><label class=\"lbl_exmp\">".$tmp."</label></li>";
		}

		if(stripos($row["sumOdPupil"], $term)!== false || stripos($row["sumOsPupil"], $term)!== false){
			$tmp="";
			if(stripos($row["sumOdPupil"], $term)!== false){ $tmp= setPatient_Chart_Search_Bold($row["sumOdPupil"], $term);}
			if(stripos($row["sumOsPupil"], $term)!== false){ $tmp= setPatient_Chart_Search_Bold($row["sumOsPupil"], $term);}
			$str2 .= "<li><label>Pupil - </label><label class=\"lbl_exmp\">".$tmp."</label></li>";
		}

		if(stripos($row["sumOdRefSurg"], $term)!== false || stripos($row["sumOsRefSurg"], $term)!== false){
			$tmp="";
			if(stripos($row["sumOdRefSurg"], $term)!== false){ $tmp= setPatient_Chart_Search_Bold($row["sumOdRefSurg"], $term);}
			if(stripos($row["sumOsRefSurg"], $term)!== false){ $tmp= setPatient_Chart_Search_Bold($row["sumOsRefSurg"], $term);}
			$str2 .= "<li><label>Ref Surgery - </label><label class=\"lbl_exmp\">".$tmp."</label></li>";
		}

		if(stripos($row["vitreous_od_summary"], $term)!== false || stripos($row["vitreous_os_summary"], $term)!== false){
			$tmp="";
			if(stripos($row["vitreous_od_summary"], $term)!== false){ $tmp= setPatient_Chart_Search_Bold($row["vitreous_od_summary"], $term);}
			if(stripos($row["vitreous_os_summary"], $term)!== false){ $tmp= setPatient_Chart_Search_Bold($row["vitreous_os_summary"], $term);}
			$str2 .= "<li><label>Vitreous - </label><label class=\"lbl_exmp\">".$tmp."</label></li>";
		}

		if(stripos($row["blood_vessels_od_summary"], $term)!== false || stripos($row["blood_vessels_os_summary"], $term)!== false){
			$tmp="";
			if(stripos($row["blood_vessels_od_summary"], $term)!== false){ $tmp= setPatient_Chart_Search_Bold($row["blood_vessels_od_summary"], $term);}
			if(stripos($row["blood_vessels_os_summary"], $term)!== false){ $tmp= setPatient_Chart_Search_Bold($row["blood_vessels_os_summary"], $term);}
			$str2 .= "<li><label>Blood Vessels - </label><label class=\"lbl_exmp\">".$tmp."</label></li>";
		}

		if(stripos($row["macula_od_summary"], $term)!== false || stripos($row["macula_os_summary"], $term)!== false){
			$tmp="";
			if(stripos($row["macula_od_summary"], $term)!== false){ $tmp= setPatient_Chart_Search_Bold($row["macula_od_summary"], $term);}
			if(stripos($row["macula_os_summary"], $term)!== false){ $tmp= setPatient_Chart_Search_Bold($row["macula_os_summary"], $term);}
			$str2 .= "<li><label>Macula - </label><label class=\"lbl_exmp\">".$tmp."</label></li>";
		}

		if(stripos($row["periphery_od_summary"], $term)!== false || stripos($row["periphery_os_summary"], $term)!== false){
			$tmp="";
			if(stripos($row["periphery_od_summary"], $term)!== false){ $tmp= setPatient_Chart_Search_Bold($row["periphery_od_summary"], $term);}
			if(stripos($row["periphery_os_summary"], $term)!== false){ $tmp= setPatient_Chart_Search_Bold($row["periphery_os_summary"], $term);}
			$str2 .= "<li><label>Periphery - </label><label class=\"lbl_exmp\">".$tmp."</label></li>";
		}

		if(stripos($row["retinal_od_summary"], $term)!== false || stripos($row["retinal_os_summary"], $term)!== false){
			$tmp="";
			if(stripos($row["retinal_od_summary"], $term)!== false){ $tmp= setPatient_Chart_Search_Bold($row["retinal_od_summary"], $term);}
			if(stripos($row["retinal_os_summary"], $term)!== false){ $tmp= setPatient_Chart_Search_Bold($row["retinal_os_summary"], $term);}
			$str2 .= "<li><label>Retinal Exam - </label><label class=\"lbl_exmp\">".$tmp."</label></li>";
		}

		if(stripos($row["conjunctiva_od_summary"], $term)!== false || stripos($row["conjunctiva_os_summary"], $term)!== false){
			$tmp="";
			if(stripos($row["conjunctiva_od_summary"], $term)!== false){ $tmp= setPatient_Chart_Search_Bold($row["conjunctiva_od_summary"], $term);}
			if(stripos($row["conjunctiva_os_summary"], $term)!== false){ $tmp= setPatient_Chart_Search_Bold($row["conjunctiva_os_summary"], $term);}
			$str2 .= "<li><label>Conjunctiva - </label><label class=\"lbl_exmp\">".$tmp."</label></li>";
		}

		if(stripos($row["cornea_od_summary"], $term)!== false || stripos($row["cornea_os_summary"], $term)!== false){
			$tmp="";
			if(stripos($row["cornea_od_summary"], $term)!== false){ $tmp= setPatient_Chart_Search_Bold($row["cornea_od_summary"], $term);}
			if(stripos($row["cornea_os_summary"], $term)!== false){ $tmp= setPatient_Chart_Search_Bold($row["cornea_os_summary"], $term);}
			$str2 .= "<li><label>Cornea - </label><label class=\"lbl_exmp\">".$tmp."</label></li>";
		}

		if(stripos($row["anf_chamber_od_summary"], $term)!== false || stripos($row["anf_chamber_os_summary"], $term)!== false){
			$tmp="";
			if(stripos($row["anf_chamber_od_summary"], $term)!== false){ $tmp= setPatient_Chart_Search_Bold($row["anf_chamber_od_summary"], $term);}
			if(stripos($row["anf_chamber_os_summary"], $term)!== false){ $tmp= setPatient_Chart_Search_Bold($row["anf_chamber_os_summary"], $term);}
			$str2 .= "<li><label>Ant. Chamber - </label><label class=\"lbl_exmp\">".$tmp."</label></li>";
		}

		if(stripos($row["iris_pupil_od_summary"], $term)!== false || stripos($row["iris_pupil_os_summary"], $term)!== false){
			$tmp="";
			if(stripos($row["iris_pupil_od_summary"], $term)!== false){ $tmp= setPatient_Chart_Search_Bold($row["iris_pupil_od_summary"], $term);}
			if(stripos($row["iris_pupil_os_summary"], $term)!== false){ $tmp= setPatient_Chart_Search_Bold($row["iris_pupil_os_summary"], $term);}
			$str2 .= "<li><label>Iris - </label><label class=\"lbl_exmp\">".$tmp."</label></li>";
		}

		if(stripos($row["lens_od_summary"], $term)!== false || stripos($row["lens_os_summary"], $term)!== false){
			$tmp="";
			if(stripos($row["lens_od_summary"], $term)!== false){ $tmp= setPatient_Chart_Search_Bold($row["lens_od_summary"], $term);}
			if(stripos($row["lens_os_summary"], $term)!== false){ $tmp= setPatient_Chart_Search_Bold($row["lens_os_summary"], $term);}
			$str2 .= "<li><label>Lens - </label><label class=\"lbl_exmp\">".$tmp."</label></li>";
		}


		if(!empty($row["memo_id"])){
			$sql2 = "SELECT memo_text FROM chart_memo_text WHERE memo_id = '".$row["memo_id"]."' ";
			$rez2 = sqlStatement($sql2);
			for($ii=0; $row2=sqlFetchArray($rez2);$ii++){
				if(stripos($row2["memo_text"], $term)!== false){
					$tmp="";
					$tmp= setPatient_Chart_Search_Bold($row2["memo_text"], $term);
					$str2 .= "<li><label>Memo - </label><label class=\"lbl_exmp\">".$tmp."</label></li>";
				}
			}
		}


		//if(count($arr[$form_id])>0){
		if(!empty($str2)){
			$DateOFService = date(''.phpDateFormat().'', strtotime($row["date_of_service"]));
			$cssrow=(empty($cssrow)) ? "" : "";
			$str2="<ul class=\"yamma\">".$str2."</ul>";
			$str .= "<tr ".$cssrow."><td style=\"vertical-align:baseline\" class=\"text-nowrap\"><a href=\"#\" onclick=\"openThisChart('".$form_id."', '".$chartStatus."', '".$releaseNum."')\">".$DateOFService."</a></td><td valign=\"top\" >".$str2."</td>";

		}

	}

	if(!empty($str)){
		$str = "<table class='table table-bordered table-striped margin_0'><tr class='grythead'><td class='text-nowrap'>DOS</td><td>Exams</td></tr>".$str."</table>";
	}else{
		$str = "<div class='alert alert-info text-center'>No Record found for '".$term."'</div>";
	}

	return $str;
}

function setPatient_Chart_Search_Bold($str,$term){
	if(!empty($str)){
		$str = str_ireplace($term,"<label class=\"alert-success\">$term</label>",$str);
		$str = nl2br($str);
	}
	return $str;
}

function general_health_div($pid,$returnData = 'All')
{

	$cur_frm_id = $_SESSION["form_id"];
	$scnMedImg = '';

	//if chart note is fianlzied, show Genral Health of that visit
	$flgArc=0;
	if(isset($_SESSION["finalize_id"]) && !empty($_SESSION["finalize_id"]) )
	{
		$finalize_id=$_SESSION["finalize_id"];
		$cur_frm_id = $finalize_id;
		list($isReviewable,$isEditable,$iscur_user_vphy) = isChartReviewable($cur_frm_id,$_SESSION["authId"],1);
		if($isReviewable!="1"){ $cur_frm_id=0; }

		//Get From Achive --
		$qry = "SELECT * FROM chart_genhealth_archive WHERE patient_id='".$pid."' AND form_id='".$finalize_id."' ";
		$sql = imw_query($qry);
		$row = imw_fetch_assoc($sql);
		if($row!=false)
		{
			if(!empty($row["ocular"])){
				$arrLogOc=unserialize($row["ocular"]);
			}else{
				$arrLogOc=false;
			}

			$arrLists = unserialize($row["lists"]);
			$arrGenMed = unserialize($row["general_medicine"]);
			$arrPtBS = unserialize($row["patient_blood_sugar"]);
			$arrPtCh = unserialize($row["patient_cholesterol"]);
			$arrSH = unserialize($row["social_history"]);
			$arrImm = unserialize($row["immunizations"]);
			$arrPtProbList = unserialize($row["pt_problem_list"]);
			$arrPtD = unserialize($row["patient_data"]);
			$arrCNMHx=unserialize($row["commonnomedicalhistory"]);
			$flgArc=1;
		}
		//Get From Achive --
	}


	// Collecting Social Data
	$social_data = '';
	$socialQryRes = array();
	if( $flgArc == 1 && count($arrSH) > 0 ){
		$socialQryRes[0] = $arrSH;
	}
	else
	{
		$query = "select * from social_history where patient_id = '".$pid."'";
		$sql = imw_query($query);
		while( $row = imw_fetch_assoc($sql) )
		{
			array_push($socialQryRes,$row);
		}
	}

	if(count($socialQryRes) > 0)
	{
		$smoke_status = $socialQryRes[0]["smoking_status"];
		$arrSmoke = explode("/", $smoke_status);
		$smoke_status = $arrSmoke[0];

		$smokePerDay = $socialQryRes[0]["smoke_perday"];
		$number_of_years_with_smoke = $socialQryRes[0]["number_of_years_with_smoke"];
		$smoke_years_months  = $socialQryRes[0]["smoke_years_months"];
		$anyDrugs = $socialQryRes[0]["list_drugs"];
		$alcohal = ($socialQryRes[0]["alcohal"] == 'Other') ? $socialQryRes[0]["source_of_alcohal_other"] : $socialQryRes[0]["alcohal"];
		$otherSocial = $socialQryRes[0]["otherSocial"];
		$source_of_smoke = ($socialQryRes[0]["source_of_smoke"] == 'Other') ? $socialQryRes[0]["source_of_smoke_other"] : $socialQryRes[0]["source_of_smoke"];
		$smoke_str = (($source_of_smoke) ? (($smokePerDay) ? $smokePerDay : "")."&nbsp;".$source_of_smoke : "")."&nbsp;Per Day&nbsp;"."for ".(($number_of_years_with_smoke) ? $number_of_years_with_smoke ."&nbsp;".$smoke_years_months : "");

		$alcohal_str = $socialQryRes[0]["consumption"] .' ' . $socialQryRes[0]["alcohal_time"];
		$alcohal_str = trim($alcohal_str);

    if(trim($smoke_status)!=""){
			$smoke_data .= '<tr><td><b>Smoke: </b>'.$smoke_status.'</td></tr>';
		}
		if(!preg_match("/Never Smoked/i",trim($smoke_status)) && trim($smoke_status)!="" && $smokePerDay != ""){
			$smoke_data .= '<tr><td><b>Smoke Frequency: </b>'.$smoke_str.'</td></tr>';
		}
		if(trim($socialQryRes[0]["family_smoke"])=="1"){
			$smokefamily = (!empty($socialQryRes[0]["smokers_in_relatives"])) ? $socialQryRes[0]["smokers_in_relatives"] : '';
			$smokefamilyDesc = (!empty($socialQryRes[0]["smoke_description"])) ? $socialQryRes[0]["smoke_description"] : '';
      $smoke_data .= '<tr><td><b>Family: </b>'.$smokefamily.'. <b>Description: </b> '.$smokefamilyDesc.'</td></tr>';
		}

		if($socialQryRes[0]["smoke_counseling"]=="1")
		{
			if($socialQryRes[0]["offered_cessation_counselling_date"] == "0000-00-00"){
				$socialQryRes[0]["offered_cessation_counselling_date"]="";
			}
			$smoke_data .= '<tr><td><b>Cessation Counseling: </b>'.get_date_format($socialQryRes[0]["offered_cessation_counselling_date"]).'</td></tr>';
		}
		if(!empty($anyDrugs)){
			$smoke_data .= '<tr><td><b>Drugs: </b>'.$anyDrugs.'</td></tr>';
		}

		if(!empty($alcohal)){
    	$smoke_data .= '<tr><td><b>Alcohol: </b></span> '.$alcohal.'</td></tr>';
		}
		if(!preg_match("/Never/i",trim($alcohal)) && trim($alcohal)!="" && $alcohal_str != ""){
			$smoke_data .= '<tr><td><b>Alcohol Frequency: </b>'.$alcohal_str.'</td></tr>';
		}
		if(!empty($otherSocial)){
			$smoke_data .= '<tr><td><b>Other : </b>'.nl2br($otherSocial).'</td></tr>';
		}
	}

	if( $returnData == 'socialData' ) {

		return array('html'=>$smoke_data,'form_data'=>createSocialFldForm($socialQryRes[0]));
	}

	//$categoryFolderArr = array('Allergies','Immunization','Medication','Sx/Procedures','Vital Sign');
	$categoryFolderArr = array('Medication');
	$medInsrtId='';
	foreach($categoryFolderArr as $categoryFolderNme) {
		$ctId = scnfoldrCatIdFun(constant("IMEDIC_SCAN_DB"),$categoryFolderNme);
		if($ctId) {
			if($categoryFolderNme == 'Medication') { $medInsrtId = $ctId; }
		}
	}

	$g2_data = '';
	$arrOcuInfo = getPtGenHealthOcularInfo($pid,array($flgArc,$arrLogOc));
	$patData = "";
	//Contact Lens
	if(!empty($arrOcuInfo["CLens"]))
	{
		$patData .= "<tr><td class=\"text_10\">&bull;&nbsp;Wears: ".$arrOcuInfo["CLens"]."</td></tr>";
	}

	//Chronic
	if( count($arrOcuInfo["ChroCond"]) > 0 )
	{
		foreach( $arrOcuInfo["ChroCond"] as $key => $val ){
			$val = trim($val);
			if(!empty($val)){
				$patData .= "<tr><td class=\"text_10\">&bull;&nbsp;".$val."</td></tr>";
			}
		}
		//echo $patData;
	}

	$relData = "";
	if( count($arrOcuInfo["ChroCondRel"]) > 0 )
	{
		foreach( $arrOcuInfo["ChroCondRel"] as $key => $val ){
			$val = trim($val);
			if(!empty($val)){
				$relData .= "<tr><td class=\"text_10\">&bull;&nbsp;".$val."</td></tr>";
			}
		}
		//echo $relData;
	}

	//Acute
	if( is_array($arrOcuInfo["ActCond"]) && count($arrOcuInfo["ActCond"]) > 0 )
	{
		$acute = '';
		foreach( $arrOcuInfo["ActCond"] as $key => $val ){
			$acute .= "<tr><td class=\"text_10\">&bull;&nbsp;".$val."</td></tr>";
		}
	}

	$qry = "select cf.id as adminControlId, cf.control_lable as adminControlLable,
									pcf.patient_control_value as patientControlVal,
									pcf.patient_cbk_control_value as patientCbkControlVal ,
									pcf.patient_control_value as patientCustomQues
									FROM
									custom_fields cf
									JOIN patient_custom_field pcf on
									cf.id = pcf.admin_control_id
									and pcf.patient_id = '".$PatientId."'
									where cf.module = 'Med_Hx'
										and cf.sub_module ='Medical Hx -> Ocular'
										and cf.status = '0'
										and (pcf.patient_control_value != '' || pcf.patient_cbk_control_value != '')
									order by cf.control_lable ";
	$result = imw_query($qry);
	$misc_data = '';
	if( imw_num_rows($result) > 0)
	{
		$misc_data  = '<tr><td colspan="2" class="col-xs-12"><div class="row">';
		$misc_data .= '<table class="table margin_0">';
		$misc_data .= '<thead>';
		$misc_data .= '<tr><th>&bull;&nbsp;<b>Miscellaneous</th></tr>';
		$misc_data .= '</thead>';
		$misc_data .= '<tbody>';

		$td = 1;
		while($row = imw_fetch_assoc($result))
		{
			$misc_data	.= '<tr>';
			if($row['patientControlVal'] != '' || $row['patientCbkControlVal'] != '')
				$misc_data	.= '<td>&bull;&nbsp;'.$row['adminControlLable']." : ".$row['patientControlVal'].'</td>';

			$row = imw_fetch_assoc($result);
			if($row['patientControlVal'] != '' || $row['patientCbkControlVal'] != '')
				$misc_data	.= '<td>&bull;&nbsp;'.$row['adminControlLable']." : ".$row['patientControlVal'].'</td>';

			$misc_data	.= '</tr>';
		}

		$misc_data	.= '</tbody>';
		$misc_data	.= '</table>';
		$misc_data	.= '</div></td></tr>';
	}

	$spec_data = '';
	$specQuesQry = 'select admMed.id,admMed.answer_type,admMed.ques,pt_spec_ques.pat_answer,pt_spec_ques.med_hx_tab
										from admn_medhx admMed
										JOIN admn_medhx_tab admMedHx on admMed.id = admMedHx.admn_medhx_question_id
										JOIN patient_specialty_question_answer pt_spec_ques on admMed.id = pt_spec_ques.question_id
										where pt_spec_ques.med_hx_tab = "Ocular" AND pt_spec_ques.patient_id = '.$pid.' Order By admMed.ques';
	$GetSpecQues = imw_query($specQuesQry);
	if( imw_num_rows($GetSpecQues) > 0 )
	{
		$spec_data  = '<tr><td colspan="2" class="col-xs-12"><div class="row">';
		$spec_data .= '<table class="table margin_0">';
		$spec_data .= '<thead>';
		$spec_data .= '<tr><th>&bull;&nbsp;<b>Specialty Question(s)</th></tr>';
		$spec_data .= '</thead>';
		$spec_data .= '<tbody>';
		$td = 1;
		while( $row = imw_fetch_assoc($GetSpecQues))
		{
			if($row['answer_type'] == '0')
			{
				$spec_data .= '<tr>';
				$spec_data .= '<td><b>&bull;&nbsp;'.$row['ques']."</b> : ".$row['pat_answer'].'</td>';
				$spec_data .= '</tr>';
			}
			else
			{
				$get_multi_opt = "select group_concat(med_hx_question_answer_options.option_value) as opt_val,patient_specialty_question_options_answer.* from patient_specialty_question_answer join patient_specialty_question_options_answer on patient_specialty_question_answer.id=patient_specialty_question_options_answer.patient_specialty_question_answer_id
join med_hx_question_answer_options on med_hx_question_answer_options.id=patient_specialty_question_options_answer.option_id where patient_specialty_question_answer.question_id = '".$row['id']."' and patient_specialty_question_answer.patient_id =  '".$pid."' group by patient_specialty_question_answer.question_id";
				$multi_opt_data = imw_query($get_multi_opt);
				$counter = 0;
				while( $fetch_data = imw_fetch_assoc($multi_opt_data))
				{
					$spec_data .= '<tr>';
					$spec_data .= '<td><b>&bull;&nbsp;'.$row['ques']."</b> : ".$fetch_data['opt_val'].'</td>';
					$spec_data .= '</tr>';
				}
			}
		}

		$spec_data .= '</tbody>';

		$spec_data .= '</table>';
		$spec_data .= '</div></td></tr>';
	}

	// Collecting Ocular Medication Data ;
	$medQryRes = array();
	if($flgArc==1){
		$medQryRes = $arrLists[4];
	}
	else{
		$query = "select title,destination,sig,med_comments,allergy_status,sites,
											date_format(begdate, '".get_sql_date_format()."') as begdate_2
											from lists where pid = '$pid'
											and type IN (4) and allergy_status IN ('Active','Order')";
		$sql = imw_query($query);
		while( $row = imw_fetch_assoc($sql))
		{
			array_push($medQryRes,$row);
		}
	}

	$no_ocular_med = $no_ocular_reviewed = '';
	if( !$medQryRes && $flgArc != 1)
	{
		$strNoMedication = commonNoMedicalHistoryAddEdit($moduleName="Medication",$moduleValue="",$mod="get");
		if($strNoMedication == "checked"){
			$no_ocular_med = '<b class="col-xs-12 text-center alert alert-info margin_0">No Known Medications</b>';
		}
		else
		{
			$query = "select title,destination,sig,med_comments, allergy_status,
									date_format(begdate, '".get_sql_date_format()."') as begdate_2
									from lists where pid = '".$pid."' and type IN (1) and allergy_status IN ('Active','Order')";
			$sql = imw_query($query);
			$cnt = imw_num_rows($sql);
			if( $cnt <= 0){
				$no_ocular_reviewed = '<b class="col-xs-12 text-center alert alert-info margin_0">Not Reviewed</b>';
			}
		}
	}

	// Collecting Ocular Surgeries Data
	$ocular_sx = array();
	if($flgArc==1){
		$ocular_sx = $arrLists[6];
	}
	else {
		$query = "select title,sites,referredby,allergy_status,comments,
											if((DAY(begdate)='00' OR DAY(begdate)='0')&& (MONTH(begdate)='00' OR MONTH(begdate)='0'),date_format(begdate, '%Y'),
											if(MONTH(begdate)='00' OR MONTH(begdate)='0',date_format(begdate,'%Y'),
											if(DAY(begdate)='00' or DAY(begdate)='0',date_format(begdate,'%m-%Y'),
											date_format(begdate,'".get_sql_date_format()."')
											)))as begdate_2
											from lists
											where
											pid = '".$pid."'  and type IN (6) and allergy_status != 'Deleted'
											order by begdate desc";

		$sql = imw_query($query);
		while( $row = imw_fetch_assoc($sql))
		{
			array_push($ocular_sx,$row);
		}
	}

	$no_ocular_sx = $no_ocular_sx_reviewed = '';
	if( !$ocular_sx && $flgArc != 1)
	{
		$strNoSurgery = commonNoMedicalHistoryAddEdit($moduleName="Surgery",$moduleValue="",$mod="get");
		if($strNoSurgery == "checked"){
			$no_ocular_sx = '<b class="col-xs-12 text-center alert alert-info margin_0">No Known Surgeries</b>';
		}
		else
		{
			$query = "select title,referredby,comments,
												if((DAY(begdate)='00' OR DAY(begdate)='0')&& (MONTH(begdate)='00' OR MONTH(begdate)='0'),date_format(begdate, '%Y'),
												if(MONTH(begdate)='00' OR MONTH(begdate)='0',date_format(begdate,'%Y'),
												(DAY(begdate)='00' or DAY(begdate)='0',date_format(begdate,'%m-%Y'),
												date_format(begdate,'".get_sql_date_format()."') )))as begdate_2
												from lists
												where
												pid = '".$pid."' and type IN (5) and allergy_status != 'Deleted'
												order by begdate desc";
			$sql = imw_query($query);
			$cnt = imw_num_rows($sql);
			if( $cnt <= 0){
				$no_ocular_sx_reviewed = '<b class="col-xs-12 text-center alert alert-info margin_0">Not Reviewed</b>';
			}
		}
	}

	// Collecting General Health PAtient Problem Data
	$gnDataPat = $gnDataRel = $gn_data = "";
	$gn_con_arr = array();
  $gn_con_arr[1] = 'High Blood Pressure';
	$gn_con_arr[7] = 'Arthritis';
	$gn_con_arr[5] = 'Stroke';
	$gn_con_arr[3] = 'Diabetes';
	$gn_con_arr[8] = 'Ulcers';
	$gn_con_arr[2] = 'Heart Problems';
	$gn_con_arr[4] = 'Lung Problems';
	$gn_con_arr[6] = 'Thyroid Problems';
	$gn_con_arr[13] = 'LDL';
	$gn_con_arr[14] = 'Cancer';

	$genHealthQryRes = array();
	if($flgArc==1){
		$genHealthQryRes[0] = $arrGenMed;
	}
	else{
		$query = "select * from general_medicine where patient_id = '".$pid."'";
		$sql = imw_query($query);
		while( $row = imw_fetch_assoc($sql) )
		{
			array_push($genHealthQryRes, $row);
		}
	}

	$genHlthArr = array();
	$any_conditions_you = substr($genHealthQryRes[0]['any_conditions_you'],1,-1);
	$any_conditions_arr = preg_split('/,/',$any_conditions_you);
 	$seprator = '~|~';
  $chk_under_control_arr = preg_split("/$seprator/",$genHealthQryRes[0]['chk_under_control']);
  $chkUnderControlArr = preg_split("/,/",$chk_under_control_arr[0]);
  $sub_conditions_you_arr = preg_split("/$seprator/",$genHealthQryRes[0]['sub_conditions_you']);

	if((count($any_conditions_arr) > 0) && ($genHealthQryRes[0]["cbk_master_pt_con"] == "yes"))
	{
		foreach($gn_con_arr as $key => $val)
		{
			$field_name = ''; $uc_field = '';
			$txt_clr = ''; $dia_data_val = '';
			$sub_conditions_you_val = '';

			if($key == 1){
				$field_name = 'desc_high_bp';
				$uc_field = 1;
			}
			else if($key == 7){
				$field_name = 'desc_arthrities';
				$uc_field = 3;
				$sub_conditions_you_val = trim($sub_conditions_you_arr[0]);
				$sub_conditions_you_val = preg_replace('/,/',' - ',$sub_conditions_you_val);
				$sub_conditions_you_val = preg_replace('/7.1/',' RA ',$sub_conditions_you_val);
				$sub_conditions_you_val = preg_replace('/7.2/',' OA ',$sub_conditions_you_val);
			}
			else if($key == 4){
				$field_name = 'desc_lung_problem';
        $uc_field = 4;
    	}
			else if($key == 5){
				$field_name = 'desc_stroke';
				$uc_field = 5;
			}
			else if($key == 6){
      	$field_name = 'desc_thyroid_problems';
				$uc_field = 6;
			}
			else if($key == 8){
				$field_name = 'desc_ulcers';
        $uc_field = 9;
			}
			else if($key == 14){
				$field_name = 'desc_cancer';
				$uc_field = 11;
			}
			else if($key == 2){
				$field_name = 'desc_heart_problem';
				$uc_field = 2;
			}
			else if($key == 13){
				$field_name = 'desc_LDL';
				$uc_field = 8;
			}
			else if($key == 3){
				$field_name = 'desc_u';
				$uc_field = 7;
				$txt_clr = 'text-danger';
				$diabetes_values_arr = preg_split("/$seprator/",$genHealthQryRes[0]['diabetes_values']);
				if(trim($diabetes_values_arr[0]) != ''){
					$dia_data_val = ' - '.$diabetes_values_arr[0];
				}
			}

			//--- PROBLEMS UNDER CONTROL CHECK --
			$under_con_data = '';
			if(count($chkUnderControlArr) > 0){
				if(in_array($uc_field,$chkUnderControlArr) === true){
					$under_con_data = ' - under control';
				}
			}

			//--- PROBLEMS DESCRIPTIONS ---
			$desc_val = preg_split("/$seprator/",$genHealthQryRes[0][$field_name]);
			$gn_desc_val = '';
			if(trim($desc_val[0]) != ''){
				$gn_desc_val = ' - '.trim($desc_val[0]);
			}

			if(in_array($key,$any_conditions_arr) === true){
				$gnDataPat .= '<tr><td class="'.$txt_clr.'">&bull;&nbsp;'.$val .$dia_data_val .$sub_conditions_you_val .$under_con_data . $gn_desc_val.'</td></tr>';
			}
		}
 	}
 	elseif($genHealthQryRes[0]["cbk_master_pt_con"] == "no"){
		$gnDataPat .= '<tr><td class="'.$txt_clr.'" >&bull;&nbsp; No known patient medical condition</td></tr>';
	}

	$any_conditions_others_both = substr($genHealthQryRes[0]['any_conditions_others_both'],1,-1);
	$other_con_arr = preg_split('/,/',$any_conditions_others_both);
	$any_conditions_others_arr = preg_split("/$seprator/",$genHealthQryRes[0]['any_conditions_others']);

	if(trim($any_conditions_others_arr[0]) != '')
	{
		$other_desc = ' - '.$any_conditions_others_arr[0];
	}
	if(trim($other_con_arr[0]) == 1)
	{
		$gnDataPat .= '<tr><td>&bull;&nbsp; Others '.$other_desc.'</td></tr>';
	}


	// Collecting General Health Relative Problem Data
	$any_conditions_relative = substr($genHealthQryRes[0]['any_conditions_relative'],1,-1);
  $conditions_relative_arr = preg_split('/,/',$any_conditions_relative);

	if((count($conditions_relative_arr) > 0) && ($genHealthQryRes[0]["cbk_master_fam_con"] == "yes"))
	{
		foreach($gn_con_arr as $key => $val)
		{
			$field_name = ''; $txt_clr = '';
			$dia_data_val = ''; $relField_name = '';
			$sub_conditions_you_val = '';
			if($key == 1){
				$field_name = 'desc_high_bp';
        $relField_name = 'relDescHighBp';
     	}
      else if($key == 7){
       	$field_name = 'desc_arthrities';
        $relField_name = 'relDescArthritisProb';
        $sub_conditions_you_val = trim($sub_conditions_you_arr[2]);
        $sub_conditions_you_val = preg_replace('/,/',' - ',$sub_conditions_you_val);
				$sub_conditions_you_val = preg_replace('/7.1/',' RA ',$sub_conditions_you_val);
				$sub_conditions_you_val = preg_replace('/7.2/',' OA ',$sub_conditions_you_val);
    	}
      else if($key == 4){
				$field_name = 'desc_lung_problem';
				$relField_name = 'relDescLungProb';
			}
			else if($key == 5){
				$field_name = 'desc_stroke';
				$relField_name = 'relDescStrokeProb';
			}
			else if($key == 6){
				$field_name = 'desc_thyroid_problems';
				$relField_name = 'relDescThyroidProb';
			}
			else if($key == 8){
				$field_name = 'desc_ulcers';
				$relField_name = 'relDescUlcersProb';
			}
			else if($key == 14){
				$field_name = 'desc_cancer';
				$relField_name = 'relDescCancerProb';
			}
			else if($key == 2){
				$field_name = 'desc_heart_problem';
				$relField_name = 'relDescHeartProb';
			}
			else if($key == 13){
				$field_name = 'desc_LDL';
				$relField_name = 'relDescLDL';
			}
			else if($key == 3){
				$field_name = 'desc_u';
				$relField_name = 'desc_r';
				$txt_clr = 'text-danger';
				$diabetes_values_arr = preg_split("/$seprator/",$genHealthQryRes[0]['diabetes_values']);
				if(trim($diabetes_values_arr[2]) != ''){
					$dia_data_val = ' - '.$diabetes_values_arr[2];
				}
				$val = 'FH '.$val;
			}

			$rel_field_val = $genHealthQryRes[0][$relField_name];
			if(trim($rel_field_val) != ''){
				$rel_field_val = ' - ('.$rel_field_val.')';
			}

			//--- PROBLEMS DESCRIPTIONS ---
			$desc_val = preg_split("/$seprator/",$genHealthQryRes[0][$field_name]);
			$gn_desc_val = '';
			if(trim($desc_val[2]) != ''){
				$gn_desc_val = ' - '.trim($desc_val[2]);
			}

			if(in_array($key,$conditions_relative_arr) === true){
				$gnDataRel .= '<tr><td class="'.$txt_clr.'">&bull;&nbsp; '.$val . $dia_data_val . $sub_conditions_you_val . $rel_field_val . $gn_desc_val.'</td></tr>';
			}
		}
	}
	elseif($genHealthQryRes[0]["cbk_master_fam_con"] == "no")
	{
		$gnDataRel .= '<tr><td class="'.$txt_clr.'">&bull;&nbsp; No known family medical condition</td></tr>';
	}

	$gen_misc_data = '';
	$qry = "select cf.id as adminControlId, cf.control_lable as adminControlLable,
									pcf.patient_control_value as patientControlVal,
									pcf.patient_cbk_control_value as patientCbkControlVal
									FROM custom_fields cf
									LEFT JOIN patient_custom_field pcf on cf.id = pcf.admin_control_id and pcf.patient_id = '".$pid."'
									where cf.module = 'Med_Hx' and cf.sub_module ='Medical Hx -> General Health'
									and cf.status = '0' and (pcf.patient_control_value != '' || pcf.patient_cbk_control_value != '')
									order by cf.control_lable ";
	$result = imw_query($qry);

	if( imw_num_rows($result) > 0)
	{
		$gen_misc_data  = '<tr><td colspan="2" class="col-xs-12 pd0">';
		$gen_misc_data .= '<table class="table margin_0">';
		$gen_misc_data .= '<thead>';
		$gen_misc_data .= '<tr><th>&bull;&nbsp;<b>Miscellaneous</th></tr>';
		$gen_misc_data .= '</thead>';
		$gen_misc_data .= '<tbody>';

		$td = 1;
		while($row = imw_fetch_assoc($result))
		{
			if($row['patientControlVal'] != '' || $row['patientCbkControlVal'] != '')
				$gen_misc_data .= '<tr><td>&bull;&nbsp;'.$row['adminControlLable']." : ".$row['patientControlVal'].'</td></tr>';
		}

		$gen_misc_data .= '</tbody>';
		$gen_misc_data .= '</table>';
		$gen_misc_data .= '</td></tr>';
	}

	$gen_spec_data = '';
	$spec_ques_qry = 'select admMed.id,admMed.answer_type,admMed.ques,pt_spec_ques.pat_answer,pt_spec_ques.med_hx_tab
														from admn_medhx admMed
														JOIN admn_medhx_tab admMedHx on admMed.id = admMedHx.admn_medhx_question_id
														JOIN patient_specialty_question_answer pt_spec_ques on admMed.id = pt_spec_ques.question_id
														where pt_spec_ques.med_hx_tab = "General Health" AND pt_spec_ques.patient_id = '.$pid.' Order By admMed.ques';
	$get_spec_ques = imw_query($spec_ques_qry);
	if( imw_num_rows($get_spec_ques) > 0)
	{
		$gen_spec_data .= '<tr><td colspan="2" class="col-xs-12 pd0">';
		$gen_spec_data .= '<table class="table margin_0">';
		$gen_spec_data .= '<thead>';
		$gen_spec_data .= '<tr><th>&bull;&nbsp;<b>Specialty Question(s)</th></tr>';
		$gen_spec_data .= '</thead>';
		$gen_spec_data .= '<tbody>';

		while($row = imw_fetch_assoc($get_spec_ques))
		{
			if($row['answer_type'] == '0'){
				$gen_spec_data .= '<tr><td>&bull;&nbsp;'.$row['ques']." : ".$row['pat_answer'].'</td></tr>';
			}
			else
			{
				$get_multi_opt ="select group_concat(med_hx_question_answer_options.option_value) as opt_val,patient_specialty_question_options_answer.* from patient_specialty_question_answer join patient_specialty_question_options_answer on patient_specialty_question_answer.id=patient_specialty_question_options_answer.patient_specialty_question_answer_id
join med_hx_question_answer_options on med_hx_question_answer_options.id=patient_specialty_question_options_answer.option_id where patient_specialty_question_answer.question_id = '".$row['id']."' and patient_specialty_question_answer.patient_id = ".$pid." group by patient_specialty_question_answer.question_id";
				$multi_opt_data = imw_query($get_multi_opt);
				$counter = 0;
				while( $fetch_data = imw_fetch_assoc($multi_opt_data))
				{
					$gen_spec_data .= '<tr><td>&bull;&nbsp;'.$row['ques']." : ".$fetch_data['opt_val'].'</td></tr>';
				}
			}
		}

		$gen_spec_data .= '</tbody>';
		$gen_spec_data .= '</table>';
		$gen_spec_data .= '</td></tr>';

	}

	// Collecting Review System
	$review_data = ''; $review_no_known = '';
	if($genHealthQryRes[0]["cbk_master_ROS"] == 0)
	{
			$review_neg = '';
			$negChkBxArr = $negChkBx = array();
			$negChkBxArr = explode(',',$genHealthQryRes[0]["negChkBx"]);
			foreach($negChkBxArr as $key => $val){
				if($val != ""){
					$negChkBx[$val] = "checked";
				}
			}

			//ROS ARRAY
			$review_sys = $genHealthQryRes[0]["review_sys"];
			if(!empty($review_sys)){
				$ar_review_sys = json_decode($review_sys, true);
			}

			//---- GET ALLERGIC / IMMUNOLOGIC  DATA -----
			$review_aller = substr($genHealthQryRes[0]['review_aller'],1,-1);
			$review_aller_arr = array();
			if($review_aller != ''){
				$review_aller_arr = preg_split('/,/',$review_aller);
			}
			if($genHealthQryRes[0]['review_aller_others'] != ''){
				array_push($review_aller_arr,"3");
			}
			$review_aller_val_arr = array();
			$review_aller_val_arr[1] = 'Seasonal Allergies';
			$review_aller_val_arr[2] = 'Hay Fever';
			$review_aller_val_arr[3] = ucfirst($genHealthQryRes[0]['review_aller_others']);

			if(count($review_aller_arr) > 0){
				$review_data .= '<tr><td class="bg-danger">&nbsp;Allergic/Immunologic</td></tr>';

				for($h=0;$h<count($review_aller_arr);$h++)
				{
					$review_aller_val = $review_aller_arr[$h];
					$review_aller = $review_aller_val_arr[$review_aller_val];
					$review_data .= '<tr><td class="bg-danger">&nbsp;&bull;&nbsp;'.$review_aller.'</td></tr>';
				}
			}
			elseif($negChkBx[7] == "checked"){
				$review_neg .= '<tr><td class="bg-success">&bull;&nbsp;Negative &nbsp;&nbsp;Allergic/Immunologic</td></tr>';
			}

			//---- GET CARDIOVASCULAR DATA -----
			$review_card = substr($genHealthQryRes[0]['review_card'],1,-1);
			$review_card_arr = array();
			if($review_card != ''){
				$review_card_arr = preg_split('/,/',$review_card);
			}
			if($genHealthQryRes[0]['review_card_others'] != ''){
				array_push($review_card_arr,"8");
			}
			$review_card_val_arr = array();
			$review_card_val_arr[1] = 'Chest Pain';
			$review_card_val_arr[2] = 'Congestive Heart Failure';
			$review_card_val_arr[3] = 'Irregular Heart beat';
			$review_card_val_arr[4] = 'Shortness of Breath';
			$review_card_val_arr[5] = 'High Blood Pressure';
			$review_card_val_arr[6] = 'Low Blood Pressure';
			$review_card_val_arr[7] = 'Pacemaker/defibrillator';
			$review_card_val_arr[8] = ucfirst($genHealthQryRes[0]['review_card_others']);

			if(count($review_card_arr) > 0){
				$review_data .= '<tr><td class="bg-danger">&nbsp;Cardiovascular</td></tr>';

				for($h=0;$h<count($review_card_arr);$h++){
					$review_card_val = $review_card_arr[$h];
					$review_card_name = $review_card_val_arr[$review_card_val];
					$review_data .= '<tr><td class="bg-danger">&nbsp;&bull;&nbsp;'.$review_card_name.'</td></tr>';
				}
			}
			elseif($negChkBx[4] == "checked"){
				$review_neg .= '<tr><td class="bg-success">&bull;&nbsp;Negative&nbsp;&nbsp;Cardiovascular</td></tr>';
			}

			//---- GET CONSTITUTIONAL DATA -----
			$review_const = substr($genHealthQryRes[0]['review_const'],1,-1);
			$review_const_arr = array();
			if($review_const != ''){
				$review_const_arr = preg_split('/,/',$review_const);
			}
			if($genHealthQryRes[0]['review_const_others'] != ''){
				array_push($review_const_arr,"6");
			}
			$review_const_val_arr = array();
			$review_const_val_arr[1] = 'Fever';
			$review_const_val_arr[2] = 'Weight Loss';
			$review_const_val_arr[3] = 'Rash';
			$review_const_val_arr[4] = 'Skin Disease';
			$review_const_val_arr[5] = 'Fatigue';
			$review_const_val_arr[6] = ucfirst($genHealthQryRes[0]['review_const_others']);

			if(count($review_const_arr) > 0){
				$review_data .= '<tr><td class="bg-danger">&nbsp;Constitutional </td></tr>';

				for($c=0;$c<count($review_const_arr);$c++){
					$con_val = $review_const_arr[$c];
					$con_name = $review_const_val_arr[$con_val];

					$review_data .= '<tr><td class="bg-danger" >&nbsp;&bull;&nbsp;'.$con_name.'</td></tr>';
				}
			}
			elseif($negChkBx[1] == "checked"){
				$review_neg .= '<tr><td class="bg-success">&bull;&nbsp;Negative&nbsp;&nbsp;Constitutional </td></tr>';
			}

			//---- GET Ear, Nose, Mouth & Throat DATA -----
			$review_head = substr($genHealthQryRes[0]['review_head'],1,-1);
			$review_head_arr = array();
			if($review_head != ''){
				$review_head_arr = preg_split('/,/',$review_head);
			}
			if($genHealthQryRes[0]['review_head_others'] != ''){
				array_push($review_head_arr,"6");
			}
			$review_head_val_arr = array();
			$review_head_val_arr[1] = 'Sinus Infection';
			$review_head_val_arr[2] = 'Post Nasal Drips';
			$review_head_val_arr[3] = 'Runny Nose';
			$review_head_val_arr[4] = 'Dry Mouth';
			$review_head_val_arr[5] = 'Deafness';
			$review_head_val_arr[6] = ucfirst($genHealthQryRes[0]['review_head_others']);

			if(count($review_head_arr) > 0){
				$review_data .= '<tr><td class="bg-danger">&nbsp;Ear, Nose, Mouth & Throat</td></tr>';

				for($h=0;$h<count($review_head_arr);$h++){
					$review_head_val = $review_head_arr[$h];
					$review_head_name = $review_head_val_arr[$review_head_val];
					$review_data .= '<tr><td class="bg-danger">&nbsp;&bull;&nbsp;'.$review_head_name.'</td></tr>';
				}
			}
			elseif($negChkBx[2] == "checked"){
				$review_neg .= '<tr><td class="bg-success">&bull;&nbsp;Negative&nbsp;&nbsp;Ear, Nose, Mouth & Throat</div></td></tr>';
			}

			//---- GET Endocrine DATA -----
			$review_endocrine = substr($ar_review_sys['review_endocrine'],1,-1);
			$review_endocrine_arr = array();
			if($review_endocrine != ''){
				$review_endocrine_arr = preg_split('/,/',$review_endocrine);
			}
			if($ar_review_sys['review_endocrine_others'] != ''){
				array_push($review_endocrine_arr,"6");
			}

			$review_endocrine_val_arr = array();
			$review_endocrine_val_arr[1] = 'Mood Swings';
			$review_endocrine_val_arr[2] = 'Constipation';
			$review_endocrine_val_arr[3] = 'Polydipsia';
			$review_endocrine_val_arr[4] = 'Hypothyroidism';
			$review_endocrine_val_arr[5] = 'Hyperthyroidism';
			$review_endocrine_val_arr[6] = ucfirst($ar_review_sys['review_endocrine_others']);

			if(count($review_endocrine_arr) > 0){
				$review_data .= '<tr><td class="bg-danger">&nbsp;Endocrine</td></tr>';

				for($h=0;$h<count($review_endocrine_arr);$h++)
				{
					$review_endocrine_val = $review_endocrine_arr[$h];
					$review_endocrine = $review_endocrine_val_arr[$review_endocrine_val];
					$review_data .= '<tr><td class="bg-danger">&nbsp;&bull;&nbsp;'.$review_endocrine.'</td></tr>';
				}
			}
			elseif($negChkBx[13] == "checked"){
				$review_neg .= '<tr><td class="bg-success">&bull;&nbsp;Negative &nbsp;&nbsp;Endocrine</td></tr>';
			}

			//---- GET Eyes DATA -----
			$review_eyes = substr($ar_review_sys['review_eye'],1,-1);
			$review_eyes_arr = array();
			if($review_eyes != ''){
				$review_eyes_arr = preg_split('/,/',$review_eyes);
			}
			if($ar_review_sys['review_eye_others'] != ''){
				array_push($review_eyes_arr,"5");
			}

			$review_eyes_val_arr = array();
			$review_eyes_val_arr[1] = 'Vision loss';
			$review_eyes_val_arr[2] = 'Eye pain';
			$review_eyes_val_arr[3] = 'Double vision';
			$review_eyes_val_arr[4] = 'Headache';
			$review_eyes_val_arr[5] = ucfirst($ar_review_sys['review_eye_others']);

			if(count($review_eyes_arr) > 0){
				$review_data .= '<tr><td class="bg-danger">&nbsp;Eyes</td></tr>';

				for($h=0;$h<count($review_eyes_arr);$h++)
				{
					$review_eyes_val = $review_eyes_arr[$h];
					$review_eyes = $review_eyes_val_arr[$review_eyes_val];
					$review_data .= '<tr><td class="bg-danger">&nbsp;&bull;&nbsp;'.$review_eyes.'</td></tr>';
				}
			}
			elseif($negChkBx[14] == "checked"){
				$review_neg .= '<tr><td class="bg-success">&bull;&nbsp;Negative &nbsp;&nbsp;Eyes</td></tr>';
			}

			//---- GET GASTROITENSTINAL DATA -----
			$review_gastro = substr($genHealthQryRes[0]['review_gastro'],1,-1);
			$review_gastro_arr = array();
			if($review_gastro != ''){
				$review_gastro_arr = preg_split('/,/',$review_gastro);
			}
			if($genHealthQryRes[0]['review_gastro_others'] != ''){
				array_push($review_gastro_arr,"8");
			}
			$review_gastro_val_arr = array();
			$review_gastro_val_arr[1] = 'Vomiting';
			$review_gastro_val_arr[2] = 'Ulcers';
			$review_gastro_val_arr[3] = 'Diarrhea';
			$review_gastro_val_arr[4] = 'Bloody Stools';
			$review_gastro_val_arr[5] = 'Hepatitis';
			$review_gastro_val_arr[6] = 'Jaundice';
			$review_gastro_val_arr[7] = 'Constipation';
			$review_gastro_val_arr[8] = ucfirst($genHealthQryRes[0]['review_gastro_others']);
			if(count($review_gastro_arr) > 0){
				$review_data .= '<tr><td class="bg-danger">&nbsp;Gastrointenstinal</td></tr>';

				for($h=0;$h<count($review_gastro_arr);$h++){
					$review_gastro_val = $review_gastro_arr[$h];
					$review_gastro = $review_gastro_val_arr[$review_gastro_val];
					$review_data .= '<tr><td class="bg-danger">&nbsp;&bull;&nbsp;'.$review_gastro.'</td></tr>';
				}
			}
			elseif($negChkBx[5] == "checked"){
				$review_neg .= '<tr><td class="bg-success">&bull;&nbsp;Negative&nbsp;&nbsp;Gastrointestinal</td></tr>';
			}

			//---- GET GASTROITENSTINAL DATA -----
			$review_genit = substr($genHealthQryRes[0]['review_genit'],1,-1);
			$review_genit_arr = array();
			if($review_genit != ''){
				$review_genit_arr = preg_split('/,/',$review_genit);
			}
			if($genHealthQryRes[0]['review_genit_others'] != ''){
				array_push($review_genit_arr,"5");
			}
			$review_genit_val_arr = array();
			$review_genit_val_arr[1] = 'Genital Ulcers';
			$review_genit_val_arr[2] = 'Discharge';
			$review_genit_val_arr[3] = 'Kidney Stones';
			$review_genit_val_arr[4] = 'Blood in Urine';
			$review_genit_val_arr[5] = ucfirst($genHealthQryRes[0]['review_genit_others']);

			if(count($review_genit_arr) > 0){
				$review_data .= '<tr><td class="bg-danger">&nbsp;Genitourinary</td></tr>';
				for($h=0;$h<count($review_genit_arr);$h++){
					$review_genit_val = $review_genit_arr[$h];
					$review_genit = $review_genit_val_arr[$review_genit_val];
					$review_data .= '<tr><td class="bg-danger">&nbsp;&bull;&nbsp;'.$review_genit.'</td></tr>';
				}
			}
			elseif($negChkBx[6] == "checked"){
				$review_neg .= '<tr><td class="bg-success">&bull;&nbsp;Negative&nbsp;&nbsp;Genitourinary</td></tr>';
			}

			//---- GET Hemotologic/Lymphatic  DATA -----
			$review_blood_lymph = substr($ar_review_sys['review_blood_lymph'],1,-1);
			$review_blood_lymph_arr = array();
			if($review_blood_lymph != ''){
				$review_blood_lymph_arr = preg_split('/,/',$review_blood_lymph);
			}
			if($ar_review_sys['review_blood_lymph_others'] != ''){
				array_push($review_blood_lymph_arr,"6");
			}

			$review_blood_lymph_val_arr = array();
			$review_blood_lymph_val_arr[1] = 'Anemia';
			$review_blood_lymph_val_arr[2] = 'Blood Transfusions';
			$review_blood_lymph_val_arr[3] = 'Excessive Bleeding';
			$review_blood_lymph_val_arr[4] = 'Purpura';
			$review_blood_lymph_val_arr[5] = 'Infection';
			$review_blood_lymph_val_arr[6] = ucfirst($ar_review_sys['review_blood_lymph_others']);

			if(count($review_blood_lymph_arr) > 0){
				$review_data .= '<tr><td class="bg-danger">&nbsp;Hemotologic/Lymphatic</td></tr>';

				for($h=0;$h<count($review_blood_lymph_arr);$h++)
				{
					$review_blood_lymph_val = $review_blood_lymph_arr[$h];
					$review_blood_lymph = $review_blood_lymph_val_arr[$review_blood_lymph_val];
					$review_data .= '<tr><td class="bg-danger">&nbsp;&bull;&nbsp;'.$review_blood_lymph.'</td></tr>';
				}
			}
			elseif($negChkBx[11] == "checked"){
				$review_neg .= '<tr><td class="bg-success">&bull;&nbsp;Negative &nbsp;&nbsp;Hemotologic/Lymphatic</td></tr>';
			}

			//---- GET Integumentary  DATA -----
			$review_intgmntr = substr($ar_review_sys['review_intgmntr'],1,-1);
			$review_intgmntr_arr = array();
			if($review_intgmntr != ''){
				$review_intgmntr_arr = preg_split('/,/',$review_intgmntr);
			}
			if($ar_review_sys['review_intgmntr_others'] != ''){
				array_push($review_intgmntr_arr,"6");
			}

			$review_intgmntr_val_arr = array();
			$review_intgmntr_val_arr[1] = 'Rashes';
			$review_intgmntr_val_arr[2] = 'Wounds';
			$review_intgmntr_val_arr[3] = 'Breast Lumps';
			$review_intgmntr_val_arr[4] = 'Eczema';
			$review_intgmntr_val_arr[5] = 'Dermatitis';
			$review_intgmntr_val_arr[6] = ucfirst($ar_review_sys['review_intgmntr_others']);

			if(count($review_intgmntr_arr) > 0){
				$review_data .= '<tr><td class="bg-danger">&nbsp;Integumentary</td></tr>';

				for($h=0;$h<count($review_intgmntr_arr);$h++)
				{
					$review_intgmntr_val = $review_intgmntr_arr[$h];
					$review_intgmntr = $review_intgmntr_val_arr[$review_intgmntr_val];
					$review_data .= '<tr><td class="bg-danger">&nbsp;&bull;&nbsp;'.$review_intgmntr.'</td></tr>';
				}
			}
			elseif($negChkBx[9] == "checked"){
				$review_neg .= '<tr><td class="bg-success">&bull;&nbsp;Negative &nbsp;&nbsp;Integumentary</td></tr>';
			}

			//---- GET Musculoskeletal DATA -----
			$review_musculoskeletal = substr($ar_review_sys['review_musculoskeletal'],1,-1);
			$review_musculoskeletal_arr = array();
			if($review_musculoskeletal != ''){
				$review_musculoskeletal_arr = preg_split('/,/',$review_musculoskeletal);
			}
			if($ar_review_sys['review_musculoskeletal_others'] != ''){
				array_push($review_musculoskeletal_arr,"6");
			}

			$review_musculoskeletal_val_arr = array();
			$review_musculoskeletal_val_arr[1] = 'Pain';
			$review_musculoskeletal_val_arr[2] = 'Joint Ache';
			$review_musculoskeletal_val_arr[3] = 'Stiffness';
			$review_musculoskeletal_val_arr[4] = 'Swelling';
			$review_musculoskeletal_val_arr[5] = 'Paralysis Fever';
			$review_musculoskeletal_val_arr[6] = ucfirst($ar_review_sys['review_musculoskeletal_others']);

			if(count($review_musculoskeletal_arr) > 0){
				$review_data .= '<tr><td class="bg-danger">&nbsp;Musculoskeletal</td></tr>';

				for($h=0;$h<count($review_musculoskeletal_arr);$h++)
				{
					$review_musculoskeletal_val = $review_musculoskeletal_arr[$h];
					$review_musculoskeletal = $review_musculoskeletal_val_arr[$review_musculoskeletal_val];
					$review_data .= '<tr><td class="bg-danger">&nbsp;&bull;&nbsp;'.$review_musculoskeletal.'</td></tr>';
				}
			}
			elseif($negChkBx[12] == "checked"){
				$review_neg .= '<tr><td class="bg-success">&bull;&nbsp;Negative &nbsp;&nbsp;Musculoskeletal</td></tr>';
			}

			//---- GET NEUROLOGICAL DATA -----
			$review_neuro = substr($genHealthQryRes[0]['review_neuro'],1,-1);
			$review_neuro_arr = array();
			if($review_neuro != ''){
				$review_neuro_arr = preg_split('/,/',$review_neuro);
			}
			if($genHealthQryRes[0]['review_neuro_others'] != ''){
				array_push($review_neuro_arr,"13");
			}
			$review_neuro_val_arr = array();
			$review_neuro_val_arr[1] = 'Headache';
			$review_neuro_val_arr[2] = 'Migraines';
			$review_neuro_val_arr[3] = 'Paralysis Fever';
			$review_neuro_val_arr[4] = 'Joint Ache';
			$review_neuro_val_arr[5] = 'Seizures';
			$review_neuro_val_arr[6] = 'Numbness';
			$review_neuro_val_arr[7] = 'Faints';
			$review_neuro_val_arr[8] = 'Stroke';
			$review_neuro_val_arr[9] = 'Multiple Sclerosis';
			$review_neuro_val_arr[10] = 'Alzheimer\'s Disease';
			$review_neuro_val_arr[11] = 'Parkinson\'s Disease';
			$review_neuro_val_arr[12] = 'Dementia';
			$review_neuro_val_arr[13] = ucfirst($genHealthQryRes[0]['review_neuro_others']);

			if(count($review_neuro_arr) > 0){
				$review_data .= '<tr><td class="bg-danger">&nbsp;Neurological</td></tr>';

				for($h=0;$h<count($review_neuro_arr);$h++){
					$review_neuro_val = $review_neuro_arr[$h];
					$review_neuro = $review_neuro_val_arr[$review_neuro_val];
					$review_data .= '<tr><td class="bg-danger">&nbsp;&bull;&nbsp;'.$review_neuro.'</td></tr>';
				}
			}
			elseif($negChkBx[8] == "checked"){
				$review_neg .= '<tr><td class="bg-success">&bull;&nbsp;Negative&nbsp;&nbsp;Neurological</td></tr>';
			}

			//---- GET Psychiatry  DATA -----
			$review_psychiatry = substr($ar_review_sys['review_psychiatry'],1,-1);
			$review_psychiatry_arr = array();
			if($review_psychiatry != ''){
				$review_psychiatry_arr = preg_split('/,/',$review_psychiatry);
			}
			if($ar_review_sys['review_psychiatry_others'] != ''){
				array_push($review_psychiatry_arr,"9");
			}

			$review_psychiatry_val_arr = array();
			$review_psychiatry_val_arr[1] = 'Depression';
			$review_psychiatry_val_arr[2] = 'Anxiety';
			$review_psychiatry_val_arr[3] = 'Paranoia';
			$review_psychiatry_val_arr[4] = 'Sleep Patterns';
			$review_psychiatry_val_arr[5] = 'Mental and/or emotional factors';
			$review_psychiatry_val_arr[6] = 'Alzheimer\'s Disease';
			$review_psychiatry_val_arr[7] = 'Parkinson\'s disease';
			$review_psychiatry_val_arr[8] = 'Memory Loss';
			$review_psychiatry_val_arr[9] = ucfirst($ar_review_sys['review_psychiatry_others']);

			if(count($review_psychiatry_arr) > 0){
				$review_data .= '<tr><td class="bg-danger">&nbsp;Psychiatry</td></tr>';

				for($h=0;$h<count($review_psychiatry_arr);$h++)
				{
					$review_psychiatry_val = $review_psychiatry_arr[$h];
					$review_psychiatry = $review_psychiatry_val_arr[$review_psychiatry_val];
					$review_data .= '<tr><td class="bg-danger">&nbsp;&bull;&nbsp;'.$review_psychiatry.'</td></tr>';
				}
			}
			elseif($negChkBx[10] == "checked"){
				$review_neg .= '<tr><td class="bg-success">&bull;&nbsp;Negative &nbsp;&nbsp;Psychiatry</td></tr>';
			}

			//---- GET RESPIRATORY DATA -----
			$review_resp = substr($genHealthQryRes[0]['review_resp'],1,-1);
			$review_resp_arr = array();
			if($review_resp != ''){
				$review_resp_arr = preg_split('/,/',$review_resp);
			}
			if($genHealthQryRes[0]['review_resp_others'] != ''){
				array_push($review_resp_arr,"8");
			}
			$review_resp_val_arr = array();
			$review_resp_val_arr[1] = 'Cough';
			$review_resp_val_arr[2] = 'Bronchitis';
			$review_resp_val_arr[3] = 'Shortness of Breath';
			$review_resp_val_arr[4] = 'Asthma';
			$review_resp_val_arr[5] = 'Emphysema';
			$review_resp_val_arr[6] = 'COPD';
			$review_resp_val_arr[7] = 'TB';
			$review_resp_val_arr[8] = ucfirst($genHealthQryRes[0]['review_resp_others']);

			if(count($review_resp_arr) > 0){
				$review_data .= '<tr><td class="bg-danger">&nbsp;Respiratory</td></tr>';

				for($h=0;$h<count($review_resp_arr);$h++){
					$review_resp_val = $review_resp_arr[$h];
					$review_resp_name = $review_resp_val_arr[$review_resp_val];
					$review_data .= '<tr><td class="bg-danger">&nbsp;&bull;&nbsp;'.$review_resp_name.'</td></tr>';
				}
			}
			elseif($negChkBx[3] == "checked"){
				$review_neg .= '<tr><td class="bg-success">&bull;&nbsp;Negative&nbsp;&nbsp;Respiratory</td></tr>';
			}


			if(!empty($review_data) && !empty($review_neg)){
				$review_data .= '<tr><td class="bg-success">&bull;&nbsp;All recorded systems are negative except as noted above.</td></tr>';
			}else if(!empty($review_neg)){
				$review_data .= $review_neg;
			}

			//END --
	}
	elseif($genHealthQryRes[0]["cbk_master_ROS"] == 1)
	{
		$review_no_known .= '<tr><td class="bg bg-info text-center"><b>&nbsp;&bull;&nbsp;No known medical condition</b></td></tr>';
	}


	//--- Collecting Blood Sugar Data
	$blood_sugar_data = '';
	if($flgArc==1){
		$bloodQryRes = $arrPtBS;
	}
	else
	{
		$query = "select *,time_of_day, description,sugar_value,
										 Date_Format(creation_date,'".get_sql_date_format()."') as creationDate_2 ,
										 time_of_day_other, is_fasting
										 from patient_blood_sugar where patient_id = '".$pid."'
										 ORDER BY creationDate_2 DESC,time_of_day_sequence";
		$sql = imw_query($query);
		$bloodQryRes = array();
		while( $row = imw_fetch_assoc($sql) )
		{
			array_push($bloodQryRes,$row);
		}
	}
	$bsClass = '';
	if( count($bloodQryRes) > 0 )
	{
		$bsClass = 'in';
		$blood_sugar_data	.= '<table id="blood_sugar_tbl" class="table table-bordered table-striped table-hover margin_0">';
		$blood_sugar_data	.= '<thead class="grythead">';
		$blood_sugar_data	.= '<tr >';
		$blood_sugar_data	.= '<th class="col-xs-1">Date</td>';
		$blood_sugar_data	.= '<th class="col-xs-2">Blood&nbsp;Sugar&nbsp;(mg/dl)</td>';
		$blood_sugar_data	.= '<th class="col-xs-1">Fasting</td>';
		$blood_sugar_data	.= '<th class="col-xs-2">Time&nbsp;of&nbsp;Day</td>';
		$blood_sugar_data	.= '<th class="col-xs-3">HbA1c</td>';
		$blood_sugar_data	.= '<th class="col-xs-3">Description</td>';
		$blood_sugar_data	.= '</tr>';
		$blood_sugar_data	.= '</thead>';
		$blood_sugar_data	.= '<tbody>';

		for($b = 0; $b < count($bloodQryRes); $b++)
		{
			$sugar_value = $bloodQryRes[$b]['sugar_value'].' mg/dl';
			$hba1c 	=  $bloodQryRes[$b]['hba1c'];
			$description = $bloodQryRes[$b]['description'] != '' ? $bloodQryRes[$b]['description'] : "" ;
			$is_fasting = ($bloodQryRes[$b]['is_fasting']) ? 'Yes' : '';

			if(isset($bloodQryRes[$b]['creationDate_2'])){
				$creationDate = $bloodQryRes[$b]['creationDate_2'];
			}else{
				$creationDate = $bloodQryRes[$b]['creationDate'];
			}

			$time_of_day= $bloodQryRes[$b]['time_of_day'];
			if($time_of_day=="Other"&&!empty($bloodQryRes[$b]['time_of_day_other'])){
				$time_of_day = $bloodQryRes[$b]['time_of_day_other'];
			}

			$blood_sugar_data .= '<tr>';
			$blood_sugar_data .= '<td class="nowrap">'.$creationDate.'</td>';
			$blood_sugar_data .= '<td>'.$sugar_value.'</td>';
			$blood_sugar_data .= '<td>'.$is_fasting.'</td>';
			$blood_sugar_data .= '<td>'.$time_of_day.'</td>';
			$blood_sugar_data .= '<td>'.$hba1c.'</td>';
			$blood_sugar_data .= '<td>'.$description.'</td>';
			$blood_sugar_data .= '</tr>';

		}

		$blood_sugar_data	.= '</tbody>';
		$blood_sugar_data	.= '</table>';
	}
	else
	{
		$blood_sugar_data	.= '<span class="col-xs-12 alert alert-info text-center margin_0">No record found</span>';
	}

	// Collecting Cholesterol Data
	if($flgArc==1){
		$cholQryRes =$arrPtCh;
	}
	else
	{
		$query = "select cholesterol_total,cholesterol_triglycerides,
											cholesterol_LDL,cholesterol_HDL,description,
											date_format(creation_date,'".get_sql_date_format()."') as creation_date_2
											from patient_cholesterol where patient_id = '".$pid."'
											ORDER BY creation_date_2 DESC";
		$sql = imw_query($query);
		$cnt = imw_num_rows($sql);
		$cholQryRes = array();
		while( $row = imw_fetch_assoc($sql) )
		{
			array_push($cholQryRes,$row);
		}
	}

	$cholesterol_data = "";
	$chClass = '';
	if( count($cholQryRes) > 0 )
	{
		$chClass = 'in';
		$cholesterol_data	.= '<table id="cholesterol_data_tbl" class="table table-bordered table-striped table-hover margin_0">';
		$cholesterol_data	.= '<thead class="grythead">';
		$cholesterol_data	.= '<tr >';
		$cholesterol_data	.= '<th class="col-xs-2">Date</td>';
		$cholesterol_data	.= '<th class="col-xs-2">Cholesterol</td>';
		$cholesterol_data	.= '<th class="col-xs-1">Trig.</td>';
		$cholesterol_data	.= '<th class="col-xs-1">LDL</td>';
		$cholesterol_data	.= '<th class="col-xs-1">HDL</td>';
		$cholesterol_data	.= '<th class="col-xs-5">Description</td>';
		$cholesterol_data	.= '</tr>';
		$cholesterol_data	.= '</thead>';
		$cholesterol_data	.= '<tbody>';

		for($b=0;$b<count($cholQryRes);$b++)
		{
			if(!empty($cholQryRes[$b]['creation_date_2']))
				$creationDate = $cholQryRes[$b]['creation_date_2'];
			else
				$creationDate = get_date_format($cholQryRes[$b]['creation_date']);
			$description = $cholQryRes[$b]['description'];
			$cholesterol = $cholQryRes[$b]['cholesterol_total'];
			$trig = $cholQryRes[$b]['cholesterol_triglycerides'];
			$ldl = $cholQryRes[$b]['cholesterol_LDL'];
			$hdl = $cholQryRes[$b]['cholesterol_HDL'];

			$cholesterol_data .= '<tr>';
			$cholesterol_data .= '<td>'.$creationDate.'</td>';
			$cholesterol_data .= '<td>'.$$cholesterol.'</td>';
			$cholesterol_data .= '<td>'.$trig.'</td>';
			$cholesterol_data .= '<td>'.$ldl.'</td>';
			$cholesterol_data .= '<td>'.$hdl.'</td>';
			$cholesterol_data .= '<td>'.$description.'</td>';
			$cholesterol_data .= '</tr>';

		}
		$cholesterol_data	.= '</tbody>';
		$cholesterol_data	.= '</table>';
	}
	else
	{
		$cholesterol_data	.= '<span class="col-xs-12 alert alert-info text-center margin_0">No record found</span>';
	}

	// Collecting Allergies Data
	if($flgArc==1){
		$allergyQryRes = $arrLists[7];
	}
	else
	{
		$query = "select ag_occular_drug,title,reactions,comments,severity,reaction_code,allergy_status,
											date_format(begdate, '".get_sql_date_format()."') as begdate_2
											from lists where pid = '$pid' and type IN (7) and allergy_status = 'Active'";
		$sql = imw_query($query);
		$allergyQryRes = array();
		while( $row = imw_fetch_assoc($sql) )
		{
			array_push($allergyQryRes,$row);
		}
	}

	$no_allergies = $no_allergies_reviewed = '';
	if( !$allergyQryRes )
	{
		if($flgArc == 1)
		{
			if($arrCNMHx["Allergy"]["no_value"]!=''){
				$strNoAllergy = "checked";
			}
		}
		else
		{
			$strNoAllergy = commonNoMedicalHistoryAddEdit($moduleName="Allergy",$moduleValue="",$mod="get");
		}
		if($strNoAllergy == "checked"){
			$no_allergies = '<b class="col-xs-12 text-center alert alert-info margin_0">NKDA</b>';
		}else{
			$no_allergies_reviewed = '<b class="col-xs-12 text-center alert alert-info margin_0">Not Reviewed</b>';
		}
	}

	// Collecting Medication Data ;
	$medQryResGen = array();
	if($flgArc==1){
		$medQryResGen = $arrLists[1];
	}
	else{
		$query = "select title,destination,sig,med_comments, med_route, allergy_status,
								date_format(begdate, '".get_sql_date_format()."') as begdate_2
								from lists where pid = '".$pid."'
								and type IN (1) and allergy_status IN ('Active','Order')";
		$sql = imw_query($query);
		while( $row = imw_fetch_assoc($sql))
		{
			array_push($medQryResGen,$row);
		}
	}

	$no_medication = $no_med_reviewed = '';
	if( !$medQryResGen )
	{
		if($flgArc == 1)
		{
			if($arrCNMHx["Medication"]["no_value"]!=''){
				$strNoMedication = "checked";
			}
		}
		else
		{
			$strNoMedication = commonNoMedicalHistoryAddEdit($moduleName="Medication",$moduleValue="",$mod="get");
		}
		if($strNoMedication == "checked"){
			$no_medication = '<b class="col-xs-12 text-center alert alert-info margin_0">No Known Medications</b>';
		}
		else
		{
			$query = "select title,destination,sig,med_comments,allergy_status,sites,
											date_format(begdate, '".get_sql_date_format()."') as begdate_2
											from lists where pid = '".$pid."' and type IN (4) and allergy_status IN ('Active','Order')";
			$sql = imw_query($query);
			$cnt = imw_num_rows($sql);
			if( $cnt <= 0){
				$no_med_reviewed = '<b class="col-xs-12 text-center alert alert-info margin_0">Not Reviewed</b>';
			}
		}

	}

	// Collecting Sx/Procedure Data
	$sx_procedure = array();
	if($flgArc==1){
		$sx_procedure = $arrLists[5];
	}
	else
	{
		$query = "select title,referredby,comments,
										if((DAY(begdate)='00' OR DAY(begdate)='0')&& (MONTH(begdate)='00' OR
										MONTH(begdate)='0'),date_format(begdate, '%Y'),
										if(MONTH(begdate)='00' OR MONTH(begdate)='0',date_format(begdate,'%Y'),
										if(DAY(begdate)='00' or DAY(begdate)='0',date_format(begdate,'%m-%Y'),
										date_format(begdate,'".get_sql_date_format()."') )))as begdate_2
										from lists where pid = '".$pid."' and type IN (5) and allergy_status != 'Deleted'
										order by begdate desc";
		$sql = imw_query($query);
		while ( $row = imw_fetch_assoc($sql) )
		{
			array_push($sx_procedure, $row);
		}
	}

	$no_sx = $no_sx_reviewed = '';
	if( !$sx_procedure)
	{
		if($flgArc==1){
			if($arrCNMHx["Surgery"]["no_value"]!=''){
				$strNoMedication = "checked";
			}
		}
		else{
			$strNoSurgery = commonNoMedicalHistoryAddEdit($moduleName="Surgery",$moduleValue="",$mod="get");
		}

		if($strNoSurgery == "checked"){
			$no_sx = '<b class="col-xs-12 text-center alert alert-info margin_0">No Known Surgeries</b>';
		}
		else
		{
			$query = "select title,sites,referredby,allergy_status,
											if((DAY(begdate)='00' OR DAY(begdate)='0')&& (MONTH(begdate)='00' OR
											MONTH(begdate)='0'),date_format(begdate, '%Y'),
											if(MONTH(begdate)='00' OR MONTH(begdate)='0',date_format(begdate,'%Y'),
											if(DAY(begdate)='00' or DAY(begdate)='0',date_format(begdate,'%m-%Y'),
											date_format(begdate,'".get_sql_date_format()."') )))as begdate_2
											from lists where pid = '".$pid."' and type IN (6) and allergy_status != 'Deleted'
											order by begdate desc";
			$sql = imw_query($query);
			$cnt = imw_num_rows($sql);
			if( $cnt <= 0){
				$no_sx_reviewed = '<b class="col-xs-12 text-center alert alert-info margin_0">Not Reviewed</b>';
			}
		}

	}

	// Collecting Immunization Data
	$immQryRes = array();
	if($flgArc==1){
			$immQryRes = $arrImm;
	}
	else
	{
		$query = "select immzn_type,immzn_dose,adverse_reaction,immunization_id,status,
											date_format(administered_date,'".get_sql_date_format()."') as administered_date_2
											from immunizations where patient_id = '".$pid."' and status = 'Given'";
		$sql = imw_query($query);
		while( $row = imw_fetch_assoc($sql) )
		{
			array_push($immQryRes,$row);
		}
 	}


	$no_imm = '';
	if( !$immQryRes)
	{
		if( $flgArc == 1 && $arrCNMHx["Immunizations"]["no_value"] <> '' ){
			$strNoImm = "checked";
		}
		else{
			$strNoImm = commonNoMedicalHistoryAddEdit($moduleName="Immunizations",$moduleValue="",$mod="get");
		}

		if($strNoImm == "checked"){
			$no_imm = '<b class="col-xs-12 text-center alert alert-info margin_0">Not Immunizations</b>';
		}
	}

	// Collecting Problem List Data
	$probQryRes = array();
	if($flgArc==1){
		$probQryRes = $arrPtProbList;
	}
	else
	{
		$query = "select status,problem_name, Date_Format(onset_date, '".get_sql_date_format('','y')."') as onset_date_2
											from pt_problem_list where status in ('Active', 'External')
											and pt_id = '".$pid."' order By onset_date_2 desc";
		$sql = imw_query($query);
		while( $row = imw_fetch_assoc($sql) )
		{
			array_push($probQryRes,$row);
		}
	}

	$hideBtn = 'hideMedList(\'PMH\');';
	if(strpos($_SERVER['HTTP_REFERER'],'MU_checklist')>0){
		$hideBtn = "$('#genHealthDiv_wv').modal('hide');";
	}

	$html  = '';

	$html .= '<div class="modal-dialog modal-lg" style="width:90%;">';
	$html .= '<div class="modal-content">';

	$html .= '<div class="modal-header bg-primary">';
	$html .= '<button type="button" class="close" onClick="'.$hideBtn.'">×</button>';
	$html .= '<h4 class="modal-title" id="modal_title">Patient Medical History</h4>';
	$html .= '</div>';

	$html	.= '<div class="modal-body mb10" style="min-height:400px; max-height:400px; overflow:hidden; overflow-y:auto;">';
	$html .= '<div id="socialSelectContainer" stye="position:absolute;"></div>';
	$html .= '<div class="panel-group" id="accordion">';

	// Panel Start - L. Exm
	$chargeMsg = trim(getOwnerShipChangeMsg($pid, $cur_frm_id));
	$chargeMsgClass = ($chargeMsg) ? 'in' : '';
	if( !$chargeMsg )
	{
		$chargeMsg = '<tr><td class="text-center bg-info">No record found</td></tr>';
	}
	$html .= '<div class="panel panel-primary">';
	$html .= '<div class="panel-heading pointer">';
	$html .= '<h4 class="panel-title" data-toggle="collapse"  href="#tdPtLExam">';
	$html .= 'L. Exm. '.getPtLExamInfo($pid,$finalize_id);
	$html .= '</h4>';
	$html .= '</div>';
	$html .= '<div id="tdPtLExam" class="panel-collapse collapse '.$chargeMsgClass.' ">';
	$html .= '<div class="panel-body pd0">';
	$html .= '<table class="table table-bordered margin_0">'.$chargeMsg.'</table>';
	$html .= '</div>';
	$html .= '</div>';
	$html .= '</div>';
	// Panel End


	// Panel Start - Allergies
	$agRecords =  (is_array($allergyQryRes) && count($allergyQryRes) > 0) ? true : false;
	$agClass = ($agRecords) ? 'in' : '';
	$html .= '<div class="panel panel-primary">';
	$html .= '<div class="panel-heading pointer">';
	$html .= '<h4 class="panel-title" data-toggle="collapse"  href="#allergiessec">';
	$html .= 'Allergies';
	$html .= '<input type="button" class="btn btn-success pull-right" value="Allergies" style="margin-top:-8px;" onclick="openMedHX(\'allergies\', \'800\');" />';
	$html .= '</h4>';
	$html .= '</div>';
	$html .= '<div id="allergiessec" class="panel-collapse collapse '.$agClass.'">';
	$html .= '<div class="panel-body pd0">';

	if( $agRecords )
	{
		$html	.= '<table id="ocular_medication_tbl" class="table table-bordered table-striped table-hover margin_0">';
		$html	.= '<thead class="grythead">';
		$html	.= '<tr >';
		$html	.= '<th class="col-xs-2">Drug</td>';
		$html	.= '<th class="col-xs-2">Name</td>';
		$html	.= '<th class="col-xs-4">Reactions / Comments</td>';
		$html	.= '<th class="col-xs-2">Severity</td>';
		$html	.= '<th class="col-xs-2">Begin Date</td>';
		$html	.= '</tr>';
		$html	.= '</thead>';
		$html	.= '<tbody>';


		foreach($allergyQryRes as $tmp_allergy)
		{
			if($tmp_allergy['allergy_status']!='Active'){continue;}

			$ag_occular_drug = trim($tmp_allergy['ag_occular_drug']);
			$drug_txt = '';
			if($ag_occular_drug == 'fdbATDrugName'){
				$drug_txt = 'Drug';
			}
			if($ag_occular_drug == 'fdbATIngredient'){
				$drug_txt = 'Ingredient';
			}
			if($ag_occular_drug == 'fdbATAllergenGroup'){
				$drug_txt = 'Allergen';
			}
			$ag_title = stripslashes(trim($tmp_allergy['title']));
			$ag_reactions = trim($tmp_allergy['reactions']);
			$ag_comments = $ag_reactions.' '.$tmp_allergy['comments'];
			$ag_comments = trim($ag_comments) . ($tmp_allergy['reaction_code'] ? ' - '.$tmp_allergy['reaction_code'] : '');
			$ag_severity = ucwords($tmp_allergy['severity']);

			if(isset($tmp_allergy['begdate_2'])){
				$bg_date = $tmp_allergy['begdate_2'];
			}else{
				$bg_date = get_date_format($tmp_allergy['begdate']);
			}

			if(preg_replace('/[^0-9]/','',$bg_date) == '00000000'){
			    $bg_date = '';
			}

			$html .= '<tr>';
			$html .= '<td>'.$drug_txt.'</td>';
			$html .= '<td>'.$ag_title.'</td>';
			$html .= '<td>'.$ag_comments.'</td>';
			$html .= '<td>'.$ag_severity.'</td>';
			$html .= '<td>'.$bg_date.'</td>';
			$html .= '</tr>';
		}
		$html .= '</tbody>';
		$html .= '</table>';
	}
	else
	{
		if($no_allergies)
		{
			$html	.= $no_allergies;
		}
		else
		{
			$html .= $no_allergies_reviewed;
		}
	}

	$html .= '</div>';
	$html .= '</div>';
	$html .= '</div>';
	// Panel End


	// Panel Start - Ocular Surgeries
	$osRecords = (is_array($ocular_sx) && count($ocular_sx) > 0) ? true : false;
	$osClass = ($osRecords) ? 'in' : '';
	$html .= '<div class="panel panel-primary">';
	$html .= '<div class="panel-heading pointer">';
	$html .= '<h4 class="panel-title" data-toggle="collapse"  href="#ocular_surgeries">';
	$html .= 'Ocular Surgeries';
	$html .= '<input type="button" class="btn btn-success pull-right" value="Ocular Surgeries" style="margin-top:-8px;" onclick="openMedHX(\'sxPro\', \'800\');" />';
	$html .= '</h4>';
	$html .= '</div>';
	$html .= '<div id="ocular_surgeries" class="panel-collapse collapse '.$osClass.'">';
	$html .= '<div class="panel-body pd0">';

	if( $osRecords )
	{
		$html	.= '<table id="ocular_surgeries_tbl" class="table table-bordered table-striped table-hover margin_0">';
		$html	.= '<thead class="grythead">';
		$html	.= '<tr >';
		$html	.= '<th class="col-xs-3">Name</td>';
		$html	.= '<th class="col-xs-1">Site</td>';
		$html	.= '<th class="col-xs-2">Begin Date</td>';
		$html	.= '<th class="col-xs-2">Physician</td>';
		$html	.= '<th class="col-xs-4">Comments</td>';
		$html	.= '</tr>';
		$html	.= '</thead>';
		$html	.= '<tbody>';

		foreach($ocular_sx as $tmp_sx)
		{
			if($tmp_sx['allergy_status']=='Deleted'){continue;}
			$sx_name = ucfirst($tmp_sx['title']);
			$referredby = $tmp_sx['referredby'];
			if(isset($tmp_sx['begdate_2'])){
				$sx_begdate = $tmp_sx['begdate_2'];
			}else{
				$sx_begdate = get_date_format($tmp_sx['begdate']);
			}
			if($tmp_sx['sites'] == 3){
				$site = "OU";
			}elseif($tmp_sx['sites'] == 2){
				$site = "OD";
			}elseif($tmp_sx['sites'] == 1){
				$site = "OS";
			}elseif($tmp_sx['sites'] == 4){
				$site = "PO";
			}else $site = '';
			if(preg_replace('/[^0-9]/','',$sx_begdate) == '00000000'){
				$sx_begdate = '';
			}
			$comments = $tmp_sx['comments'];

			$html .= '<tr>';
			$html .= '<td>'.$sx_name.'</td>';
			$html .= '<td>'.$site.'</td>';
			$html .= '<td>'.$sx_begdate.'</td>';
			$html .= '<td>'.$referredby.'</td>';
			$html .= '<td>'.$comments.'</td>';
			$html .= '</tr>';
		}

		$html .= '</tbody>';
		$html .= '</table>';
	}
	else
	{
		if($no_ocular_sx)
		{
			$html	.= $no_ocular_sx;
		}
		else
		{
			$html .= $no_ocular_sx_reviewed;
		}
	}


	$html .= '</div>';
	$html .= '</div>';
	$html .= '</div>';
	// Panel End



	// Panel Start - Sx/Procedure
	$sxRecords =  (is_array($sx_procedure) && count($sx_procedure) > 0) ? true : false;
	$sxClass = ($sxRecords) ? 'in' : '';
	$html .= '<div class="panel panel-primary">';
	$html .= '<div class="panel-heading pointer">';
	$html .= '<h4 class="panel-title" data-toggle="collapse"  href="#sx_procedure">';
	$html .= 'Sx/Procedures';
	$html .= '<input type="button" class="btn btn-success pull-right" value="Sx/Procedures" style="margin-top:-8px;" onclick="openMedHX(\'sxPro\', \'800\');" />';
	$html .= '</h4>';
	$html .= '</div>';
	$html .= '<div id="sx_procedure" class="panel-collapse collapse '.$sxClass.'">';
	$html .= '<div class="panel-body pd0">';

	if(is_array($sx_procedure) && count($sx_procedure) > 0 )
	{
		$html	.= '<table id="sx_procedure_tbl" class="table table-bordered table-striped table-hover margin_0">';
		$html	.= '<thead class="grythead">';
		$html	.= '<tr >';
		$html	.= '<th class="col-xs-3">Name</td>';
		$html	.= '<th class="col-xs-2">Begin Date</td>';
		$html	.= '<th class="col-xs-3">Physician</td>';
		$html	.= '<th class="col-xs-4">Comments</td>';
		$html	.= '</tr>';
		$html	.= '</thead>';
		$html	.= '<tbody>';


		foreach($sx_procedure as $tmp_sx)
		{
			if($tmp_sx['allergy_status']=='Deleted'){continue;}
			$sx_name = ucfirst($tmp_sx['title']);
			$referredby = $tmp_sx['referredby'];
			$sx_cmnts = core_refine_user_input($tmp_sx['comments']);
			if(isset($tmp_sx['begdate_2'])){
				$sx_begdate = $tmp_sx['begdate_2'];
			}else{
				$sx_begdate = get_date_format($tmp_sx['begdate']);
			}
			if(preg_replace('/[^0-9]/','',$sx_begdate) == '00000000'){
				$sx_begdate = '';
			}

			$html .= '<tr>';
			$html .= '<td>'.$sx_name.'</td>';
			$html .= '<td>'.$sx_begdate.'</td>';
			$html .= '<td>'.$referredby.'</td>';
			$html .= '<td>'.$sx_cmnts.'</td>';
			$html .= '</tr>';

		}

		$html .= '</tbody>';
		$html .= '</table>';
	}
	else
	{
		if($no_sx) $html	.= $no_sx;
		else $html .= $no_sx_reviewed;
	}

	$html .= '</div>';
	$html .= '</div>';
	$html .= '</div>';
	// Panel End


	// Panel Start - Ocular Health
	$ohClass = ($acute || $patData || $relData || $misc_data || $spec_data) ? 'in' : '';
	$html .= '<div class="panel panel-primary">';
	$html .= '<div class="panel-heading pointer">';
	$html .= '<h4 class="panel-title" data-toggle="collapse"  href="#pnl_ocular_health">';
	$html .= 'Ocular Health';
	$html .= '</h4>';
	$html .= '</div>';
	$html .= '<div id="pnl_ocular_health" class="panel-collapse collapse '.$ohClass.'">';
	$html .= '<div class="panel-body pd0">';
	$html .= ($acute) ? '<table class="table table-bordered margin_0">'.$acute.'</table>' : '';
	$html .= '<table class="table table-bordered margin_0">';
	$html .= '<tr>';

	$html .= '<td style="width:50%; vertical-align:top!important;" class="pd0"><div class="row">';
	$html .= '<table class="table table-hover margin_0">';
	$html .= '<thead>';
	$html .= '<tr><th>Patient</th></tr>';
	$html .= '</thead>';
	$html .= '<tbody>';
	$html .= $patData;
	$html .= '</tbody>';
	$html .= '</table>';
	$html .= '</div></td>';

	$html .= '<td style="width:50%; vertical-align:top!important;" class="pd0"><div class="row">';
	$html .= '<table class="table table-hover margin_0">';
	$html .= '<thead>';
	$html .= '<tr><th>Family/Blood relative</th></tr>';
	$html .= '</thead>';
	$html .= '<tbody>';
	$html .= $relData;
	$html .= '</tbody>';
	$html .= '</table>';
	$html .= '</div></td>';
	$html .= '</tr>';

	$html	.= $misc_data;

	$html .= $spec_data;

	$html .= '</table>';
	$html .= '</div>';
	$html .= '</div>';
	$html .= '</div>';
	// Panel End

	// Panel Start - Ocular Medication
	$omRecords = (is_array($medQryRes) && count($medQryRes) > 0) ? true : false;
	$omClass = ($omRecords) ? 'in' : '';
	$html .= '<div class="panel panel-primary">';
	$html .= '<div class="panel-heading pointer">';
	$html .= '<h4 class="panel-title" data-toggle="collapse"  href="#ocular_medication">';
	$html .= 'Ocular Medication';
	$html .= '<input type="button" class="btn btn-success pull-right" value="Ocular Medication" style="margin-top:-8px;" onclick="openMedHX(\'medication\', \'800\');" />';
	$html .= '</h4>';
	$html .= '</div>';
	$html .= '<div id="ocular_medication" class="panel-collapse collapse '.$omClass.'">';
	$html .= '<div class="panel-body pd0">';

	if( $omRecords )
	{
		$html	.= '<table id="ocular_medication_tbl" class="table table-bordered table-striped table-hover margin_0">';
		$html	.= '<thead class="grythead">';
		$html	.= '<tr >';
		$html	.= '<th class="col-xs-3">Name (Dosage)</td>';
		$html	.= '<th class="col-xs-1">Site</td>';
		$html	.= '<th class="col-xs-1">Sig.</td>';
		$html	.= '<th class="col-xs-5">Comments</td>';
		$html	.= '<th class="col-xs-2">Begin Date</td>';
		$html	.= '</tr>';
		$html	.= '</thead>';
		$html	.= '<tbody>';


		foreach($medQryRes as $tmp_med)
		{
			if($tmp_med['allergy_status']!='Active' && $tmp_med['allergy_status']!='Order') continue;

			$med_name = ucfirst($tmp_med['title']);
			$med_destination = $tmp_med['destination'];
			if($tmp_med['sites'] == 3){
				$site = "OU";
			}elseif($tmp_med['sites'] == 2){
				$site = "OD";
			}elseif($tmp_med['sites'] == 1){
				$site = "OS";
			}elseif($tmp_med['sites'] == 4){
				$site = "PO";
			}else $site = '';
			$med_sig = $tmp_med['sig'];
			if(isset($tmp_med['begdate_2'])){
				$med_begdate = $tmp_med['begdate_2'];
			}else{
				$med_begdate =  get_date_format($tmp_med['begdate']);
			}
			$med_cmnts = core_refine_user_input($tmp_med['med_comments']);
			if(preg_replace("/[^0-9]/",'',$med_begdate)== '00000000'){
				$med_begdate = '';
			}
			$med_name_dosage = $med_name;
			if($med_destination) { $med_name_dosage = $med_name." (".$med_destination.")"; }
			$html .= '<tr>';
			$html .= '<td>'.$med_name_dosage.'</td>';
			$html .= '<td>'.$site.'</td>';
			$html .= '<td>'.$med_sig.'</td>';
			$html .= '<td>'.$med_cmnts.'</td>';
			$html .= '<td>'.$med_begdate.'</td>';
			$html .= '</tr>';
		}
	}
	else
	{
		if($no_ocular_med)
		{
			$html	.= $no_ocular_med;
		}
		else
		{
			$html .= $no_ocular_reviewed;
		}
	}
	$html .= '</tbody>';
	$html .= '</table>';

	$html .= '</div>';
	$html .= '</div>';
	$html .= '</div>';
	// Panel End


	// Panel Start - General HEalth
	$ghClass = ($gnDataRel || $gnDataPat || $gen_misc_data || $gen_spec_data) ? 'in' : '';
	$html .= '<div class="panel panel-primary">';
	$html .= '<div class="panel-heading pointer">';
	$html .= '<h4 class="panel-title" data-toggle="collapse"  href="#general_health">';
	$html .= 'General Health';
	$html .= '</h4>';
	$html .= '</div>';
	$html .= '<div id="general_health" class="panel-collapse collapse '.$ghClass.'">';
	$html .= '<div class="panel-body pd0">';
	$html .= '<table class="table table-bordered margin_0">';
	$html .= '<tr>';

	$html .= '<td style="width:50%; padding:0!important; vertical-align:top!important;">';
	$html .= '<table class="table margin_0">';
	$html .= '<thead>';
	$html .= '<tr><th>Patient</th></tr>';
	$html .= '</thead>';
	$html .= '<tbody>';
	$html .= $gnDataPat;
	$html .= '</tbody>';
	$html .= '</table>';
	$html .= '</td>';

	$html .= '<td style="width:50%; padding:0!important; vertical-align:top!important;">';
	$html .= '<table class="table margin_0">';
	$html .= '<thead>';
	$html .= '<tr><th>Family/Blood relative</th></tr>';
	$html .= '</thead>';
	$html .= '<tbody>';
	$html .= $gnDataRel;
	$html .= '</tbody>';
	$html .= '</table>';
	$html .= '</td>';
	$html .= '</tr>';

	$html	.= $gen_misc_data;

	$html .= $gen_spec_data;

	$html .= '</table>';

	$html .= '</div>';
	$html .= '</div>';
	$html .= '</div>';
	// Panel End

	// Panel Start - Review Of Systems
	$rosClass = ($review_data) ? 'in' : '';
	$html .= '<div class="panel panel-primary">';
	$html .= '<div class="panel-heading pointer">';
	$html .= '<h4 class="panel-title" data-toggle="collapse"  href="#review_systems">';
	$html .= 'Review Of Systems';
	$html .= '</h4>';
	$html .= '</div>';
	$html .= '<div id="review_systems" class="panel-collapse collapse '.$rosClass.'">';
	$html .= '<div class="panel-body pd0">';
	$html .= '<table class="table margin_0">'.($review_data ? $review_data : $review_no_known).'</table>';
	$html .= '</div>';
	$html .= '</div>';
	$html .= '</div>';
	// Panel End


	// Panel Start - Cholesterol
	$html .= '<div class="panel panel-primary">';
	$html .= '<div class="panel-heading pointer">';
	$html .= '<h4 class="panel-title" data-toggle="collapse"  href="#cholesterol">';
	$html .= 'Cholesterol';
	$html .= '</h4>';
	$html .= '</div>';
	$html .= '<div id="cholesterol" class="panel-collapse collapse '.$chClass.'">';
	$html .= '<div class="panel-body pd0">';
	$html .= $cholesterol_data;
	$html .= '</div>';
	$html .= '</div>';
	$html .= '</div>';
	// Panel End

	// Panel Start - Blood Sugar
	$html .= '<div class="panel panel-primary">';
	$html .= '<div class="panel-heading pointer">';
	$html .= '<h4 class="panel-title" data-toggle="collapse"  href="#blood_sugar">';
	$html .= 'Blood Sugar';
	$html .= '</h4>';
	$html .= '</div>';
	$html .= '<div id="blood_sugar" class="panel-collapse collapse '.$bsClass.'">';
	$html .= '<div class="panel-body pd0">';
	$html .= $blood_sugar_data;
	$html .= '</div>';
	$html .= '</div>';
	$html .= '</div>';
	// Panel End


	// Panel Start - Medication
	$medRecords =  (is_array($medQryResGen) && count($medQryResGen) > 0) ? true : false;
	$medClass = ($medRecords) ? 'in' : '';
	$html .= '<div class="panel panel-primary">';
	$html .= '<div class="panel-heading pointer">';
	$html .= '<h4 class="panel-title" data-toggle="collapse"  href="#medication">';
	$html .= 'Medication';
	$html .= '<input type="button" class="btn btn-success pull-right" value="Medications" style="margin-top:-8px;" onclick="openMedHX(\'medication\', \'800\');" />';
	$html .= '</h4>';
	$html .= '</div>';
	$html .= '<div id="medication" class="panel-collapse collapse '.$medClass.'">';
	$html .= '<div class="panel-body pd0">';

	if(is_array($medQryResGen) && count($medQryResGen) > 0 )
	{
		$html	.= '<table id="ocular_medication_tbl" class="table table-bordered table-striped table-hover margin_0">';
		$html	.= '<thead class="grythead">';
		$html	.= '<tr >';
		$html	.= '<th class="col-xs-3">Name (Dosage)</td>';
		$html	.= '<th class="col-xs-2">Sig.</td>';
		$html	.= '<th class="col-xs-1">Route</td>';
		$html	.= '<th class="col-xs-4">Comments</td>';
		$html	.= '<th class="col-xs-2">Begin Date</td>';
		$html	.= '</tr>';
		$html	.= '</thead>';
		$html	.= '<tbody>';


		foreach($medQryResGen as $tmp_med)
		{
			if($tmp_med['allergy_status']!='Active' && $tmp_med['allergy_status']!='Order' ) { continue; }
			$med_name = ucfirst($tmp_med['title']);
			$med_destination = $tmp_med['destination'];
			$med_sig = $tmp_med['sig'];
			$med_route = $tmp_med['med_route'];
			if(isset($tmp_med['begdate_2'])){
				$med_begdate = $tmp_med['begdate_2'];
			}
			else{
				$med_begdate = get_date_format($tmp_med['begdate']);
			}
			$med_cmnts2 = $tmp_med['med_comments'];
			if(preg_replace('/[^0-9]/','',$med_begdate) == '00000000'){
				$med_begdate = '';
			}
			$med_name_dosage = $med_name;
			if($med_destination) { $med_name_dosage = $med_name." (".$med_destination.")"; }

			$html .= '<tr>';
			$html .= '<td>'.$med_name_dosage.'</td>';
			$html .= '<td>'.$med_sig.'</td>';
			$html .= '<td>'.$med_route.'</td>';
			$html .= '<td>'.$med_cmnts2.'</td>';
			$html .= '<td>'.$med_begdate.'</td>';
			$html .= '</tr>';
		}

		$html .= '</tbody>';
		$html .= '</table>';
	}
	else
	{
		if($no_medication)
		{
			$html	.= $no_medication;
		}
		else
		{
			$html .= $no_med_reviewed;
		}
	}

	$html .= '</div>';
	$html .= '</div>';
	$html .= '</div>';
	// Panel End


	// Panel Start - Social
	$smkClass = ($smoke_data) ? 'in' : '';
	$html .= '<div class="panel panel-primary" id="socialGHDiv" >';
	$html .= '<div class="panel-heading pointer" style="position:relative;">';
	$html .= '<h4 class="panel-title" data-toggle="collapse" href="#social">';
	$html .= 'Social';
	$html .= '</h4>';
	$html .= '<span style="position:absolute;right:12px; top:0; font-size:1.6em;"  >';
	$html .= '<i title="Edit Social History" class="glyphicon glyphicon-edit pd5" onClick="showSocialForm(); " id="edit_icon"></i>';
	$html .= '<i title="Save" class="glyphicon glyphicon-save pd5 hidden" id="save_icon" onClick="saveSocialForm();"></i>';
	$html .= '<i title="Cancel" class="glyphicon glyphicon-remove-circle pd5 hidden" id="close_icon" onClick="cancelSocialFrmEditing();"></i>';
	$html .= '</span>';
	$html .= '</div>';
	$html .= '<div id="social" class="panel-collapse collapse '.$smkClass.'">';
	$html .= '<div class="panel-body pd0" id="socialBody">';
	$html .= '<div id="socialForm" class="row hidden">'.createSocialFldForm($socialQryRes[0]).'</div>';
	$html .= '<table class="table margin_0" id="socialDataTbl">'.$smoke_data.'</table>';
	$html .= '</div>';
	$html .= '</div>';
	$html .= '</div>';
	// Panel End


	$tmp_imm_data = '';
	$immClass = '';
	if( is_array($immQryRes) && count($immQryRes) > 0 )
	{
		foreach($immQryRes as $tmp_imm)
		{
			if($tmp_imm['status']=='Given'){continue;}
			$immClass = 'in';
			$immunization_id = $tmp_imm['immunization_id'];
			$immzn_type = $tmp_imm['immzn_type'];
			$immzn_dose = $tmp_imm['immzn_dose'];
			$adverse_reaction = $tmp_imm['adverse_reaction'];
			if(isset($tmp_imm['administered_date_2'])){
				$administered_date = $tmp_imm['administered_date_2'];
			}else{
				$administered_date = get_date_format($tmp_imm['administered_date']);
			}
			if(preg_replace('/[^0-9]/','',$administered_date) == '00000000'){
				$administered_date = '';
			}

			$tmp_imm_data .= '<tr>';
			$tmp_imm_data .= '<td>'.$immunization_id.'</td>';
			$tmp_imm_data .= '<td>'.$immzn_type.'</td>';
			$tmp_imm_data .= '<td>'.$immzn_dose.'</td>';
			$tmp_imm_data .= '<td>'.$administered_date.'</td>';
			$tmp_imm_data .= '<td>'.$adverse_reaction.'</td>';
			$tmp_imm_data .= '</tr>';
		}
	}
	else
	{
		if($no_imm) $tmp_imm_data	.= $no_imm;
	}

	// Panel Start - Immunizations
	$html .= '<div class="panel panel-primary">';
	$html .= '<div class="panel-heading pointer">';
	$html .= '<h4 class="panel-title" data-toggle="collapse"  href="#immunizations">';
	$html .= 'Immunizations';
	$html .= '</h4>';
	$html .= '</div>';
	$html .= '<div id="immunizations" class="panel-collapse collapse '.$immClass.'">';
	$html .= '<div class="panel-body pd0">';

	if( $immRecords )
	{
		$html	.= '<table id="immunization_tbl" class="table table-bordered table-striped table-hover margin_0">';
		$html	.= '<thead class="grythead">';
		$html	.= '<tr >';
		$html	.= '<th class="col-xs-3">Name (Dosage)</td>';
		$html	.= '<th class="col-xs-2">Type</td>';
		$html	.= '<th class="col-xs-3">Dose</td>';
		$html	.= '<th class="col-xs-2">Date&nbsp;Adminstd.</td>';
		$html	.= '<th class="col-xs-2">Reaction</td>';
		$html	.= '</tr>';
		$html	.= '</thead>';
		$html	.= '<tbody>'.$tmp_imm_data.'</tbody>';
		$html .= '</table>';
	}
	else
	{
		$html	.= $tmp_imm_data;
	}

	$html .= '</div>';
	$html .= '</div>';
	$html .= '</div>';
	// Panel End


	// Panel Start - Patient Problem List
	$probRecords = (is_array($probQryRes) && count($probQryRes) > 0) ? true : false;
	$probClass =  ($probRecords) ? 'in' : '';
	$html .= '<div class="panel panel-primary">';
	$html .= '<div class="panel-heading pointer">';
	$html .= '<h4 class="panel-title" data-toggle="collapse"  href="#problem_list">';
	$html .= 'Patient Problem List';
	$html .= '</h4>';
	$html .= '</div>';
	$html .= '<div id="problem_list" class="panel-collapse collapse '.$probClass.'">';
	$html .= '<div class="panel-body pd0">';

	if( $probRecords )
	{
		$html	.= '<table id="immunization_tbl" class="table table-bordered table-striped table-hover margin_0">';
		$html	.= '<thead class="grythead">';
		$html	.= '<tr >';
		$html	.= '<th class="col-xs-2">Date</td>';
		$html	.= '<th class="col-xs-8">Problem Name</td>';
		$html	.= '<th class="col-xs-2">Status</td>';
		$html	.= '</tr>';
		$html	.= '</thead>';
		$html	.= '<tbody>';


		foreach($probQryRes as $tmp_prob)
		{
			if($tmp_prob['status']!='Active'&&$tmp_prob['status']!='External'){continue;}

			if(isset($tmp_prob['onset_date_2'])){
				$onset_date = $tmp_prob['onset_date_2'];
			}
			else{
				$onset_date = get_Date_format($tmp_prob['onset_date']);
			}

			$problem_name = "".wordwrap($tmp_prob['problem_name'],60,"\n<br />", true);
			$status = $tmp_prob['status'];

			$html .= '<tr>';
			$html .= '<td>'.$onset_date.'</td>';
			$html .= '<td>'.$problem_name.'</td>';
			$html .= '<td>'.$status.'</td>';
			$html .= '</tr>';
		}

		$html .= '</tbody>';
		$html .= '</table>';
	}
	else
	{
		$html	.= '<b class="col-xs-12 text-center alert alert-info margin_0">No record found</b>';
	}

	$html .= '</div>';
	$html .= '</div>';
	$html .= '</div>';
	// Panel End


	// Panel Start - Active Directives
	$html .= '<div class="panel panel-primary">';
	$html .= '<div class="panel-heading pointer">';
	$html .= '<h4 class="panel-title" data-toggle="collapse"  href="#active_dir">';
	$html .= 'Active Directives';
	$html .= '</h4>';
	$html .= '</div>';
	$html .= '<div id="active_dir" class="panel-collapse collapse">';
	$html .= '<div class="panel-body pd0">';

	if($flgArc==1){
		$patQryRes[0]=$arrPtD;
	}
	else
	{
		$query= "select ado_option,desc_ado_other_txt from patient_data where id = '".$pid."'
										and ado_option != 'no' and ado_option != 'NULL'";
		$sql = imw_query($query);
		$patQryRes = imw_fetch_assoc($sql);
	}

	$ado_option = ($ado_option=="Other") ? $patQryRes['desc_ado_other_txt'] : $patQryRes['ado_option'];
	if($ado_option != '')
	{
		$query = "select scan_id from ".constant("IMEDIC_SCAN_DB").".scans
											where patient_id = '".$pid."' and form_id = '0' and image_form = 'ptInfoMedHxGeneralHealth'";
		$sql = imw_query($query);
		$scanQryRes = imw_fetch_assoc($sql);
		$scan_id = $scanQryRes["scan_id"];
		$scan_active_data = '';
		if($scan_id>0){
			$scan_active_data .= '<tr>';
			$scan_active_data .= '<td>';
			$scan_active_data .= '<a class="text_10ab" href="javascript:showpdf(\''.$scan_id.'\',\'pdf\');">';
			$scan_active_data .= '&bull;&nbsp;'.$ado_option;
			$scan_active_data .= '</a>';
			$scan_active_data .= '</td>';
			$scan_active_data .= '</tr>';
		}
		else{
			$scan_active_data .= '<tr><td>&bull;&nbsp;'.$ado_option.'</td></tr>';
		}
	}

	$html .= '<table class="table margin_0">'.$scan_active_data.'</table>';
	$html .= '</div>';
	$html .= '</div>';
	$html .= '</div>';
	// Panel End


  $html .= '</div>';// Panel Group

	$html .= '</div>';// Modal Body

	$html .= '<div class="modal-footer ad_modal_footer" id="module_buttons">';
	$reviewedFunc 	= 'ptInfoReviewed()';
	$ptChange	 	= 'top.core_redirect_to(\'Medical_Hx\')';
	$closeBtn		= 'close_gh_window()';
	if(strpos($_SERVER['HTTP_REFERER'],'MU_checklist')>0){
		$reviewedFunc 	= 'ptInfoReviewed();setTimeout(function(){window.location.reload();},1000)';
		$ptChange	 	= 'window.opener.top.core_redirect_to(\'Medical_Hx\')';
		$closeBtn		= "$('#genHealthDiv_wv').modal('hide');";
	}


	$html .= '<button type="button" class="btn btn-success" id="reviewd" onClick="'.$reviewedFunc.'">Reviewed</button>';
	$html .= '<button type="button" class="btn btn-success" id="ptChange" onClick="'.$ptChange.'">Change</button>';
	$html .= '<button type="button" class="btn btn-danger" onClick="'.$closeBtn.'">Close</button>';
	$html .= '</div>';


	$html .= '</div>';// Modal Content
	$html .= '</div>';// Modal Dialog


	return $html;
}

function isChartReviewable($formId,$loggedId,$flgEd=0)
{
	$isReviewable=false;
	$isEditable=false;
	$iscur_user_vphy=false;
	//Finalize Provider
	$qry = "SELECT c1.id, c1.update_date, c1.patient_id, c1.providerId,
				c1.finalizerId FROM chart_master_table c1 WHERE c1.id='".$formId."' ";
	$sql = imw_query($qry);
	$row = imw_fetch_assoc($sql);
	if($row != false)
	{
		$finalizeDate = $row["update_date"];
		$patientId = $row["patient_id"];
		$providerId = $row["providerId"];
		//$doctorId = $row["doctorId"];
		$finalizeDoctorId = $row["finalizerId"];
		//$coSignerId = $row["cosigner_id"];
	}

	//
	$arrProIds=array();
	$qry = "SELECT c2.id FROM chart_signatures c1
			LEFT JOIN users c2 ON c1.pro_id =  c2.id
			WHERE form_id='".$formId."' AND c2.user_type!='13'
			ORDER BY sign_type  ";
	$sql = imw_query($qry);
	$cnt = imw_num_rows($sql);

	for( $i = 1; $row = imw_fetch_assoc($sql); $i++ ){
		if($row["id"] != false){
			$arrProIds[] = $row["id"];
			if($i==1){ $doctorId = $row["id"];   }
			if($i==2){ $coSignerId = $row["id"];   }
		}
	}

	//Check for previous data
	if(empty($finalizeDoctorId)){
		$finalizeDoctorId = (!empty($doctorId)) ? $doctorId : $providerId;
	}
	//Check Doctor
	$doctorId = (!empty($doctorId)) ? $doctorId : $providerId;

	if(($finalizeDoctorId == $loggedId))
	{
		$qry = "SELECT chart_timer FROM facility WHERE facility_type = '1' ORDER BY id limit 0,1 ";
		$sql = imw_query($qry);
		$row = imw_fetch_assoc($sql);
		if($row != false){
			$reviewTime = $row["chart_timer"];
		}

		//if $reviewTime is empty, check in id = 1 ---
		if(!empty($reviewTime)){
			//time
			$qry = "SELECT chart_timer FROM facility WHERE id = '1' ";
			$sql = imw_query($qry);
			$row = imw_fetch_assoc($sql);
			if($row != false){
				$reviewTime = $row["chart_timer"];
			}
		}
		//if $reviewTime is empty, check in id = 1 ---


		if(!empty($reviewTime)){
			$reviewTimeHrs = 24 * $reviewTime;
			$qry = "SELECT UNIX_TIMESTAMP(DATE_ADD('".$finalizeDate."', INTERVAL ".$reviewTimeHrs." HOUR)) as reviewableTime, ". "UNIX_TIMESTAMP(NOW()) as curTime ";
			$sql = imw_query($qry);
			$row = imw_fetch_assoc($sql);
			if($row != false){
				if($row["reviewableTime"] > $row["curTime"]){
					$isReviewable=true;
				}
			}
		}
	}

	//isEditable
	//if Loggin user is signer/cosigner
	if(($doctorId == $loggedId) || ($coSignerId == $loggedId)){
		//Check if all records are finalized
		if(isAllCNFinalized($patientId)){
			$isEditable=true;
		}
		//Check valid physician
		$iscur_user_vphy = true;
	}

	return ($flgEd==1) ? array($isReviewable,$isEditable,$iscur_user_vphy):$isReviewable ;
}

function isAllCNFinalized($pid)
{
	$qry = "SELECT id FROM chart_master_table ".
					"WHERE patient_id = '".$pid."' ".
					"AND finalize = '0' AND delete_status='0' ";
	$sql = imw_query($qry);
	$cnt = imw_num_rows($sql);
	if( $cnt > 0){
		return false ; // Active
	}
	return true; // Finalized
}

function scnfoldrCatIdFun($dBaseName,$categoryFolderNme)
{
	$scnCatId='';
	$sqlQry = "SELECT folder_categories_id FROM ".$dBaseName.".folder_categories
			   WHERE folder_name='".addslashes($categoryFolderNme)."' AND parent_id='0' AND patient_id='0'
			   ORDER BY folder_categories_id";
	$sqlRes = imw_query($sqlQry) or die(imw_error());
	if(imw_num_rows($sqlRes)>0) {
		$sqlRow = imw_fetch_array($sqlRes);
		$scnCatId=$sqlRow['folder_categories_id'];

	}
	return $scnCatId;

}

if(!function_exists('getPtLExamInfo')){
	function getPtLExamInfo($pid,$fid="")
	{
			if(!empty($fid)){
				$strFid = "  AND formid='".$fid."' ";
			}

			$tmp = "";
			$qry = "select operator_id,date_format(created_date,'".get_sql_date_format('','Y')." %h:%i %p') as createdDate
				  from patient_last_examined where patient_id = '$pid' ".$strFid."
				  order by patient_last_examined_id desc limit 0,1";
			$sql = imw_query($qry);
			$row = imw_fetch_assoc($sql);
			if($row != false){
				$operator_id = $row["operator_id"];
				$createdDate = $row['createdDate'];
			}

			if(!empty($operator_id)){
				$qry = "select concat(substr(fname from 1 for 1),'',
					  substr(lname from 1 for 1)) as name from users where id = '$operator_id'";
				$sql = imw_query($qry);
				$row = imw_fetch_assoc($sql);
				if($row != false){
					$phyDetail = $row["name"];
				}
				$tmp = " ".$createdDate." ".$phyDetail;
			}
			return $tmp;
	}
}


function getOwnerShipChangeMsg($pid, $fid)
{
	$str="";

	if(empty($pid) || empty($fid)){ return $str; }

	//show for physician only
	if(!in_array($_SESSION["logged_user_type"],$GLOBALS['arrValidCNPhy'])){ return $str; }

	//msg into CC
	$phy_nm = getUserFirstName($_SESSION["authId"],3);
	$msg = "HPI was performed by ".$phy_nm.".";

	//
	$flgShow=0;
	$str_tech = implode(",", $GLOBALS['arrValidCNTech']);

	//if CC, Patient Pain, Neuro
	//
	if(empty($flgShow)){
	$qry = "select id from chart_left_cc_history c1
						LEFT JOIN users c2 ON c1.pro_id = c2.id
						where c1.patient_id='".$pid."' AND c1.form_id='".$fid."'
						AND c2.user_type IN (".$str_tech.") ";
	$sql = imw_query($qry);
	$row = imw_fetch_assoc($sql);
	if($row!=false){	$flgShow=1; }
	}

	//if Ocu Hx
	if(empty($flgShow)){
	$qry = "select id from chart_left_provider_issue c1
						LEFT JOIN users c2 ON c1.uid = c2.id
						where c1.patient_id='".$pid."' AND c1.form_id='".$fid."'
						AND c2.user_type IN (".$str_tech.") ";
	$sql = imw_query($qry);
	$row = imw_fetch_assoc($sql);
	if($row!=false){	$flgShow=1; }
	}

	//Show Message
	if($flgShow==1){
		$str = "<tr id=\"tr_ownership_msg\" data-msg='".$msg."'>
							<td>I reviewed and approve the following:-<br/>
										<label style=\"margin-left:20px;\">Chief Complaint</label>
										<label style=\"margin-left:20px;\">Ocular Hx</label>
										<label style=\"margin-left:20px;\">Patient Pain Level</label>
										<label style=\"margin-left:20px;\">Neuro/Psych</label>
							</td>
						</tr>";

	}

	if( $str == '')
	{
		return false;
	}
	return $str;

}

if(!function_exists('getPtGenHealthOcularInfo')){
	function getPtGenHealthOcularInfo($pid,$arrLogOc=array())
{
		$retVal = "";
		if(!empty($pid))
		{
			$retVal = array();

			//Get From Achive --
			$flgArc=0;
			if(count($arrLogOc) > 0){
				$flgArc=$arrLogOc[0];
				$arr=$arrLogOc[1];
			}
			//Get From Achive --

			//Check if previous data exists, else use current data
			if($flgArc==0)
			{
				$qry = "select you_wear, any_conditions_you, chronicDesc, eye_problems,
											 any_conditions_others_you,eye_problems_other, any_conditions_relative,
											 chronicRelative, OtherDesc, any_conditions_other_relative
											 from ocular where patient_id='".$pid."' ";
				$sql = imw_query($qry);
				$row = imw_fetch_assoc($sql);
			}
			else { $row = $arr; }

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
						if(!empty($valTmp) && !empty($arrChroCond[$valTmp-1])){
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
						if(!empty($valTmp) && !empty($arrChroCond[$valTmp-1])){
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


function patient_communication($pid, $view = 'view_active', $mode = '', &$request = array() )
{
	if( $pid == 0) return;

	$date = $request['pat_msg_date'];
	$date = getDateFormatDB($date);
	if($date == '') $date = date('Y-m-d');
	$msg = $request['pat_msg_txt'];
	$sub = $request['pat_msg_sub'];
	$app_status = $request['approve_status'];

	if($mode == 'delete')
	{
		$arrFields = array();
		$arrFields['del_status'] = 1;
		$arrFields['del_operator_id'] = $_SESSION['authId'];
		$arrFields['del_datetime'] = date('Y-m-d H:i:s');
		UpdateRecords($request['del_id'],'user_message_id',$arrFields,'user_messages');
		unset($arrFields);
	}
	else if($mode == 'edit')
	{
		$qry = "SELECT message_text,message_subject,approved FROM user_messages WHERE user_message_id='".$request['edit_id']."' LIMIT 0,1";
		$sql = imw_query($qry);
		$res = imw_fetch_assoc($sql);

		if($res['message_text'] != $msg  || $res['message_subject'] != $sub || $res['approved']!=$app_status ) //|| strpos($res['approved'], $date)===false
		{
			$qry = "INSERT INTO user_messages (patientId,Pt_Communication,message_to)
					(SELECT patientId,Pt_Communication,message_to FROM user_messages WHERE user_message_id ='".$request['edit_id']."')";
			imw_query($qry);
			$insert_user_message_id = imw_insert_id();

			$arrFields = array();
			$arrFields['message_subject'] = core_refine_user_input($sub);
			$arrFields['message_text'] = core_refine_user_input($msg);
			$arrFields['approved'] = ($app_status);
			$arrFields['message_sender_id'] = $_SESSION['authId'];
			$arrFields['message_send_date'] = $date.' '.date('H:i:s'); //date("Y-m-d H:i:s");
			UpdateRecords($insert_user_message_id,'user_message_id',$arrFields,'user_messages');
			unset($arrFields);

			$arrFields = array();
			$arrFields['edit_status'] = 1;
			$arrFields['edit_user_message_id'] = $insert_user_message_id;
			UpdateRecords($request['edit_id'],'user_message_id',$arrFields,'user_messages');
			unset($arrFields);
		}
	}
	else if($mode == 'get_edit_vals')
	{
		$qry = "SELECT message_text,message_subject,approved FROM user_messages WHERE user_message_id='".$request['eid']."' LIMIT 0,1";
		$sql = imw_query($qry);
		$res = imw_fetch_assoc($sql);
		$res["message_subject"] = html_entity_decode($res["message_subject"]);
		$res["message_text"] = html_entity_decode($res["message_text"]);
		if(strpos($res["message_text"],'&quot;')!==false || strpos($res["message_subject"],'&quot;')!==false){
			$res["message_subject"] = html_entity_decode($res["message_subject"]);
			$res["message_text"] = html_entity_decode($res["message_text"]);
		}
		$res["message_text"] = stripslashes($res["message_text"]);
		$res["message_subject"] = stripslashes($res["message_subject"]);
		echo json_encode($res);
		exit();
	}
	else if($mode == 'add')
	{
		$dataArr = array();

		$dataArr['message_send_date'] = $date.' '.date('H:i:s');
		$dataArr['message_text'] = core_refine_user_input($msg);
		$dataArr['message_subject'] = core_refine_user_input($sub);
		$dataArr['patientId'] = $pid;
		$dataArr['message_sender_id'] = $_SESSION['authId'];
		$dataArr['approved'] = ($app_status);
		$dataArr['Pt_Communication'] = 1;
		$dataArr['message_status'] = 0;
		$dataArr['message_to'] = $_SESSION['authId'];
		$insertId = AddRecords($dataArr,'user_messages');
	}

	if(isset($view) && $view == "view_all")	$whereCond = '';
	else $whereCond = 'and user_messages.del_status = 0 and user_messages.edit_status = 0';

	$msg_qry = "select user_messages.user_message_id as id,
									user_messages.message_send_date as created_date, user_messages.message_subject as task_subject,
									user_messages.edit_status,user_messages.del_status,user_messages.del_datetime,user_messages.sent_to_groups,
									users.lname as user_lname,users.fname as user_fname,users.mname as user_mname,
									user_messages.message_text as message_text, usr2.lname as del_user_lname,
									usr3.lname as to_user_lname, usr3.fname as to_user_fname,usr3.mname as to_user_mname,
									usr2.fname as del_user_fname,usr2.mname as del_user_mname, user_messages.approved
									from user_messages
									left join patient_data on user_messages.patientId = patient_data.id
									left join users on users.id = user_messages.message_sender_id
									left join users usr2 on usr2.id = user_messages.del_operator_id
									left join users usr3 on usr3.id = user_messages.message_to
									where  patient_data.id = '".$pid."' and user_messages.Pt_Communication = '1' ".$whereCond."
									group by message_send_date,message_text
									order by user_messages.message_send_date desc";
	$sql = imw_query($msg_qry);
	$cnt = imw_num_rows($sql);

	$no_rec_msg = '';
	if(isset($view) && $view == "view_all"){
		if( $cnt <=0 ){
			$no_rec_msg = '<tr><td class="text-center" colspan="5">No records found</td></tr>';
		}
	}

	/*** Updating review by physician ***/
	if(			(intval($_SESSION['logged_user_type'])==1 || intval($_SESSION['logged_user_type'])==12)
			&&	( (isset($mode) && ($mode =="" || $mode=='view_all')) || !isset($mode)))
	{
		$query_rvw = "UPDATE user_messages SET review_by='".intval($_SESSION['logged_user_type'])."', review_on='".date('Y-m-d H:i:s')."' WHERE patientId='".$pid."' AND Pt_Communication = '1'";
		$result_rvw = imw_query($query_rvw);
	}
	/*** review updation end ***/

	$com_data = NULL;
	$GLOBALDATEFORMAT = $GLOBALS["date_format"];

	while( $mainRes = imw_fetch_assoc($sql) )
	{
			$id = $mainRes['id'];
			$created_date = strpos($mainRes['created_date'],"0000")===false ? date(phpDateFormat(),strtotime($mainRes['created_date']))." ".date('g:i A',strtotime($mainRes['created_date'])) : "" ;
			$created_date = str_replace(" ","&nbsp;",$created_date);
			$operator_id = $mainRes['operator_id'];
			$approv_decline =(ucwords($mainRes['approved']))?ucwords($mainRes['approved']):"Accept";

			//decode
			if(strpos($mainRes['task_subject'],"%20")!==false || strpos($mainRes['message_text'],"%20")!==false){
			$mainRes['task_subject'] = urldecode($mainRes['task_subject']);
			$mainRes['message_text'] = urldecode($mainRes['message_text']);
			}

			$msg_subject = html_entity_decode(ucwords(trim(stripslashes($mainRes['task_subject']))));

			//--- OPERATOR NAME FORMAT -----
			$user_name_arr = array();
			$user_name_arr["LAST_NAME"] = $mainRes['user_lname'];
			$user_name_arr["FIRST_NAME"] = $mainRes['user_fname'];
			$user_name_arr["MIDDLE_NAME"] = $mainRes['user_mname'];
			$user_name = changeNameFormat($user_name_arr);

			$message_text = html_entity_decode(ucfirst(str_replace("\n"," ",stripslashes($mainRes['message_text']))));

			$del_user_name_arr = array();
			$del_user_name_arr["LAST_NAME"] = $mainRes['del_user_lname'];
			$del_user_name_arr["FIRST_NAME"] = $mainRes['del_user_fname'];
			$del_user_name_arr["MIDDLE_NAME"] = $mainRes['del_user_mname'];
			$deleted_by = changeNameFormat($del_user_name_arr);

            //Create recipients list
            $received_by='';
            if(isset($mainRes['sent_to_groups']) && $mainRes['sent_to_groups'] != '') {
                $temp_users=array();
                $temp_users = explode('<br>', html_entity_decode($mainRes['sent_to_groups']));
                $group_list = array_shift($temp_users);
                $Ugroup_arr = explode(',', $group_list);
                if(!empty($Ugroup_arr)) {
                    foreach($Ugroup_arr as $Ugroup) {
                        $tugrp='';
                        if(!empty($Ugroup)) {
                            $tUgroup=explode('--', trim($Ugroup));
                            if(count($tUgroup)>0) {
                                $tugrp.=$tUgroup[0].'; ';
                            } else {
                                $tugrp.=trim($Ugroup).'; ';
                            }
                            $received_by.=$tugrp;
                        }
                    }
                }
                if(count($temp_users)>0)
                $received_by = $received_by.implode(';',$temp_users).';';
            }

            $to_user_name_arr = array();
            $to_user_name_arr["LAST_NAME"] = $mainRes['to_user_lname'];
            $to_user_name_arr["FIRST_NAME"] = $mainRes['to_user_fname'];
            $to_user_name_arr["MIDDLE_NAME"] = $mainRes['to_user_mname'];
            if($received_by!='' && ($received_by!=($mainRes['to_user_lname'].', '.$mainRes['to_user_fname'].' '.$mainRes['to_user_mname'].';') && ($received_by!=changeNameFormat($to_user_name_arr).';'))) {
                $received_by = $received_by.changeNameFormat($to_user_name_arr).';';
            } else {
                $received_by = changeNameFormat($to_user_name_arr).';';
            }
						
						//remove single semicolon
						$tmp = str_replace(";","",$received_by);
						$tmp = trim($tmp);
						if(empty($tmp)){$received_by="";}


			//By Karan
			// $deletedDateDB = explode(" ",$mainRes['del_datetime']);
			// $GlobalDeleteDate = explode("-",$deletedDateDB[0]);

			// if($GLOBALDATEFORMAT == "dd-mm-yyyy" && $GLOBALDATEFORMAT != ""){
			// 	$GLbDeleteDate = $GlobalDeleteDate[2]."-".$GlobalDeleteDate[1]."-".$GlobalDeleteDate[0];
			// }
			// else{
			// 	$GLbDeleteDate = $GlobalDeleteDate[1]."-".$GlobalDeleteDate[2]."-".$GlobalDeleteDate[0];
			// }
			// $deleted_datetime = $GLbDeleteDate."&nbsp;".$deletedDateDB[1];

			$deleted_datetime = "<br/>".date(phpDateFormat(),strtotime($mainRes['del_datetime']))."<br/>".date('g:i A',strtotime($mainRes['del_datetime']));

			$temp_created_date = preg_split('/ /',$created_date);
			$created_date = $temp_created_date[0].'<br>'.$temp_created_date[1].' '.$temp_created_date[2];

			//--- PATIENT COMMUNICATION DATA ----
			$subClass = "";
			if($mainRes['del_status'] == 1){
				$subClass = "del_text";
				$txtClass = "del_text";
			}
			else if($mainRes['edit_status'] == 1){
				$txtClass = "text-orange";
				$subClass = "text-orange";
			}
			else{
				$txtClass = "";
			}

			/*
			$slashedSubject = $mainRes['task_subject'];
			$slashedText = $mainRes['message_text'];
			if(strpos($slashedText,'&quot;')!==false || strpos($slashedSubject,'&quot;')!==false){
				$slashedSubject = htmlentities($slashedSubject);
				$slashedText = htmlentities($slashedText);
			}
			$slashedSubject = addslashes($slashedSubject);
			$slashedText = addslashes($slashedText);
			$slashedSubject = html_entity_decode($slashedSubject);
			$slashedText = html_entity_decode($slashedText);
			*/
			// $slashedSubject=$slashedText=$approv_decline="";
			$slashedSubject=$slashedText="";


			$onClick = '';
			if($mainRes['edit_status']== 0 && $mainRes['del_status'] == 0){
				$onClick = 'onClick="edit_pvc_message('.$id.',\''.$slashedSubject.'\',\''.$slashedText.'\',\''.$approv_decline.'\')"';
				$subClass = 'text_purple pointer';
			}

			$com_data .= '<tr class="valign-top">';
			$com_data .= '<td class="nowrap">'.$created_date.'</td>';
			$com_data .= '<td>'.$approv_decline.'</td>';
			$com_data .= '<td><span class="mb5 '.$subClass.'" '.$onClick.'>'.$msg_subject.'</span><br>'.$user_name.'</td>';
			$com_data .= '<td class="'.$txtClass.'">'.$message_text.'</td>';
			$com_data .= '<td >'.$received_by.'</td>';
			$com_data .= '<td>';
			if($mainRes['del_status'] == 1)
				$com_data .= '<b>Deleted by: </b>'.$deleted_by.' on '.$deleted_datetime;
			elseif($mainRes['edit_status']== 0 && $mainRes['del_status'] == 0)
				$com_data .= '<span class="glyphicon glyphicon-remove pointer" title="Delete Message" onClick="pt_comm_action(\'delete\',\''.$id.'\')" ></span>';
			$com_data .= '</td>';
			$com_data .= '</tr>';

	}

	$table   = '';

	$table	.= '';
	$table	.= '<div class="" style="max-height:200px; oveflow:hidden; overflow-y:scroll;">';
	$table	.= '<table id="tbl_pt_comm" class="table table-bordered table-striped table-hover margin_0">';
	$table	.= '<thead class="grythead">';
	$table	.= '<tr>';
	$table	.= '<th class="col-xs-2">Date</td>';
	$table	.= '<th class="col-xs-1">Accept/Decline</td>';
	$table	.= '<th class="col-xs-2">Subject/Sender</td>';
	$table	.= '<th class="col-xs-4">Communication</td>';
	$table	.= '<th class="col-xs-1">Receiver</td>';
	$table	.= '<th class="col-xs-2">Action</td>';
	$table	.= '</tr>';
	$table	.= '</thead>';
	$table	.= '<tbody>';
	$table	.= '{{__DATA__}}';
	$table	.= '</tbody>';
	$table	.= '</table>';
	$table	.= '</div>';

	$html = '';
	if( $mode === 'view_all')
	{
		$to_replace = ($cnt > 0 ) ? 'com_data' : 'no_rec_msg';
		$html = str_replace('{{__DATA__}}',$$to_replace,$table);
	}
	else if( $cnt > 0 )
	{
		$html = str_replace('{{__DATA__}}',$com_data,$table);
	}

	return $html;

}

function ptGlancePopUp($request) {

	$patientId=$_SESSION["patient"];

	require($GLOBALS['srcdir']."/classes/pt_at_glance.class.php");
	$glance = new Pt_at_glance($patientId,$_SESSION['authId'],$request);

	$pdg_showpop = 1;
	$html = '';
	//$html .= '<div id="pgd_showpop" onmousemove="$(this).show();" onmouseout="$(\'#pgd_showpop\').hide()" class="div_popup white border" style="top:0px; left:5px;max-width:98%; height:'.($_SESSION['wn_height']-350).'px; overflow-x:hidden; overflow-y:auto;">';
	//Active List ----------
	if($request['limit_records']){ $limit_records=" LIMIT 0,10 "; }

	$patient_data = $glance->get_patient_data($glance->patient_id);
	$str_ActPtProbList = $glance->pt_active_prob_list($glance->patient_id, " AND status != 'Deleted' AND status != 'Inactive' ");
	$str_ActOrder = $glance->pt_active_order($glance->patient_id);
	$str_ActTests = $glance->pt_active_test($glance->patient_id,'',1);

	$html .= '<style type="text/css">td#redBold { font-weight:bold; color:Red;}
			td#orangeBold { font-weight:bold; color:Orange;}
			td.pgd_apTbl div p:nth-child(1) { margin: 0px; width: 5px; position: absolute; }
			td.pgd_apTbl div p:nth-child(2) { margin: 0px; padding-left: 8px; }
			#pgd_showpop .num_cnt{ margin-left:3px!important;border:1px solid #CCC; font-size:12px; font-weight:bold; cursor:pointer; color:#666; background:#F9F8F6; font-family:Verdana, Geneva, sans-serif; padding:2px 5px 2px 5px;}
			.num_cnt.selected{ font-size:14px; color:#FFF !important; cursor:text;  background:#5c2a79 !important;}
			.pagination > li:first-child > span { border-radius: 50px !important; color: #000000; }
			</style>';
	$html .= '<script>
	</script>';
	$proc_info = $glance->get_chart_procedures($glance->patient_id);

	$cols1 = 0;

	if(!empty($str_ActPtProbList)||!empty($str_ActOrder)||!empty($str_ActTests) || !empty($proc_info)  )
	{
		if(!empty($str_ActPtProbList) ) $cols++;
		if(!empty($str_ActOrder) || !empty($str_ActTests))  $cols++;
		if(!empty($proc_info) ) $cols++;

		if( $cols == 1) $width1 = $width2 = $width3 = "100%";
		if( $cols == 2) $width1 = $width2 = $width3 = "50%";
		if( $cols == 3) $width1 = "30%"; $width2= "35%"; $width3="35%";

		$html .= '<table class="table table-bordered mb0">';
		$html .= '<thead>';
		$html .= '<tr class="" valign="top">';
		if(!empty($str_ActPtProbList) ) $html .= '<th style="width:'.$width1.'">Active Patient Problem List</th>';
		if(!empty($str_ActOrder) || !empty($str_ActTests))  $html .= '<th style="width:'.$width2.'">Active Orders/Tests</th>';
		if(!empty($proc_info) ) $html .= '<th style="width:'.$width3.'">Procedure</th>';
		$html .= '</tr>';
		$html .= '</thead>';
		$html .= '<tbody>';

		$html .= '<tr valign="top">';
		if(!empty($str_ActPtProbList) ) {
			$html	.= '<td style="padding:0 !important;vertical-align:top!important;">';
			$html .= '<table class="table table-bordered table-striped table-hover">';
			$html .= '<thead>';
			$html .= '<tr class="grythead">';
			$html .= '<td class="text-nowrap">Date</td>';
			$html .= '<td>Patient Problem</td>';
			$html .= '</tr>';
			$html .= '</thead>';
			$html .= '<tbody id="ppl_res">'.$str_ActPtProbList.'</tbody>';
			$html .= '</table>';
			$html	.= '</td>';
		}

		if(!empty($str_ActOrder) || !empty($str_ActTests))  {
			$html	.= '<td style="padding:0 !important;vertical-align:top!important;">';
			//$html .= '<div class="row">';
			// Printing Active Orders
			$html .= '<div class="col-xs-7">';
			$html .= '<div class="row">';
			//if(!empty($str_ActOrder)) {
				$html .= '<table class="table table-bordered table-striped table-hover mb0" >';
				$html .= '<tr class="grythead">';
				$html .= '<td>Date</td>';
				$html .= '<td colspan="2">Orders</td>';
				$html .= '</tr>';
				$html .= $str_ActOrder;
				$html .= '</table>';
			//}
			$html	.= '</div>';
			$html	.= '</div>';

			// Printing Active Tests
			$html .= '<div class="col-xs-5" style="overflow:hidden; overflow-x:auto;">';
			$html .= '<div class="row">';
			//if(!empty($str_ActTests)) {
				$html .= '<table class="table table-bordered table-striped table-hover mb0">';
				$html .= '<thead>';
				$html .= '<tr class="grythead"><td colspan="3">Tests</td></tr>';
				$html .= '</thead>';
				$html .= '<tbody id="test_res">';
				$html .= $str_ActTests;
				$html .= '</tbody>';
				$html .= '</table>';
			//}
			$html	.= '</div>';
			$html	.= '</div>';

			//$html	.= '</div>';
			$html	.= '</td>';

		}

		if(!empty($proc_info) ) {
			$html .= '<td style="padding:0!important;vertical-align:top!important;">';
			$html .= '<table class="table table-bordered table-striped table-hover mb0">';
			$html .= $proc_info;
			$html .= '</table>';
			$html .= '</td>';

		}
		$html .= '</tr>';


		$html .= '</tbody>';
		$html .= '</table>';
	}

	//Active List ----------

	//Medication List ----------

	$strOcularHx = $glance->get_ocular_hx_data($glance->patient_id);
	$strMedicalHx = $glance->get_medical_hx_data($glance->patient_id);
	$test_medication_data = $glance->get_test_medications_data($glance->patient_id);
	$strAllergies=$glance->get_allergies_data($glance->patient_id);
	$strSurgeries= $glance->get_surgeries_data($glance->patient_id);

	$elem_commentsta=$glance->get_pt_diag_comm($glance->patient_id);

	//Med Hx
	$cols = 0;
	if(!empty($strOcularHx)||!empty($test_medication_data['strOcuMedication'])||!empty($strSurgeries)||
		 !empty($strAllergies)|| !empty($strMedicalHx)|| !empty($test_medication_data['strGenMedication'])){

			if(!empty($strOcularHx) ) $cols++;
			if(!empty($test_medication_data['strOcuMedication']) )  $cols++;
			if(!empty($strSurgeries) ) $cols++;
			if(!empty($strAllergies) ) $cols++;
			if(!empty($strMedicalHx))  $cols++;
			if(!empty($test_medication_data['strGenMedication']) ) $cols++;

			$width2 = '';
			if( $cols > 4) { $width = (70/$cols).'%'; $width2 = '15%'; }
			else $width = (100/$cols).'%';



				$html .= '<table class="table table-bordered">';
				$html .= '<thead class=""><tr><th colspan="6" class="purple_bar text-center">MEDICAL HISTORY</th></tr></thead>';
				$html .= '<tr class="grythead" >';

				if(!empty($strOcularHx))
					$html .= '<td style="width:'.($width2?$width2:$width).'">Ocular Hx.</th>';

				if(!empty($test_medication_data['strOcuMedication']))
					$html .= '<th style="width:'.$width.'">Ocular Medi.</th>';

				if(!empty($strSurgeries))
					$html .= '<th style="width:'.$width.'">Ocular Surgeries</th>';

				if(!empty($strAllergies))
					$html .= '<th style="width:'.$width.'">Allergies-Reactions</th>';

				if(!empty($strMedicalHx))
					$html .= '<th style="width:'.($width2?$width2:$width).'">Medical Hx.</th>';

				if(!empty($test_medication_data['strGenMedication']))
					$html .= '<th style="width:'.$width.'">General Medi.</th>';
				$html .= '</tr>';

				$html .= '<tr>';
				if(!empty($strOcularHx))
					$html .= '<td id="redBold" style="vertical-align:top!important;" >'.$strOcularHx.'</td>';

				if(!empty($test_medication_data['strOcuMedication']))
					$html .= '<td id="orangeBold" style="vertical-align:top!important;" >'.$test_medication_data['strOcuMedication'].'</td>';

				if(!empty($strSurgeries))
					$html .= '<td id="orangeBold" style="vertical-align:top!important;">'.$strSurgeries.'</td>';

				if(!empty($strAllergies))
					$html .= '<td id="orangeBold" style="vertical-align:top!important;">'.$strAllergies.'</td>';

				if(!empty($strMedicalHx))
					$html .= '<td id="redBold" style="vertical-align:top!important;">'.$strMedicalHx.'</td>';

				if(!empty($test_medication_data['strGenMedication']))
					$html .= '<td id="orangeBold" style="vertical-align:top!important;">'.$test_medication_data['strGenMedication'].'</td>';

				$html .= '</tr>';
				$html .= '</table>';
			}
	//Comm.
	if(!empty($elem_commentsta)&&$elem_commentsta!="Comments:")
		$html .= '<table class="table"><tr><td class="bg-info"><b>Comments: </b>'.$elem_commentsta.'</td></tr></table>';


	$st_index = $glance->st_index;
	$proc_table = $glance->get_pt_diagnostic($glance->patient_id,$pdg_showpop,0,$st_index,'popup',true);

	//$paging = '<table class="table"><tr><td class="bg-success" id="pgd_paging">';
	$paging .= '<div class="row">';
	$paging .= '<div class="col-sm-4">';
	$paging .= '<h4><small>'.$glance->str_dig_info.'</small></h4>';
	$paging .= '</div>';
	/*$paging .= '<div class="col-sm-3">';
	$paging .= '<div class="input-group pt5">';
	$paging .= '<span class="input-group-addon">Record(s) per page</span>';
	$paging .= '<select id="el_shw_rec" name="el_shw_rec" onchange="set_shw_rec()" class="minimal form-control">';

	$a_opts=array("10","20","30","40","60","80","100");
	foreach($a_opts as $k => $v){
		$sel = ($glance->el_shw_rec == $v) ? " selected " : "" ;
		$paging .= '<option value="'.$v.'" '.$sel.' >'.$v.'</option>';
	}
	$paging .= '</select>';
	$paging .= '</div>';
	$paging .= '</div>';*/

	$paging .= '<div class="col-sm-8 text-left" id="paging_links">';
	$paging .= $glance->paging_links;
	$paging .= '</div>';
	$paging .= '</div>';
	//$paging .= '</td></tr></table>';

	$html .= $proc_table;


	$data = '
		<div class="modal-dialog modal-lg" style="width:100%;margin:0;" onmouseenter="show_pgd();" onmouseleave="$(\'#pgd_showpop\').hide();">
			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header bg-primary">
					<button type="button" class="close" onClick="$(\'#pgd_showpop\').hide();">&times;</button>
					<h4 class="modal-title">Patient At Glance <span id="pt_title" style="color:white; position:absolute; right:20%;">'.$patient_data['strPtinfo'].' '.($patient_data['patientNickName'] ? '('.$patient_data['patientNickName'].')':'').($patient_data['patientPhoneticName'] ? '<span style="margin-left:20px;"> [&nbsp;'.$patient_data['patientPhoneticName'].'&nbsp;]</span>':'').($patient_data['heard_abt_us_str'] ? '<span style="margin-left:20px;"> Heard About Us: <h5 class="inline">'.$patient_data['heard_abt_us_str'].'</h5></span>':'').'</center></h4>
				</div>
				<div class="modal-body pd0" style="min-height:450px;max-height:450px;overflow:hidden; overflow-y:auto;">
					'.$html.'
				</div>
				<div id="module_buttons" class="ad_modal_footer modal-footer">
					<div class="pull-left col-xs-6" id="pgd_paging">'.$paging.'</div>
					<button type="button" class="btn btn-danger"  onClick="$(\'#pgd_showpop\').hide();">Close</button>
				</div>
			</div>
		</div>
	';

	//return array('pt_info'=>$patient_data['strPtinfo'],'data'=>$html,'paging'=>$paging);
	return array('data'=>$data);
}


function createSocialFldForm($socialData = array() ){

	$show_smoke_arr = $show_code_arr = array();

	$query = "select * from smoking_status_tbl where status='0'";
	$sql = imw_query($query);
	$cnt = imw_num_rows($sql);
	while($row = imw_fetch_assoc($sql))
	{
		$show_smoke= ucfirst($row['desc']);
		$show_code_arr[$row['id']]= $row['code'];
		$show_smoke_arr[$row['id']]= $show_smoke;
	}


	$smoke_opt="<option value=''></option>";
	foreach($show_smoke_arr as $smoke_key=>$smoke_val){
		$sel="";
		if($smoke_key==$socialData['smoking_status_id']){  $sel="selected";}
		$smoke_opt.= "<option value='".$smoke_key."' $sel >".$smoke_val."</option>";
	}


	$type_disabled="";
	if(	$socialData["smoking_status"]=="Never Smoked" || $socialData["smoking_status"]=="Never smoker" )
	{
		$type_disabled = "disabled";
	}


	$arrOptions = array("Cigars", "Cigarettes", "Tobacco");
	sort($arrOptions);
	array_push($arrOptions,"Other");
	$smoke_src = '<option value=""></option>';
	foreach($arrOptions as $source)
	{
		$selected =  ($socialData["source_of_smoke"] == $source) ? 'selected' : '';
  	$smoke_src .= '<option value="'.$source.'" '.$selected.'>'.$source.'</option>';
	}

	$arrOptionsYearMonth = array("Years", "Months");
	$smoke_yrmonth = '';
	foreach($arrOptionsYearMonth as $v)
	{
		$selected= $socialData["smoke_years_months"] == $v ? 'selected' : '';
		$smoke_yrmonth .= '<option value="'.$v.'" '.$selected.'>'.ucfirst($v).'</option>';
	}

	$cessationType = '';
	$arrSmoke = array("","Advised patient to Quit","Discussed Smoking and Tobacco Use Cessation Medications","Discussed Smoking and Tobacco Use Cessation Strategies","Other");
	foreach ($arrSmoke as $s)
	{
		$sel = ($s == $socialData["cessation_counselling_option"]) ? 'selected="selected"' : '';
		$cessationType .= '<option value="'.$s.'" '.$sel.'>'.ucfirst($s).'</option>';
	}

	$intervnOpt = '';
	$arrInterVn = array("","Medical Reason","Patient Reason");
	foreach ($arrInterVn as $s)
	{
		$selected = ($s == $socialData["intervention_reason_option"]) ? 'selected="selected"' : '';
		$intervnOpt .= '<option value="'.$s.'" '.$selected.'>'.ucfirst($s).'</option>';
	}

	$medOrderOpt = '';
	foreach ($arrInterVn as $s)
	{
		$selected = ($s == $socialData["med_order_reason_option"]) ? 'selected="selected"' : '';
		$medOrderOpt .= '<option value="'.$s.'" '.$selected.'>'.ucfirst($s).'</option>';
	}

	$dateOfferedCessationCounselling = get_date_format($socialData["offered_cessation_counselling_date"]);
	if(get_number($dateOfferedCessationCounselling) == '00000000')
	{
		$dateOfferedCessationCounselling = '';
	}
	$smoke_start_date = get_date_format($socialData["smoke_start_date"]);
	if(get_number($smoke_start_date) == '00000000')
	{
		$smoke_start_date = '';
	}
	$smoke_end_date = get_date_format($socialData["smoke_end_date"]);
	if(get_number($smoke_end_date) == '00000000')
	{
		$smoke_end_date = '';
	}

	$script = '';

	$script .= '
	<script type="text/javascript">
	var show_code_arr = '.json_encode($show_code_arr).';
	function set_smoke_code(id){
		if(typeof(show_code_arr[id])=="undefined"){
			document.getElementById("smoking_code").value="";
		}else{
			document.getElementById("smoking_code").value=show_code_arr[id];
		}
	}
	function switchcontrolsMode(obj,mode){
		if(obj){
			otype = obj.type;
			if(mode && obj){
				if(otype=="checkbox" || otype=="radio")
					obj.checked=false;
				else if(otype=="text" || otype=="textarea")
					obj.value="";
				else if(otype=="select-one" || otype=="select-multiple")
					obj.selectedIndex=0;
			}
			if(obj.otype != "undefined"){
				obj.disabled = mode;
				if(otype=="select-multiple")
					$("select").selectpicker("refresh");

			}
		}
	}

	function controlsMode(currentObj){
		switch(currentObj.name){
			case "SmokingStatus":
				var arr_Controls = new Array("source_of_smoke", "imgBackSmokingSource", "source_of_smoke_other", "smoke_perday", "number_of_years_with_smoke","smoke_years_months", "offered_cessation_counseling", "txtDateOfferedCessationCounselling", "cessationCounselling");
				for(i=0; i<arr_Controls.length; i++){
					obj = dgi(arr_Controls[i]);
					if(currentObj.value=="" || currentObj.value=="4 - Never smoked"){
						switchcontrolsMode(obj,true);
					}else{
						switchcontrolsMode(obj,false);
					}
				}
				break;
			case "alcohal":
				var arr_Controls = new Array("source_of_alcohal_other", "imgBackAlcohalSource", "alcohal_quentity", "alcohal_time", "list_drugs", "elem_otherSocial");
				for(i=0; i<arr_Controls.length; i++){
					obj = dgi(arr_Controls[i]);
					if(currentObj.value==""){
						switchcontrolsMode(obj,true);
					}else{
						switchcontrolsMode(obj,false);
					}
				}
				break;
			case "radio_family_smoke":
				var arr_Controls = new Array("smokers_in_relatives", "smoke_description");
				for(i=0; i<arr_Controls.length; i++){
					obj = dgi(arr_Controls[i]);
					if(currentObj.value=="1" && dgi("family_smoke_yes").checked){
						switchcontrolsMode(obj,false);
					}else{
						switchcontrolsMode(obj,true);
					}
				}
				break;
			case "offered_cessation_counseling":
				var arr_Controls = new Array("txtDateOfferedCessationCounselling", "cessationCounselling");
				for(i=0; i<arr_Controls.length; i++){
					obj = dgi(arr_Controls[i]);
					if(currentObj.checked){
						switchcontrolsMode(obj,false);
					}else{
						switchcontrolsMode(obj,true);
					}
				}
				break;
		}
	}

	function show_hide(show_obj,hide_obj,_this)
	{
		//show elements
		var status = false;
		if(			(typeof _this === "object" && _this.value.indexOf("Other") != "-1")
				||	typeof _this === "undefined" )
			status = true;

		if(typeof show_obj ==="object") {
			$.each(show_obj,function(i,elem){
				if(status) $("#"+elem).removeClass("hidden");
			});
		}
		else {
			if(status) $("#"+show_obj).removeClass("hidden");
		}

		//hide elements
		if(typeof hide_obj ==="object") {
				$.each(hide_obj,function(i,elem){
					if(status) $("#"+elem).addClass("hidden");
				});
		}
		else {
			if(status) $("#"+hide_obj).addClass("hidden");
		}

	}
	$(function() {
		$("body").on("click",".back_other",function(){
			var t = $(this).data("tab-name");
			$("#div_"+t).removeClass("hidden");
			$("#other_"+t).addClass("hidden");
			if( $("#" + t )[0].type =="select-multiple" ) {
				$("#" + t ).selectpicker("val","");
			}
			else {
				$("#" + t ).val("");
			}
		});
		$("#cessationCounselling").trigger("change");
		$("#this_blood_sugar_time,#source_of_smoke").trigger("change");
		$("select.selectpicker_new").each(function(i,elem){
			var id = $(elem).attr("id");
			var val = $(elem).val();
			if(val == "Other")
			{
				$("#div_"+id).addClass("hidden");
				$("#other_"+id).removeClass("hidden");
			}
		});

	});
	</script>';

	$html = '';

	$html .= $script.'
	<div class="col-sm-12 "><form id="genHlthSocialForm">
		<input type="hidden" name="elem_formAction" value="saveGHSocialData" />
		<div class="socialbox"><div class="row">';

		$html .= '<div class="col-xs-4 mb5">
								<div class="row">
									<!-- Smoke Type -->
									<div class="col-xs-7">
										<label>Smoke</label>
										<select name="SmokingStatus" id="SmokingStatus" class="form-control minimal" title="'.imw_msg('drop_sel').'" onChange="set_smoke_code(this.value);controlsMode(this);" data-prev="'.$socialData['smoking_status_id'].'" >'.$smoke_opt.'</select>
									</div>

									<!-- SMOKE SNOMED CODES -->
									<div class="col-xs-5">
										<label>Snomed Code</label>
										<input type="text" class="form-control" id="smoking_code" name="smoking_code" data-prev="'.$show_code_arr[$socialData['smoking_status_id']].'" readonly value="'.$show_code_arr[$socialData['smoking_status_id']].'">
									</div>

								</div>
							</div>';

		$html .= '<div class="col-xs-1 mb5">
								<label>Type</label>
								<div id="div_source_of_smoke">
									<select class="form-control minimal" name="source_of_smoke" id="source_of_smoke" data-width="100%" title="'.imw_msg('drop_sel').'" '.$type_disabled.' onChange="show_hide(\'other_source_of_smoke\', \'div_source_of_smoke\', this);" data-prev="'.$socialData["source_of_smoke"].'">'.$smoke_src.'</select>
								</div>

								<div id="other_source_of_smoke" class="hidden">
									<div class="input-group">
										<input type="text" class="form-control" id="source_of_smoke_other" name="source_of_smoke_other" '.$type_disabled.' data-prev="'.$socialData["source_of_smoke_other"].'" value="'.$socialData["source_of_smoke_other"].'" />
										<label class="input-group-addon btn back_other" data-tab-name="source_of_smoke"><i class="glyphicon glyphicon-arrow-left"></i></label>
									</div>
								</div>
							</div>';

		$html .= '<div class="col-xs-1 mb5">
								<label>Frequency</label>
								<input type="text" name="smoke_perday" id="smoke_perday" '.$type_disabled.' value="'.$socialData["smoke_perday"].'" data-prev="'.$socialData["smoke_perday"].'" class="form-control" />
							</div>';

		$html .= '<div class="col-xs-1 mb5">
								<label>For</label>
								<input type="text" name="number_of_years_with_smoke" id="number_of_years_with_smoke" '.$type_disabled.' data-prev="'.$socialData["number_of_years_with_smoke"].'" value="'.$socialData["number_of_years_with_smoke"].'" class="form-control" />
							</div>';

		$html .= '<div class="col-xs-1 mb5">
								<label>Period</label>
								<select name="smoke_years_months" id="smoke_years_months" class="form-control minimal" data-width="100%" '.$type_disabled.' data-prev="'.$socialData["smoke_years_months"].'" >'.$smoke_yrmonth.'</select>
							</div>';


		//$html .= '<div class="clearfix"></div>';

		$html .= '<div class="col-sm-2">
								<label for="smoke_start_date">Start Date</label>
								<div class="input-group">
									<input type="text" name="smoke_start_date" id="smoke_start_date" data-prev="'.$smoke_start_date.'" value="'.($smoke_start_date ? $smoke_start_date : '').'" title="'.$GLOBALS['date_format'].'" class="datepicker form-control" />
									<label class="input-group-addon btn" for="smoke_start_date"><i class="glyphicon glyphicon-calendar"></i></label>
								</div>
							</div>';

		$html .= '<div class="col-sm-2">
								<label for="smoke_end_date">End Date</label>
								<div class="input-group">
									<input type="text" name="smoke_end_date" id="smoke_end_date" data-prev="'.$smoke_end_date.'" value="'.($smoke_end_date ? $smoke_end_date : '').'" title="'.$GLOBALS['date_format'].'" class="datepicker form-control" />
									<label class="input-group-addon btn" for="smoke_end_date"><i class="glyphicon glyphicon-calendar"></i></label>
								</div>
							</div>';

		$html .= '<div class="clearfix"></div>';

		$html .= '<div class="col-xs-8 mb5">
								<div class="row">';

				$html .= '<div class="col-xs-7 col-sm-6 col-md-7"><br>
										<div class="checkbox">
											<input type="checkbox" '.$type_disabled.' '.($socialData["smoke_counseling"]==1?"checked":"").' data-prev="'.($socialData["smoke_counseling"]==1?"checked":"").'" name="offered_cessation_counseling" id="offered_cessation_counseling" value="1" onChange="if(this.checked){dgi(\'txtDateOfferedCessationCounselling\').click();}else{dgi(\'txtDateOfferedCessationCounselling\').value=\'\';} controlsMode(this);" />
											<label for="offered_cessation_counseling">Cessation Counseling</label>
										</div>
									</div>';

				$html .= '<div class="col-xs-5 col-sm-6 col-md-5 ">
										<label for="txtDateOfferedCessationCounselling">Counseling Date</label>
										<div class="input-group">
											<input type="text" name="txtDateOfferedCessationCounselling" id="txtDateOfferedCessationCounselling" data-prev="'.$dateOfferedCessationCounselling.'" value="'.($dateOfferedCessationCounselling?$dateOfferedCessationCounselling:"").'" title="'.$GLOBALS['date_format'].'" class="datepicker form-control" />
											<label class="input-group-addon btn" for="txtDateOfferedCessationCounselling"><i class="glyphicon glyphicon-calendar"></i></label>
										</div>
									</div>';

		$html .= '	</div>
							</div>';

		$html .= '<div class="col-xs-4  mb5">
								<label for="cessationCounselling">Type</label>
								<div id="div_cessationCounselling">
									<select name="cessationCounselling" id="cessationCounselling" class="form-control minimal" '.$type_disabled.' onChange="show_hide(\'other_cessationCounselling\',\'div_cessationCounselling\',this);" data-prev="'.$socialData["cessation_counselling_option"].'" data-width="100%" title="'.imw_msg('drop_sel').'">'.$cessationType.'</select>
								</div>

								<div id="other_cessationCounselling" class="hidden">
									<div class="input-group">
										<input type="text" class="form-control" id="cessationCounsellingOther" name="cessationCounsellingOther" '.$type_disabled.' data-prev="'.$socialData["cessation_counselling_other"].'" value="'.$socialData["cessation_counselling_other"].'" />
										<label class="input-group-addon btn back_other" data-tab-name="cessationCounselling">
											<i class="glyphicon glyphicon-arrow-left"></i>
										</label>
									</div>
								</div>

							</div>';

		$html .= '<div class="clearfix"></div>';


		$html .= '<!-- Intervention not done for Tobacco Use Cessation Counseling due to -->
							<div class="col-xs-12 mb5">
								<div class="row">';

		$html .= '<div class="col-xs-8">
								<div class="checkbox">
									<input type="checkbox" '.($socialData["intervention_not_performed_status"]=='Yes'?"checked":"").' data-prev="'.($socialData["intervention_not_performed_status"]=='Yes'?"checked":"").'" name="interventionNotPerformedStatus" id="interventionNotPerformedStatus" value="Yes" />
									<label for="interventionNotPerformedStatus">Intervention not done for Tobacco Use Cessation Counseling due to</label>
								</div>
							</div>

							<div class="col-xs-4">
								<select name="interventionReason" id="interventionReason" class="form-control minimal" data-width="100%" title="'.imw_msg('drop_sel').'" data-prev="'.$socialData["intervention_reason_option"].'">'.$intervnOpt.'</select>
							</div>';

		$html .= '	</div>
							</div>';

		$html .= '<div class="clearfix"></div>';

		$html .= '<!-- Medication order not done for Tobacco Use Cessation due to -->';
		$html .= '<div class="col-xs-12 mb5">
								<div class="row">';

		$html .= '<div class="col-xs-8">
								<div class="checkbox">
									<input type="checkbox" '.($socialData["med_order_not_performed_status"]=='Yes'?"checked":"").' data-prev="'.($socialData["med_order_not_performed_status"]=='Yes'?"checked":"").'" name="medOrderNotPerformedStatus" id="medOrderNotPerformedStatus" value="Yes" />
									<label for="medOrderNotPerformedStatus">Medication order not done for Tobacco Use Cessation due to</label>
								</div>
							</div>

							<div class="col-xs-4">
								<select name="medOrderReason" id="medOrderReason" class="form-control minimal" data-width="100%" title="'.imw_msg('drop_sel').'" data-prev="'.$socialData["med_order_reason_option"].'">'.$medOrderOpt.'</select>
							</div>';

		$html .= '	</div>
							</div>';


		$html .= '		</div>
								</div>';

		$html .= '<div class="alchbox"><div class="row">';

		$html .= '<div class="col-xs-12">
								<div class="row head">
									<span>Alcohol</span>
								</div>
     					</div>';

		$html .= '<div class="clearfix"></div>';

		$html .= '<div class="col-xs-12 col-sm-4">
      					<div class="row">';


    $html .= '<!-- Alcohol -->
								<div class="col-xs-12">
									<label for="alcohal">Alcohol</label>
									<div id="div_alcohal">
										<select class="selectpicker " data-width="100%" name="alcohal[]" id="alcohal" onChange="controlsMode(this);" multiple title="'.imw_msg('drop_sel').'" data-prev="'.$socialData["alcohal"].'">';
										$arrAlcoholOptions = array("All","Never","Beer", "Spirits", "Wine", "Former Drinker", "Other");
										$counter = 0;
										$chkArr = explode(",",$socialData["alcohal"]);
										foreach($arrAlcoholOptions as $aol ){ $counter++;
											$sel = in_array($aol,$chkArr) ? 'selected' : '';
											$html .= '<option value="'.$aol.'" '.$sel.'>'.$aol.'</option>';
										}

		$html .= '			</select>
									</div>
									<div id="other_alcohal" class="hidden">
										<div class="input-group">
											<input type="text" class="form-control" id="source_of_alcohal_other" name="source_of_alcohal_other" data-prev="'.$socialData["source_of_alcohal_other"].'" value="'.$socialData["source_of_alcohal_other"].'" >
											<label class="input-group-addon btn back_other" data-tab-name="alcohal">
												<i class="glyphicon glyphicon-arrow-left"></i>
											</label>
										</div>
									</div>
          			</div>';


		$html .= '<!-- Frequency -->
							<div class="col-xs-12">
								<label for="alcohal_quentity">Frequency</label>
								<div class="row">
									<div class="col-xs-4">
										<input type="text" class="form-control" id="alcohal_quentity" name="alcohal_quentity" data-prev="'.$socialData["consumption"].'" value="'.$socialData["consumption"].'" />
									</div>
									<div class="col-xs-8">
										<select name="alcohal_time" id="alcohal_time" class="form-control minimal" data-width="100%" title="'.imw_msg('drop_sel').'" data-prev="'.$socialData["alcohal_time"].'">
										<option value=""></option>';
										$arrFrequencyOptions = array("Per Day", "Per Week", "Occasionally");
										$counter = 0;
										foreach($arrFrequencyOptions as $afl ){ $counter++;
											$sel = ($socialData["alcohal_time"] == $afl) ? 'selected' : '';
											$html .= '<option value="'.$afl.'" '.$sel.'>'.$afl.'</option>';
										}

		$html .= '			</select>
									</div>
								</div>
          		</div>';


    $html .= '  	</div>
      					</div>';


		$html .= '<!-- List Any Drugs -->
							<div class="col-xs-6 col-sm-4">
								<label for="list_drugs">List any Drugs</label>
								<textarea name="list_drugs" id="list_drugs" data-prev="'.$socialData["list_drugs"].'" class="form-control" style="height:76px!important;">'.$socialData["list_drugs"].'</textarea>
							</div>

							<!-- More Information -->
							<div class="col-xs-6 col-sm-4">
								<label for="elem_otherSocial">More Information</label>
								<textarea name="elem_otherSocial" id="elem_otherSocial" rows="3" cols="30" class="form-control" data-prev="'.$socialData['otherSocial'].'" style="height:76px!important;">'.$socialData['otherSocial'].'</textarea>
							</div>';

		$html .= '</div> </div>';

		$html .= '</form></div>';

		return $html;
}

function saveGHSocialData($pid){
	extract($_POST);
	include $GLOBALS['incdir'].'/Medical_history/general_health/inc_social_save.php';
	$success = ($socialHistoryError) ? false : true;

	$return = array( 'success'=>$success, 'error'=>$socialHistoryError, 'data' => general_health_div($pid,'socialData') );
	return $return;
}
?>
