<?php

	$sql_check = "
		SELECT 
	
		c2.sph as sph_r, c2.cyl as cyl_r, c2.axs as axs_r, c2.ad as ad_r, c2.prsm_p as prsm_p_r, 
		c2.prism as prism_r, c2.slash as slash_r, c2.sel_1 as sel_1_r, c2.sel_2 as sel_2_r, 
		c2.txt_1 as txt_1_r, c2.txt_2 as txt_2_r, c2.sel2v as sel2v_r,
					
		c3.sph as sph_l, c3.cyl as cyl_l, c3.axs as axs_l, c3.ad as ad_l, c3.prsm_p as prsm_p_l, 
		c3.prism as prism_l, c3.slash as slash_l, c3.sel_1 as sel_1_l, c3.sel_2 as sel_2_l, 
		c3.txt_1 as txt_1_l, c3.txt_2 as txt_2_l, c3.sel2v as sel2v_l
					
		FROM chart_master_table c4
		INNER JOIN chart_vis_master c0 ON c4.id = c0.form_id
		LEFT JOIN chart_pc_mr c1 ON c1.id_chart_vis_master = c0.id	
		LEFT JOIN chart_pc_mr_values c2 ON c1.id = c2.chart_pc_mr_id AND c2.site='OD'
		LEFT JOIN chart_pc_mr_values c3 ON c1.id = c3.chart_pc_mr_id AND c3.site='OS'	
		WHERE c4.finalize='0' AND c4.patient_id = '$patient_id' 
		AND c1.mr_none_given!='' AND mr_none_given!='None'
		AND c1.ex_type='MR' AND c1.delete_by='0'  
		Order By c4.date_of_service DESC, c4.id DESC, c1.ex_number
		LIMIT 1;
	";			 
	$res_check = imw_query($sql_check);
	$num_rows_check = imw_num_rows($res_check);
	if($num_rows_check > 0){
		//return $res_check;
	}		 
	else{
		
		$sql_check = "
				SELECT 
			
				c2.sph as sph_r, c2.cyl as cyl_r, c2.axs as axs_r, c2.ad as ad_r, c2.prsm_p as prsm_p_r, 
				c2.prism as prism_r, c2.slash as slash_r, c2.sel_1 as sel_1_r, c2.sel_2 as sel_2_r, 
				c2.txt_1 as txt_1_r, c2.txt_2 as txt_2_r, c2.sel2v as sel2v_r,
							
				c3.sph as sph_l, c3.cyl as cyl_l, c3.axs as axs_l, c3.ad as ad_l, c3.prsm_p as prsm_p_l, 
				c3.prism as prism_l, c3.slash as slash_l, c3.sel_1 as sel_1_l, c3.sel_2 as sel_2_l, 
				c3.txt_1 as txt_1_l, c3.txt_2 as txt_2_l, c3.sel2v as sel2v_l
							
				FROM chart_master_table c4
				INNER JOIN chart_vis_master c0 ON c4.id = c0.form_id
				LEFT JOIN chart_pc_mr c1 ON c1.id_chart_vis_master = c0.id	
				LEFT JOIN chart_pc_mr_values c2 ON c1.id = c2.chart_pc_mr_id AND c2.site='OD'
				LEFT JOIN chart_pc_mr_values c3 ON c1.id = c3.chart_pc_mr_id AND c3.site='OS'	
				WHERE c4.finalize='1' AND c4.patient_id = '$patient_id' 
				AND c1.mr_none_given!='' AND mr_none_given!='None'
				AND c1.ex_type='MR' AND c1.delete_by='0'  
				Order By c4.date_of_service DESC, c4.id DESC, c1.ex_number
				LIMIT 1;
			";		
				
		$res_check = imw_query($sql);
		$num_rows_check = imw_num_rows($res_check);
		if($num_rows_check > 0){
			
		}else{
			$sql_check = "
				SELECT 
			
				c2.sph as sph_r, c2.cyl as cyl_r, c2.axs as axs_r, c2.ad as ad_r, c2.prsm_p as prsm_p_r, 
				c2.prism as prism_r, c2.slash as slash_r, c2.sel_1 as sel_1_r, c2.sel_2 as sel_2_r, 
				c2.txt_1 as txt_1_r, c2.txt_2 as txt_2_r, c2.sel2v as sel2v_r,
							
				c3.sph as sph_l, c3.cyl as cyl_l, c3.axs as axs_l, c3.ad as ad_l, c3.prsm_p as prsm_p_l, 
				c3.prism as prism_l, c3.slash as slash_l, c3.sel_1 as sel_1_l, c3.sel_2 as sel_2_l, 
				c3.txt_1 as txt_1_l, c3.txt_2 as txt_2_l, c3.sel2v as sel2v_l
							
				FROM chart_master_table c4
				INNER JOIN chart_vis_master c0 ON c4.id = c0.form_id
				LEFT JOIN chart_pc_mr c1 ON c1.id_chart_vis_master = c0.id	
				LEFT JOIN chart_pc_mr_values c2 ON c1.id = c2.chart_pc_mr_id AND c2.site='OD'
				LEFT JOIN chart_pc_mr_values c3 ON c1.id = c3.chart_pc_mr_id AND c3.site='OS'	
				WHERE c4.finalize='0' AND c4.patient_id = '$patient_id' 				
				AND c1.ex_type='MR' AND c1.delete_by='0'  
				AND (c2.sph!='' OR c2.cyl!='' OR c2.axs!='' OR c2.ad!='' OR c3.sph!='' OR c3.cyl!='' OR c3.axs!='' OR c3.ad!='')
				Order By c4.date_of_service DESC, c4.id DESC, c1.ex_number
				LIMIT 1;
			";			 
			$res_check = imw_query($sql_check);
			$num_rows_check = imw_num_rows($res_check);
			if($num_rows_check > 0){
				//return $res_check;
			}else{
				$sql_check = "
						SELECT 
					
						c2.sph as sph_r, c2.cyl as cyl_r, c2.axs as axs_r, c2.ad as ad_r, c2.prsm_p as prsm_p_r, 
						c2.prism as prism_r, c2.slash as slash_r, c2.sel_1 as sel_1_r, c2.sel_2 as sel_2_r, 
						c2.txt_1 as txt_1_r, c2.txt_2 as txt_2_r, c2.sel2v as sel2v_r,
									
						c3.sph as sph_l, c3.cyl as cyl_l, c3.axs as axs_l, c3.ad as ad_l, c3.prsm_p as prsm_p_l, 
						c3.prism as prism_l, c3.slash as slash_l, c3.sel_1 as sel_1_l, c3.sel_2 as sel_2_l, 
						c3.txt_1 as txt_1_l, c3.txt_2 as txt_2_l, c3.sel2v as sel2v_l
									
						FROM chart_master_table c4
						INNER JOIN chart_vis_master c0 ON c4.id = c0.form_id
						LEFT JOIN chart_pc_mr c1 ON c1.id_chart_vis_master = c0.id	
						LEFT JOIN chart_pc_mr_values c2 ON c1.id = c2.chart_pc_mr_id AND c2.site='OD'
						LEFT JOIN chart_pc_mr_values c3 ON c1.id = c3.chart_pc_mr_id AND c3.site='OS'	
						WHERE c4.finalize='1' AND c4.patient_id = '$patient_id' 						
						AND c1.ex_type='MR' AND c1.delete_by='0'  
						AND (c2.sph!='' OR c2.cyl!='' OR c2.axs!='' OR c2.ad!='' OR c3.sph!='' OR c3.cyl!='' OR c3.axs!='' OR c3.ad!='')
						Order By c4.date_of_service DESC, c4.id DESC, c1.ex_number
						LIMIT 1;
					";		
						
				$res_check = imw_query($sql);
				$num_rows_check = imw_num_rows($res_check);
				if($num_rows_check > 0){
					//return $res;
				}else{
					$res_check = false;
				}				
			}		
		}		
	}
	
	if($res_check!=false){
		$rowVal = imw_fetch_array($res_check);
		$sphere_od1 = $rowMRval['sph_r'];
		$sphere_os1 = $rowMRval['sph_l'];
		$cyl_od1 = $rowMRval['cyl_r'];
		$cyl_os1 = $rowMRval['cyl_l'];
		$axis_od1 = $rowMRval['axs_r'];
		$axis_os1 = $rowMRval['axs_l'];
		$add_od1 = $rowMRval['ad_r'];
		$add_os1 = $rowMRval['ad_l'];		
	}
?>
<div id="previousVal" style="display:none; position:absolute; left:10px; top:1px; background-color:#FFFF99; height:50px; width:200px;">
	<table class="standardTable">
	<tr style="background-color:#CC9900">
		<td style="width:237px; height:12px;" class="text center">Vision</td>
		<td style="width:226px;" class="text center">Sphere</td>
		<td style="width:177px;" class="text center">Cyl</td>
		<td style="width:179px;" class="text center">Axis</td>
		<td style="width:174px;" class="text center">Add</td>
	</tr>
	<tr>
		<td class="blue_color txt_10b center" >OD</td>
		<td class="center"><?php echo $sphere_od1; ?></td>
		<td class="center"><?php echo $cyl_od1; ?></td>
		<td class="center"><?php echo $axis_od1; ?></td>
		<td class="center"><?php echo $add_od1; ?></td>
	</tr>
	<tr>
		<td class="green_color txt_10b center">OS</td>
		<td class="center"><?php echo $sphere_os1; ?></td>
		<td class="center"><?php echo $cyl_os1; ?></td>
		<td class="center"><?php echo $axis_os1; ?></td>
		<td class="center"><?php echo $add_os1; ?></td>
	</tr>
</table>
</div>