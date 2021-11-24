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

File: test_cellcount_print.php
Purpose: This file provides print version of Cell count test.
Access Type : Include file
*/
  
$patient_id = $_SESSION['patient'];
	
	if(is_array($_REQUEST["printTestRadioCellCount"]) && count($_REQUEST["printTestRadioCellCount"])>0){
		$sql = "SELECT * FROM test_cellcnt WHERE patientId = '$patient_id' AND test_cellcnt_id in(".implode(",",$_REQUEST["printTestRadioCellCount"]).")";		
	}else if($form_id){
		$sql = "SELECT * FROM test_cellcnt WHERE patientId = '$patient_id' AND formId = '$form_id'";		
	}
	$rowCellCount= imw_query($sql);
	if(imw_num_rows($rowCellCount)>0){
		while($row=imw_fetch_array($rowCellCount)){
		$test_cellcnt_id= $row["test_cellcnt_id"] ;		
		$elem_examDate =FormatDate_show($row["examDate"]);
		$elem_examTime =  $row["examTime"] ;		
		$elem_cellcntEye = $row["test_cellcnt_eye"];
		$elem_performedBy =  $row["performedBy"];
		$elem_ptUnderstanding = $row["ptUnderstanding"];
		$elem_diagnosis = $row["diagnosis"];
		$elem_diagnosisOther = $row["diagnosisOther"];
		$elem_reliabilityOd = $row["reliabilityOd"];
		$elem_reliabilityOs = $row["reliabilityOs"];		
		$elem_descOd = stripslashes($row["descOd"]);
		$elem_descOs = stripslashes($row["descOs"]);
		$elem_stable = $row["stable"];
		$elem_fuApa = $row["fuApa"];
		$elem_ptInformed = $row["ptInformed"];				
		$elem_physician = $row["phyName"];		
		$elem_tech2InformPt=$row["tech2InformPt"];		
		$elem_techComments = stripslashes($row["techComments"]);
		$elem_informedPtNv = $row["ptInformedNv"];
		$elem_contiMeds = $row["contiMeds"];
		$encounterId = $row["encounter_id"];
		$elem_opidTestOrdered = $row["ordrby"];
		if(($row["ordrdt"] != "" && $row["ordrdt"] != "0000-00-00")){		
			$elem_opidTestOrderedDate = $row["ordrdt"];
		}
		$elem_numOd = $row["numod"];
		$elem_cdOd = $row["cdod"];
		$elem_avgOd = $row["avgod"];
		$elem_sdOd = $row["sdod"];
		$elem_cvOd = $row["cvod"];
		$elem_mxOd = $row["mxod"];
		$elem_mnOd = $row["mnod"];
		$elem_6aOd = $row["e6aod"];
		$elem_cctOd = $row["cctod"];
		$elem_ppOd = $row["ppod"];

		$elem_numOs = $row["numos"];
		$elem_cdOs = $row["cdos"];
		$elem_avgOs = $row["avgos"];
		$elem_sdOs = $row["sdos"];
		$elem_cvOs = $row["cvos"];
		$elem_mxOs = $row["mxos"];
		$elem_mnOs = $row["mnos"];
		$elem_6aOs = $row["e6aos"];
		$elem_cctOs = $row["cctos"];
		$elem_ppOs = $row["ppos"];

?>	
<table style="width:700px;" class="paddingTop" border="0" cellspacing="0" cellpadding="0" >
	<tr>
		<td  valign="middle" class="tb_heading" style="width:100%">Cell Count (<span class="text_value">Exam Date:&nbsp;<?php print $elem_examDate;?></span>)</td>
	</tr>
</table>							
							
<table  style="width:700px;"  border="0" cellspacing="0" cellpadding="0">
<?php if($elem_techComments!=""){?>
	<tr  valign="middle">
		<td  colspan="2" class="text_lable">Technician Comments:<?php echo $elem_techComments; ?></td>
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
	<tr  valign="middle">
		
		<td>
			<table style="width:700px;" border="0"  cellpadding="0" cellspacing="0" >
			
			<tr>
				<td>
				<table border="0" align="center" cellpadding="0" cellspacing="0">
					
					<tr valign="middle">
						<td colspan="2"  class="text_lable"><?php odLable();?></td>
					</tr>

					<tr>
						<td class="text_lable">NUM</td>
						<td class="text_value">
							<?php echo $elem_numOd;?>
						</td>
					</tr>

					<tr>
						<td class="text_lable">CD</td>
						<td class="text_value">
							<?php echo $elem_cdOd;?>
						</td>
					</tr>

					<tr>
						<td class="text_lable">AVG</td>
						<td class="text_value">
							<?php echo $elem_avgOd;?>
						</td>
					</tr>

					 <tr>
						<td class="text_lable">SD</td>
						<td class="text_value">
							<?php echo $elem_sdOd;?>
						</td>
					</tr>

					<tr>
						<td class="text_lable">CV</td>
						<td class="text_value">
							<?php echo $elem_cvOd;?>
						</td>
					</tr>

					<tr>
						<td class="text_lable">Max</td>
						<td class="text_value">
							<?php echo $elem_mxOd;?>
						</td>
					</tr>

					 <tr>
						<td class="text_lable">Min</td>
						<td class="text_value">
							<?php echo $elem_mnOd;?>
						</td>
					</tr>

					<tr>
						<td class="text_lable">6A</td>
						<td class="text_value">
							<?php echo $elem_6aOd;?>
						</td>
					</tr>

					<tr>
						<td class="text_lable">CCT</td>
						<td class="text_value">
							<?php echo $elem_cctOd;?>
						</td>
					</tr>
					<tr>																
						<td colspan="2" class="text_value">
						<span class="text_lable">Pleomorphism Present</span>
						 <?php echo $elem_ppOd;?>

						</td>
					</tr>

					<tr>
						<td class="text_lable">Comments</td>
						<td class="text_value" ><?php echo $elem_descOd;?></td>
					</tr>															
				</table>
			</td>
			
			<td>													
				<table  border="0"  cellpadding="0" cellspacing="0">
					
					<tr valign="middle">
						<td  class="text_lable"><?php osLable();?></td>
					</tr>
					 <tr>																
						<td class="text_value">
							<?php echo $elem_numOs;?>
						</td>
					</tr>
					
					<tr>																
						<td class="text_value">
							<?php echo $elem_cdOs;?>
						</td>
					</tr>
					
					<tr>																
						<td class="text_value">
							<?php echo $elem_avgOs;?>
						</td>
					</tr>
					
					 <tr>																
						<td class="text_value">
							<?php echo $elem_sdOs;?>
						</td>
					</tr>
					
					<tr>																
						<td class="text_value">
							<?php echo $elem_cvOs;?>
						</td>
					</tr>
					
					<tr>																
						<td class="text_value">
							<?php echo $elem_mxOs;?>
						</td>
					</tr>
					
					 <tr>																
						<td class="text_value">
							<?php echo $elem_mnOs;?>
						</td>
					</tr>
					
					<tr>																
						<td class="text_value">
							<?php echo $elem_6aOs;?>
						</td>
					</tr>
					
					<tr>																
						<td class="text_value">
							<?php echo $elem_cctOs;?>
						</td>
					</tr>
					<tr>																
						<td colspan="2" class="text_value">
							 <?php echo $elem_ppOs;?>
						</td>
					</tr>

					<tr>																
						<td class="text_value"><?php echo $elem_descOs;?></td>
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
			<table   border="0" cellspacing="0" cellpadding="0">
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
///Add CellCount Images//
					$imagesHtml=getTestImages($test_cellcnt_id,$sectionImageFrom="CellCount",$patient_id);
					if($imagesHtml!=""){
						echo('<table cellspacing="0" cellpadding="0"  style="width:100%">
							 	'.$imagesHtml.' 
							</table>');
					} 
					$imagesHtml="";
					//End CellCount Images
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