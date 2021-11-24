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
	//get width for pdf columns
	$available_width_px=($printPdfWidth-(250+130));
	$each_col_wd_px=$available_width_px;
	if($ar_cols){
		$each_col_wd_px=$available_width_px/$ar_cols;
	}
	//get width for html outpur columns
	$available_width_per=60;
	$each_col_wd_per=$available_width_per;
	if($ar_cols){
		$each_col_wd_per=$available_width_per/$ar_cols;
	}
	
	$show_ord_sign=array();
	if($_POST['ord_by_field']!=""){
		if($_POST['ord_by_ascdesc']=="DESC"){
			$show_ord_sign[$_POST['ord_by_field']]='<span class="glyphicon glyphicon-chevron-down pull-right"></span>';
		}else{
			$show_ord_sign[$_POST['ord_by_field']]='<span class="glyphicon glyphicon-chevron-up pull-right"></span>';
		}
	}
	
	$main_html_body=$main_pdf_header=$main_csv_data="";
	for($a=$aging_start;$a<=$aging_to;$a++){
		$start = $a;
		$a = $a > 0 ? $a - 1 : $a;
		$end = ($a) + $aggingCycle;
		if($start==181){
			$main_html_body .='<td class="text_b_w" style="text-align:right; width:'.$each_col_wd_per.'%" onClick="ar_ord_by(\'aging_181\')">181+'.$show_ord_sign['aging_181'].'</td>';
			$main_pdf_header .='<td class="text_b_w" style="text-align:right; width:'.$each_col_wd_px.'px">181+</td>';
			$main_csv_data.="181+".$pfx;
		}else{
			$main_html_body .='<td class="text_b_w" style="text-align:right; width:'.$each_col_wd_per.'%" onClick="ar_ord_by(\'aging_'.$start.'\')">'.$start.' - '.$end.$show_ord_sign['aging_'.$start].'</td>';
			$main_pdf_header .='<td class="text_b_w" style="text-align:right; width:'.$each_col_wd_px.'px">'.$start.' - '.$end.'</td>';
			$main_csv_data.="\"$start - $end\"".$pfx;
		}
		$a += $aggingCycle;
	}

	$ff=0;
	$html_tr_content='';
	$ord_by_field_exp=explode('_',$_POST['ord_by_field']);
	if($ord_by_field_exp[0]=="aging"){
		$comp_ord_arr=array();
		foreach($ins_comp_arr as $ins_key => $ins_val){
			foreach($fac_comp_arr as $fac_key => $fac_val){
				foreach($prov_comp_arr as $prov_key => $prov_val){
					$final_comp_arr=$ins_res_arr[$ins_key][$fac_key][$prov_key];
					if($group_by=="Facility"){
						$arr_key=$fac_key;
					}else if($group_by=="POS Facility"){
						$arr_key=$fac_key;
					}else if($group_by=="Provider"){
						$arr_key=$prov_key;
					}else{
						$arr_key=$ins_key;
					}
					for($a=$aging_start;$a<=$aging_to;$a++){
						$start = $a;
						$a = $a > 0 ? $a - 1 : $a;
						$end = ($a) + $aggingCycle;
						$a += $aggingCycle;
						if(array_sum($final_comp_arr[$start])>0){
							$final_comp_arr_ord[$start][$arr_key]=array_sum($final_comp_arr[$start]);
						}
						$final_comp_arr_ord['balance'][$arr_key]=array_sum($final_comp_arr[$start]);
					}
				}
			}
		}
		if($_POST['ord_by_ascdesc']=="DESC"){
			arsort($final_comp_arr_ord[$ord_by_field_exp[1]]);
		}else{
			asort($final_comp_arr_ord[$ord_by_field_exp[1]]);
		}
		
		foreach($final_comp_arr_ord[$ord_by_field_exp[1]] as $ord_key => $ord_val){
			if($group_by=="Facility"){
				$comp_ord_arr[$ord_key]=$fac_comp_arr[$ord_key];
				unset($fac_comp_arr[$ord_key]);
			}else if($group_by=="Provider"){
				$comp_ord_arr[$ord_key]=$prov_comp_arr[$ord_key];
				unset($prov_comp_arr[$ord_key]);
			}else{
				$comp_ord_arr[$ord_key]=$ins_comp_arr[$ord_key];
				unset($ins_comp_arr[$ord_key]);
			}
		}
		
		if($_POST['ord_by_ascdesc']=="DESC"){
			if($group_by=="Facility"){
				$fac_comp_arr=$comp_ord_arr+$fac_comp_arr;
			}else if($group_by=="Provider"){
				$prov_comp_arr=$comp_ord_arr+$prov_comp_arr;
			}else{
				$ins_comp_arr=$comp_ord_arr+$ins_comp_arr;
			}
		}else{
			if($group_by=="Facility"){
				$fac_comp_arr=$fac_comp_arr+$comp_ord_arr;
			}else if($group_by=="Provider"){
				$prov_comp_arr=$prov_comp_arr+$comp_ord_arr;
			}else{
				$ins_comp_arr=$ins_comp_arr+$comp_ord_arr;
			}
		}
	}
	//pre($comp_ord_arr);
	foreach($ins_comp_arr as $ins_key => $ins_val){
		foreach($fac_comp_arr as $fac_key => $fac_val){
			foreach($prov_comp_arr as $prov_key => $prov_val){
				$final_comp_arr=$ins_res_arr[$ins_key][$fac_key][$prov_key];
				if($group_by=="Facility"){
					$arr_key=$fac_key;
					$show_arr_name=$fac_comp_arr[$arr_key];
				}else if($group_by=="POS Facility"){
					$arr_key=$fac_key;
					$show_arr_name=$fac_comp_arr[$arr_key];
				}else if($group_by=="Provider"){
					$arr_key=$prov_key;
					$show_arr_name=$prov_comp_arr[$arr_key]['name'];
				}else{
					$arr_key=$ins_key;
					$show_arr_name=$insCompArr[$arr_key]['in_house_code'].' - '.$insCompArr[$arr_key]['name'];
					$ins_id=$arr_key;
				}

				$ins_html_data=$ins_pdf_data=$csv_ar_data="";
				for($a=$aging_start;$a<=$aging_to;$a++){
					$start = $a;
					$a = $a > 0 ? $a - 1 : $a;
					$end = ($a) + $aggingCycle;
					$ins_total_due[$arr_key][]=array_sum($final_comp_arr[$start]);
					$ar_ins_total_due[$start][]=array_sum($final_comp_arr[$start]);
					$tmp_val=0;
					$tmp_val=$CLSReports->numberFormat(array_sum($final_comp_arr[$start]),2,1);
					$ins_html_data .='<td class="text_10 txt_r">'.$tmp_val.'</td>';
					$ins_pdf_data .='<td class="text_10" style="text-align:right; width:'.$each_col_wd_px.'px">'.$tmp_val.'</td>';
					$csv_ar_data.="\"".$tmp_val."\"".$pfx;
					$a += $aggingCycle;
				}
				if($ins_html_data!="" && array_sum($ins_total_due[$arr_key])>0){
					if((array_sum($ins_total_due[$arr_key])>=$_POST['balance_from'] || $_POST['balance_from']<=0) && (array_sum($ins_total_due[$arr_key])<=$_POST['balance_to'] || $_POST['balance_to']<=0)){

						if($ff==0){
							$html_body = $pdf_body = '';
							$csv_data.=$group_by.$pfx.$main_csv_data;
							$html_body .='<table id="ar_summ_tbl" class="rpt_table_det rpt rpt_table-bordered table-hover" style="width:100%">';
							$pdf_header .='<table class="rpt_table rpt rpt_table-bordered" style="width:'.$printPdfWidth.'px">';

							$html_body .='<thead><tr><td class="text_b_w txt_c" style="width:30%" onClick="ar_ord_by(\''.$group_by.'_ord\')">'.$group_by.$show_ord_sign[$group_by.'_ord'].'</td>'.$main_html_body;
							$pdf_header .='<tr><td class="text_b_w" style="text-align:center; width:270px">'.$group_by.'</td>'.$main_pdf_header;

							$html_body .='<td class="text_b_w" style="text-align:right; width:10%" onClick="ar_ord_by(\'aging_balance\')">Balance'.$show_ord_sign['aging_balance'].'</td></tr></thead><tbody>';
							$pdf_header .='<td class="text_b_w" style="text-align:right; width:110px">Balance</td></tr>';
							$pdf_header.='</table>';
							$pdf_body .='<table cellpadding="1" cellspacing="1" bgcolor="#FFF3E8" border="0" style="width:'.$printPdfWidth.'px">';

							$csv_data.="Balance$pfx \n";

						}
						$ff++;

						$tmp_val=0;
						$tmp_val=$CLSReports->numberFormat(array_sum($ins_total_due[$arr_key]),2,1);
						#HTML
						$html_body .='<tr><td class="text_10 txt_l srh_ins pointer text_purple" data-id="'.$arr_key.'" data-fax="'.$insCompArr[$ins_id]['fax'].'" data-email="'.$insCompArr[$ins_id]['email'].'" data-name="'.$insCompArr[$ins_id]['name'].'">'.$show_arr_name.'</a></td>
						'.$ins_html_data.'
						<td class="text_10 txt_r">'.$tmp_val.'</td></tr>';
						#PDF
						$pdf_body .='<tr><td class="text_10 txt_l" style="width:270px">'.$show_arr_name.'</td>
						'.$ins_pdf_data.'
						<td class="text_10" style="text-align:right;width:110px">'.$tmp_val.'</td></tr>';
						#CSV
						$csv_data.="\"".$show_arr_name."\"".$pfx;
						$csv_data.=$csv_ar_data;
						$csv_data.="\"".$tmp_val."\"".$pfx."\n";
						$ins_grand_due[]=array_sum($ins_total_due[$arr_key]);
					}
				}
			}
		}
	}

	if(array_sum($ins_grand_due)>0){
		$html_tr_content .='<tr>';
		$html_tr_content .='<td class="text_b_w" style="text-align:right;">Total</td>';
		$csv_data.="Total".$pfx;
		for($a=$aging_start;$a<=$aging_to;$a++){
			$start = $a;
			$a = $a > 0 ? $a - 1 : $a;
			$end = ($a) + $aggingCycle;
			$tmp_val=0;
			$tmp_val=$CLSReports->numberFormat(array_sum($ar_ins_total_due[$start]),2,1);
			$html_tr_content .='<td class="text_b_w txt_r">'.$tmp_val.'</td>';
			$csv_data.="\"".$tmp_val."\"".$pfx;
			$a += $aggingCycle;
		}
		$tmp_val=0;
		$tmp_val=$CLSReports->numberFormat(array_sum($ins_grand_due),2,1);
		$html_tr_content .='<td class="text_b_w txt_r">'.$tmp_val.'</td></tr>';
		$csv_data.="\"".$tmp_val."\"".$pfx."\n";

		$html_tr_content .='</tbody></table>';
		$html_body.=$html_tr_content;
		$pdf_body.=str_replace('</tbody>','',$html_tr_content);
	}
?>
