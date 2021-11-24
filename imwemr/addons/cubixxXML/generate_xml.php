<?php

require_once(dirname(__FILE__)."/../../config/globals.php");
if(constant('GENERATE_CUBIXX_CHARGES_XML') && strtolower(constant('GENERATE_CUBIXX_CHARGES_XML'))=='yes'){	
	function cubixx_create_charges_xml($charge_list_id){
		$xml = ''; $charge_list_detail_id_arr = array();
		$q1 = "SELECT pcl.patient_id, pcl.encounter_id, pcl.date_of_service, pcl.entered_date, pcl.entered_time, 
			   pd.fname AS patient_fname, pd.lname AS patient_lname, 
			   ic.id AS insurnace_id, ic.name AS insurnace_name, 
			   u.user_npi AS rendering_npi 
			   FROM patient_charge_list pcl 
			   JOIN patient_data pd ON (pd.id=pcl.patient_id) 
			   LEFT JOIN insurance_companies ic ON (ic.id=pcl.primaryInsuranceCoId) 
			   LEFT JOIN users u ON (u.id=pcl.primaryProviderId) 
			   WHERE charge_list_id = '$charge_list_id' LIMIT 0,1";
		$res1 = imw_query($q1);
		if($res1 && imw_num_rows($res1)==1){
			$rs1 = imw_fetch_assoc($res1);
			$patient_id 		= $rs1['patient_id'];
			$encounter_id 		= $rs1['encounter_id'];
			$date_of_service 	= $rs1['date_of_service'];
			$appt_location_id	= cubixx_get_location_id_by_dos($patient_id,$date_of_service);
			$enc_created_date 	= $rs1['entered_date'].' '.$rs1['entered_time'];
			$enc_modified_date 	= cubixx_get_encounter_modified_date($encounter_id,'DESC');
			$enc_modified_date 	= !empty($enc_modified_date) ? $enc_modified_date : $enc_created_date;
			
			$line_items_rs = cubixx_get_line_items($charge_list_id);
			if(is_array($line_items_rs)){
				foreach($line_items_rs as $line_rs){
					$charge_list_detail_id	= $line_rs['charge_list_detail_id'];
					$procCode 				= $line_rs['procCode'];
					$cpt_rs 				= cubixx_get_cpt_details($procCode);
					$cpt4_code				= $cpt_rs['cpt4_code'];
					$cpt4_desc				= $cpt_rs['cpt_desc'];

					$charge_list_detail_id_arr[] = $charge_list_detail_id;
					
					$proc_admin_cmnt = trim(preg_replace("/[^A-Za-z0-9 ]/", "", $cpt_rs['cpt_comments']));
					$temp_cpt_comments_ar = explode('/',$cpt_rs['cpt_comments']);
					$proc_admin_cmnt = trim(preg_replace("/[^A-Za-z0-9 ]/", "", trim($temp_cpt_comments_ar[0])));
					
					$firstCharOfCPT = strtolower(substr($cpt4_code,0,1));
					if(!in_array($firstCharOfCPT,array('j','l','6'))){
						$proc_admin_cmnt = '';
						continue; //skip the record where NDC information not avaialble.
					}
					
					$patient_paid_amt		= '0.00';
					$payer_paid_amt			= '0.00';
					$payer_allowed_amt		= number_format($line_rs['approvedAmt'],2);
					$payer_adj_amt			= '0.00';
					$payments	= cubixx_get_pcld_wise_payments($charge_list_detail_id);
					if($payments && is_array($payments)){
						$patient_paid_amt 	= number_format($payments['Patient'],2);
						$payer_paid_amt 	= number_format($payments['Insurance'],2);
					}
					$writeoff	= cubixx_get_payer_write_off($charge_list_detail_id);
					if($writeoff){
						$payer_adj_amt			= number_format($writeoff,2);
					}
					
					$xml .= '<ChargePayment>'.chr(13);
					$xml .= '<encounter_UID>'.$encounter_id.'</encounter_UID>'.chr(13);
					$xml .= '<person_UID>'.$patient_id.'</person_UID>'.chr(13);
					$xml .= '<patient_first>'.addslashes($rs1['patient_fname']).'</patient_first>'.chr(13);
					$xml .= '<patient_last>'.addslashes($rs1['patient_lname']).'</patient_last>'.chr(13);
					$xml .= '<patient_id_nbr>'.$patient_id.'</patient_id_nbr>'.chr(13);
					$xml .= '<patient_MRN>'.$patient_id.'</patient_MRN>'.chr(13);
					$xml .= '<insurance_1_payer_UID>'.$rs1['insurnace_id'].'</insurance_1_payer_UID>'.chr(13);
					$xml .= '<insurance_1_company>'.$rs1['insurnace_name'].'</insurance_1_company>'.chr(13);
					$xml .= '<rendering_physician_NPID>'.$rs1['rendering_npi'].'</rendering_physician_NPID>'.chr(13);
					$xml .= '<location_UID>'.$appt_location_id.'</location_UID>'.chr(13);
					$xml .= '<charge_UID>'.$charge_list_detail_id.'</charge_UID>'.chr(13);
					$xml .= '<charge_service_item>'.$cpt4_code.'</charge_service_item>'.chr(13);
					$xml .= '<charge_modifier_1>'.$line_rs['modifier_id1'].'</charge_modifier_1>'.chr(13);
					$xml .= '<charge_modifier_2>'.$line_rs['modifier_id2'].'</charge_modifier_2>'.chr(13);
					$xml .= '<charge_modifier_3>'.$line_rs['modifier_id3'].'</charge_modifier_3>'.chr(13);
					$xml .= '<charge_modifier_4>'.$line_rs['modifier_id4'].'</charge_modifier_4>'.chr(13);
					$xml .= '<charge_patient_amt>'.$patient_paid_amt.'</charge_patient_amt>'.chr(13);								//***************put patient paid value here.
					$xml .= '<charge_amt>'.$line_rs['totalAmount'].'</charge_amt>'.chr(13);
					$xml .= '<charge_quantity>'.number_format($line_rs['units'],0).'</charge_quantity>'.chr(13);
					$xml .= '<charge_unit_price>'.number_format($line_rs['procCharges'],2).'</charge_unit_price>'.chr(13);
					$xml .= '<charge_date_of_service>'.$date_of_service.'</charge_date_of_service>'.chr(13);
					$xml .= '<charge_created>'.$enc_created_date.'</charge_created>'.chr(13);
					$xml .= '<charge_modified>'.$enc_modified_date.'</charge_modified>'.chr(13);
					$xml .= '<charge_revenue_description>'.$cpt4_desc.'</charge_revenue_description>'.chr(13);
					$xml .= '<charge_ndc>'.$proc_admin_cmnt.'</charge_ndc>'.chr(13);
					$xml .= '<pay_paid_amt>'.$payer_paid_amt.'</pay_paid_amt>'.chr(13);												//***************put payer paid value here.
					$xml .= '<pay_allowed_amt>'.$payer_allowed_amt.'</pay_allowed_amt>'.chr(13);									//***************put payer allowed amount value here.
					$xml .= '<pay_adj_amt>'.$payer_adj_amt.'</pay_adj_amt>'.chr(13);												//***************put adjustment value here.
					$xml .= '</ChargePayment>'.chr(13);
				}
			}
		
		}
		if(!empty($xml)){//log this.
			$q = "INSERT INTO xml_outbound_interface SET ";
			$q.= "patient_id='".$patient_id."', ";
			$q.= "encounter_id='".$encounter_id."', ";
			$q.= "charge_list_id='".$charge_list_id."', ";
			$q.= "charge_list_detail_id='".implode(',',$charge_list_detail_id_arr)."', ";
			$q.= "xml_text='".addslashes($xml)."', ";
			$q.= "created_for='cubixx', ";
			$q.= "created_by='".$_SESSION['authId']."', ";
			$q.= "created_on='".date('Y-m-d H:i:s')."' ";
			$res = imw_query($q);
		}
	}
	
	function cubixx_get_cpt_details($procCode){
		$res = imw_query("SELECT cpt4_code,cpt_comments,cpt_desc FROM `cpt_fee_tbl` WHERE cpt_fee_id = '".$procCode."' LIMIT 0,1");
		if($res && imw_num_rows($res)==1){
			$rs = imw_fetch_assoc($res);
			return $rs;			
		}
		return false;
	}
	
	function cubixx_get_encounter_modified_date($enc_id,$orderby='DESC'){
		$res = imw_query("SELECT modifier_on FROM `patient_charge_list_modifiy` WHERE enc_id = '".$enc_id."' ORDER BY id ".$orderby." LIMIT 0,1");
		if($res && imw_num_rows($res)==1){
			$rs = imw_fetch_assoc($res);
			return str_ireplace(array(' pm',' am'),'',$rs['modifier_on']);
		}
		return '';
	}
	
	function cubixx_get_line_items($chl_id){
		$return = false;
		$res = imw_query("SELECT charge_list_detail_id, procCode, procCharges, totalAmount, approvedAmt, units, modifier_id1, modifier_id2, modifier_id3, modifier_id4 FROM patient_charge_list_details WHERE charge_list_id = '".$chl_id."' AND del_status='0'");
		if($res && imw_num_rows($res)>0){
			$return = array();
			while($rs = imw_fetch_assoc($res)){
				$charge_list_detail_id = $rs['charge_list_detail_id'];
				$rs['modifier_id1'] = cubixx_get_modifier($rs['modifier_id1']);
				$rs['modifier_id2'] = cubixx_get_modifier($rs['modifier_id2']);
				$rs['modifier_id3'] = cubixx_get_modifier($rs['modifier_id3']);
				$rs['modifier_id4'] = cubixx_get_modifier($rs['modifier_id4']);
				$return[] = $rs;
			}
		}
		return $return;		
	}
	
	function cubixx_get_modifier($modifier_id){
		if(empty($modifier_id) || $modifier_id==0) return '';
		$res = imw_query("SELECT mod_prac_code FROM modifiers_tbl WHERE modifiers_id=".$modifier_id." LIMIT 1");
		if($res && imw_num_rows($res)==1){
			$rs = imw_fetch_assoc($res);
			return $rs['mod_prac_code'];
		}
		return '';
	}
	
	function cubixx_get_rev_code($rev_id){
		$res = imw_query("select r_desc from revenue_code where r_id = '".$rev_id."' LIMIT 0,1");
		if($res && imw_num_rows($res)==1){
			$rs = imw_fetch_assoc($res);
			return $rs['r_desc'];
		}
		return '';
	}
	
	function cubixx_get_location_id_by_dos($patient,$dos){
		$res = imw_query("SELECT f.id FROM facility f JOIN schedule_appointments sa ON (sa.sa_facility_id=f.id) WHERE sa.sa_patient_id=$patient AND sa.sa_app_start_date='$dos' AND sa_patient_app_status_id NOT IN (203,201,18,19,20,3) LIMIT 1");
		if($res && imw_num_rows($res)==1){
			$rs = imw_fetch_assoc($res);
			return $rs['id'];
		}
		return '';
	}
	
	function cubixx_get_todays_posted_charges($begin_date=""){
		$today = date('Y-m-d');
		$date_query = "patient_charge_list.postedDate='$today'";
		if(!empty($begin_date)){
			$date_query = "(patient_charge_list.postedDate BETWEEN '$begin_date' AND '$today')";
		}

		$q = "SELECT patient_charge_list.charge_list_id FROM patient_charge_list 
			  LEFT JOIN patient_charge_list_details ON (patient_charge_list_details.charge_list_id = patient_charge_list.charge_list_id) 
			  WHERE patient_charge_list_details.del_status='0' 
		      AND patient_charge_list_details.posted_status='1' AND $date_query AND patient_charge_list.totalBalance > '0' 
		      AND patient_charge_list_details.proc_selfpay != '1' 
		      GROUP BY patient_charge_list.charge_list_id";
		$res = imw_query($q);
		$return = false;
		if($res && imw_num_rows($res)>0){
			$return = array();
			while($rs = imw_fetch_assoc($res)){
				$return[] = $rs['charge_list_id'];
			}
		}
		return $return;
	}
	
	function cubixx_get_todays_paid_charges($begin_date=""){
		$today = date('Y-m-d');
		$date_query = "pcdpi.paidDate = '$today'";
		if(!empty($begin_date)){
			$date_query = "(pcdpi.paidDate BETWEEN '$begin_date' AND '$today')";
		}
		
		$q = "SELECT pcld.charge_list_id FROM patient_charge_list_details pcld 
		JOIN patient_charges_detail_payment_info pcdpi ON (pcdpi.charge_list_detail_id=pcld.charge_list_detail_id AND pcdpi.deletePayment = 0) 
		WHERE pcld.del_status = '0' AND $date_query AND pcdpi.paidForProc > 0";
		$res = imw_query($q);
		$return = false;
		if($res && imw_num_rows($res)>0){
			$return = array();
			while($rs = imw_fetch_assoc($res)){
				$return[] = $rs['charge_list_id'];
			}
		}
		return $return;
	}
	
	function cubixx_get_pcld_wise_payments($pcld_id){
		$q = "SELECT SUM(paidForProc) as paid_amount, paidBy FROM patient_charges_detail_payment_info WHERE charge_list_detail_id = '$pcld_id' AND deletePayment = 0 AND paidForProc > 0 GROUP BY (paidBy)";
		$res = imw_query($q);
		$return = false;
		if($res && imw_num_rows($res)>0){
			$return = array();
			while($rs = imw_fetch_assoc($res)){
				$return[$rs['paidBy']] = $rs['paid_amount'];
			}
		}
		return $return;
	}
	
	function cubixx_get_payer_write_off($pcld_id){
		$q = "SELECT SUM(write_off_amount) as write_off_amt FROM paymentswriteoff WHERE charge_list_detail_id = '$pcld_id' AND write_off_by_id > 0 AND delStatus = 0 ";
		$q .= "WHERE paymentStatus LIKE 'Write Off' GROUP BY (charge_list_detail_id)";
		$res = imw_query($q);
		$return = false;
		if($res && imw_num_rows($res)>0){
			$rs = imw_fetch_assoc($res);
			$return = $rs['write_off_amt'];
		}
		return $return;
	}
}else{
	function cubixx_create_charges_xml($charge_list_id){};
}

?>