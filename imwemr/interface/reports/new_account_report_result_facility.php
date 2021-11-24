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
FILE : new_acount_repotr_result_facility.php
PURPOSE :  FACILITY WISE ACCOUNT REPORT
ACCESS TYPE : INCLUDED
*/
			if($processReport=="Detail"){
				$top_pad="15mm";
				$page_data ='
				<tr>
					<td style="width:20px; text-align:center;" class="text_b_w">#</td>
					<td style="width:50px; text-align:center;" class="text_b_w">Facility</td>
					<td style="width:250px; text-align:center;" class="text_b_w">Patient Name - ID</td>
					<td style="width:80px; text-align:center;" class="text_b_w">Appt Created Date</td>
					<td style="width:80px; text-align:center;" class="text_b_w">Appt Date</td>
					<td style="width:100px; text-align:center;" class="text_b_w">Appointment Type</td>
					<td style="width:180px; text-align:center;" class="text_b_w">Physician Name</td>
					<td style="width:120px; text-align:center;" class="text_b_w">Heard About us</td>
					<td style="width:130px; text-align:center;" class="text_b_w">Pat Created Date</td>
					<td style="width:60px; text-align:center;" class="text_b_w">Operator</td>
				</tr>';
				
				$page_title_header='<table width="100%" cellpadding="1" cellspacing="1" border="0" bgcolor="#FFF3E8" class="rpt_padding">						
						<tr>
							<td style="width:20px; text-align:center;" class="text_b_w">#</td>
							<td style="width:80px; text-align:center;" class="text_b_w">Facility</td>
							<td style="width:200px; text-align:center;" class="text_b_w">Patient Name - ID</td>
							<td style="width:80px; text-align:center;" class="text_b_w">Appt Created Date</td>
							<td style="width:80px; text-align:center;" class="text_b_w">Appt Date</td>
							<td style="width:100px; text-align:center;" class="text_b_w">Appointment Type</td>
							<td style="width:180px; text-align:center;" class="text_b_w">Physician Name</td>
							<td style="width:100px; text-align:center;" class="text_b_w">Heard About us</td>
							<td style="width:130px; text-align:center;" class="text_b_w">Pat Created Date</td>
							<td style="width:60px; text-align:center;" class="text_b_w">Operator</td>
						</tr>
					</table>';
				$st_no=0;	
				$gr_fac_total_data=0;
				$gr_heard_abt_us_id_arr=array();
				$gr_heard_abt_us_name_arr=array();
				$gr_sch_app_arr=array();
				$gr_sch_app_data_arr=array();
				for($l=0;$l<count($fac_ids_arr);$l++){	
					$fac_id=$fac_ids_arr[$l];
					$fac_name=$fac_id_data_arr[$fac_id];
					$fac_total_data=0;
					//$heard_abt_us_id_arr=array();
					//$heard_abt_us_name_arr=array();
					//$sch_app_arr=array();
					//$sch_app_data_arr=array();
					if($fac_based_data_arr[$fac_id]){

						for($i=0;$i<count($fac_based_data_arr[$fac_id]);$i++){	
							$mainQryRes_data=$fac_based_data_arr[$fac_id];
							$st_no=$st_no+1;
							$fac_total_data=$fac_total_data+1;
							$gr_fac_total_data=$gr_fac_total_data+1;
							$pt_id = $mainQryRes_data[$i]['id'];
							$heard_abt_us_name = $heard_arr[$mainQryRes_data[$i]['heard_abt_us']];

							$pat_date = $mainQryRes_data[$i]['pat_date'];
							if($mainQryRes[$i]['created_by']>0){
								$created_by = $mainQryRes_data[$i]['created_by'];
							}else{
								$created_by = 0;
							}
							
							$pat_name = "";
							$pat_name_arr = array();
							$pat_name_arr['LAST_NAME'] = $mainQryRes_data[$i]['lname'];
							$pat_name_arr['FIRST_NAME'] = $mainQryRes_data[$i]['fname'];
							$pat_name_arr['MIDDLE_NAME'] = $mainQryRes_data[$i]['mname'];		
							$pat_name= changeNameFormat($pat_name_arr);	
							$pat_name = $pat_name.' - '.$pt_id;	
							
							$heard_abt_us_id_arr[$mainQryRes_data[$i]['heard_abt_us']]=$mainQryRes_data[$i]['heard_abt_us'];
							$heard_abt_us_name_arr[$fac_id][$mainQryRes_data[$i]['heard_abt_us']][]= $mainQryRes_data[$i]['heard_abt_us'];
							$gr_heard_abt_us_id_arr[$mainQryRes_data[$i]['heard_abt_us']]=$mainQryRes_data[$i]['heard_abt_us'];
							$gr_heard_abt_us_name_arr[$mainQryRes_data[$i]['heard_abt_us']][]= $mainQryRes_data[$i]['heard_abt_us'];
							
							$sch_app_arr[$sch_data_arr[$pt_id]['appt_proc']]=$sch_data_arr[$pt_id]['appt_proc'];
							$sch_app_data_arr[$fac_id][$sch_data_arr[$pt_id]['appt_proc']][]= $sch_data_arr[$pt_id]['appt_proc'];
							$gr_sch_app_arr[$sch_data_arr[$pt_id]['appt_proc']]=$sch_data_arr[$pt_id]['appt_proc'];
							$gr_sch_app_data_arr[$sch_data_arr[$pt_id]['appt_proc']][]= $sch_data_arr[$pt_id]['appt_proc'];
							
							$page_data .='<tr style="background-color:#ffffff;">
								<td style="text-align:left;width:20px;" class="text_10">'.$st_no.'</td>
								<td style="text-align:left;width:100px;" class="text_10">'.$sch_data_arr[$pt_id]['facility'].'</td>								
								<td style="text-align:left;width:250px" class="text_10">'.$pat_name.'</td>
								<td style="text-align:left;width:80px;" class="text_10">'.$arr_appt_created_date[$pt_id].'</td>
								<td style="text-align:left;width:150px;" class="text_10">'.$sch_data_arr[$pt_id]['appt_date'].'</td>
								<td style="text-align:left;width:100px;" class="text_10">'.$sch_data_arr[$pt_id]['appt_proc'].'</td>
								<td style="text-align:left;width:250px;" class="text_10">'.$sch_data_arr[$pt_id]['physician'].'</td>
								<td style="text-align:left;width:120px;" class="text_10">'.$heard_abt_us_name.'</td>
								<td style="text-align:left;width:150px;" class="text_10">'.$pat_date.'</td>
								<td style="text-align:left;width:40px;" class="text_10">'.$opr_ins[$created_by].'</td>
							</tr>';	
							$page_data2 .='<tr style="background-color:#ffffff;">
								<td style="text-align:left;width:20px;" class="text_10">'.$st_no.'</td>
								<td style="text-align:left;width:80px;" class="text_10">'.$sch_data_arr[$pt_id]['facility'].'</td>
								<td style="text-align:left;width:200px" class="text_10">'.$pat_name.'</td>
								<td style="text-align:left;width:80px;" class="text_10">'.$arr_appt_created_date[$pt_id].'</td>
								<td style="text-align:left;width:80px;" class="text_10">'.$sch_data_arr[$pt_id]['appt_date'].'</td>
								<td style="text-align:left;width:100px;" class="text_10">'.$sch_data_arr[$pt_id]['appt_proc'].'</td>
								<td style="text-align:left;width:180px;" class="text_10">'.$sch_data_arr[$pt_id]['physician'].'</td>
								<td style="text-align:left;width:100px;" class="text_10">'.$heard_abt_us_name.'</td>
								<td style="text-align:left;width:130px;" class="text_10">'.$pat_date.'</td>
								<td style="text-align:left;width:60px;" class="text_10">'.$opr_ins[$created_by].'</td>
							</tr>';	
							
						}
					}
				}
						
/*						$heard_abt_us_id_arr=array_values($heard_abt_us_id_arr);
						
						//print_r($heard_abt_us_id_arr);	
						$hrd_data="";
						$hrd_data_none="";
						$tot_hrd_arr=array();
						$hrd_row=0;
						$hrd_title="";
						$hrd_data .='<tr style="background-color:#ffffff;">';
						if(count($heard_abt_us_id_arr)>0){
							for($b=0;$b<count($heard_abt_us_id_arr);$b++){
								$hrd_id=$heard_abt_us_id_arr[$b];
								$tot_hrd=count($heard_abt_us_name_arr[$fac_id][$hrd_id]);
								if($heard_arr[$hrd_id]){
									$hrd_row=$hrd_row+1;
									if($hrd_row>0 && $hrd_row<3){
										$hrd_title .='
										<td style="text-align:left;" class="text_10b" colspan="3">Heard about us</td>';
									}
									$tot_hrd_arr[]=$tot_hrd;
									$hrd_data .='<td style="text-align:left;" class="text_10" colspan="3">
									'.$heard_arr[$hrd_id].' : '.$tot_hrd.'</td>';
									if($hrd_row%3==0){
										$hrd_data .='</tr><tr style="background-color:#ffffff;">';
									}
								}
							}
						}
						
						
						$sch_app_arr=array_values($sch_app_arr);
						$sch_sum_data="";
						$sch_sum_data_none="";
						$noApp="";
						$tot_proc_arr=array();
						$sch_row=0;
						$sch_title="";
						$sch_sum_data .='<tr style="background-color:#ffffff;">';
						for($s=0;$s<count($sch_app_arr);$s++){
							$proc_name=$sch_app_arr[$s];
							$tot_proc=count($sch_app_data_arr[$fac_id][$proc_name]);
							if($proc_name){
								$sch_row=$sch_row+1;
								if($sch_row>0 && $sch_row<3){
									$sch_title .='<td style="text-align:left;" class="text_10b" colspan="3">Procedure Type</td>';
								}
								$tot_proc_arr[]=$tot_proc;
								$sch_sum_data .='<td style="text-align:left;" class="text_10" colspan="3">
								'.$proc_name.' : '.$tot_proc.'</td>';
								if($sch_row%3==0){
									$sch_sum_data .='</tr><tr style="background-color:#ffffff;">';
								}
							}
						}
						
						$no_hrd=$fac_total_data-array_sum($tot_hrd_arr);
						if($no_hrd>0){
							$hrd_no_cols = 9;
							if($hrd_row%3 == 1){
								$hrd_no_cols = 6;								
							}
							if($hrd_row%3 == 2){
								$hrd_no_cols = 3;								
							}
							$hrd_title .='<td style="text-align:left;" class="text_10b" colspan="'.$hrd_no_cols.'">Heard about us</td>';
							$hrd_data_none .='
									<td style="text-align:left;" class="text_10" colspan="'.$hrd_no_cols.'">None : '.$no_hrd.'</td></tr>
									<tr style="background-color:#ffffff;">
									<td style="text-align:left;" class="text_10" colspan="9">&nbsp;</td></tr>';
						}else{
							$hrd_no_cols = 9;
							if($hrd_row%3 == 1){
								$hrd_no_cols = 6;								
							}
							if($hrd_row%3 == 2){
								$hrd_no_cols = 3;								
							}
							$hrd_title .='<td style="text-align:left;" class="text_10b" colspan="'.$hrd_no_cols.'">&nbsp;</td>';
							$hrd_data_none .='
									<td style="text-align:left;" class="text_10" colspan="'.$hrd_no_cols.'">&nbsp;</td></tr>
									<tr style="background-color:#ffffff;">
									<td style="text-align:left;" class="text_10" colspan="9">&nbsp;</td></tr>';
						}
								
						$noApp=$fac_total_data-array_sum($tot_proc_arr);	
						if($noApp>0){	
							$sch_no_cols = 9;
							if($sch_row%3 == 1){
								$sch_no_cols = 6;								
							}
							if($sch_row%3 == 2){
								$sch_no_cols = 3;								
							}
							$sch_title .='<td style="text-align:left;" class="text_10b" colspan="'.$sch_no_cols.'">Procedure Type</td>';
							$sch_sum_data_none .='<td style="text-align:left;" class="text_10" colspan="'.$sch_no_cols.'">None : '.$noApp.'</td></tr>';
						}else{
							$sch_no_cols = 9;
							if($sch_row%3 == 1){
								$sch_no_cols = 6;								
							}
							if($sch_row%3 == 2){
								$sch_no_cols = 3;								
							}
							$sch_title .='<td style="text-align:left;" class="text_10b" colspan="'.$sch_no_cols.'">&nbsp;</td>';
							$sch_sum_data_none .='<td style="text-align:left;" class="text_10" colspan="'.$sch_no_cols.'">&nbsp;</td></tr>';
						}
						
						
						$page_data .='<tr style="background-color:#ffffff;">
							<td style="text-align:left;" class="text_10b" colspan="9">Total Number of New Account(s) : '.$fac_total_data.'</td></tr>
							<tr style="background-color:#ffffff;">
							<td style="text-align:left;" class="text_10b" colspan="9">&nbsp;</td></tr>';	
							
						$page_data .='<tr style="background-color:#ffffff;">'.$hrd_title.'</tr>';				
						$page_data .=$hrd_data.$hrd_data_none;
						$page_data .='<tr style="background-color:#ffffff;">'.$sch_title.'</tr>';	
						$page_data .=$sch_sum_data.$sch_sum_data_none;
						
						$page_data2 .='<tr>
							<td style="text-align:left;" class="text_10b" colspan="9">Total Number of New Account(s) : '.$fac_total_data.'</td></tr>
							<tr style="background-color:#ffffff;">
								<td style="text-align:left;" class="text_10b" colspan="9">&nbsp;</td></tr>';
						$page_data2 .='<tr style="background-color:#ffffff;">'.$hrd_title.'</tr>';		
						$page_data2 .=$hrd_data.$hrd_data_none;
						
						$page_data2 .='<tr style="background-color:#ffffff;">'.$sch_title.'</tr>';	
						$page_data2 .=$sch_sum_data.$sch_sum_data_none;*/


					//GRAND TOTALS
					$totCols=10;
					$gr_heard_abt_us_id_arr=array_values($gr_heard_abt_us_id_arr);		
					$gr_tot_hrd_arr=array();
					$hrd_data_none="";
					$gr_hrd_row=0;
					$gr_hrd_title="";
					$gr_hrd_data .='<tr style="background-color:#ffffff;">';
					
					for($g=0;$g<count($gr_heard_abt_us_id_arr);$g++){
						$hrd_id_main=$gr_heard_abt_us_id_arr[$g];
						$tot_hrd_main=count($gr_heard_abt_us_name_arr[$hrd_id_main]);
						if($heard_arr[$hrd_id_main]){
							$gr_tot_hrd_arr[]=$tot_hrd_main;
							
							$gr_hrd_row=$gr_hrd_row+1;

							$tdcolspan=($gr_hrd_row%3==0)? 4 : 3;
							if($gr_hrd_row>0 && $gr_hrd_row<3){
								
								$gr_hrd_title .='
								<td style="text-align:left;" class="text_10b" colspan="'.$tdcolspan.'"><strong>Heard about us</strong></td>';
							}
							
							$gr_hrd_data .='
							<td style="text-align:left;" class="text_10" colspan="'.$tdcolspan.'">
							'.$heard_arr[$hrd_id_main].' : '.$tot_hrd_main.'</td>';
							
							if($gr_hrd_row%3==0){
								$gr_hrd_data .='</tr><tr style="background-color:#ffffff;">';
							}
						}
					}
					
					$gr_no_hrd=$gr_fac_total_data-array_sum($gr_tot_hrd_arr);
					if($gr_no_hrd>0){
/*						$gr_hrd_no_cols = 10;
						if($gr_hrd_row%3 == 1){
							$gr_hrd_no_cols = 6;								
						}
						if($gr_hrd_row%3 == 2){
							$gr_hrd_no_cols = 3;								
						}
*/						$gr_hrd_title .='<td style="text-align:left;" class="text_10b" colspan="'.$gr_hrd_no_cols.'"><strong>Heard about us</strong></td>';
						$hrd_data_none .='
								<td style="text-align:left;" class="text_10" colspan="'.$gr_hrd_no_cols.'">None : '.$gr_no_hrd.'</td></tr>
								<tr style="background-color:#ffffff;">
								<td style="text-align:left;" class="text_10" colspan="10">&nbsp;</td></tr>';
					}else{
						$gr_hrd_no_cols = 10;
						if($gr_hrd_row%3 == 1){
							$gr_hrd_no_cols = 6;								
						}
						if($gr_hrd_row%3 == 2){
							$gr_hrd_no_cols = 3;								
						}
						$gr_hrd_title .='<td style="text-align:left;" class="text_10b" colspan="'.$gr_hrd_no_cols.'">&nbsp;</td>';
						$hrd_data_none .='
								<td style="text-align:left;" class="text_10" colspan="'.$gr_hrd_no_cols.'">&nbsp;</td></tr>
								<tr style="background-color:#ffffff;">
								<td style="text-align:left;" class="text_10" colspan="10">&nbsp;</td></tr>';
					}
					
					$gr_sch_app_arr=array_values($gr_sch_app_arr);
					$gr_sch_sum_data_none="";
					$gr_tot_proc_arr=array();
					$gr_sch_row=0;
					$gr_sch_title="";
					$gr_sch_sum_data .='<tr style="background-color:#ffffff;">';
					
					for($n=0;$n<count($gr_sch_app_arr);$n++){
						$gr_proc_name=$gr_sch_app_arr[$n];
						$gr_tot_proc=count($gr_sch_app_data_arr[$gr_proc_name]);
						if($gr_proc_name){
							$gr_tot_proc_arr[]=$gr_tot_proc;
							$gr_sch_row=$gr_sch_row+1;
							
							if($gr_sch_row>0 && $gr_sch_row<3){
								$gr_sch_title .='
								<td style="text-align:left;" class="text_10b" colspan="3"><strong>Procedure Type</strong></td>';
							}
							
							$gr_sch_sum_data .='<td style="text-align:left;" class="text_10" colspan="3">
							'.$gr_proc_name.' : '.$gr_tot_proc.'</td>';
							
							if($gr_sch_row%3==0){
								$gr_sch_sum_data .='</tr><tr style="background-color:#ffffff;">';
							}
						}
					}
					
					$gr_noApp=$gr_fac_total_data-array_sum($gr_tot_proc_arr);	
					if($gr_noApp>0){
						$sch_no_cols = 10;
						if($gr_sch_row%3 == 1){
							$sch_no_cols = 6;								
						}
						if($gr_sch_row%3 == 2){
							$sch_no_cols = 3;								
						}	
						$gr_sch_title .='<td style="text-align:left;" class="text_10b" colspan="'.$sch_no_cols.'"><strong>Procedure Type</strong></td>';
						$gr_sch_sum_data_none .='
							<td style="text-align:left;" class="text_10" colspan="'.$sch_no_cols.'">None : '.$gr_noApp.'</td></tr>';
					}else{
						$sch_no_cols = 10;
						if($gr_sch_row%3 == 1){
							$sch_no_cols = 6;								
						}
						if($gr_sch_row%3 == 2){
							$sch_no_cols = 3;								
						}	
						$gr_sch_title .='<td style="text-align:left;" class="text_10b" colspan="'.$sch_no_cols.'">&nbsp;</td>';
						$gr_sch_sum_data_none .='<td style="text-align:left;" class="text_10b" colspan="'.$sch_no_cols.'">&nbsp;</td></tr>';
					}
						
					$page_data .='<tr style="background-color:#ffffff;">
						<td style="text-align:left;" class="text_10b" colspan="10">&nbsp;</td></tr><tr style="background-color:#ffffff;">
						<td style="text-align:left;" class="text_b_w" colspan="10">Grand Total Number of New Account(s) : '.$gr_fac_total_data.'</td></tr>
						<tr style="background-color:#ffffff;">
						<td style="text-align:left;" class="text_10b" colspan="10">&nbsp;</td></tr>';
		
					$page_data .='<tr style="background-color:#ffffff;">'.$gr_hrd_title.'</tr>';				
					
					$page_data .=$gr_hrd_data.$hrd_data_none;
					
					$page_data .='<tr style="background-color:#ffffff;">'.$gr_sch_title.'</tr>';	
					
					$page_data .=$gr_sch_sum_data.$gr_sch_sum_data_none;
					
					$page_data2 .='<tr style="background-color:#ffffff;">
						<td style="text-align:left;" class="text_10b" colspan="10">&nbsp;</td></tr><tr>
						<td style="text-align:left;" class="text_b_w" colspan="10">Grand Total Number of New Account(s) : '.$gr_fac_total_data.'</td></tr>
						<tr style="background-color:#ffffff;">
							<td style="text-align:left;" class="text_10b" colspan="10">&nbsp;</td></tr>';
					$page_data2 .='<tr style="background-color:#ffffff;">'.$gr_hrd_title.'</tr>';		
					$page_data2 .=$gr_hrd_data.$hrd_data_none;
					
					$page_data2 .='<tr style="background-color:#ffffff;">'.$gr_sch_title.'</tr>';	
					$page_data2 .=$gr_sch_sum_data.$gr_sch_sum_data_none;
				
				}else{  //SUMMARY
	
				$page_title_header="";
				$top_pad="10mm";
				$st_no=0;	
				$gr_fac_total_data=0;
				$gr_heard_abt_us_id_arr=array();
				$gr_heard_abt_us_name_arr=array();
				$gr_sch_app_arr=array();
				$gr_sch_app_data_arr=array();
				for($l=0;$l<count($fac_ids_arr);$l++){	
					$fac_id=$fac_ids_arr[$l];
					$fac_name=$fac_id_data_arr[$fac_id];
					$fac_total_data=0;
					$heard_abt_us_id_arr=array();
					$heard_abt_us_name_arr=array();
					$sch_app_arr=array();
					$sch_app_data_arr=array();
					if($fac_based_data_arr[$fac_id]){
						$page_data .='
							<tr style="background-color:#ffffff;">
								<td style="text-align:left;width:30px;" class="text_10"></td>
								<td style="text-align:left;width:220px" class="text_10"></td>
								<td style="text-align:left;width:150px;" class="text_10"></td>
								<td style="text-align:left;width:100px;" class="text_10"></td>
								<td style="text-align:left;width:180px;" class="text_10"></td>
								<td style="text-align:left;width:80px;" class="text_10"></td>
								<td style="text-align:left;width:100px;" class="text_10"></td>
								<td style="text-align:left;width:130px;" class="text_10"></td>
								<td style="text-align:left;width:40px;" class="text_10"></td>
							</tr>
							<tr>
								<td style="text-align:left; height:20px;" class="text_b_w" colspan="9">Facility : '.$fac_name.'</td>
							</tr>';
						$page_data2 .='
						<tr style="background-color:#ffffff;">
							<td style="text-align:left;width:30px;" class="text_10"></td>
							<td style="text-align:left;width:220px" class="text_10"></td>
							<td style="text-align:left;width:150px;" class="text_10"></td>
							<td style="text-align:left;width:100px;" class="text_10"></td>
							<td style="text-align:left;width:180px;" class="text_10"></td>
							<td style="text-align:left;width:80px;" class="text_10"></td>
							<td style="text-align:left;width:100px;" class="text_10"></td>
							<td style="text-align:left;width:130px;" class="text_10"></td>
							<td style="text-align:left;width:40px;" class="text_10"></td>
						</tr>
						<tr><td style="text-align:left;height:20px;width:1060px;" class="text_b_w" colspan="9">Facility : '.$fac_name.'</td></tr>';		
						
						for($i=0;$i<count($fac_based_data_arr[$fac_id]);$i++){	
							$mainQryRes_data=$fac_based_data_arr[$fac_id];
							$st_no=$st_no+1;
							$fac_total_data=$fac_total_data+1;
							$gr_fac_total_data=$gr_fac_total_data+1;
							$pt_id = $mainQryRes_data[$i]['id'];
							
							$heard_abt_us_id_arr[$mainQryRes_data[$i]['heard_abt_us']]=$mainQryRes_data[$i]['heard_abt_us'];
							$heard_abt_us_name_arr[$fac_id][$mainQryRes_data[$i]['heard_abt_us']][]= $mainQryRes_data[$i]['heard_abt_us'];
							$gr_heard_abt_us_id_arr[$mainQryRes_data[$i]['heard_abt_us']]=$mainQryRes_data[$i]['heard_abt_us'];
							$gr_heard_abt_us_name_arr[$mainQryRes_data[$i]['heard_abt_us']][]= $mainQryRes_data[$i]['heard_abt_us'];
							
							$sch_app_arr[$sch_data_arr[$pt_id]['appt_proc']]=$sch_data_arr[$pt_id]['appt_proc'];
							$sch_app_data_arr[$fac_id][$sch_data_arr[$pt_id]['appt_proc']][]= $sch_data_arr[$pt_id]['appt_proc'];
							$gr_sch_app_arr[$sch_data_arr[$pt_id]['appt_proc']]=$sch_data_arr[$pt_id]['appt_proc'];
							$gr_sch_app_data_arr[$sch_data_arr[$pt_id]['appt_proc']][]= $sch_data_arr[$pt_id]['appt_proc'];
						}
						$heard_abt_us_id_arr=array_values($heard_abt_us_id_arr);
						
						//print_r($heard_abt_us_id_arr);	
						$hrd_data="";
						$hrd_data_none="";
						$tot_hrd_arr=array();
						$hrd_row=0;
						$hrd_title="";
						$hrd_data .='<tr style="background-color:#ffffff;">';
						if(count($heard_abt_us_id_arr)>0){
							for($b=0;$b<count($heard_abt_us_id_arr);$b++){
								$hrd_id=$heard_abt_us_id_arr[$b];
								$tot_hrd=count($heard_abt_us_name_arr[$fac_id][$hrd_id]);
								if($heard_arr[$hrd_id]){
									$hrd_row=$hrd_row+1;
									if($hrd_row>0 && $hrd_row<3){
										$hrd_title .='
										<td style="text-align:left;" class="text_10b" colspan="3">Heard about us</td>';
									}
									$tot_hrd_arr[]=$tot_hrd;
									$hrd_data .='<td style="text-align:left;" class="text_10" colspan="3">
									'.$heard_arr[$hrd_id].' : '.$tot_hrd.'</td>';
									if($hrd_row%3==0){
										$hrd_data .='</tr><tr style="background-color:#ffffff;">';
									}
								}
							}
						}
						
						
						$sch_app_arr=array_values($sch_app_arr);
						$sch_sum_data="";
						$sch_sum_data_none="";
						$noApp="";
						$tot_proc_arr=array();
						$sch_row=0;
						$sch_title="";
						$sch_sum_data .='<tr style="background-color:#ffffff;">';
						for($s=0;$s<count($sch_app_arr);$s++){
							$proc_name=$sch_app_arr[$s];
							$tot_proc=count($sch_app_data_arr[$fac_id][$proc_name]);
							if($proc_name){
								$sch_row=$sch_row+1;
								if($sch_row>0 && $sch_row<3){
									$sch_title .='<td style="text-align:left;" class="text_10b" colspan="3">Procedure Type</td>';
								}
								$tot_proc_arr[]=$tot_proc;
								$sch_sum_data .='<td style="text-align:left;" class="text_10" colspan="3">
								'.$proc_name.' : '.$tot_proc.'</td>';
								if($sch_row%3==0){
									$sch_sum_data .='</tr><tr style="background-color:#ffffff;">';
								}
							}
						}
						
						$no_hrd=$fac_total_data-array_sum($tot_hrd_arr);
						if($no_hrd>0){
							$hrd_no_cols = 9;
							if($hrd_row%3 == 1){
								$hrd_no_cols = 6;								
							}
							if($hrd_row%3 == 2){
								$hrd_no_cols = 3;								
							}
							$hrd_title .='<td style="text-align:left;" class="text_10b" colspan="'.$hrd_no_cols.'">Heard about us</td>';
							$hrd_data_none .='
									<td style="text-align:left;" class="text_10" colspan="'.$hrd_no_cols.'">None : '.$no_hrd.'</td></tr>
									<tr style="background-color:#ffffff;">
									<td style="text-align:left;" class="text_10" colspan="9">&nbsp;</td></tr>';
						}else{
							$hrd_no_cols = 9;
							if($hrd_row%3 == 1){
								$hrd_no_cols = 6;								
							}
							if($hrd_row%3 == 2){
								$hrd_no_cols = 3;								
							}
							$hrd_title .='<td style="text-align:left;" class="text_10b" colspan="'.$hrd_no_cols.'">&nbsp;</td>';
							$hrd_data_none .='
									<td style="text-align:left;" class="text_10" colspan="'.$hrd_no_cols.'">&nbsp;</td></tr>
									<tr style="background-color:#ffffff;">
									<td style="text-align:left;" class="text_10" colspan="9">&nbsp;</td></tr>';
						}
								
						$noApp=$fac_total_data-array_sum($tot_proc_arr);	
						if($noApp>0){	
							$sch_no_cols = 9;
							if($sch_row%3 == 1){
								$sch_no_cols = 6;								
							}
							if($sch_row%3 == 2){
								$sch_no_cols = 3;								
							}
							$sch_title .='<td style="text-align:left;" class="text_10b" colspan="'.$sch_no_cols.'">Procedure Type</td>';
							$sch_sum_data_none .='<td style="text-align:left;" class="text_10" colspan="'.$sch_no_cols.'">None : '.$noApp.'</td></tr>';
						}else{
							$sch_no_cols = 9;
							if($sch_row%3 == 1){
								$sch_no_cols = 6;								
							}
							if($sch_row%3 == 2){
								$sch_no_cols = 3;								
							}
							$sch_title .='<td style="text-align:left;" class="text_10b" colspan="'.$sch_no_cols.'">&nbsp;</td>';
							$sch_sum_data_none .='<td style="text-align:left;" class="text_10" colspan="'.$sch_no_cols.'">&nbsp;</td></tr>';
						}
						
						
						$page_data .='<tr style="background-color:#ffffff;">
							<td style="text-align:left;" class="text_10b" colspan="9">Total Number of New Account(s) : '.$fac_total_data.'</td></tr>
							<tr style="background-color:#ffffff;">
							<td style="text-align:left;" class="text_10b" colspan="9">&nbsp;</td></tr>';	
							
						$page_data .='<tr style="background-color:#ffffff;">'.$hrd_title.'</tr>';				
						$page_data .=$hrd_data.$hrd_data_none;
						$page_data .='<tr style="background-color:#ffffff;">'.$sch_title.'</tr>';	
						$page_data .=$sch_sum_data.$sch_sum_data_none;
						
						$page_data2 .='<tr>
							<td style="text-align:left;" class="text_10b" colspan="9">Total Number of New Account(s) : '.$fac_total_data.'</td></tr>
							<tr style="background-color:#ffffff;">
								<td style="text-align:left;" class="text_10b" colspan="9">&nbsp;</td></tr>';
						$page_data2 .='<tr style="background-color:#ffffff;">'.$hrd_title.'</tr>';		
						$page_data2 .=$hrd_data.$hrd_data_none;
						
						$page_data2 .='<tr style="background-color:#ffffff;">'.$sch_title.'</tr>';	
						$page_data2 .=$sch_sum_data.$sch_sum_data_none;
					}
				}
				$gr_heard_abt_us_id_arr=array_values($gr_heard_abt_us_id_arr);		
					$gr_tot_hrd_arr=array();
					$hrd_data_none="";
					$gr_hrd_row=0;
					$gr_hrd_title="";
					$gr_hrd_data .='<tr style="background-color:#ffffff;">';
					for($g=0;$g<count($gr_heard_abt_us_id_arr);$g++){
						$hrd_id_main=$gr_heard_abt_us_id_arr[$g];
						$tot_hrd_main=count($gr_heard_abt_us_name_arr[$hrd_id_main]);
						if($heard_arr[$hrd_id_main]){
							$gr_tot_hrd_arr[]=$tot_hrd_main;
							
							$gr_hrd_row=$gr_hrd_row+1;
							if($gr_hrd_row>0 && $gr_hrd_row<3){
								$gr_hrd_title .='
								<td style="text-align:left;" class="text_10b" colspan="3">Heard about us</td>';
							}
							
							$gr_hrd_data .='
							<td style="text-align:left;" class="text_10" colspan="3">
							'.$heard_arr[$hrd_id_main].' : '.$tot_hrd_main.'</td>';
							
							if($gr_hrd_row%3==0){
								$gr_hrd_data .='</tr><tr style="background-color:#ffffff;">';
							}
						}
					}
					
					$gr_no_hrd=$gr_fac_total_data-array_sum($gr_tot_hrd_arr);
					if($gr_no_hrd>0){
						$gr_hrd_no_cols = 9;
						if($gr_hrd_row%3 == 1){
							$gr_hrd_no_cols = 6;								
						}
						if($gr_hrd_row%3 == 2){
							$gr_hrd_no_cols = 3;								
						}
						$gr_hrd_title .='<td style="text-align:left;" class="text_10b" colspan="'.$gr_hrd_no_cols.'">Heard about us</td>';
						$hrd_data_none .='
								<td style="text-align:left;" class="text_10" colspan="'.$gr_hrd_no_cols.'">None : '.$gr_no_hrd.'</td></tr>
								<tr style="background-color:#ffffff;">
								<td style="text-align:left;" class="text_10" colspan="9">&nbsp;</td></tr>';
					}else{
						$gr_hrd_no_cols = 9;
						if($gr_hrd_row%3 == 1){
							$gr_hrd_no_cols = 6;								
						}
						if($gr_hrd_row%3 == 2){
							$gr_hrd_no_cols = 3;								
						}
						$gr_hrd_title .='<td style="text-align:left;" class="text_10b" colspan="'.$gr_hrd_no_cols.'">&nbsp;</td>';
						$hrd_data_none .='<td style="text-align:left;" class="text_10" colspan="'.$gr_hrd_no_cols.'">&nbsp;</td></tr>
								<tr style="background-color:#ffffff;">
								<td style="text-align:left;" class="text_10" colspan="9">&nbsp;</td></tr>';
					}
					
					$gr_sch_app_arr=array_values($gr_sch_app_arr);
					$gr_sch_sum_data_none="";
					$gr_tot_proc_arr=array();
					$gr_sch_row=0;
					$gr_sch_title="";
					$gr_sch_sum_data .='<tr style="background-color:#ffffff;">';
					for($n=0;$n<count($gr_sch_app_arr);$n++){
						$gr_proc_name=$gr_sch_app_arr[$n];
						$gr_tot_proc=count($gr_sch_app_data_arr[$gr_proc_name]);
						if($gr_proc_name){
							$gr_tot_proc_arr[]=$gr_tot_proc;
							$gr_sch_row=$gr_sch_row+1;
							
							if($gr_sch_row>0 && $gr_sch_row<3){
								$gr_sch_title .='
								<td style="text-align:left;" class="text_10b" colspan="3">Procedure Type</td>';
							}
							
							$gr_sch_sum_data .='<td style="text-align:left;" class="text_10" colspan="3">
							'.$gr_proc_name.' : '.$gr_tot_proc.'</td>';
							
							if($gr_sch_row%3==0){
								$gr_sch_sum_data .='</tr><tr style="background-color:#ffffff;">';
							}
						}
					}
					
					$gr_noApp=$gr_fac_total_data-array_sum($gr_tot_proc_arr);	
					if($gr_noApp>0){
						$sch_no_cols = 9;
						if($gr_sch_row%3 == 1){
							$sch_no_cols = 6;								
						}
						if($gr_sch_row%3 == 2){
							$sch_no_cols = 3;								
						}	
						$gr_sch_title .='<td style="text-align:left;" class="text_10b" colspan="'.$sch_no_cols.'">Procedure Type</td>';
						$gr_sch_sum_data_none .='
							<td style="text-align:left;" class="text_10" colspan="'.$sch_no_cols.'">None : '.$gr_noApp.'</td></tr>';
					}else{
						$sch_no_cols = 9;
						if($gr_sch_row%3 == 1){
							$sch_no_cols = 6;								
						}
						if($gr_sch_row%3 == 2){
							$sch_no_cols = 3;								
						}	
						$gr_sch_title .='<td style="text-align:left;" class="text_10b" colspan="'.$sch_no_cols.'">&nbsp;</td>';
						$gr_sch_sum_data_none .='<td style="text-align:left;" class="text_10b" colspan="'.$sch_no_cols.'">&nbsp;</td></tr>';
					}
						
					$page_data .='<tr style="background-color:#ffffff;">
						<td style="text-align:left;" class="text_10b" colspan="9">&nbsp;</td></tr><tr style="background-color:#ffffff;">
						<td style="text-align:left;" class="text_b_w" colspan="9">Grand Total Number of New Account(s) : '.$gr_fac_total_data.'</td></tr>
						<tr style="background-color:#ffffff;">
						<td style="text-align:left;" class="text_10b" colspan="9">&nbsp;</td></tr>';
		
					$page_data .='<tr style="background-color:#ffffff;">'.$gr_hrd_title.'</tr>';				
					
					$page_data .=$gr_hrd_data.$hrd_data_none;
					
					$page_data .='<tr style="background-color:#ffffff;">'.$gr_sch_title.'</tr>';	
					
					$page_data .=$gr_sch_sum_data.$gr_sch_sum_data_none;
					
					$page_data2 .='<tr style="background-color:#ffffff;">
						<td style="text-align:left;" class="text_10b" colspan="9">&nbsp;</td></tr><tr>
						<td style="text-align:left;" class="text_b_w" colspan="9">Grand Total Number of New Account(s) : '.$gr_fac_total_data.'</td></tr>
						<tr style="background-color:#ffffff;">
							<td style="text-align:left;" class="text_10b" colspan="9">&nbsp;</td></tr>';
					$page_data2 .='<tr style="background-color:#ffffff;">'.$gr_hrd_title.'</tr>';		
					$page_data2 .=$gr_hrd_data.$hrd_data_none;
					
					$page_data2 .='<tr style="background-color:#ffffff;">'.$gr_sch_title.'</tr>';	
					$page_data2 .=$gr_sch_sum_data.$gr_sch_sum_data_none;
				
			}
		
?>