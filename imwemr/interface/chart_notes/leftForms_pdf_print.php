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
File: leftForms_pdf_print.php
Purpose: This file provides Tests in print view.
Access Type : Include file
*/
?>
<?php
//Function to get Uploaded and scaned JPG images Images Ragarding Tests//

if(!function_exists("getTestImages")){
function getTestImages($testtid,$sectionImageFrom,$patient_id){
		$scnMultiImage=""; 
		$sqlScan = "SELECT scan_id, image_name, file_path, file_type,testing_docscan,multi_doc_upload_comment FROM ".constant("IMEDIC_SCAN_DB").".scans ".
					 "WHERE patient_id = '$patient_id' AND image_form = '".$sectionImageFrom."'  AND test_id  = '".$testtid."' "; 		 
		$sqlScanRes = 	imw_query($sqlScan);		 
		if(imw_num_rows($sqlScanRes)>0) {
			//start code for a-scanned image
			while($sqlScanRow = imw_fetch_array($sqlScanRes)) {
				$scn_img_name = $sqlScanRow['image_name'];
				$s_imagename = $sqlScanRow['file_path'];
				$s_file_type = $sqlScanRow['file_type'];
				
				if($s_imagename && ($s_file_type!='application/pdf' && $s_file_type!='image/png')) {
					$scndirPath = '../main/uploaddir'.$s_imagename;
					$scn_dir_real_path = realpath($scndirPath);
					$scn_img_name = substr($s_imagename,strrpos($s_imagename,'/')+1);	
					//copy($scn_dir_real_path,'../common/new_html2pdf/'.$scn_img_name);
						$scndirPath = $scn_img_name;
						if(file_exists($scn_dir_real_path)){
						$scnfileSize = getimagesize($scn_dir_real_path);
						if($scnfileSize[0]>700 ){
							$scnimageWidth2 = ManageData::imageResize($scnfileSize[0],$scnfileSize[1],700);
							$scnMultiImage .= '<tr><td align="left"><img style="cursor:pointer" src="'.$scn_dir_real_path.'" alt="patient Image" '.$scnimageWidth2.'></td></tr>';
						}
						else{
							$scnMultiImage .= '<tr><td align="left"><img style="cursor:pointer" src="'.$scn_dir_real_path.'" alt="patient Image"></td></tr>';
						}
							if(strip_tags($sqlScanRow["testing_docscan"])!=""){$scnMultiImage .= '<tr>
								<td  align="left" class="text_lable">Comments:&nbsp;<span class="text_value">'.strip_tags($sqlScanRow["testing_docscan"]).'</span></td>
							</tr>';
							}
							if(strip_tags($sqlScanRow["multi_doc_upload_comment"])!=""){$scnMultiImage .= '<tr>
								<td  align="left" class="text_lable">Comments:&nbsp;<span class="text_value">'.strip_tags($sqlScanRow["multi_doc_upload_comment"]).'</span></td>
							</tr>';
							}				
					}
				
				}
			}
			
		}
		return $scnMultiImage;
}
}
?>
<!-- VF -->

<?php 
$ivfa=true;
$vf = true;
$nfa = true;
$oct = true;
$pachy = true;
$disc=true;
$external=true;
$topography=true;

