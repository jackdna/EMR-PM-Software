<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
/*
File: appt_cl_funtions.php
Purpose: Define Class for Contact Lens
Access Type: Include
*/
class appt_contactlens{
	private $obj_db;
	
	function __construct(){
	}

	/*
	Function: get_order_received
	Purpose: to check whether the order is received for this patient
	Author: AA
	Returns: ARRAY if found, else false
	*/
	function get_order_received($pat_id = 0){
		$return = array(false, "");
		if($pat_id > 0){
			$sql = "SELECT print_order_id FROM clprintorder_master WHERE order_status='Received' and patient_id='".$pat_id."' ORDER BY print_order_savedatetime DESC limit 0,1";
			$res = imw_query($sql);
			if(imw_num_rows($res) > 0){
				$arr = imw_fetch_assoc($res);
				$return = array(true, $arr["print_order_id"]);
			}
		}
		return $return;
	}

	/*
	Function: get_DOS_due_balance
	Purpose: to get get_DOS_due_balance for this patient on the appointment day
	Author: AA
	Returns: ARRAY if found, else false
	*/
	function get_DOS_due_balance($appt_date, $pat_id = 0){
		$return = array(false, "");
		if($pat_id > 0){
			$sql = "SELECT totalCharges FROM clprintorder_master WHERE DATE_FORMAT( `print_order_savedatetime` , '%Y-%m-%d' ) ='".$appt_date."' and patient_id='".$pat_id."' limit 0,1";
			$res = imw_query($sql);
			if(imw_num_rows($res) > 0){
				$arr = imw_fetch_assoc($res);
				$return = array(true, $arr["totalCharges"]);
			}
		}
		return $return;
	}

	/*
	Function: get_trial_cost
	Purpose: to get trial (fit, eval, refit) cose for this patient on the appointment day
	Author: AA
	Returns: ARRAY if found, else false
	*/
	function get_trial_cost($appt_date, $pat_id = 0){
		$return = array("", "");
		if($pat_id > 0){
			$sql = "SELECT clws_id,clws_type,cpt_evaluation_fit_refit FROM contactlensmaster WHERE DATE_FORMAT(`clws_savedatetime`,'%Y-%m-%d') ='".$appt_date."' and patient_id='".$pat_id."' ORDER BY clws_id DESC limit 0,1";
			$res = imw_query($sql);
			if(imw_num_rows($res) > 0){
				$arr = imw_fetch_assoc($res);
				$return = array($arr["clws_type"], $arr["cpt_evaluation_fit_refit"]);
			}
		}
		return $return;
	}

	/*
	Function: get_last_final_rx
	Purpose: to get last final rx
	Author: AA
	Returns: ARRAY if found, else false
	*/
	function get_last_final_rx($pat_id = 0,$dos = ''){
		$return = $return_arr = false;
		$final_true = 0;
		if($pat_id > 0){
			$sql = "SELECT clws_id, clws_type FROM contactlensmaster WHERE patient_id='".$pat_id."' and dos = '".$dos."'  ORDER BY clws_id DESC";
			$res = imw_query($sql);
			if(imw_num_rows($res) > 0){
				while($result_row = imw_fetch_assoc($res))
				{
					$clws_type_arr = explode(',',$result_row['clws_type']);					
					if(is_array($clws_type_arr))
					{
						foreach($clws_type_arr as $cur_type)
						{
							$cur_type = trim($cur_type);
							if(strtolower($cur_type) == 'final')
							{
								$return_arr	= $result_row;
								$final_true = 1;
								break 2;
							}
						}
					}
					else
					{
						$cur_type = trim($cur_type);
						if(strtolower($clws_type_arr) == 'final')
						{
							$return_arr	= $result_row;
							$final_true = 1;							
							break;
						}
					}
				}
			}
		}
		$return = array($return_arr);		
		$return['final_status'] = $final_true;
		return $return;
	}
	
