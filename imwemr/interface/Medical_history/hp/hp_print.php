<?php
ob_end_clean();
ob_start();

$_SESSION['callFrom']="medicationTab";

include_once('../../../config/globals.php'); 
require_once($GLOBALS['srcdir']."/classes/SaveFile.php");
require_once($GLOBALS['srcdir']."/classes/medical_hx/history_physical.class.php");

$historyPhysical = new HistoryPhysical($medical->current_tab);

$pid = $_SESSION['patient'];
$displayRecord='none';

$procArr = $historyPhysical->load_sx_procedures(); 
?>
<style>
.text_11 {
	font-family: Arial, Verdana,  Helvetica, sans-serif;
	font-size: 15px;
	font-weight: normal;
	color: #000000;
}
.text_11b {
	font-family: Arial, Verdana,  Helvetica, sans-serif;
	font-size: 15px;
	font-weight:bold;
	color: #000000;
}
.text_10 {
	font-family: Arial, Verdana, Helvetica, sans-serif;
	font-size: 14px;
	font-weight: normal;
	color: #000000;
}
.text_10b {
	font-family: Arial, Verdana, Helvetica, sans-serif;
	font-size: 14px;
	color: #000000;
}
.text_9b {
	font-family: Arial, Verdana, Helvetica, sans-serif;
	font-size: 13px;
	font-weight:bold;
	color: #000000;
}
.text_18 {
	font-family: Arial, Verdana,  Helvetica, sans-serif;
	font-size: 18px;
	font-weight:normal;
	color: #000000;
}
.text_18b {
	font-family: Arial, Verdana,  Helvetica, sans-serif;
	font-size: 18px;
	font-weight:bold;
	color: #000000;
}

.tb_heading{
	font-size:12px;
	font-family:Arial, Helvetica, sans-serif;
	font-weight:bold;
	color:#000000;
	background-color:#FE8944;
}

.lightBlue {
	font-size:12px;
	font-family:Arial, Helvetica, sans-serif;
<!--background-color:#EAF4FD;-->
}
.midBlue {
	font-size:12px;
	font-family:Arial, Helvetica, sans-serif;
	background-color:#80AFEF;
}
.text_orangeb{
	font-weight:bold;
	font-family:Arial, Helvetica, sans-serif;
	background-color:#FFFFFF;
	color:#CB6B43;
}
.lightGreen {
	font-size:12px;
	font-family:Arial, Helvetica, sans-serif;
	background-color:#ECF1EA;
}
.lightorange {
	font-size:12px;
	font-family:Arial, Helvetica, sans-serif;
	background-color:#CB6B43;
}
.pdtb{
	padding-top:5px;
	padding-bottom:5px;
}
.pdl5{padding-left:5px;}
.pdtb{padding-left:10px;}
.text-center{text-align:center;}
</style>
<page backtop="0mm" backbottom="0mm">			
<page_footer> 
	<table style="width: 98%; display:<?php echo $displayRecord;?>;">
		<tr>
			<td style="text-align: center;	width: 100%">Page [[page_cu]]/[[page_nb]]</td>
		</tr>
	</table>
</page_footer>
<?php
	$sql_select = "
		SELECT 
			CONCAT_WS(', ', lname, fname ) AS full_name, 
			Date_Format(dob,'%W,  %M %d, %Y') as dob,
			CONCAT_WS(', ', street, street2, city, state ) AS address,
			phone_home, 
			phone_biz, 
			phone_contact, 
			phone_cell, 
			ado_option, 
			desc_ado_other_txt
		FROM  
			`patient_data` 
		WHERE 
			id = '".$pid."' 
		";
	$sql_result	= imw_query($sql_select);
	$sql_array	= imw_fetch_array($sql_result);
	
	$phone_of_patient = "";
	
	if($sql_array["phone_home"]!="")
	{
		$phone_of_patient = $sql_array["phone_home"];
	}
	else if($sql_array["phone_biz"]!="")
	{
		$phone_of_patient = $sql_array["phone_biz"];
	}
	else if($sql_array["phone_contact"]!="")
	{
		$phone_of_patient = $sql_array["phone_contact"];
	}
	else if($sql_array["phone_cell"]!="")
	{
		$phone_of_patient = $sql_array["phone_cell"];
	}
	
	$full_name 		= $sql_array["full_name"];
	$dob			= $sql_array["dob"];
	$address		= $sql_array["address"];
	$ado_option		= $sql_array["ado_option"];
	$ado_other_txt	= $sql_array["desc_ado_other_txt"];
	
	if($ado_option == "Other"){	$ado_option.= " - ".$ado_other_txt;	}
