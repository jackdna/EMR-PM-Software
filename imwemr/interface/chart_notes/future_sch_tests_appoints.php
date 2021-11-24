<?php
$data="";
		$data_tests="";
		$data_appoint_ref="";
		$dis_dvFSTA_loinc = $dis_dvFSTA_cpt = "hidden";
		//List All --
		$sql = "SELECT * FROM chart_schedule_test_external WHERE patient_id = '".$patient_id."' AND deleted_by = '0' ORDER BY id ";
		$rez = sqlStatement($sql);				
		for($i=0, $a=1, $b=1;$row = sqlFetchArray($rez);$i++){
			
			if($row["appoint_test"] == "Test" ){
			$data_tests.="<tr>
					<td><div class=\"checkbox\"><input type=\"checkbox\" id=\"elem_FSTA_delid".$i."\"  name=\"elem_FSTA_delid".$i."\" value=\"".$row["id"]."\" ><label for=\"elem_FSTA_delid".$i."\"></label></div></td>
					<td>".($a++).".</td>
					<td class=\"edclick\" onclick=\"add_future_sch_tests_appoints('', '".$row["id"]."')\">".$row["reff_phy"]."</td>
					<td class=\"edclick\" onclick=\"add_future_sch_tests_appoints('', '".$row["id"]."')\">".$row["appoint_test"]."</td>
					<td class=\"edclick\" onclick=\"add_future_sch_tests_appoints('', '".$row["id"]."')\">".$row["phy_address"]."</td>
					<td class=\"edclick\" onclick=\"add_future_sch_tests_appoints('', '".$row["id"]."')\">".$row["reason"]."</td>
					<td class=\"edclick\" onclick=\"add_future_sch_tests_appoints('', '".$row["id"]."')\">".wv_formatDate($row["schedule_date"])."</td>
					<td class=\"edclick\" onclick=\"add_future_sch_tests_appoints('', '".$row["id"]."')\">".$row["variation"]."</td>
					<td class=\"edclick\" onclick=\"add_future_sch_tests_appoints('', '".$row["id"]."')\">".$row["test_name"]."</td>
					<td class=\"edclick\" onclick=\"add_future_sch_tests_appoints('', '".$row["id"]."')\">".$row["test_type"]."</td>
					<td class=\"edclick\" onclick=\"add_future_sch_tests_appoints('', '".$row["id"]."')\">".$row["snomed"]."</td>
					<td class=\"edclick\" onclick=\"add_future_sch_tests_appoints('', '".$row["id"]."')\">".$row["cpt"]."</td>
					<td class=\"edclick\" onclick=\"add_future_sch_tests_appoints('', '".$row["id"]."')\">".$row["loinc"]."</td>
					
					</tr>";
			}else{				
				
			$data_appoint_ref.="<tr>
					<td><div class=\"checkbox\"><input type=\"checkbox\" id=\"elem_FSTA_delid".$i."\" name=\"elem_FSTA_delid".$i."\" value=\"".$row["id"]."\"><label for=\"elem_FSTA_delid".$i."\"></label></div></td>
					<td>".($b++).".</td>
					<td class=\"edclick\" onclick=\"add_future_sch_tests_appoints('', '".$row["id"]."')\">".$row["reff_phy"]."</td>
					<td class=\"edclick\" onclick=\"add_future_sch_tests_appoints('', '".$row["id"]."')\">".$row["appoint_test"]."</td>
					<td class=\"edclick\" onclick=\"add_future_sch_tests_appoints('', '".$row["id"]."')\">".$row["phy_address"]."</td>
					<td class=\"edclick\" onclick=\"add_future_sch_tests_appoints('', '".$row["id"]."')\">".$row["reason"]."</td>
					<td class=\"edclick\" onclick=\"add_future_sch_tests_appoints('', '".$row["id"]."')\">".wv_formatDate($row["schedule_date"])."</td>
					<td class=\"edclick\" onclick=\"add_future_sch_tests_appoints('', '".$row["id"]."')\">".$row["variation"]."</td>
					<!--
					<td class=\"edclick\" onclick=\"add_future_sch_tests_appoints('', '".$row["id"]."')\">".$row["test_name"]."</td>
					<td class=\"edclick\" onclick=\"add_future_sch_tests_appoints('', '".$row["id"]."')\">".$row["test_type"]."</td>
					<td class=\"edclick\" onclick=\"add_future_sch_tests_appoints('', '".$row["id"]."')\">".$row["snomed"]."</td>
					<td class=\"edclick\" onclick=\"add_future_sch_tests_appoints('', '".$row["id"]."')\">".$row["cpt"]."</td>
					<td class=\"edclick\" onclick=\"add_future_sch_tests_appoints('', '".$row["id"]."')\">".$row["loinc"]."</td>
					-->
					
					</tr>";		
			}
			//	
			if(!empty($editId) && $row["id"] == $editId){
				$elem_FSTA_editid = $row["id"];
				$elem_fsta_phy_name = $row["reff_phy"];

				$elem_fsta_test_appoint_Test = ($row["appoint_test"]=="Test") ? "CHECKED" : "";
				$elem_fsta_test_appoint_Appointment = ($row["appoint_test"]=="Appointment") ? "CHECKED" : "";
				$elem_fsta_test_appoint_Referral = ($row["appoint_test"]=="Referral") ? "CHECKED" : "";

				$elem_fsta_test_name = $row["test_name"];

				$elem_fsta_test_type_Imaging = ($row["test_type"]=="Imaging") ? "CHECKED" : "";
				$elem_fsta_test_type_Lab = ($row["test_type"]=="Lab") ? "CHECKED" : "";
				$elem_fsta_test_type_Procedure = ($row["test_type"]=="Procedure") ? "CHECKED" : "";

				$elem_fsta_phy_address = $row["phy_address"];
				$elem_fsta_reason = $row["reason"];
				$elem_fsta_sch_date = wv_formatDate($row["schedule_date"]);
				$elem_fsta_variation = $row["variation"];
				$elem_fsta_sch_snomed = $row["snomed"];
				$elem_fsta_sch_cpt = $row["cpt"];
				$elem_fsta_sch_loinc = $row["loinc"];
				
				$dis_dvFSTA_test = ($row["appoint_test"]!="Test") ? "hidden" : "show" ;
				//$dis_dvFSTA_ref = ($row["appoint_test"]=="Referral") ? "none" : "block" ;
				
				$dis_dvFSTA_loinc = ($row["appoint_test"]=="Referral" || $row["test_type"]!="Lab") ? "hidden" : "show" ;
				$dis_dvFSTA_cpt = ($row["appoint_test"]=="Referral" || $row["test_type"]!="Procedure" ) ? "hidden" : "show" ;
				$dis_dvFSTA_snomed = ($row["appoint_test"]=="Referral" || $row["test_type"]!="Imaging") ? "hidden" : "show" ;
			
			}					
			
		}
		
		$elem_FSTA_counter=$i; //conuter
		
		//Test				
		if(!empty($data_tests)){
			$data_tests="	<table class=\"table\"><tr>
						<th class=\"btn btn-link\" onclick=\"add_future_sch_tests_appoints('3','1');\">Delete</th>
						<th>SNo.</th>
						<th>Physician</th>
						<th>Type</th>
						<th>Address</th>
						<th>Reason</th>
						<th>Schedule Date</th>
						<th>Any variation</th>
						<th>Test Name</th>
						<th>Test Type</th>
						<th>Snomed CT</th>
						<th>CPT</th>
						<th>Loinc</th>
					</tr>".$data_tests."</table>";
				
		}else{
			$data_tests="No Record found.";
		}
		
		//Refreal + Appointment
		if(!empty($data_appoint_ref)){
			$data_appoint_ref="	<table class=\"table\"><tr>
						<th class=\"btn btn-link\" onclick=\"add_future_sch_tests_appoints('3','2');\">Delete</th>
						<th>SNo.</th>
						<th>Physician</th>
						<th>Type</th>
						<th>Address</th>
						<th>Reason</th>
						<th>Schedule Date</th>
						<th>Any variation</th>
						<!--
						<th>Test Name</th>
						<th>Test Type</th>
						<th>Snomed CT</th>
						<th>CPT</th>
						<th>Loinc</th>
						-->								
					</tr>".$data_appoint_ref."</table>";
				
		}else{
			$data_appoint_ref="No Record found.";
		}
		
		//List All --				
		
		$str="<div id=\"div_add_future_tests\" class=\"modal fade\" role=\"dialog\">
		<div class=\"modal-dialog modal-lg\">

			<!-- Modal content-->
			<div class=\"modal-content\">
				<form id=\"frm_future_tests_extr\">
				<div class=\"modal-header\">
				<button type=\"button\" class=\"close\" data-dismiss=\"modal\">&times;</button>
				<h4 class=\"modal-title\">Future Scheduled Tests/appointments(Outside)</h4>
				</div>
				<div class=\"modal-body\">".
		
		//$str="<div id=\"div_add_future_tests\">
		//		<div class=\"hdr\">Future Scheduled Tests/appointments(Outside)</div>".
				"
				<input type=\"hidden\" name=\"elem_formAction\" value=\"add_future_sch_tests_appoints\">
				<input type=\"hidden\" name=\"elem_FSTA_editid\" value=\"".$elem_FSTA_editid."\">
				<input type=\"hidden\" name=\"elem_FSTA_counter\" value=\"".$elem_FSTA_counter."\">
				<label for=\"elem_fsta_phy_name\" class=\"lft\">Physician Name</label><input type=\"text\" id=\"elem_fsta_phy_name\" name=\"elem_fsta_phy_name\" value=\"".$elem_fsta_phy_name."\" class=\"form-control\"><br/>
				<div class=\"form-inline\"><label for=\"elem_fsta_test_appoint\" class=\"lft\">Test or Appointment</label><div class=\"radio\" ><input type=\"radio\" id=\"elem_fsta_test_appoint_Test\" name=\"elem_fsta_test_appoint\" value=\"Test\" ".$elem_fsta_test_appoint_Test."  onclick=\"set_future_sch_tests_options(this.value)\" ><label class=\"lbl_test_type\" for=\"elem_fsta_test_appoint_Test\">Test</label></div>
																	<div class=\"radio\" ><input type=\"radio\" id=\"elem_fsta_test_appoint_Appointment\" name=\"elem_fsta_test_appoint\" value=\"Appointment\" ".$elem_fsta_test_appoint_Appointment." onclick=\"set_future_sch_tests_options(this.value)\" ><label class=\"lbl_test_type\" for=\"elem_fsta_test_appoint_Appointment\">Appointment</label></div>
																	<div class=\"radio\" ><input type=\"radio\" id=\"elem_fsta_test_appoint_Referral\" name=\"elem_fsta_test_appoint\" value=\"Referral\" ".$elem_fsta_test_appoint_Referral." onclick=\"set_future_sch_tests_options(this.value)\" ><label class=\"lbl_test_type\" for=\"elem_fsta_test_appoint_Referral\">Referral</label></div></div>
				<div id=\"dvFSTA_test\" >
				<label for=\"elem_fsta_test_name\" class=\"lft\">Test Name</label><input type=\"text\" name=\"elem_fsta_test_name\" value=\"".$elem_fsta_test_name."\" class=\"form-control\"><br/>
				
				<div class=\"form-inline ".$dis_dvFSTA_test."\"><label for=\"elem_fsta_test_type\" class=\"lft\">Test Type</label><div class=\"radio\"><input type=\"radio\" id=\"elem_fsta_test_type_Imaging\" name=\"elem_fsta_test_type\" value=\"Imaging\" ".$elem_fsta_test_type_Imaging." onclick=\"set_future_sch_testsType_options()\" > <label class=\"lbl_test_type\" for=\"elem_fsta_test_type_Imaging\">Imaging</label></div>
																	<div class=\"radio\"><input type=\"radio\" id=\"elem_fsta_test_type_Lab\" name=\"elem_fsta_test_type\" value=\"Lab\" ".$elem_fsta_test_type_Lab." onclick=\"set_future_sch_testsType_options()\"  > <label class=\"lbl_test_type\" for=\"elem_fsta_test_type_Lab\">Lab</label></div>
																	<div class=\"radio\"><input type=\"radio\" id=\"elem_fsta_test_type_Procedure\" name=\"elem_fsta_test_type\" value=\"Procedure\" ".$elem_fsta_test_type_Procedure." onclick=\"set_future_sch_testsType_options()\"  > <label class=\"lbl_test_type\" for=\"elem_fsta_test_type_Procedure\">Procedure</label></div></div>	
				</div>
				<label for=\"elem_fsta_phy_address\" class=\"lft\">Address of physician</label><textarea name=\"elem_fsta_phy_address\" class=\"form-control\" >".$elem_fsta_phy_address."</textarea><br/>
				<label for=\"elem_fsta_reason\" class=\"lft\">Reason</label><textarea name=\"elem_fsta_reason\" class=\"form-control\" >".$elem_fsta_reason."</textarea><br/>
				<label for=\"elem_fsta_sch_date\" class=\"lft\">Schedule Date</label><input type=\"text\" name=\"elem_fsta_sch_date\" value=\"".$elem_fsta_sch_date."\" class=\"form-control\"><br/>
				<label for=\"elem_fsta_variation\" class=\"lft\">Any variation</label><textarea name=\"elem_fsta_variation\" class=\"form-control\" >".$elem_fsta_variation."</textarea>
				
				<div id=\"dvFSTA_snomed\" class=\"".$dis_dvFSTA_snomed."\">
				<label for=\"elem_fsta_sch_snomed\" class=\"lft\">Snomed CT</label><input type=\"text\" name=\"elem_fsta_sch_snomed\" value=\"".$elem_fsta_sch_snomed."\" class=\"form-control\" >
				</div>
				
				<div id=\"dvFSTA_cpt\" class=\"".$dis_dvFSTA_cpt."\">
				<label for=\"elem_fsta_sch_cpt\" class=\"lft\">CPT</label><input type=\"text\" name=\"elem_fsta_sch_cpt\" value=\"".$elem_fsta_sch_cpt."\" class=\"form-control\">
				</div>
				
				<div id=\"dvFSTA_loinc\" class=\"".$dis_dvFSTA_loinc."\">
				<label for=\"elem_fsta_sch_loinc\" class=\"lft\">Loinc</label><input type=\"text\" name=\"elem_fsta_sch_loinc\" value=\"".$elem_fsta_sch_loinc."\" class=\"form-control\">
				</div>	
				<center>
				<button type=\"button\" name=\"elem_fsta_btn_save\" class=\"dff_button btn btn-success\" onclick=\"add_future_sch_tests_appoints('1');\">Save</button>
				<button type=\"button\" name=\"elem_fsta_btn_reset\" value=\"Add New\" class=\"dff_button btn btn-info\" onclick=\"add_future_sch_tests_appoints();\">Add New</button>
				<button type=\"button\" name=\"elem_fsta_btn_cancel\" value=\"Close\" class=\"dff_button btn btn-danger\" onclick=\"add_future_sch_tests_appoints('0');\">Close</button>	
				</center>
				<div id=\"div_saved_future_tests\" class=\"panel panel-success\">
				<div class=\"panel-heading\"><strong>Test</strong></div>
				<div class=\"panel-body table-responsive\">".$data_tests."</div>		
				</div>
				<div id=\"div_saved_future_appoint_ref\" class=\"panel panel-success\">
				<div class=\"panel-heading\"><strong>Appointment / Referral</strong></div>
				<div class=\"panel-body table-responsive\">".$data_appoint_ref."</div>		
				</div>".
			//"</div>".
		
				"</div>
				
				</form>
			</div>

		</div>
		</div>";		
		
	echo $str;		
?>		