	/*
	Function: get_last_glasses_rx
	Purpose: to get last glasses rx id
	Author: AA
	Returns: ARRAY if found, else false
	*/
	function get_last_glasses_rx($pat_id = 0,$dos = ''){
		$return = false;
		if($pat_id > 0){
			$qry = "SELECT GROUP_CONCAT(c3.mr_none_given) AS vis_mr_none_given, c2.form_id, c1.finalize, c1.date_of_service, 
						c2.status_elements AS vis_statusElements
					FROM chart_master_table c1 
					INNER JOIN chart_vis_master c2 ON c2.form_id = c1.id
					INNER JOIN chart_pc_mr c3 ON c2.id = c3.id_chart_vis_master
					WHERE c1.patient_id = '".$pat_id."' and c1.date_of_service = '".$dos."' 
					AND c3.mr_none_given != '' AND c3.ex_type='MR'
					AND LOCATE(CONCAT('elem_mrNoneGiven',c3.ex_number,'=1'), c2.status_elements) > 0 
					GROUP BY c1.id 
					ORDER BY c1.id DESC LIMIT 0,1";			
			$res = imw_query($qry);
			if(imw_num_rows($res) > 0){
				$return = imw_fetch_assoc($res);
			}
		}
		return $return;
	}
	
	/*
	Function : get PC data
	Purpose : to check PC data exists or not	
	*/
	
	function pc_data_existence($start_date,$pat_id = 0)
	{
		$return = 0;
		if($pat_id > 0)
		{
			
			$qry = '
				SELECT
					c0.form_id, c2.sph as OdSpherical,
							   c2.cyl as odCylinder,
							   c2.axs AS odAxis,
							   c2.prsm_p as odPrism1, c2.sel_1 as odPrism2,
							   c2.slash as odBase1, c2.prism as odBase2, 
							   c2.ad as odAdd, 
							   c3.sph as osSpherical,
							   c3.cyl as osCylinder,
							   c3.axs as osAxis,  
							   c3.prsm_p as osPrism1, c3.sel_1 as osPrism2,
							   c3.slash as osBase1, c3.prism as osBase2,
							   c3.ad as osAdd,
							   c1.ex_desc as notes 
				FROM chart_master_table c4
				INNER JOIN chart_vis_master c0 ON c4.id = c0.form_id
				INNER JOIN chart_pc_mr c1 ON c1.id_chart_vis_master = c0.id
				INNER JOIN chart_pc_mr_values c2 ON c1.id = c2.chart_pc_mr_id AND c2.site="OD"
				INNER JOIN chart_pc_mr_values c3 ON c1.id = c3.chart_pc_mr_id AND c3.site="OS"	
				WHERE c4.patient_id = "'.$pat_id.'" && c4.date_of_service = "'.$start_date.'" 
				AND c1.ex_type="PC" 
				AND c1.delete_by="0"  
				Order By c4.date_of_service DESC, c4.id DESC, c1.ex_number
			';				   
							   
			$result_obj = imw_query($qry);
			for($i=1; $result_data = imw_fetch_assoc($result_obj);$i++){
				$form_id = $result_data['form_id'];			
				
				extract($result_data);
				
				if($odAxis){
					$odAxis .= "&#176;";
				}
				
				if($osAxis){
					$osAxis .= "&#176;";
				}
				
				//Check
				if($OdSpherical=="+"||$OdSpherical=="-") $OdSpherical = "";
				if($osSpherical=="+"||$osSpherical=="-") $osSpherical = "";
				if($odCylinder=="+"||$odCylinder=="-")$odCylinder="";
				if($osCylinder=="+"||$osCylinder=="-")$osCylinder="";
				if($odAdd=="+"||$odAdd=="-")$odAdd="";
				if($osAdd=="+"||$osAdd=="-")$osAdd="";
				if(empty($OdSpherical)&&empty($odCylinder)&&empty($odAxis)&&empty($odAdd)&&
					empty($osSpherical)&&empty($osCylinder)&&empty($osAxis)&&empty($osAdd)){
					$form_id = 0;
				}else{break;}
			}
			
			$return = $form_id;
		}
		return $return;
	}
	
}
?>