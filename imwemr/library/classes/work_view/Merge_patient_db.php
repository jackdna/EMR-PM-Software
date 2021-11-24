<?php
/*
 The MIT License (MIT)
 Distribute, Modify and Contribute under MIT License
 Use this software under MIT License
*/

include_once $GLOBALS['srcdir'].'/classes/SaveFile.php';
include_once $GLOBALS['srcdir'].'/classes/common_function.php';
include_once $GLOBALS['srcdir'].'/classes/class.app_base.php';
$app_base_obj = new app_base;

class Merge_patient_db{
	//Public variables
		public $patient_id = '';
		public $patientFacility = '';
		public $extract_arr = '';
		
	public function __construct(){
		
	}
	
	//Copy target file to destination
	public function full_copy( $source, $target ) {	
		if ( is_dir( $source ) ) {				
			@mkdir( $target );
			$d = dir( $source );
			while ( FALSE !== ( $entry = $d->read() ) ) {
				if ( $entry == '.' || $entry == '..' ) {
					continue;
				}
				$Entry = $source . '/' . $entry; 
				if ( is_dir( $Entry ) ) {
					$this->full_copy( $Entry, $target . '/' . $entry );
					continue;
				}
				copy( $Entry, $target . '/' . $entry );
			} 
			$d->close();
		}else {
			copy( $source, $target );
		}
	}
	
