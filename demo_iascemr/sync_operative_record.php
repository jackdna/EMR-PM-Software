<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
if($ascId<>"" && $ascId<>0 && $imwPatientId && $_REQUEST["pConfId"]){
	$reportTemplate="";
	$opnote_qry_sc="SELECT opr.oprativeReportId,opr.reportTemplate,opt.template_name,stb.appt_id  FROM `operativereport` opr 
					LEFT JOIN operative_template opt ON (opt.template_id = opr.template_id)
					LEFT JOIN stub_tbl stb ON (stb.patient_confirmation_id = opr.confirmation_id)
					WHERE opr.confirmation_id = '".$_REQUEST["pConfId"]."' AND opr.form_status = 'completed'";
	$opnote_res_sc=imw_query($opnote_qry_sc) or die($opnote_qry_sc.imw_error());
	if(@imw_num_rows($opnote_res_sc)>0){
		$opnote_row_sc 		= imw_fetch_array($opnote_res_sc);
		$oprativeReportId 	= $opnote_row_sc["oprativeReportId"];
		$reportTemplate 	= stripslashes($opnote_row_sc["reportTemplate"]);
		$template_name 		= stripslashes($opnote_row_sc["template_name"]);
		$sc_emr_iasc_appt_id= $opnote_row_sc["appt_id"];
	}
	
	imw_close($link); //CLOSE SURGERYCENTER CONNECTION
	include('connect_imwemr.php'); // imwemr connection
	
	if(trim($reportTemplate) && $oprativeReportId) {
		$chkOpnoteQry="SELECT pn_rep_id FROM pn_reports WHERE patient_id = '".$imwPatientId."' AND sc_emr_operative_report_id = '".$oprativeReportId."'";
		$chkOpnoteRes=imw_query($chkOpnoteQry) or die($chkOpnoteQry.imw_error());
		$insUpdtOpQry = " INSERT INTO ";
		$insUpdtOpWhereQry =  " ";
		if(@imw_num_rows($chkOpnoteRes)>0){
			$insUpdtOpQry = " UPDATE ";
			$insUpdtOpWhereQry =  " WHERE patient_id = '".$imwPatientId."' AND sc_emr_operative_report_id = '".$oprativeReportId."' ";	
		}
		$setOpnoteQry = $insUpdtOpQry." pn_reports SET  
							patient_id 					= '".$imwPatientId."',
							txt_data 					= '".addslashes($reportTemplate)."',
							pn_rep_date 				= '".date("Y-m-d H:i:s")."',
							sc_emr_template_name 		= '".addslashes($template_name)."',
							sc_emr_operative_report_id 	= '".$oprativeReportId."',
							sc_emr_iasc_appt_id			= '".$sc_emr_iasc_appt_id."',
							status 						= '0'
							".$insUpdtOpWhereQry;
		$setOpnoteRes=imw_query($setOpnoteQry) or die($setOpnoteQry.imw_error());
														
	}
	imw_close($link_imwemr); //CLOSE IMWEMR CONNECTION
	include("common/conDb.php");  //SURGERYCENTER CONNECTION
	
	// ADD/UPDATE SX PROCEDURE IN iASC
	include_once("sync_sx_procedure.php");
}

?>