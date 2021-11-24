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
	if($detail_column_html['Aging'])$ar_cols_html=$ar_cols;
	if($detail_column_html)
	{
		$detail_column_show_total=sizeof($detail_column_html)+($ar_cols_html+1);//+1=checkbox column
		$headerColspan=round(abs($detail_column_show_total/3));
		$colspanDiff=($detail_column_show_total-($headerColspan*3));
	}
	//colspan setting for pdf on behalf of choosen columns
	$total_col_span=$total_col_span_a=$total=0;
	if($detail_column_list['Patient Name - ID']){$total_col_span++;$total_col++;}
	if($detail_column_list['DOB']){$total_col_span++;$total_col++;}
	if($detail_column_list['DOS']){$total_col_span++;$total_col++;}
	if($detail_column_list['Facility']){$total_col_span++;$total_col++;}
	if($detail_column_list['Provider']){$total_col_span++;$total_col++;}
	if($detail_column_list['Ins. Type']){$total_col_span++;$total_col++;}
	if($detail_column_list['Ins. ID']){$total_col_span++;$total_col++;}
	if($detail_column_list['DOC']){$total_col_span++;$total_col++;}
	if($detail_column_list['CPT']){$total_col_span++;$total_col++;}
	if($detail_column_list['ICD10']){$total_col_span++;$total_col++;}
	if($detail_column_list['R']){$total_col_span++;$total_col++;}
	if($detail_column_list['Charge']){$total_col++;}
	if($detail_column_list['Aging']){$total_col=$ar_cols+$total_col;}
	if($detail_column_list['Balance']){$total_col++;}
	if($detail_column_list['CFD']){$total_col++;$total_col_span_a++;}
	if($detail_column_list['PD']){$total_col++;$total_col_span_a++;}
	if($detail_column_list['Prt Pt St']){$total_col_span_a++;$total_col++;}
	if($detail_column_list['1st Claim']){$total_col_span_a++;$total_col++;}
	if($detail_column_list['Note']){$total_col_span_a++;$total_col++;}
	if($detail_column_list['Reminder Date']){$total_col_span_a++;$total_col++;}
	if($detail_column_list['Case Type']){$total_col_span_a++;$total_col++;}
	
	//colspan setting for html on behalf of choosen columns
	$total_col_span_html=$total_col_span_a_html=0;
	if($detail_column_html['Patient Name - ID']){$total_col_span_html++;}
	if($detail_column_html['DOB']){$total_col_span_html++;}
	if($detail_column_html['DOS']){$total_col_span_html++;}
	if($detail_column_html['Facility']){$total_col_span_html++;}
	if($detail_column_html['Provider']){$total_col_span_html++;}
	if($detail_column_html['Ins. Type']){$total_col_span_html++;}
	if($detail_column_html['Ins. ID']){$total_col_span_html++;}
	if($detail_column_html['DOC']){$total_col_span_html++;}
	if($detail_column_html['CPT']){$total_col_span_html++;}
	if($detail_column_html['ICD10']){$total_col_span_html++;}
	if($detail_column_html['R']){$total_col_span_html++;}
	if($detail_column_html['Charge']){}
	if($detail_column_html['Aging']){}
	if($detail_column_html['Balance']){}
	if($detail_column_html['CFD']){$total_col_span_a_html++;}
	if($detail_column_html['PD']){$total_col_span_a_html++;}
	if($detail_column_html['Prt Pt St']){$total_col_span_a_html++;}
	if($detail_column_html['1st Claim']){$total_col_span_a_html++;}
	if($detail_column_html['Note']){$total_col_span_a_html++;}
	if($detail_column_html['Reminder Date']){$total_col_span_a_html++;}
	if($detail_column_html['Case Type']){$total_col_span_a_html++;}
	if($detail_column_html['AR Status']){$total_col_span_a_html++;}
	if($detail_column_html['Assign To']){$total_col_span_a_html++;}

	//calculate top header row colspan
	$title_col_span=0;
	if($total_col){
		$title_col_span=$total_col/3;
		$each_cell_width=100/$total_col;
		$wdth=' style="width:'.$each_cell_width.'%"';
	}
	$title_col_span=intval($title_col_span);
	$total_col_diff=$total_col-($title_col_span*3);

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
				$html_body ='<table id="ar_summ_tbl" class="rpt_table_det rpt rpt_table-bordered table-hover"  >'; //style="width:100%"
				$pdf_body ='<table cellpadding="1" cellspacing="1" bgcolor="#FFF3E8" border="0" style="width:'.$printPdfWidth.'px">';
			}

			$ff++;
			#HTML
			$html_body.='<thead><tr><td class="text_b_w txt_l" colspan="'.$headerColspan.'">'.$group_by.': '.$show_arr_name.'</td>';
			$html_body.='<td class="text_b_w txt_l" colspan="'.$headerColspan.'">'.$show_tel. core_phone_format($insCompArr[$ins_key]['phone']).'</td>';
			$html_body.='<td class="text_b_w txt_l" colspan="'.($headerColspan+$colspanDiff).'">'.$show_fax. core_phone_format($insCompArr[$ins_key]['fax']).'</td>
			</tr>';
			#PDF
			$pdf_body.='<tr><td class="text_b_w txt_l" colspan="6">'.$group_by.': '.$show_arr_name.'</td>';
			$pdf_body.='<td class="text_b_w txt_l" colspan="7">'.$show_tel. core_phone_format($insCompArr[$ins_key]['phone']).'</td>';
			$pdf_body.='<td class="text_b_w txt_l" colspan="'.(7+$total_col_diff).'">'.$show_fax. core_phone_format($insCompArr[$ins_key]['fax']).'</td>
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
			
			$show_ord_sign=array();
			if($_POST['ord_by_field']!=""){
				if($_POST['ord_by_ascdesc']=="DESC"){
					$show_ord_sign[$_POST['ord_by_field']]='<span class="glyphicon glyphicon-chevron-down pull-right"></span>';
				}else{
					$show_ord_sign[$_POST['ord_by_field']]='<span class="glyphicon glyphicon-chevron-up pull-right"></span>';
				}
			}
			
			$html_body.='<tr>';
			$html_body.='<td class="text_b_w txt_c"><div class="checkbox">
							<input type="checkbox" name="chkbx_all" id="chkbx_all" onClick="return chk_all();"><label for="chkbx_all"></label>
							</div></td>';
			if($detail_column_html['Patient Name - ID']){
			$html_body.='<td class="text_b_w txt_l" onClick="ar_ord_by(\'pat_name_ord\')">Patient Name - ID'.$show_ord_sign['pat_name_ord'].'</td>';}
			if($detail_column_html['DOB']){
			$html_body.='<td class="text_b_w txt_c" onClick="ar_ord_by(\'pat_dob_ord\')">DOB'.$show_ord_sign['pat_dob_ord'].'</td>';}
			if($detail_column_html['DOS']){
			$html_body.='<td class="text_b_w txt_c" onClick="ar_ord_by(\'enc_dos_ord\')">DOS'.$show_ord_sign['enc_dos_ord'].'</td>';}
			/*if($detail_column_html['Facility']){
			$html_body.='<td class="text_b_w txt_c" onClick="ar_ord_by(\'enc_fac_ord\')">Facility'.$show_ord_sign['enc_fac_ord'].'</td>';*/
			if($detail_column_html['Facility']){
			$html_body.='<td class="text_b_w txt_c" onClick="ar_ord_by(\'enc_pos_fac_ord\')">POS Facility'.$show_ord_sign['enc_pos_fac_ord'].'</td>';}
			if($detail_column_html['Provider']){
			$html_body.='<td class="text_b_w txt_c" onClick="ar_ord_by(\'enc_prov_ord\')">Provider'.$show_ord_sign['enc_prov_ord'].'</td>';}
			if($detail_column_html['Ins. Type']){
			$html_body.='<td class="text_b_w txt_c" onClick="ar_ord_by(\'enc_ins_type_ord\')">'.$ins_type_head.''.$show_ord_sign['enc_ins_type_ord'].'</td>';}
			if($detail_column_html['Ins. ID']){
			$html_body.='<td class="text_b_w txt_c" onClick="ar_ord_by(\'enc_ins_pol_ord\')">Ins. ID'.$show_ord_sign['enc_ins_pol_ord'].'</td>';}
			if($detail_column_html['DOC']){
			$html_body.='<td class="text_b_w txt_c">DOC</td>';}
			if($detail_column_html['CPT']){
			$html_body.='<td class="text_b_w txt_c">CPT</td>';}
			if($detail_column_html['ICD10']){
			$html_body.='<td class="text_b_w txt_c">ICD10</td>';}
			if($detail_column_html['R']){
			$html_body.='<td class="text_b_w txt_c">R</td>';}
			if($detail_column_html['Charge']){
			$html_body.='<td class="text_b_w txt_r">Charge</td>';}
			if($detail_column_html['Aging']){
			$html_body.=$ins_ar_heading_data;}
			if($detail_column_html['Balance']){
			$html_body.='<td class="text_b_w txt_r">Balance</td>';}
			if($detail_column_html['CFD']){
			$html_body.='<td class="text_b_w txt_c">CFD</td>';}
			if($detail_column_html['PD']){
			$html_body.='<td class="text_b_w txt_c">PD</td>';}
			if($detail_column_html['Prt Pt St']){
			$html_body.='<td class="text_b_w txt_c" onClick="ar_ord_by(\'enc_pt_st_ord\')">Prt Pt St'.$show_ord_sign['enc_pt_st_ord'].'</td>';}
			if($detail_column_html['1st Claim']){
			$html_body.='<td class="text_b_w txt_c" onClick="ar_ord_by(\'enc_claim_ord\')">1st Claim'.$show_ord_sign['enc_claim_ord'].'</td>';}
			if($detail_column_html['Note']){
			$html_body.='<td class="text_b_w txt_c">Note</td>';}
			if($detail_column_html['Reminder Date']){
			$html_body.='<td class="text_b_w txt_c">Reminder Date</td>';}
			if($detail_column_html['Case Type']){
			$html_body.='<td class="text_b_w txt_l" onClick="ar_ord_by(\'enc_ins_case_ord\')">Case Type'.$show_ord_sign['enc_ins_case_ord'].'</td>';}
			if($detail_column_html['AR Status']){$html_body.='<td class="text_b_w txt_l">AR Status</td>';}
			if($detail_column_html['Assign To']){
			$html_body.='<td class="text_b_w txt_l">Assign To</td>';}
			$html_body.='</tr></thead><tbody>';
			
			$pdf_body.='<tr>';
			if($detail_column_list['Patient Name - ID']){$pdf_body.='<td class="text_b_w txt_c" style="width:120px">Patient Name - ID</td>';}
			if($detail_column_list['DOB']){$pdf_body.='<td class="text_b_w txt_c" style="width:40px">DOB</td>';}
			if($detail_column_list['DOS']){$pdf_body.='<td class="text_b_w txt_c" style="width:40px">DOS</td>';}
			if($detail_column_list['Facility']){$pdf_body.='<td class="text_b_w txt_c" style="width:100px">POS Facility</td>';}
			if($detail_column_list['Provider']){$pdf_body.='<td class="text_b_w txt_c" style="width:40px">Prov.</td>';}
			if($detail_column_list['Ins. Type']){$pdf_body.='<td class="text_b_w txt_c" style="width:50px">'.$ins_type_head.'</td>';}
			if($detail_column_list['Ins. ID']){$pdf_body.='<td class="text_b_w txt_c" style="width:50px">Ins. ID</td>';}
			if($detail_column_list['DOC']){$pdf_body.='<td class="text_b_w txt_c" style="width:40px">DOC</td>';}
			if($detail_column_list['CPT']){$pdf_body.='<td class="text_b_w txt_c" style="width:50px">CPT</td>';}
			if($detail_column_list['ICD10']){$pdf_body.='<td class="text_b_w txt_c" style="width:50px">ICD10</td>';}
			if($detail_column_list['R']){$pdf_body.='<td class="text_b_w txt_c" style="width:10px">R</td>';}
			if($detail_column_list['Charge']){$pdf_body.='<td class="text_b_w txt_c" style="width:40px">Charge</td>';}
			if($detail_column_list['Aging']){$pdf_body.=$ins_ar_heading_data;}
			if($detail_column_list['Balance']){$pdf_body.='<td class="text_b_w txt_r" style="width:40px">Balance</td>';}
			if($detail_column_list['CFD']){$pdf_body.='<td class="text_b_w txt_c" style="width:20px">CFD</td>';}
			if($detail_column_list['PD']){$pdf_body.='<td class="text_b_w txt_c" style="width:20px">PD</td>';}
			if($detail_column_list['Prt Pt St']){$pdf_body.='<td class="text_b_w txt_c" style="width:40px">Prt Pt St</td>';}
			if($detail_column_list['1st Claim']){$pdf_body.='<td class="text_b_w txt_c" style="width:40px">1st Claim</td>';}
			if($detail_column_list['Note']){$pdf_body.='<td class="text_b_w txt_c" style="width:40px">Note</td>';}
			if($detail_column_list['Reminder Date']){$pdf_body.='<td class="text_b_w txt_c" style="width:40px">Reminder Date</td>';}
			if($detail_column_list['Case Type']){$pdf_body.='<td class="text_b_w txt_r" style="width:auto">Case Type</td>';}
			if($detail_column_list['AR Status']){$pdf_body.='<td class="text_b_w txt_r" style="width:auto">AR Status</td>';}
			if($detail_column_list['Assign To']){$pdf_body.='<td class="text_b_w txt_r" style="width:auto">Assign To</td>';}
			$pdf_body.='</tr>';



			$csv_data.="Patient Name - ID". $pfx ."DOB". $pfx ."DOS". $pfx ."POS Facility". $pfx ."Provider". $pfx .$ins_type_head. $pfx ."Ins. ID". $pfx  ."DOC". $pfx ."CPT". $pfx ."ICD10". $pfx ."R". $pfx ."Charge". $pfx . $csv_ins_ar_heading_data."Balance". $pfx  ."CFD". $pfx ."PD". $pfx ."Prt Pt St". $pfx."1st Claim". $pfx ."Note". $pfx ."Reminder Date" .$pfx ."Case Type". $pfx ."AR Status". $pfx ."Assign To \n";

			foreach($ins_priority_arr as $ins_priority_key => $ins_priority_val){
				foreach($final_ar_data_arr[$ins_priority_key] as $ar_data_key => $ar_data_val){
					$ar_pat_data=$final_ar_data_arr[$ins_priority_key][$ar_data_key];
					$pat_id=$ar_pat_data['patient_id'];
					$charge_list_id=$ar_pat_data['charge_list_id'];
					$ar_pat_data_arr[$charge_list_id]['patient_id']=$pat_id;
					$ar_pat_data_arr[$charge_list_id]['name']=ucwords(trim($ar_pat_data['lname'].", ".$ar_pat_data['fname']." ".$ar_pat_data['mname']));
					$ar_pat_data_arr[$charge_list_id]['DOB']=$ar_pat_data['DOB'];
					$ar_pat_data_arr[$charge_list_id]['totalAmount'][]=$ar_pat_data['totalAmount'];
					$ar_pat_data_arr[$charge_list_id]['chld'][]=$ar_pat_data['charge_list_detail_id'];
					$ar_pat_data_arr[$charge_list_id]['chld_det'][$ar_pat_data['charge_list_detail_id']]=$ar_pat_data;
					$err_msg="";
					if($ar_pat_data['primaryInsuranceCoId']==0){
						$err_msg='Patient Primary Infomation is Required.';
						$all_error[$pat_id][$ar_pat_data['charge_list_id']][$err_msg] = $err_msg;
					}
					if($ar_pat_data['sex'] == ''){
						$err_msg='Patient Gender Infomation is Required.';
						$all_error[$pat_id][$ar_pat_data['charge_list_id']][$err_msg] = $err_msg;
					}
					if($usersArr[$ar_pat_data['primaryProviderId']]['user_npi'] == ''){
						$err_msg='Rendering Physician NPI # is Required.';
						$all_error[$pat_id][$ar_pat_data['charge_list_id']][$err_msg] = $err_msg;
					}
					if($usersArr[$ar_pat_data['primaryProviderId']]['TaxonomyId'] == ''){
						$err_msg='Rendering Physician Taxonomy # is Required.';
						$all_error[$pat_id][$ar_pat_data['charge_list_id']][$err_msg] = $err_msg;
					}
					if($ar_pat_data['reff_phy_nr']==0){
						if($ar_pat_data['reff_phy_id']>0){
							$npiNumber = $reff_phy_arr[$ar_pat_data['reff_phy_id']]['NPI'];
						}
						if($npiNumber == ''){
							$err_msg='Referring Physician NPI # is Required.';
							$all_error[$pat_id][$ar_pat_data['charge_list_id']][$err_msg] = $err_msg;
						}
					}
				}
			}
			$old_patient_id="";
			//pre($ar_pat_data_arr);
			foreach($ar_pat_data_arr as $ar_pat_key => $ar_pat_val){
				$ar_data=$ar_pat_data_arr[$ar_pat_key];
				$patientName = $ar_data['name'];
				$ins_ar_data=$csv_ins_ar_data=$val_cfd=$csv_cfd=$val_pd=$csv_pd=$ins_val_cfd=$ins_csv_cfd=$ins_val_pd=$ins_csv_pd="";
				for($a=$aging_start;$a<=$aging_to;$a++){
					$start = $a;
					$a = $a > 0 ? $a - 1 : $a;
					$end = ($a) + $aggingCycle;
					$ins_pat_total_due[$ins_key][$ar_data['patient_id']][]=array_sum($final_ins_pat_res_arr[$ar_data['patient_id']][$start]);
					//$ar_ins_pat_total_due[$ins_key][$start][]=array_sum($final_ins_pat_res_arr[$ar_data['patient_id']][$start]);
					//$ins_pat_sub_due[$ins_key][]=array_sum($final_ins_pat_res_arr[$ar_data['patient_id']][$start]);
					//$ar_ins_pat_grand_total_due[$start][]=array_sum($final_ins_pat_res_arr[$ar_data['patient_id']][$start]);
					//$ins_pat_grand_due[]=array_sum($final_ins_pat_res_arr[$ar_data['patient_id']][$start]);
					$tmp_val=0;
					$tmp_val=$CLSReports->numberFormat(array_sum($final_ins_pat_res_arr[$ar_data['patient_id']][$start]),2,1);
					$ins_ar_data .='<td class="text_10 txt_r">'.$tmp_val.'</td>';
					$csv_ins_ar_data.="\"".$tmp_val."\"".$pfx;
					$a += $aggingCycle;
				}

				$prt_pt_st="Y";
				if($collection_pat_id[$ar_data['patient_id']][$ar_chld_data['encounter_id']]>0){
					$prt_pt_st="N";
				}

				#values to show up in html, pdf and csv
				$val_patient=$csv_patient=$patientName.' - '.$ar_data['patient_id'];
				$val_dob=$csv_dob=get_date_format($ar_data['DOB']);
				if($group_by=="Insurance"){
					$ins_val_cfd=$ins_csv_cfd=$insCompArr[$ins_key]['claim_filing_days'];
					$ins_val_pd=$ins_csv_pd=$insCompArr[$ins_key]['payment_due_days'];
				}
				$val_charge=$CLSReports->numberFormat(array_sum($ar_data['totalAmount']),2,1);
				$val_Balance=$CLSReports->numberFormat(array_sum($ins_pat_total_due[$ins_key][$ar_data['patient_id']]),2,1);
				//$val_prt_pt_st=$csv_prt_pt_st=$prt_pt_st;
				$chld_id_imp=implode(',',$ar_data['chld']);
				$old_patient_id=$ar_data['patient_id'];
				$ins_total_charges[$ins_key][]=array_sum($ar_data['totalAmount']);
				$yellow_bg="";
				$pat_chk_arr=array_combine($pat_chk_arr,$pat_chk_arr);
				if($pat_chk_arr[$old_patient_id])$yellow_bg=" class=\"yellow_bg\"";
				
				foreach($ar_data['chld_det'] as $ar_chld_key => $ar_chld_val){

					$pat_ins_pol=array();
					$ar_chld_data=$ar_data['chld_det'][$ar_chld_key];

					$ar_pol_data=$pat_ins_pol_data[$ar_chld_data['patient_id']][$ar_chld_data['case_type_id']];
					if($ar_pol_data[$ar_chld_data['primaryInsuranceCoId']]['primary']!=""){
						$pat_ins_pol[$ar_chld_data['primaryInsuranceCoId']]=$ar_pol_data[$ar_chld_data['primaryInsuranceCoId']]['primary'];
					}
					if($ar_pol_data[$ar_chld_data['secondaryInsuranceCoId']]['secondary']!=""){
						$pat_ins_pol[$ar_chld_data['secondaryInsuranceCoId']]=$ar_pol_data[$ar_chld_data['secondaryInsuranceCoId']]['secondary'];
					}
					if($ar_pol_data[$ar_chld_data['tertiaryInsuranceCoId']]['tertiary']!=""){
						$pat_ins_pol[$ar_chld_data['tertiaryInsuranceCoId']]=$ar_pol_data[$ar_chld_data['tertiaryInsuranceCoId']]['tertiary'];
					}
					$patientName = ucwords(trim($ar_chld_data['lname'].", ".$ar_chld_data['fname']." ".$ar_chld_data['mname']));
					$ins_ar_data=$csv_ins_ar_data=$ins_ar_pdf_data="";
					for($a=$aging_start;$a<=$aging_to;$a++){
						$start = $a;
						$a = $a > 0 ? $a - 1 : $a;
						$end = ($a) + $aggingCycle;
						$ins_total_due[$ins_key][$ar_chld_data['charge_list_detail_id']][]=array_sum($final_ins_chld_res_arr[$ar_chld_data['charge_list_detail_id']][$start]);
						$ar_ins_total_due[$ins_key][$start][]=array_sum($final_ins_chld_res_arr[$ar_chld_data['charge_list_detail_id']][$start]);
						$ins_sub_due[$ins_key][]=array_sum($final_ins_chld_res_arr[$ar_chld_data['charge_list_detail_id']][$start]);
						$ar_ins_grand_total_due[$start][]=array_sum($final_ins_chld_res_arr[$ar_chld_data['charge_list_detail_id']][$start]);
						$ins_grand_due[]=array_sum($final_ins_chld_res_arr[$ar_chld_data['charge_list_detail_id']][$start]);
						$tmp_val=0;
						$tmp_val=$CLSReports->numberFormat(array_sum($final_ins_chld_res_arr[$ar_chld_data['charge_list_detail_id']][$start]),2,1);
						$ins_ar_data .='<td class="text_10 txt_r">'.$tmp_val.'</td>';
						$ins_ar_pdf_data .='<td class="text_10 txt_r">'.$tmp_val.'</td>';
						$csv_ins_ar_data.="\"".$tmp_val."\"".$pfx;
						$a += $aggingCycle;
					}

					$prt_pt_st="Y";
					if($collection_pat_id[$ar_chld_data['patient_id']][$ar_chld_data['encounter_id']]>0){
						$prt_pt_st="N";
					}
					
					$icd10_code_arr=array();
					for($kh=1;$kh<=12;$kh++){
						if($ar_chld_data['diagnosis_id'.$kh]!=""){
							$icd10_code_arr[$ar_chld_data['diagnosis_id'.$kh]]=$ar_chld_data['diagnosis_id'.$kh];
						}
					}
					$show_icd10_codes=implode(', ',$icd10_code_arr);

					$claim_rejected="";$claim_rejected_css=$claim_rejected_code="";
					//echo $chld_rejected_arr[$ar_chld_data['charge_list_detail_id']]['denied_date'].'-'.$enc_submitted_arr[$ar_chld_data['encounter_id']].'<br>';
					if($chld_rejected_arr[$ar_chld_data['charge_list_detail_id']]['denied_date']!='' && $chld_rejected_arr[$ar_chld_data['charge_list_detail_id']]['denied_date']>$enc_submitted_arr[$ar_chld_data['encounter_id']]){
						$claim_rejected="Y";
						$claim_rejected_css=" text-red";
						$claim_rejected_code=$chld_rejected_arr[$ar_chld_data['charge_list_detail_id']]['cas_code'];
					}
					#values to show up in html, pdf and csv
					$val_patient=$csv_patient=$patientName.' - '.$ar_data['patient_id'];
					$val_dob=$csv_dob=get_date_format($ar_data['DOB']);
					//$val_facility=$csv_facility=$facArr[$ar_chld_data['billing_facility_id']];
					$val_facility=$csv_facility=$PosFacArr[$ar_chld_data['posFacilityId']];
					$val_provider=$csv_provider=strtoupper($usersArrInt[$ar_chld_data['primaryProviderId']]);
					$val_dos=$csv_dos=get_date_format($ar_chld_data['date_of_service']);
					$val_doc=get_date_format($ar_chld_data['entered_date']);
					$val_cpt=$cptDetailsArr[$ar_chld_data['procCode']]['prac_code'];
					$val_prt_pt_st=$csv_prt_pt_st=$prt_pt_st;
					$val_icd10=$show_icd10_codes;
					$val_charge=$CLSReports->numberFormat($ar_chld_data['totalAmount'],2,1);
					$val_p='';
					$val_r=$claim_rejected;
					$val_Balance=$CLSReports->numberFormat(array_sum($ins_total_due[$ins_key][$ar_chld_data['charge_list_detail_id']]),2,1);
					$val_f_claim=$csv_f_claim=get_date_format($ar_chld_data['firstSubmitDate']);
					//$val_note=$ar_chld_data['notes'];
					$val_note=$pat_enc_comments[$ar_chld_data['encounter_id']]['encComments'];
					$val_reminder_date=get_date_format($pat_enc_comments[$ar_chld_data['encounter_id']]['reminder_date']);
					if($val_note==""){
						$val_reminder_date="";
					}
					
					$val_case=$csv_case=$arr_ins_case_type_all[$ins_case_type_id[$ar_chld_data['case_type_id']]];
					$val_ar_claim_status=$csv_ar_claim_status=$statusArr[$ar_chld_data['claim_status']];
					
					$ar_assign_to_exp=explode(',',$ar_chld_data['ar_assign_to']);
					$ar_assign_usr_arr=array();
					foreach($ar_assign_to_exp as $ar_assign_to_exp_key => $ar_assign_to_exp_val){
						if($ar_assign_to_exp>0){
							$ar_assign_usr_arr[$ar_assign_to_exp_val]=strtoupper($usersArrInt[$ar_assign_to_exp_val]);
						}
					}
					$val_ar_assign=implode(', ',$ar_assign_usr_arr);
					$csv_ar_assign='"'.implode(', ',$ar_assign_usr_arr).'"';
					if($group_by=="Insurance"){
						$val_ins=$csv_ins=$pat_ins_pol[$ins_key];
						if($ar_chld_data['primaryInsuranceCoId']>0 && $ins_key==$ar_chld_data['primaryInsuranceCoId']){
							$val_ins_type=$csv_ins_type="Pri";
						}
						if($ar_chld_data['secondaryInsuranceCoId']>0 && $ins_key==$ar_chld_data['secondaryInsuranceCoId']){
							$val_ins_type=$csv_ins_type="Sec";
						}
						if($ar_chld_data['tertiaryInsuranceCoId']>0 && $ins_key==$ar_chld_data['tertiaryInsuranceCoId']){
							$val_ins_type=$csv_ins_type="Ter";
						}
					}else{
						$val_ins_type=$csv_ins_type=$insCompArr[$ar_chld_data['primaryInsuranceCoId']]['in_house_code'];
						$val_ins=$csv_ins=$pat_ins_pol[$ar_chld_data['primaryInsuranceCoId']];
					}
					//$class=$class_sub=$val_patient=$val_dob=$val_cfd=$val_pd=$val_prt_pt_st='';
					$class=$class_sub='';

					if($group_by!="Insurance"){
						if($insCompArr[$ar_chld_data['primaryInsuranceCoId']]['claim_filing_days']>0){
							$ins_val_cfd=$ins_csv_cfd=$insCompArr[$ar_chld_data['primaryInsuranceCoId']]['claim_filing_days'];
						}
						if($insCompArr[$ar_chld_data['primaryInsuranceCoId']]['payment_due_days']>0){
							$ins_val_pd=$ins_csv_pd=$insCompArr[$ar_chld_data['primaryInsuranceCoId']]['payment_due_days'];
						}
					}
					
					if($ins_val_cfd>0){
						$val_cfd=$csv_cfd=($ins_val_cfd-show_date_diff($ar_chld_data['date_of_service']));
					}else{
						$val_pd=$csv_pd='';
					}
					$cfd_css=$cfd_pdf_css="";
					if($val_cfd<0){
						$cfd_css=" text-red";
						$cfd_pdf_css=" color:red";
					}

					if($ins_val_pd>0 && $enc_submitted_arr[$ar_chld_data['encounter_id']]!='' && $enc_submitted_arr[$ar_chld_data['encounter_id']]>=$ar_chld_data['postedDate']){
						$val_pd=$csv_pd=($ins_val_pd-show_date_diff($enc_submitted_arr[$ar_chld_data['encounter_id']]));
					}else{
						$val_pd=$csv_pd='';
					}
					$pd_css=$pd_pdf_css="";
					if($val_pd<0){
						$pd_css=" text-red";
						$pd_pdf_css=" color:red";
					}
										
					$child_tr_css=$old_patient_id.'_child';
					if($old_enc_id==$ar_chld_data['encounter_id']){
						$val_patient=$val_dob=$val_facility=$val_ins=$val_ins_type=$val_provider=$val_cfd=$val_pd=$val_dos=$val_f_claim=$val_prt_pt_st=$val_case=$val_ar_claim_status=$val_note=$val_reminder_date="";
						$enc_class="chk_box_enc_".$old_enc_id."_child";
						$child_tr_css=$old_patient_id.'_child light_bg';
					}else{
						$old_enc_id=$ar_chld_data['encounter_id'];
						$enc_class="chk_box_enc_parent";
					}

					#HTML
					$html_body.='<tr class="'.$child_tr_css.'" data-patient="'.$old_patient_id.'">
					<input type="hidden" name="chld_balance['.$ar_chld_data['charge_list_detail_id'].']" id="chld_balance_'.$ar_chld_data['charge_list_detail_id'].'" value="'.array_sum($ins_total_due[$ins_key][$ar_chld_data['charge_list_detail_id']]).'">
					<input type="hidden" name="enc_chld['.$ar_chld_data['charge_list_detail_id'].']" id="enc_chld_'.$ar_chld_data['charge_list_detail_id'].'" value="'.$ar_chld_data['encounter_id'].'">
					<td class="text_10 txt_l"><div class="checkbox">
					<input class="chk_box_css chk_box_'.$old_patient_id.'_child '.$enc_class.'" data-encid="'.$old_enc_id.'" data-chlid="'.$ar_chld_data['charge_list_id'].'" type="checkbox" value="'.$ar_chld_data['charge_list_detail_id'].'" name="chld_arr[]" id="chld_'.$ar_chld_data['charge_list_detail_id'].'" data-ptid="'.$old_patient_id.'"><label for="chld_'.$ar_chld_data['charge_list_detail_id'].'"></label>
					</div></td>';
					if($detail_column_html['Patient Name - ID']){
					$html_body.='<td class="text_10 txt_l">'.$val_patient;
					$info_msg_detail="";
					if(count($all_error[$ar_data['patient_id']])>0 && $val_patient!=""){
						$ar_charge_list_id=$ar_chld_data['charge_list_id'];
						if(count($all_error[$ar_data['patient_id']][$ar_charge_list_id])>0){
							$info_msg_detail.="<div class=infoSubTitle>DOS ".$val_dob.":-</div><br>";
							$info_msg_detail.=implode('<br>',$all_error[$ar_data['patient_id']][$ar_charge_list_id]);
							$info_msg_detail.="<div class=infoDataLine></div>";
							$i_info[$ar_charge_list_id]=addslashes($info_msg_detail);
							$html_body.='<div id="rptInfoImg" style="float:right;" class="rptInfoImg" onclick="showHideReportInfo(event, \'30\',\''.$ar_charge_list_id.'\')"></div>';
						}
					}
					$html_body.='</td>';}
					if($detail_column_html['DOB']){
					$html_body.='<td class="text_10 txt_l" nowrap>'.$val_dob.'</td>';}
					if($detail_column_html['DOS']){
					$html_body.='<td class="text_10 txt_l" nowrap>'.$val_dos.'</td>';}
					if($detail_column_html['Facility']){
					$html_body.='<td class="text_10 txt_l">'.$val_facility.'</td>';}
					if($detail_column_html['Provider']){
					$html_body.='<td class="text_10 txt_l">'.$val_provider.'</td>';}
					if($detail_column_html['Ins. Type']){
					$html_body.='<td class="text_10 txt_l">'.$val_ins_type.'</td>';}
					if($detail_column_html['Ins. ID']){
					$html_body.='<td class="text_10 txt_l">'.$val_ins.'</td>';}
					if($detail_column_html['DOC']){
					$html_body.='<td class="text_10 txt_l" nowrap>'.$val_doc.'</td>';}
					if($detail_column_html['CPT']){
					$html_body.='<td class="text_10 txt_l">'.$val_cpt.'</td>';}
					if($detail_column_html['ICD10']){
					$html_body.='<td class="text_10 txt_l">'.$val_icd10.'</td>';}
					if($detail_column_html['R']){
					$html_body.='<td class="text_10'.$claim_rejected_css.' txt_l pointer" '.show_tooltip($claim_rejected_code).'>'.$val_r.'</td>';}
					if($detail_column_html['Charge']){
					$html_body.='<td class="text_10 txt_r">'.$val_charge.'</td>';}
					if($detail_column_html['Aging']){
					$html_body.=$ins_ar_data;}
					if($detail_column_html['Balance']){
					$html_body.='<td class="text_10 txt_r">'.$val_Balance.'</td>';}
					if($detail_column_html['CFD']){
					$html_body.='<td class="text_10'.$cfd_css.' txt_r">'.$val_cfd.'</td>';}
					if($detail_column_html['PD']){
					$html_body.='<td class="text_10'.$pd_css.' txt_r">'.$val_pd.'</td>';}
					if($detail_column_html['Prt Pt St']){
					$html_body.='<td class="text_10 txt_l">'.$val_prt_pt_st.'</td>';}
					if($detail_column_html['1st Claim']){
					$html_body.='<td class="text_10 txt_l" nowrap>'.$val_f_claim.'</td>';}
					if($detail_column_html['Note']){
						if($val_note!=""){
							$html_body.='<td class="text_10 txt_l text_purple pointer" onClick="all_notes('.$ar_chld_data['encounter_id'].')">'.$val_note.'</td>';
						}else{
							$html_body.='<td class="text_10 txt_l"></td>';
						}
					}
					if($detail_column_html['Reminder Date']){
					$html_body.='<td class="text_10 txt_l" nowrap>'.$val_reminder_date.'</td>';}
					if($detail_column_html['Case Type']){
					$html_body.='<td class="text_10 txt_l">'.$val_case.'</td>';}
					if($detail_column_html['AR Status']){
					$html_body.='<td class="text_10 txt_l">'.$val_ar_claim_status.'</td>';}
					if($detail_column_html['Assign To']){
					$html_body.='<td class="text_10 txt_l">'.$val_ar_assign.'</td>';}
					$html_body.='</tr>';
					#PDF
					$pdf_body.='<tr>';
					if($detail_column_list['Patient Name - ID'])$pdf_body.='<td class="text_10 txt_l" style="width:120px">'.$val_patient.'</td>';
					if($detail_column_list['DOB'])$pdf_body.='<td class="text_10 txt_l" nowrap style="width:40px">'.$val_dob.'</td>';
					if($detail_column_list['DOS'])$pdf_body.='<td class="text_10 txt_l" nowrap style="width:40px">'.$val_dos.'</td>';
					if($detail_column_list['Facility'])$pdf_body.='<td class="text_10 txt_l" style="width:100px">'.$val_facility.'</td>';
					if($detail_column_list['Provider'])$pdf_body.='<td class="text_10 txt_l" style="width:40px">'.$val_provider.'</td>';
					if($detail_column_list['Ins. Type'])$pdf_body.='<td class="text_10 txt_l" style="width:10px">'.$val_ins_type.'</td>';
					if($detail_column_list['Ins. ID'])$pdf_body.='<td class="text_10 txt_l" style="width:50px">'.$val_ins.'</td>';
					if($detail_column_list['DOC'])$pdf_body.='<td class="text_10 txt_l" nowrap style="width:40px">'.$val_doc.'</td>';
					if($detail_column_list['CPT'])$pdf_body.='<td class="text_10 txt_l" style="width:50px">'.$val_cpt.'</td>';
					if($detail_column_list['ICD10'])$pdf_body.='<td class="text_10 txt_l" style="width:50px">'.$val_icd10.'</td>';
					if($detail_column_list['R'])$pdf_body.='<td class="text_10'.$claim_rejected_css.' txt_l" style="width:10px">'.$val_r.'</td>';
					if($detail_column_list['Charge'])$pdf_body.='<td class="text_10 txt_r" style="width:40px">'.$val_charge.'</td>';
					if($detail_column_list['Aging'])$pdf_body.=$ins_ar_pdf_data;
					if($detail_column_list['Balance'])$pdf_body.='<td class="text_10 txt_r" style="width:40px">'.$val_Balance.'</td>';
					if($detail_column_list['CFD'])$pdf_body.='<td class="text_10 txt_r" style="width:20px;'.$cfd_pdf_css.'">'.$val_cfd.'</td>';
					if($detail_column_list['PD'])$pdf_body.='<td class="text_10 txt_l" style="width:20px;'.$pd_pdf_css.'">'.$val_pd.'</td>';
					if($detail_column_list['Prt Pt St'])$pdf_body.='<td class="text_10 txt_l" style="width:40px">'.$val_prt_pt_st.'</td>';
					if($detail_column_list['1st Claim'])$pdf_body.='<td class="text_10 txt_l" style="width:40px" nowrap>'.$val_f_claim.'</td>';
					if($detail_column_list['Note'])$pdf_body.='<td class="text_10 txt_l" style="width:40px">'.$val_note.'</td>';
					if($detail_column_list['Reminder Date'])$pdf_body.='<td class="text_10 txt_l" style="width:40px">'.$val_reminder_date.'</td>';
					if($detail_column_list['Case Type'])$pdf_body.='<td class="text_10 txt_l" style="width:auto">'.$val_case.'</td>';
					if($detail_column_list['AR Status'])$pdf_body.='<td class="text_10 txt_l" style="width:auto">'.$val_ar_claim_status.'</td>';
					if($detail_column_list['Assign To'])$pdf_body.='<td class="text_10 txt_l" style="width:auto">'.$val_ar_assign.'</td>';
					$pdf_body.='</tr>';
					#CSV
					$csv_data.="\"".$csv_patient."\"".$pfx;
					$csv_data.=$csv_dob. $pfx;
					$csv_data.=$csv_dos. $pfx;
					$csv_data.=$csv_facility . $pfx;
					$csv_data.="\"".$csv_provider."\"". $pfx;
					$csv_data.=$csv_ins_type. $pfx;
					$csv_data.=$csv_ins. $pfx;
					$csv_data.=$val_doc. $pfx;
					$csv_data.="\"".$val_cpt."\"". $pfx;
					$csv_data.="\"".$val_icd10."\"". $pfx;
					$csv_data.=$val_r. $pfx;
					$csv_data.="\"".$val_charge."\"".$pfx;
					$csv_data.=$csv_ins_ar_data;
					$csv_data.="\"".$val_Balance."\"". $pfx;
					$csv_data.=$csv_cfd. $pfx;
					$csv_data.=$csv_pd. $pfx;
					$csv_data.=$csv_prt_pt_st. $pfx;
					$csv_data.=$csv_f_claim. $pfx;
					$csv_data.="\"".$val_note."\"". $pfx;
					$csv_data.="\"".$val_reminder_date."\"". $pfx;
					$csv_data.="\"".$csv_case."\"". $pfx;
					$csv_data.="\"".$csv_ar_claim_status."\"". $pfx;
					$csv_data.=$csv_ar_assign. $pfx . "\n";
				}
			}
		}
		if(array_sum($ins_sub_due[$ins_key])>0){
			#HTML
			$html_body.='<tr><td class="text_b_w txt_r detail_field_precal" colspan="'.($total_col_span_html+1).'"> Total</td>';
			if($detail_column_html['Charge']){
			$html_body.='<td class="text_b_w txt_r">'.$CLSReports->numberFormat(array_sum($ins_total_charges[$ins_key]),2,1).'</td>';
			}
			#PDF
			$pdf_body.='<tr>';
			if($detail_column_list['Aging'])$pdf_body.='<td class="text_b_w txt_r" colspan="'.$total_col_span.'"> Total</td>';
			#CSV
			for($i=1;$i<11;$i++)$csv_data.=$pfx;
			$csv_data.="Total".$pfx;

			for($a=$aging_start;$a<=$aging_to;$a++){
				$start = $a;
				$a = $a > 0 ? $a - 1 : $a;
				$end = ($a) + $aggingCycle;
				$tmp_val=0;
				$tmp_val=$CLSReports->numberFormat(array_sum($ar_ins_total_due[$ins_key][$start]),2,1);
				#HTML
				if($detail_column_html['Aging'])$html_body .='<td class="text_b_w txt_r">'.$tmp_val.'</td>';
				#PDF
				if($detail_column_list['Aging'])$pdf_body .='<td class="text_b_w txt_r">'.$tmp_val.'</td>';
				#CSV
				$csv_data.="\"".$tmp_val."\"".$pfx;
				$a += $aggingCycle;
			}
			$tmp_val=0;
			$tmp_val=$CLSReports->numberFormat(array_sum($ins_sub_due[$ins_key]),2,1);
			#HTML
			if($detail_column_html['Balance']){
			$html_body .='<td class="text_b_w txt_r">'.$tmp_val.'</td>';}
			if($total_col_span_a_html>0){
			$html_body .='<td class="text_b_w txt_r detail_field_postcal" colspan="'.$total_col_span_a_html.'"></td>';}
			$html_body .='</tr>';
			#PDF
			if($detail_column_list['Balance'])$pdf_body .='<td class="text_b_w txt_r">'.$CLSReports->numberFormat(array_sum($ins_total_charges[$ins_key]),2,1).'</td><td class="text_b_w txt_r">'.$tmp_val.'</td>';
			if($total_col_span_a>0)$pdf_body .='<td class="text_b_w txt_r" colspan="'.$total_col_span_a.'"></td>';
			$pdf_body .='</tr>';
			#CSV
			$csv_data.="\"".$CLSReports->numberFormat(array_sum($ins_total_charges[$ins_key]),2,1)."\"".$pfx;
			$csv_data.="\"".$tmp_val."\"".$pfx."\n";

		}
		$html_body .= '</tbody></table>';
		$pdf_body .='</table>';

		$html_body .= "<script> var info_arr=". json_encode($i_info) .";</script>";
	}

?>