	//Merging patient data
	public function merge_requested_patients($request){
		extract($request);
		//merging starts here //that condition is checks for the  clicking on the merge button
		if((isset($request["hid_action"]) == true) && ($request["hid_action"] == "GO!") && ($merge_patient_id > 0) && ($this_patient_id > 0) && ($merge_form_id > 0)){
			
			//check if not finalized
			$sql = "SELECT * FROM chart_master_table where id='".$merge_form_id."' AND finalize='0' ";
			$row = sqlQuery($sql);
			if($row!=false){ exit(" Please Finalized the chart note first. "); }		
			
			$scan_db_name = "".IMEDIC_SCAN_DB;
			
			//getting all patient related tables to update patient records in detail tables	
			$sql = "select * from merge_patient_tables";
			$result_select = imw_query($sql);
			//echo imw_num_rows($result_select);
			
			if($result_select !== false){
				if(imw_num_rows($result_select) > 0){					
					
					//$arrNotUpdateTable = array("resp_party","employer_data","ocular","general_medicine","immunizations","social_history","vital_sign_master");					
					//array_push($arrNotUpdateTable, "creditapplied", "deniedpayment", "patient_charge_list", "patient_charge_list_details", "paymentswriteoff", "paymentscomment","era_835_patient_details","check_in_out_payment_post");
					//
					$arrNotUpdateTable = array(
						'chart_genhealth_archive',
						'chart_records_archive','order_set_associate_chart_notes','pt_problem_list','test_labs',
						'test_other',
						'oct','chart_gonio',
						'superbill','surgical_tbl','topography','vf','vf_nfa',
						'pachy','pnotes','scans','schedule',
						'disc_external',
						'ivfa','memo_tbl','nfa','ophtha',
						'amendments','amsler_grid','chart_assessment_plans','chart_correction_values',
						'chart_cvf','chart_dialation','chart_diplopia','chart_eom','chart_external_exam','chart_iop',
						'chart_lids', 'chart_lesion', 'chart_lid_pos', 'chart_lac_sys', 'chart_drawings',						
						'chart_left_cc_history',
						'chart_left_cc_history','chart_master_table','chart_optic','chart_pupil',
						'chart_vitreous','chart_retinal_exam','chart_blood_vessels','chart_periphery','chart_macula',
						'chart_conjunctiva', 'chart_cornea', 'chart_ant_chamber', 'chart_iris', 'chart_lens',
						'chart_vis_master','disc','tbl_def_val','trl_dsp_fnl_contact_lens','contactlensmaster',
						'restricted_providers','vision_contact_lens','prev_gas_contact_lens',						
						'prev_soft_contact_lens','gas_trl_dsp_fnl_contact_lens',
						'iolphyformulavalues','contact_lens_progress'
					);
					
					$arr_formid_array = array('chart_cvf'=>"formId", "chart_diplopia"=>"formId", "chart_master_table"=>"id","chart_pupil"=>"formId",
										"disc"=>"formId","disc_external"=>"formId","pachy"=>"formId","superbill"=>"formId",
										"topography"=>"formId","vf"=>"formId","test_labs"=>"formId","test_other"=>"formId");
					
					while($row = imw_fetch_array($result_select)){
						if(in_array($row["table_name"],$arrNotUpdateTable)){
							if(($row["table_name"] == "scans") || ($row["table_name"] == "folder_categories") || ($row["table_name"] == "scan_doc_tbl") || 
								($row["table_name"] == "idoc_drawing")){
								$column_array = explode(", ",$row["pt_id"]);
								
								//making query to update
								$update_sql = "update ".$scan_db_name.".".$row["table_name"]." set ";
								for($i=0;$i<count($column_array);$i++){
									$update_sql .= $column_array[$i]." = '".$this_patient_id."',";
								}
								
								$update_sql = substr($update_sql, 0, -1)." ";	// that  code is delete the last comma (,) from the query
								$update_sql .= " where  ";
								
								for($i=0;$i<count($column_array);$i++){
									$update_sql .= $column_array[$i]." = '".$merge_patient_id."' and ";
								}
								
								//formid
								$tt_form_id = !empty($arr_formid_array[$row["table_name"]]) ? $arr_formid_array[$row["table_name"]] : "form_id" ;
								$update_sql .= $tt_form_id." = '".$merge_form_id."' and ";	
							
								$update_sql = substr($update_sql, 0, -4)." "; //that code is delete the last "and" from the query
							
								if(empty($change_pt)){
									echo "<br/>".$update_sql;
								}else{
									//updataing patient records - merging patients
									imw_query($update_sql);
								}	
								//echo ($row["database_name"]);
								//query to audit updated primary keys
								
								if($row["table_name"] == "scans"){
									$qryUpdateScan = "UPDATE ".$scan_db_name.".scans SET file_path = REPLACE(file_path,'".$merge_patient_id."','".$this_patient_id."') WHERE patient_id = '".$this_patient_id."'";							
									$rsUpdateScan = imw_query($qryUpdateScan);							
								}
								elseif($row["table_name"] == "scan_doc_tbl"){
									$qryUpdateScanDocTbl = "UPDATE ".$scan_db_name.".scan_doc_tbl SET file_path = REPLACE(file_path,'".$merge_patient_id."','".$this_patient_id."') WHERE patient_id = '".$this_patient_id."'";
									$rsUpdateScanDocTbl = imw_query($qryUpdateScanDocTbl);							
								}
								elseif($row["table_name"] == "idoc_drawing"){
									$qryUpdateScanDocTbl = "UPDATE ".$scan_db_name.".idoc_drawing SET drawing_image_path = REPLACE(drawing_image_path,'".$merge_patient_id."','".$this_patient_id."') WHERE patient_id = '".$this_patient_id."'";
									$rsUpdateScanDocTbl = imw_query($qryUpdateScanDocTbl);							
								}						
							}
							else{
								$column_array = explode(", ",$row["pt_id"]);
								
								//making query to update
								$update_sql = "update ".$row["table_name"]." set ";
								for($i=0;$i<count($column_array);$i++){
									$update_sql .= $column_array[$i]." = '".$this_patient_id."',";
								}
								
								$update_sql = substr($update_sql, 0, -1)." ";	// that  code is delete the last comma (,) from the query
								$update_sql .= " where  ";
								
								for($i=0;$i<count($column_array);$i++){
									$update_sql .= $column_array[$i]." = '".$merge_patient_id."' and ";
								}
								
								//formid
								$tt_form_id = !empty($arr_formid_array[$row["table_name"]]) ? $arr_formid_array[$row["table_name"]] : "form_id" ;
								$update_sql .= $tt_form_id." = '".$merge_form_id."' and ";	
							
								$update_sql = substr($update_sql, 0, -4)." "; //that code is delete the last "and" from the query								
								
								if(empty($change_pt)){
									echo "<br/>".$update_sql;
								}else{	
									//updataing patient records - merging patients
									imw_query($update_sql);	
								}
								//echo ($row["database_name"]);
								//query to audit updated primary keys								
							}
						}						
					}//while loop
				}
			}			
			
			
			echo "<br/>Process Done!";
			exit();
		}
	}
}
?>