if($vf==true){
	if(is_array($_REQUEST["printTestRadioVF"]) && count($_REQUEST["printTestRadioVF"])>0){
		$sqlVFFormQry = imw_query("SELECT * FROM vf WHERE patientId = '".$patient_id."' AND vf_id in (".implode(",",$_REQUEST["printTestRadioVF"]).")");
	}else if($form_id!=""){
		$sqlVFFormQry = imw_query("SELECT * FROM vf WHERE patientId = '".$patient_id."' AND formId = '$form_id' AND purged='0' ");
	}

 
	if(imw_num_rows($sqlVFFormQry)>0){
		while($sqlVFFormRow = imw_fetch_assoc($sqlVFFormQry)){
		extract($sqlVFFormRow);
		if($performedBy){
			$getNameQry = imw_query("SELECT CONCAT_WS(', ', lname, fname) as namePerformer FROM users WHERE id = '$performedBy'");	
			$getNameRow = imw_fetch_assoc($getNameQry);
			$namePerformer = $getNameRow['namePerformer'];
		}
	
			$orderByName="";
			if($ordrby ){
			$getNameQry = imw_query("SELECT CONCAT_WS(', ', lname, fname) as orderByName FROM users WHERE id = '$ordrby '");	
			$getNameRow = imw_fetch_assoc($getNameQry);
			$orderByName = $getNameRow['orderByName'];
			}
		?>
		
				<table style="width:100%;" class="paddingTop" class="border" cellspacing="0" cellpadding="0" >
					<tr>
						<td  valign="middle" class="tb_heading" style="width:100%">VF (<span class="text_value">Exam Date:&nbsp;<?php print FormatDate_show($examDate);?></span>)&nbsp;<span class="text_lable">Order By:&nbsp;</span> <?php echo($orderByName."&nbsp;&nbsp;".FormatDate_show($ordrdt)); ?></td>
					</tr>
				</table>
				<table style="width:100%;"  class="border" cellspacing="0" cellpadding="0" >
					<tr>
						<td  valign="middle" style="width:100%" class="text_lable">VF &nbsp;<span class="text_value"><?php echo($vf_eye."&nbsp;".$elem_gla_mac); ?></span> </td>
					</tr>
				</table>
					<?php
					if($namePerformer || $ptUnderstanding || $diagnosis ){
						?>
				<table cellspacing="0" cellpadding="0" style="width:100%">
						
					<?php if($techComments!=""){?>
						<tr>
							<td  valign="middle" class="text_lable">Technician Comments:&nbsp;<span class="text_value"><?php echo($techComments);?></span>  </td>
						</tr>
					<?php }?>
						<tr>
							<td  valign="middle" class="text_value">
								
								<span class="text_lable">Performed By:&nbsp;</span> <?php echo $namePerformer; ?>
								&nbsp;&nbsp;
								<span class="text_lable">Patient Understanding & Cooperation</span>:&nbsp;<?php echo $ptUnderstanding; ?>
								&nbsp;&nbsp;
								<span class="text_lable">Diagnosis:&nbsp;</span> <?php echo ($diagnosis!="--Select--") ? $diagnosis : ""; ?>
							</td>
						</tr>
						
					</table>
						<?php
					}
					if($reliabilityOd || $reliabilityOs){
						?>
					<table class="border" cellpadding="0" cellspacing="0" style="width:100%">
						<tr>
							<td  valign="middle" class="text_lable">Physician Interpretation:&nbsp;</td>							
							<td valign="middle" class="text_lable">Reliability</td>
							<td valign="middle" class="text_lable"><?php odLable();?></td>
							<td valign="middle" class="text_value"><?php echo $reliabilityOd; ?></td>
							<td style="width:20px;"></td>
							<td valign="middle" class="text_lable"><?php osLable();?></td>
							<td valign="middle" class="text_value"><?php echo $reliabilityOs; ?></td>
						</tr>
					</table>
							
						<?php
					}
					// OD DATA
					$odData ="";
					if($Normal_OD_T) $odData = 'Normal&nbsp;<br>';
					if($Normal_OD_PoorStudy == 1) $odData .= 'Poor Study&nbsp;<br>';
					if($BorderLineDefect_OD_T == 1 || $BorderLineDefect_OD_1 == 1 || $BorderLineDefect_OD_2 == 1 || $BorderLineDefect_OD_3 == 1 || $BorderLineDefect_OD_4 == 1){
						$odData.= '<span style="width:110px;">Border Line Defect</span>';
						$odData.= '<span style="width:50px;">&nbsp;</span>';
						if($BorderLineDefect_OD_T == 1) $odData.= '&nbsp;T&nbsp;';
						if($BorderLineDefect_OD_1 == 1) $odData.= '&nbsp;+1&nbsp;';
						if($BorderLineDefect_OD_2 == 1) $odData.= '&nbsp;+2&nbsp;';
						if($BorderLineDefect_OD_3 == 1) $odData.= '&nbsp;+3&nbsp;';
						if($BorderLineDefect_OD_4 == 1) $odData.= '&nbsp;+4&nbsp;';
					}
					if($Abnormal_OD_T == 1 || $Abnormal_OD_1 == 1 || $Abnormal_OD_2 == 1 || $Abnormal_OD_3 == 1 || $Abnormal_OD_4 == 1){
						$odData.= '<span style="width:110px;">Abnormal </span>';
						$odData.= '<span style="width:50px;">&nbsp;</span>';
						if($Abnormal_OD_T == 1) $odData.= '&nbsp;T&nbsp;';
						if($Abnormal_OD_1 == 1) $odData.= '&nbsp;+1&nbsp;';
						if($Abnormal_OD_2 == 1) $odData.= '&nbsp;+2&nbsp;';
						if($Abnormal_OD_3 == 1) $odData.= '&nbsp;+3&nbsp;';
						if($Abnormal_OD_4 == 1) $odData.= '&nbsp;+4&nbsp;';
					}
					if($NasalSteep_OD_Superior == 1 || $NasalSteep_OD_S_T == 1 || $NasalSteep_OD_S_1 == 1 || $NasalSteep_OD_S_2 == 1 || $NasalSteep_OD_S_3 == 1 || $NasalSteep_OD_S_4 == 1){
						$odData.= '<br><span style="width:110px;">Nasal Steep </span>';						
						if($NasalSteep_OD_Superior == 1) $odData.= '<span style="width:50px;">Superior </span>';
						if($NasalSteep_OD_S_T == 1) $odData.= '&nbsp;T&nbsp;';
						if($NasalSteep_OD_S_1 == 1) $odData.= '&nbsp;+1&nbsp;';
						if($NasalSteep_OD_S_2 == 1) $odData.= '&nbsp;+2&nbsp;';
						if($NasalSteep_OD_S_3 == 1) $odData.= '&nbsp;+3&nbsp;';
						if($NasalSteep_OD_S_4 == 1) $odData.= '&nbsp;+4&nbsp;';
					}
					if($NasalSteep_OD_Inferior == 1 || $NasalSteep_OD_I_T == 1 || $NasalSteep_OD_I_1 == 1 || $NasalSteep_OD_I_2 == 1 || $NasalSteep_OD_I_3 == 1 || $NasalSteep_OD_I_4 == 1){
						$odData.= '<br><span style="width:110px;">Nasal Steep</span>';
						if($NasalSteep_OD_Inferior == 1) $odData.= '<span style="width:50px;">Inferior </span>';
						if($NasalSteep_OD_I_T == 1) $odData.= '&nbsp;T&nbsp;';
						if($NasalSteep_OD_I_1 == 1) $odData.= '&nbsp;+1&nbsp;';
						if($NasalSteep_OD_I_2 == 1) $odData.= '&nbsp;+2&nbsp;';
						if($NasalSteep_OD_I_3 == 1) $odData.= '&nbsp;+3&nbsp;';
						if($NasalSteep_OD_I_4 == 1) $odData.= '&nbsp;+4&nbsp;';
					}
					if($Arcuatedefect_OD_Superior == 1 || $Arcuatedefect_OD_S_T == 1 || $Arcuatedefect_OD_S_1 == 1 || $Arcuatedefect_OD_S_2 == 1 || $Arcuatedefect_OD_S_3 == 1 || $Arcuatedefect_OD_S_4 == 1){
						$odData.= '<br><span style="width:110px;">Arcuate defect </span>';
						if($Arcuatedefect_OD_Superior == 1) $odData.= '<span style="width:50px;">Superior </span>';
						if($Arcuatedefect_OD_S_T == 1) $odData.= '&nbsp;T&nbsp;';
						if($Arcuatedefect_OD_S_1 == 1) $odData.= '&nbsp;+1&nbsp;';
						if($Arcuatedefect_OD_S_2 == 1) $odData.= '&nbsp;+2&nbsp;';
						if($Arcuatedefect_OD_S_3 == 1) $odData.= '&nbsp;+3&nbsp;';
						if($Arcuatedefect_OD_S_4 == 1) $odData.= '&nbsp;+4&nbsp;';
					}
					if($Arcuatedefect_OD_Inferior == 1 || $Arcuatedefect_OD_I_T == 1 || $Arcuatedefect_OD_I_1 == 1 || $Arcuatedefect_OD_I_2 == 1 || $Arcuatedefect_OD_I_3 == 1 || $Arcuatedefect_OD_I_4 == 1){
						$odData.= '<br><span style="width:110px;">Arcuate defect</span>';
						if($Arcuatedefect_OD_Inferior == 1) $odData.= '<span style="width:50px;">Inferior </span>';
						if($Arcuatedefect_OD_I_T == 1) $odData.= '&nbsp;T&nbsp;';
						if($Arcuatedefect_OD_I_1 == 1) $odData.= '&nbsp;+1&nbsp;';
						if($Arcuatedefect_OD_I_2 == 1) $odData.= '&nbsp;+2&nbsp;';
						if($Arcuatedefect_OD_I_3 == 1) $odData.= '&nbsp;+3&nbsp;';
						if($Arcuatedefect_OD_I_4 == 1) $odData.= '&nbsp;+4&nbsp;';
					}
					if($Defect_OD_Central == 1 || $Defect_OD_Superior == 1 || $Defect_OD_Inferior == 1 || $Defect_OD_Scattered == 1
						|| 
						$Defect_OD_T == 1 || $Defect_OD_1 == 1 || $Defect_OD_2 == 1 || $Defect_OD_3 == 1 || $Defect_OD_4 == 1
						){
						$odData.= '<br><span style="width:110px;">Defect </span>';
						if($Defect_OD_Central == 1) $odData.= '<span style="width:50px;">Central </span>';
						if($Defect_OD_Superior == 1) $odData.= '<span style="width:50px;">Superior </span>';
						if($Defect_OD_Inferior == 1) $odData.= '<span style="width:50px;">Inferior </span>';
						if($Defect_OD_Scattered == 1) $odData.= '<span style="width:50px;">Scattered </span>';
						if($Defect_OD_Central == 1 || $Defect_OD_Superior == 1 || $Defect_OD_Inferior == 1 || $Defect_OD_Scattered == 1)
							$odData.= '<br><span style="width:110px;">&nbsp;</span>';
						if($Defect_OD_T == 1) $odData.= '&nbsp;T&nbsp;';
						if($Defect_OD_1 == 1) $odData.= '&nbsp;+1&nbsp;';
						if($Defect_OD_2 == 1) $odData.= '&nbsp;+2&nbsp;';
						if($Defect_OD_3 == 1) $odData.= '&nbsp;+3&nbsp;';
						if($Defect_OD_4 == 1) $odData.= '&nbsp;+4&nbsp;';
					}				
					if($blindSpot_OD_T == 1 || $blindSpot_OD_1 == 1 || $blindSpot_OD_2 == 1 || $blindSpot_OD_3 == 1 || $blindSpot_OD_4 == 1){
						$odData.= '<br><span style="width:110px;">Increase size of Blind&nbsp;spot</span>';
						if($blindSpot_OD_T == 1) $odData.= '&nbsp;T&nbsp;';
						if($blindSpot_OD_1 == 1) $odData.= '&nbsp;+1&nbsp;';
						if($blindSpot_OD_2 == 1) $odData.= '&nbsp;+2&nbsp;';
						if($blindSpot_OD_3 == 1) $odData.= '&nbsp;+3&nbsp;';
						if($blindSpot_OD_4 == 1) $odData.= '&nbsp;+4&nbsp;';
					}
					if($NoSigChange_OD == 1) $odData.= '&nbsp;No Sig. Change&nbsp;';
						if($Improved_OD == 1) $odData.= '&nbsp;Improved&nbsp;';
						if($IncAbn_OD == 1) $odData.= '&nbsp;Inc. Abn&nbsp;';
					if($iopTrgtOd  !="") $odData.= '&nbsp;IOP Target:'.$iopTrgtOd.'&nbsp;';
					// OS DATA
					$osData = "";
					if($Normal_OS_T) $osData = 'Normal&nbsp;<br>';			
					if($Normal_OS_PoorStudy == 1) $osData .= 'Poor Study&nbsp;<br>';	
					if($BorderLineDefect_OS_T == 1 || $BorderLineDefect_OS_1 == 1 || $BorderLineDefect_OS_2 == 1 || $BorderLineDefect_OS_3 == 1 || $BorderLineDefect_OS_4 == 1){
						$osData.= '<span style="width:110px;">Border Line Defect </span>';
						$osData.= '<span style="width:50px;">&nbsp;</span>';
						if($BorderLineDefect_OS_T == 1) $osData.= '&nbsp;T&nbsp;';
						if($BorderLineDefect_OS_1 == 1) $osData.= '&nbsp;+1&nbsp;';
						if($BorderLineDefect_OS_2 == 1) $osData.= '&nbsp;+2&nbsp;';
						if($BorderLineDefect_OS_3 == 1) $osData.= '&nbsp;+3&nbsp;';
						if($BorderLineDefect_OS_4 == 1) $osData.= '&nbsp;+4&nbsp;';
					}
					if($Abnormal_OS_T == 1 || $Abnormal_OS_1 == 1 || $Abnormal_OS_2 == 1 || $Abnormal_OS_3 == 1 || $Abnormal_OS_4 == 1){
						$osData.= '<br><span style="width:110px;">Abnormal </span>';
						$osData.= '<span style="width:50px;">&nbsp;</span>';
						if($Abnormal_OS_T == 1) $osData.= '&nbsp;T&nbsp;';
						if($Abnormal_OS_1 == 1) $osData.= '&nbsp;+1&nbsp;';
						if($Abnormal_OS_2 == 1) $osData.= '&nbsp;+2&nbsp;';
						if($Abnormal_OS_3 == 1) $osData.= '&nbsp;+3&nbsp;';
						if($Abnormal_OS_4 == 1) $osData.= '&nbsp;+4&nbsp;';
					}
					if($NasalSteep_OS_Superior == 1 || $NasalSteep_OS_S_T == 1 || $NasalSteep_OS_S_1 == 1 || $NasalSteep_OS_S_2 == 1 || $NasalSteep_OS_S_3 == 1 || $NasalSteep_OS_S_4 == 1){
						$osData.= '<br><span style="width:110px;">Nasal Steep </span>';						
						if($NasalSteep_OS_Superior == 1) $osData.= '<span style="width:50px;">Superior </span>';
						if($NasalSteep_OS_S_T == 1) $osData.= '&nbsp;T&nbsp;';
						if($NasalSteep_OS_S_1 == 1) $osData.= '&nbsp;+1&nbsp;';
						if($NasalSteep_OS_S_2 == 1) $osData.= '&nbsp;+2&nbsp;';
						if($NasalSteep_OS_S_3 == 1) $osData.= '&nbsp;+3&nbsp;';
						if($NasalSteep_OS_S_4 == 1) $osData.= '&nbsp;+4&nbsp;';
					}
					if($NasalSteep_OS_Inferior == 1 || $NasalSteep_OS_I_T == 1 || $NasalSteep_OS_I_1 == 1 || $NasalSteep_OS_I_2 == 1 || $NasalSteep_OS_I_3 == 1 || $NasalSteep_OS_I_4 == 1){
						$osData.= '<br><span style="width:110px;">Nasal Steep </span>';
						if($NasalSteep_OS_Inferior == 1) $osData.= '<span style="width:50px;">Inferior </span>';
						if($NasalSteep_OS_I_T == 1) $osData.= '&nbsp;T&nbsp;';
						if($NasalSteep_OS_I_1 == 1) $osData.= '&nbsp;+1&nbsp;';
						if($NasalSteep_OS_I_2 == 1) $osData.= '&nbsp;+2&nbsp;';
						if($NasalSteep_OS_I_3 == 1) $osData.= '&nbsp;+3&nbsp;';
						if($NasalSteep_OS_I_4 == 1) $osData.= '&nbsp;+4&nbsp;';
					}
					if($Arcuatedefect_OS_Superior == 1 || $Arcuatedefect_OS_S_T == 1 || $Arcuatedefect_OS_S_1 == 1 || $Arcuatedefect_OS_S_2 == 1 || $Arcuatedefect_OS_S_3 == 1 || $Arcuatedefect_OS_S_4 == 1){
						$osData.= '<br><span style="width:110px;">Arcuate defect </span>';
						if($Arcuatedefect_OS_Superior == 1) $osData.= '<span style="width:50px;">Superior </span>';
						if($Arcuatedefect_OS_S_T == 1) $osData.= '&nbsp;T&nbsp;';
						if($Arcuatedefect_OS_S_1 == 1) $osData.= '&nbsp;+1&nbsp;';
						if($Arcuatedefect_OS_S_2 == 1) $osData.= '&nbsp;+2&nbsp;';
						if($Arcuatedefect_OS_S_3 == 1) $osData.= '&nbsp;+3&nbsp;';
						if($Arcuatedefect_OS_S_4 == 1) $osData.= '&nbsp;+4&nbsp;';
					}
					if($Arcuatedefect_OS_Inferior == 1 || $Arcuatedefect_OS_I_T == 1 || $Arcuatedefect_OS_I_1 == 1 || $Arcuatedefect_OS_I_2 == 1 || $Arcuatedefect_OS_I_3 == 1 || $Arcuatedefect_OS_I_4 == 1){
						$osData.= '<br><span style="width:110px;">Arcuate defect </span>';
						if($Arcuatedefect_OS_Inferior == 1) $osData.= '<span style="width:50px;">Inferior </span>';
						if($Arcuatedefect_OS_I_T == 1) $osData.= '&nbsp;T&nbsp;';
						if($Arcuatedefect_OS_I_1 == 1) $osData.= '&nbsp;+1&nbsp;';
						if($Arcuatedefect_OS_I_2 == 1) $osData.= '&nbsp;+2&nbsp;';
						if($Arcuatedefect_OS_I_3 == 1) $osData.= '&nbsp;+3&nbsp;';
						if($Arcuatedefect_OS_I_4 == 1) $osData.= '&nbsp;+4&nbsp;';
					}
					if($Defect_OS_Central == 1 || $Defect_OS_Superior == 1 || $Defect_OS_Inferior == 1 || $Defect_OS_Scattered == 1
						|| 
						$Defect_OS_T == 1 || $Defect_OS_1 == 1 || $Defect_OS_2 == 1 || $Defect_OS_3 == 1 || $Defect_OS_4 == 1
						){
						$osData.= '<br><span style="width:110px;">Defect </span>';
						if($Defect_OS_Central == 1) $osData.= '<span style="width:50px;">Central </span>';
						if($Defect_OS_Superior == 1) $osData.= '<span style="width:50px;">Superior </span>';
						if($Defect_OS_Inferior == 1) $osData.= '<span style="width:50px;">Inferior </span>';
						if($Defect_OS_Scattered == 1) $osData.= '<span style="width:50px;">Scattered </span>';
						if($Defect_OS_Central == 1 || $Defect_OS_Superior == 1 || $Defect_OS_Inferior == 1 || $Defect_OS_Scattered == 1)
							$osData.= '<br><span style="width:110px;">&nbsp;</span>';
						if($Defect_OS_T == 1) $osData.= '&nbsp;T&nbsp;';
						if($Defect_OS_1 == 1) $osData.= '&nbsp;+1&nbsp;';
						if($Defect_OS_2 == 1) $osData.= '&nbsp;+2&nbsp;';
						if($Defect_OS_3 == 1) $osData.= '&nbsp;+3&nbsp;';
						if($Defect_OS_4 == 1) $osData.= '&nbsp;+4&nbsp;';
					}				
					if($blindSpot_OS_T == 1 || $blindSpot_OS_1 == 1 || $blindSpot_OS_2 == 1 || $blindSpot_OS_3 == 1 || $blindSpot_OS_4 == 1){
						$osData.= '<br><span style="width:110px;">Increase size of Blind&nbsp;spot</span>';
						if($blindSpot_OS_T == 1) $osData.= '&nbsp;T&nbsp;';
						if($blindSpot_OS_1 == 1) $osData.= '&nbsp;+1&nbsp;';
						if($blindSpot_OS_2 == 1) $osData.= '&nbsp;+2&nbsp;';
						if($blindSpot_OS_3 == 1) $osData.= '&nbsp;+3&nbsp;';
						if($blindSpot_OS_4 == 1) $osData.= '&nbsp;+4&nbsp;';
					}
					if($NoSigChange_OS == 1) $osData.= '&nbsp;No Sig. Change&nbsp;';
						if($Improved_OS == 1) $osData.= '&nbsp;Improved&nbsp;';
						if($IncAbn_OS == 1) $osData.= '&nbsp;Inc. Abn&nbsp;';
						if($iopTrgtOs  !="") $osData.= '&nbsp;IOP Target:'.$iopTrgtOs.'&nbsp;';
				if($osData!="" || $odData!="" || $Others_OD!="" || $Others_OS!=""){
						?>
					<table  cellpadding="0" cellspacing="0" style="width:100%;" class="paddingTop border">
						<tr>
							<td  valign="middle" class="tb_heading" style="width:100%" colspan="2">Test Results</td>
						</tr>
						<tr>
							<td class="text_value" style="width:50%" align="left"><?php odLable();?></td>
							<td class="text_value" style="width:50%" align="left"><?php osLable();?></td>
						</tr>
					<?php	
						if($odData || $osData){
						?>
						<tr>
							<td class="text_value" style="width:50%" align="left" valign="top"><?php echo  $odData; ?></td>
							<td class="text_value" style="width:50%" align="left" valign="top"><?php echo  $osData; ?></td>
						</tr>
					<?php
						}	
					if($Others_OD!="" || $Others_OS!=""){
						?>
							<tr>
								<td class="text_value" style="width:50%" align="left"><?php echo $Others_OD; ?></td>
								<td class="text_value" style="width:50%" align="left"><?php echo $Others_OS; ?></td>
							</tr>
						<?php				
					}
					?>
					</table>
						<?php
					}
						$Others_OD = '';
						$Others_OS = '';
						$treatment = '';
						
					
						
						if($stable == 1) $treatment.= 'Stable&nbsp;&nbsp;';
						if($contiMeds == 1) $treatment.= '&nbsp;Continue Meds &nbsp;';
						if($monitorIOP == 1) $treatment.= '&nbsp;Monitor IOP &nbsp;';
						if($fuApa == 1) $treatment.= 'F/U APA&nbsp;&nbsp;';
						if($tech2InformPt == 1) $treatment.= '&nbsp;Tech to inform Pt.&nbsp;';
						if($ptInformed == 1) $treatment.= 'Pt informed of results';
						if($ptInformedNv==1)$treatment.= '&nbsp;Informed Pt. result next visit.&nbsp;';
						if($rptTst1yr==1){$treatment.= '&nbsp;Repeat test 1 year &nbsp;';}
						
						
						if($stable == 1 || $fuApa == 1 || $ptInformed == 1 || $rptTst1yr==1 || $tech2InformPt == 1 || $contiMeds==1 || $monitorIOP==1 || $ptInformedNv==1){
							?>
						<table cellspacing="0" cellpadding="0"  style="width:100%">
							<tr>
								<td  align="left" class="text_lable" style="width:100%;">Treatment/Prognosis:&nbsp;<span class="text_value"><?php echo $treatment; ?></span></td>
							</tr>
						</table>
							
							<?php
						}
					
					if($comments!=""){
					?>
						<table cellspacing="0" cellpadding="0"  style="width:100%">
							<tr>
								<td  align="left" class="text_lable">Comments:&nbsp;<span class="text_value"><?php echo $comments; ?></span></td>
							</tr>
							
						</table>
					<?php
					} 
					///Add VF Images//
					$imagesHtml=getTestImages($vf_id,$sectionImageFrom="VF",$patient_id);
					if($imagesHtml!=""){
						echo('<table cellspacing="0" cellpadding="0"  style="width:100%">
							 
								'.$imagesHtml.' 
							</table>');
					} 
					$imagesHtml="";
					//End VF Images
					$comments = '';
					if($phyName){
						$getNameQry = imw_query("SELECT CONCAT_WS(', ', lname, fname) as phyName FROM users WHERE id = '$phyName'");	
						$getNameRow = imw_fetch_assoc($getNameQry);
						$phyName = str_replace(", ,"," ",$getNameRow['phyName']);
						?>
<table cellspacing="0" cellpadding="0"  style="width:100%">
		<tr>
			<td  align="left" class="text_value" style="width:100%;"><span class="text_lable">Performed By:&nbsp;</span> <?php echo $phyName; ?></td>
		</tr>
</table>
						<?php
					}
					?>
				
		
		<?php 
	}//End of while
  }
}
?>
<!-- VF -->


<!-- NFA -->
<?php 
if($nfa==true){
if(is_array($_REQUEST["printTestRadioHRT"]) && count($_REQUEST["printTestRadioHRT"])>0){
	$sql = "SELECT * FROM nfa WHERE patient_id = '".$patient_id."' AND nfa_id in(".implode(",",$_REQUEST["printTestRadioHRT"]).")";	
	}else if($form_id!=""){
		$sql = "SELECT * FROM nfa WHERE patient_id = '".$patient_id."' AND form_id = '$form_id' AND purged='0' ";	
	}
					
	$rowResTemp = imw_query($sql);
	if($rowResTemp){
		while($row=imw_fetch_array($rowResTemp)){
		extract($row);
		?>
	
				<table style="width:100%;" class="paddingTop" class="border" cellspacing="0" cellpadding="0" >
					<tr>
						<td valign="middle" class="tb_heading" style="width:100%;">HRT (<span class="text_value">Exam Date:&nbsp;<?php print FormatDate_show($examDate);?></span>)</td>
					</tr>
				</table>
					<?php
					if($scanLaserEye){
						if($scanLaserEye == 'OU') $scanLaserEye = '<span style="color:purple;">'.$scanLaserEye.'</span>';
						if($scanLaserEye == 'OD') $scanLaserEye = '<span style="color:blue;">'.$scanLaserEye.'</span>';
						if($scanLaserEye == 'OS') $scanLaserEye = '<span style="color:green;">'.$scanLaserEye.'</span>';
						?>
<table cellspacing="0" cellpadding="0"  style="width:100%">
						<tr>
							<td  valign="middle" class="text_lable">Scanning Laser/NFA:&nbsp; <?php echo $scanLaserEye; ?></td>
						</tr>
</table>	
						<?php
					}
					if($performedBy || $ptUndersatnding){
						$performedByQry = imw_query("SELECT CONCAT_WS(', ', lname, fname) as performedBy FROM users WHERE id = '$performedBy'");	
						$performedByRow = imw_fetch_assoc($performedByQry);
						$performedBy = str_replace(", ,"," ",$performedByRow['performedBy']);
						?>
<table cellspacing="0" cellpadding="0"  style="width:100%">
					<?php if($techComments!=""){?>
						<tr>
							<td  valign="middle" class="text_lable" colspan="2">Technician Comments:&nbsp;<span class="text_value"><?php echo($techComments);?></span> </td>
						</tr>
					<?php }?>
						<tr>
							
							<td align="left" class="text_value"><span class="text_lable">Performed By:&nbsp;</span> <?php echo $performedBy; ?></td>
							<td align="left" class="text_value"><span class="text_lable">Patient Understanding & Cooperation</span>:&nbsp;<?php echo $ptUndersatnding; ?></td>
						</tr>
</table>
						<?php
					}
					if($diagnosis && $diagnosis!="--Select--"){
						?>
<table cellspacing="0" cellpadding="0"  style="width:100%">
						<tr>
							<td  align="left" class="text_value"><span class="text_lable">Diagnosis:&nbsp;</span> <?php echo ($diagnosis!="--Select--") ? $diagnosis : ""; ?></td>
						</tr>
			</table>
						<?php
					}
					if($reliabilityOd || $reliabilityOs){
						?>
					<table class="border" cellpadding="0" cellspacing="0" style="width:100%">
						<tr>	
							<td  valign="middle" class="text_lable">Physician Interpretation:&nbsp;</td>						
							<td valign="middle" class="text_lable">Reliability</td>
							<td valign="middle" class="text_lable"><?php odLable();?></td>
							<td valign="middle" class="text_value"><?php echo $reliabilityOd; ?></td>
							<td style="width:20px;"></td>
							<td valign="middle" class="text_lable"><?php osLable();?></td>
							<td valign="middle" class="text_value"><?php echo $reliabilityOs; ?></td>
						</tr>
					</table>
							
						<?php
					}
					$odData = '';
					$osData = '';
					
					// OD DATA
					if($Normal_OD_T) $odData = 'Normal&nbsp;<br>';
					if($Normal_OD_PoorStudy == 1) $odData .= 'Poor Study&nbsp;<br>';
					if($BorderLineDefect_OD_T == 1 || $BorderLineDefect_OD_1 == 1 || $BorderLineDefect_OD_2 == 1 || $BorderLineDefect_OD_3 == 1 || $BorderLineDefect_OD_4 == 1){
						$odData.= '<span style="width:110px;">Border Line Defect</span>';
						$odData.= '<span style="width:50px;">&nbsp;</span>';
						if($BorderLineDefect_OD_T == 1) $odData.= '&nbsp;T&nbsp;';
						if($BorderLineDefect_OD_1 == 1) $odData.= '&nbsp;+1&nbsp;';
						if($BorderLineDefect_OD_2 == 1) $odData.= '&nbsp;+2&nbsp;';
						if($BorderLineDefect_OD_3 == 1) $odData.= '&nbsp;+3&nbsp;';
						if($BorderLineDefect_OD_4 == 1) $odData.= '&nbsp;+4&nbsp;';
					}
					if($Abnorma_OD_T == 1 || $Abnorma_OD_1 == 1 || $Abnorma_OD_2 == 1 || $Abnorma_OD_3 == 1 || $Abnorma_OD_4 == 1){
						$odData.= '<br><span style="width:110px;">Abnormal</span>';
						$odData.= '<span style="width:50px;">&nbsp;</span>';
						if($Abnorma_OD_T == 1) $odData.= '&nbsp;T&nbsp;';
						if($Abnorma_OD_1 == 1) $odData.= '&nbsp;+1&nbsp;';
						if($Abnorma_OD_2 == 1) $odData.= '&nbsp;+2&nbsp;';
						if($Abnorma_OD_3 == 1) $odData.= '&nbsp;+3&nbsp;';
						if($Abnorma_OD_4 == 1) $odData.= '&nbsp;+4&nbsp;';
						if($NoSigChange_OD == 1) $odData.= 'No Sig. Change';
						if($Improved_OD == 1) $odData.= 'Improved';
						if($IncAbn_OD == 1) $odData.= 'Inc. Abn';
						
					}
	
					// OS DATA
					if($Normal_OS_T) $osData = 'Normal&nbsp;<br>';
					if($Normal_OS_PoorStudy == 1) $osData .= 'Poor Study&nbsp;<br>';

					if($BorderLineDefect_OS_T == 1 || $BorderLineDefect_OS_1 == 1 || $BorderLineDefect_OS_2 == 1 || $BorderLineDefect_OS_3 == 1 || $BorderLineDefect_OS_4 == 1 || $NoSigChange_OD == 1 || $Improved_OD == 1 || $IncAbn_OD == 1){
						$osData.= '<span style="width:110px;">Border Line Defect</span>';
						$osData.= '<span style="width:50px;">&nbsp;</span>';
						if($BorderLineDefect_OS_T == 1) $osData.= '&nbsp;T&nbsp;';
						if($BorderLineDefect_OS_1 == 1) $osData.= '&nbsp;+1&nbsp;';
						if($BorderLineDefect_OS_2 == 1) $osData.= '&nbsp;+2&nbsp;';
						if($BorderLineDefect_OS_3 == 1) $osData.= '&nbsp;+3&nbsp;';
						if($BorderLineDefect_OS_4 == 1) $osData.= '&nbsp;+4&nbsp;';
					}
					if($Abnorma_OS_T == 1 || $Abnorma_OS_1 == 1 || $Abnorma_OS_2 == 1 || $Abnorma_OS_3 == 1 || $Abnorma_OS_4 == 1 || $NoSigChange_OS == 1 || $Improved_OS == 1 || $IncAbn_OS == 1){
						$osData.= '<br><span style="width:110px;">Abnormal</span>';
						$osData.= '<span style="width:50px;">&nbsp;</span>';
						if($Abnorma_OS_T == 1) $osData.= '&nbsp;T&nbsp;';
						if($Abnorma_OS_1 == 1) $osData.= '&nbsp;+1&nbsp;';
						if($Abnorma_OS_2 == 1) $osData.= '&nbsp;+2&nbsp;';
						if($Abnorma_OS_3 == 1) $osData.= '&nbsp;+3&nbsp;';
						if($Abnorma_OS_4 == 1) $osData.= '&nbsp;+4&nbsp;';
						if($NoSigChange_OS == 1) $osData.= 'No Sig. Change';
						if($Improved_OS == 1) $osData.= 'Improved';
						if($IncAbn_OS == 1) $osData.= 'Inc. Abn';
					}



				if($osData!="" || $odData!="" || $Others_OD!="" || $Others_OS!=""){
						?>
					<table class="border" cellpadding="0" cellspacing="0" style="width:100%;" class="paddingTop">
						<tr>
							<td  valign="middle" class="tb_heading" style="width:100%" colspan="2">Test Results</td>
						</tr>
						<tr>
							<td class="text_value" style="width:50%" align="left"><?php odLable();?></td>
							<td class="text_value" style="width:50%" align="left"><?php osLable();?></td>
						</tr>
					<?php	
						if($odData || $osData){
						?>
						<tr>
							<td class="text_value" style="width:50%" align="left" valign="top"><?php echo  $odData; ?></td>
							<td class="text_value" style="width:50%" align="left" valign="top"><?php echo  $osData; ?></td>
						</tr>
					<?php
						}	
					if($Others_OD!="" || $Others_OS!=""){
						?>
							<tr>
								<td class="text_value" style="width:50%" align="left"><?php echo $Others_OD; ?></td>
								<td class="text_value" style="width:50%" align="left"><?php echo $Others_OS; ?></td>
							</tr>
						<?php				
					}
					?>
					</table>
						<?php
					}
				if($stable == 1) $treatment.= 'Stable&nbsp;&nbsp;';
					if($fuApa == 1) $treatment.= 'F/U APA&nbsp;&nbsp;';
					if($ptInformed == 1) $treatment.= 'Pt informed of results &nbsp;';
					if($monitorIOP == 1) $treatment.= 'monitor IOP &nbsp;';
					if($tech2InformPt == 1) $treatment.= 'Tech to Inform Pt.';
					
					if($stable == 1 || $fuApa == 1 || $ptInformed == 1 || $monitorIOP == 1 || $tech2InformPt == 1){
						?>
						<table cellspacing="0" cellpadding="0"  style="width:100%">
							<tr>
								<td  align="left" class="text_lable" style="width:100%;">Treatment/Prognosis:&nbsp;<span class="text_value"><?php echo $treatment; ?></span></td>
							</tr>
						</table>
						<?php
					}
					if($comments){
						?>
					<table cellspacing="0" cellpadding="0"  style="width:100%">
						<tr>
							<td  align="left" class="text_lable">Comments:&nbsp;<span class="text_value"><?php echo $comments; ?></span></td>
						</tr>
					</table>
						<?php
					}///Add OCT Images//
					$imagesHtml=getTestImages($nfa_id,$sectionImageFrom="NFA",$patient_id);
					if($imagesHtml!=""){
						echo('<table cellspacing="0" cellpadding="0"  style="width:100%">
							 	'.$imagesHtml.' 
							</table>');
					} 
					$imagesHtml="";
					//End NFA Images
					if($phyName){
						$getNameQry = imw_query("SELECT CONCAT_WS(', ', lname, fname) as phyName FROM users WHERE id = '$phyName'");	
						$getNameRow = imw_fetch_assoc($getNameQry);
						$phyName = str_replace(", ,"," ",$getNameRow['phyName']);
						?>
						<table cellspacing="0" cellpadding="0"  style="width:100%">
							<tr>
								<td align="left" class="text_value"><span class="text_lable">Performed By:&nbsp;</span> <?php echo $phyName; ?></td>
							</tr>
						</table>
						<?php
					}
					?>
				
		<?php
	}
	}
}
?>
<!-- NFA -->
<!-- OCT -->

<?php
if($oct==true){
if(is_array($_REQUEST["printTestRadioOCT"]) && count($_REQUEST["printTestRadioOCT"])>0){
		$sql = "SELECT * FROM oct WHERE patient_id = '".$patient_id."' AND oct_id in(".implode(",",$_REQUEST["printTestRadioOCT"]).") ";	
	}else if($form_id!=""){
		$sql = "SELECT * FROM oct WHERE patient_id = '".$patient_id."' AND form_id = '$form_id' AND purged='0' ";	
	}
	$rowOct = imw_query($sql);
	if($rowOct){
while($row=imw_fetch_array($rowOct)){
		extract($row);
		
					if($scanLaserEye){
						if($scanLaserEye == 'OU') $scanLaserEye = '<span style="color:purple;">'.$scanLaserEye.'</span>';
						if($scanLaserEye == 'OD') $scanLaserEye = '<span style="color:blue;">'.$scanLaserEye.'</span>';
						if($scanLaserEye == 'OS') $scanLaserEye = '<span style="color:green;">'.$scanLaserEye.'</span>';
						?>
					<table cellspacing="0" cellpadding="0"  class="paddingTop border" style="width:100%">
						<tr>
							<td  valign="middle" class="tb_heading" style="width:100%">OCT:&nbsp; <?php echo $scanLaserEye; ?> (<span class="text_value">Exam Date:&nbsp;<?php print FormatDate_show($examDate);?></span>)</td>
						</tr>
					</table>	
						<?php
					}
					if($performBy || $ptUndersatnding){
						$performedByQry = imw_query("SELECT CONCAT_WS(', ', lname, fname) as performedBy FROM users WHERE id = '$performBy'");	
						$performedByRow = imw_fetch_assoc($performedByQry);
						$performedBy = str_replace(", ,"," ",$performedByRow['performedBy']);
						?>
					<table cellspacing="0" cellpadding="0" class="border"  style="width:100%">
						<?php if($techComments!=""){
						?>
						<tr>
							<td colspan="2" valign="middle" class="text_lable bdrbtm" style="width:100%;">Technician Comments:&nbsp;<span class="text_value bdrbtm"><?php echo($techComments); ?></span></td>
						</tr>
						<?php }?>
						<tr>
						
							<td align="left" class="text_value bdrbtm"><span class="text_lable">Performed By:&nbsp;</span> <?php echo $performedBy; ?></td>
						<td align="left" class="text_value bdrbtm"><span class="text_lable">Patient Understanding & Cooperation</span>:&nbsp; <?php echo $ptUndersatnding; ?></td>
						</tr>
					</table>
						<?php
					}
					if($diagnosis && $diagnosis!="--Select--"){
						?>
					<table cellspacing="0" cellpadding="0"  style="width:100%">
						<tr>
							<td  align="left" class="text_value bdrbtm"><span class="text_lable">Diagnosis:&nbsp;</span> <?php echo ($diagnosis!="--Select--") ? $diagnosis : ""; ?></td>
						</tr>
					</table>
						<?php
					}
					if($reliabilityOd || $reliabilityOs){
						?>
						
			
					<table class="border" cellpadding="0" cellspacing="0" style="width:100%">
						<tr>	
							<td  valign="middle" class="text_lable">Physician Interpretation:&nbsp;</td>						
							<td valign="middle" class="text_lable">Reliability</td>
							<td valign="middle" class="text_lable"><?php odLable();?></td>
							<td valign="middle" class="text_value"><?php echo $reliabilityOd; ?></td>
							<td style="width:20px;"></td>
							<td valign="middle" class="text_lable"><?php osLable();?></td>
							<td valign="middle" class="text_value"><?php echo $reliabilityOs; ?></td>
						</tr>
					</table>
							
						<?php
					}
					$odData = '';
					$osData = '';
					
					// OD DATA
					if($Normal_OD_T) $odData = 'Normal&nbsp;<br>';
					if($Normal_OD_PoorStudy == 1) $odData .= 'Poor Study&nbsp;<br>';
					if($BorderLineDefect_OD_T == 1 || $BorderLineDefect_OD_1 == 1 || $BorderLineDefect_OD_2 == 1 || $BorderLineDefect_OD_3 == 1 || $BorderLineDefect_OD_4 == 1){
						$odData.= '<span style="width:110px;">Border Line Defect</span>';
						$odData.= '<span style="width:50px;">&nbsp;</span>';
						if($BorderLineDefect_OD_T == 1) $odData.= '&nbsp;T&nbsp;';
						if($BorderLineDefect_OD_1 == 1) $odData.= '&nbsp;+1&nbsp;';
						if($BorderLineDefect_OD_2 == 1) $odData.= '&nbsp;+2&nbsp;';
						if($BorderLineDefect_OD_3 == 1) $odData.= '&nbsp;+3&nbsp;';
						if($BorderLineDefect_OD_4 == 1) $odData.= '&nbsp;+4&nbsp;';
					}
					if($Abnorma_OD_T == 1 || $Abnorma_OD_1 == 1 || $Abnorma_OD_2 == 1 || $Abnorma_OD_3 == 1 || $Abnorma_OD_4 == 1){
						$odData.= '<br><span style="width:110px;">Abnormal</span>';
						$odData.= '<span style="width:50px;">&nbsp;</span>';
						if($Abnorma_OD_T == 1) $odData.= '&nbsp;T&nbsp;';
						if($Abnorma_OD_1 == 1) $odData.= '&nbsp;+1&nbsp;';
						if($Abnorma_OD_2 == 1) $odData.= '&nbsp;+2&nbsp;';
						if($Abnorma_OD_3 == 1) $odData.= '&nbsp;+3&nbsp;';
						if($Abnorma_OD_4 == 1) $odData.= '&nbsp;+4&nbsp;';
						if($NoSigChange_OD == 1) $odData.= 'No Sig. Change';
						if($Improved_OD == 1) $odData.= 'Improved';
						if($IncAbn_OD == 1) $odData.= 'Inc. Abn';
						
					}
	 
					// OS DATA
					if($Normal_OS_T) $osData = 'Normal&nbsp;<br>';
					if($Normal_OS_PoorStudy == 1) $osData .= 'Poor Study&nbsp;<br>';

					if($BorderLineDefect_OS_T == 1 || $BorderLineDefect_OS_1 == 1 || $BorderLineDefect_OS_2 == 1 || $BorderLineDefect_OS_3 == 1 || $BorderLineDefect_OS_4 == 1 || $NoSigChange_OD == 1 || $Improved_OD == 1 || $IncAbn_OD == 1){
						$osData.= '<span style="width:110px;">Border Line Defect</span>';
						$osData.= '<span style="width:50px;">&nbsp;</span>';
						if($BorderLineDefect_OS_T == 1) $osData.= '&nbsp;T&nbsp;';
						if($BorderLineDefect_OS_1 == 1) $osData.= '&nbsp;+1&nbsp;';
						if($BorderLineDefect_OS_2 == 1) $osData.= '&nbsp;+2&nbsp;';
						if($BorderLineDefect_OS_3 == 1) $osData.= '&nbsp;+3&nbsp;';
						if($BorderLineDefect_OS_4 == 1) $osData.= '&nbsp;+4&nbsp;';
					}
					if($Abnorma_OS_T == 1 || $Abnorma_OS_1 == 1 || $Abnorma_OS_2 == 1 || $Abnorma_OS_3 == 1 || $Abnorma_OS_4 == 1 || $NoSigChange_OS == 1 || $Improved_OS == 1 || $IncAbn_OS == 1){
						$osData.= '<br><span style="width:110px;">Abnormal</span>';
						$osData.= '<span style="width:50px;">&nbsp;</span>';
						if($Abnorma_OS_T == 1) $osData.= '&nbsp;T&nbsp;';
						if($Abnorma_OS_1 == 1) $osData.= '&nbsp;+1&nbsp;';
						if($Abnorma_OS_2 == 1) $osData.= '&nbsp;+2&nbsp;';
						if($Abnorma_OS_3 == 1) $osData.= '&nbsp;+3&nbsp;';
						if($Abnorma_OS_4 == 1) $osData.= '&nbsp;+4&nbsp;';
						if($NoSigChange_OS == 1) $osData.= 'No Sig. Change';
						if($Improved_OS == 1) $osData.= 'Improved';
						if($IncAbn_OS == 1) $osData.= 'Inc. Abn';
					}
				if($osData!="" || $odData!="" || $Others_OD!="" || $Others_OS!="" || $fovea_thick_OD!="" || $fovea_thick_OS!="" || $test_res_od!="" || $test_res_os!="" || $avg_nfl_Thick_OD!="" || $avg_nfl_Thick_OS!="") {
						?>
					<table cellpadding="0" cellspacing="0" style="width:100%;" class="paddingTop border">
						<tr>
							<td  valign="middle" class="tb_heading" style="width:100%" colspan="2">Test Results</td>
						</tr>
						<tr>
							<td class="text_value" style="width:50%" align="left"><?php odLable();?></td>
							<td class="text_value" style="width:50%" align="left"><?php osLable();?></td>
						</tr>
					<?php	
						if($odData || $osData){
						?>
						<tr>
							<td class="text_value" style="width:50%" align="left" valign="top"><?php echo  $odData; ?></td>
							<td class="text_value" style="width:50%" align="left" valign="top"><?php echo  $osData; ?></td>
						</tr>
					<?php
						}	
					if($Others_OD!="" || $Others_OS!=""){
						?>
							<tr>
								<td class="text_value" style="width:50%" align="left"><?php echo $Others_OD; ?></td>
								<td class="text_value" style="width:50%" align="left"><?php echo $Others_OS; ?></td>
							</tr>
						<?php				
					}
					if($test_res_od!="" || $test_res_os!="" ){
						?>
							<tr>
								<td class="text_value" style="width:50%" align="left"><?php echo $test_res_od; ?></td>
								<td class="text_value" style="width:50%" align="left"><?php echo $test_res_os; ?></td>
							</tr>
						
						<?php				
					}
					?>
					</table>
						<?php
					}
					
					if($stable == 1) $treatment.= 'Stable&nbsp;&nbsp;';
					if($contiMeds == 1) $treatment.= '&nbsp;Continue Meds &nbsp;';
					if($monitorIOP == 1) $treatment.= 'monitor IOP &nbsp;';
					if($fuApa == 1) $treatment.= 'F/U APA&nbsp;&nbsp;';
					if($tech2InformPt == 1) $treatment.= 'Tech to Inform Pt.';
					if($ptInformed == 1) $treatment.= 'Pt informed of results &nbsp;';
					if($ptInformedNv==1)$treatment.= '&nbsp;Informed Pt. result next visit.&nbsp;';
					
					if($stable == 1 || $fuApa == 1 || $ptInformed == 1 || $monitorIOP == 1 || $tech2InformPt == 1){
						?>
						<table cellspacing="0" cellpadding="0"  style="width:100%">
							<tr>
								<td  align="left" class="text_lable" style="width:100%;">Treatment/Prognosis:&nbsp;<span class="text_value"><?php echo $treatment; ?></span></td>
							</tr>
						</table>
						<?php
					}
					if($comments){
						?>
						<table cellspacing="0" cellpadding="0"  style="width:100%">
							<tr>
								<td  align="left" class="text_lable">Comments:&nbsp;<span class="text_value"><?php echo $comments; ?></span></td>
							</tr>
						</table>
						<?php
					}
					
					if($fovea_thick_OD!="" || $fovea_thick_OS!="" ){
						?><table cellspacing="0" cellpadding="0"  style="width:100%">
							<tr>
								<td  align="left" class="text_lable" colspan="2">Foveal Thickness</td>
							</tr>
							<tr>
								<td class="text_value" style="width:50%" align="left"><?php echo $fovea_thick_OD; ?></td>
								<td class="text_value" style="width:50%" align="left"><?php echo $fovea_thick_OS; ?></td>
							</tr>
						</table>
						<?php				
					}
					
					if($avg_nfl_Thick_OD!="" || $avg_nfl_Thick_OS!="" ){
						?><table cellspacing="0" cellpadding="0"  style="width:100%">
							<tr>
								<td  align="left" class="text_lable" colspan="2">AVG NFL Thickness</td>
							</tr>
							<tr>
								<td class="text_value" style="width:50%" align="left"><?php echo $avg_nfl_Thick_OD; ?></td>
								<td class="text_value" style="width:50%" align="left"><?php echo $avg_nfl_Thick_OS; ?></td>
							</tr>
						</table>
						<?php				
					}
					
						if($iopTrgtOd !="" || $iopTrgtOs !="" ){
						?><table cellspacing="0" cellpadding="0"  style="width:100%">
							<tr>
								<td  align="left" class="text_lable" colspan="2">IOP Comments</td>
							</tr>
							<tr>
								
								<td class="text_value" style="width:50%" align="left"><?php echo $iopTrgtOd; ?></td>
								<td class="text_value" style="width:50%" align="left"><?php echo $iopTrgtOs; ?></td>
							</tr>
						</table>
						<?php				
					}
					///Add OCT Images//
					$imagesHtml=getTestImages($oct_id,$sectionImageFrom="Pacchy",$patient_id);
					if($imagesHtml!=""){
						echo('<table cellspacing="0" cellpadding="0"  style="width:100%">
							 	'.$imagesHtml.' 
							</table>');
					} 
					$imagesHtml="";
					//End OCT Images
					
					if($phyName){
						$getNameQry = imw_query("SELECT CONCAT_WS(', ', lname, fname) as phyName FROM users WHERE id = '$phyName'");	
						$getNameRow = imw_fetch_assoc($getNameQry);
						$phyName = str_replace(", ,"," ",$getNameRow['phyName']);
						?>
						<table cellspacing="0" cellpadding="0"  style="width:100%">
							<tr>
								<td align="left" class="text_value"><span class="text_lable">Performed By:&nbsp;</span> <?php echo $phyName; ?></td>
							</tr>
						</table>
						<?php
					}
					?>
				
		<?php
	}//End oct While
 }
}
?>


<!-- OCT -->
<!-- PACHY -->
<?php
if($pachy==true){
	$stable = "";
	$fuApa= "";
	$ptInforme = "";
	$monitorIOP = "";
	$tech2InformPt = "";
	if(is_array($_REQUEST["printTestRadioPachy"]) && count($_REQUEST["printTestRadioPachy"])>0){
		$sqlPachy = "SELECT * FROM pachy WHERE patientId = '".$patient_id."' AND pachy_id in (".implode(",",$_REQUEST["printTestRadioPachy"]).")";		
	}else if($form_id!=""){
		$sqlPachy = "SELECT * FROM pachy WHERE patientId = '".$patient_id."' AND formId = '$form_id' AND purged='0' ";		
	}
					
	$resPachy = imw_query($sqlPachy);
	if(imw_num_rows($resPachy)>0){
	while($row=imw_fetch_array($resPachy)){
		extract($row);
		?>

				<table style="width:100%;" class="paddingTop" class="border" cellspacing="0" cellpadding="0" >
					<tr>
						<td  valign="middle" class="tb_heading" style="width:100%">Pachy (<span class="text_value">Exam Date:&nbsp;<?php print FormatDate_show($examDate);?></span>)</td>
					</tr>
				</table>
					<?php
					if($pachyMeterEye){
						if($pachyMeterEye == 'OU') $pachyMeterEye = '<span style="color:purple;">'.$pachyMeterEye.'</span>';
						if($pachyMeterEye == 'OD') $pachyMeterEye = '<span style="color:blue;">'.$pachyMeterEye.'</span>';
						if($pachyMeterEye == 'OS') $pachyMeterEye = '<span style="color:green;">'.$pachyMeterEye.'</span>';
						?>
<table cellspacing="0" cellpadding="0"  style="width:100%">
						<tr>
							<td  valign="middle" class="text_lable">Pachymeter:&nbsp; <?php echo $pachyMeterEye; ?></td>
						</tr>
</table>
						<?php
					}
					if($performedBy || $ptUnderstanding){
						$performedByQry = imw_query("SELECT CONCAT_WS(', ', lname, fname) as performedBy FROM users WHERE id = '$performedBy'");	
						$performedByRow = imw_fetch_assoc($performedByQry);
						$performedBy = str_replace(", ,"," ",$performedByRow['performedBy']);
						?>
					<?php
			if($techComments!=""){
			?>
			<table cellspacing="0" cellpadding="0"  style="width:100%">
						<tr>
							<td  align="left" class="text_value" style="width:100%">
								<span class="text_lable">Technician Comments:&nbsp;</span><?php echo $techComments; ?>						
							</td>
						</tr>
			</table>	
			<?php }?>
					<table cellspacing="0" cellpadding="0"  style="width:100%">
						<tr>
							<td align="left" class="text_value"><span class="text_lable">Performed By:&nbsp;</span> <?php echo $performedBy; ?></td>
							<td align="left" class="text_value"><span class="text_lable">Patient Understanding & Cooperation</span>:&nbsp;<?php echo $ptUnderstanding; ?></td>
						</tr>
					</table>	
						<?php
					}
					if($diagnosis){		
						?>
					<table cellspacing="0" cellpadding="0"  style="width:100%">
						<tr>
							<td  align="left" class="text_value"><span class="text_lable">Diagnosis:&nbsp;</span> <?php echo $diagnosis; ?></td>
						</tr>
					</table>
						<?php
					}
					if($reliabilityOd || $reliabilityOs){
						?>
					<table class="border" cellpadding="0" cellspacing="0" style="width:100%">
						<tr>	
							<td  valign="middle" class="text_lable">Physician Interpretation:&nbsp;</td>						
							<td valign="middle" class="text_lable">Reliability</td>
							<td valign="middle" class="text_lable"><?php odLable();?></td>
							<td valign="middle" class="text_value"><?php echo $reliabilityOd; ?></td>
							<td style="width:20px;"></td>
							<td valign="middle" class="text_lable"><?php osLable();?></td>
							<td valign="middle" class="text_value"><?php echo $reliabilityOs; ?></td>
						</tr>
					</table>
						<?php
					}
					$odData = '';
					$osData = '';
									
					// OD DATA
					if($Central_OD || $Nasal_OD || $pachy_od_readings || $pachy_od_average || $pachy_od_correction_value){
						$odData.= '<span>Pachy</span>';
						if($Central_OD == 1) $odData.= '&nbsp;Central&nbsp;';
						if($Nasal_OD == 1) $odData.= '&nbsp;Nasal&nbsp;';
						if($pachy_od_readings) $odData.= '&nbsp;'.$pachy_od_readings.'&nbsp;';
						if($pachy_od_average) $odData.= '&nbsp;'.$pachy_od_average.'&nbsp;';
						if($pachy_od_correction_value) $odData.= '&nbsp;'.$pachy_od_correction_value.'&nbsp;';
						if($Central_OD == 1 || $Nasal_OD == 1 || $pachy_od_readings || $pachy_od_average || $pachy_od_correction_value)
							$odData.= '<br>';
						if($Inferior_OD) $odData.= '&nbsp;Inferior&nbsp;';
						if($Temporal_OD) $odData.= '&nbsp;Temporal&nbsp;';
						if($Superior_OD) $odData.= '&nbsp;Superior&nbsp;';
					}
	
					// OS DATA
					if($Central_OS || $Nasal_OS || $pachy_os_readings || $pachy_os_average || $pachy_os_correction_value){
						$osData.= '<span>Pachy</span>';
						if($Central_OS == 1) $osData.= '&nbsp;Central&nbsp;';
						if($Nasal_OS == 1) $osData.= '&nbsp;Nasal&nbsp;';
						if($pachy_os_readings) $osData.= '&nbsp;'.$pachy_os_readings.'&nbsp;';
						if($pachy_os_average) $osData.= '&nbsp;'.$pachy_os_average.'&nbsp;';
						if($pachy_os_correction_value) $osData.= '&nbsp;'.$pachy_os_correction_value.'&nbsp;';
						if($Central_OS == 1 || $Nasal_OS == 1 || $pachy_os_readings || $pachy_os_average || $pachy_os_correction_value)
							$osData.= '<br>';
						if($Inferior_OS) $osData.= '&nbsp;Inferior&nbsp;';
						if($Temporal_OS) $osData.= '&nbsp;Temporal&nbsp;';
						if($Superior_OS) $osData.= '&nbsp;Superior&nbsp;';
					}

				if($osData!="" || $odData!="" || $descOd!="" || $descOs!=""){
						?>
					<table class="border" cellpadding="0" cellspacing="0" style="width:700px;" class="paddingTop">
						<tr>
							<td  valign="middle" class="tb_heading" style="width:100%" colspan="2">Test Results</td>
						</tr>
						<tr>
							<td class="text_value" style="width:50%" align="left"><?php odLable();?></td>
							<td class="text_value" style="width:50%" align="left"><?php osLable();?></td>
						</tr>
					<?php	
						if($odData || $osData){
						?>
						<tr>
							<td class="text_value" style="width:50%" align="left" valign="top"><?php echo  $odData; ?></td>
							<td class="text_value" style="width:50%" align="left" valign="top"><?php echo  $osData; ?></td>
						</tr>
					<?php
						}	
					if($descOd!="" || $descOs!=""){
						?>
							<tr>
								<td class="text_value" style="width:50%" align="left"><span class="text_lable">Description:&nbsp;</span><?php echo $descOd; ?></td>
								<td class="text_value" style="width:50%" align="left"><span class="text_lable">Description:&nbsp;</span><?php echo $descOs; ?></td>
							</tr>
						<?php				
					}
					?>
					</table>
						<?php
					}				
					
					$treatment = "";
					if($stable == 1) $treatment= 'Stable&nbsp;&nbsp;';
					if($fuApa == 1) $treatment.= 'F/U APA&nbsp;&nbsp;';
					if($tech2InformPt == 1) $treatment.= 'Tech to Inform Pt. &nbsp;';
					if($ptInformed == 1) $treatment.= 'Pt informed of results';			
					
					
					if($stable == 1 || $fuApa == 1 || $ptInformed == 1){
						?>
						<table cellspacing="0" cellpadding="0"  style="width:100%">
							<tr>
								<td  align="left" class="text_lable" style="width:700px;">Treatment/Prognosis:&nbsp;<span class="text_value"><?php echo $treatment; ?></span></td>
							</tr>
						</table>
						<?php
					}
					if($comments){
						?>
					<table cellspacing="0" cellpadding="0"  style="width:100%">
						<tr>
							<td  align="left" class="text_lable">Comments:&nbsp;<span class="text_value"><?php echo $comments; ?></span></td>
						</tr>
					</table>
						<?php
					}
					///Add IVFA Images//
					$imagesHtml=getTestImages($pachy_id,$sectionImageFrom="Pacchy",$patient_id);
					if($imagesHtml!=""){
						echo('<table cellspacing="0" cellpadding="0"  style="width:100%">
							 	'.$imagesHtml.' 
							</table>');
					} 
					$imagesHtml="";
					//End Pacchy Images
					if($phyName){
						$getNameQry = imw_query("SELECT CONCAT_WS(', ', lname, fname) as phyName FROM users WHERE id = '$phyName'");	
						$getNameRow = imw_fetch_assoc($getNameQry);
						$phyName = str_replace(", ,"," ",$getNameRow['phyName']);
						?>
			<table class="border" cellpadding="0" cellspacing="0" style="width:100%">
						<tr>
							<td  align="left" class="text_value"><span class="text_lable">Performed By:&nbsp;</span> <?php echo $phyName; ?></td>
						</tr>
			</table>
						<?php
					}
					?>
				
		<?php
	}//End of Pachy While
 }
}
?>
<!-- PACHY -->


<!-- IVFA -->
<?php
if( $ivfa == true){
	$stable = "";
	$fuApa= "";
	$ptInforme = "";
	$monitorIOP = "";
	$tech2InformPt = "";
if(is_array($_REQUEST["printTestRadioIVFA"]) && count($_REQUEST["printTestRadioIVFA"])>0){
	$sqlIVFA = "SELECT * FROM ivfa WHERE patient_id = '".$patient_id."' AND vf_id in(".implode(",",$_REQUEST["printTestRadioIVFA"]).") ";
	}else if($form_id){
		$sqlIVFA = "SELECT * FROM ivfa WHERE patient_id = '".$patient_id."' AND form_id = '$form_id' AND purged='0' ";
	}
	$rowIVFA = imw_query($sqlIVFA);
	if(imw_num_rows($rowIVFA)>0){
	while($row=imw_fetch_array($rowIVFA)){
		extract($row);
		$osData = '';
		$odData = '';
		?>

			<table style="width:700px;" class="paddingTop" class="border" cellspacing="0" cellpadding="0">
				<tr>
					<td valign="middle" class="tb_heading" style="width:100%">IVFA (<span class="text_value">Exam Date:&nbsp;<?php print FormatDate_show($exam_date);?></span>)</td>
				</tr>
			</table>
				<?php
				
				
				if($ivfa_od == "1") $ivfa_od = '<span style="color:purple;">OU</span>';
				if($ivfa_od == "2" || $ivfa_od == "3") $ivfa_od = '<span style="color:blue;">OD</span> > <span style="color:green;">OS</span>';
				if($ivfa_early == 1) $ivfa_early = 'Early and late shots&nbsp;&nbsp;';
				if($ivfa_extra == 1) $ivfa_extra = 'Extra Copy';
				if($ivfa_od || $ivfa_early == 1 || $ivfa_extra == 1){
					?>
				<table class="border" cellpadding="0" cellspacing="0" style="width:100%">
					<tr>
						<td valign="middle" class="text_lable" style="width:100%"><?php if($ivfa_od) echo '<span class="text_lable">IVFA:&nbsp;</span>'.$ivfa_od.'&nbsp;'; if($ivfa_early) echo $ivfa_early; if($ivfa_extra) echo $ivfa_extra; ?></td>
					</tr>
				</table>
					<?php
				}
				if($comments_ivfa){
					?>
					<table class="border" cellpadding="0" cellspacing="0" style="width:100%">
						<tr>
							<td align="left" class="text_value"><span class="text_lable">Description:&nbsp;</span> <?php echo $comments_ivfa; ?></td>
						</tr>
					</table>
						<?php
					}
					if($performed_by || $pa_under){
						$getNameQry = imw_query("SELECT CONCAT_WS(', ', lname, fname) as performed_by FROM users WHERE id = '$performed_by'");						
						if(imw_num_rows($getNameQry)>0){
							$getNameRow = imw_fetch_assoc($getNameQry);
							$performed_by = $getNameRow['performed_by'];
						}
						?>
						<table class="border" cellpadding="0" cellspacing="0" style="width:100%">
						<?php if($techComments!=""){?>
							<tr>
								<td  valign="middle" class="text_lable">Technician Comments:&nbsp;<span class="text_value"><?php echo($techComments);?></span> </td>
							</tr>
						<?php }?>
							<tr>
								
								<td align="left" class="text_value" style="width:100%">
									<?php 
										if($performed_by) echo '<span class="text_lable">Performed By:&nbsp;</span> '.$performed_by.'&nbsp;&nbsp;';
										if($pa_under) echo '<span class="text_lable">Patient Understanding & Cooperation</span>:&nbsp;'.$pa_under; 
									?>
								</td>					
							</tr>
						</table>
						<?php
					}
					if($pa_inter || $pa_inter1){
						?>
					
					<table class="border" cellpadding="0" cellspacing="0" style="width:100%">
						<tr>	
							<td  valign="middle" class="text_lable">Physician Interpretation:&nbsp;</td>						
							<td valign="middle" class="text_lable">Reliability</td>
							<td valign="middle" class="text_lable"><?php odLable();?></td>
							<td valign="middle" class="text_value"><?php echo $pa_inter; ?></td>
							<td style="width:20px;"></td>
							<td valign="middle" class="text_lable"><?php osLable();?></td>
							<td valign="middle" class="text_value"><?php echo $pa_inter1; ?></td>
						</tr>
					</table>
					
							
						<?php
					}
					// OD DATA
				// $Retina_Ischemia_OD ||$Retina_BRVO_OD || $Retina_CRVO_OD||$SR_Heme_OD ||$Classic_CNV_OD||$Occult_CNV_OD
				// $Retina_Ischemia_OS ||$Retina_BRVO_OS || $Retina_CRVO_OS||$SR_Heme_OS ||$Classic_CNV_OS||$Occult_CNV_OS 
				//$ivfaComments 
					
					
					if($Sharp_Pink_OD || $Pale_OD || $Large_Cap_OD || $Sloping_OD || $Notch_OD || $NVD_OD || $Leakage_OD){
						$odData.= 'Disc';
						if($Sharp_Pink_OD == 1){ $odData.= '&nbsp;Sharp Pink&nbsp;' ; ++$c;}
						if($Pale_OD == 1){ $odData.= '&nbsp;Pale&nbsp;'; ++$c;}
						if($Large_Cap_OD == 1){ $odData.= '&nbsp;Large Cup&nbsp;'; ++$c;}
							if($c>=3){ $c = 0; $odData.= '<br>'; $c = 0; }
						if($Sloping_OD == 1){ $odData.= '&nbsp;Sloping&nbsp;'; ++$c;}
							if($c>=3){ $odData.= '<br>'; $c = 0; }
						if($Notch_OD == 1){ $odData.= '&nbsp;Notch&nbsp;'; ++$c;}
							if($c>=3){ $odData.= '<br>'; $c = 0; }
						if($NVD_OD == 1){ $odData.= '&nbsp;NVD&nbsp;'; ++$c;}
							if($c>=3){ $odData.= '<br>'; $c = 0; }
						if($Leakage_OD == 1) $odData.= '&nbsp;Leakage&nbsp;';
					}
	
					if($Retina_Hemorrhage_OD || $Retina_Microaneurysms_OD || $Retina_Exudates_OD || $Retina_Laser_Scars_OD
						|| $Retina_NEVI_OD || $Retina_SRVNM_OD || $Retina_Edema_OD
						|| $Retina_BDR_OD_T || $Retina_BDR_OD_1 || $Retina_BDR_OD_2 || $Retina_BDR_OD_3 || $Retina_BDR_OD_4
						|| $Retina_Druse_OD_T || $Retina_Druse_OD_1 || $Retina_Druse_OD_2 || $Retina_Druse_OD_3 || $Retina_Druse_OD_4
						|| $Retina_RPE_Change_OD_T || $Retina_RPE_Change_OD_1 || $Retina_RPE_Change_OD_2 || $Retina_RPE_Change_OD_3 || $Retina_RPE_Change_OD_4
						|| $Retina_Ischemia_OD || 	$Retina_BRVO_OD || $Retina_CRVO_OD || $SR_Heme_OD || $Classic_CNV_OD ||	$Occult_CNV_OD){
						$odData.= '<br><span>Retina</span>';
					}
	
					if($Retina_Hemorrhage_OD || $Retina_Microaneurysms_OD || $Retina_Exudates_OD || $Retina_Laser_Scars_OD || $Retina_NEVI_OD || $Retina_SRVNM_OD || $Retina_Edema_OD || $Retina_Ischemia_OD || 	$Retina_BRVO_OD || $Retina_CRVO_OD || $SR_Heme_OD || $Classic_CNV_OD ||	$Occult_CNV_OD ){
						$c = 0;
						if($Retina_Hemorrhage_OD == 1){ $odData.= '&nbsp;Hemorrhage&nbsp;' ; ++$c; }
						if($Retina_Microaneurysms_OD == 1){ $odData.= '&nbsp;Microaneurysms&nbsp;' ; ++$c; }
						if($Retina_Exudates_OD == 1){ $odData.= '&nbsp;Exudates&nbsp;' ; ++$c; }
							if($c>=3){ $odData.= '<br>'; $c = 0; }
						if($Retina_Laser_Scars_OD == 1){ $odData.= '&nbsp;Laser Scars&nbsp;' ; ++$c; }
							if($c>=3){ $odData.= '<br>'; $c = 0; }
						if($Retina_NEVI_OD == 1){ $odData.= '&nbsp;NEVI&nbsp;' ; ++$c; }
							if($c>=3){ $odData.= '<br>'; $c = 0; }
						if($Retina_SRVNM_OD == 1){ $odData.= '&nbsp;SRVNM&nbsp;' ; ++$c; }
							if($c>=3){ $odData.= '<br>'; $c = 0; }
						if($Retina_Edema_OD == 1){ $odData.= '&nbsp;Edema&nbsp;' ; ++$c; }
						
						if($Retina_Ischemia_OD==1){$odData.= '&nbsp;Ischemia&nbsp;' ;	++$c; }
							if($c>=3){ $odData.= '<br>'; $c = 0; }
							
						if($Retina_BRVO_OD==1){$odData.= '&nbsp;BRVO&nbsp;' ;	++$c; }
							if($c>=3){ $odData.= '<br>'; $c = 0; }
							
						if($Retina_CRVO_OD==1){$odData.= '&nbsp;CRVO&nbsp;' ;++$c; }
							if($c>=3){ $odData.= '<br>'; $c = 0; } 
							
						if($SR_Heme_OD==1){	$odData.= '&nbsp;SR Heme&nbsp;' ;++$c; }
							if($c>=3){ $odData.= '<br>'; $c = 0; }
							
						if($Classic_CNV_OD==1){	$odData.= '&nbsp;Classic CNV&nbsp;' ;++$c; }
							if($c>=3){ $odData.= '<br>'; $c = 0; }
							
						if($Occult_CNV_OD==1){$odData.= '&nbsp;Occult CNV&nbsp;' ; ++$c; }
							if($c>=3){ $odData.= '<br>'; $c = 0; }
											
					}
					if($Retina_BDR_OD_T || $Retina_BDR_OD_1 || $Retina_BDR_OD_2 || $Retina_BDR_OD_3 || $Retina_BDR_OD_4){
						$odData.= '<br>';
						$odData.= '&nbsp;<span style="width:75px;">BDR</span>';
						if($Retina_BDR_OD_T == 1) $odData.= '&nbsp;T&nbsp;';
						if($Retina_BDR_OD_1 == 1) $odData.= '&nbsp;+1&nbsp;';
						if($Retina_BDR_OD_2 == 1) $odData.= '&nbsp;+2&nbsp;';
						if($Retina_BDR_OD_3 == 1) $odData.= '&nbsp;+3&nbsp;';
						if($Retina_BDR_OD_4 == 1) $odData.= '&nbsp;+4&nbsp;';
					}		
					if($Retina_Druse_OD_T || $Retina_Druse_OD_1 || $Retina_Druse_OD_2 || $Retina_Druse_OD_3 || $Retina_Druse_OD_4){
						$odData.= '<br>';
						$odData.= '&nbsp;<span style="width:75px;">Druse</span>';
						if($Retina_Druse_OD_T == 1) $odData.= '&nbsp;T&nbsp;';
						if($Retina_Druse_OD_1 == 1) $odData.= '&nbsp;+1&nbsp;';
						if($Retina_Druse_OD_2 == 1) $odData.= '&nbsp;+2&nbsp;';
						if($Retina_Druse_OD_3 == 1) $odData.= '&nbsp;+3&nbsp;';
						if($Retina_Druse_OD_4 == 1) $odData.= '&nbsp;+4&nbsp;';
					}		
					if($Retina_RPE_Change_OD_T || $Retina_RPE_Change_OD_1 || $Retina_RPE_Change_OD_2 || $Retina_RPE_Change_OD_3 || $Retina_RPE_Change_OD_4){
						$odData.= '<br>';
						$odData.= '&nbsp;<span style="width:75px;">RPE&nbsp;Change</span>';
						if($Retina_RPE_Change_OD_T == 1) $odData.= '&nbsp;T&nbsp;';
						if($Retina_RPE_Change_OD_1 == 1) $odData.= '&nbsp;+1&nbsp;';
						if($Retina_RPE_Change_OD_2 == 1) $odData.= '&nbsp;+2&nbsp;';
						if($Retina_RPE_Change_OD_3 == 1) $odData.= '&nbsp;+3&nbsp;';
						if($Retina_RPE_Change_OD_4 == 1) $odData.= '&nbsp;+4&nbsp;';
					}
					if($Druse_OD || $RPE_Changes_OD || $SRNVM_OD || $Edema_OD || $Scars_OD || $Hemorrhage_OD || $Microaneurysms_OD || $Exudates_OD
						|| $Macula_BDR_OD_T || $Macula_BDR_OD_1 || $Macula_BDR_OD_2 || $Macula_BDR_OD_3 || $Macula_BDR_OD_4
						|| $Macula_SMD_OD_T || $Macula_SMD_OD_1 || $Macula_SMD_OD_2 || $Macula_SMD_OD_3 || $Macula_SMD_OD_4){
							$odData.= '<br><span>Macula</span>';
					}
					if($Druse_OD || $RPE_Changes_OD || $SRNVM_OD || $Edema_OD || $Scars_OD || $Hemorrhage_OD || $Microaneurysms_OD || $Exudates_OD){
						$c = 0;					
						if($Druse_OD == 1){ $odData.= '&nbsp;Druse&nbsp;' ; ++$c; }
						if($RPE_Changes_OD == 1){ $odData.= '&nbsp;RPE Changes&nbsp;' ; ++$c; }
						if($SRNVM_OD == 1){ $odData.= '&nbsp;SRNVM&nbsp;' ; ++$c; }
							if($c>=3){ $odData.= '<br>'; $c = 0; }
						if($Edema_OD == 1){ $odData.= '&nbsp;Edema&nbsp;' ; ++$c; }
							if($c>=3){ $odData.= '<br>'; $c = 0; }
						if($Retina_Nevus_OD == 1){ $odData.= '&nbsp;Nevus&nbsp;' ; ++$c; }
							if($c>=3){ $odData.= '<br>'; $c = 0; }
						if($Scars_OD == 1){ $odData.= '&nbsp;Scars&nbsp;' ; ++$c; }
							if($c>=3){ $odData.= '<br>'; $c = 0; }
						if($Hemorrhage_OD == 1){ $odData.= '&nbsp;Hemorrhage&nbsp;' ; ++$c; }
							if($c>=3){ $odData.= '<br>'; $c = 0; }
						if($Microaneurysms_OD == 1){ $odData.= '&nbsp;Microaneurysms&nbsp;' ; ++$c; }
							if($c>=3){ $odData.= '<br>'; $c = 0; }
						if($Exudates_OD == 1){ $odData.= '&nbsp;Exudates&nbsp;' ; ++$c; }
					}
					if($Macula_BDR_OD_T || $Macula_BDR_OD_1 || $Macula_BDR_OD_2 || $Macula_BDR_OD_3 || $Macula_BDR_OD_4){
						$odData.= '<br>';
						$odData.= '&nbsp;<span style="width:75px;">BDR</span>';
						if($Macula_BDR_OD_T == 1) $odData.= '&nbsp;T&nbsp;';
						if($Macula_BDR_OD_1 == 1) $odData.= '&nbsp;+1&nbsp;';
						if($Macula_BDR_OD_2 == 1) $odData.= '&nbsp;+2&nbsp;';
						if($Macula_BDR_OD_3 == 1) $odData.= '&nbsp;+3&nbsp;';
						if($Macula_BDR_OD_4 == 1) $odData.= '&nbsp;+4&nbsp;';
					}		
					if($Macula_SMD_OD_T || $Macula_SMD_OD_1 || $Macula_SMD_OD_2 || $Macula_SMD_OD_3 || $Macula_SMD_OD_4){
						$odData.= '<br>';
						$odData.= '&nbsp;<span style="width:75px;">SMD</span>';
						if($Macula_SMD_OD_T == 1) $odData.= '&nbsp;T&nbsp;';
						if($Macula_SMD_OD_1 == 1) $odData.= '&nbsp;+1&nbsp;';
						if($Macula_SMD_OD_2 == 1) $odData.= '&nbsp;+2&nbsp;';
						if($Macula_SMD_OD_3 == 1) $odData.= '&nbsp;+3&nbsp;';
						if($Macula_SMD_OD_4 == 1) $odData.= '&nbsp;+4&nbsp;';
					}
					
					
					// OS DATA
					if($Sharp_Pink_OS || $Pale_OS || $Large_Cap_OS || $Sloping_OS || $Notch_OS || $NVD_OS || $Leakage_OS){
						$osData.= '<span>Disc</span>';
						if($Sharp_Pink_OS == 1){ $osData.= '&nbsp;Sharp Pink&nbsp;' ; ++$c;}
						if($Pale_OS == 1){ $osData.= '&nbsp;Pale&nbsp;'; ++$c;}
						if($Large_Cap_OS == 1){ $osData.= '&nbsp;Large Cup&nbsp;'; ++$c;}
							if($c>=3){ $c = 0; $osData.= '<br>'; $c = 0; }
						if($Sloping_OS == 1){ $osData.= '&nbsp;Sloping&nbsp;'; ++$c;}
							if($c>=3){ $osData.= '<br>'; $c = 0; }
						if($Notch_OS == 1){ $osData.= '&nbsp;Notch&nbsp;'; ++$c;}
							if($c>=3){ $osData.= '<br>'; $c = 0; }
						if($NVD_OS == 1){ $osData.= '&nbsp;NVD&nbsp;'; ++$c;}
							if($c>=3){ $osData.= '<br>'; $c = 0; }
						if($Leakage_OS == 1) $osData.= '&nbsp;Leakage&nbsp;';
					}
					if($Retina_Hemorrhage_OS || $Retina_Microaneurysms_OS || $Retina_Exudates_OS || $Retina_Laser_Scars_OS
						|| $Retina_NEVI_OS || $Retina_SRVNM_OS || $Retina_Edema_OS
						|| $Retina_BDR_OS_T || $Retina_BDR_OS_1 || $Retina_BDR_OS_2 || $Retina_BDR_OS_3 || $Retina_BDR_OS_4
						|| $Retina_Druse_OS_T || $Retina_Druse_OS_1 || $Retina_Druse_OS_2 || $Retina_Druse_OS_3 || $Retina_Druse_OS_4
						|| $Retina_RPE_Change_OS_T || $Retina_RPE_Change_OS_1 || $Retina_RPE_Change_OS_2 || $Retina_RPE_Change_OS_3 || $Retina_RPE_Change_OS_4
						|| $Retina_Ischemia_OS ||$Retina_BRVO_OS || $Retina_CRVO_OS||$SR_Heme_OS ||$Classic_CNV_OS||$Occult_CNV_OS ){
						$osData.= '<br><span>Retina</span>';
					}
					if($Retina_Hemorrhage_OS || $Retina_Microaneurysms_OS || $Retina_Exudates_OS || $Retina_Laser_Scars_OS || $Retina_NEVI_OS || $Retina_SRVNM_OS || $Retina_Edema_OS || $Retina_Ischemia_OS ||$Retina_BRVO_OS || $Retina_CRVO_OS||$SR_Heme_OS ||$Classic_CNV_OS||$Occult_CNV_OS ){
						$c = 0;					
						if($Retina_Hemorrhage_OS == 1){ $osData.= '&nbsp;Hemorrhage&nbsp;' ; ++$c; }
						if($Retina_Microaneurysms_OS == 1){ $osData.= '&nbsp;Microaneurysms&nbsp;' ; ++$c; }
						if($Retina_Exudates_OS == 1){ $osData.= '&nbsp;Exudates&nbsp;' ; ++$c; }
							if($c>=3){ $osData.= '<br>'; $c = 0; }
						if($Retina_Laser_Scars_OS == 1){ $osData.= '&nbsp;Laser Scars&nbsp;' ; ++$c; }
							if($c>=3){ $osData.= '<br>'; $c = 0; }
						if($Retina_NEVI_OS == 1){ $osData.= '&nbsp;NEVI&nbsp;' ; ++$c; }
							if($c>=3){ $osData.= '<br>'; $c = 0; }
						if($Retina_SRVNM_OS == 1){ $osData.= '&nbsp;SRVNM&nbsp;' ; ++$c; }
							if($c>=3){ $osData.= '<br>'; $c = 0; }
						if($Retina_Edema_OS == 1){ $osData.= '&nbsp;Edema&nbsp;' ; ++$c; }	
						
						if($Retina_Ischemia_OS== 1){$osData.= '&nbsp;Ischemia&nbsp;' ;	++$c; }
							if($c>=3){ $osData.= '<br>'; $c = 0; }
						if($Retina_BRVO_OS==1){		$osData.= '&nbsp;BRVO&nbsp;' ;	++$c; }
							if($c>=3){ $osData.= '<br>'; $c = 0; }
						if($Retina_CRVO_OS==1){		$osData.= '&nbsp;CRVO&nbsp;' ;++$c; }
							if($c>=3){ $osData.= '<br>'; $c = 0; } 
						if($SR_Heme_OS==1){		$osData.= '&nbsp;SR Heme&nbsp;' ;++$c; }
							if($c>=3){ $osData.= '<br>'; $c = 0; }
						if($Classic_CNV_OS==1){		$osData.= '&nbsp;Classic CNV&nbsp;' ;++$c; }
							if($c>=3){ $osData.= '<br>'; $c = 0; }
						if($Occult_CNV_OS==1){		$osData.= '&nbsp;Occult CNV&nbsp;' ; ++$c; }
							if($c>=3){ $osData.= '<br>'; $c = 0; }		
					}
					if($Retina_BDR_OS_T || $Retina_BDR_OS_1 || $Retina_BDR_OS_2 || $Retina_BDR_OS_3 || $Retina_BDR_OS_4){
						$osData.= '<br>';
						$osData.= '&nbsp;<span style="width:75px;">BDR</span>';
						if($Retina_BDR_OS_T == 1) $osData.= '&nbsp;T&nbsp;';
						if($Retina_BDR_OS_1 == 1) $osData.= '&nbsp;+1&nbsp;';
						if($Retina_BDR_OS_2 == 1) $osData.= '&nbsp;+2&nbsp;';
						if($Retina_BDR_OS_3 == 1) $osData.= '&nbsp;+3&nbsp;';
						if($Retina_BDR_OS_4 == 1) $osData.= '&nbsp;+4&nbsp;';
					}		
					if($Retina_Druse_OS_T || $Retina_Druse_OS_1 || $Retina_Druse_OS_2 || $Retina_Druse_OS_3 || $Retina_Druse_OS_4){
						$osData.= '<br>';
						$osData.= '&nbsp;<span style="width:75px;">Druse</span>';
						if($Retina_Druse_OS_T == 1) $osData.= '&nbsp;T&nbsp;';
						if($Retina_Druse_OS_1 == 1) $osData.= '&nbsp;+1&nbsp;';
						if($Retina_Druse_OS_2 == 1) $osData.= '&nbsp;+2&nbsp;';
						if($Retina_Druse_OS_3 == 1) $osData.= '&nbsp;+3&nbsp;';
						if($Retina_Druse_OS_4 == 1) $osData.= '&nbsp;+4&nbsp;';
					}		
					if($Retina_RPE_Change_OS_T || $Retina_RPE_Change_OS_1 || $Retina_RPE_Change_OS_2 || $Retina_RPE_Change_OS_3 || $Retina_RPE_Change_OS_4){
						$osData.= '<br>';
						$osData.= '&nbsp;<span style="width:75px;">RPE&nbsp;Change</span>';
						if($Retina_RPE_Change_OS_T == 1) $osData.= '&nbsp;T&nbsp;';
						if($Retina_RPE_Change_OS_1 == 1) $osData.= '&nbsp;+1&nbsp;';
						if($Retina_RPE_Change_OS_2 == 1) $osData.= '&nbsp;+2&nbsp;';
						if($Retina_RPE_Change_OS_3 == 1) $osData.= '&nbsp;+3&nbsp;';
						if($Retina_RPE_Change_OS_4 == 1) $osData.= '&nbsp;+4&nbsp;';
					}
					if($Druse_OS || $RPE_Changes_OS || $SRNVM_OS || $Edema_OS || $Scars_OS || $Hemorrhage_OS || $Microaneurysms_OS || $Exudates_OS
						|| $Macula_BDR_OS_T || $Macula_BDR_OS_1 || $Macula_BDR_OS_2 || $Macula_BDR_OS_3 || $Macula_BDR_OS_4
						|| $Macula_SMD_OS_T || $Macula_SMD_OS_1 || $Macula_SMD_OS_2 || $Macula_SMD_OS_3 || $Macula_SMD_OS_4){
							$osData.= '<br><span>Macula</span>';
					}				
					if($Druse_OS || $RPE_Changes_OS || $SRNVM_OS || $Edema_OS || $Scars_OS || $Hemorrhage_OS || $Microaneurysms_OS || $Exudates_OS){
						$c = 0;
						if($Druse_OS == 1){ $osData.= '&nbsp;Druse&nbsp;' ; ++$c; }
						if($RPE_Changes_OS == 1){ $osData.= '&nbsp;RPE Changes&nbsp;' ; ++$c; }
						if($SRNVM_OS == 1){ $osData.= '&nbsp;SRNVM&nbsp;' ; ++$c; }
							if($c>=3){ $osData.= '<br>'; $c = 0; }
						if($Edema_OS == 1){ $osData.= '&nbsp;Edema&nbsp;' ; ++$c; }
							if($c>=3){ $osData.= '<br>'; $c = 0; }
						if($Retina_Nevus_OS == 1){ $osData.= '&nbsp;Nevus&nbsp;' ; ++$c; }
							if($c>=3){ $osData.= '<br>'; $c = 0; }
						if($Scars_OS == 1){ $osData.= '&nbsp;Scars&nbsp;' ; ++$c; }
							if($c>=3){ $osData.= '<br>'; $c = 0; }
						if($Hemorrhage_OS == 1){ $osData.= '&nbsp;Hemorrhage&nbsp;' ; ++$c; }
							if($c>=3){ $osData.= '<br>'; $c = 0; }
						if($Microaneurysms_OS == 1){ $osData.= '&nbsp;Microaneurysms&nbsp;' ; ++$c; }
							if($c>=3){ $osData.= '<br>'; $c = 0; }
						if($Exudates_OS == 1){ $osData.= '&nbsp;Exudates&nbsp;' ; ++$c; }
					}
					if($Macula_BDR_OS_T || $Macula_BDR_OS_1 || $Macula_BDR_OS_2 || $Macula_BDR_OS_3 || $Macula_BDR_OS_4){
						$osData.= '<br>';
						$osData.= '&nbsp;<span style="width:75px;">BDR</span>';
						if($Macula_BDR_OS_T == 1) $osData.= '&nbsp;T&nbsp;';
						if($Macula_BDR_OS_1 == 1) $osData.= '&nbsp;+1&nbsp;';
						if($Macula_BDR_OS_2 == 1) $osData.= '&nbsp;+2&nbsp;';
						if($Macula_BDR_OS_3 == 1) $osData.= '&nbsp;+3&nbsp;';
						if($Macula_BDR_OS_4 == 1) $osData.= '&nbsp;+4&nbsp;';
					}		
					if($Macula_SMD_OS_T || $Macula_SMD_OS_1 || $Macula_SMD_OS_2 || $Macula_SMD_OS_3 || $Macula_SMD_OS_4){
						$osData.= '<br>';
						$osData.= '&nbsp;<span style="width:75px;">SMD</span>';
						if($Macula_SMD_OS_T == 1) $osData.= '&nbsp;T&nbsp;';
						if($Macula_SMD_OS_1 == 1) $osData.= '&nbsp;+1&nbsp;';
						if($Macula_SMD_OS_2 == 1) $osData.= '&nbsp;+2&nbsp;';
						if($Macula_SMD_OS_3 == 1) $osData.= '&nbsp;+3&nbsp;';
						if($Macula_SMD_OS_4 == 1) $osData.= '&nbsp;+4&nbsp;';
					}						
					if($osData!="" || $odData!="" || $testresults_desc_od!="" || $testresults_desc_os!=""){
						?>
					<table class="border" cellpadding="0" cellspacing="0" style="width:700px;" class="paddingTop">
						<tr>
							<td  valign="middle" class="tb_heading" style="width:100%" colspan="2">Test Results</td>
						</tr>
						<tr>
							<td class="text_value" style="width:50%" align="left"><?php odLable();?></td>
							<td class="text_value" style="width:50%" align="left"><?php osLable();?></td>
						</tr>
					<?php	
						if($odData || $osData){
						?>
						<tr>
							<td class="text_value" style="width:50%" align="left" valign="top"><?php echo  $odData; ?></td>
							<td class="text_value" style="width:50%" align="left" valign="top"><?php echo  $osData; ?></td>
						</tr>
					<?php
						}	
					if($testresults_desc_od || $testresults_desc_os){
						?>
							<tr>
								<td class="text_value" style="width:50%" align="left"><?php echo $testresults_desc_od; ?></td>
								<td class="text_value" style="width:50%" align="left"><?php echo $testresults_desc_os; ?></td>
							</tr>
						<?php				
					}
					?>
					</table>
						<?php
					}
					
						if($ivfaComments !=""){
						?>
						<table class="border" cellpadding="0" cellspacing="0" style="width:700px;" class="paddingTop">
							<tr>
								<td class="text_value" style="width:100%" align="left"><strong>Comments:</strong><?php echo $ivfaComments; ?></td>
								
							</tr>
						</table>
						<?php				
					}
					$treatment = '';
					
			if($Stable == 1) $treatment.= 'Stable&nbsp;';
			if($ContinueMeds == 1) $treatment.= 'Continue Meds&nbsp;';
			if($FuApa == 1) $treatment.= 'F/U APA&nbsp;';
			if($tech2InformPt == 1) $treatment.= 'Tech to Inform Pt.&nbsp;';
			if($PatientInformed == 1) $treatment.= 'Pt informed of results&nbsp;';					
			if($MonitorAg  == 1) $treatment.= 'Monitor AG&nbsp;';					
			if($ptInformedNv == 1) $treatment.= 'Inform Pt result next visit&nbsp;';										
			
					if($stable == 1 || $treatment!=""){
						?>
						<table cellspacing="0" cellpadding="0"  style="width:100%">
							<tr>
								<td  align="left" class="text_lable" style="width:700px;">Treatment/Prognosis:&nbsp;<span class="text_value"><?php echo $treatment; ?></span></td>
							</tr>
						</table>
						<?php
					}
					if($ArgonLaser || $ArgonLaserEye || $ArgonLaserEyeOptions || $FuRetinaComments){
						if($ArgonLaser) $ArgonLaser = 'Argon Laser Surgery&nbsp;&nbsp;';
						if($ArgonLaserEye) $ArgonLaser.= '<span class=text_value>'.$ArgonLaserEye.'</span>&nbsp;&nbsp;';
						if($ArgonLaserEyeOptions) $ArgonLaser.= $ArgonLaserEyeOptions.'&nbsp;&nbsp;';
						if($FuRetina == 1) $ArgonLaser.= 'F/U Retina &nbsp;';
						if($FuRetinaComments){
						$ArgonLaser.=$FuRetinaComments;
						}
						if($rptTst1yr==1){$ArgonLaser.= '&nbsp;&nbsp;Repeat test 1 year ';}
						?>
						<table class="border" cellpadding="0" cellspacing="0" style="width:100%">
							<tr >
								<td align="left" class="text_value">
									<?php echo $ArgonLaser; ?>
								</td>						
							</tr>
						</table>
						<?php
					}
				
					///Add IVFA Images//
					$imagesHtml=getTestImages($vf_id,$sectionImageFrom="IVFA",$patient_id);
					if($imagesHtml!=""){
						echo('<table cellspacing="0" cellpadding="0"  style="width:100%">
							 	'.$imagesHtml.' 
							</table>');
					} 
					$imagesHtml="";
					//End IVFA Images
					if($phy){
						$getphysicianQry = imw_query("SELECT CONCAT_WS(', ', lname, fname) as physician FROM users WHERE id = '$phy'");
						$getphysicianRow = imw_fetch_assoc($getphysicianQry);
						$physicianName = str_replace(", , "," ",$getphysicianRow['physician']);
						?>
					<table class="border" cellpadding="0" cellspacing="0" style="width:100%">
						<tr >
							<td align="left" class="text_value"><span class="text_lable">Performed By:&nbsp;</span> <?php echo $physicianName; ?></td>
						</tr>
					</table>
						<?php
					}
					?>				
				
		<?php
		}//END IVFA WHILE
	}
}
?>
<!-- IVFA -->

<!-- Fundus -->
<?php
if($disc == true){
if(is_array($_REQUEST["printTestRadioFundus"]) && count($_REQUEST["printTestRadioFundus"])>0){
	$sql = "SELECT * FROM disc WHERE patientId = '".$patient_id."' AND disc_id in(".implode(",",$_REQUEST["printTestRadioFundus"]).")";					
	}else if($form_id){
		$sql = "SELECT * FROM disc WHERE patientId = '".$patient_id."' AND formId = '$form_id' AND purged='0' ";					
	}
	$rowDISC = imw_query($sql);
	if(imw_num_rows($rowDISC)>0){
	while($row=imw_fetch_array($rowDISC)){
		extract($row);
		?>

			<table style="width:700px;" class="paddingTop" class="border" cellspacing="0" cellpadding="0" >
				<tr>
					<td  valign="middle" class="tb_heading" style="width:100%">Fundus (<span class="text_value">Exam Date:&nbsp;<?php print FormatDate_show($examDate);?></span>)</td>
				</tr>
			</table>
					
				<table class="border" cellpadding="0" cellspacing="0" style="width:100%">
					<tr>
						<td  align="left" class="text_lable" style="width:20%">
							<?php 
							if($fundusDiscPhoto == 1){
								echo 'Disc Photo&nbsp;'; 
							}
							if($fundusDiscPhoto == 2){
								echo 'Macula Photo&nbsp;'; 
							}
							if($fundusDiscPhoto == 3){
								echo 'Retina Photo&nbsp;'; 
							}
							if($shots == 1){
								echo 'Early and late shots&nbsp;';
							}
							if($extraCopy == 1){
								echo 'Extra Copy';
							}
							?>
						</td>
							<td  align="left" class="text_value" style="width:80%">
							<?php 
							if($photoEye){
								if($photoEye == 'OD') $photoEye = '<span class="text_lable" style="color:blue;">'.$photoEye.'</span>';
								if($photoEye == 'OS') $photoEye = '<span class="text_lable"  style="color:green;">'.$photoEye.'</span>';
								if($photoEye == 'OU') $photoEye = '<span class="text_lable" style="color:purple;">'.$photoEye.'</span>';
								echo ''.$photoEye.'';
							}
							?>
						</td>
					</tr>
</table>

					<?php 	
					$getNameQry = imw_query("SELECT CONCAT_WS(', ', lname, fname) as performedBy FROM users WHERE id = '$performedBy'");
					if(imw_num_rows($getNameQry)>0){
						$getNameRow = imw_fetch_assoc($getNameQry);
						$performedBy = $getNameRow['performedBy'];
					}
		if($discDesc!=""){
					?>
				<table class="border" cellpadding="0" cellspacing="0" style="width:100%">
					<tr>
						<td  valign="middle" class="text_lable" style="width:100%">Technician Comments:&nbsp;<span class="text_value"><?php echo($discDesc);?></span></td>
					</tr>
				</table>
				<?php } ?>
			<table class="border" cellpadding="0" cellspacing="0" style="width:100%">
				<tr>
				   <td align="left" class="text_value">
					<?php 
						if($performedBy) echo '<span class="text_lable">Performed By:&nbsp;</span> '.$performedBy.'&nbsp;&nbsp;';
						if($ptUnderstanding) echo '<span class="text_lable">Patient Understanding & Cooperation</span>:&nbsp;'.$ptUnderstanding;
					?>
				</td>					
			</tr>
			</table>
		<?php
					if($reliabilityOd || $reliabilityOs){
						?>
					<table class="border" cellpadding="0" cellspacing="0" style="width:100%">
						<tr>	
							<td  valign="middle" class="text_lable">Physician Interpretation:&nbsp;</td>						
							<td valign="middle" class="text_lable">Reliability</td>
							<td valign="middle" class="text_lable"><?php odLable();?></td>
							<td valign="middle" class="text_value"><?php echo $reliabilityOd; ?></td>
							<td style="width:20px;"></td>
							<td valign="middle" class="text_lable"><?php osLable();?></td>
							<td valign="middle" class="text_value"><?php echo $reliabilityOs; ?></td>
						</tr>
					</table>
							
						<?php
					}


					$osData = '';
					$odData = '';
					
					// OD DATA
					if($normal_OD) $odData = 'Normal&nbsp;<br>';
					if($Sharp_Pink_OD || $Pale_OD || $Large_Cap_OD || $Sloping_OD || $Notch_OD || $NVD_OD || $Leakage_OD){
						$odData.= '<span>Disc</span>';
						if($Sharp_Pink_OD == 1){ $odData.= '&nbsp;Sharp Pink&nbsp;' ; ++$c;}
						if($Pale_OD == 1){ $odData.= '&nbsp;Pale&nbsp;'; ++$c;}
						if($Large_Cap_OD == 1){ $odData.= '&nbsp;Large Cup&nbsp;'; ++$c;}
							if($c>=3){ $c = 0; $odData.= '<br>'; $c = 0; }
						if($Sloping_OD == 1){ $odData.= '&nbsp;Sloping&nbsp;'; ++$c;}
							if($c>=3){ $odData.= '<br>'; $c = 0; }
						if($Notch_OD == 1){ $odData.= '&nbsp;Notch&nbsp;'; ++$c;}
							if($c>=3){ $odData.= '<br>'; $c = 0; }
						if($NVD_OD == 1){ $odData.= '&nbsp;NVD&nbsp;'; ++$c;}
							if($c>=3){ $odData.= '<br>'; $c = 0; }
						if($Leakage_OD == 1) $odData.= '&nbsp;Leakage&nbsp;';
					}
					if($Macula_BDR_OD_T || $Macula_BDR_OD_1 || $Macula_BDR_OD_2 || $Macula_BDR_OD_3){
						$odData.= '<br><span>Macula</span>';
						$odData.= '<span>BDR</span>';
						if($Macula_BDR_OD_T == 1) $odData.= '&nbsp;T&nbsp;';
						if($Macula_BDR_OD_1 == 1) $odData.= '&nbsp;+1&nbsp;';
						if($Macula_BDR_OD_2 == 1) $odData.= '&nbsp;+2&nbsp;';
						if($Macula_BDR_OD_3 == 1) $odData.= '&nbsp;+3&nbsp;';
						if($Macula_BDR_OD_4 == 1) $odData.= '&nbsp;+4&nbsp;';
			
						$odData.= '<br>';
						$odData.= '<span>Rpe&nbsp;change</span>';
						if($Macula_Rpe_OD_T == 1) $odData.= '&nbsp;T&nbsp;';
						if($Macula_Rpe_OD_1 == 1) $odData.= '&nbsp;+1&nbsp;';
						if($Macula_Rpe_OD_2 == 1) $odData.= '&nbsp;+2&nbsp;';
						if($Macula_Rpe_OD_3 == 1) $odData.= '&nbsp;+3&nbsp;';
						if($Macula_Rpe_OD_4 == 1) $odData.= '&nbsp;+4&nbsp;';
						
						
						$odData.= '<br>';
						$odData.= '<span>Edema</span>';
						if($Macula_Edema_OD_T == 1) $odData.= '&nbsp;T&nbsp;';
						if($Macula_Edema_OD_1 == 1) $odData.= '&nbsp;+1&nbsp;';
						if($Macula_Edema_OD_2 == 1) $odData.= '&nbsp;+2&nbsp;';
						if($Macula_Edema_OD_3 == 1) $odData.= '&nbsp;+3&nbsp;';
						if($Macula_Edema_OD_4 == 1) $odData.= '&nbsp;+4&nbsp;';
						
						$odData.= '<br>';
						$odData.= '<span>Periphery:&nbsp;</span>';
						if($Macula_SRNVM_OD == 1) $odData.= '&nbsp;SRNVM&nbsp;';
						if($Macula_Scars_OD == 1) $odData.= '&nbsp;Scars&nbsp;';
						if($Macula_Hemorrhage_OD == 1) $odData.= '&nbsp;hemorrhage&nbsp;';
						if($Macula_Microaneurysm_OD == 1) $odData.= '&nbsp;Microaneurysm&nbsp;';
						if($Macula_Exudates_OD == 1) $odData.= '&nbsp;Exudates&nbsp;';						
						if($Macula_Normal_OD == 1) $odData.= '&nbsp;Normal&nbsp;';
						if($Periphery_Hemorrhage_OD == 1) $odData.= '&nbsp;Hemorrhage&nbsp;';
						if($Periphery_Microaneurysms_OD == 1) $odData.= '&nbsp;Microaneurysm&nbsp;';
						if($Periphery_Exudates_OD == 1) $odData.= '&nbsp;Exudates&nbsp;';
						if($Periphery_Cr_Scars_OD == 1) $odData.= '&nbsp;Cr Scars&nbsp;';						
						if($Periphery_NV_OD == 1) $odData.= '&nbsp;NV&nbsp;';
						if($Periphery_Nevus_OD == 1) $odData.= '&nbsp;Nevus&nbsp;';
						if($Periphery_Edema_OD == 1) $odData.= '&nbsp;Edema&nbsp;';
						
						
						$c = 0;			
						$odData.= '<br><span style="width:150px;">&nbsp;</span><br>';
						if($Macula_SRNVM_OD == 1){ $odData.= '&nbsp;SRNVM&nbsp;' ; ++$c;}
						if($Macula_Scars_OD == 1){ $odData.= '&nbsp;Scars&nbsp;' ; ++$c;}
							if($c>=2){ $odData.= '<span style="width:150px;">&nbsp;</span>'; $c = 0; }
						if($Macula_Hemorrhage_OD == 1){ $odData.= '&nbsp;hemorrhage&nbsp;' ; ++$c;}
							if($c>=2){ $odData.= '<span style="width:150px;">&nbsp;</span>'; $c = 0; }
						if($Macula_Microaneurysm_OD == 1){ $odData.= '&nbsp;Microaneurysm&nbsp;' ; ++$c;}
							if($c>=2){ $odData.= '<span style="width:150px;">&nbsp;</span>'; $c = 0; }
						if($Macula_Exudates_OD == 1){ $odData.= '&nbsp;Exudates&nbsp;' ; ++$c;}
							if($c>=2){ $odData.= '<span style="width:150px;">&nbsp;</span>'; $c = 0; }
						if($Macula_Normal_OD == 1){ $odData.= '&nbsp;Normal&nbsp;' ; ++$c;}
					}
					// OS DATA
					if($normal_OS) $osData = 'Normal&nbsp;<br>';
					if($Sharp_Pink_OS || $Pale_OS || $Large_Cap_OS || $Sloping_OS || $Notch_OS || $NVD_OS || $Leakage_OS){
						$osData.= '<span>Disc</span>';
						if($Sharp_Pink_OS == 1){ $osData.= '&nbsp;Sharp Pink&nbsp;' ; ++$c;}
						if($Pale_OS == 1){ $osData.= '&nbsp;Pale&nbsp;'; ++$c;}
						if($Large_Cap_OS == 1){ $osData.= '&nbsp;Large Cup&nbsp;'; ++$c;}
							if($c>=3){ $c = 0; $osData.= ''; $c = 0; }
						if($Sloping_OS == 1){ $osData.= '&nbsp;Sloping&nbsp;'; ++$c;}
							if($c>=3){ $osData.= ''; $c = 0; }
						if($Notch_OS == 1){ $osData.= '&nbsp;Notch&nbsp;'; ++$c;}
							if($c>=3){ $osData.= ''; $c = 0; }
						if($NVD_OS == 1){ $osData.= '&nbsp;NVD&nbsp;'; ++$c;}
							if($c>=3){ $osData.= ''; $c = 0; }
						if($Leakage_OS == 1) $osData.= '&nbsp;Leakage&nbsp;';
					}
					if($Macula_BDR_OS_T || $Macula_BDR_OS_1 || $Macula_BDR_OS_2 || $Macula_BDR_OS_3){
						$osData.= '<br><span>Macula</span>';
						$osData.= '<span>BDR</span>';
						if($Macula_BDR_OS_T == 1) $osData.= '&nbsp;T&nbsp;';
						if($Macula_BDR_OS_1 == 1) $osData.= '&nbsp;+1&nbsp;';
						if($Macula_BDR_OS_2 == 1) $osData.= '&nbsp;+2&nbsp;';
						if($Macula_BDR_OS_3 == 1) $osData.= '&nbsp;+3&nbsp;';
						if($Macula_BDR_OS_4 == 1) $osData.= '&nbsp;+4&nbsp;';
			
						$osData.= '<br>';
						$osData.= '<span>Rpe&nbsp;change</span>';
						if($Macula_Rpe_OS_T == 1) $osData.= '&nbsp;T&nbsp;';
						if($Macula_Rpe_OS_1 == 1) $osData.= '&nbsp;+1&nbsp;';
						if($Macula_Rpe_OS_2 == 1) $osData.= '&nbsp;+2&nbsp;';
						if($Macula_Rpe_OS_3 == 1) $osData.= '&nbsp;+3&nbsp;';
						if($Macula_Rpe_OS_4 == 1) $osData.= '&nbsp;+4&nbsp;';
						
						
						$osData.= '<br>';
						$osData.= '<span>Edema</span>';
						if($Macula_Edema_OS_T == 1) $osData.= '&nbsp;T&nbsp;';
						if($Macula_Edema_OS_1 == 1) $osData.= '&nbsp;+1&nbsp;';
						if($Macula_Edema_OS_2 == 1) $osData.= '&nbsp;+2&nbsp;';
						if($Macula_Edema_OS_3 == 1) $osData.= '&nbsp;+3&nbsp;';
						if($Macula_Edema_OS_4 == 1) $osData.= '&nbsp;+4&nbsp;';
						
						$osData.= '<br>';
						$osData.= '<span>Periphery:&nbsp;</span>';
						if($Macula_SRNVM_OS == 1) $osData.= '&nbsp;SRNVM&nbsp;';
						if($Macula_Scars_OS == 1) $osData.= '&nbsp;Scars&nbsp;';
						if($Macula_Hemorrhage_OS == 1) $osData.= '&nbsp;hemorrhage&nbsp;';
						if($Macula_Microaneurysm_OS == 1) $osData.= '&nbsp;Microaneurysm&nbsp;';
						if($Macula_Exudates_OS == 1) $osData.= '&nbsp;Exudates&nbsp;';						
						if($Macula_Normal_OS == 1) $osData.= '&nbsp;Normal&nbsp;';
						if($Periphery_Hemorrhage_OS == 1) $osData.= '&nbsp;Hemorrhage&nbsp;';
						if($Periphery_Microaneurysms_OS == 1) $osData.= '&nbsp;Microaneurysm&nbsp;';
						if($Periphery_Exudates_OS == 1) $osData.= '&nbsp;Exudates&nbsp;';
						if($Periphery_Cr_Scars_OS == 1) $osData.= '&nbsp;Cr Scars&nbsp;';						
						if($Periphery_NV_OS == 1) $osData.= '&nbsp;NV&nbsp;';
						if($Periphery_Nevus_OS == 1) $osData.= '&nbsp;Nevus&nbsp;';
						if($Periphery_Edema_OS == 1) $osData.= '&nbsp;Edema&nbsp;';
						
						$c = 0;			
						$osData.= '<br><span style="width:150px;">&nbsp;</span><br>';
						if($Macula_SRNVM_OS == 1){ $osData.= '<br>&nbsp;SRNVM&nbsp;' ; ++$c;}
						if($Macula_Scars_OS == 1){ $osData.= '<br>&nbsp;Scars&nbsp;' ; ++$c;}
							if($c>=2){ $osData.= '<br><span style="width:150px;">&nbsp;</span>'; $c = 0; }
						if($Macula_Hemorrhage_OS == 1){ $osData.= '&nbsp;hemorrhage&nbsp;' ; ++$c;}
							if($c>=2){ $osData.= '<br><span style="width:150px;">&nbsp;</span>'; $c = 0; }
						if($Macula_Microaneurysm_OS == 1){ $osData.= '&nbsp;Microaneurysm&nbsp;' ; ++$c;}
							if($c>=2){ $osData.= '<br><span style="width:150px;">&nbsp;</span>'; $c = 0; }
						if($Macula_Exudates_OS == 1){ $osData.= '&nbsp;Exudates&nbsp;' ; ++$c;}
							if($c>=2){ $osData.= '<br><span style="width:150px;">&nbsp;</span>'; $c = 0; }
						if($Macula_Normal_OS == 1){ $osData.= '&nbsp;Normal&nbsp;' ; ++$c;}
					}


		if($osData!="" || $odData!="" || $cdOd!="" || $cdOs!="" || $resDescOd!="" || $resDescOs!=""){
						?>
					<table  cellpadding="0" cellspacing="0" style="width:700px;" class="paddingTop border">
						<tr>
							<td  valign="middle" class="tb_heading" style="width:100%" colspan="2">Test Results</td>
						</tr>
						<tr>
							<td class="text_value" style="width:50%" align="left"><?php odLable();?></td>
							<td class="text_value" style="width:50%" align="left"><?php osLable();?></td>
						</tr>
					<?php	
						if($cdOd!="" || $cdOd!=""){
						?>
						<tr>
							<td class="text_value" style="width:50%" align="left" valign="top"><?php echo 'C:D&nbsp;'.$cdOd; ?></td>
							<td class="text_value" style="width:50%" align="left" valign="top"><?php echo 'C:D&nbsp;'.$cdOs; ?></td>
						</tr>
					<?php
						}	
					
						if($odData || $osData){
						?>
						<tr>
							<td class="text_value" style="width:50%" align="left" valign="top"><?php echo  $odData; ?></td>
							<td class="text_value" style="width:50%" align="left" valign="top"><?php echo  $osData; ?></td>
						</tr>
						
					<?php
						}	
					if($resDescOd!="" || $resDescOs!=""){
						?>
							<tr>
								<td class="text_value" style="width:50%" align="left"><span class="text_lable">Description:&nbsp;</span><?php echo $resDescOd; ?></td>
								<td class="text_value" style="width:50%" align="left"><span class="text_lable">Description:&nbsp;</span><?php echo $resDescOs; ?></td>
							</tr>
						<?php				
					}
					?>
					</table>
						<?php
					}				
					$treatment = '';
					if($stable == 1) $treatment.= 'Stable&nbsp;&nbsp;';
					if($monitorAg == 1) $treatment.= 'Continue Meds&nbsp;';
					if($fuApa == 1) $treatment.= 'F/U APA&nbsp;&nbsp;';
					if($tech2InformPt == 1) $treatment.= 'Tech to Inform Pt.&nbsp;';
					if($ptInformed == 1) $treatment.= 'Pt informed of results&nbsp;&nbsp;';					
					if($contiMeds == 1) $treatment.= 'Monitor AG&nbsp;&nbsp;';					
					if($ptInformedNv == 1) $treatment.= 'Inform Pt result next visit&nbsp;';										
					
					if($fuRetina == 1) $treatment.= 'F/U Retina &nbsp;&nbsp;';

					if($treatment!=""){

						?>

						<table cellspacing="0" cellpadding="0"  style="width:100%">
							<tr>
								<td  align="left" class="text_lable" style="width:700px;">Treatment/Prognosis:&nbsp;<span class="text_value"><?php echo $treatment; ?></span></td>
							</tr>
						</table>
						<?php 
						}
						if($fuRetinaDesc){
						?>
						<table cellspacing="0" cellpadding="0"  style="width:100%">
								<tr>
									<td  align="left"  class="text_value" style="width:100%"><?php echo "<span class='text_lable'>Desc:</span>".$fuRetinaDesc; ?></td>						
								</tr>
						</table>
						<?php
						}
					///Add Disc Images//
					$imagesHtml=getTestImages($disc_id,$sectionImageFrom="discExternal",$patient_id);
					if($imagesHtml!=""){
						echo('<table cellspacing="0" cellpadding="0"  style="width:100%">
							 	'.$imagesHtml.' 
							</table>');
					} 
					$imagesHtml="";
					//End Disc Images
						
					
				}//End of Fundus While	
			}
		}
		?>

<!-- Fundus -->

<!-- External   -->
<?php
if($external == true){

	if(is_array($_REQUEST["printTestRadioExternal_Anterior"]) && count($_REQUEST["printTestRadioExternal_Anterior"])>0){
		$sql = "SELECT * FROM disc_external WHERE patientId = '".$patient_id."' AND disc_id in(".implode(",",$_REQUEST["printTestRadioExternal_Anterior"]).")";		
	}else if($form_id){
		$sql = "SELECT * FROM disc_external WHERE patientId = '".$patient_id."' AND formId = '$form_id' AND purged='0' ";	
	}
	$rowexternal = imw_query($sql);
	if(imw_num_rows($rowexternal)>0){
		while($row=imw_fetch_array($rowexternal)){

		extract($row);
		?>
			<table style="width:700px;" class="paddingTop border" cellspacing="0" cellpadding="0" >
				<tr>
					<td  valign="middle" class="tb_heading" style="width:100%">External (<span class="text_value">Exam Date:&nbsp;<?php print FormatDate_show($examDate);?></span>)</td>
				</tr>
			</table>
			<table class="border" cellpadding="0" cellspacing="0" style="width:100%">
				<tr>
					<td  align="left" class="text_value">
						<?php 
						if($fundusDiscPhoto == 1){
							echo 'ES (External)&nbsp;'; 
						}
						if($fundusDiscPhoto == 2){
							echo 'ASP (Anterior Segment Photos)&nbsp;'; 
						}						
						?>
					</td>
					<td  align="left" class="text_value">
						<?php 
						if($photoEye){

							if($photoEye == 'OD') $photoEyeEXT = '<span style="color:blue;">'.$photoEye.'</span>';
							if($photoEye == 'OS') $photoEyeEXT = '<span style="color:green;">'.$photoEye.'</span>';
							if($photoEye == 'OU') $photoEyeEXT = '<span style="color:purple;">'.$photoEye.'</span>';
							echo ''.$photoEyeEXT.'';
						}
						?>
					</td>
				</tr>
			</table>
			<?php
			if($discDesc){
			?>
			<table class="border" cellpadding="0" cellspacing="0" style="width:100%">
				<tr>
					<td  align="left" class="text_value"><span class="text_lable">Technician Comments:&nbsp;</span><?php echo $discDesc; ?></td>
				</tr>
			</table>
			<?php
			}
				///Add discExternal Images//
					$imagesHtml=getTestImages($disc_id,$sectionImageFrom="discExternal",$patient_id);
					if($imagesHtml!=""){
						echo('<table cellspacing="0" cellpadding="0"  style="width:100%">
							 	'.$imagesHtml.' 
							</table>');
					} 
					$imagesHtml="";
					//End discExternal Images
			?>
			<?php 	
			$getNameQry = imw_query("SELECT CONCAT_WS(', ', lname, fname) as performedBy FROM users WHERE id = '$performedBy'");
			if(imw_num_rows($getNameQry)>0){
				$getNameRow = imw_fetch_assoc($getNameQry);
				$performedBy = $getNameRow['performedBy'];
			}
			?>
			<table class="border" cellpadding="0" cellspacing="0" style="width:100%">
				<tr>
				   <td align="left" class="text_value">
					<?php 
						if($performedBy) echo '<span class="text_lable">Performed By:&nbsp;</span> '.$performedBy.'&nbsp;&nbsp;';
						if($ptUnderstanding) echo '<span class="text_lable">Patient Understanding & Cooperation</span>:&nbsp;'.$ptUnderstanding;
					?>
					</td>					
				</tr>				
			</table>
			<?php 
			if($diagnosis && $diagnosis!="--Select--"){
			?>
			<table cellspacing="0" cellpadding="0"  style="width:100%">
						<tr>
							<td  align="left" class="text_value"><span class="text_lable">Diagnosis:&nbsp;</span> <?php echo ($diagnosis!="--Select--") ? $diagnosis : ""; ?></td>
						</tr>
			</table>
			<?php
			}			
			if($reliabilityOd || $reliabilityOs){
			?>
			<table class="border" cellpadding="0" cellspacing="0" style="width:100%">
						<tr>	
							<td  valign="middle" class="text_lable">Physician Interpretation:&nbsp;</td>						
							<td valign="middle" class="text_lable">Reliability</td>
							<td valign="middle" class="text_lable"><?php odLable();?></td>
							<td valign="middle" class="text_value"><?php echo $reliabilityOd; ?></td>
							<td style="width:20px;"></td>
							<td valign="middle" class="text_lable"><?php osLable();?></td>
							<td valign="middle" class="text_value"><?php echo $reliabilityOs; ?></td>
						</tr>
					</table>
			<?php
			}
			$osData = '';
			$odData = '';
			// OD DATA
			$odData.= '<span>Ptosis</span>';			
			if($ptosisOd_neg == 1) $odData.= '&nbsp;Negative&nbsp;';
			if($ptosisOd_T == 1) $odData.= '&nbsp;T&nbsp;';
			if($ptosisOd_pos1 == 1) $odData.= '&nbsp;+1&nbsp;';
			if($ptosisOd_pos2 == 1) $odData.= '&nbsp;+2&nbsp;';
			if($ptosisOd_pos3 == 1) $odData.= '&nbsp;+3&nbsp;';
			if($ptosisOd_pos4 == 1) $odData.= '&nbsp;+4&nbsp;';
			if($ptosisOd_rul == 1) $odData.= '&nbsp;+RUL&nbsp;';
			if($ptosisOd_rll == 1) $odData.= '&nbsp;+RLL&nbsp;';
			
			$odData.= '<br>';
			$odData.= '<span>Dematochalasis</span>';
			if($dermaOd_neg == 1) $odData.= '&nbsp;Negative&nbsp;';
			if($dermaOd_T == 1) $odData.= '&nbsp;+1&nbsp;';
			if($dermaOd_pos1 == 1) $odData.= '&nbsp;+1&nbsp;';
			if($dermaOd_pos2 == 1) $odData.= '&nbsp;+2&nbsp;';
			if($dermaOd_pos3 == 1) $odData.= '&nbsp;+3&nbsp;';
			if($dermaOd_pos4 == 1) $odData.= '&nbsp;+4&nbsp;';
			if($dermaOd_rul == 1) $odData.= '&nbsp;+RUL&nbsp;';
			if($dermaOd_rll == 1) $odData.= '&nbsp;+RLL&nbsp;';
			
			$odData.= '<br>';
			$odData.= '<span>Pterygium</span>';
			if($pterygium1mmOd == 1) $odData.= '&nbsp;1mm&nbsp;';
			if($pterygium2mmOd == 1) $odData.= '&nbsp;2mm&nbsp;';
			if($pterygium3mmOd == 1) $odData.= '&nbsp;3mm&nbsp;';
			if($pterygium4mmOd == 1) $odData.= '&nbsp;4mm&nbsp;';
			if($pterygium5mmOd == 1) $odData.= '&nbsp;5mm&nbsp;';
			if($pterygiumNasalOd == 1) $odData.= '&nbsp;Nasal&nbsp;';
			if($pterygiumTemporalOd == 1) $odData.= '&nbsp;Temporal&nbsp;';
			
			$odData.= '<br>';
			$odData.= '<span>Vascularization</span>';
			if($vascOd_SubEpithelial == 1) $odData.= '&nbsp;Sub-epithelial&nbsp;';
			if($vascOd_Stromal == 1) $odData.= '&nbsp;Stromal&nbsp;';
			if($vascOd_Superficial == 1) $odData.= '&nbsp;Superficial&nbsp;';
			if($vascOd_Deep == 1) $odData.= '&nbsp;Deep&nbsp;';
			if($vascOd_Endothelial == 1) $odData.= '&nbsp;<br>Endothelial&nbsp;';
			if($vascOd_Peripheral == 1) $odData.= '&nbsp;<br>Peripheral&nbsp;';
			if($vascOd_Central == 1) $odData.= '&nbsp;Central&nbsp;<br>';
			if($vascOd_Pannus == 1) $odData.= '&nbsp;Pannus&nbsp;';
			if($vascOd_GhostBV == 1) $odData.= '&nbsp;Ghost BV&nbsp;';
			if($vascOd_Superior == 1) $odData.= '&nbsp;<br>Superior&nbsp;';
			if($vascOd_Inferior == 1) $odData.= '&nbsp;Inferior&nbsp;<br>';
			if($vascOd_Nasal == 1) $odData.= '&nbsp;Nasal&nbsp;';
			if($vascOd_Temporal  == 1) $odData.= '&nbsp;Temporal&nbsp;';
			
			$odData.= '<br>';
			$odData.= '<span>Nevus</span>';
			if($NevusOd_neg == 1) $odData.= '&nbsp;Negative&nbsp;';
			if($NevusOd_Pos == 1) $odData.= '&nbsp;Positive&nbsp;';
			if($NevusOd_Inferior == 1) $odData.= '&nbsp;<br>Inferior&nbsp;';
			if($NevusOd_Superior == 1) $odData.= '&nbsp;Superior&nbsp;<br>';
			if($NevusOd_Temporal == 1) $odData.= '&nbsp;Temporal&nbsp;';
			if($NevusOd_Nasal == 1) $odData.= '&nbsp;Nasal&nbsp;';
			
			
			// OS DATA
			$osData = '<span>Ptosis</span>';			
			if($ptosisOs_neg == 1) $osData.= '&nbsp;Negative&nbsp;';
			if($ptosisOs_T == 1) $osData.= '&nbsp;T&nbsp;';
			if($ptosisOs_pos1 == 1) $osData.= '&nbsp;+1&nbsp;';
			if($ptosisOs_pos2 == 1) $osData.= '&nbsp;+2&nbsp;';
			if($ptosisOs_pos3 == 1) $osData.= '&nbsp;+3&nbsp;';
			if($ptosisOs_pos4 == 1) $oData.= '&nbsp;+4&nbsp;';
			if($ptosisOs_rul == 1) $osData.= '&nbsp;+RUL&nbsp;';
			if($ptosisOs_rll == 1) $osData.= '&nbsp;+RLL&nbsp;';
			
			$osData.= '<br>';
			$osData.= '<span>Dematochalasis</span>';
			if($dermaOs_neg == 1) $osData.= '&nbsp;Negative&nbsp;';
			if($dermaOs_T == 1) $osData.= '&nbsp;+1&nbsp;';
			if($dermaOs_pos1 == 1) $osData.= '&nbsp;+1&nbsp;';
			if($dermaOs_pos2 == 1) $osData.= '&nbsp;+2&nbsp;';
			if($dermaOs_pos3 == 1) $osData.= '&nbsp;+3&nbsp;';
			if($dermaOs_pos4 == 1) $osData.= '&nbsp;+4&nbsp;';
			if($dermaOs_rul == 1) $osData.= '&nbsp;+RUL&nbsp;';
			if($dermaOs_rll == 1) $osData.= '&nbsp;+RLL&nbsp;';
			
			$osData.= '<br>';
			$osData.= '<span>Pterygium</span>';
			if($pterygium1mmOs == 1) $osData.= '&nbsp;1mm&nbsp;';
			if($pterygium2mmOs == 1) $osData.= '&nbsp;2mm&nbsp;';
			if($pterygium3mmOs == 1) $osData.= '&nbsp;3mm&nbsp;';
			if($pterygium4mmOs == 1) $osData.= '&nbsp;4mm&nbsp;';
			if($pterygium5mmOs == 1) $osData.= '&nbsp;5mm&nbsp;';
			if($pterygiumNasalOs == 1) $osData.= '&nbsp;Nasal&nbsp;';
			if($pterygiumTemporalOs == 1) $osData.= '&nbsp;Temporal&nbsp;';
			
			$osData.= '<br>';
			$osData.= '<span>Vascularization</span>';
			if($vascOs_SubEpithelial == 1) $osData.= '&nbsp;Sub-epithelial&nbsp;';
			if($vascOs_Stromal == 1) $osData.= '&nbsp;Stromal&nbsp;';
			if($vascOs_Superficial == 1) $osData.= '&nbsp;Superficial&nbsp;';
			if($vascOs_Deep == 1) $osData.= '&nbsp;Deep&nbsp;';
			if($vascOs_Endothelial == 1) $osData.= '&nbsp;<br>Endothelial&nbsp;';
			if($vascOs_Peripheral == 1) $osData.= '&nbsp;Peripheral&nbsp;';
			if($vascOs_Central == 1) $osData.= '&nbsp;Central&nbsp;';
			if($vascOs_Pannus == 1) $osData.= '&nbsp;Pannus&nbsp;';
			if($vascOs_GhostBV == 1) $osData.= 'Ghost BV&nbsp;';
			if($vascOs_Superior == 1) $osData.= '&nbsp;Superior&nbsp;';
			if($vascOs_Inferior == 1) $osData.= '&nbsp;Inferior&nbsp;';
			if($vascOs_Nasal == 1) $osData.= '&nbsp;Nasal&nbsp;';
			if($vascOs_Temporal  == 1) $osData.= '&nbsp;Temporal&nbsp;';
			
			$osData.= '<br>';
			$osData.= 'Nevus';
			if($NevusOs_neg == 1) $osData.= '&nbsp;Negative&nbsp;';
			if($NevusOs_Pos == 1) $osData.= '&nbsp;Positive&nbsp;';
			if($NevusOs_Inferior == 1) $osData.= '&nbspInferior &nbsp;';
			if($NevusOs_Superior == 1) $osData.= '&nbsp;Superior &nbsp;';
			if($NevusOs_Temporal == 1) $osData.= '&nbsp;Temporal &nbsp;';
			if($NevusOs_Nasal == 1) $osData.= '&nbsp; Nasal &nbsp;';
			if($osData!="" || $odData!=""  || $resDescOd!="" || $resDescOs!=""){
						?>
					<table class="border" cellpadding="0" cellspacing="0" style="width:700px;" class="paddingTop">
						<tr>
							<td  valign="middle" class="tb_heading" style="width:100%" colspan="2">Test Results</td>
						</tr>
						<tr>
							<td class="text_value bdrbtm" style="width:50%" align="left"><?php odLable();?></td>
							<td class="text_value bdrbtm" style="width:50%" align="left"><?php osLable();?></td>
						</tr>
					<?php	
						
					
						if($odData!="" || $osData!=""){
						?>
						<tr>
							<td class="text_value bdrbtm" style="width:50%" align="left" valign="top"><?php echo  $odData; ?></td>
							<td class="text_value bdrbtm" style="width:50%" align="left" valign="top"><?php echo  $osData; ?></td>
						</tr>
						
					<?php
						}	
					if($resDescOd!="" || $resDescOs!=""){
						?>
							<tr>
								<td class="text_value bdrbtm" style="width:50%" align="left"><span class="text_lable">Description:&nbsp;</span><?php echo $resDescOd; ?></td>
								<td class="text_value bdrbtm" style="width:50%" align="left"><span class="text_lable">Description:&nbsp;</span><?php echo $resDescOs; ?></td>
							</tr>
						<?php				
					}
					?>
					</table>
						<?php
					}				
			
			
			$treatment = '';
			if($stable == 1) $treatment.= 'Stable&nbsp;';
			if($monitorAg == 1) $treatment.= 'Continue Meds&nbsp;';
			if($fuApa == 1) $treatment.= 'F/U APA&nbsp;';
			if($tech2InformPt == 1) $treatment.= 'Tech to Inform Pt.&nbsp;';
			if($ptInformed == 1) $treatment.= 'Pt informed of results&nbsp;';					
			if($contiMeds == 1) $treatment.= 'Monitor AG&nbsp;';					
			if($ptInformedNv == 1) $treatment.= 'Inform Pt result next visit&nbsp;';										
			
			if($fuRetina == 1) $treatment.= 'F/U Retina &nbsp;&nbsp;';
			if($treatment!="" || $fuRetinaDesc!=""){
			?>
			<table class="border" cellpadding="0" cellspacing="0" style="width:100%">
				<?php 
				if($treatment!=""){
				?>
				<tr>
					<td  align="left" class="text_lable bdrbtm" style="width:700px;">Treatment/Prognosis:&nbsp;<span class="text_value"><?php echo $treatment; ?></span></td>
				</tr>
						
				<?php 
				}
				if($fuRetinaDesc){
				?>
				<tr>
					<td  align="left"  class="text_value bdrbtm" style="width:100%"><?php echo "<span class='text_lable'>Desc:</span>".$fuRetinaDesc; ?></td>						
				</tr>
				<?php
				}
				?>
			</table>
				<?php
			}
			
	}//end External While
 }
}
?>
<!-- External   -->

<!-- Topography   -->
<?php
if($topography == true){

	if(is_array($_REQUEST["printTestRadioTopography"]) && count($_REQUEST["printTestRadioTopography"])>0){
		$sql = "SELECT * FROM topography WHERE patientId = '".$patient_id."' AND topo_id in(".implode(",",$_REQUEST["printTestRadioTopography"]).")";		
	}else if($form_id){
		$sql = "SELECT * FROM topography WHERE patientId = '".$patient_id."' AND formId = '$form_id' AND purged='0' ";		
	}
	$rowtopography = imw_query($sql);
	if(imw_num_rows($rowtopography)>0){
		while($row=imw_fetch_array($rowtopography)){
		extract($row);
		?>
			<table style="width:700px;" class="paddingTop border" cellspacing="0" cellpadding="0" >
				<tr>
					<td  valign="middle" class="tb_heading" style="width:700px;">Topography
					<span class="text_lable">
						<?php 
						if($topoMeterEye){
							if(trim($topoMeterEye) == 'OD') $topoMeterEye = '<span style="color:blue;">'.$topoMeterEye.'</span>';
							if(trim($topoMeterEye) == 'OS') $topoMeterEye = '<span style="color:green;">'.$topoMeterEye.'</span>';
							if(trim($topoMeterEye) == 'OU') $topoMeterEye = '<span style="color:purple;">'.$topoMeterEye.'</span>';
							echo ''.$topoMeterEye.'';
						}
						?>
					</span>
					(<span class="text_value">Exam Date:&nbsp;<?php print FormatDate_show($examDate);?></span>)
				</td>
				</tr>
			<?php
			if($techComments!=""){
			?>
			<tr>
				<td  align="left" class="text_value" style="width:100%">
					<span class="text_lable">Technician Comments:&nbsp;</span><?php echo $techComments; ?>						
				</td>
			</tr>
			<?php }?>
			</table>
			<?php

			$getNameQry = imw_query("SELECT CONCAT_WS(', ', lname, fname) as performedBy FROM users WHERE id = '$performedBy'");
			if(imw_num_rows($getNameQry)>0){
				$getNameRow = imw_fetch_assoc($getNameQry);
				$performedBy = $getNameRow['performedBy'];
			}
			?>
			<table class="border" cellpadding="0" cellspacing="0" style="width:100%">
				<tr>
				   <td align="left" class="text_value" style="width:100%">
					<?php 
						if($performedBy) echo '<span class="text_lable">Performed By:&nbsp;</span> '.$performedBy.'&nbsp;&nbsp;';
						if($ptUnderstanding) echo '<span class="text_lable">Patient Understanding & Cooperation</span>:&nbsp;'.$ptUnderstanding;
					?>
					</td>					
				</tr>				
			</table>
			<?php 
			if($diagnosis && $diagnosis!="--Select--"){
			?>
			<table cellspacing="0" cellpadding="0"  style="width:100%">
						<tr>
							<td  align="left" class="text_value" style="width:100%"><span class="text_lable">Diagnosis:&nbsp;</span> <?php echo ($diagnosis!="--Select--") ? $diagnosis : ""; ?></td>
						</tr>
			</table>
			<?php
			}			
			if($reliabilityOd || $reliabilityOs || $descOd || $descOs){
			
					if($reliabilityOd || $reliabilityOs){
					?>
				<table class="border" cellpadding="0" cellspacing="0" style="width:100%">		
					<tr>	
							<td  valign="middle" class="text_lable">Physician Interpretation:&nbsp;</td>						
							<td valign="middle" class="text_lable">Reliability</td>
							<td valign="middle" class="text_lable"><?php odLable();?></td>
							<td valign="middle" class="text_value"><?php echo $reliabilityOd; ?></td>
							<td style="width:20px;"></td>
							<td valign="middle" class="text_lable"><?php osLable();?></td>
							<td valign="middle" class="text_value"><?php echo $reliabilityOs; ?></td>
						</tr>
				</table>
				<?php }
				if($descOd || $descOs){
				?>
				<table class="border" cellpadding="0" cellspacing="0" style="width:100%">
				<tr>							
					<td valign="middle" class="text_lable">Reliability Description</td>
					<td style="width:35px;"></td>
					<?php if($descOd){
					?>
					<td valign="middle" class="text_lable"><?php odLable();?></td>
					<?php }?>
					<td valign="middle" class="text_value"></td>
					<td style="width:20px;"></td>
					<?php if($descOs){
					?>
					<td valign="middle" class="text_lable"><?php osLable();?></td>
					<?php }?>
					<td valign="middle" class="text_value"></td>
				</tr>
				<tr>							
					<td valign="middle" class="text_lable"></td>
					<td style="width:35px;"></td>					
					<?php if($descOd){
					?>
					<td valign="middle" colspan="2" class="text_value"><?php echo $descOd; ?></td>
					<?php }?>
					<td style="width:20px;"></td>					
					<?php if($descOs){
					?>
					<td valign="middle" colspan="2" class="text_value"><?php echo $descOs; ?></td>
					<?php }?>
				</tr>
			</table>
				<?php }
				
			}
			
			$treatment = '';
			if($stable == 1) $treatment.= 'Stable&nbsp;&nbsp;';
			if($monitorAg == 1) $treatment.= 'Continue Meds&nbsp;';
			if($fuApa == 1) $treatment.= 'F/U APA&nbsp;&nbsp;';
			if($tech2InformPt == 1) $treatment.= 'Tech to Inform Pt.&nbsp;';
			if($ptInformed == 1) $treatment.= 'Pt informed of results&nbsp;&nbsp;';					
			if($contiMeds == 1) $treatment.= 'Monitor AG&nbsp;&nbsp;';					
			if($ptInformedNv == 1) $treatment.= 'Inform Pt result next visit&nbsp;';										
			
			if($fuRetina == 1) $treatment.= 'F/U Retina &nbsp;&nbsp;';
			if($treatment!=""  || $comments!=""){
			?>
			<table class="border" cellpadding="0" cellspacing="0" style="width:740px">
					
			<?php if($treatment!=""){	?>
				<tr>
					<td  align="left" class="text_lable" style="width:700px;">Treatment/Prognosis:&nbsp;<span class="text_value"><?php echo $treatment; ?></span></td>
				</tr>
			<?php 
				}
				if($comments!=""){
					?>
					
						<tr>
							<td  align="left" class="text_lable">Comments:&nbsp;<span class="text_value"><?php echo $comments; ?></span></td>
						</tr>
					
					<?php
					}
					?>
				</table>
			<?php
			}
			///Add Topogrphy Images//
					$imagesHtml=getTestImages($topo_id,$sectionImageFrom="Topogrphy",$patient_id);
					if($imagesHtml!=""){
						echo('<table cellspacing="0" cellpadding="0"  style="width:100%">
							 	'.$imagesHtml.' 
							</table>');
					} 
					$imagesHtml="";
					//End Topogrphy Images
			if($phyName){
				$getphysicianQry = imw_query("SELECT CONCAT_WS(', ', lname, fname) as physician FROM users WHERE id = '$phyName'");
				$getphysicianRow = imw_fetch_assoc($getphysicianQry);
				$physicianName = str_replace(", ,"," ",$getphysicianRow['physician']);
				?>
				<table class="border" cellpadding="0" cellspacing="0" style="width:100%">
					<tr >
						<td align="left" class="text_value" style="width:100%"><span class="text_lable">Interpreted By:&nbsp;</span> <?php echo $physicianName; ?></td>
					</tr>
				</table>
				<?php
			}
		}		//End Of topogroahy while
	}
}

include("test_cellcount_print.php");
include("test_laboratoriest_print.php");
?>
<!-- Topography   -->
