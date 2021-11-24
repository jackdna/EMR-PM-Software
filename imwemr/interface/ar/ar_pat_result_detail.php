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
	PURPOSE : AR RESULT PREPRATION
	ACCESS TYPE : INDIRECT
*/
	$detail_column_show_total=$headerColspan=$colspanDiff=$ar_cols_html=0;
	$ar_cols_html=$ar_cols;
	if($detail_column_html)
	{
		$detail_column_show_total=4+($ar_cols_html+1);
		$headerColspan=round(abs($detail_column_show_total/3));
		$colspanDiff=($detail_column_show_total-($headerColspan*3));
	}
	
	$available_width_per=50;
	$each_col_wd_per=$available_width_per;
	if($ar_cols){
		$each_col_wd_per=$available_width_per/$ar_cols;
	}

	$ins_key=$_POST['detail_ins_id'];
	if($group_by=="Facility"){
		$final_ar_data_arr=$ar_data_arr[0][$ins_key][0];
		$final_ins_chld_res_arr=$ins_chld_res_arr[0][$ins_key][0];
		$final_ins_pat_res_arr=$ins_pat_res_arr[0][$ins_key][0];
		$show_arr_name=$fac_comp_arr[$ins_key];
		$show_tel=$show_fax="";
	}else if($group_by=="POS Facility"){
		$final_ar_data_arr=$ar_data_arr[0][$ins_key][0];
		$final_ins_chld_res_arr=$ins_chld_res_arr[0][$ins_key][0];
		$final_ins_pat_res_arr=$ins_pat_res_arr[0][$ins_key][0];
		$show_arr_name=$fac_comp_arr[$ins_key];
		$show_tel=$show_fax="";
	}else if($group_by=="Provider"){
		$final_ar_data_arr=$ar_data_arr[0][0][$ins_key];
		$final_ins_chld_res_arr=$ins_chld_res_arr[0][0][$ins_key];
		$final_ins_pat_res_arr=$ins_pat_res_arr[0][0][$ins_key];
		$show_arr_name=$prov_comp_arr[$ins_key]['name'];
		$show_tel=$show_fax="";
	}else{
		$final_ar_data_arr=$ar_data_arr[$ins_key][0][0];
		$final_ins_chld_res_arr=$ins_chld_res_arr[$ins_key][0][0];
		$final_ins_pat_res_arr=$ins_pat_res_arr[$ins_key][0][0];
		$show_arr_name=$insCompArr[$ins_key]['in_house_code'].' - '.$insCompArr[$ins_key]['name'];
		$show_tel="Fax: ";
		$show_fax="Tel. ";
	}

	$ar_ins_total_due=$ins_sub_due=array();
	$ff=0;

	if(count($final_ar_data_arr)>0){
		$ins_sub_due_chk=array();
		foreach($ins_priority_arr as $ins_priority_key => $ins_priority_val){
			foreach($final_ar_data_arr[$ins_priority_key] as $ar_data_key => $ar_data_val){
				$ar_sub_data=$final_ar_data_arr[$ins_priority_key][$ar_data_key];
				for($a=$aging_start;$a<=$aging_to;$a++){
					$start = $a;
					$a = $a > 0 ? $a - 1 : $a;
					$end = ($a) + $aggingCycle;
					$ins_sub_due_chk[$ins_key][]=array_sum($final_ins_chld_res_arr[$ar_sub_data['charge_list_detail_id']][$start]);
					$a += $aggingCycle;
				}
			}
		}
		if((array_sum($ins_sub_due_chk[$ins_key])>=$_POST['balance_from'] || $_POST['balance_from']<=0) && (array_sum($ins_sub_due_chk[$ins_key])<=$_POST['balance_to'] || $_POST['balance_to']<=0)){

			if($ff==0){
				
				$html_body ='<table id="ar_summ_tbl" class="rpt_table_det rpt rpt_table-bordered table-hover" style="width:100%">'; //style="width:100%"
				$pdf_body ='<table cellpadding="1" cellspacing="1" bgcolor="#FFF3E8" border="0" style="width:'.$printPdfWidth.'px">';
			}

			$ff++;
			#HTML
			$html_body.='<thead><tr><td class="text_b_w txt_l" colspan="'.$headerColspan.'">'.$group_by.': '.$show_arr_name.'</td>';
			$html_body.='<td class="text_b_w txt_l" colspan="'.$headerColspan.'">'.$show_tel. core_phone_format($insCompArr[$ins_key]['phone']).'</td>';
			$html_body.='<td class="text_b_w txt_l" colspan="'.($headerColspan+$colspanDiff).'">'.$show_fax. core_phone_format($insCompArr[$ins_key]['fax']).'</td>
			</tr>';
			#PDF
			$pdf_body.='<tr><td class="text_b_w txt_l" colspan="3">'.$group_by.': '.$show_arr_name.'</td>';
			$pdf_body.='<td class="text_b_w txt_l" colspan="4">'.$show_tel. core_phone_format($insCompArr[$ins_key]['phone']).'</td>';
			$pdf_body.='<td class="text_b_w txt_l" colspan="'.(4+$total_col_diff).'">'.$show_fax. core_phone_format($insCompArr[$ins_key]['fax']).'</td>
			</tr>';
			#CSV
			$csv_data.=$group_by.":".$pfx.$show_arr_name.$pfx;
			if($show_fax!=""){
				$csv_data.="Tel.".$pfx.core_phone_format($insCompArr[$ins_key]['phone']).$pfx;
				$csv_data.="Fax:".$pfx.core_phone_format($insCompArr[$ins_key]['fax']).$pfx."\n";
				$ins_type_head="Ins. Type";
			}else{
				$csv_data.="\n";
				$ins_type_head="Pri Ins.";
			}
			
			foreach($ins_priority_arr as $ins_priority_key => $ins_priority_val){
				foreach($final_ar_data_arr[$ins_priority_key] as $ar_data_key => $ar_data_val){
					$ar_pat_data=$final_ar_data_arr[$ins_priority_key][$ar_data_key];
					$pat_id=$ar_pat_data['patient_id'];
					$charge_list_id=$ar_pat_data['charge_list_id'];
					$ar_pat_data_arr[$pat_id]['patient_id']=$pat_id;
					$ar_pat_data_arr[$pat_id]['name']=ucwords(trim($ar_pat_data['lname'].", ".$ar_pat_data['fname']." ".$ar_pat_data['mname']));
					$ar_pat_data_arr[$pat_id]['DOB']=$ar_pat_data['DOB'];
					$ar_pat_data_arr[$pat_id]['totalAmount'][]=$ar_pat_data['totalAmount'];
					$ar_pat_data_arr[$pat_id]['chld'][]=$ar_pat_data['charge_list_detail_id'];
					$ar_pat_data_arr[$pat_id]['enc'][$ar_pat_data['charge_list_detail_id']]=$ar_pat_data['encounter_id'];
					$ar_pat_data_arr[$pat_id]['chld_det'][$ar_pat_data['charge_list_detail_id']]=$ar_pat_data;
					$err_msg="";
					
					for($a=$aging_start;$a<=$aging_to;$a++){
						$start = $a;
						$a = $a > 0 ? $a - 1 : $a;
						$end = ($a) + $aggingCycle;
						$a += $aggingCycle;
						$ins_ar_aging_arr[$pat_id]['aging_'.$start]=array_sum($final_ins_pat_res_arr[$pat_id][$start]);
						$ins_ar_aging_arr[$pat_id][]=array_sum($final_ins_pat_res_arr[$pat_id][$start]);
						$final_ins_chld_res_arr_chk[$ar_pat_data['charge_list_detail_id']][]=array_sum($final_ins_chld_res_arr[$ar_pat_data['charge_list_detail_id']][$start]);
					}
				}
			}
			
			foreach($ar_pat_data_arr as $ar_pat_key => $ar_pat_val){
				if($_POST['ord_by_field']=="pat_charge_ord"){
					$ord_ar_pat_data_arr[$ar_pat_key]=array_sum($ar_pat_data_arr[$ar_pat_key]['totalAmount']);
				}else if($_POST['ord_by_field']=="pat_balance_ord"){
					$ord_ar_pat_data_arr[$ar_pat_key]=array_sum($ins_ar_aging_arr[$ar_pat_key]);
				}else if(strstr($_POST['ord_by_field'],"aging_0")){
					$ord_ar_pat_data_arr[$ar_pat_key]=$ins_ar_aging_arr[$ar_pat_key]['aging_0'];
				}else if(strstr($_POST['ord_by_field'],"aging_31")){
					$ord_ar_pat_data_arr[$ar_pat_key]=$ins_ar_aging_arr[$ar_pat_key]['aging_31'];
				}else if(strstr($_POST['ord_by_field'],"aging_61")){
					$ord_ar_pat_data_arr[$ar_pat_key]=$ins_ar_aging_arr[$ar_pat_key]['aging_61'];
				}else if(strstr($_POST['ord_by_field'],"aging_91")){
					$ord_ar_pat_data_arr[$ar_pat_key]=$ins_ar_aging_arr[$ar_pat_key]['aging_91'];
				}else if(strstr($_POST['ord_by_field'],"aging_121")){
					$ord_ar_pat_data_arr[$ar_pat_key]=$ins_ar_aging_arr[$ar_pat_key]['aging_121'];
				}else if(strstr($_POST['ord_by_field'],"aging_151")){
					$ord_ar_pat_data_arr[$ar_pat_key]=$ins_ar_aging_arr[$ar_pat_key]['aging_151'];
				}else if(strstr($_POST['ord_by_field'],"aging_181")){
					$ord_ar_pat_data_arr[$ar_pat_key]=$ins_ar_aging_arr[$ar_pat_key]['aging_181'];
				}else{
					$ord_ar_pat_data_arr[$ar_pat_key]=$ar_pat_key;
				}
			}
			$show_ord_sign=array();
			if($_POST['ord_by_field']!=""){
				if($_POST['ord_by_ascdesc']=="DESC"){
					$show_ord_sign[$_POST['ord_by_field']]='<span class="glyphicon glyphicon-chevron-down pull-right"></span>';
					if($_POST['ord_by_field']=="pat_charge_ord" || strstr($_POST['ord_by_field'],"aging_") || $_POST['ord_by_field']=="pat_balance_ord"){
						arsort($ord_ar_pat_data_arr);
					}
				}else{
					$show_ord_sign[$_POST['ord_by_field']]='<span class="glyphicon glyphicon-chevron-up pull-right"></span>';
					if($_POST['ord_by_field']=="pat_charge_ord" || strstr($_POST['ord_by_field'],"aging_") || $_POST['ord_by_field']=="pat_balance_ord"){
						asort($ord_ar_pat_data_arr);
					}
				}
			}
			
			
			$html_body.='<tr>';
			$html_body.='<td class="text_b_w txt_c" style="width:1%"><div class="checkbox txt_l"><input type="checkbox" name="chkbx_all" id="chkbx_all" onClick="return chk_all();"><label for="chkbx_all"></label></div></td>';
			$html_body.='<td class="text_b_w txt_l" onClick="ar_ord_by(\'pat_name_ord\')" style="width:20%">Patient Name - ID'.$show_ord_sign['pat_name_ord'].'</td>';
			$html_body.='<td class="text_b_w txt_c" onClick="ar_ord_by(\'pat_dob_ord\')" style="width:10%">DOB'.$show_ord_sign['pat_dob_ord'].'</td>';
			$html_body.='<td class="text_b_w txt_r" onClick="ar_ord_by(\'pat_charge_ord\')" style="width:10%">Charge'.$show_ord_sign['pat_charge_ord'].'</td>';	
			$html_body.=str_replace('<td ','<td style="text-align:right; width:'.$each_col_wd_per.'%"',$ins_ar_heading_data);	
			$html_body.='<td class="text_b_w txt_r" onClick="ar_ord_by(\'pat_balance_ord\')" style="width:10%;">Balance'.$show_ord_sign['pat_balance_ord'].'</td>';	
			$html_body.='</tr></thead><tbody>';
			
			$pdf_body.='<tr>';
			$pdf_body.='<td class="text_b_w txt_c">Patient Name - ID</td>';
			$pdf_body.='<td class="text_b_w txt_c">DOB</td>';
			$pdf_body.='<td class="text_b_w txt_c">Charge</td>';
			$pdf_body.=$ins_ar_heading_data;
			$pdf_body.='<td class="text_b_w txt_r">Balance</td>';
			$pdf_body.='</tr>';

			$csv_data.="Patient Name - ID". $pfx ."DOB". $pfx ."Charge". $pfx . $csv_ins_ar_heading_data."Balance \n";
			
			$old_patient_id="";
			foreach($ord_ar_pat_data_arr as $ord_ar_pat_key => $ord_ar_pat_val){
				$ar_data=$ar_pat_data_arr[$ord_ar_pat_key];
				$patientName = $ar_data['name'];
				$ins_ar_data=$ins_ar_pdf_data=$csv_ins_ar_data=$val_cfd=$csv_cfd=$val_pd=$csv_pd=$ins_val_cfd=$ins_csv_cfd=$ins_val_pd=$ins_csv_pd="";
				for($a=$aging_start;$a<=$aging_to;$a++){
					$start = $a;
					$a = $a > 0 ? $a - 1 : $a;
					$end = ($a) + $aggingCycle;
					$ins_pat_total_due[$ins_key][$ar_data['patient_id']][]=array_sum($final_ins_pat_res_arr[$ar_data['patient_id']][$start]);
					$tmp_val=0;
					$tmp_val=$CLSReports->numberFormat(array_sum($final_ins_pat_res_arr[$ar_data['patient_id']][$start]),2,1);
					$ins_ar_data .='<td class="text_10 txt_r">'.$tmp_val.'</td>';
					$ins_ar_pdf_data .='<td class="text_10 txt_r" style="width:70px">'.$tmp_val.'</td>';
					$csv_ins_ar_data.="\"".$tmp_val."\"".$pfx;
					$a += $aggingCycle;
					$ar_ins_total_due[$ins_key][$start][]=array_sum($final_ins_pat_res_arr[$ar_data['patient_id']][$start]);
					$ins_sub_due[$ins_key][]=array_sum($final_ins_pat_res_arr[$ar_data['patient_id']][$start]);
				}

				#values to show up in html, pdf and csv
				$val_patient=$csv_patient=$patientName.' - '.$ar_data['patient_id'];
				$val_dob=$csv_dob=get_date_format($ar_data['DOB']);
				$val_charge=$CLSReports->numberFormat(array_sum($ar_data['totalAmount']),2,1);
				$val_Balance=$CLSReports->numberFormat(array_sum($ins_pat_total_due[$ins_key][$ar_data['patient_id']]),2,1);
				//$val_prt_pt_st=$csv_prt_pt_st=$prt_pt_st;
				$chld_id_imp=implode(',',$ar_data['chld']);
				$old_patient_id=$ar_data['patient_id'];
				$ins_total_charges[$ins_key][]=array_sum($ar_data['totalAmount']);
				$yellow_bg="";
				$pat_chk_arr=array_combine($pat_chk_arr,$pat_chk_arr);
				if($pat_chk_arr[$old_patient_id])$yellow_bg=" class=\"yellow_bg\"";
				$enc_class=$old_enc_id=$chld_imp=$enc_imp="";
				$chld_imp=implode(',',$ar_pat_data_arr[$old_patient_id]['chld']);
				$enc_imp=implode(',',$ar_pat_data_arr[$old_patient_id]['enc']);

				#HTML
				$html_body.='<tr class="'.$child_tr_css.'" data-patient="'.$old_patient_id.'">
				<td class="text_10 txt_l"><div class="checkbox">';
				foreach($ar_pat_data_arr[$old_patient_id]['chld'] as $chld_key => $chld_val){
					$html_body.= '<input type="hidden" name="chld_balance['.$chld_val.']" id="chld_balance_'.$chld_val.'" value="'.array_sum($final_ins_chld_res_arr_chk[$chld_val]).'">
					<input type="hidden" name="enc_chld['.$chld_val.']" id="enc_chld_'.$chld_val.'" value="'.$ar_pat_data_arr[$old_patient_id]['enc'][$chld_val].'">
';
				}
				
				$html_body.='<input class="chk_box_css chk_box_'.$old_patient_id.'_child '.$enc_class.'" data-encid="" data-chlid="" type="checkbox" value="'.$chld_imp.'" name="chld_arr[]" id="chld_'.$old_patient_id.'" data-ptid="'.$old_patient_id.'"><label for="chld_'.$old_patient_id.'"></label>
				</div></td>';
				$html_body.='<td class="text_10 txt_l srh_ins pointer text_purple" data-id="'.$ins_key.'" data-patid="'.$old_patient_id.'" data-fax="'.$insCompArr[$ins_key]['fax'].'" data-email="'.$insCompArr[$ins_key]['email'].'" data-name="'.$show_arr_name.'">'.$val_patient;
				$info_msg_detail="";
				$html_body.='</td>';
				$html_body.='<td class="text_10 txt_l" nowrap>'.$val_dob.'</td>';
				$html_body.='<td class="text_10 txt_r">'.$val_charge.'</td>';
				$html_body.=$ins_ar_data;
				$html_body.='<td class="text_10 txt_r">'.$val_Balance.'</td>';
				$html_body.='</tr>';
				#PDF
				$pdf_body.='<tr>';
				$pdf_body.='<td class="text_10 txt_l" style="width:250px">'.$val_patient.'</td>';
				$pdf_body.='<td class="text_10 txt_l" nowrap style="width:50px">'.$val_dob.'</td>';
				$pdf_body.='<td class="text_10 txt_r" style="width:100px">'.$val_charge.'</td>';
				$pdf_body.=$ins_ar_pdf_data;
				$pdf_body.='<td class="text_10 txt_r" style="width:100px">'.$val_Balance.'</td>';
				$pdf_body.='</tr>';
				#CSV
				$csv_data.="\"".$csv_patient."\"".$pfx;
				$csv_data.=$csv_dob. $pfx;
				$csv_data.="\"".$val_charge."\"".$pfx;
				$csv_data.=$csv_ins_ar_data;
				$csv_data.="\"".$val_Balance."\"".$pfx . "\n";
				
			}
			
		}
		
		if(array_sum($ins_total_charges[$ins_key])>0){
			#HTML
			$html_body.='<tr><td class="text_b_w txt_r detail_field_precal" colspan="3"> Total</td>';
			$html_body.='<td class="text_b_w txt_r">'.$CLSReports->numberFormat(array_sum($ins_total_charges[$ins_key]),2,1).'</td>';
			#PDF
			$pdf_body.='<tr>';
			$pdf_body.='<td class="text_b_w txt_r" colspan="2"> Total</td>';
			$pdf_body .='<td class="text_b_w txt_r">'.$CLSReports->numberFormat(array_sum($ins_total_charges[$ins_key]),2,1).'</td>';
			#CSV
			for($i=1;$i<2;$i++)$csv_data.=$pfx;
			$csv_data.="Total".$pfx;
			$csv_data.="\"".$CLSReports->numberFormat(array_sum($ins_total_charges[$ins_key]),2,1)."\"".$pfx;

			for($a=$aging_start;$a<=$aging_to;$a++){
				$start = $a;
				$a = $a > 0 ? $a - 1 : $a;
				$end = ($a) + $aggingCycle;
				$tmp_val=0;
				$tmp_val=$CLSReports->numberFormat(array_sum($ar_ins_total_due[$ins_key][$start]),2,1);
				#HTML
				$html_body .='<td class="text_b_w txt_r">'.$tmp_val.'</td>';
				#PDF
				$pdf_body .='<td class="text_b_w txt_r">'.$tmp_val.'</td>';
				#CSV
				$csv_data.="\"".$tmp_val."\"".$pfx;
				$a += $aggingCycle;
			}
			$tmp_val=0;
			$tmp_val=$CLSReports->numberFormat(array_sum($ins_sub_due[$ins_key]),2,1);
			#HTML
			$html_body .='<td class="text_b_w txt_r">'.$tmp_val.'</td>';
			$html_body .='</tr>';
			#PDF
			
			$pdf_body .='<td class="text_b_w txt_r">'.$tmp_val.'</td>';
			$pdf_body .='</tr>';
			#CSV
			$csv_data.="\"".$tmp_val."\"".$pfx."\n";

		}
		$html_body .= '</tbody></table>';
		$pdf_body .='</table>';
	}

?>
