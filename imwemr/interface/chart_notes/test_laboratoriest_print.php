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

File: test_laboratoriest_print.php
Purpose: This file provides Lab test print version.
Access Type : Include file
*/
?>
<?php 

$patient_id = $_SESSION['patient'];
	
	if(is_array($_REQUEST["printTestRadioLaboratories"]) && count($_REQUEST["printTestRadioLaboratories"])>0){
		$sql = "SELECT * FROM test_labs WHERE patientId = '$patient_id' AND test_labs_id in(".implode(",",$_REQUEST["printTestRadioLaboratories"]).")";		
	}else if($form_id){
		$sql = "SELECT * FROM test_labs WHERE patientId = '$patient_id' AND formId = '$form_id'";		
	}
	$rowCellCount= imw_query($sql);
	if(imw_num_rows($rowCellCount)>0){
		while($row=imw_fetch_array($rowCellCount)){
		$test_labs_id=$row["test_labs_id"];
		$elem_examDate = ($topo_mode != "new") ? FormatDate_show($row["examDate"]) : $elem_examDate;
		$elem_examTime = ($topo_mode != "new") ? $row["examTime"] : $elem_examTime ;
		$elem_testLabsName = $row["test_labs"];
		$elem_topoMeterEye = $row["test_labs_eye"];
		$elem_performedBy = ($topo_mode != "new") ? $row["performedBy"] : "";
		$elem_ptUnderstanding = $row["ptUnderstanding"];
		$elem_diagnosis = $row["diagnosis"];
		$elem_diagnosisOther = $row["diagnosisOther"];
		$elem_reliabilityOd = $row["reliabilityOd"];
		$elem_reliabilityOs = $row["reliabilityOs"];		
		$elem_descOd = stripslashes($row["descOd"]);
		$elem_descOs = stripslashes($row["descOs"]);
		$elem_inter_pret_od = stripslashes($row["inter_pret_od"]);
		$elem_inter_pret_os = stripslashes($row["inter_pret_os"]);
		$elem_stable = $row["stable"];
		$elem_fuApa = $row["fuApa"];
		$elem_ptInformed = $row["ptInformed"];				
		$elem_physician = ( $topo_mode == "update" ) ? $row["phyName"] : "" ;		
		$elem_tech2InformPt=$row["tech2InformPt"];		
		$elem_techComments = stripslashes($row["techComments"]);
		$elem_informedPtNv = $row["ptInformedNv"];
		$elem_contiMeds = $row["contiMeds"];
		$encounterId = $row["encounter_id"];
		$elem_opidTestOrdered = $row["ordrby"];
		if(($row["ordrdt"] != "" && $row["ordrdt"] != "0000-00-00")){		
			$elem_opidTestOrderedDate = FormatDate_show($row["ordrdt"]);
		}

	

?>	
<table style="width:100%;" class="paddingTop" border="0" cellspacing="0" cellpadding="0" >
	<tr>
		<td  valign="middle" class="tb_heading" style="width:100%">Laboratories (<span class="text_value">Exam Date:&nbsp;<?php print $elem_examDate;?></span>)</td>
	</tr>
</table>							
							
<table  style="width:100%;"  border="0" cellspacing="0" cellpadding="0">
<?php if($elem_testLabsName!=""){?>
	<tr  valign="middle">
		<td  colspan="2" class="text_lable"><?php echo $elem_testLabsName."&nbsp;". $elem_topoMeterEye; ?></td>
	</tr>
<?php }?>
<?php if($elem_techComments!=""){?>
	<tr  valign="middle">
		<td  colspan="2" class="text_lable">Technician Comments:<span class="text_value"><?php echo $elem_techComments; ?></span></td>
	</tr>
<?php }?>
	<tr>
		
		<td colspan="2">
			<table border="0" cellspacing="0" cellpadding="0">
				<tr>
					<?php 
					if($elem_performedBy){
					?>
					<td class="text_lable">Performed By:&nbsp;</td>
					<td class="text_value">
					<?php 
						if($elem_performedBy!=""){
							$getNameQry = imw_query("SELECT CONCAT_WS(', ', lname, fname) as phyName FROM users WHERE id = '".$elem_performedBy."'");	
							$getNameRow = imw_fetch_assoc($getNameQry);
							$PerformedBy = str_replace(", ,"," ",$getNameRow['phyName']);
							print($PerformedBy);
						}
					?>
					</td>
					<?php }?>
					<td class="text_lable">Patient Understanding & Cooperation:&nbsp;</td>
					<td class="text_value">
						<?php echo ($elem_ptUnderstanding);?>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td class="text_lable" colspan="2"><?php if($elem_diagnosis!="--Select--"){
						echo("Diagnosis:&nbsp;".$elem_diagnosis);
				}?></td>
	</tr>
	<tr>
		<td  class="text_lable" colspan="2">Physician Interpretation </td>
	</tr>
	<tr  valign="middle">
		<td colspan="2">
			<table border="0" cellpadding="0" cellspacing="0">
				<tr>
					<td  class="text_lable">Reliability</td>
					<td  class="text_value" ><?php odLable();?>
						 <?php echo ($elem_reliabilityOd);?>
					</td>
					<td class="text_value" ><?php osLable();?>

						 <?php echo ($elem_reliabilityOs);?>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		
		<td>
			<table style="width:100%;" border="0"  cellpadding="0" cellspacing="0" >
			
			<tr>
				<td style="width:340px;">
				<table border="0" align="center" cellpadding="0" cellspacing="0">
					
					<tr valign="middle">
						<td colspan="2"  class="text_lable"><?php odLable();?></td>
					</tr>

					<tr>
						<td class="text_lable">Comments</td>
						<td class="text_value">
							<?php echo $elem_descOd;?>
						</td>
					</tr>
					<tr>
						<td class="text_lable">Interpretation</td>
						<td class="text_value">
							<?php echo $elem_inter_pret_od;?>
						</td>
					</tr>
						
				</table>
			</td>
			
			<td style="width:340px;">													
				<table  border="0"  cellpadding="0" cellspacing="0">
					
					<tr valign="middle">
						<td  class="text_lable"><?php osLable();?></td>
					</tr>
					 <tr>																
						<td class="text_value">
							<?php echo $elem_descOs;?>
						</td>
					</tr>
 					<tr>																
						<td class="text_value">
							<?php echo $elem_inter_pret_os;?>
						</td>
					</tr>
					
					
				</table>
			</td>
			</tr>
		</table>													
	</td>
	</tr>
	<tr  valign="middle">
		<td  class="text_lable" colspan="2">Treatment/Prognosis:</td>
	</tr>
	<tr>
		<td colspan="2">
			<table border="0" cellspacing="0" cellpadding="0">
				<tr>
					
					<td colspan="2">
						
							<table border="0" cellspacing="0" cellpadding="0" >
								<tr  valign="top">
									<td class='text_value'>
										<?php echo ($elem_stable == "1") ? "Stable" : "" ;?>
										<?php echo ($elem_contiMeds == "1") ? "Continue Meds" : "" ;?>	
										 <?php echo ($elem_fuApa == "1") ? "F/U APA" : "" ;?>
									</td>
									<td class='text_value'>	
										<?php echo ($elem_tech2InformPt == "1") ? "Tech to Inform Pt." : "" ;?>
										 <?php echo ($elem_ptInformed == "1") ? "Pt informed of results" : "" ;?>
										 <?php echo ($elem_informedPtNv == "1") ? "Inform Pt result next visit" : "" ;?>
									</td>			
								</tr>	
							</table>
						
					</td>
				</tr>
<?php 
///Add TestLabs Images//
					$imagesHtml=getTestImages($test_labs_id,$sectionImageFrom="TestLabs",$patient_id);
					if($imagesHtml!=""){
						echo('<table cellspacing="0" cellpadding="0"  style="width:100%">
							 	'.$imagesHtml.' 
							</table>');
					} 
					$imagesHtml="";
					//End TestLabs Images
if($elem_physician){?>
			<tr>
				<td class="text_lable" valign="middle">Interpreted By:</td>
				<td  class="text_value">
					<?php 
						if($elem_physician!=""){
							$getNameQry = imw_query("SELECT CONCAT_WS(', ', lname, fname) as phyName FROM users WHERE id = '".$elem_physician."'");	
							$getNameRow = imw_fetch_assoc($getNameQry);
							$InterpretedBy = str_replace(", ,"," ",$getNameRow['phyName']);
							print($InterpretedBy);
						}
					?>
				</td>
			</tr>
<?php }?>
		</table>
	</td>
</tr>
</table>
<?php 
}//end of cellcount while
}
?>