?>
<table width="100%" border="0" cellspacing="2" cellpadding="5" style="display:<?php echo $displayRecord;?>; ">
	<tr>
		<td>
			<?php
			$query="SELECT 
						* 
					FROM 
						`surgerycenter_pt_history_physical` 
					WHERE 
						patient_id=$pid";
			
			$sql = imw_query($query);
			$row = imw_fetch_assoc($sql); 
			
			$cadMI						= $row["cadMI"];
			$cadMIDesc					= $row["cadMIDesc"];
			$cvaTIA						= $row["cvaTIA"];
			$cvaTIADesc					= $row["cvaTIADesc"];
			$htnCP						= $row["htnCP"];
			$htnCPDesc					= $row["htnCPDesc"];
			$anticoagulationTherapy		= $row["anticoagulationTherapy"];
			$anticoagulationTherapyDesc	= $row["anticoagulationTherapyDesc"];
			$respiratoryAsthma			= $row["respiratoryAsthma"];
			$respiratoryAsthmaDesc		= $row["respiratoryAsthmaDesc"];
			$arthritis					= $row["arthritis"];
			$arthritisDesc				= $row["arthritisDesc"];
			$diabetes					= $row["diabetes"];
			$diabetesDesc				= $row["diabetesDesc"];
			$recreationalDrug			= $row["recreationalDrug"];
			$recreationalDrugDesc		= $row["recreationalDrugDesc"];
			$giGerd						= $row["giGerd"];
			$giGerdDesc					= $row["giGerdDesc"];
			$ocular						= $row["ocular"];
			$ocularDesc					= $row["ocularDesc"];
			$kidneyDisease				= $row["kidneyDisease"];
			$kidneyDiseaseDesc			= $row["kidneyDiseaseDesc"];
			$hivAutoimmune				= $row["hivAutoimmune"];
			$hivAutoimmuneDesc			= $row["hivAutoimmuneDesc"];
			$historyCancer				= $row["historyCancer"];
			$historyCancerDesc			= $row["historyCancerDesc"];
			$organTransplant			= $row["organTransplant"];
			$organTransplantDesc		= $row["organTransplantDesc"];
			$badReaction				= $row["badReaction"];
			$badReactionDesc			= $row["badReactionDesc"];
			$highCholesterol			= $row["highCholesterol"];
			$highCholesterolDesc		= $row["highCholesterolDesc"];
			$thyroid					= $row["thyroid"];
			$thyroidDesc				= $row["thyroidDesc"];
			$ulcer						= $row["ulcer"];
			$ulcerDesc					= $row["ulcerDesc"];
			$heartExam					= $row["heartExam"];
			$heartExamDesc				= $row["heartExamDesc"];
			$lungExam					= $row["lungExam"];
			$lungExamDesc				= $row["lungExamDesc"];
			$otherHistoryPhysical		= $row["otherHistoryPhysical"];
			?>
			<!--- Printing Work Starts From Here --->
			<table  border="0" cellspacing="2" cellpadding="2">
				<tr valign="top" height="30" bgcolor="#000000">
					<td align="center" colspan="3" class="text_18b text_orangeb">HISTORY AND PHYSICAL</td>
				</tr>
				<tr valign="top" height="15">
					<td align="left" colspan="3" class="text_10" >
						<table border="0" cellpadding="0" cellspacing="2" >
						<tr>
							<td colspan="2" class="text_10 pdtb" style="width:450px;text-align:left;">
								<?php  if($full_name!=""){ ?> <b>Patient Name:</b> <?php echo $full_name; }?>
							</td>
							<td class="text_10 pdtb" style="width:250px;text-align:left;">
								<b>Patient Id:</b> <?php echo $pid; ?>
							</td>
						</tr>
						<tr>
							<td colspan="2" class="text_10 pdtb" style="width:450px;text-align:left;">
								<?php if($dob!=""){?> <b>DOB:</b> <?php echo $dob; }?>
							</td>
							<td class="text_10 pdtb" style="width:250px;text-align:left;">
									<?php  if($phone_of_patient!=""){?> <b>Phone:</b> <?php echo $phone_of_patient; }?>
							</td>
						</tr>
						<tr>
							<td colspan="2" class="text_10 pdtb" style="width:450px;text-align:left;">
								<?php if($address!=""){?> <b>Address:</b> <?php echo $address; }?>
							</td>
							<td class="text_10 pdtb" style="width:250px;text-align:left;">
							</td>
						</tr>
						</table>
					</td>
				</tr>
				<?php if(!empty($ado_option)){ ?>
				<tr>
					<td class="text_10 pdtb" style="width:700px;" colspan="3"><b>Advance Directive:</b> <?php echo $ado_option; ?> </td>
				</tr>
				<?php } ?>	
				<tr>
					<td class="text_10b midBlue pdtb" style="width:300px;">HISTORY AND PHYSICAL</td>
					<td class="text_10b midBlue pdtb" >YES / NO</td>
					<td class="text_10b midBlue pdtb" style="width:250px;">DESCRIPTION</td>
				</tr>
				<?php if($cadMI == "Yes" || $cadMI == "No" ){ ?>
				<tr>
					<td class="text_10 pdtb" >CAD/MIN(W/ WO Stent OR CABG)/PVD</td>
					<td class="text_10 pdtb" style="text-align:center;"><?php echo $cadMI;?></td>
					<td class="text_10 pdtb" ><?php echo $cadMIDesc;?></td>
				</tr>
				<?php }
					if($cvaTIA == "Yes" || $cvaTIA == "No" ){ 
				?>
				<tr>
					<td class="text_10 pdtb" >CVA/TIA/ Epilepsy, Neurological</td>
					<td class="text_10 pdtb" style="text-align:center;"><?php echo $cvaTIA;?></td>
					<td class="text_10 pdtb" ><?php echo $cvaTIADesc;?></td>
				</tr>
				<?php }
					if($htnCP == "Yes" || $htnCP == "No" ){ 
				?>				
				<tr>
					<td class="text_10 pdtb" >HTN/ +/- CP/SOB on Exertion</td>
					<td class="text_10 pdtb" style="text-align:center;"><?php echo $htnCP;?></td>
					<td class="text_10 pdtb" ><?php echo $htnCPDesc;?></td>
				</tr>
				<?php }
					if($anticoagulationTherapy == "Yes" || $anticoagulationTherapy == "No" ){ 
				?>					
				<tr>
					<td class="text_10 pdtb" >Anticoagulation therapy</td>
					<td class="text_10 pdtb" style="text-align:center;"><?php echo $anticoagulationTherapy;?></td>
					<td class="text_10 pdtb" ><?php echo $anticoagulationTherapyDesc;?></td>
				</tr>
				<?php }
					if($respiratoryAsthma == "Yes" || $respiratoryAsthma == "No" ){ 
				?>			
				<tr>
					<td class="text_10 pdtb" >Respiratory - Asthma / COPD / Sleep Apnea</td>
					<td class="text_10 pdtb" style="text-align:center;"><?php echo $respiratoryAsthma;?></td>
					<td class="text_10 pdtb" ><?php echo $respiratoryAsthmaDesc;?></td>
				</tr>
				<?php }
					if($arthritis == "Yes" || $arthritis == "No" ){ 
				?>		
				<tr>
					<td class="text_10 pdtb" >Arthritis</td>
					<td class="text_10 pdtb" style="text-align:center;"><?php echo $arthritis;?></td>
					<td class="text_10 pdtb" ><?php echo $arthritisDesc;?></td>
				</tr>
				<?php }
					if($diabetes == "Yes" || $diabetes == "No" ){ 
				?>		
				<tr>
					<td class="text_10 pdtb" >Diabetes</td>
					<td class="text_10 pdtb" style="text-align:center;"><?php echo $diabetes;?></td>
					<td class="text_10 pdtb" ><?php echo $diabetesDesc;?></td>
				</tr>
				<?php }
					if($recreationalDrug == "Yes" || $recreationalDrug == "No" ){ 
				?>	
				<tr>
					<td class="text_10 pdtb" >Recreational Drug Use</td>
					<td class="text_10 pdtb" style="text-align:center;"><?php echo $recreationalDrug;?></td>
					<td class="text_10 pdtb" ><?php echo $recreationalDrugDesc;?></td>
				</tr>
				<?php }
					if($giGerd == "Yes" || $giGerd == "No" ){ 
				?>					
				<tr>
					<td class="text_10 pdtb" >GI - GERD / PUD / Liver Disease / Hepatitis</td>
					<td class="text_10 pdtb" style="text-align:center;"><?php echo $giGerd;?></td>
					<td class="text_10 pdtb" ><?php echo $giGerdDesc;?></td>
				</tr>
				<?php }
					if($ocular == "Yes" || $ocular == "No" ){ 
				?>	
				<tr>
					<td class="text_10 pdtb" >Ocular</td>
					<td class="text_10 pdtb" style="text-align:center;"><?php echo $ocular;?></td>
					<td class="text_10 pdtb" ><?php echo $ocularDesc;?></td>
				</tr>
				<?php }
					if($kidneyDisease == "Yes" || $kidneyDisease == "No" ){ 
				?>		
				<tr>
					<td class="text_10 pdtb" >Kidney Disease, Dialysis, G-U</td>
					<td class="text_10 pdtb" style="text-align:center;"><?php echo $kidneyDisease;?></td>
					<td class="text_10 pdtb" ><?php echo $kidneyDiseaseDesc;?></td>
				</tr>	
				<?php }
					if($hivAutoimmune == "Yes" || $hivAutoimmune == "No" ){ 
				?>
				<tr>
					<td class="text_10 pdtb" >HIV, Autoimmune Diseases, Contagious Diseases</td>
					<td class="text_10 pdtb" style="text-align:center;"><?php echo $hivAutoimmune;?></td>
					<td class="text_10 pdtb" ><?php echo $hivAutoimmuneDesc;?></td>
				</tr>
				<?php }
					if($historyCancer == "Yes" || $historyCancer == "No" ){ 
				?>				
				<tr>
					<td class="text_10 pdtb" >History of Cancer</td>
					<td class="text_10 pdtb" style="text-align:center;"><?php echo $historyCancer;?></td>
					<td class="text_10 pdtb" ><?php echo $historyCancerDesc;?></td>
				</tr>
				<?php }
					if($organTransplant == "Yes" || $organTransplant == "No" ){ 
				?>		
				<tr>
					<td class="text_10 pdtb" >Organ Transplant</td>
					<td class="text_10 pdtb" style="text-align:center;"><?php echo $organTransplant;?></td>
					<td class="text_10 pdtb" ><?php echo $organTransplantDesc;?></td>
				</tr>
				<?php }
					if($badReaction == "Yes" || $badReaction == "No" ){ 
				?>		
				<tr>
					<td class="text_10 pdtb" >A Bad Reaction to Local or General Anesthesia</td>
					<td class="text_10 pdtb" style="text-align:center;"><?php echo $badReaction;?></td>
					<td class="text_10 pdtb" ><?php echo $badReactionDesc;?></td>
				</tr>
				<?php }
					if($highCholesterol == "Yes" || $highCholesterol == "No" ){ 
				?>		
				<tr>
					<td class="text_10 pdtb" >High Cholesterol</td>
					<td class="text_10 pdtb" style="text-align:center;"><?php echo $highCholesterol;?></td>
					<td class="text_10 pdtb" ><?php echo $highCholesterolDesc;?></td>
				</tr>
				<?php }
					if($thyroid == "Yes" || $thyroid == "No" ){ 
				?>	
				<tr>
					<td class="text_10 pdtb" >Thyroid Problems</td>
					<td class="text_10 pdtb" style="text-align:center;"><?php echo $thyroid;?></td>
					<td class="text_10 pdtb" ><?php echo $thyroidDesc;?></td>
				</tr>
				<?php }
					if($ulcer == "Yes" || $ulcer == "No" ){ 
				?>	
				<tr>
					<td class="text_10 pdtb" >Ulcers</td>
					<td class="text_10 pdtb" style="text-align:center;"><?php echo $ulcer;?></td>
					<td class="text_10 pdtb" ><?php echo $ulcerDesc;?></td>
				</tr>
				<?php }
					if($heartExam == "Yes" || $heartExam == "No" ){ 
				?>		
			 	<tr>
					<td class="text_10 pdtb" >Heart Exam done with stethoscope - Normal</td>
					<td class="text_10 pdtb" style="text-align:center;"><?php echo $heartExam;?></td>
					<td class="text_10 pdtb" ><?php echo $heartExamDesc;?></td>
				</tr>
				<?php }
					if($lungExam == "Yes" || $lungExam == "No" ){
				?>	
				<tr>
					<td class="text_10 pdtb" >Lung Exam done with stethoscope - Normal	</td>
					<td class="text_10 pdtb" style="text-align:center;"><?php echo $lungExam;?></td>
					<td class="text_10 pdtb" ><?php echo $lungExamDesc;?></td>
				</tr>
				<?php }
					if($otherHistoryPhysical != ""){
				?>	
				<tr>
					<td class="text_10 pdtb" >Other	</td>
					<td class="text_10 pdtb" style="text-align:center;"></td>
					<td class="text_10 pdtb" ><?php echo $otherHistoryPhysical;?></td>
				</tr>
				<?php } ?>
				<!--- Surgery Center Imported Custom Field Work --->
				<?php
					$qry_custom_records="SELECT
											ques_id,
											ques_status,
											ques_desc
										FROM
											`surgerycenter_pt_history_physical_ques`
										WHERE
											patient_id = '".$pid."'
										AND
											ques_id!= 0
										AND
										(
											ques_status!= ''
										OR
											ques_desc!= ''	
										)
										";
					$exe_custom_records=imw_query($qry_custom_records);	
					if( imw_num_rows($exe_custom_records) > 0 )
					{
						while($res_custom_records = imw_fetch_assoc($exe_custom_records))
						{
							$ques_id	=	$res_custom_records['ques_id'];
							$ques_status=	$res_custom_records['ques_status'];
							$ques_desc	=	$res_custom_records['ques_desc'];
							
							$qry_custom_ques="
										SELECT
											name
										FROM
											`surgerycenter_history_physical_ques`
										WHERE
											id = '".$ques_id."'
										AND
											deleted = 0
										";
							$exe_custom_ques=imw_query($qry_custom_ques);
							
							$res_custom_ques=imw_fetch_assoc($exe_custom_ques);
							
							$name	=	$res_custom_ques['name'];
						?>
						<tr>
							<td class="text_10 pdtb" ><?php echo $name; ?></td>
							<td class="text_10 pdtb" style="text-align:center;"><?php echo $ques_status; ?></td>
							<td class="text_10 pdtb" ><?php echo $ques_desc;?></td>
						</tr>
						<?php	
						}	
					}		
				?>
				
			</table>	
		</td>
	</tr>
	<tr>
		<td>
			<table>
				<tr valign="top" height="30" style="padding-top:35px;" bgcolor="#000000">
					<td align="center" colspan="4" class="text_10b midBlue pdtb">OCULAR SX/PROCEDURES</td>
				</tr>
				<tr>
					<td class="text_10b" style="width:200px;">Name</td>
					<td class="text_10b" style="width:100px;">Site</td>
					<td class="text_10b" style="width:150px;">Date</td>
					<td class="text_10b" style="width:250px;">Comments</td>
				</tr>
				<?php
					//Ocular Sx/Procedure Data Shown
					$html = '';
					if( is_array($procArr['ocu']) && count($procArr['ocu']) )	{

						foreach($procArr['ocu'] as $proc){
							$html .= '<tr>';
							$html .= '<td>'.$proc['name'].'</td>';	
							$html .= '<td>'.$proc['site'].'</td>';	
							$html .= '<td>'.$proc['beg_date'].'</td>';	
							$html .= '<td>'.$proc['comment'].'</td>';	
							$html .= '</tr>';
						}
					} else{
						$html = '<tr><td colspan="4" class="text-center">No Record found</td></tr>';
					}	
					echo $html;
				?>
			</table>	
		</td>
	</tr>
	<tr>
		<td>
			<table>
				<tr valign="top" height="30" style="padding-top:35px;" bgcolor="#000000">
					<td align="center" colspan="3" class="text_10b midBlue pdtb" >OTHER SX/PROCEDURES</td>
				</tr>
				<tr>
					<td class="text_10b" style="width:300px;">Name</td>
					<td class="text_10b" style="width:150px;">Date</td>
					<td class="text_10b" style="width:250px;">Comments</td>
				</tr>
					<?php
						//Other Sx/Procedure Data Shown
						$html = '';
						if( is_array($procArr['sys']) && count($procArr['sys']) )	{

							foreach($procArr['sys'] as $proc){
								$html .= '<tr>';
								$html .= '<td>'.$proc['name'].'</td>';	
								$html .= '<td>'.$proc['beg_date'].'</td>';	
								$html .= '<td>'.$proc['comment'].'</td>';	
								$html .= '</tr>';
							}
						} else{
							$html = '<tr><td colspan="3" class="text-center">No Record found</td></tr>';
						}	
						echo $html;
					?>
			</table>
		</td>
	</tr>	
