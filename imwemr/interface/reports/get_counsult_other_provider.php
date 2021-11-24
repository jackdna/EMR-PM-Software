<?php 
include_once(dirname(__FILE__)."/../../config/globals.php");
$searchDateFrom = $_REQUEST['fromDate'];
$searchDateTo = $_REQUEST['toDate'];
$data = "";
if($searchDateFrom && $searchDateTo){
	list($month, $day, $year) = explode('-',$searchDateFrom);
	$searchDateFrom = $year."-".$month."-".$day;
	list($month, $day, $year) = explode('-',$searchDateTo);
	$searchDateTo = $year."-".$month."-".$day;
	$qryGetConsultOtherProvider = "select DISTINCT tp.patient_consult_letter_to_other as othProvider,tp.templateName as tempName,DATE_FORMAT(tp.cur_date, '%m/%d/%Y') as tempCruDate,
									tp.patient_id as patientID,CONCAT_WS(', ',pd.lname ,pd.fname) as patientName
									from patient_consult_letter_tbl tp
									INNER JOIN patient_data pd ON tp.patient_id = pd.id
									left JOIN users us ON us.id = tp.operator_id
									where tp.date BETWEEN '".$searchDateFrom."' AND '".$searchDateTo."'
									and tp.status !='1' and tp.patient_consult_letter_to_other !='' 
							 	";
	
	$rsGetConsultOtherProvider = imw_query($qryGetConsultOtherProvider);
	
	if($rsGetConsultOtherProvider){
		if(imw_num_rows($rsGetConsultOtherProvider)>0){
			$hieght = $_SESSION['wn_height'] - 425;
			$data = "<div style=\" height:".$hieght."px; overflow:auto;\" class=\"section\">";
			$data .= "<form name=\"frmRegOtherProvider\" method=\"post\" action=\"register_other_provider_counsult.php\" target=\"registerOtherPro\">";
			$data .= "<input type=\"hidden\" name=\"otherProvider\" id=\"otherProvider\">";
			$data .= "<div class=\"section_header text12b\">
							<span class=\"closeBtn\" onClick=\"$('#counsult_letter_patient').hide();\"></span>
							Un-Registered Providers
					  </div>
					  <div class=\"text12b m10\" style=\"line-height:1.5;\">For some of the consult letters referring physician/primary care provider are not registered with the system. Would you like to register them?</div>
					<table class=\"table_collapse\">
						<tr class=\"subsection\">
							<th align=\"left\"><input type=\"checkbox\" id=\"cbkSelectAllOthPro\" name=\"cbkSelectAllOthPro\" onClick=\"selDeSelAllOthgProChkBox('sel');\"/></th>					
							<th align=\"left\">Provider name</th>					
							<th align=\"left\">Template name</th>					
							<th align=\"left\">Patient</th>				
							<th align=\"left\">Date</th>						
						</tr>
						";
			
			while($rowGetConsultOtherProvider = imw_fetch_array($rsGetConsultOtherProvider)){
					$data .= "<tr class=\"cellBorder3\">
								<td>
									<input type=\"checkbox\" id=\"cbkOthPro$counter\" name=\"cbkOthPro\" value='".$rowGetConsultOtherProvider['othProvider']."' onClick=\"document.getElementById('otherProvider').value = ''; document.getElementById('cbkSelectAllOthPro').checked = false;\"></span>
								</td>
								<td>
									".$rowGetConsultOtherProvider['othProvider']."
								</td>
								<td>
									".$rowGetConsultOtherProvider['tempName']."
								</td>
								<td>
									".$rowGetConsultOtherProvider['patientName']."(".$rowGetConsultOtherProvider['patientID'].")"."
								</td>
								<td>
									".$rowGetConsultOtherProvider['tempCruDate']."
								</td>
							</tr>";							  				
			}
			
		$data .= "</table>
			</div>
			<div class=\"subsection border\">
			<table class=\"table_collapse subsection border\">
				<tr>
					<td class=\"alignCenter\" style=\"width:50%\">
						<input type=\"button\" onClick=\"registerOtherprovider();\" value=\"Yes\" name=\"registerProvider\" class=\"dff_button\" id=\"registerProvider\" onMouseOver=\"button_over('registerProvider')\" onMouseOut=\"button_over('registerProvider','')\">
					</td>
					<td class=\"alignCenter\" style=\"width:50%\">
						<input type=\"button\" onClick=\"closeOthProDiv();\" value=\"No\" name=\"closeDivOthPro\" class=\"dff_button\" id=\"closeDivOthPro\" onMouseOver=\"button_over('closeDivOthPro')\" onMouseOut=\"button_over('closeDivOthPro','')\">
					</td>
				</tr>
			</table>
			</div>
			";
		$data .= "</form>
		<iframe name=\"registerOtherPro\" id=\"registerOtherPro\" src=\"\" style=\"border:none; margin:0px; height:0px; width:0px;\"  frameborder=\"0\"></iframe>
		";
		}
	}
}	

echo $data;	

?>