</table>	
</page>

<?php
$headDataALL = ob_get_clean(); //Whole Data

if(trim($headDataALL) != "")
{
	$pdf_file_name = "hp_print_pt_id_".$pid.".pdf"; //PDF File Name
	$html_file_path = write_html($headDataALL); // HTML File Name
	
	//$hp_make_pdf Parameter Is Coming From Save.php File. It Be True When Hit Save & Reviewed Button
	if(!empty($hp_make_pdf) && $hp_make_pdf == "yes") 
	{	
		//PDF File Path
		$pdfPath=data_path()."PatientId_".$pid."/".$pdf_file_name; 
		
		$db_file_path = "/PatientId_".$pid."/".$pdf_file_name;
		
		//Create Patient Directory, Incase It Not Exists
		$dir_path = $GLOBALS['fileroot']."/data/".constant('PRACTICE_PATH');
		$folder_path = $dir_path."/PatientId_".$pid;
		if(!is_dir($folder_path)){
			mkdir($folder_path,0755,true);
		}
		
		//Get Existing Records To Overwrite Else Insert New
		$qry = "SELECT
					id
				FROM 
					`surgery_center_patient_scan_docs`
				WHERE	
					patient_id = '".$pid."'
				AND
					scan_doc_add = '".$db_file_path."'
				AND
					scan_type_folder = '3'
				AND
					surgery_patient_scan = 'hnp'
				";
		$qry_row = imw_query($qry);
		if( imw_num_rows($qry_row) > 0 )
		{
			$res = imw_fetch_assoc($qry_row);
			
			$id 					= $res['id'];
			
			$qry_update="UPDATE 
							`surgery_center_patient_scan_docs`
						SET	
							created_date = '".date('Y-m-d')."',
							surgery_patient_scan_operator ='".$_SESSION['authId']."',
							surgery_patient_scan_date = '".date('Y-m-d h:i:s')."'
						WHERE
							id = '".$id ."'
						";
			$qry_update_exe = imw_query($qry_update);		
		}
		else
		{
			$qry_insert="INSERT INTO
							`surgery_center_patient_scan_docs`
						SET
							patient_id = '".$pid."',
							scan_doc_add = '".$db_file_path."',
							scan_type_folder = '3',
							created_date = '".date('Y-m-d')."',
							surgery_patient_scan_operator ='".$_SESSION['authId']."',
							surgery_patient_scan_date = '".date('Y-m-d h:i:s')."',
							surgery_patient_scan = 'hnp'
						";
			$qry_insert_exe = imw_query($qry_insert);				
		}	
		
		if(file_exists($pdfPath)){ unlink($pdfPath); }
		
		$myHTTPAddress = $GLOBALS['php_server'].'/library/html_to_pdf/createPdf.php';
		
		$urlPdfFile=$myHTTPAddress."?setIgnoreAuth=true&op=p&saveOption=F&pdf_name=".$pdfPath."&file_location=".$html_file_path;
		
		$curNew = curl_init();
		curl_setopt($curNew,CURLOPT_URL,$urlPdfFile);
		curl_setopt($curNew, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curNew, CURLOPT_SSL_VERIFYPEER, false); 
		curl_setopt($curNew, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curNew, CURLOPT_FOLLOWLOCATION, true); 
		$data = curl_exec($curNew);
	}
	else
	{	
	?>
	<html>	
		<title>Print History Physical</title>
		<body>
			<script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/js/jquery.min.1.12.4.js"></script>
			<script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/js/common.js"></script>
			<script type="text/javascript">
				var file_name = '<?php print $pdf_file_name; ?>';
				top.JS_WEB_ROOT_PATH = '<?php echo $GLOBALS['webroot']; ?>';
				html_to_pdf('<?php echo $html_file_path; ?>','p',file_name);
			</script>
		</body>
	</html>
	<?php
	}
}
else
{
	if(empty($hp_make_pdf))
	{		
?>
	<table align="center" width="100%" border="0" cellpadding="1" cellspacing="1">
		<tr class="text_9" height="20" bgcolor="#EAF0F7" valign="top">
			<td align="center">No Result.</td>
		</tr>
	</table>
<?php
	}
}
?>