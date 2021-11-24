<?php
//===========FUNCTION TO GET PHYSICIAN INITIAL===========
function print_phyInitial($phyid){
	$phyname="";
	$getNameQry = imw_query("SELECT `fname`,`lname` FROM `users` WHERE id = '".$phyid."'");	
	if(imw_num_rows($getNameQry)>0){
		$getNameRow = imw_fetch_assoc($getNameQry);
		$phyFname 	= substr(trim($getNameRow['fname']),0,1);
		$phyLname 	= substr(trim($getNameRow['lname']),0,1);
		$phyname 	= strtoupper($phyFname.$phyLname);
	}
	return $phyname;
}

function getAllTestPdf($strAllTestIds){
	$qry = "SELECT file_type, file_path FROM ".constant("IMEDIC_SCAN_DB").".scans 
			WHERE patient_id = '".$_SESSION['patient']."' 
			AND test_id in (".$strAllTestIds.")
			AND scan_or_upload = 'upload'
			AND file_type = 'application/pdf'
			";
	$res = imw_query($qry);
	$strPDFdata = '';$arrPDFs = array();
	while($row = imw_fetch_assoc($res)){
		$scndirPath = '../main/uploaddir/'.$row['file_path'];
		$scn_dir_real_path = realpath($scndirPath);
		$arrPDFs[] = $scn_dir_real_path;
	}
	return $arrPDFs;
}


//$_REQUEST["printTestRadioCellCount"]
function print_cellcount($patient_id,$form_id,$req){	
	if(is_array($req) && count($req)>0){
		$sql = "SELECT * FROM test_cellcnt WHERE patientId = '$patient_id' AND test_cellcnt_id in(".implode(",",$req).")";	
	}else if($form_id){
		$sql = "SELECT * FROM test_cellcnt WHERE patientId = '$patient_id' AND formId = '$form_id'";		
	}
	$rowCellCount= imw_query($sql);
	if(imw_num_rows($rowCellCount)>0){
		while($row=imw_fetch_array($rowCellCount)){
				$test_cellcnt_id= $row["test_cellcnt_id"] ;		
				$elem_examDate =get_date_format($row["examDate"]);
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
		<table style="width:740px;" class="border" cellpadding="0" cellspacing="0">
			<tr>
				<td colspan="2" class="tb_heading" >Cell Count (<span class="text_value">Exam Date:&nbsp;<?php print $elem_examDate;?></span>)</td>
			</tr>

		<?php if($elem_techComments!=""){?>
			<tr  >
				<td  colspan="2" class="bdrbtm" style="width:740px;" ><b>Technician Comments:</b><?php echo $elem_techComments; ?></td>
			</tr>
		<?php }?>
			<tr>
				<?php 
				if($elem_performedBy){
				?>
				<td style="width:350px;" class="bdrbtm"><b>Performed By:</b>&nbsp;
				<?php 
					if($elem_performedBy!=""){
					   /* $getNameQry = imw_query("SELECT CONCAT_WS(', ', lname, fname) as phyName FROM users WHERE id = '".$elem_performedBy."'");	
						$getNameRow = imw_fetch_assoc($getNameQry);
						$PerformedBy = str_replace(", ,"," ",$getNameRow['phyName']);*/
						$PerformedBy = print_phyInitial($elem_performedBy);
						print($PerformedBy);
					}
				?>
				</td>
				<?php }?>
				<td style="width:350px;" class="bdrbtm"><b>Patient Understanding & Cooperation:</b>&nbsp;
					<?php echo ($elem_ptUnderstanding);?>
				</td>
			</tr>
			<?php
			if($elem_diagnosis=="Other"&&!empty($elem_diagnosisOther)){$elem_diagnosis=$elem_diagnosisOther;}
			?>
			<tr>
				<td style="width:700px;" class="bdrbtm" colspan="2"><?php if($elem_diagnosis!="--Select--"){
								echo("<b>Diagnosis:</b>&nbsp;".$elem_diagnosis);
						}?></td>
			</tr>
			<tr>
				<td class="bdrbtm text_lable" colspan="2">Physician Interpretation </td>
			</tr>
			<tr>    
				<td style="width:350px;" class="bdrbtm">Reliability:&nbsp;<?php odLable();?>&nbsp;
					 <?php echo ($elem_reliabilityOd);?>
				</td>
				<td style="width:350px;" class="bdrbtm"><?php osLable();?>&nbsp;
					 <?php echo ($elem_reliabilityOs);?>
				</td>
				
			</tr>
			<tr>		
				<td colspan="2" style="width:750px;">
					<table style="width:750px;" cellpadding="0" cellspacing="0">			
						<tr>
							<td style="width:150px;" class="text_lable bdrbtm">&nbsp;</td>
							<td style="width:285px;" class=" bdrbtm"><?php odLable();?></td>
							<td style="width:285px;" class=" bdrbtm"><?php osLable();?></td>
						</tr>
						<tr>
							<td style="width:150px;" class="text_lable bdrbtm">NUM</td>
							<td style="width:285px;" class=" bdrbtm"><?php echo $elem_numOd;?></td>
							<td style="width:285px;" class=" bdrbtm"><?php echo $elem_numOs;?></td>
						</tr> 
						 <tr>
							<td style="width:150px;" class="text_lable bdrbtm">CD</td>
							<td style="width:285px;" class=" bdrbtm"><?php echo $elem_cdOd;?></td>
							<td style="width:285px;" class=" bdrbtm"><?php echo $elem_cdOs;?></td>
						</tr> 
						 <tr>
							<td style="width:150px;" class="text_lable bdrbtm">AVG</td>
							<td style="width:285px;" class=" bdrbtm"><?php echo $elem_avgOd;?></td>
							<td style="width:285px;" class=" bdrbtm"><?php echo $elem_avgOs;?></td>
						</tr> 
						 <tr>
							<td style="width:150px;" class="text_lable bdrbtm">SD</td>
							<td style="width:285px;" class=" bdrbtm"><?php echo $elem_sdOd;?></td>
							<td style="width:285px;" class=" bdrbtm"><?php echo $elem_sdOs;?></td>
						</tr> 
						 <tr>
							<td style="width:150px;" class="text_lable bdrbtm">CV</td>
							<td style="width:285px;" class=" bdrbtm"><?php echo $elem_cvOd;?></td>
							<td style="width:285px;" class=" bdrbtm"><?php echo $elem_cvOs;?></td>
						</tr> 
						 <tr>
							<td style="width:150px;" class="text_lable bdrbtm">Max</td>
							<td style="width:285px;" class=" bdrbtm"><?php echo $elem_mxOd;?></td>
							<td style="width:285px;" class=" bdrbtm"><?php echo $elem_mxOs;?></td>
						</tr> 
						 <tr>
							<td style="width:150px;" class="text_lable bdrbtm">Min</td>
							<td style="width:285px;" class=" bdrbtm"><?php echo $elem_mnOd;?></td>
							<td style="width:285px;" class=" bdrbtm"><?php echo $elem_mnOs;?></td>
						</tr> 
						 <tr>
							<td style="width:150px;" class="text_lable bdrbtm">6A</td>
							<td style="width:285px;" class=" bdrbtm"><?php echo $elem_6aOd;?></td>
							<td style="width:285px;" class=" bdrbtm"><?php echo $elem_6aOs;?></td>
						</tr> 
						 <tr>
							<td style="width:150px;" class="text_lable bdrbtm">CCT</td>
							<td style="width:285px;" class=" bdrbtm"><?php echo $elem_cctOd;?></td>
							<td style="width:285px;" class=" bdrbtm"><?php echo $elem_cctOs;?></td>
						</tr> 
						 <tr>
							<td style="width:150px;" class="text_lable bdrbtm">Pleomorphism Present</td>
							<td style="width:285px;" class=" bdrbtm"><?php echo $elem_ppOd;?></td>
							<td style="width:285px;" class=" bdrbtm"><?php echo $elem_ppOs;?></td>
						</tr> 
						<tr>
							<td style="width:150px;" class="bdrbtm">Comments:</td>
							<td colspan="2" style="width:580px;" class="bdrbtm"><?php echo $elem_descOd; ?></td>
						</tr>  
					</table>													
				</td>
			</tr>
			<tr>
				<td colspan="2" class="text_lable bdrbtm" >Treatment/Prognosis:</td>
			</tr>
			<tr>
				<td style="width:350px;" class="bdrbtm">
					<?php echo ($elem_stable == "1") ? "Stable" : "" ;?>
					<?php echo ($elem_contiMeds == "1") ? "Continue Meds" : "" ;?>	
					 <?php echo ($elem_fuApa == "1") ? "F/U APA" : "" ;?>
				</td>
				<td style="width:350px;" class="bdrbtm">	
					<?php echo ($elem_tech2InformPt == "1") ? "Tech to Inform Pt." : "" ;?>
					 <?php echo ($elem_ptInformed == "1") ? "Pt informed of results" : "" ;?>
					 <?php echo ($elem_informedPtNv == "1") ? "Inform Pt result next visit" : "" ;?>
				</td>			
			</tr>	
									
		<?php 
		///Add CellCount Images//
							$imagesHtml=getTestImages($test_cellcnt_id,$sectionImageFrom="CellCount",$patient_id);
							if($imagesHtml!=""){
								echo('<tr><td colspan="2" style="width:700px;"><table style="width:700px;">
										'.$imagesHtml.' 
									</table></td></tr>');
							} 
							$imagesHtml="";
							//End CellCount Images
		if($elem_physician){?>
					<tr>
						<td colspan="2"  class="bdrbtm" ><b>Interpreted By:</b>&nbsp;
							<?php 
								if($elem_physician!=""){
									/*$getNameQry = imw_query("SELECT CONCAT_WS(', ', lname, fname) as phyName FROM users WHERE id = '".$elem_physician."'");	
									$getNameRow = imw_fetch_assoc($getNameQry);
									$InterpretedBy = str_replace(", ,"," ",$getNameRow['phyName']);*/
									$InterpretedBy = print_phyInitial($elem_physician);
									print($InterpretedBy);
								}
							?>
						</td>
					</tr>
					
		<?php }?>
		</table>
		<?php 
		}

	}
}

function print_lab($patient_id,$form_id,$req){		
	if(is_array($req) && count($req)>0){
		$sql = "SELECT * FROM test_labs WHERE patientId = '$patient_id' AND test_labs_id in(".implode(",",$req).")";	
	}else if($form_id){
		$sql = "SELECT * FROM test_labs WHERE patientId = '$patient_id' AND formId = '$form_id'";		
	}
	$rowCellCount= imw_query($sql);
		if(imw_num_rows($rowCellCount)>0){
			while($row=imw_fetch_array($rowCellCount)){
				$test_labs_id=$row["test_labs_id"];
				$elem_examDate = ($topo_mode != "new") ? get_date_format($row["examDate"]) : $elem_examDate;
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
					$elem_opidTestOrderedDate = get_date_format($row["ordrdt"]);
				}

			

		?>	
		<table >
			<tr>
				<td   class="tb_heading" >Laboratories (<span class="text_value">Exam Date:&nbsp;<?php print $elem_examDate;?></span>)</td>
			</tr>
		</table>					
		<table  >
		<?php if($elem_testLabsName!=""){?>
			<tr  >
				<td  colspan="2" class="text_lable"><?php echo $elem_testLabsName."&nbsp;". $elem_topoMeterEye; ?></td>
			</tr>
		<?php }?>
		<?php if($elem_techComments!=""){?>
			<tr  >
				<td  colspan="2" ><b>Technician Comments:</b><?php echo $elem_techComments; ?></td>
			</tr>
		<?php }?>
			<tr>
				
				<td colspan="2">
					<table >
						<tr>
							<?php 
							if($elem_performedBy){
							?>
							<td class="text_lable">Performed By:&nbsp;</td>
							<td >
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
							<td >
								<?php echo ($elem_ptUnderstanding);?>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td class="text_lable" colspan="2"><?php 
					if($elem_diagnosis=="Other"&&!empty($elem_diagnosisOther)){$elem_diagnosis=$elem_diagnosisOther;}
					if($elem_diagnosis && $elem_diagnosis!="--Select--"){
								echo("Diagnosis:&nbsp;".$elem_diagnosis);
						}?></td>
			</tr>
			<tr>
				<td  class="text_lable" colspan="2">Physician Interpretation </td>
			</tr>
			<tr  >
				<td colspan="2">
					<table >
						<tr>
							<td  class="text_lable">Reliability</td>
							<td   ><?php odLable();?>
								 <?php echo ($elem_reliabilityOd);?>
							</td>
							<td  ><?php osLable();?>

								 <?php echo ($elem_reliabilityOs);?>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				
				<td>
					<table  >
					
					<tr>
						<td style="width:50%;">
						<table >
							
							<tr >
								<td colspan="2"  class="text_lable"><?php odLable();?></td>
							</tr>

							<tr>
								<td class="text_lable">Comments</td>
								<td >
									<?php echo $elem_descOd;?>
								</td>
							</tr>
							<tr>
								<td class="text_lable">Interpretation</td>
								<td >
									<?php echo $elem_inter_pret_od;?>
								</td>
							</tr>
								
						</table>
					</td>
					
					<td style="width:50%;">													
						<table  >
							
							<tr >
								<td  class="text_lable"><?php osLable();?></td>
							</tr>
							 <tr>																
								<td >
									<?php echo $elem_descOs;?>
								</td>
							</tr>
							<tr>																
								<td >
									<?php echo $elem_inter_pret_os;?>
								</td>
							</tr>
							
							
						</table>
					</td>
					</tr>
				</table>													
			</td>
			</tr>
			<tr  >
				<td  class="text_lable" colspan="2">Treatment/Prognosis:</td>
			</tr>
			<tr>
				<td colspan="2">
					<table   >
						<tr>
							
							<td colspan="2">
								
									<table  >
										<tr  >
											<td >
												<?php echo ($elem_stable == "1") ? "Stable" : "" ;?>
												<?php echo ($elem_contiMeds == "1") ? "Continue Meds" : "" ;?>	
												 <?php echo ($elem_fuApa == "1") ? "F/U APA" : "" ;?>
											</td>
											<td >	
												<?php echo ($elem_tech2InformPt == "1") ? "Tech to Inform Pt." : "" ;?>
												 <?php echo ($elem_ptInformed == "1") ? "Pt informed of results" : "" ;?>
												 <?php echo ($elem_informedPtNv == "1") ? "Inform Pt result next visit" : "" ;?>
											</td>			
										</tr>	
									</table>
								
							</td>
						</tr>
						<tr>
						<td colspan="2">	
						<?php 
						///Add TestLabs Images//
						$imagesHtml=getTestImages($test_labs_id,$sectionImageFrom="TestLabs",$patient_id);
						if($imagesHtml!=""){
							echo('<table >
									'.$imagesHtml.' 
								</table>');
						} 
						$imagesHtml="";
						//End TestLabs Images
						?>
						</td>
						</tr>
		<?php
		if($elem_physician){?>
					<tr>
						<td class="text_lable" >Interpreted By:</td>
						<td  >
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

		}
	//end of cellcount while
	}
}

//TEST Other
//$_REQUEST["printTestRadioOther"]
function print_testother($patient_id,$form_id,$req){		
	if(is_array($req) && count($req)>0){
		$sql = "SELECT * FROM test_other WHERE patientId = '$patient_id' AND test_other_id in(".implode(",",$req).")";	
	}else if($form_id){
		$sql = "SELECT * FROM test_other WHERE patientId = '$patient_id' AND formId = '$form_id'";		
	}
	$rowCellCount= imw_query($sql);
		if(imw_num_rows($rowCellCount)>0){
				while($row=imw_fetch_array($rowCellCount)){
				$test_other_id=$row["test_other_id"];
				$elem_examDate = ($topo_mode != "new") ? get_date_format($row["examDate"]) : $elem_examDate;
				$elem_examTime = ($topo_mode != "new") ? $row["examTime"] : $elem_examTime ;
				$elem_testOtherName = $row["test_other"];
				$elem_topoMeterEye = $row["test_other_eye"];
				$elem_performedBy = $row["performedBy"];
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
				$elem_physician = $row["phyName"] ;		
				$elem_tech2InformPt=$row["tech2InformPt"];		
				$elem_techComments = stripslashes($row["techComments"]);
				$elem_informedPtNv = $row["ptInformedNv"];
				$elem_contiMeds = $row["contiMeds"];
				$encounterId = $row["encounter_id"];
				$elem_opidTestOrdered = $row["ordrby"];
				if(($row["ordrdt"] != "" && $row["ordrdt"] != "0000-00-00")){		
					$elem_opidTestOrderedDate = get_date_format($row["ordrdt"]);
				}

		?>	
		<table style="width:750px;" class="border" cellpadding="0" cellspacing="0" >
			<tr>
				<td colspan="3" class="tb_heading" >Test (<span class="text_value">Exam Date:&nbsp;<?php print $elem_examDate;?></span>)</td>
			</tr>
		<?php if($elem_testOtherName!=""){?>
			<tr  >
				<td colspan="3" style="width:700px;" class="bdrbtm text_lable"><?php echo $elem_testOtherName."&nbsp;". $elem_topoMeterEye; ?></td>
			</tr>
		<?php }?>
		<?php if($elem_techComments!=""){?>
			<tr  >
				<td colspan="3" style="width:700px;" class="bdrbtm"><b>Technician Comments:&nbsp;</b><?php echo $elem_techComments; ?></td>
			</tr>
		<?php }?>
			<tr>
				<?php 
				if($elem_performedBy){
				?>
				<td colspan="2" style="width:400px;" class="bdrbtm"><b>Performed By:&nbsp;</b>
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
				<td style="width:300px;" class="bdrbtm"><b>Patient Understanding & Cooperation:&nbsp;</b>
					<?php echo ($elem_ptUnderstanding);?>
				</td>
			</tr>
					
			<tr>
				<td style="width:700px;" class="bdrbtm text_lable" colspan="3"><?php if($elem_diagnosis!="--Select--"){
								echo("Diagnosis:&nbsp;".$elem_diagnosis);
						}?></td>
			</tr>
			<tr>
				<td style="width:700px;" class="bdrbtm text_lable" colspan="3">Physician Interpretation </td>
			</tr>
			<tr>
				<td style="width:100px;" class="text_label bdrbtm">Reliability</td>
				<td style="width:300px;" class="bdrbtm">&nbsp;<?php odLable();?>
					 &nbsp;<?php echo ($elem_reliabilityOd);?>
				</td>
				<td style="width:300px;" class="bdrbtm"><?php osLable();?>&nbsp;

					 <?php echo ($elem_reliabilityOs);?>
				</td>
			</tr>
			<tr>
				<td style="width:100px;" class="bdrbtm">&nbsp;</td>
				<td style="width:300px;" class="bdrbtm text_lable"><?php odLable();?></td>
				<td style="width:300px;" class="bdrbtm text_lable"><?php odLable();?></td>
			</tr>
			<tr>
				<td style="width:100px;" class="bdrbtm text_label">Comments:</td>
				<td style="width:300px;" class="bdrbtm"><?php echo $elem_descOd;?></td>
				<td style="width:300px;" class="bdrbtm"><?php echo $elem_descOs;?></td>
			</tr>
			 <tr>
				<td style="width:100px;" class="bdrbtm text_label">Interpretation:</td>
				<td style="width:300px;" class="bdrbtm"><?php echo $elem_inter_pret_od;?></td>
				<td style="width:300px;" class="bdrbtm"><?php echo $elem_inter_pret_os;?></td>
			</tr>
			
			<tr>
				<td  class="text_lable" colspan="3">Treatment/Prognosis:</td>
			</tr>
			<tr  >
				<td colspan="2" class="bdrbtm" style="width:400px;" >
					<?php echo ($elem_stable == "1") ? "Stable" : "" ;?>
					<?php echo ($elem_contiMeds == "1") ? "Continue Meds" : "" ;?>	
					 <?php echo ($elem_fuApa == "1") ? "F/U APA" : "" ;?>
				</td>
				 <td class="bdrbtm" style="width:300px;" >	
					<?php echo ($elem_tech2InformPt == "1") ? "Tech to Inform Pt." : "" ;?>
					 <?php echo ($elem_ptInformed == "1") ? "Pt informed of results" : "" ;?>
					 <?php echo ($elem_informedPtNv == "1") ? "Inform Pt result next visit" : "" ;?>
				</td>			
			</tr>	
			<tr>
									
						<td colspan="3" style="width:700px;" class="bdrbtm">
				<?php 
						
						///Add TestLabs Images//
						$imagesHtml=getTestImages($test_other_id,$sectionImageFrom="TestOther",$patient_id);
						if($imagesHtml!=""){
							echo('<table >
									'.$imagesHtml.' 
								</table>');
						} 
						$imagesHtml="";
						//End TestLabs Images
						
				?>	
					 </td>
				</tr>
		<?php				
		if($elem_physician){?>
					<tr>
						<td style="width:700px;" colspan="3" class="text_lable bdrbtm" >Interpreted By:&nbsp;
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
		<?php 
		}//end of other while
	}
}

function print_testotherTemplate($patient_id,$form_id,$req){		
		if(is_array($req) && count($req)>0){
			$sql = "SELECT * FROM test_other WHERE patientId = '$patient_id' AND test_other_id in(".implode(",",$req).") and test_template_id!='0'";	
		}else if($form_id){
			$sql = "SELECT * FROM test_other WHERE patientId = '$patient_id' AND formId = '$form_id' and test_template_id!='0'";		
		}
		$rowCellCount= imw_query($sql);
		if(imw_num_rows($rowCellCount)>0){
			$arr_test_name=array();
			$qry_tests_name="SELECT id,temp_name FROM tests_name";
			$res_tests_name=imw_query($qry_tests_name);
			while($row_tests_name=imw_fetch_assoc($res_tests_name)){
				$arr_test_name[$row_tests_name['id']]=$row_tests_name['temp_name'];
			}
			while($row=imw_fetch_array($rowCellCount)){
			$test_template_id=$row["test_template_id"];
			$test_other_id=$row["test_other_id"];
			$elem_examDate = ($topo_mode != "new") ? get_date_format($row["examDate"]) : $elem_examDate;
			$elem_examTime = ($topo_mode != "new") ? $row["examTime"] : $elem_examTime ;
			$elem_testOtherName = $row["test_other"];
			$elem_topoMeterEye = $row["test_other_eye"];
			$elem_performedBy = $row["performedBy"];
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
			$elem_physician = $row["phyName"] ;		
			$elem_tech2InformPt=$row["tech2InformPt"];		
			$elem_techComments = stripslashes($row["techComments"]);
			$elem_informedPtNv = $row["ptInformedNv"];
			$elem_contiMeds = $row["contiMeds"];
			$encounterId = $row["encounter_id"];
			$elem_opidTestOrdered = $row["ordrby"];
			if(($row["ordrdt"] != "" && $row["ordrdt"] != "0000-00-00")){		
				$elem_opidTestOrderedDate = get_date_format($row["ordrdt"]);
			}
	
	?>	
	<table style="width:750px;" class="border" cellpadding="0" cellspacing="0" >
		<tr>
			<td colspan="3" class="tb_heading" >Test (<span class="text_value">Exam Date:&nbsp;<?php print $elem_examDate;?></span>)</td>
		</tr>
	<?php if($elem_testOtherName!=""){?>
		<tr  >
			<td colspan="3" style="width:700px;" class="bdrbtm text_lable"><?php echo $arr_test_name[$test_template_id]."&nbsp;". $elem_topoMeterEye; ?></td>
		</tr>
	<?php }?>
	<?php if($elem_techComments!=""){?>
		<tr  >
			<td colspan="3" style="width:700px;" class="bdrbtm"><b>Technician Comments:&nbsp;</b><?php echo $elem_techComments; ?></td>
		</tr>
	<?php }?>
		<tr>
			<?php 
			if($elem_performedBy){
			?>
			<td colspan="2" style="width:400px;" class="bdrbtm"><b>Performed By:&nbsp;</b>
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
			<td style="width:300px;" class="bdrbtm"><b>Patient Understanding & Cooperation:&nbsp;</b>
				<?php echo ($elem_ptUnderstanding);?>
			</td>
		</tr>
				
		<tr>
			<td style="width:700px;" class="bdrbtm text_lable" colspan="3"><?php if($elem_diagnosis!="--Select--"){
							echo("Diagnosis:&nbsp;".$elem_diagnosis);
					}?></td>
		</tr>
		<tr>
			<td style="width:700px;" class="bdrbtm text_lable" colspan="3">Physician Interpretation </td>
		</tr>
		<tr>
			<td style="width:100px;" class="text_label bdrbtm">Reliability</td>
			<td style="width:300px;" class="bdrbtm">&nbsp;<?php odLable();?>
				 &nbsp;<?php echo ($elem_reliabilityOd);?>
			</td>
			<td style="width:300px;" class="bdrbtm"><?php osLable();?>&nbsp;
	
				 <?php echo ($elem_reliabilityOs);?>
			</td>
		</tr>
		<tr>
			<td style="width:100px;" class="bdrbtm">&nbsp;</td>
			<td style="width:300px;" class="bdrbtm text_lable"><?php odLable();?></td>
			<td style="width:300px;" class="bdrbtm text_lable"><?php odLable();?></td>
		</tr>
		<tr>
			<td style="width:100px;" class="bdrbtm text_label">Comments:</td>
			<td style="width:300px;" class="bdrbtm"><?php echo $elem_descOd;?></td>
			<td style="width:300px;" class="bdrbtm"><?php echo $elem_descOs;?></td>
		</tr>
		 <tr>
			<td style="width:100px;" class="bdrbtm text_label">Interpretation:</td>
			<td style="width:300px;" class="bdrbtm"><?php echo $elem_inter_pret_od;?></td>
			<td style="width:300px;" class="bdrbtm"><?php echo $elem_inter_pret_os;?></td>
		</tr>
		
		<tr>
			<td  class="text_lable" colspan="3">Treatment/Prognosis:</td>
		</tr>
		<tr  >
			<td colspan="2" class="bdrbtm" style="width:400px;" >
				<?php echo ($elem_stable == "1") ? "Stable" : "" ;?>
				<?php echo ($elem_contiMeds == "1") ? "Continue Meds" : "" ;?>	
				 <?php echo ($elem_fuApa == "1") ? "F/U APA" : "" ;?>
			</td>
			 <td class="bdrbtm" style="width:300px;" >	
				<?php echo ($elem_tech2InformPt == "1") ? "Tech to Inform Pt." : "" ;?>
				 <?php echo ($elem_ptInformed == "1") ? "Pt informed of results" : "" ;?>
				 <?php echo ($elem_informedPtNv == "1") ? "Inform Pt result next visit" : "" ;?>
			</td>			
		</tr>	
		<tr>
								
					<td colspan="3" style="width:700px;" class="bdrbtm">
			<?php 
					
					///Add TestLabs Images//
					$imagesHtml=getTestImages($test_other_id,$sectionImageFrom=$elem_testOtherName,$patient_id);
					if($imagesHtml!=""){
						echo('<table >
								'.$imagesHtml.' 
							</table>');
					} 
					$imagesHtml="";
					//End TestLabs Images
					
			?>	
				 </td>
			</tr>
	<?php				
	if($elem_physician){?>
				<tr>
					<td style="width:700px;" colspan="3" class="text_lable bdrbtm" >Interpreted By:&nbsp;
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
	<?php 
	}//end of other while
	}
}


function getTestImages($testtid,$sectionImageFrom,$patient_id){
	global $ChartNoteImagesString;
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
			
			if($s_imagename && ($s_file_type!='application/pdf'  && $s_file_type!="application/octet-stream" && $s_file_type!="text/xml"  && $s_file_type!="")) { //&& $s_file_type!='image/png'
				$scndirPath = '../main/uploaddir'.$s_imagename;
				$scn_dir_real_path = realpath($scndirPath);
				$scn_img_name = substr($s_imagename,strrpos($s_imagename,'/')+1);	
				//copy($scn_dir_real_path,'../common/new_html2pdf/'.$scn_img_name);
				$scndirPath = $scn_img_name;
				if(file_exists($scn_dir_real_path)){
					if(stristr($scn_dir_real_path,".tif")) { //CONVERT TIF TO JPG
						$scn_dir_old_path	= str_ireplace("\\","/",$scn_dir_real_path);
						$scn_dir_real_path 	= str_ireplace(".tiff",".jpg",$scn_dir_real_path);
						$scn_dir_real_path 	= str_ireplace(".tif",".jpg",$scn_dir_real_path);
						$scn_dir_new_path 	= str_ireplace("\\","/",$scn_dir_real_path);
						exec("convert ".$scn_dir_old_path." ".$scn_dir_new_path);
					}
					if(stristr($scn_dir_real_path,".PNG")){
						$scn_dir_old_path	= str_ireplace("\\","/",$scn_dir_real_path);
						$scn_dir_new_jpg_path 	= str_ireplace("\\","/",str_ireplace(".PNG",".jpg",$scn_dir_real_path));	
						//Convert PNG Image To JPG Format. As the Reason PNG is not working in PRINT PDF. 
						png2jpg($scn_dir_old_path,$scn_dir_new_jpg_path);
						if(file_exists($scn_dir_new_jpg_path)){							
							$scn_dir_real_path=$scn_dir_new_jpg_path;
						}
					}
					$scnfileSize = getimagesize($scn_dir_real_path);
					if($scnfileSize[0]>700 ){
						$scnimageWidth2 = ManageData::imageResize($scnfileSize[0],$scnfileSize[1],700);
						$scnMultiImage .= '<tr><td style="width:700px;text-align:center" ><img style="cursor:pointer" src="'.$scn_dir_real_path.'" alt="patient Image" '.$scnimageWidth2.'></td></tr>';
					}
					else{
						$scnMultiImage .= '<tr><td style="width:700px;text-align:center"><img style="cursor:pointer" src="'.$scn_dir_real_path.'" alt="patient Image"></td></tr>';
					}
						if(strip_tags($sqlScanRow["testing_docscan"])!=""){$scnMultiImage .= '<tr>
							<td ><b>Comments:</b>&nbsp;'.strip_tags($sqlScanRow["testing_docscan"]).'</td>
						</tr>';
						}
						if(strip_tags($sqlScanRow["multi_doc_upload_comment"])!=""){$scnMultiImage .= '<tr>
							<td ><b>Comments:</b>&nbsp;'.strip_tags($sqlScanRow["multi_doc_upload_comment"]).'</td>
						</tr>';
						}
					$ChartNoteImagesString[]=$scn_dir_real_path;		
				}
			
			}
		}
		
	}
	return $scnMultiImage;
}

//VF -GL----------
//$_REQUEST["printTestRadioVF"]
function print_vf_gl($patient_id,$form_id,$req){
	if(is_array($req) && count($req)>0){
		$sqlVFFormQry = imw_query("SELECT * FROM vf_gl WHERE patientId = '".$patient_id."' AND vf_gl_id in (".implode(",",$req).")");
	}else if($form_id!=""){
		$sqlVFFormQry = imw_query("SELECT * FROM vf_gl WHERE patientId = '".$patient_id."' AND formId = '$form_id' AND purged='0' ");
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
			if($orderByName){
				$orderByName = "<span class=\"text_lable\">Order By:&nbsp;</span>".$orderByName; 
				if($ordrdt && $ordrdt!="0000-00-00") {$orderByName .= "&nbsp;&nbsp;".get_date_format($ordrdt);}
			}
		?>
		
				<table style="width:755px;" class="border" cellpadding="0" cellspacing="0">
					<tr>
						<td colspan="2" class="tb_heading" >VF-GL (<span class="text_value">Exam Date:&nbsp;<?php print get_date_format($examDate);?></span>)&nbsp;<?php echo $orderByName;?></td>
					</tr>
				
					<tr>
						<td colspan="2" style="width:700px;" class="bdrbtm">
						<b>VF-GL</b>&nbsp;<?php echo($vf_gl_eye."&nbsp;".$elem_gla_mac."&nbsp;".$gla_mac_other_od."&nbsp;".$gla_mac_os."&nbsp;".$gla_mac_other_os); ?>
						</td>
					</tr>
				
					<?php
					if($namePerformer || $ptUnderstanding || $diagnosis )
					{
						?>
					
					<?php if($techComments!=""){?>
						<tr>
							<td colspan="2" style="width:700px;" class="bdrbtm"><b>Technician Comments:</b>&nbsp;<?php echo($techComments);?></td>
						</tr>
					<?php } ?>
						<tr>
							<td  colspan="2" style="width:700px;" class="bdrbtm">								
								<span class="text_lable">Performed By:&nbsp;</span> <?php echo $namePerformer; ?>
								&nbsp;&nbsp;
								<span class="text_lable">Patient Understanding & Cooperation</span>:&nbsp;<?php echo $ptUnderstanding; ?>
								&nbsp;&nbsp;
							</td>
						</tr>
						
						<?php
							$strDia = "";
							if(!empty($diagnosis) && $diagnosis!="--Select--"){
								if(!empty($diagnosisOther) && trim($diagnosis)=="Other"){ $diagnosis = $diagnosisOther;  }
								$strDia .= odLable(1)." ".$diagnosis."&nbsp;&nbsp;&nbsp;&nbsp;";
							}
							if(!empty($diagnosis_os) && $diagnosis_os!="--Select--"){
								if(!empty($diagnosisOther_os) && trim($diagnosis_os)=="Other"){ $diagnosis_os = $diagnosisOther_os;  }
								$strDia .= osLable(1)." ".$diagnosis_os."";
							}
							
							if(!empty($strDia)){
								echo "
									<tr>
										<td  colspan=\"2\" style=\"width:700px;\" class=\"bdrbtm\">
											<span class=\"text_lable\">Diagnosis:&nbsp;</span>". $strDia."											
										</td>
									</tr>
									";
							}
						?>
					
					<?php
					}
					if($reliabilityOd || $reliabilityOs)
					{
					?>
                    <tr>
                    	<td colspan="2" style="width:700px;">
					<table style="width:740px;" cellpadding="0" cellspacing="0">
						<tr>
							<td class="text_lable bdrbtm" style="width:180px;">Physician Interpretation:&nbsp;</td>							
							<td class="text_lable bdrbtm" style="width:80px;">Reliability</td>
							<td class="text_lable bdrbtm" style="width:50px;"><?php odLable();?></td>
							<td style="width:100px;" class="bdrbtm"><?php echo $reliabilityOd; ?></td>
							<td class="text_lable bdrbtm" style="width:50px;"><?php osLable(); ?></td>
							<td style="width:100px;" class="bdrbtm"><?php echo $reliabilityOs; ?></td>
						</tr>
					</table>							
                    </td>
                    </tr>
					<?php
					}
					// OD DATA
					$odData ="";
					if(!empty($mdOd)){ $odData .="<b>MD</b> ".$mdOd." dB&nbsp;&nbsp;";   }
					if(!empty($psdOd)){ $odData .="<b>PSD</b> ".$psdOd." dB&nbsp;&nbsp;";   }
					if(!empty($vfiOd)){ $odData .="<b>VFI</b> ".$vfiOd." %";   }
					if(!empty($odData)){  $odData.="<br/>"; }
					
					if(!empty($details_high_od)){
						$odData.="<b>Details:</b><br/>";
						$odData.=rtrim($details_high_od,", ")."<br/>";
					}
					
					if(strpos($poor_study_od,"Poor Study")!==false){
						$odData.="<b>Poor Study:</b>&nbsp;&nbsp;";
						if(!empty($poor_study_od_desc)){ $odData.="".$poor_study_od_desc;  }
						if(!empty($poor_study_od_desc_other)){ $odData.="".$poor_study_od_desc_other;  }						
						$odData.="<br/>";
					}
					
					if(strpos($intratest_fluctuation_od,"Intratest Fluctuation")!==false) {  
						$odData.= "Intratest Fluctuation<br/>"; 
					}
					
					if(strpos($artifact_od,"Artifact")!==false) {  $odData.= "Artifact<br/>"; }
					
					if(!empty($details_lids_od)){
						$odData.="<b>Details:</b><br/>";
						$odData.=rtrim($details_lids_od,", ")."<br/>";
					}
					
					if(strpos($normal_od,"Normal / Full")!==false) {  
						$odData.= "Normal / Full<br/>";
					}
					
					if(strpos($nonspecific_od,"Nonspecific Details")!==false) {  $odData.= "Nonspecific Details<br/>"; }
					
					if(!empty($nasal_step_od)) {  $odData.= "<b>Nasal Step</b> ".rtrim($nasal_step_od,", ")."<br/>"; }
					if(!empty($arcuate_od)) {  $odData.= "<b>Arcuate</b> ".rtrim($arcuate_od,", ")."<br/>"; }
					if(!empty($hemifield_od)) {  $odData.= "<b>Hemifield</b> ".rtrim($hemifield_od,", ")."<br/>"; }
					if(!empty($paracentral_od)) {  $odData.= "<b>Paracentral</b> ".rtrim($paracentral_od,", ")."<br/>"; }
					if(!empty($into_fixation_od)) {  $odData.= "<b>Into Fixation</b> ".rtrim($into_fixation_od,", ")."<br/>"; }
					if(!empty($central_island_od)) {  $odData.= "<b>Central Island</b> Remaining ".$central_island_od." degrees"."<br/>"; }
					if(strpos($enlarged_blind_spot_od,"Enlarged Blind Spot")!==false) {  $odData.="Enlarged Blind Spot"."<br/>"; }
					if(strpos($cecocentral_scotone_od,"Cecocentral Scotone")!==false) {  $odData.= "Cecocentral Scotone"."<br/>"; }
					if(strpos($central_scotoma_od,"Central Scotoma")!==false) {  $odData.= "Central Scotoma"."<br/>"; }
					
					
					// OS DATA
					$osData = "";
					if(!empty($mdOs)){ $osData .="<b>MD</b> ".$mdOs." dB&nbsp;&nbsp;";   }
					if(!empty($psdOs)){ $osData .="<b>PSD</b> ".$psdOs." dB&nbsp;&nbsp;";   }
					if(!empty($vfiOs)){ $osData .="<b>VFI</b> ".$vfiOs." %";   }
					if(!empty($osData)){  $osData.="<br/>"; }
					
					if(!empty($details_high_os)){
						$osData.="<b>Details:</b><br/>";
						$osData.=rtrim($details_high_os,", ")."<br/>";
					}
					
					if(strpos($poor_study_os,"Poor Study")!==false){
						$osData.="<b>Poor Study:</b>&nbsp;&nbsp;";
						if(!empty($poor_study_os_desc)){ $osData.="".$poor_study_os_desc;  }
						if(!empty($poor_study_os_desc_other)){ $osData.="".$poor_study_os_desc_other;  }						
						$osData.="<br/>";
					}
					
					if(strpos($intratest_fluctuation_os,"Intratest Fluctuation")!==false) {  
						$osData.= "Intratest Fluctuation<br/>"; 
					}
					
					if(strpos($artifact_os,"Artifact")!==false) {  $osData.= "Artifact<br/>"; }
					
					if(!empty($details_lids_os)){
						$osData.="<b>Details:</b><br/>";
						$osData.=rtrim($details_lids_os,", ")."<br/>";
					}
					
					if(strpos($normal_os,"Normal / Full")!==false) {  
						$osData.= "Normal / Full<br/>";
					}
					
					if(strpos($nonspecific_os,"Nonspecific Details")!==false) {  $osData.= "Nonspecific Details<br/>"; }
					
					if(!empty($nasal_step_os)) {  $osData.= "<b>Nasal Step</b> ".rtrim($nasal_step_os,", ")."<br/>"; }
					if(!empty($arcuate_os)) {  $osData.= "<b>Arcuate</b> ".rtrim($arcuate_os,", ")."<br/>"; }
					if(!empty($hemifield_os)) {  $osData.= "<b>Hemifield</b> ".rtrim($hemifield_os,", ")."<br/>"; }
					if(!empty($paracentral_os)) {  $osData.= "<b>Paracentral</b> ".rtrim($paracentral_os,", ")."<br/>"; }
					if(!empty($into_fixation_os)) {  $osData.= "<b>Into Fixation</b> ".rtrim($into_fixation_os,", ")."<br/>"; }
					if(!empty($central_island_os)) {  $osData.= "<b>Central Island</b> Remaining ".$central_island_os." degrees"."<br/>"; }
					if(strpos($enlarged_blind_spot_os,"Enlarged Blind Spot")!==false) {  $osData.="Enlarged Blind Spot"."<br/>"; }
					if(strpos($cecocentral_scotone_os,"Cecocentral Scotone")!==false) {  $osData.= "Cecocentral Scotone"."<br/>"; }
					if(strpos($central_scotoma_os,"Central Scotoma")!==false) {  $osData.= "Central Scotoma"."<br/>"; }
					
					
					
				if($osData!="" || $odData!=""){
						?>
						<tr>
							<td class="tb_subheading bdrbtm" colspan="2">Test Results</td>
						</tr>
						<tr>
							<td style="width:360px;" class="bdrbtm"><?php odLable();?></td>
							<td style="width:360px;" class="bdrbtm"><?php osLable();?></td>
						</tr>
					<?php	
						if($odData || $osData){
						?>
						<tr>
							<td style="width:360px;" class="bdrbtm"><?php echo  $odData; ?></td>
							<td style="width:360px;" class="bdrbtm"><?php echo  $osData; ?></td>
						</tr>
					<?php
						}						
					?>
					
						<?php
					}
						$Others_OD = '';
						$Others_OS = '';
						
						//--
						
						if(!empty($hemianopsia_od)) {  
							echo "<tr>
									<td colspan=\"2\" style=\"width:700px;\"><b>Hemianopsis</b> ".rtrim($hemianopsia_od,", ")."</td>
								</tr>
								"; 
						}
						
						if(!empty($quadrantanopsia_od)) {  
							echo "<tr>
									<td colspan=\"2\" style=\"width:700px;\"><b>Quadranopsia</b> ".rtrim($quadrantanopsia_od,", ")."</td>
								</tr>
								"; 
						}
						
						if(!empty($homonomous_od)) {  
							echo "<tr>
									<td colspan=\"2\" style=\"width:700px;\"><b>Congruity</b> ".rtrim($homonomous_od,", ")."</td>
								</tr>
								"; 
						}
						
						if(!empty($synthesis_od) || !empty($synthesis_os)){
							if(!empty($synthesis_od)){ $synthesis_od = "<b>Synthesis</b> ".$synthesis_od; }
							if(!empty($synthesis_os)){ $synthesis_os = "<b>Synthesis</b> ".$synthesis_os; }
							
							echo "<tr>
									<td style=\"width:360px;\" class=\"bdrbtm\">".$synthesis_od."</td>
									<td style=\"width:360px;\" class=\"bdrbtm\">".$synthesis_os."</td>
								</tr>
								";
						}
						
						//Inter
						if(!empty($interpretation_OD) || !empty($interpretation_OS) || !empty($comments_interp)){
							echo "<tr>
								<td style=\"width:360px;\" class=\"bdrbtm\"><b>Interpretation</b></td>
								<td style=\"width:360px;\" class=\"bdrbtm\"></td>
								</tr>
								<tr>
								<td style=\"width:360px;\" class=\"bdrbtm\">".rtrim($interpretation_OD,", ")." ".$comments_interp."</td>
								<td style=\"width:360px;\" class=\"bdrbtm\">".rtrim($interpretation_OS,", ")."</td>
								</tr>
								";
						}		
								
						//GLucoma Stage
						if(!empty($glaucoma_stage_opt_OD) || !empty($glaucoma_stage_opt_OS)){
							echo "<tr>
								<td style=\"width:360px;\" class=\"bdrbtm\"><b>Glaucoma Stage</b></td>
								<td style=\"width:360px;\" class=\"bdrbtm\"></td>
								</tr>
								<tr>
								<td style=\"width:360px;\" class=\"bdrbtm\">".rtrim($glaucoma_stage_opt_OD,", ")."</td>
								<td style=\"width:360px;\" class=\"bdrbtm\">".rtrim($glaucoma_stage_opt_OS,", ")."</td>
								</tr>
								";
						}
						
						//plan						
						if(!empty($plan)){
							$strPlan = "";
							$strPlan .= $plan."  ";	
							if(!empty($repeatTestVal1)){ $strPlan .= $repeatTestVal1."  ";  }
							if(!empty($repeatTestVal2)){ $strPlan .= $repeatTestVal2."  "; }
							if(!empty($repeatTestEye)){  $strPlan .= $repeatTestEye."  "; }
							
							echo "<tr>
									<td colspan=\"2\" style=\"width:700px;\" class=\"bdrbtm\" ><b>Plan</b> ".$strPlan."</td>
								</tr>
								"; 
						}
						
						//--
						
						
					if($comments!=""){
					?>
						
							<tr>
								<td colspan="2" style="width:700px;" class="bdrbtm" ><b>Comments:</b>&nbsp;<?php echo $comments; ?></td>
							</tr>							
						
					<?php
					} 
					///Add VF Images//
					$imagesHtml=getTestImages($vf_gl_id,$sectionImageFrom="VF-GL",$patient_id);
					if($imagesHtml!=""){
						echo("<tr><td colspan='2'><table >							 
								".$imagesHtml." 
							</table></td></tr>");
					} 
					$imagesHtml="";
					//End VF Images
					$comments = '';
					if($phyName){
						$getNameQry = imw_query("SELECT CONCAT_WS(', ', lname, fname) as phyName FROM users WHERE id = '$phyName'");	
						$getNameRow = imw_fetch_assoc($getNameQry);
						$phyName = str_replace(", ,"," ",$getNameRow['phyName']);
						?>

		<tr>
			<td colspan="2" ><span class="text_lable">Interpreted By:&nbsp;</span> <?php echo $phyName; ?></td>
		</tr>

						<?php
					}
					?>
				
</table>		
		<?php 
	}//End of while
  }else{ ?>
    <table style="width:740px;" class="border" cellpadding="0" cellspacing="0">
                <tr>
                    <td class="tb_heading">VF-GL</td>
                </tr>
                <tr>
                    <td>No Data</td>
                </tr>		
           </table>  
    <?php 
    }

}



/**********************************************************************************************/
function pdfVersion2($filename)
{ 
	$fp = @fopen($filename, 'rb');
 
	if (!$fp) {

		return 0;
	}
 
	
	fseek($fp, 0);
 
	
	preg_match('/\d\.\d/',fread($fp,20),$match);
 
	fclose($fp);
 
	if (isset($match[0])) {
		return $match[0];
	} else {
		return 0;
	}
}
function checknMakeImagesHTML($arrPDFs){
	$str_alternative_images='';
	foreach($arrPDFs as $pdf_file){
		if(file_exists($pdf_file)){
			
			$version= pdfVersion2($pdf_file);
			if($version>1.4){
				$jpg_name= substr($pdf_file, 0, -4).'.jpg';
	
				if(file_exists($jpg_name)){
					$scnfileSize = getimagesize($jpg_name);
					if($scnfileSize[0]>700 ){
						$scnimageWidth2 = ManageData::imageResize($scnfileSize[0],$scnfileSize[1],700);
						$scnMultiImage= '<tr><td style="width:700px;text-align:center" ><img style="cursor:pointer" src="'.$jpg_name.'" alt="patient Image" '.$scnimageWidth2.'></td></tr>';
					}
					else{
						$scnMultiImage= '<tr><td style="width:700px;text-align:center"><img style="cursor:pointer" src="'.$jpg_name.'" alt="patient Image"></td></tr>';
					}
					
					$arr_alternative_images[]= $scnMultiImage;
				}
			}
		}
	}

	if(sizeof($arr_alternative_images)>0){
		$str_alternative_images=implode(',', $arr_alternative_images);
	}
	return $str_alternative_images;
}

function print_vf($patient_id,$form_id,$req){
	if(is_array($req) && count($req)>0){
		$sqlVFFormQry = imw_query("SELECT * FROM vf WHERE patientId = '".$patient_id."' AND vf_id in (".implode(",",$req).")");
	}else if($form_id!=""){
		$sqlVFFormQry = imw_query("SELECT * FROM vf WHERE patientId = '".$patient_id."' AND formId = '$form_id' AND purged='0' ");
	}

 
	if(imw_num_rows($sqlVFFormQry)>0){
		while($sqlVFFormRow = imw_fetch_assoc($sqlVFFormQry)){
		extract($sqlVFFormRow);
		if($performedBy){
			/*$getNameQry = imw_query("SELECT CONCAT_WS(', ', lname, fname) as namePerformer FROM users WHERE id = '$performedBy'");	
			$getNameRow = imw_fetch_assoc($getNameQry);
			$namePerformer = $getNameRow['namePerformer'];*/
			$namePerformer = print_phyInitial($performedBy);
		}
	
			$orderByName="";
			if($ordrby){
			/*$getNameQry = imw_query("SELECT CONCAT_WS(', ', lname, fname) as orderByName FROM users WHERE id = '$ordrby '");	
			$getNameRow = imw_fetch_assoc($getNameQry);
			$orderByName = $getNameRow['orderByName'];*/
			$orderByName = print_phyInitial($ordrby);	
			}
			if($orderByName){
				$orderByName = "<span class=\"text_lable\">Order By:&nbsp;</span>".$orderByName; 
				if($ordrdt && $ordrdt!="0000-00-00") {$orderByName .= "&nbsp;&nbsp;".get_date_format($ordrdt);}
			}
		?>
		
				<table style="width:755px;" class="border" cellpadding="0" cellspacing="0">
					<tr>
						<td colspan="2" class="tb_heading" >VF (<span class="text_value">Exam Date:&nbsp;<?php print get_date_format($examDate);?></span>)&nbsp;<?php echo $orderByName;?></td>
					</tr>
				
					<tr>
						<td colspan="2" style="width:700px;" class="bdrbtm">
						<b>VF</b>&nbsp;<?php echo($vf_eye."&nbsp;".$elem_gla_mac); ?>
						</td>
					</tr>
				
					<?php
					if($diagnosis=="Other"&&!empty($diagnosisOther)){$diagnosis=$diagnosisOther;}
					if($namePerformer || $ptUnderstanding || $diagnosis )
					{
						?>
					
					<?php if($techComments!=""){?>
						<tr>
							<td colspan="2" style="width:700px;" class="bdrbtm"><b>Technician Comments:</b>&nbsp;<?php echo($techComments);?></td>
						</tr>
					<?php } ?>
						<tr>
							<td  colspan="2" style="width:700px;" class="bdrbtm">								
								<span class="text_lable">Performed By:&nbsp;</span> <?php echo $namePerformer; ?>
								&nbsp;&nbsp;
								<span class="text_lable">Patient Understanding & Cooperation</span>:&nbsp;<?php echo $ptUnderstanding; ?>
								&nbsp;&nbsp;
								<span class="text_lable">Diagnosis:&nbsp;</span> <?php echo ($diagnosis!="--Select--") ? $diagnosis : ""; ?>
							</td>
						</tr>
						
					
					<?php
					}
					if($reliabilityOd || $reliabilityOs)
					{
					?>
                    <tr>
                    	<td colspan="2" style="width:700px;">
					<table style="width:740px;" cellpadding="0" cellspacing="0">
						<tr>
							<td class="text_lable bdrbtm" style="width:180px;">Physician Interpretation:&nbsp;</td>							
							<td class="text_lable bdrbtm" style="width:80px;">Reliability</td>
							<td class="text_lable bdrbtm" style="width:50px;"><?php odLable();?></td>
							<td style="width:100px;" class="bdrbtm"><?php echo $reliabilityOd; ?></td>
							<td class="text_lable bdrbtm" style="width:50px;"><?php osLable(); ?></td>
							<td style="width:100px;" class="bdrbtm"><?php echo $reliabilityOs; ?></td>
						</tr>
					</table>							
                    </td>
                    </tr>
					<?php
					}
					// OD DATA
					$odData ="";
					if($Normal_OD_T) $odData = 'Normal&nbsp; ';
					if($Normal_OD_PoorStudy == 1) $odData .= '<br>Poor Study&nbsp; ';
					if($BorderLineDefect_OD_T == 1 || $BorderLineDefect_OD_1 == 1 || $BorderLineDefect_OD_2 == 1 || $BorderLineDefect_OD_3 == 1 || $BorderLineDefect_OD_4 == 1)
					{
						$odData.= '<br>Border Line Defect ';
						$odData.= '&nbsp; ';
						if($BorderLineDefect_OD_T == 1) $odData.= '&nbsp;T&nbsp; ';
						if($BorderLineDefect_OD_1 == 1) $odData.= '&nbsp;+1&nbsp; ';
						if($BorderLineDefect_OD_2 == 1) $odData.= '&nbsp;+2&nbsp; ';
						if($BorderLineDefect_OD_3 == 1) $odData.= '&nbsp;+3&nbsp; ';
						if($BorderLineDefect_OD_4 == 1) $odData.= '&nbsp;+4&nbsp; ';
					}
					if($Abnormal_OD_T == 1 || $Abnormal_OD_1 == 1 || $Abnormal_OD_2 == 1 || $Abnormal_OD_3 == 1 || $Abnormal_OD_4 == 1)
					{
						$odData.= '<br>Abnormal ';
						$odData.= '&nbsp; ';
						if($Abnormal_OD_T == 1) $odData.= '&nbsp;T&nbsp; ';
						if($Abnormal_OD_1 == 1) $odData.= '&nbsp;+1&nbsp; ';
						if($Abnormal_OD_2 == 1) $odData.= '&nbsp;+2&nbsp; ';
						if($Abnormal_OD_3 == 1) $odData.= '&nbsp;+3&nbsp; ';
						if($Abnormal_OD_4 == 1) $odData.= '&nbsp;+4&nbsp; ';
					}
					if($NasalSteep_OD_Superior == 1 || $NasalSteep_OD_S_T == 1 || $NasalSteep_OD_S_1 == 1 || $NasalSteep_OD_S_2 == 1 || $NasalSteep_OD_S_3 == 1 || $NasalSteep_OD_S_4 == 1)
					{
						$odData.= '<br>Nasal Step ';						
						if($NasalSteep_OD_Superior == 1) $odData.= 'Superior ';
						if($NasalSteep_OD_S_T == 1) $odData.= '&nbsp;T&nbsp; ';
						if($NasalSteep_OD_S_1 == 1) $odData.= '&nbsp;+1&nbsp; ';
						if($NasalSteep_OD_S_2 == 1) $odData.= '&nbsp;+2&nbsp; ';
						if($NasalSteep_OD_S_3 == 1) $odData.= '&nbsp;+3&nbsp; ';
						if($NasalSteep_OD_S_4 == 1) $odData.= '&nbsp;+4&nbsp; ';
					}
					if($NasalSteep_OD_Inferior == 1 || $NasalSteep_OD_I_T == 1 || $NasalSteep_OD_I_1 == 1 || $NasalSteep_OD_I_2 == 1 || $NasalSteep_OD_I_3 == 1 || $NasalSteep_OD_I_4 == 1){
						$odData.= '<br>Nasal Step ';
						if($NasalSteep_OD_Inferior == 1) $odData.= 'Inferior ';
						if($NasalSteep_OD_I_T == 1) $odData.= '&nbsp;T&nbsp; ';
						if($NasalSteep_OD_I_1 == 1) $odData.= '&nbsp;+1&nbsp; ';
						if($NasalSteep_OD_I_2 == 1) $odData.= '&nbsp;+2&nbsp; ';
						if($NasalSteep_OD_I_3 == 1) $odData.= '&nbsp;+3&nbsp; ';
						if($NasalSteep_OD_I_4 == 1) $odData.= '&nbsp;+4&nbsp; ';
					}
					if($Arcuatedefect_OD_Superior == 1 || $Arcuatedefect_OD_S_T == 1 || $Arcuatedefect_OD_S_1 == 1 || $Arcuatedefect_OD_S_2 == 1 || $Arcuatedefect_OD_S_3 == 1 || $Arcuatedefect_OD_S_4 == 1){
						$odData.= '<br>Arcuate defect ';
						if($Arcuatedefect_OD_Superior == 1) $odData.= 'Superior ';
						if($Arcuatedefect_OD_S_T == 1) $odData.= '&nbsp;T&nbsp; ';
						if($Arcuatedefect_OD_S_1 == 1) $odData.= '&nbsp;+1&nbsp; ';
						if($Arcuatedefect_OD_S_2 == 1) $odData.= '&nbsp;+2&nbsp; ';
						if($Arcuatedefect_OD_S_3 == 1) $odData.= '&nbsp;+3&nbsp; ';
						if($Arcuatedefect_OD_S_4 == 1) $odData.= '&nbsp;+4&nbsp; ';
					}
					if($Arcuatedefect_OD_Inferior == 1 || $Arcuatedefect_OD_I_T == 1 || $Arcuatedefect_OD_I_1 == 1 || $Arcuatedefect_OD_I_2 == 1 || $Arcuatedefect_OD_I_3 == 1 || $Arcuatedefect_OD_I_4 == 1){
						$odData.= '<br>Arcuate defect ';
						if($Arcuatedefect_OD_Inferior == 1) $odData.= 'Inferior ';
						if($Arcuatedefect_OD_I_T == 1) $odData.= '&nbsp;T&nbsp; ';
						if($Arcuatedefect_OD_I_1 == 1) $odData.= '&nbsp;+1&nbsp; ';
						if($Arcuatedefect_OD_I_2 == 1) $odData.= '&nbsp;+2&nbsp; ';
						if($Arcuatedefect_OD_I_3 == 1) $odData.= '&nbsp;+3&nbsp; ';
						if($Arcuatedefect_OD_I_4 == 1) $odData.= '&nbsp;+4&nbsp; ';
					}
					if($Defect_OD_Central == 1 || $Defect_OD_Superior == 1 || $Defect_OD_Inferior == 1 || $Defect_OD_Scattered == 1
						|| 
						$Defect_OD_T == 1 || $Defect_OD_1 == 1 || $Defect_OD_2 == 1 || $Defect_OD_3 == 1 || $Defect_OD_4 == 1
						){
						$odData.= '<br>Defect ';
						if($Defect_OD_Central == 1) $odData.= 'Central ';
						if($Defect_OD_Superior == 1) $odData.= 'Superior ';
						if($Defect_OD_Inferior == 1) $odData.= 'Inferior ';
						if($Defect_OD_Scattered == 1) $odData.= 'Scattered ';
						if($Defect_OD_Central == 1 || $Defect_OD_Superior == 1 || $Defect_OD_Inferior == 1 || $Defect_OD_Scattered == 1)
							$odData.= '<br>&nbsp; ';
						if($Defect_OD_T == 1) $odData.= '&nbsp;T&nbsp; ';
						if($Defect_OD_1 == 1) $odData.= '&nbsp;+1&nbsp; ';
						if($Defect_OD_2 == 1) $odData.= '&nbsp;+2&nbsp; ';
						if($Defect_OD_3 == 1) $odData.= '&nbsp;+3&nbsp; ';
						if($Defect_OD_4 == 1) $odData.= '&nbsp;+4&nbsp; ';
					}				
					if($blindSpot_OD_T == 1 || $blindSpot_OD_1 == 1 || $blindSpot_OD_2 == 1 || $blindSpot_OD_3 == 1 || $blindSpot_OD_4 == 1){
						$odData.= '<br>Increase size of Blind&nbsp;spot ';
						if($blindSpot_OD_T == 1) $odData.= '&nbsp;T&nbsp; ';
						if($blindSpot_OD_1 == 1) $odData.= '&nbsp;+1&nbsp; ';
						if($blindSpot_OD_2 == 1) $odData.= '&nbsp;+2&nbsp; ';
						if($blindSpot_OD_3 == 1) $odData.= '&nbsp;+3&nbsp; ';
						if($blindSpot_OD_4 == 1) $odData.= '&nbsp;+4&nbsp; ';
					}
					if($NoSigChange_OD == 1) $odData.= '<br/>No Sig. Change&nbsp; ';
						if($Improved_OD == 1) $odData.= '&nbsp;Improved&nbsp; ';
						if($IncAbn_OD == 1) $odData.= '&nbsp;Inc. Abn&nbsp; ';
					if($iopTrgtOd  !="") $odData.= '<br/>IOP Target:'.$iopTrgtOd.'&nbsp; ';
					// OS DATA
					$osData = "";
					if($Normal_OS_T) $osData = 'Normal&nbsp; ';
					if($Normal_OS_PoorStudy == 1) $osData .= '<br>Poor Study&nbsp; ';	
					if($BorderLineDefect_OS_T == 1 || $BorderLineDefect_OS_1 == 1 || $BorderLineDefect_OS_2 == 1 || $BorderLineDefect_OS_3 == 1 || $BorderLineDefect_OS_4 == 1){
						$osData.= '<br>Border Line Defect ';
						$osData.= '&nbsp; ';
						if($BorderLineDefect_OS_T == 1) $osData.= '&nbsp;T&nbsp; ';
						if($BorderLineDefect_OS_1 == 1) $osData.= '&nbsp;+1&nbsp; ';
						if($BorderLineDefect_OS_2 == 1) $osData.= '&nbsp;+2&nbsp; ';
						if($BorderLineDefect_OS_3 == 1) $osData.= '&nbsp;+3&nbsp; ';
						if($BorderLineDefect_OS_4 == 1) $osData.= '&nbsp;+4&nbsp; ';
					}
					if($Abnormal_OS_T == 1 || $Abnormal_OS_1 == 1 || $Abnormal_OS_2 == 1 || $Abnormal_OS_3 == 1 || $Abnormal_OS_4 == 1){
						$osData.= '<br>Abnormal ';
						$osData.= '&nbsp; ';
						if($Abnormal_OS_T == 1) $osData.= '&nbsp;T&nbsp; ';
						if($Abnormal_OS_1 == 1) $osData.= '&nbsp;+1&nbsp; ';
						if($Abnormal_OS_2 == 1) $osData.= '&nbsp;+2&nbsp; ';
						if($Abnormal_OS_3 == 1) $osData.= '&nbsp;+3&nbsp; ';
						if($Abnormal_OS_4 == 1) $osData.= '&nbsp;+4&nbsp; ';
					}
					if($NasalSteep_OS_Superior == 1 || $NasalSteep_OS_S_T == 1 || $NasalSteep_OS_S_1 == 1 || $NasalSteep_OS_S_2 == 1 || $NasalSteep_OS_S_3 == 1 || $NasalSteep_OS_S_4 == 1){
						$osData.= '<br>Nasal Step ';						
						if($NasalSteep_OS_Superior == 1) $osData.= 'Superior ';
						if($NasalSteep_OS_S_T == 1) $osData.= '&nbsp;T&nbsp; ';
						if($NasalSteep_OS_S_1 == 1) $osData.= '&nbsp;+1&nbsp; ';
						if($NasalSteep_OS_S_2 == 1) $osData.= '&nbsp;+2&nbsp; ';
						if($NasalSteep_OS_S_3 == 1) $osData.= '&nbsp;+3&nbsp; ';
						if($NasalSteep_OS_S_4 == 1) $osData.= '&nbsp;+4&nbsp; ';
					}
					if($NasalSteep_OS_Inferior == 1 || $NasalSteep_OS_I_T == 1 || $NasalSteep_OS_I_1 == 1 || $NasalSteep_OS_I_2 == 1 || $NasalSteep_OS_I_3 == 1 || $NasalSteep_OS_I_4 == 1){
						$osData.= '<br>Nasal Step ';
						if($NasalSteep_OS_Inferior == 1) $osData.= 'Inferior ';
						if($NasalSteep_OS_I_T == 1) $osData.= '&nbsp;T&nbsp; ';
						if($NasalSteep_OS_I_1 == 1) $osData.= '&nbsp;+1&nbsp; ';
						if($NasalSteep_OS_I_2 == 1) $osData.= '&nbsp;+2&nbsp; ';
						if($NasalSteep_OS_I_3 == 1) $osData.= '&nbsp;+3&nbsp; ';
						if($NasalSteep_OS_I_4 == 1) $osData.= '&nbsp;+4&nbsp; ';
					}
					if($Arcuatedefect_OS_Superior == 1 || $Arcuatedefect_OS_S_T == 1 || $Arcuatedefect_OS_S_1 == 1 || $Arcuatedefect_OS_S_2 == 1 || $Arcuatedefect_OS_S_3 == 1 || $Arcuatedefect_OS_S_4 == 1){
						$osData.= '<br>Arcuate defect ';
						if($Arcuatedefect_OS_Superior == 1) $osData.= 'Superior ';
						if($Arcuatedefect_OS_S_T == 1) $osData.= '&nbsp;T&nbsp; ';
						if($Arcuatedefect_OS_S_1 == 1) $osData.= '&nbsp;+1&nbsp; ';
						if($Arcuatedefect_OS_S_2 == 1) $osData.= '&nbsp;+2&nbsp; ';
						if($Arcuatedefect_OS_S_3 == 1) $osData.= '&nbsp;+3&nbsp; ';
						if($Arcuatedefect_OS_S_4 == 1) $osData.= '&nbsp;+4&nbsp; ';
					}
					if($Arcuatedefect_OS_Inferior == 1 || $Arcuatedefect_OS_I_T == 1 || $Arcuatedefect_OS_I_1 == 1 || $Arcuatedefect_OS_I_2 == 1 || $Arcuatedefect_OS_I_3 == 1 || $Arcuatedefect_OS_I_4 == 1){
						$osData.= '<br>Arcuate defect ';
						if($Arcuatedefect_OS_Inferior == 1) $osData.= 'Inferior ';
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
						$osData.= '<br>Defect ';
						if($Defect_OS_Central == 1) $osData.= 'Central ';
						if($Defect_OS_Superior == 1) $osData.= 'Superior ';
						if($Defect_OS_Inferior == 1) $osData.= 'Inferior ';
						if($Defect_OS_Scattered == 1) $osData.= 'Scattered ';
						if($Defect_OS_Central == 1 || $Defect_OS_Superior == 1 || $Defect_OS_Inferior == 1 || $Defect_OS_Scattered == 1)
							$osData.= '<br>&nbsp;';
						if($Defect_OS_T == 1) $osData.= '&nbsp;T&nbsp;';
						if($Defect_OS_1 == 1) $osData.= '&nbsp;+1&nbsp;';
						if($Defect_OS_2 == 1) $osData.= '&nbsp;+2&nbsp;';
						if($Defect_OS_3 == 1) $osData.= '&nbsp;+3&nbsp;';
						if($Defect_OS_4 == 1) $osData.= '&nbsp;+4&nbsp;';
					}				
					if($blindSpot_OS_T == 1 || $blindSpot_OS_1 == 1 || $blindSpot_OS_2 == 1 || $blindSpot_OS_3 == 1 || $blindSpot_OS_4 == 1){
						$osData.= '<br>Increase size of Blind&nbsp;spot';
						if($blindSpot_OS_T == 1) $osData.= '&nbsp;T&nbsp;';
						if($blindSpot_OS_1 == 1) $osData.= '&nbsp;+1&nbsp;';
						if($blindSpot_OS_2 == 1) $osData.= '&nbsp;+2&nbsp;';
						if($blindSpot_OS_3 == 1) $osData.= '&nbsp;+3&nbsp;';
						if($blindSpot_OS_4 == 1) $osData.= '&nbsp;+4&nbsp;';
					}
					if($NoSigChange_OS == 1) $osData.= '<br/>No Sig. Change&nbsp;';
						if($Improved_OS == 1) $osData.= '&nbsp;Improved&nbsp;';
						if($IncAbn_OS == 1) $osData.= '&nbsp;Inc. Abn&nbsp;';
						if($iopTrgtOs  !="") $osData.= '<br/>IOP Target:'.$iopTrgtOs.'&nbsp;';
				if($osData!="" || $odData!="" || $Others_OD!="" || $Others_OS!=""){
						?>
						<tr>
							<td class="tb_subheading bdrbtm" colspan="2">Test Results</td>
						</tr>
						<tr>
							<td style="width:360px;" class="bdrbtm"><?php odLable();?></td>
							<td style="width:360px;" class="bdrbtm"><?php osLable();?></td>
						</tr>
					<?php	
						if($odData || $osData){
						?>
						<tr>
							<td style="width:360px;" class="bdrbtm"><?php echo  $odData; ?></td>
							<td style="width:360px;" class="bdrbtm"><?php echo  $osData; ?></td>
						</tr>
					<?php
						}	
					if($Others_OD!="" || $Others_OS!=""){
						?>
							<tr>
								<td style="width:360px;" class="bdrbtm"><?php echo $Others_OD; ?></td>
								<td style="width:360px;" class="bdrbtm"><?php echo $Others_OS; ?></td>
							</tr>
						<?php				
					}
					?>
					
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
							<tr>
								<td colspan="2" style="width:700px;" class="bdrbtm" ><b>Treatment/Prognosis:</b>&nbsp;<?php echo $treatment; ?></td>
							</tr>
							
							<?php
						}
					if($loss_sup_degree_od && $improve_degree_od)
					{
						?>
						
							<tr>
								<td style="width:700px;" class="bdrbtm" colspan="2">
								Visual field in the Right Eye shows loss of the superior (<?php echo $loss_sup_degree_od;?>) degrees. This improves by (<?php echo $improve_degree_od;?>) degrees when the lid is taped in the elevated position. This documents functional ptosis in the Right Eye. 
								</td>
							</tr>
						
						<?php	
					}
					
					if($loss_sup_degree_os && $improve_degree_os)
					{
						?>
						
							<tr>
								<td style="width:700px;" class="bdrbtm" colspan="2">
								Visual field in the Left Eye shows loss of the superior (<?php echo $loss_sup_degree_os;?>) degrees. This improves by (<?php echo $improve_degree_os;?>) degrees when the lid is taped in the elevated position. This documents functional ptosis in the Left Eye.  
								</td>
							</tr>
						
						<?php	
					}
					if($comments!=""){
					?>
						
							<tr>
								<td colspan="2" style="width:700px;" class="bdrbtm" ><b>Comments:</b>&nbsp;<?php echo $comments; ?></td>
							</tr>							
						
					<?php
					} 

					///Add VF Images//
					$imagesHtml=getTestImages($vf_id,$sectionImageFrom="VF",$patient_id);
					
					//GET IMAGES FOR PDFs THAT ARE HIGHER THAN PDF VERSION 1.4 . HIGHER VERSION IMAGES ARE SKIPPED IN merge_pdf.php FILE.
					$arr_alternative_images=array();
					$str_alternative_images='';
					if($vf_id!=""){
						$arrPDFs = getAllTestPdf($vf_id);
						$str_alternative_images= checknMakeImagesHTML($arrPDFs);
						$imagesHtml.=$str_alternative_images;
					}	
					//------------------------------------
					
					if($imagesHtml!=""){
						echo("<tr><td colspan='2'><table >							 
								".$imagesHtml." 
							</table></td></tr>");
					} 

					$imagesHtml="";
					//End VF Images
					$comments = '';
					if($phyName){
						/*$getNameQry = imw_query("SELECT CONCAT_WS(', ', lname, fname) as phyName FROM users WHERE id = '$phyName'");	
						$getNameRow = imw_fetch_assoc($getNameQry);
						$phyName = str_replace(", ,"," ",$getNameRow['phyName']);*/
						$phyName = print_phyInitial($phyName);
						?>

		<tr>
			<td colspan="2" ><span class="text_lable">Interpreted By:&nbsp;</span> <?php echo $phyName; ?></td>
		</tr>

						<?php
					}
					?>
					<tr><td></td><td></td></tr>
				
</table>		
		<?php 
	}//End of while
  }else{ ?>
    <table style="width:740px;" class="border" cellpadding="0" cellspacing="0">
                <tr>
                    <td class="tb_heading">VF</td>
                </tr>
                <tr>
                    <td>No Data</td>
                </tr>		
           </table>  
    <?php 
    }
}

function print_vf_gl_fun($patient_id,$form_id,$req){
	if(is_array($req) && count($req)>0){
		$sqlVFFormQry = imw_query("SELECT * FROM vf_gl WHERE patientId = '".$patient_id."' AND vf_gl_id in (".implode(",",$req).")");
	}else if($form_id!=""){
		$sqlVFFormQry = imw_query("SELECT * FROM vf_gl WHERE patientId = '".$patient_id."' AND formId = '$form_id' AND purged='0' ");
	}

 
	if(imw_num_rows($sqlVFFormQry)>0){
		while($sqlVFFormRow = imw_fetch_assoc($sqlVFFormQry)){
		extract($sqlVFFormRow);
		if($performedBy){
			/*$getNameQry = imw_query("SELECT CONCAT_WS(', ', lname, fname) as namePerformer FROM users WHERE id = '$performedBy'");	
			$getNameRow = imw_fetch_assoc($getNameQry);
			$namePerformer = $getNameRow['namePerformer'];*/
			$namePerformer = print_phyInitial($performedBy);
		}
	
			$orderByName="";
			if($ordrby){
			/*$getNameQry = imw_query("SELECT CONCAT_WS(', ', lname, fname) as orderByName FROM users WHERE id = '$ordrby '");	
			$getNameRow = imw_fetch_assoc($getNameQry);
			$orderByName = $getNameRow['orderByName'];*/
			$orderByName = print_phyInitial($ordrby);
			}
			if($orderByName){
				$orderByName = "<span class=\"text_lable\">Order By:&nbsp;</span>".$orderByName; 
				if($ordrdt && $ordrdt!="0000-00-00") {$orderByName .= "&nbsp;&nbsp;".get_date_format($ordrdt);}
			}
		?>
		
				<table style="width:755px;" class="border" cellpadding="0" cellspacing="0">
					
                    <tr>
						<td colspan="3" class="tb_heading" >VF-GL (<span class="text_value">Exam Date:&nbsp;<?php print get_date_format($examDate);?></span>)&nbsp;<?php echo $orderByName;?></td>
					</tr>
				
					<tr>
						<td colspan="3" style="width:700px;" class="bdrbtm">
						<b>VF-GL:</b>&nbsp;<?php echo($vf_gl_eye."&nbsp;".$elem_gla_mac."&nbsp;".$gla_mac_os); ?>
						</td>
					</tr>
				
					<?php if($namePerformer || $ptUnderstanding || $diagnosis ){ ?>
					
					<?php if($techComments!=""){ ?>
                    <tr>
                        <td colspan="3" style="width:700px;" class="bdrbtm">
                            <b>Technician Comments:</b>&nbsp;<?php echo($techComments);?>
                        </td>
                    </tr>
					<?php } ?>
                    <tr>
                        <td colspan="3" style="width:700px;" class="bdrbtm">								
                            <b>Performed By:</b>&nbsp;<?php echo $namePerformer; ?>
                            &nbsp;&nbsp;
                            <b>Patient Understanding & Cooperation:</b>&nbsp;<?php echo $ptUnderstanding; ?>
                         </td>
                    </tr>
		    
		    <?php
			if($diagnosis=="Other"&&!empty($diagnosisOther)){$diagnosis=$diagnosisOther;}
			if($diagnosis_os=="Other"&&!empty($diagnosisOther_os)){$diagnosis_os=$diagnosisOther_os;}
		    ?>
		    
                    <tr>
                    	<td colspan="3" style="width:700px;" class="bdrbtm">
                         	<b>Diagnosis:</b>&nbsp;<?php echo ($diagnosis!="--Select--") ? "OD: ".$diagnosis : "";
							 echo ($diagnosis_os!="--Select--") ? "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;OS: ".$diagnosis_os : ""; 
							 ?>
                        </td>
                    </tr>
                    <tr>
                    	<td colspan="3" style="width:700px;font-weight:bold;" class="bdrbtm">Physician Interpretation</td>
                    </tr>
                    
                    
                    <?php
					if($descOd || $descOs){ ?>
                    <tr>
                    	<td colspan="3" style="width:700px;font-weight:bold;" class="bdrbtm">Test Results</td>
                    </tr>
                    <tr>
                    	<td style="width:100px" class="bdrbtm tb_subheading">&nbsp;</td>
                        <td style="width:300px;font-weight:bold;" class="bdrbtm tb_subheading bdrright">OD</td>
                        <td style="width:300px;font-weight:bold;" class="bdrbtm tb_subheading">OS</td>
                    </tr>
                    <?php if($reliabilityOd || $reliabilityOs){ ?>
                    <tr>
                    	<td style="width:100px; font-weight:bold;" class="bdrbtm bdrright">Reliability:</td>
                        <td style="width:300px;" class="bdrbtm bdrright"><?php echo $reliabilityOd; ?></td>
                        <td style="width:300px;" class="bdrbtm"><?php echo $reliabilityOs; ?></td>
                    </tr>
                    <?php } ?>
                    <?php if($mdOd || $psdOd || $vfiOd || $mdOs || $psdOs || $vfiOs){ ?>
                    <tr>
                    	<td style="width:100px; font-weight:bold;" class="bdrbtm bdrright">&nbsp;</td>
                        <td style="width:300px;" class="bdrbtm bdrright">
						<?php echo $mdOdVal=($mdOd)?"MD ".$mdOd." dB":"";
							  echo $psdOdVal=($psdOd)?"&nbsp;&nbsp;PSD ".$psdOd." dB":"";
							  echo $vfiOdVal=($vfiOd)?"&nbsp;&nbsp;VFI ".$psdOd." %":"";
						?>
                        </td>
                        <td style="width:300px;" class="bdrbtm">
						<?php echo $mdOsVal=($mdOs)?"MD ".$mdOs." dB":"";
							  echo $psdOsVal=($psdOs)?"&nbsp;&nbsp;PSD ".$psdOs." dB":"";
							  echo $vfiOsVal=($vfiOs)?"&nbsp;&nbsp;VFI ".$psdOs."%":"";
						?>
                        </td>
                    </tr>
                    <?php }
					if($details_high_od || $details_high_os){ ?>
                    <tr>
                    	<td style="width:100px; font-weight:bold;" class="bdrbtm bdrright">Details:</td>
                        <td style="width:300px;" class="bdrbtm bdrright"><?php echo $details_high_od; ?></td>
                        <td style="width:300px;" class="bdrbtm"><?php echo $details_high_os; ?></td>
                    </tr>
                    <?php } 
					if($poor_study_od || $poor_study_os){ ?>
                    <tr>
                    	<td style="width:100px; font-weight:bold;" class="bdrbtm bdrright">Poor Study:</td>
                        <td style="width:300px;" class="bdrbtm bdrright"><?php echo $poor_study_od." ".$poor_study_od_desc; ?></td>
                        <td style="width:300px;" class="bdrbtm"><?php echo $poor_study_os." ".$poor_study_os_desc; ?></td>
                    </tr>
                    <?php }
					
					if($intratest_fluctuation_od || $intratest_fluctuation_os){ ?>
                    <tr>
                    	<td style="width:100px; font-weight:bold;" class="bdrbtm bdrright">&nbsp;</td>
                        <td style="width:300px;" class="bdrbtm bdrright"><?php echo $intratest_fluctuation_od; ?></td>
                        <td style="width:300px;" class="bdrbtm"><?php echo $intratest_fluctuation_os; ?></td>
                    </tr>
                    <?php } 
					if($artifact_od || $artifact_os){ ?>
                    <tr>
                    	<td style="width:100px; font-weight:bold;" class="bdrbtm bdrright">&nbsp;</td>
                        <td style="width:300px;" class="bdrbtm bdrright"><?php echo $artifact_od; ?></td>
                        <td style="width:300px;" class="bdrbtm"><?php echo $artifact_os; ?></td>
                    </tr>
                    <?php }
					if($details_lids_od || $details_lids_os){ ?>
                    <tr>
                    	<td style="width:100px; font-weight:bold;" class="bdrbtm bdrright">Details:&nbsp;</td>
                        <td style="width:300px;" class="bdrbtm bdrright"><?php echo $details_lids_od; ?></td>
                        <td style="width:300px;" class="bdrbtm"><?php echo $details_lids_os; ?></td>
                    </tr>
                    <?php }
					if($normal_os || $normal_od){ ?>
                    <tr>
                    	<td style="width:100px; font-weight:bold;" class="bdrbtm bdrright">&nbsp;</td>
                        <td style="width:300px;" class="bdrbtm bdrright"><?php echo $normal_od; ?></td>
                        <td style="width:300px;" class="bdrbtm"><?php echo $normal_os; ?></td>
                    </tr>
                    <?php } 
					if($nonspecific_od || $nonspecific_os){ ?>
                    <tr>
                    	<td style="width:100px; font-weight:bold;" class="bdrbtm bdrright">&nbsp;</td>
                        <td style="width:300px;" class="bdrbtm bdrright"><?php echo $nonspecific_od; ?></td>
                        <td style="width:300px;" class="bdrbtm"><?php echo $nonspecific_os; ?></td>
                    </tr>
                    <?php }
					if($nasal_step_od || $nasal_step_os){ ?>
                    <tr>
                    	<td style="width:100px; font-weight:bold;" class="bdrbtm bdrright">Nasal Step:</td>
                        <td style="width:300px;" class="bdrbtm bdrright"><?php echo $nasal_step_od; ?></td>
                        <td style="width:300px;" class="bdrbtm"><?php echo $nasal_step_os; ?></td>
                    </tr>
                    <?php }
					if($arcuate_od || $arcuate_os){ ?>
                    <tr>
                    	<td style="width:100px; font-weight:bold;" class="bdrbtm bdrright">Arcuate:</td>
                        <td style="width:300px;" class="bdrbtm bdrright"><?php echo $arcuate_od; ?></td>
                        <td style="width:300px;" class="bdrbtm"><?php echo $arcuate_os; ?></td>
                    </tr>
                    <?php }
					if($hemifield_od || $hemifield_os){ ?>
                    <tr>
                    	<td style="width:100px; font-weight:bold;" class="bdrbtm bdrright">Hemifield:</td>
                        <td style="width:300px;" class="bdrbtm bdrright"><?php echo $hemifield_od; ?></td>
                        <td style="width:300px;" class="bdrbtm"><?php echo $hemifield_os; ?></td>
                    </tr>
                    <?php }
					if($paracentral_od || $paracentral_os){ ?>
                    <tr>
                    	<td style="width:100px; font-weight:bold;" class="bdrbtm bdrright">Paracentral:</td>
                        <td style="width:300px;" class="bdrbtm bdrright"><?php echo $paracentral_od; ?></td>
                        <td style="width:300px;" class="bdrbtm"><?php echo $paracentral_os; ?></td>
                    </tr>
                    <?php }
					if($into_fixation_od || $into_fixation_os){ ?>
                    <tr>
                    	<td style="width:100px; font-weight:bold;" class="bdrbtm bdrright">Into Fixation:</td>
                        <td style="width:300px;" class="bdrbtm bdrright"><?php echo $into_fixation_od; ?></td>
                        <td style="width:300px;" class="bdrbtm"><?php echo $into_fixation_os; ?></td>
                    </tr>
                    <?php }
					if($central_island_od || $central_island_os){ ?>
                    <tr>
                    	<td style="width:100px; font-weight:bold;" class="bdrbtm bdrright">Central Island:</td>
                        <td style="width:300px;" class="bdrbtm bdrright">
							<?php echo $central_island_od_val=($central_island_od)?"Remaining ".$central_island_od." degrees":""; ?>
                        </td>
                        <td style="width:300px;" class="bdrbtm">
							<?php echo $central_island_os_val=($central_island_os)?"Remaining ".$central_island_os." degrees":""; ?>

                        </td>
                    </tr>
                    <?php }
					if($enlarged_blind_spot_od || $enlarged_blind_spot_os){ ?>
                    <tr>
                    	<td style="width:100px; font-weight:bold;" class="bdrbtm bdrright">&nbsp;</td>
                        <td style="width:300px;" class="bdrbtm bdrright">
							<?php echo $enlarged_blind_spot_od; ?>
                        </td>
                        <td style="width:300px;" class="bdrbtm">
							<?php echo $enlarged_blind_spot_os; ?>
                        </td>
                    </tr>
                    <?php }
					if($cecocentral_scotone_od || $cecocentral_scotone_os){ ?>
                    <tr>
                    	<td style="width:100px; font-weight:bold;" class="bdrbtm bdrright">&nbsp;</td>
                        <td style="width:300px;" class="bdrbtm bdrright">
							<?php echo $cecocentral_scotone_od; ?>
                        </td>
                        <td style="width:300px;" class="bdrbtm">
							<?php echo $cecocentral_scotone_os; ?>
                        </td>
                    </tr>
                    <?php }
					if($central_scotoma_od || $central_scotoma_os){ ?>

                    <tr>
                    	<td style="width:100px; font-weight:bold;" class="bdrbtm bdrright">&nbsp;</td>
                        <td style="width:300px;" class="bdrbtm bdrright">
							<?php echo $central_scotoma_od; ?>
                        </td>
                        <td style="width:300px;" class="bdrbtm">
							<?php echo $central_scotoma_os; ?>
                        </td>
                    </tr>
                    <?php }
					if($hemianopsia_od || $hemianopsia_os){ ?>
                    <tr>
                    	<td style="width:100px; font-weight:bold;" class="bdrbtm bdrright">Hemianopsis:</td>
                        <td style="width:300px;" class="bdrbtm bdrright">
							<?php echo $hemianopsia_od; ?>
                        </td>
                        <td style="width:300px;" class="bdrbtm">
							<?php echo $hemianopsia_os; ?>
                        </td>
                    </tr>
                    <?php }
					if($quadrantanopsia_od || $quadrantanopsia_os){ ?>
                    <tr>
                    	<td style="width:100px; font-weight:bold;" class="bdrbtm bdrright">Quadranopsia:</td>
                        <td style="width:300px;" class="bdrbtm bdrright">
							<?php echo $quadrantanopsia_od; ?>
                        </td>
                        <td style="width:300px;" class="bdrbtm">
							<?php echo $quadrantanopsia_os; ?>
                        </td>
                    </tr>
                    <?php }
					if($homonomous_od || $homonomous_os){ ?>
                    <tr>
                    	<td style="width:100px; font-weight:bold;" class="bdrbtm bdrright">Congruity:</td>
                        <td style="width:300px;" class="bdrbtm bdrright">
							<?php echo $homonomous_od; ?>
                        </td>
                        <td style="width:300px;" class="bdrbtm">
							<?php echo $homonomous_os; ?>
                        </td>
                    </tr>
                    <?php }
					if($synthesis_od || $synthesis_os){ ?>
                    <tr>
                    	<td style="width:100px; font-weight:bold;" class="bdrbtm bdrright">Synthesis:</td>
                        <td style="width:300px;" class="bdrbtm bdrright">
							<?php echo $synthesis_od; ?>
                        </td>
                        <td style="width:300px;" class="bdrbtm">
							<?php echo $synthesis_os; ?>
                        </td>
                    </tr>
                    <?php }
					if($interpretation_OD || $interpretation_OS){ ?>
                    <tr>
                    	<td style="width:100px; font-weight:bold;" class="bdrbtm bdrright">Interpretation:</td>
                        <td style="width:300px;" class="bdrbtm bdrright">
							<?php echo $interpretation_OD; ?>
                        </td>
                        <td style="width:300px;" class="bdrbtm">
							<?php echo $interpretation_OS; ?>
                        </td>
                    </tr>
                    <?php }
					if($comments_interp){ ?>
                    <tr>
                    	<td style="width:100px; font-weight:bold;" class="bdrbtm bdrright">Comments:</td>
                        <td colspan="2" style="width:600px;" class="bdrbtm">
							<?php echo $comments_interp; ?>
                        </td>
                    </tr>
                    <?php }	
					if($glaucoma_stage_opt_OD || $glaucoma_stage_opt_OS){ ?>
                    <tr>
                    	<td style="width:100px; font-weight:bold;" class="bdrbtm bdrright">Glaucoma&nbsp;Stage:</td>
                         <td style="width:300px;" class="bdrbtm bdrright">
							<?php echo $glaucoma_stage_opt_OD; ?>
                        </td>
                        <td style="width:300px;" class="bdrbtm">
							<?php echo $glaucoma_stage_opt_OS; ?>
                        </td>
                    </tr>
                    <?php }	
					if($plan){ ?>
                    <tr>
                    	<td style="width:100px; font-weight:bold;" class="bdrbtm bdrright">Plan:</td>
                         <td colspan="2" style="width:600px;" class="bdrbtm">
							<?php 
						$arr_find=array("Pt informed of results by physician today","to be called by technician","by letter","will inform next visit","Continue meds","Monitor findings","Repeat test time");
						$arr_rep=array("Pt informed of results by physician today, ","to be called by technician, ","by letter, ","will inform next visit, ","Continue meds, ","Monitor findings, ","Repeat test time: ".$repeatTestVal1." ".$repeatTestVal2." ".$repeatTestEye);
						echo str_replace($arr_find,$arr_rep,$plan); ?>
                        </td>
                    </tr>
                    <?php }	
					
					}
					}
					if($comments!=""){ ?>
                    <tr>
                        <td colspan="3" style="width:700px;" class="bdrbtm" ><b>Comments:</b>&nbsp;<?php echo $comments; ?></td>
                    </tr>							
					<?php
					} 
					///Add VF Images//
					$imagesHtml=getTestImages($vf_gl_id,$sectionImageFrom="VF-GL",$patient_id);
					
					//GET IMAGES FOR PDFs THAT ARE HIGHER THAN PDF VERSION 1.4 . HIGHER VERSION IMAGES ARE SKIPPED IN merge_pdf.php FILE.
					$arr_alternative_images=array();
					$str_alternative_images='';
					if($vf_gl_id!=""){
						$arrPDFs = getAllTestPdf($vf_gl_id);
						$str_alternative_images= checknMakeImagesHTML($arrPDFs);
						$imagesHtml.=$str_alternative_images;
					}	
					//------------------------------------					
					
					if($imagesHtml!=""){
						echo("<tr><td colspan='3' class='bdrbtm'><table >							 
								".$imagesHtml." 
							</table></td></tr>");
					} 
					$imagesHtml="";
					//End VF Images
					$comments = '';
					if($phyName){
						/*$getNameQry = imw_query("SELECT CONCAT_WS(', ', lname, fname) as phyName FROM users WHERE id = '$phyName'");	
						$getNameRow = imw_fetch_assoc($getNameQry);
						$phyName = str_replace(", ,"," ",$getNameRow['phyName']);*/
						$phyName = print_phyInitial($phyName);
						?>

                    <tr>
                        <td colspan="3" ><b>Interpreted By:</b>&nbsp;<?php echo $phyName; ?></td>
                    </tr>

						<?php
					}
					?>
                    <tr>
                    	<td style="width:100px;"></td>
                    	<td style="width:300px;">&nbsp;</td>
                        <td style="width:300px;">&nbsp;</td>
                    </tr>
			</table>		
		<?php 
	}//End of while
  }
	
}

function print_hrt($patient_id,$form_id,$req){
	if(is_array($req) && count($req)>0){
		$sql = "SELECT * FROM nfa WHERE patient_id = '".$patient_id."' AND nfa_id in(".implode(",",$req).")";	
	}else if($form_id!=""){
		$sql = "SELECT * FROM nfa WHERE patient_id = '".$patient_id."' AND form_id = '$form_id' AND purged='0' ";	
	}
	
	$rowResTemp = imw_query($sql);
	if($rowResTemp){
		while($row=imw_fetch_array($rowResTemp)){
		extract($row);
		?>
	
				<table style="width:755px;" class="border" cellpadding="0" cellspacing="0">
					<tr>
						<td colspan="2" style="width:755px;" class="tb_heading">HRT (<span class="text_value">Exam Date:&nbsp;<?php print get_date_format($examDate);?></span>)</td>
					</tr>
				
					<?php
					if($scanLaserEye){
						if($scanLaserEye == 'OU') $scanLaserEye = '<span style="color:purple;">'.$scanLaserEye.'</span>';
						if($scanLaserEye == 'OD') $scanLaserEye = '<span style="color:blue;">'.$scanLaserEye.'</span>';
						if($scanLaserEye == 'OS') $scanLaserEye = '<span style="color:green;">'.$scanLaserEye.'</span>';
						?>
					
						<tr>
							<td colspan="2" style="width:740px;" class="text_lable bdrbtm">Scanning Laser/NFA:&nbsp; <?php echo $scanLaserEye; ?></td>
						</tr>
					
						<?php
					}
					if($performedBy || $ptUndersatnding){
						/*$performedByQry = imw_query("SELECT CONCAT_WS(', ', lname, fname) as performedBy FROM users WHERE id = '$performedBy'");	
						$performedByRow = imw_fetch_assoc($performedByQry);
						$performedBy = str_replace(", ,"," ",$performedByRow['performedBy']);*/
						$performedBy = print_phyInitial($performedBy);
						?>

					<?php if($techComments!=""){?>
						<tr>
							<td class="text_lable bdrbtm" style="width:740px;" colspan="2">Technician Comments:&nbsp;<span class="text_value"><?php echo($techComments);?></span> </td>
						</tr>
					<?php }?>
						<tr>
							
							<td style="width:350px;" class="bdrbtm"><span class="text_lable">Performed By:&nbsp;</span> <?php echo $performedBy; ?></td>
							<td style="width:350px;" class="bdrbtm"><span class="text_lable">Patient Understanding & Cooperation</span>:&nbsp;<?php echo $ptUndersatnding; ?></td>
						</tr>

						<?php
					}
					
					if($diagnosis=="Other"&&!empty($diagnosisOther)){$diagnosis=$diagnosisOther;}
					if($diagnosis && $diagnosis!="--Select--"){
						?>
			
						<tr>
							<td colspan="2" style="width:700px;" class="bdrbtm"><span class="text_lable">Diagnosis:&nbsp;</span> <?php echo ($diagnosis!="--Select--") ? $diagnosis : ""; ?></td>
						</tr>
			
					<?php
					}
					if($reliabilityOd || $reliabilityOs){
					?>
                    <tr>
                    	<td colspan="2" style="width:740px;">
                            <table style="width:740px;" cellpadding="0" cellspacing="0">
                                <tr>	
                                    <td class="text_lable bdrbtm"  style="width:180px;">Physician Interpretation:&nbsp;</td>						
                                    <td class="text_lable bdrbtm"  style="width:80px;">Reliability</td>
                                    <td class="text_lable bdrbtm"  style="width:30px;"><?php odLable();?></td>
                                    <td class="bdrbtm" style="width:100px;" ><?php echo $reliabilityOd; ?></td>
                                    <td class="text_lable bdrbtm" style="width:30px;" ><?php osLable();?></td>
                                    <td class="bdrbtm" style="width:250px;" ><?php echo $reliabilityOs; ?></td>
                                </tr>
                            </table>
                        </td>
                    </tr>        	
					<?php
					}
					$odData = '';
					$osData = '';
					
					// OD DATA
					if($Normal_OD_T) $odData = 'Normal&nbsp;<br>';
					if($Normal_OD_PoorStudy == 1) $odData .= 'Poor Study&nbsp;<br>';
					if($BorderLineDefect_OD_T == 1 || $BorderLineDefect_OD_1 == 1 || $BorderLineDefect_OD_2 == 1 || $BorderLineDefect_OD_3 == 1 || $BorderLineDefect_OD_4 == 1){
						$odData.= 'Border Line Defect';
						$odData.= '&nbsp;';
						if($BorderLineDefect_OD_T == 1) $odData.= '&nbsp;T&nbsp;';
						if($BorderLineDefect_OD_1 == 1) $odData.= '&nbsp;+1&nbsp;';
						if($BorderLineDefect_OD_2 == 1) $odData.= '&nbsp;+2&nbsp;';
						if($BorderLineDefect_OD_3 == 1) $odData.= '&nbsp;+3&nbsp;';
						if($BorderLineDefect_OD_4 == 1) $odData.= '&nbsp;+4&nbsp;';
					}
					if($Abnorma_OD_T == 1 || $Abnorma_OD_1 == 1 || $Abnorma_OD_2 == 1 || $Abnorma_OD_3 == 1 || $Abnorma_OD_4 == 1){
						$odData.= '<br>Abnormal';
						$odData.= '&nbsp;';
						if($Abnorma_OD_T == 1) $odData.= '&nbsp;T&nbsp;';
						if($Abnorma_OD_1 == 1) $odData.= '&nbsp;+1&nbsp;';
						if($Abnorma_OD_2 == 1) $odData.= '&nbsp;+2&nbsp;';
						if($Abnorma_OD_3 == 1) $odData.= '&nbsp;+3&nbsp;';
						if($Abnorma_OD_4 == 1) $odData.= '&nbsp;+4&nbsp;';
					}
					
					if(!empty($decreaseOd)){
						$odData.= '<br>Decreased<br/>';
						//$odData.= '&nbsp;';
						$odData.=str_replace(",","<br/>",$decreaseOd);						
					}
					
					if(!empty($thinOd)){
						$odData.= '<br>Thin';
						$odData.= '&nbsp;'.$thinOd;
					}
					
					if(!empty($totalThinOd)){
						$odData.= '<br>Total Thin';
						$odData.= '&nbsp;'.$totalThinOd;
					}					
					
					if($NoSigChange_OD == 1||$Improved_OD == 1||$IncAbn_OD == 1){
						$odData.= '<br/>';
						if($NoSigChange_OD == 1) $odData.= 'No Sig. Change&nbsp;'; 
						if($Improved_OD == 1) $odData.= 'Improved&nbsp;';
						if($IncAbn_OD == 1) $odData.= 'Inc. Abn&nbsp;';
					}
					
					if($iopTrgtOd){
						$odData.= '<br>IOP Target';
						$odData.= '&nbsp;'.$iopTrgtOd;
					}					
	
					// OS DATA
					if($Normal_OS_T) $osData = 'Normal&nbsp;<br>';
					if($Normal_OS_PoorStudy == 1) $osData .= 'Poor Study&nbsp;<br>';

					if($BorderLineDefect_OS_T == 1 || $BorderLineDefect_OS_1 == 1 || $BorderLineDefect_OS_2 == 1 || $BorderLineDefect_OS_3 == 1 || $BorderLineDefect_OS_4 == 1 || $NoSigChange_OD == 1 || $Improved_OD == 1 || $IncAbn_OD == 1){
						$osData.= 'Border Line Defect';
						$osData.= '&nbsp;';
						if($BorderLineDefect_OS_T == 1) $osData.= '&nbsp;T&nbsp;';
						if($BorderLineDefect_OS_1 == 1) $osData.= '&nbsp;+1&nbsp;';
						if($BorderLineDefect_OS_2 == 1) $osData.= '&nbsp;+2&nbsp;';
						if($BorderLineDefect_OS_3 == 1) $osData.= '&nbsp;+3&nbsp;';
						if($BorderLineDefect_OS_4 == 1) $osData.= '&nbsp;+4&nbsp;';
					}
					if($Abnorma_OS_T == 1 || $Abnorma_OS_1 == 1 || $Abnorma_OS_2 == 1 || $Abnorma_OS_3 == 1 || $Abnorma_OS_4 == 1 || $NoSigChange_OS == 1 || $Improved_OS == 1 || $IncAbn_OS == 1){
						$osData.= '<br>Abnormal';
						$osData.= '&nbsp;';
						if($Abnorma_OS_T == 1) $osData.= '&nbsp;T&nbsp;';
						if($Abnorma_OS_1 == 1) $osData.= '&nbsp;+1&nbsp;';
						if($Abnorma_OS_2 == 1) $osData.= '&nbsp;+2&nbsp;';
						if($Abnorma_OS_3 == 1) $osData.= '&nbsp;+3&nbsp;';
						if($Abnorma_OS_4 == 1) $osData.= '&nbsp;+4&nbsp;';
					}
					
					if(!empty($decreaseOs)){
						$osData.= '<br/>Decreased<br/>';
						//$osData.= '&nbsp;';
						$osData.=str_replace(",","<br/>",$decreaseOs);						
					}
					
					if(!empty($thinOs)){
						$osData.= '<br>Thin';
						$osData.= '&nbsp;'.$thinOs;
					}
					
					if(!empty($totalThinOs)){
						$osData.= '<br>Total Thin';
						$osData.= '&nbsp;'.$totalThinOs;
					}

					if($NoSigChange_OS == 1||$Improved_OS == 1||$IncAbn_OS == 1){
						$osData.= '<br/>';
						if($NoSigChange_OS == 1) $osData.= 'No Sig. Change&nbsp;';
						if($Improved_OS == 1) $osData.= 'Improved&nbsp;';
						if($IncAbn_OS == 1	) $osData.= 'Inc. Abn&nbsp;';
					}
					
					if($iopTrgtOs){
						$osData.= '<br>IOP Target';
						$osData.= '&nbsp;'.$iopTrgtOs;
					}	

				if($osData!="" || $odData!="" || $Others_OD!="" || $Others_OS!=""){
						?>
						<tr>
							<td style="width:700px;" class="bdrbtm tb_subheading" colspan="2">Test Results</td>
						</tr>
						<tr>
							<td style="width:380px;" class="bdrbtm"><?php odLable();?></td>
							<td style="width:350px;" class="bdrbtm"><?php osLable();?></td>
						</tr>
					<?php	
						if($odData || $osData){
						?>
						<tr>
							<td style="width:380px;" class="bdrbtm" ><?php echo  $odData; ?></td>
							<td style="width:350px;" class="bdrbtm"><?php echo  $osData; ?></td>
						</tr>
					<?php
						}	
					if($Others_OD!="" || $Others_OS!=""){
						?>
							<tr>
								<td style="width:380px;" class="bdrbtm"><?php echo $Others_OD; ?></td>
								<td style="width:350px;" class="bdrbtm"><?php echo $Others_OS; ?></td>
							</tr>
						<?php				
					}
					?>
					
						<?php
					}
				if($stable == 1) $treatment.= 'Stable&nbsp;&nbsp;';
					if($fuApa == 1) $treatment.= 'F/U APA&nbsp;&nbsp;';
					if($ptInformed == 1) $treatment.= 'Pt informed of results &nbsp;';
					if($monitorIOP == 1) $treatment.= 'monitor IOP &nbsp;';
					if($tech2InformPt == 1) $treatment.= 'Tech to Inform Pt.';
					
					if($stable == 1 || $fuApa == 1 || $ptInformed == 1 || $monitorIOP == 1 || $tech2InformPt == 1){
						?>
						
							<tr>
								<td style="width:700px;" class="bdrbtm" colspan="2"><b>Treatment/Prognosis:</b>&nbsp;<?php echo $treatment; ?></td>
							</tr>
						
						<?php
					}
				if($comments){
					?>
					
						<tr>
							<td style="width:700px;" class="bdrbtm" colspan="2"><b>Comments:</b>&nbsp;<?php echo $comments; ?></td>
						</tr>
						<?php
					}///Add OCT Images//
					$imagesHtml=getTestImages($nfa_id,$sectionImageFrom="NFA",$patient_id);

					//GET IMAGES FOR PDFs THAT ARE HIGHER THAN PDF VERSION 1.4 . HIGHER VERSION IMAGES ARE SKIPPED IN merge_pdf.php FILE.
					$arr_alternative_images=array();
					$str_alternative_images='';
					if($nfa_id!=""){
						$arrPDFs = getAllTestPdf($nfa_id);
						$str_alternative_images= checknMakeImagesHTML($arrPDFs);
						$imagesHtml.=$str_alternative_images;
					}	
					//------------------------------------
										
					if($imagesHtml!=""){
						echo('<tr><td colspan="2" style="width:700px; text-align:center;" class="bdrbtm"><table style="width:700px;text-align:center;" cellspacing="0" cellpadding="0">
							 	'.$imagesHtml.' 
							</table></td></tr>');
					} 
					$imagesHtml="";
					//End NFA Images
					if($phyName){
						/*$getNameQry = imw_query("SELECT CONCAT_WS(', ', lname, fname) as phyName FROM users WHERE id = '$phyName'");	
						$getNameRow = imw_fetch_assoc($getNameQry);
						$phyName = str_replace(", ,"," ",$getNameRow['phyName']);*/
						$phyName = print_phyInitial($phyName);
						?>
						<tr>
							<td style="width:700px;" class="bdrbtm" colspan="2"><span class="text_lable">Interpreted By:&nbsp;</span> <?php echo $phyName; ?></td>
						</tr>
						<?php
					}
					?>
				</table>
		<?php
	}
	}else{ ?>
        <table style="width:740px;" class="border" cellpadding="0" cellspacing="0">
                    <tr>
                        <td class="tb_heading">HRT</td>
                    </tr>
                    <tr>
                        <td>No Data</td>
                    </tr>		
               </table>  
        <?php 
        }
}


function print_oct($patient_id,$form_id,$req){
	if(is_array($req) && count($req)>0){
		$sql = "SELECT * FROM oct WHERE patient_id = '".$patient_id."' AND oct_id in(".implode(",",$req).") ";	
	}else if($form_id!=""){
		$sql = "SELECT * FROM oct WHERE patient_id = '".$patient_id."' AND form_id = '$form_id' AND purged='0' ";	
	}
	$rowOct = imw_query($sql);
	if($rowOct){
while($row=imw_fetch_array($rowOct)){
		extract($row);
	?>				
					<table style="width:750px;" class="border" cellpadding="0" cellspacing="0" >
						<tr>
							<td colspan="2" style="width:750px;" class="tb_heading">OCT:&nbsp; (<span class="text_value">Exam Date:&nbsp;<?php print get_date_format($examDate);?></span>)</td>
						</tr>
						
					
	<?php				
					if($scanLaserEye){
						if($scanLaserEye == 'OU') $scanLaserEye = '<span style="color:purple;">'.$scanLaserEye.'</span>';
						if($scanLaserEye == 'OD') $scanLaserEye = '<span style="color:blue;">'.$scanLaserEye.'</span>';
						if($scanLaserEye == 'OS') $scanLaserEye = '<span style="color:green;">'.$scanLaserEye.'</span>';
						
						if($scanLaserOct=="3"){
							$scanLaserOct = "Anterior Segment";
						}else if($scanLaserOct=="2"){
							$scanLaserOct = "Retina";
						}else if($scanLaserOct=="1"){
							$scanLaserOct = "Optic Nerve";
						}
						
						?>					
					
					<tr>
						<td colspan="2" style="width:700px;" class="bdrbtm" >
						<b>OCT</b>&nbsp;<?php echo($scanLaserOct." ".$scanLaserEye); ?>
						</td>
					</tr>
					
						<?php
					}
					if($performBy || $ptUndersatnding){
						/*$performedByQry = imw_query("SELECT CONCAT_WS(', ', lname, fname) as performedBy FROM users WHERE id = '$performBy'");	
						$performedByRow = imw_fetch_assoc($performedByQry);
						$performedBy = str_replace(", ,"," ",$performedByRow['performedBy']);*/
						$performedBy = print_phyInitial($performBy);
						?>
					
						<?php if($techComments!=""){
						?>
						<tr>
							<td colspan="2" style="width:700px;" class="bdrbtm"><b>Technician Comments:</b>&nbsp;<?php echo($techComments); ?></td>
						</tr>
						<?php }?>
						<tr>
							<td style="width:350px;" class="bdrbtm"><span class="text_lable">Performed By:&nbsp;</span> <?php echo $performedBy; ?></td>
							<td style="width:350px;" class="bdrbtm"><span class="text_lable">Patient Understanding & Cooperation</span>:&nbsp; <?php echo $ptUndersatnding; ?></td>
						</tr>
					
						<?php
					}
					if($diagnosis && $diagnosis!="--Select--"){if(trim(strtolower($diagnosis))=="other"){ $diagnosis=$diagnosisOther;}
						?>
					
						<tr>
							<td colspan="2" style="width:750px;" class="bdrbtm"><span class="text_lable">Diagnosis:&nbsp;</span> <?php echo ($diagnosis!="--Select--") ? $diagnosis : ""; ?></td>
						</tr>
					
						<?php
					}
					if($reliabilityOd || $reliabilityOs){
						?>
						
				<tr>
                	<td colspan="2" style="width:700px;">
					<table style="width:700px;" cellpadding="0" cellspacing="0">
						<tr>	
							<td style="width:180px;" class="text_lable bdrbtm">Physician Interpretation:&nbsp;</td>						
							<td style="width:80px;" class="text_lable bdrbtm">Reliability</td>
							<td style="width:50px;" class="text_lable bdrbtm"><?php odLable();?></td>
							<td style="width:150px;" class="text_value bdrbtm"><?php echo $reliabilityOd; ?></td>
							<td style="width:50px;" class="bdrbtm text_lable"><?php osLable();?></td>
							<td style="width:200px;" class="bdrbtm text_value"><?php echo $reliabilityOs; ?></td>
						</tr>
					</table>
					</td>
                   </tr> 		
						<?php
					}
					$odData = '';
					$osData = '';
					
					// OD DATA
					if($Normal_OD_T) $odData = 'Normal&nbsp;<br>';
					if($Normal_OD_PoorStudy == 1) $odData .= 'Poor Study&nbsp;<br>';
					if($BorderLineDefect_OD_T == 1 || $BorderLineDefect_OD_1 == 1 || $BorderLineDefect_OD_2 == 1 || $BorderLineDefect_OD_3 == 1 || $BorderLineDefect_OD_4 == 1){
						$odData.= 'Border Line Defect';
						$odData.= '&nbsp;';
						if($BorderLineDefect_OD_T == 1) $odData.= '&nbsp;T&nbsp;';
						if($BorderLineDefect_OD_1 == 1) $odData.= '&nbsp;+1&nbsp;';
						if($BorderLineDefect_OD_2 == 1) $odData.= '&nbsp;+2&nbsp;';
						if($BorderLineDefect_OD_3 == 1) $odData.= '&nbsp;+3&nbsp;';
						if($BorderLineDefect_OD_4 == 1) $odData.= '&nbsp;+4&nbsp;';
					}
					if($Abnorma_OD_T == 1 || $Abnorma_OD_1 == 1 || $Abnorma_OD_2 == 1 || $Abnorma_OD_3 == 1 || $Abnorma_OD_4 == 1){
						$odData.= '<br>Abnormal';
						$odData.= '&nbsp;';
						if($Abnorma_OD_T == 1) $odData.= '&nbsp;T&nbsp;';
						if($Abnorma_OD_1 == 1) $odData.= '&nbsp;+1&nbsp;';
						if($Abnorma_OD_2 == 1) $odData.= '&nbsp;+2&nbsp;';
						if($Abnorma_OD_3 == 1) $odData.= '&nbsp;+3&nbsp;';
						if($Abnorma_OD_4 == 1) $odData.= '&nbsp;+4&nbsp;';
						if($NoSigChange_OD == 1) $odData.= '<br/>No Sig. Change&nbsp;';
						if($Improved_OD == 1) $odData.= 'Improved&nbsp;';
						if($IncAbn_OD == 1) $odData.= 'Inc. Abn&nbsp;';
						
					}
	 
					// OS DATA
					if($Normal_OS_T) $osData = 'Normal&nbsp;<br>';
					if($Normal_OS_PoorStudy == 1) $osData .= 'Poor Study&nbsp;<br>';

					if($BorderLineDefect_OS_T == 1 || $BorderLineDefect_OS_1 == 1 || $BorderLineDefect_OS_2 == 1 || $BorderLineDefect_OS_3 == 1 || $BorderLineDefect_OS_4 == 1 || $NoSigChange_OD == 1 || $Improved_OD == 1 || $IncAbn_OD == 1){
						$osData.= 'Border Line Defect';
						$osData.= '&nbsp;';
						if($BorderLineDefect_OS_T == 1) $osData.= '&nbsp;T&nbsp;';
						if($BorderLineDefect_OS_1 == 1) $osData.= '&nbsp;+1&nbsp;';
						if($BorderLineDefect_OS_2 == 1) $osData.= '&nbsp;+2&nbsp;';
						if($BorderLineDefect_OS_3 == 1) $osData.= '&nbsp;+3&nbsp;';
						if($BorderLineDefect_OS_4 == 1) $osData.= '&nbsp;+4&nbsp;';
					}
					if($Abnorma_OS_T == 1 || $Abnorma_OS_1 == 1 || $Abnorma_OS_2 == 1 || $Abnorma_OS_3 == 1 || $Abnorma_OS_4 == 1 || $NoSigChange_OS == 1 || $Improved_OS == 1 || $IncAbn_OS == 1){
						$osData.= '<br>Abnormal';
						$osData.= '&nbsp;';
						if($Abnorma_OS_T == 1) $osData.= '&nbsp;T&nbsp;';
						if($Abnorma_OS_1 == 1) $osData.= '&nbsp;+1&nbsp;';
						if($Abnorma_OS_2 == 1) $osData.= '&nbsp;+2&nbsp;';
						if($Abnorma_OS_3 == 1) $osData.= '&nbsp;+3&nbsp;';
						if($Abnorma_OS_4 == 1) $osData.= '&nbsp;+4&nbsp;';
						if($NoSigChange_OS == 1) $osData.= '<br/>No Sig. Change&nbsp;';
						if($Improved_OS == 1) $osData.= 'Improved&nbsp;';
						if($IncAbn_OS == 1) $osData.= 'Inc. Abn&nbsp;';
					}
				if($osData!="" || $odData!="" || $Others_OD!="" || $Others_OS!="" || $fovea_thick_OD!="" || $fovea_thick_OS!="" || $test_res_od!="" || $test_res_os!="" || $avg_nfl_Thick_OD!="" || $avg_nfl_Thick_OS!="") {
						?>
					
						<tr>
							<td class="tb_subheading bdrbtm" colspan="2">Test Results</td>
						</tr>
						<tr>
							<td style="width:350px;" class="bdrbtm"><?php odLable();?></td>
							<td style="width:350px;" class="bdrbtm"><?php osLable();?></td>
						</tr>
					<?php	
						if($odData || $osData){
						?>
						<tr>
							<td style="width:350px;" class="bdrbtm"><?php echo  $odData; ?></td>
							<td style="width:350px;" class="bdrbtm"><?php echo  $osData; ?></td>
						</tr>
					<?php
						}	
					?>
					<?php
					if($test_res_od!="" || $test_res_os!="" ){
						?>
							<tr>
								<td style="width:350px;" class="bdrbtm"><?php echo $test_res_od; ?></td>
								<td style="width:350px;" class="bdrbtm"><?php echo $test_res_os; ?></td>
							</tr>
						
						<?php				
					}
					?>
					
					<?php
					if($fovea_thick_OD!="" || $fovea_thick_OS!="" ){
						?>
							<tr>
							<td style="width:350px;" class="bdrbtm"><?php echo "Foveal Thickness &nbsp;".$fovea_thick_OD; ?></td>
							<td style="width:350px;" class="bdrbtm"><?php echo "Foveal Thickness &nbsp;".$fovea_thick_OS; ?></td>							
							</tr>
						<?php				
					}
					
					if($avg_nfl_Thick_OD!="" || $avg_nfl_Thick_OS!="" ){
						?>
							<tr>
								<td style="width:350px;" class="bdrbtm"><?php echo "AVG NFL Thickness&nbsp;".$avg_nfl_Thick_OD; ?></td>
								<td style="width:350px;" class="bdrbtm"><?php echo "AVG NFL Thickness&nbsp;".$avg_nfl_Thick_OS; ?></td>
							</tr>
						
						<?php				
					}
					
						if($iopTrgtOd !="" || $iopTrgtOs !="" ){
						?>
							<tr>								
								<td style="width:350px;" class="bdrbtm"><?php echo "IOP Comments&nbsp;".$iopTrgtOd; ?></td>
								<td style="width:350px;" class="bdrbtm"><?php echo "IOP Comments&nbsp;".$iopTrgtOs; ?></td>
							</tr>						
						<?php				
					}
					?>
					
					<?php
					if($Others_OD!="" || $Others_OS!=""){
						?>
							<tr>
								<td style="width:350px;" class="bdrbtm"><?php echo $Others_OD; ?></td>
								<td style="width:350px;" class="bdrbtm"><?php echo $Others_OS; ?></td>
							</tr>
						<?php				
					}
					?>
					
					
						<?php
					}
					
					if($stable == 1) $treatment.= 'Stable&nbsp;&nbsp;';
					if($contiMeds == 1) $treatment.= '&nbsp;Continue Meds &nbsp;';
					if($monitorIOP == 1) $treatment.= 'Monitor IOP &nbsp;';
					if($mon_finding == 1) $treatment.= 'Monitor findings &nbsp;';
					if($fuApa == 1) $treatment.= 'F/U APA&nbsp;';
					if($tech2InformPt == 1) $treatment.= 'Tech to Inform Pt.&nbsp;';
					if($ptInformed == 1) $treatment.= ' Pt informed of results &nbsp;';
					if($ptInformedNv==1)$treatment.= '&nbsp;Informed Pt. result next visit.&nbsp;';
					
					if($stable == 1 || $fuApa == 1 || $ptInformed == 1 || $monitorIOP == 1 || $mon_finding == 1 || $tech2InformPt == 1){
						?>
						
							<tr>
								<td colspan="2" style="width:700px;" class="bdrbtm"><b>Treatment/Prognosis:</b>&nbsp;<?php echo $treatment; ?></td>
							</tr>
						
						<?php
					}
					if($comments){
						?>
					
							<tr>
								<td colspan="2" style="width:700px;" class="bdrbtm"><b>Comments:</b>&nbsp;<?php echo $comments; ?></td>
							</tr>
						
						<?php
					}
					?>
					
					<?php
					///Add OCT Images//
					$imagesHtml=getTestImages($oct_id,$sectionImageFrom="OCT",$patient_id);
					
					//GET IMAGES FOR PDFs THAT ARE HIGHER THAN PDF VERSION 1.4 . HIGHER VERSION IMAGES ARE SKIPPED IN merge_pdf.php FILE.
					$arr_alternative_images=array();
					$str_alternative_images='';
					if($oct_id!=""){
						$arrPDFs = getAllTestPdf($oct_id);
						$str_alternative_images= checknMakeImagesHTML($arrPDFs);
						$imagesHtml.=$str_alternative_images;
					}	
					//------------------------------------					
					
					if($imagesHtml!=""){
						echo('<tr><td colspan="2"><table >
							 	'.$imagesHtml.' 
							</table></td></tr>');
					} 
					$imagesHtml="";
					//End OCT Images
					
					if($phyName){
						/*$getNameQry = imw_query("SELECT CONCAT_WS(', ', lname, fname) as phyName FROM users WHERE id = '$phyName'");	
						$getNameRow = imw_fetch_assoc($getNameQry);
						$phyName = str_replace(", ,"," ",$getNameRow['phyName']);*/
						$phyName = print_phyInitial($phyName);
						?>
						
							<tr>
								<td colspan="2"><span class="text_lable">Interpreted By:&nbsp;</span> <?php echo $phyName; ?></td>
							</tr>
						
						<?php
					}
					?>
				</table>
		<?php
	}//End oct While
 }else{ ?>
    <table style="width:740px;" class="border" cellpadding="0" cellspacing="0">
                <tr>
                    <td class="tb_heading">OCT</td>
                </tr>
                <tr>
                    <td>No Data</td>
                </tr>		
           </table>  
    <?php 
    }
}

function print_oct_rnfl($patient_id,$form_id,$req){
	
	if(is_array($req) && count($req)>0){
		$sql = "SELECT * FROM oct_rnfl WHERE patient_id = '".$patient_id."' AND oct_rnfl_id  in(".implode(",",$req).") ";	
	}else if($form_id!=""){
		$sql = "SELECT * FROM oct_rnfl WHERE patient_id = '".$patient_id."' AND form_id = '$form_id' AND purged='0' ";	
	}
	$rowOct = imw_query($sql);
	if($rowOct){
while($row=imw_fetch_array($rowOct)){
		extract($row);
	?>				
					<table style="width:750px;" class="border" cellpadding="0" cellspacing="0" >
						<tr>
							<td colspan="3" style="width:750px;" class="tb_heading">OCT-RNFL:&nbsp; (<span class="text_value">Exam Date:&nbsp;<?php print get_date_format($examDate);?></span>)</td>
						</tr>
						
					
	<?php				
					if($scanLaserEye){
						if($scanLaserEye == 'OU') $scanLaserEye = '<span style="color:purple;">'.$scanLaserEye.'</span>';
						if($scanLaserEye == 'OD') $scanLaserEye = '<span style="color:blue;">'.$scanLaserEye.'</span>';
						if($scanLaserEye == 'OS') $scanLaserEye = '<span style="color:green;">'.$scanLaserEye.'</span>';
						
						if($scanLaserOct_rnfl=="3"){
							$scanLaserOct_rnfl = "Anterior Segment";
						}else if($scanLaserOct_rnfl =="2"){
							$scanLaserOct_rnfl = "Retina";
						}else if($scanLaserOct_rnfl =="1"){
							$scanLaserOct_rnfl = "Optic Nerve";
						}
						
						?>					
					
					<tr>
						<td colspan="2" style="width:400px;" class="bdrbtm" >
						<b>OCT - Optic Nerve / RNFL</b>&nbsp;<?php echo($scanLaserEye); ?>
						</td>
                        <td style="width:300px;" class="bdrbtm" >
                        Dilated&nbsp;<?php echo $dilated; ?>
                        </td>
					</tr>
					
						<?php
					}
					if($performBy || $ptUndersatnding){
						/*$performedByQry = imw_query("SELECT CONCAT_WS(', ', lname, fname) as performedBy FROM users WHERE id = '$performBy'");	
						$performedByRow = imw_fetch_assoc($performedByQry);
						$performedBy = str_replace(", ,"," ",$performedByRow['performedBy']);*/
						$performedBy = print_phyInitial($performBy);
						?>
					
						<?php if($techComments!=""){
						?>
						<tr>
							<td colspan="3" style="width:700px;" class="bdrbtm"><b>Technician Comments:</b>&nbsp;<?php echo($techComments); ?></td>
						</tr>
						<?php }?>
						<tr>
							<td colspan="2" style="width:400px;" class="bdrbtm"><span class="text_lable">Performed By:&nbsp;</span> <?php echo $performedBy; ?></td>
							<td style="width:300px;" class="bdrbtm"><span class="text_lable">Patient Understanding & Cooperation</span>:&nbsp; <?php echo $ptUndersatnding; ?></td>
						</tr>
					
						<?php
					}
					
					if($diagnosis=="Other"&&!empty($diagnosisOther)){$diagnosis=$diagnosisOther;}
					if($diagnosis_os=="Other"&&!empty($diagnosisOther_os)){$diagnosis_os=$diagnosisOther_os;}
					
					if(($diagnosis && $diagnosis!="--Select--") ||($diagnosis_os && $diagnosis_os!="--Select--")){
						?>
					
						<tr>
							
                           <?php 
						    if($diagnosis && $diagnosis!="--Select--"){ ?>
                            <td colspan="2" style="width:400px;" class="bdrbtm">
                            	<b>Diagnosis OD:</b>&nbsp;<?php echo ($diagnosis!="--Select--") ? $diagnosis : ""; ?>
							</td>	
							<?php
                            } 
							if($diagnosis_os && $diagnosis_os!="--Select--"){ ?>
                            <td style="width:300px;" class="bdrbtm">
	                            <b>Diagnosis OS:</b>&nbsp;
                             <?php echo $diagnosis_os; ?>
						    </td>
							<?php	} ?>
						</tr>
					<?php } ?>
					
					<tr><td colspan="3" style="width:700px;font-weight:bold;" class="bdrbtm">Physician Interpretation</td></tr>
					<tr><td colspan="3" style="width:700px;font-weight:bold;" class="bdrbtm">Test Results</td></tr>
	 				
                    <tr>
                    	<td style="width:100px;" class="bdrbtm tb_subheading">&nbsp;</td>
                    	<td style="width:300px;font-size:14px;font-weight:bold;" class="bdrbtm tb_subheading bdrright">&nbsp;OD</td>
                        <td style="width:300px;font-size:14px;font-weight:bold;" class="bdrbtm tb_subheading">&nbsp;OS</td>
                    </tr>
                    <?php
					if($reliabilityOd || $reliabilityOs){
					?>
                    <tr>
                    	<td style="width:100px;font-weight:bold;" class="bdrbtm bdrright">Reliability:</td>
                        <td style="width:300px;" class="bdrbtm bdrright"><?php echo $reliabilityOd;?></td>
                        <td style="width:300px;" class="bdrbtm"><?php echo $reliabilityOs;?></td>
                    </tr>
					<?php
					}
					if($signal_strength_od || $signal_strength_os){
					?>
                    <tr>
                    	<td style="width:100px;font-weight:bold;" class="bdrbtm bdrright">Signal&nbsp;Strength:</td>
                        <td style="width:300px;" class="bdrbtm bdrright"><?php echo $signal_strength_od;?></td>
                        <td style="width:300px;" class="bdrbtm"><?php echo $signal_strength_od;?></td>
                    </tr>
					<?php	
					}
					if($quality_od || $quality_os){
					?>
                    <tr>
                    	<td style="width:100px;font-weight:bold;" class="bdrbtm bdrright">Quality:</td>
                        <td style="width:300px;" class="bdrbtm bdrright"><?php echo $quality_od;?></td>
                        <td style="width:300px;" class="bdrbtm"><?php echo $quality_os;?></td>
                    </tr>
					<?php	
					}
					if($details_od || $details_os){
					?>
                    <tr>
                    	<td style="width:100px;font-weight:bold;" class="bdrbtm bdrright">Details:</td>
                        <td style="width:300px;" class="bdrbtm bdrright"><?php echo str_ireplace("!","",$details_od);?></td>
                        <td style="width:300px;" class="bdrbtm"><?php echo str_ireplace("!","",$details_od);?></td>
                    </tr>
					<?php	
					}
					if($disc_area_od || $disc_area_os){
					?>
                    <tr>
                    	<td style="width:100px;font-weight:bold;" class="bdrbtm bdrright">Disc area:</td>
                        <td style="width:300px;" class="bdrbtm bdrright"><?php echo $disc_area_od;?></td>
                        <td style="width:300px;" class="bdrbtm"><?php echo $disc_area_os;?></td>
                    </tr>
					<?php	
					}
					if($disc_size_od || $disc_size_os){
					?>
                    <tr>
                    	<td style="width:100px;font-weight:bold;" class="bdrbtm bdrright">Disc size:</td>
                        <td style="width:300px;" class="bdrbtm bdrright"><?php echo $disc_size_od;?></td>
                        <td style="width:300px;" class="bdrbtm"><?php echo $disc_size_os;?></td>
                    </tr>
					<?php	
					}
					if($verti_cd_od || $verti_cd_os){
					?>
                    <tr>
                    	<td style="width:100px;font-weight:bold;" class="bdrbtm bdrright">Vertical C:D:</td>
                        <td style="width:300px;" class="bdrbtm bdrright"><?php echo $verti_cd_od;?></td>
                        <td style="width:300px;" class="bdrbtm"><?php echo $verti_cd_os;?></td>
                    </tr>
					<?php	
					}
					if($disc_edema_od || $disc_edema_os){
					?>
                    <tr>
                    	<td style="width:100px;font-weight:bold;" class="bdrbtm bdrright">Disc edema:</td>
                        <td style="width:300px;" class="bdrbtm bdrright"><?php echo $disc_edema_od;?></td>
                        <td style="width:300px;" class="bdrbtm"><?php echo $disc_edema_os;?></td>
                    </tr>
					<?php	
					}
					if($rnfl_od || $rnfl_od){
					?>
                    <tr>
                    	<td style="width:100px;font-weight:bold;" class="bdrbtm bdrright">RNFL:</td>
                        <td style="width:300px;" class="bdrbtm bdrright">
						<?php echo ($rnfl_od_val=($rnfl_od)?"Avg&nbsp;".$rnfl_od:"&nbsp;"); ?>
                        </td>
                        <td style="width:300px;" class="bdrbtm">
						<?php echo ($rnfl_od_val=($rnfl_os)?"Avg&nbsp;".$rnfl_os:"&nbsp;"); ?>
                        </td>
                    </tr>
					<?php	
					}
					
                   	if($contour_overall_od || $contour_overall_os){
					?>
                    <tr>
                    	<td style="width:100px;font-weight:bold;" class="bdrbtm bdrright">Overall:</td>
                        <td style="width:300px;" class="bdrbtm bdrright">
						<?php echo ($contour_overall_od); ?>
                        </td>
                        <td style="width:300px;" class="bdrbtm">
						<?php echo ($contour_overall_os); ?>
                        </td>
                    </tr>
					<?php	
					}
					if($contour_superior_od || $contour_superior_os){
					?>
                    <tr>
                    	<td style="width:100px;font-weight:bold;" class="bdrbtm bdrright">Superior:</td>
                        <td style="width:300px;" class="bdrbtm bdrright">
						<?php echo ($contour_superior_od); ?>
                        </td>
                        <td style="width:300px;" class="bdrbtm">
						<?php echo ($contour_overall_os); ?>
                        </td>
                    </tr>
					<?php	
					}
					if($contour_inferior_od  || $contour_inferior_os){
					?>
                    <tr>
                    	<td style="width:100px;font-weight:bold;" class="bdrbtm bdrright">Inferior:</td>
                        <td style="width:300px;" class="bdrbtm bdrright">
						<?php echo ($contour_inferior_od); ?>
                        </td>
                        <td style="width:300px;" class="bdrbtm">
						<?php echo ($contour_inferior_os); ?>
                        </td>
                    </tr>
					<?php	
					}
					if($contour_temporal_od  || $contour_temporal_os){
					?>
                    <tr>
                    	<td style="width:100px;font-weight:bold;" class="bdrbtm bdrright">Temporal:</td>
                        <td style="width:300px;" class="bdrbtm bdrright">
						<?php echo ($contour_temporal_od); ?>
                        </td>
                        <td style="width:300px;" class="bdrbtm">
						<?php echo ($contour_temporal_os); ?>
                        </td>
                    </tr>
					<?php	
					}
					if($contour_nasal_od  || $contour_nasal_os){
					?>
                    <tr>
                    	<td style="width:100px;font-weight:bold;" class="bdrbtm bdrright">Nasal:</td>
                        <td style="width:300px;" class="bdrbtm bdrright">
						<?php echo ($contour_nasal_od); ?>
                        </td>
                        <td style="width:300px;" class="bdrbtm">
						<?php echo ($contour_nasal_os); ?>
                        </td>
                    </tr>
					<?php	
					}
					if($contour_gcc_od  || $contour_gcc_os){
					?>
                    <tr>
                    	<td style="width:100px;font-weight:bold;" class="bdrbtm bdrright">GCC:</td>
                        <td style="width:300px;" class="bdrbtm bdrright">
						<?php echo ($contour_gcc_od); ?>
                        </td>
                        <td style="width:300px;" class="bdrbtm">
						<?php echo ($contour_gcc_os); ?>
                        </td>
                    </tr>
					<?php	
					}
					if($symmetric_od  || $symmetric_os){
					?>
                    <tr>
                    	<td style="width:100px;font-weight:bold;" class="bdrbtm bdrright">Symmetric:</td>
                        <td style="width:300px;" class="bdrbtm bdrright">
						<?php echo ($symmetric_od); ?>
                        </td>
                        <td style="width:300px;" class="bdrbtm">
						<?php echo ($symmetric_os); ?>
                        </td>
                    </tr>
					<?php	
					}
					if($gpa_od  || $gpa_os){
					?>
                    <tr>
                    	<td style="width:100px;font-weight:bold;" class="bdrbtm bdrright">GPA:</td>
                        <td style="width:300px;" class="bdrbtm bdrright">
						<?php echo ($gpa_od); ?>
                        </td>
                        <td style="width:300px;" class="bdrbtm">
						<?php echo ($gpa_os); ?>
                        </td>
                    </tr>
					<?php
					}
					if($synthesis_od  || $synthesis_os){
					?>
                    <tr>
                    	<td style="width:100px;font-weight:bold;" class="bdrbtm bdrright">Synthesis:</td>
                        <td style="width:300px;" class="bdrbtm bdrright">
						<?php echo ($synthesis_od); ?>
                        </td>
                        <td style="width:300px;" class="bdrbtm">
						<?php echo ($synthesis_os); ?>
                        </td>
                    </tr>
					<?php	
					}
					if($interpretation_OD  || $interpretation_OS){
					?>
                    <tr>
                    	<td style="width:100px;font-weight:bold;" class="bdrbtm bdrright">Interpretation:</td>
                        <td style="width:300px;" class="bdrbtm bdrright">
						<?php echo ($interpretation_OD); ?>
                        </td>
                        <td style="width:300px;" class="bdrbtm">
						<?php echo ($interpretation_OS); ?>
                        </td>
                    </tr>
					<?php	
					}
					if($glaucoma_stage_opt_OD  || $glaucoma_stage_opt_OS){
					?>
                    <tr>
                    	<td style="width:100px;font-weight:bold;" class="bdrbtm bdrright">Glaucoma&nbsp;Stage:</td>
                        <td style="width:300px;" class="bdrbtm bdrright">
						<?php echo ($glaucoma_stage_opt_OD); ?>
                        </td>
                        <td style="width:300px;" class="bdrbtm">
						<?php echo ($glaucoma_stage_opt_OS); ?>
                        </td>
                    </tr>
					<?php	
					}
					if($plan){
					?>
                    <tr>
                    	<td style="width:100px;font-weight:bold;" class="bdrbtm">Plan:</td>
                        <td colspan="2" style="width:600px;" class="bdrbtm">
						<?php 
						$arr_find=array("Pt informed of results by physician today","to be called by technician","by letter","will inform next visit","Continue meds");
						$arr_rep=array("Pt informed of results by physician today, ","to be called by technician, ","by letter, ","will inform next visit, ","Continue meds, ");
						echo str_replace($arr_find,$arr_rep,$plan); ?>
                        </td>
                    </tr>
					<?php	
					}
					if($comments){
					?>
                    <tr>
                    	<td style="width:100px;font-weight:bold;" class="bdrbtm">Comments:</td>
                        <td colspan="2" style="width:600px;" class="bdrbtm">
						<?php echo ($comments); ?>
                        </td>
                    </tr>
					<?php	
					}
					
					///Add OCT Images//
					$imagesHtml=getTestImages($oct_rnfl_id,$sectionImageFrom="OCT-RNFL",$patient_id);
					
					//GET IMAGES FOR PDFs THAT ARE HIGHER THAN PDF VERSION 1.4 . HIGHER VERSION IMAGES ARE SKIPPED IN merge_pdf.php FILE.
					$arr_alternative_images=array();
					$str_alternative_images='';
					if($oct_rnfl_id!=""){
						$arrPDFs = getAllTestPdf($oct_rnfl_id);
						$str_alternative_images= checknMakeImagesHTML($arrPDFs);
						$imagesHtml.=$str_alternative_images;
					}	
					//------------------------------------	
									
					if($imagesHtml!=""){
						echo('<tr><td colspan="3"  class="bdrbtm"><table >
							 	'.$imagesHtml.' 
							</table></td></tr>');
					} 
					$imagesHtml="";
					?>
                    
                    <?php
					if($phyName){
						/*$getNameQry = imw_query("SELECT CONCAT_WS(', ', lname, fname) as phyName FROM users WHERE id = '$phyName'");	
						$getNameRow = imw_fetch_assoc($getNameQry);
						$phyName = str_replace(", ,"," ",$getNameRow['phyName']);*/
						$phyName = print_phyInitial($phyName);
						?>
						
							<tr>
								<td colspan="3"><span class="text_lable">Interpreted By:&nbsp;</span> <?php echo $phyName; ?></td>
							</tr>
						
						<?php
					}
					?>
				</table>
		<?php
	}//End oct While
 }

}



function print_gdx($patient_id,$form_id,$req){
	if(is_array($req) && count($req)>0){
		$sql = "SELECT * FROM test_gdx WHERE patient_id = '".$patient_id."' AND gdx_id in(".implode(",",$req).") ";	
	}else if($form_id!=""){
		$sql = "SELECT * FROM test_gdx WHERE patient_id = '".$patient_id."' AND form_id = '$form_id' AND purged='0' ";	
	}
	$rowOct = imw_query($sql);
	if($rowOct){
while($row=imw_fetch_array($rowOct)){
		extract($row);
		?>
        <table style="width:750px;" class="border" cellpadding="0" cellspacing="0">
        <?php
		
					if($scanLaserEye){
						if($scanLaserEye == 'OU') $scanLaserEye = '<span style="color:purple;">'.$scanLaserEye.'</span>';
						if($scanLaserEye == 'OD') $scanLaserEye = '<span style="color:blue;">'.$scanLaserEye.'</span>';
						if($scanLaserEye == 'OS') $scanLaserEye = '<span style="color:green;">'.$scanLaserEye.'</span>';
						?>
						<tr>
							<td colspan="2" class="tb_heading">GDX:&nbsp; <?php echo $scanLaserEye; ?> (<span class="text_value">Exam Date:&nbsp;<?php print get_date_format($examDate);?></span>)</td>
						</tr>
						
						<?php
					}
					if($performBy || $ptUndersatnding){
						/*$performedByQry = imw_query("SELECT CONCAT_WS(', ', lname, fname) as performedBy FROM users WHERE id = '$performBy'");	
						$performedByRow = imw_fetch_assoc($performedByQry);
						$performedBy = str_replace(", ,"," ",$performedByRow['performedBy']);*/
						$performedBy = print_phyInitial($performBy);
						?>
					
						<?php if($techComments!=""){
						?>
						<tr>
							<td colspan="2" style="width:700px;" class="bdrbtm"><b>Technician Comments:</b>&nbsp;<?php echo($techComments); ?></td>
						</tr>
						<?php }?>
						<tr>
							<td style="width:360px;" class="bdrbtm"><span class="text_lable">Performed By:&nbsp;</span> <?php echo $performedBy; ?></td>
							<td style="width:360px;" class="bdrbtm"><span class="text_lable">Patient Understanding & Cooperation</span>:&nbsp; <?php echo $ptUndersatnding; ?></td>
						</tr>
					
						<?php
					}
					if($diagnosis=="Other"&&!empty($diagnosisOther)){$diagnosis=$diagnosisOther;}
					if($diagnosis && $diagnosis!="--Select--"){
						?>
						<tr>
							<td colspan="2" style="width:700px;" class="bdrbtm"><span class="text_lable">Diagnosis:&nbsp;</span> <?php echo ($diagnosis!="--Select--") ? $diagnosis : ""; ?></td>
						</tr>
						<?php
					}
					if($reliabilityOd || $reliabilityOs){
						?>
						
					<tr>
                    	<td style="width:740px;" colspan="2">
					<table style="width:700px;" cellpadding="0" cellspacing="0" >
						<tr>	
							<td style="width:180px;" class="text_lable bdrbtm">Physician Interpretation:&nbsp;</td>						
							<td style="width:80px;" class="text_lable bdrbtm">Reliability</td>
							<td style="width:50px;" class="text_lable bdrbtm"><?php odLable();?></td>
							<td style="width:100px;" class="text_value bdrbtm"><?php echo $reliabilityOd; ?></td>
							<td style="width:50px;" class="bdrbtm text_lable"><?php osLable();?></td>
							<td  style="width:150px;" class="bdrbtm text_value"><?php echo $reliabilityOs; ?></td>
						</tr>
					</table>
						</td>
                        </tr>	
						<?php
					}
					$odData = '';
					$osData = '';
					
					// OD DATA
					if(!empty($normal_OD)){ 						
						$odData .= (strpos($normal_OD, "Normal")!==false) ? 'Normal&nbsp;' : "" ;
						$odData .= (strpos($normal_OD, "Poor Study")!==false) ? 'Poor Study&nbsp;' : "" ;
						$odData .= "<br/>";
					}
					if(!empty($nf_Thick_OD)){						
						$odData .= 'Nerve Fiber Thickness Map <br/>';
						$odData .= (strpos($nf_Thick_OD, "Normal Appearing Nerve Fiber Layer")!==false) ? 'Normal Appearing Nerve Fiber Layer<br/>' : "" ;
						$odData .= (strpos($nf_Thick_OD, "Suspicious Nerve Fiber Layer Thinning")!==false) ? 'Suspicious Nerve Fiber Layer Thinning<br/>' : "" ;
						$odData .= (strpos($nf_Thick_OD, "Definite Nerve Fiber Layer Thinning")!==false) ? 'Definite Nerve Fiber Layer Thinning<br/>' : "" ;
					}
					if(!empty($quad_devi_OD)){
						$odData .= 'Quadrant Deviation Map Outside Normal <br/>';
						$odData .= (strpos($quad_devi_OD, "Superior Quardrant")!==false) ? 'Superior Quardrant<br/>' : "" ;
						$odData .= (strpos($quad_devi_OD, "Nasal Quadrant")!==false) ? 'Nasal Quadrant<br/>' : "" ;
						$odData .= (strpos($quad_devi_OD, "Temporal Quadrant")!==false) ? 'Temporal Quadrant<br/>' : "" ;
						$odData .= (strpos($quad_devi_OD, "Inferior Quadrant")!==false) ? 'Inferior Quadrant<br/>' : "" ;
					}	
					if(!empty($nf_Indic_OD)){
						$odData .= 'Nerve Fiber Indicator <br/>';
						$odData .= (strpos($nf_Indic_OD, "0-30 Normal (Low risk of Glaucoma)")!==false) ? '0-30 Normal (Low risk of Glaucoma)<br/>' : "" ;	
						$odData .= (strpos($nf_Indic_OD, "31-50 Borderline")!==false) ? '31-50 Borderline<br/>' : "" ;		
						$odData .= (strpos($nf_Indic_OD, "51+ (Abnormal risk of Glaucoma)")!==false) ? '51+ (Abnormal risk of Glaucoma)<br/>' : "" ;
					}	
	 
					// OS DATA
					if(!empty($normal_OS)){ 						
						$osData .= (strpos($normal_OS, "Normal")!==false) ? 'Normal&nbsp;' : "" ;
						$osData .= (strpos($normal_OS, "Poor Study")!==false) ? 'Poor Study&nbsp;' : "" ;
						$osData .= "<br/>";
					}
					if(!empty($nf_Thick_OS)){						
						$osData .= 'Nerve Fiber Thickness Map <br/>';
						$osData .= (strpos($nf_Thick_OS, "Normal Appearing Nerve Fiber Layer")!==false) ? 'Normal Appearing Nerve Fiber Layer<br/>' : "" ;
						$osData .= (strpos($nf_Thick_OS, "Suspicious Nerve Fiber Layer Thinning")!==false) ? 'Suspicious Nerve Fiber Layer Thinning<br/>' : "" ;
						$osData .= (strpos($nf_Thick_OS, "Definite Nerve Fiber Layer Thinning")!==false) ? 'Definite Nerve Fiber Layer Thinning<br/>' : "" ;
					}
					if(!empty($quad_devi_OS)){
						$osData .= 'Quadrant Deviation Map Outside Normal <br/>';
						$osData .= (strpos($quad_devi_OS, "Superior Quardrant")!==false) ? 'Superior Quardrant<br/>' : "" ;
						$osData .= (strpos($quad_devi_OS, "Nasal Quadrant")!==false) ? 'Nasal Quadrant<br/>' : "" ;
						$osData .= (strpos($quad_devi_OS, "Temporal Quadrant")!==false) ? 'Temporal Quadrant<br/>' : "" ;
						$osData .= (strpos($quad_devi_OS, "Inferior Quadrant")!==false) ? 'Inferior Quadrant<br/>' : "" ;
					}	
					if(!empty($nf_Indic_OS)){
						$osData .= 'Nerve Fiber Indicator <br/>';
						$osData .= (strpos($nf_Indic_OS, "0-30 Normal (Low risk of Glaucoma)")!==false) ? '0-30 Normal (Low risk of Glaucoma)<br/>' : "" ;	
						$osData .= (strpos($nf_Indic_OS, "31-50 Borderline")!==false) ? '31-50 Borderline<br/>' : "" ;		
						$osData .= (strpos($nf_Indic_OS, "51+ (Abnormal risk of Glaucoma)")!==false) ? '51+ (Abnormal risk of Glaucoma)<br/>' : "" ;
					}					
					
				if($osData!="" || $odData!="" || $Others_OD!="" || $Others_OS!="") {
						?>
					
						<tr>
							<td style="width:700px;" class="tb_subheading bdrbtm" colspan="2">Test Results</td>
						</tr>
						<tr>
							<td style="width:360px;" class="bdrbtm"><?php odLable();?></td>
							<td style="width:360px;" class="bdrbtm"><?php osLable();?></td>
						</tr>
					<?php	
						if($odData || $osData){
						?>
						<tr>
							<td style="width:360px;" class="bdrbtm"><?php echo  $odData; ?></td>
							<td style="width:360px;" class="bdrbtm"><?php echo  $osData; ?></td>
						</tr>
					<?php
						}	
					if($others_OD!="" || $others_OS!=""){
						?>
							<tr>
								<td style="width:360px;" class="bdrbtm"><?php echo $others_OD; ?></td>
								<td style="width:360px;" class="bdrbtm"><?php echo $others_OS; ?></td>
							</tr>
						<?php				
					}					
					?>
					
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
							<tr>
								<td style="width:740px;" class="bdrbtm" colspan="2"><b>Treatment/Prognosis:</b>&nbsp;<?php echo $treatment; ?></td>
							</tr>
						<?php
					}
					if($comments){
						?>
							<tr>
								<td style="width:740px;" class="bdrbtm" colspan="2" ><b>Comments:</b>&nbsp;<?php echo $comments; ?></td>
							</tr>
						<?php
					}					
					
					/*
					//if($iopTrgtOd !="" || $iopTrgtOs !="" ){
						?><table >
							<tr>
								<td   class="text_lable" colspan="2">IOP Comments</td>
							</tr>
							<tr>
								
								<td style="width:50%" ><?php echo $iopTrgtOd; ?></td>
								<td style="width:50%" ><?php echo $iopTrgtOs; ?></td>
							</tr>
						</table>
						<?php				
					//}
					*/
					
					///Add OCT Images//
					$imagesHtml=getTestImages($gdx_id,$sectionImageFrom="GDX",$patient_id);

					//GET IMAGES FOR PDFs THAT ARE HIGHER THAN PDF VERSION 1.4 . HIGHER VERSION IMAGES ARE SKIPPED IN merge_pdf.php FILE.
					$arr_alternative_images=array();
					$str_alternative_images='';
					if($gdx_id!=""){
						$arrPDFs = getAllTestPdf($gdx_id);
						$str_alternative_images= checknMakeImagesHTML($arrPDFs);
						$imagesHtml.=$str_alternative_images;
					}	
					//------------------------------------
										
					if($imagesHtml!=""){
						echo('<tr><td colspan="2"><table >
							 	'.$imagesHtml.' 
							</table></td></tr>');
					} 
					$imagesHtml="";
					//End OCT Images
					
					if($phyName){
						/*$getNameQry = imw_query("SELECT CONCAT_WS(', ', lname, fname) as phyName FROM users WHERE id = '$phyName'");	
						$getNameRow = imw_fetch_assoc($getNameQry);
						$phyName = str_replace(", ,"," ",$getNameRow['phyName']);*/
						$phyName = print_phyInitial($phyName);
						?>
							<tr>
								<td colspan="2" style="width:700px;" class="bdrbtm" ><span class="text_lable">Interpreted By:&nbsp;</span> <?php echo $phyName; ?></td>
							</tr>
						<?php
					}
					?>
				</table>
		<?php
	}//End oct While
 }else{ ?>
    <table style="width:740px;" class="border" cellpadding="0" cellspacing="0">
                <tr>
                    <td class="tb_heading">GDX</td>
                </tr>
                <tr>
                    <td>No Data</td>
                </tr>		
           </table>  
    <?php 
    }

}

function print_pachy($patient_id,$form_id,$req){
	$stable = "";
	$fuApa= "";
	$ptInforme = "";
	$monitorIOP = "";
	$tech2InformPt = "";
	if(is_array($req) && count($req)>0){
		$sqlPachy = "SELECT * FROM pachy WHERE patientId = '".$patient_id."' AND pachy_id in (".implode(",",$req).")";		
	}else if($form_id!=""){
		$sqlPachy = "SELECT * FROM pachy WHERE patientId = '".$patient_id."' AND formId = '$form_id' AND purged='0' ";		
	}
	
	$resPachy = imw_query($sqlPachy);
	if(imw_num_rows($resPachy)>0){
	while($row=imw_fetch_array($resPachy)){
		extract($row);
		?>

				<table style="width:760px;" class="border" cellpadding="0" cellspacing="0">
					<tr>
						<td colspan="2"  class="tb_heading" style="width:750px;">Pachy (<span class="text_value">Exam Date:&nbsp;<?php print get_date_format($examDate);?></span>)</td>
					</tr>
				
					<?php
					if($pachyMeterEye){
						if($pachyMeterEye == 'OU') $pachyMeterEye = '<span style="color:purple;">'.$pachyMeterEye.'</span>';
						if($pachyMeterEye == 'OD') $pachyMeterEye = '<span style="color:blue;">'.$pachyMeterEye.'</span>';
						if($pachyMeterEye == 'OS') $pachyMeterEye = '<span style="color:green;">'.$pachyMeterEye.'</span>';
						?>

						<tr>
							<td colspan="2" class="text_lable bdrbtm">Pachymeter:&nbsp; <?php echo $pachyMeterEye; ?></td>
						</tr>

						<?php
					}
					if($performedBy || $ptUnderstanding){
					/*	$performedByQry = imw_query("SELECT CONCAT_WS(', ', lname, fname) as performedBy FROM users WHERE id = '$performedBy'");	
						$performedByRow = imw_fetch_assoc($performedByQry);
						$performedBy = str_replace(", ,"," ",$performedByRow['performedBy']);*/
						$performedBy = print_phyInitial($performedBy);
						?>
					<?php
			if($techComments!=""){
			?>
			
						<tr>
							<td colspan="2" style="width:700px;" class="bdrbtm">
								<span class="text_lable">Technician Comments:&nbsp;</span><?php echo $techComments; ?>						
							</td>
						</tr>
			
			<?php }?>
					
						<tr>
							<td style="width:350px;" class="bdrbtm"><span class="text_lable">Performed By:&nbsp;</span> <?php echo $performedBy; ?></td>
							<td style="width:350px;" class="bdrbtm"><span class="text_lable">Patient Understanding & Cooperation</span>:&nbsp;<?php echo $ptUnderstanding; ?></td>
						</tr>
					
						<?php
					}
					if($diagnosis=="Other"&&!empty($diagnosisOther)){$diagnosis=$diagnosisOther;}
					if($diagnosis){		
						?>
					
						<tr>
							<td  colspan="2" style="width:700px;" class="bdrbtm"><span class="text_lable">Diagnosis:&nbsp;</span> <?php echo $diagnosis; ?></td>
						</tr>
					
						<?php
					}
					if($reliabilityOd || $reliabilityOs){
						?>
                        <tr>
                        	<td colspan="2" style="width:740px;">
                        <table style="width:740px;" cellpadding="0" cellspacing="0">
                            <tr>	
                                <td style="width:180px;" class="bdrbtm text_lable">Physician Interpretation:&nbsp;</td>						
                                <td style="width:80px;" class="bdrbtm text_lable">Reliability</td>
                                <td style="width:50px;" class="bdrbtm text_lable"><?php odLable();?></td>
                                <td style="width:150px;" class="bdrbtm text_value"><?php echo $reliabilityOd; ?></td>
                                <td style="width:50px;"  class="bdrbtm text_lable"><?php osLable();?></td>
                                <td style="width:150px;" class="bdrbtm text_value"><?php echo $reliabilityOs; ?></td>
                            </tr>
                        </table>
                        </td>
                    </tr>
						<?php
					}
					$odData = '';
					$osData = '';
									
					// OD DATA
					if($Central_OD || $Nasal_OD || $pachy_od_readings || $pachy_od_average || $pachy_od_correction_value){
						$odData.= 'Pachy';
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
					if($iris_iridec_od){
						$odData.= '<br/>Iris/Iridectomy at '.$iris_iridec_od.' o\' clock&nbsp;';
					}
	
					// OS DATA
					if($Central_OS || $Nasal_OS || $pachy_os_readings || $pachy_os_average || $pachy_os_correction_value){
						$osData.= 'Pachy';
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
					if($iris_iridec_os){
						$osData.= '<br/>Iris/Iridectomy at '.$iris_iridec_os.' o\' clock&nbsp;';
					}

				if($osData!="" || $odData!="" || $descOd!="" || $descOs!=""){
						?>
					
						<tr>
							<td style="width:700px;" class="tb_subheading bdrbtm" colspan="2">Test Results</td>
						</tr>
						<tr>
							<td style="width:350px;" class="bdrbtm"><?php odLable();?></td>
							<td style="width:350px;" class="bdrbtm"><?php osLable();?></td>
						</tr>
					<?php	
						if($odData || $osData){
						?>
						<tr>
							<td style="width:350px;" class="bdrbtm"><?php echo  $odData; ?></td>
							<td style="width:350px;" class="bdrbtm"><?php echo  $osData; ?></td>
						</tr>
					<?php
						}	
					if($descOd!="" || $descOs!=""){
						?>
							<tr>
								<td style="width:350px;" class="bdrbtm"><span class="text_lable">Description:&nbsp;</span><?php echo $descOd; ?></td>
								<td style="width:350px;" class="bdrbtm"><span class="text_lable">Description:&nbsp;</span><?php echo $descOs; ?></td>
							</tr>
						<?php				
					}
					?>
					
						<?php
					}				
					
					$treatment = "";
					if($stable == 1) $treatment= 'Stable&nbsp;&nbsp;';
					if($contiMeds == 1) $treatment.= 'Continue Meds&nbsp;&nbsp;';					
					if($fuApa == 1) $treatment.= 'F/U APA&nbsp;&nbsp;';
					if($tech2InformPt == 1) $treatment.= 'Tech to Inform Pt. &nbsp;';
					if($ptInformed == 1) $treatment.= 'Pt informed of results&nbsp;';
					if($ptInformedNv == 1) $treatment.= 'Inform Pt result next visit';	
					
					
					if($stable == 1 || $fuApa == 1 || $ptInformed == 1){
						?>
							<tr>
								<td colspan="2"  class="bdrbtm text_lable" style="width:700px;">Treatment/Prognosis:&nbsp;<span class="text_value"><?php echo $treatment; ?></span></td>
							</tr>
						<?php
							}
							if($comments)
							{
						?>
						<tr>
							<td colspan="2" class="bdrbtm text_lable" style="width:700px;">Comments:&nbsp;<span class="text_value"><?php echo $comments; ?></span></td>
						</tr>
						<?php
					}
					///Add IVFA Images//
					$imagesHtml=getTestImages($pachy_id,$sectionImageFrom="Pacchy",$patient_id);

					//GET IMAGES FOR PDFs THAT ARE HIGHER THAN PDF VERSION 1.4 . HIGHER VERSION IMAGES ARE SKIPPED IN merge_pdf.php FILE.
					$arr_alternative_images=array();
					$str_alternative_images='';
					if($pachy_id!=""){
						$arrPDFs = getAllTestPdf($pachy_id);
						$str_alternative_images= checknMakeImagesHTML($arrPDFs);
						$imagesHtml.=$str_alternative_images;
					}	
					//------------------------------------
										
					if($imagesHtml!=""){
						echo('<tr><td style="width:700px;" colspan="2" class="bdrbtm"><table >
							 	'.$imagesHtml.' 
							</table></td></tr>');
					} 
					$imagesHtml="";
					//End Pacchy Images
					if($phyName){
						/*$getNameQry = imw_query("SELECT CONCAT_WS(', ', lname, fname) as phyName FROM users WHERE id = '$phyName'");	
						$getNameRow = imw_fetch_assoc($getNameQry);
						$phyName = str_replace(", ,"," ",$getNameRow['phyName']);*/
						$phyName = print_phyInitial($phyName);
						?>
			
						<tr>
							<td colspan="2" style="width:700px;" class="bdrbtm"><span class="text_lable">Interpreted By:&nbsp;</span> <?php echo $phyName; ?></td>
						</tr>
			
						<?php
					}
					?>
				</table>
		<?php
	}//End of Pachy While
 }else{ ?>
	<table style="width:740px;" class="border" cellpadding="0" cellspacing="0">
            <tr>
                <td class="tb_heading">Pachy</td>
            </tr>
            <tr>
            	<td>No Data</td>
            </tr>		
       </table>  
	<?php 
    }

}

function print_ivfa($patient_id,$form_id,$req){
	$stable = "";
	$fuApa= "";
	$ptInforme = "";
	$monitorIOP = "";
	$tech2InformPt = "";
if(is_array($req) && count($req)>0){
	$sqlIVFA = "SELECT * FROM ivfa WHERE patient_id = '".$patient_id."' AND vf_id in(".implode(",",$req).") ";
	}else if($form_id){
		$sqlIVFA = "SELECT * FROM ivfa WHERE patient_id = '".$patient_id."' AND form_id = '$form_id' AND purged='0' ";
	}
	$rowIVFA = imw_query($sqlIVFA);
	if(imw_num_rows($rowIVFA)>0){
	while($row=imw_fetch_array($rowIVFA))
	{
		extract($row);
		$osData = '';
		$odData = '';
		?>

<table class="border" cellpadding="0" cellspacing="0" style="width:750px;">
<tr>
	<td colspan="2" class="tb_heading">IVFA (<span class="text_value">Exam Date:&nbsp;<?php print get_date_format($exam_date);?></span>)</td>
</tr>

				<?php
					if($ivfa_od == "1") $ivfa_od = '<span style="color:purple;">OU</span>';
					if($ivfa_od == "2" || $ivfa_od == "3") $ivfa_od = '<span style="color:blue;">OD</span> > <span style="color:green;">OS</span>';
					if($ivfa_early == 1) $ivfa_early = 'Early and late shots&nbsp;&nbsp;';
					if($ivfa_extra == 1) $ivfa_extra = 'Extra Copy';
					if($ivfa_od || $ivfa_early == 1 || $ivfa_extra == 1)
					{
				?>

<tr>
<td colspan="2" class="text_lable bdrbtm" style="width:700px;" ><?php if($ivfa_od) echo '<span class="text_lable">IVFA:&nbsp;</span>'.$ivfa_od.'&nbsp;'; if($ivfa_early) echo $ivfa_early; if($ivfa_extra) echo $ivfa_extra; ?></td>
					</tr>
				
				<?php
				}
				if($comments_ivfa)
				{
					?>					
						<tr>
							<td colspan="2" class="bdrbtm" style="width:700px;">Description:&nbsp; <?php echo $comments_ivfa; ?></td>
						</tr>
				<!--	</table> -->
					<?php
					}
					if($performed_by || $pa_under)
					{
						/*$getNameQry = imw_query("SELECT CONCAT_WS(', ', lname, fname) as performed_by FROM users WHERE id = '$performed_by'");						
						if(imw_num_rows($getNameQry)>0){
							$getNameRow = imw_fetch_assoc($getNameQry);
							$performed_by = $getNameRow['performed_by'];
						}*/
							$performed_by = print_phyInitial($performed_by);
						?>
						<!--<table> -->
						<?php if($techComments!=""){?>
							<tr>
								<td  colspan="2" class="text_lable bdrbtm" style="width:700px;"><b>Technician Comments:</b>&nbsp;<?php echo($techComments);?></td>
							</tr>
						<?php }?>
							<tr>
								
								<td colspan="2" class="bdrbtm">
									<?php 
										if($performed_by) echo '<span class="text_lable">Performed By:&nbsp;</span> '.$performed_by.'&nbsp;&nbsp;';
										if($pa_under) echo '<span class="text_lable">Patient Understanding & Cooperation</span>:&nbsp;'.$pa_under; 
										if($diagnosis=="Other"&&!empty($diagnosisOther)){$diagnosis=$diagnosisOther;}
										if(!empty($diagnosis) && $diagnosis!="--Select--") echo '&nbsp;&nbsp;<span class="text_lable">Diagnosis</span>:&nbsp;'.$diagnosis;
									?>
								</td>					
							</tr>
						
						<?php
						}
						if($pa_inter || $pa_inter1)
						{
						?>
					<tr>
	                    <td  style="width:700px;" colspan="2">
                        <table cellpadding="0" cellspacing="0" style="width:700px;">
                            <tr>
                                <td style="width:180px;" class="bdrbtm text_lable">Physician Interpretation:&nbsp;</td>
                                <td style="width:80px;" class="bdrbtm text_lable">Reliability</td>
                                <td style="width:50px;" class="bdrbtm text_lable"><?php odLable();?></td>
                                <td style="width:150px;" class="bdrbtm text_value"><?php echo $pa_inter; ?></td>
                                <td style="width:50px;" class="bdrbtm text_lable"><?php osLable();?></td>
                                <td style="width:150px;" class="bdrbtm text_value"><?php echo $pa_inter1; ?></td>
                            </tr>
                        </table>
						</td>
                    </tr>	
					<?php
						}
						?>
                  <!--  </table> -->
                    <?php
					// OD DATA
				// $Retina_Ischemia_OD ||$Retina_BRVO_OD || $Retina_CRVO_OD||$SR_Heme_OD ||$Classic_CNV_OD||$Occult_CNV_OD
				// $Retina_Ischemia_OS ||$Retina_BRVO_OS || $Retina_CRVO_OS||$SR_Heme_OS ||$Classic_CNV_OS||$Occult_CNV_OS 
				//$ivfaComments
					
					
					if($Sharp_Pink_OD || $Pale_OD || $Large_Cap_OD || $Sloping_OD || $Notch_OD || $NVD_OD || $Leakage_OD){
						$odData.= 'Disc';
						if($Sharp_Pink_OD == 1){ $odData.= '&nbsp;Sharp &amp; Pink&nbsp;' ; ++$c;}
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
						|| $Retina_Ischemia_OD || 	$Retina_BRVO_OD || $Retina_CRVO_OD ||  $Retina_Nevus_OD){
						$odData.= '<br>Retina';
					}
	
					if($Retina_Hemorrhage_OD || $Retina_Microaneurysms_OD || $Retina_Exudates_OD || $Retina_Laser_Scars_OD || $Retina_NEVI_OD || $Retina_SRVNM_OD || $Retina_Edema_OD || $Retina_Nevus_OD || $Retina_Ischemia_OD || 	$Retina_BRVO_OD || $Retina_CRVO_OD  )
					{						
						$c=0;						
						if($Retina_Hemorrhage_OD == 1){ $odData.= '&nbsp;Hemorrhage&nbsp;' ; ++$c; }
						if($Retina_Microaneurysms_OD == 1){ $odData.= '&nbsp;Microaneurysms&nbsp;' ; ++$c; }
						if($Retina_Exudates_OD == 1){ $odData.= '&nbsp;Exudates&nbsp;' ; ++$c; }
							if($c>=3){ $odData.= '<br>'; $c = 0; }
						if($Retina_Laser_Scars_OD == 1){ $odData.= '&nbsp;Laser Scars&nbsp;' ; ++$c; }
							if($c>=3){ $odData.= '<br>'; $c = 0; }
						if($Retina_NEVI_OD == 1){ $odData.= '&nbsp;NVE&nbsp;' ; ++$c; }
							if($c>=3){ $odData.= '<br>'; $c = 0; }
						if($Retina_SRVNM_OD == 1){ $odData.= '&nbsp;SRVNM&nbsp;' ; ++$c; }
							if($c>=3){ $odData.= '<br>'; $c = 0; }
						if($Retina_Edema_OD == 1){ $odData.= '&nbsp;Edema&nbsp;' ; ++$c; }
							if($c>=3){ $odData.= '<br>'; $c = 0; }
						if($Retina_Nevus_OD==1){$odData.= '&nbsp;Nevus&nbsp;' ; ++$c;}
							if($c>=3){ $odData.= '<br>'; $c = 0; }
						if($Retina_Ischemia_OD==1){$odData.= '&nbsp;Ischemia&nbsp;' ;	++$c; }
							if($c>=3){ $odData.= '<br>'; $c = 0; }
							
						if($Retina_BRVO_OD==1){$odData.= '&nbsp;BRVO&nbsp;' ;	++$c; }
							if($c>=3){ $odData.= '<br>'; $c = 0; }
							
						if($Retina_CRVO_OD==1){$odData.= '&nbsp;CRVO&nbsp;' ;++$c; }
							if($c>=3){ $odData.= '<br>'; $c = 0; } 						
						
											
					}
					if($Retina_BDR_OD_T || $Retina_BDR_OD_1 || $Retina_BDR_OD_2 || $Retina_BDR_OD_3 || $Retina_BDR_OD_4){
						$odData.= '<br>';
						$odData.= '&nbsp;BDR';
						if($Retina_BDR_OD_T == 1) $odData.= '&nbsp;T&nbsp;';
						if($Retina_BDR_OD_1 == 1) $odData.= '&nbsp;+1&nbsp;';
						if($Retina_BDR_OD_2 == 1) $odData.= '&nbsp;+2&nbsp;';
						if($Retina_BDR_OD_3 == 1) $odData.= '&nbsp;+3&nbsp;';
						if($Retina_BDR_OD_4 == 1) $odData.= '&nbsp;+4&nbsp;';
					}		
					if($Retina_Druse_OD_T || $Retina_Druse_OD_1 || $Retina_Druse_OD_2 || $Retina_Druse_OD_3 || $Retina_Druse_OD_4){
						$odData.= '<br>';
						$odData.= '&nbsp;Drusen';
						if($Retina_Druse_OD_T == 1) $odData.= '&nbsp;T&nbsp;';
						if($Retina_Druse_OD_1 == 1) $odData.= '&nbsp;+1&nbsp;';
						if($Retina_Druse_OD_2 == 1) $odData.= '&nbsp;+2&nbsp;';
						if($Retina_Druse_OD_3 == 1) $odData.= '&nbsp;+3&nbsp;';
						if($Retina_Druse_OD_4 == 1) $odData.= '&nbsp;+4&nbsp;';
					}		
					if($Retina_RPE_Change_OD_T || $Retina_RPE_Change_OD_1 || $Retina_RPE_Change_OD_2 || $Retina_RPE_Change_OD_3 || $Retina_RPE_Change_OD_4){
						$odData.= '<br>';
						$odData.= '&nbsp;RPE&nbsp;Change';
						if($Retina_RPE_Change_OD_T == 1) $odData.= '&nbsp;T&nbsp;';
						if($Retina_RPE_Change_OD_1 == 1) $odData.= '&nbsp;+1&nbsp;';
						if($Retina_RPE_Change_OD_2 == 1) $odData.= '&nbsp;+2&nbsp;';
						if($Retina_RPE_Change_OD_3 == 1) $odData.= '&nbsp;+3&nbsp;';
						if($Retina_RPE_Change_OD_4 == 1) $odData.= '&nbsp;+4&nbsp;';
					}
					if($Druse_OD || $RPE_Changes_OD || $SRNVM_OD || $Edema_OD || $Scars_OD || $Hemorrhage_OD || $Microaneurysms_OD || $Exudates_OD
						|| $Macula_BDR_OD_T || $Macula_BDR_OD_1 || $Macula_BDR_OD_2 || $Macula_BDR_OD_3 || $Macula_BDR_OD_4
						|| $Macula_SMD_OD_T || $Macula_SMD_OD_1 || $Macula_SMD_OD_2 || $Macula_SMD_OD_3 || $Macula_SMD_OD_4 ||
							$PED_OD || $SR_Heme_OD || $Classic_CNV_OD || $Occult_CNV_OD ){
							$odData.= '<br>Macula';
					}
					if($Druse_OD || $RPE_Changes_OD || $SRNVM_OD || $Edema_OD || $Scars_OD || $Hemorrhage_OD || $Microaneurysms_OD || $Exudates_OD ||
							$PED_OD || $SR_Heme_OD || $Classic_CNV_OD || $Occult_CNV_OD ){
						$c = 0;					
						if($Druse_OD == 1){ $odData.= '&nbsp;Drusen&nbsp;' ; ++$c; }
						if($RPE_Changes_OD == 1){ $odData.= '&nbsp;RPE Changes&nbsp;' ; ++$c; }
						if($SRNVM_OD == 1){ $odData.= '&nbsp;SRNVM&nbsp;' ; ++$c; }
							if($c>=3){ $odData.= '<br>'; $c = 0; }
						if($Edema_OD == 1){ $odData.= '&nbsp;Edema&nbsp;' ; ++$c; }
							if($c>=3){ $odData.= '<br>'; $c = 0; }
						//if($Retina_Nevus_OD == 1){ $odData.= '&nbsp;Nevus&nbsp;' ; ++$c; }
						///	if($c>=3){ $odData.= '<br>'; $c = 0; }
						if($Scars_OD == 1){ $odData.= '&nbsp;Scars&nbsp;' ; ++$c; }
							if($c>=3){ $odData.= '<br>'; $c = 0; }
						if($Hemorrhage_OD == 1){ $odData.= '&nbsp;Hemorrhage&nbsp;' ; ++$c; }
							if($c>=3){ $odData.= '<br>'; $c = 0; }
						if($Microaneurysms_OD == 1){ $odData.= '&nbsp;Microaneurysms&nbsp;' ; ++$c; }
							if($c>=3){ $odData.= '<br>'; $c = 0; }
						if($Exudates_OD == 1){ $odData.= '&nbsp;Exudates&nbsp;' ; ++$c; }
							if($c>=3){ $odData.= '<br>'; $c = 0; }
						
						if($PED_OD==1){	$odData.= '&nbsp;PED&nbsp;' ;++$c; }
							if($c>=3){ $odData.= '<br>'; $c = 0; }
						
						if($SR_Heme_OD==1){	$odData.= '&nbsp;SR Heme&nbsp;' ;++$c; }
							if($c>=3){ $odData.= '<br>'; $c = 0; }
							
						if($Classic_CNV_OD==1){	$odData.= '&nbsp;Classic CNV&nbsp;' ;++$c; }
							if($c>=3){ $odData.= '<br>'; $c = 0; }
							
						if($Occult_CNV_OD==1){$odData.= '&nbsp;Occult CNV&nbsp;' ; ++$c; }
							if($c>=3){ $odData.= '<br>'; $c = 0; }
							
						
					}
					if($Macula_BDR_OD_T || $Macula_BDR_OD_1 || $Macula_BDR_OD_2 || $Macula_BDR_OD_3 || $Macula_BDR_OD_4){
						$odData.= '<br>';
						$odData.= '&nbsp;BDR';
						if($Macula_BDR_OD_T == 1) $odData.= '&nbsp;T&nbsp;';
						if($Macula_BDR_OD_1 == 1) $odData.= '&nbsp;+1&nbsp;';
						if($Macula_BDR_OD_2 == 1) $odData.= '&nbsp;+2&nbsp;';
						if($Macula_BDR_OD_3 == 1) $odData.= '&nbsp;+3&nbsp;';
						if($Macula_BDR_OD_4 == 1) $odData.= '&nbsp;+4&nbsp;';
					}		
					if($Macula_SMD_OD_T || $Macula_SMD_OD_1 || $Macula_SMD_OD_2 || $Macula_SMD_OD_3 || $Macula_SMD_OD_4){
						$odData.= '<br>';
						$odData.= '&nbsp;SMD';
						if($Macula_SMD_OD_T == 1) $odData.= '&nbsp;T&nbsp;';
						if($Macula_SMD_OD_1 == 1) $odData.= '&nbsp;+1&nbsp;';
						if($Macula_SMD_OD_2 == 1) $odData.= '&nbsp;+2&nbsp;';
						if($Macula_SMD_OD_3 == 1) $odData.= '&nbsp;+3&nbsp;';
						if($Macula_SMD_OD_4 == 1) $odData.= '&nbsp;+4&nbsp;';
					}
					
					
					// OS DATA
					if($Sharp_Pink_OS || $Pale_OS || $Large_Cap_OS || $Sloping_OS || $Notch_OS || $NVD_OS || $Leakage_OS){
						$osData.= 'Disc';
						if($Sharp_Pink_OS == 1){ $osData.= '&nbsp;Sharp &amp; Pink&nbsp;' ; ++$c;}
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
						|| $Retina_Ischemia_OS ||$Retina_BRVO_OS || $Retina_CRVO_OS||$Retina_Nevus_OS ){
						$osData.= '<br>Retina';
					}
					if($Retina_Hemorrhage_OS || $Retina_Microaneurysms_OS || $Retina_Exudates_OS || $Retina_Laser_Scars_OS || $Retina_NEVI_OS || $Retina_SRVNM_OS || $Retina_Edema_OS || $Retina_Nevus_OS ||$Retina_Ischemia_OS ||$Retina_BRVO_OS || $Retina_CRVO_OS ){
						$c = 0;					
						if($Retina_Hemorrhage_OS == 1){ $osData.= '&nbsp;Hemorrhage&nbsp;' ; ++$c; }
						if($Retina_Microaneurysms_OS == 1){ $osData.= '&nbsp;Microaneurysms&nbsp;' ; ++$c; }
						if($Retina_Exudates_OS == 1){ $osData.= '&nbsp;Exudates&nbsp;' ; ++$c; }
							if($c>=3){ $osData.= '<br>'; $c = 0; }
						if($Retina_Laser_Scars_OS == 1){ $osData.= '&nbsp;Laser Scars&nbsp;' ; ++$c; }
							if($c>=3){ $osData.= '<br>'; $c = 0; }
						if($Retina_NEVI_OS == 1){ $osData.= '&nbsp;NVE&nbsp;' ; ++$c; }
							if($c>=3){ $osData.= '<br>'; $c = 0; }
						if($Retina_SRVNM_OS == 1){ $osData.= '&nbsp;SRVNM&nbsp;' ; ++$c; }
							if($c>=3){ $osData.= '<br>'; $c = 0; }
						if($Retina_Edema_OS == 1){ $osData.= '&nbsp;Edema&nbsp;' ; ++$c; }	
						if($Retina_Nevus_OS==1){$osData.= '&nbsp;Nevus&nbsp;' ; ++$c;}
						if($Retina_Ischemia_OS== 1){$osData.= '&nbsp;Ischemia&nbsp;' ;	++$c; }
							if($c>=3){ $osData.= '<br>'; $c = 0; }
						if($Retina_BRVO_OS==1){		$osData.= '&nbsp;BRVO&nbsp;' ;	++$c; }
							if($c>=3){ $osData.= '<br>'; $c = 0; }
						if($Retina_CRVO_OS==1){		$osData.= '&nbsp;CRVO&nbsp;' ;++$c; }
							if($c>=3){ $osData.= '<br>'; $c = 0; } 
						
					}
					if($Retina_BDR_OS_T || $Retina_BDR_OS_1 || $Retina_BDR_OS_2 || $Retina_BDR_OS_3 || $Retina_BDR_OS_4){
						$osData.= '<br>';
						$osData.= '&nbsp;BDR';
						if($Retina_BDR_OS_T == 1) $osData.= '&nbsp;T&nbsp;';
						if($Retina_BDR_OS_1 == 1) $osData.= '&nbsp;+1&nbsp;';
						if($Retina_BDR_OS_2 == 1) $osData.= '&nbsp;+2&nbsp;';
						if($Retina_BDR_OS_3 == 1) $osData.= '&nbsp;+3&nbsp;';
						if($Retina_BDR_OS_4 == 1) $osData.= '&nbsp;+4&nbsp;';
					}		
					if($Retina_Druse_OS_T || $Retina_Druse_OS_1 || $Retina_Druse_OS_2 || $Retina_Druse_OS_3 || $Retina_Druse_OS_4){
						$osData.= '<br>';
						$osData.= '&nbsp;Druse';
						if($Retina_Druse_OS_T == 1) $osData.= '&nbsp;T&nbsp;';
						if($Retina_Druse_OS_1 == 1) $osData.= '&nbsp;+1&nbsp;';
						if($Retina_Druse_OS_2 == 1) $osData.= '&nbsp;+2&nbsp;';
						if($Retina_Druse_OS_3 == 1) $osData.= '&nbsp;+3&nbsp;';
						if($Retina_Druse_OS_4 == 1) $osData.= '&nbsp;+4&nbsp;';
					}		
					if($Retina_RPE_Change_OS_T || $Retina_RPE_Change_OS_1 || $Retina_RPE_Change_OS_2 || $Retina_RPE_Change_OS_3 || $Retina_RPE_Change_OS_4){
						$osData.= '<br>';
						$osData.= '&nbsp;RPE&nbsp;Change';
						if($Retina_RPE_Change_OS_T == 1) $osData.= '&nbsp;T&nbsp;';
						if($Retina_RPE_Change_OS_1 == 1) $osData.= '&nbsp;+1&nbsp;';
						if($Retina_RPE_Change_OS_2 == 1) $osData.= '&nbsp;+2&nbsp;';
						if($Retina_RPE_Change_OS_3 == 1) $osData.= '&nbsp;+3&nbsp;';
						if($Retina_RPE_Change_OS_4 == 1) $osData.= '&nbsp;+4&nbsp;';
					}
					if($Druse_OS || $RPE_Changes_OS || $SRNVM_OS || $Edema_OS || $Scars_OS || $Hemorrhage_OS || $Microaneurysms_OS || $Exudates_OS
						|| $Macula_BDR_OS_T || $Macula_BDR_OS_1 || $Macula_BDR_OS_2 || $Macula_BDR_OS_3 || $Macula_BDR_OS_4
						|| $Macula_SMD_OS_T || $Macula_SMD_OS_1 || $Macula_SMD_OS_2 || $Macula_SMD_OS_3 || $Macula_SMD_OS_4 ||
						$SR_Heme_OS ||$Classic_CNV_OS||$Occult_CNV_OS){
							$osData.= '<br>Macula';
					}				
					if($Druse_OS || $RPE_Changes_OS || $SRNVM_OS || $Edema_OS || $Scars_OS || $Hemorrhage_OS || $Microaneurysms_OS || $Exudates_OS||$SR_Heme_OS ||$Classic_CNV_OS||$Occult_CNV_OS){
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
						
						if($PED_OS==1){	$osData.= '&nbsp;PED&nbsp;' ;++$c; }
							if($c>=3){ $osData.= '<br>'; $c = 0; }
						
						if($SR_Heme_OS==1){		$osData.= '&nbsp;SR Heme&nbsp;' ;++$c; }
							if($c>=3){ $osData.= '<br>'; $c = 0; }
						if($Classic_CNV_OS==1){		$osData.= '&nbsp;Classic CNV&nbsp;' ;++$c; }
							if($c>=3){ $osData.= '<br>'; $c = 0; }
						if($Occult_CNV_OS==1){		$osData.= '&nbsp;Occult CNV&nbsp;' ; ++$c; }
							if($c>=3){ $osData.= '<br>'; $c = 0; }		
						
					}
					if($Macula_BDR_OS_T || $Macula_BDR_OS_1 || $Macula_BDR_OS_2 || $Macula_BDR_OS_3 || $Macula_BDR_OS_4){
						$osData.= '<br>';
						$osData.= '&nbsp;BDR';
						if($Macula_BDR_OS_T == 1) $osData.= '&nbsp;T&nbsp;';
						if($Macula_BDR_OS_1 == 1) $osData.= '&nbsp;+1&nbsp;';
						if($Macula_BDR_OS_2 == 1) $osData.= '&nbsp;+2&nbsp;';
						if($Macula_BDR_OS_3 == 1) $osData.= '&nbsp;+3&nbsp;';
						if($Macula_BDR_OS_4 == 1) $osData.= '&nbsp;+4&nbsp;';
					}		
					if($Macula_SMD_OS_T || $Macula_SMD_OS_1 || $Macula_SMD_OS_2 || $Macula_SMD_OS_3 || $Macula_SMD_OS_4){
						$osData.= '<br>';
						$osData.= '&nbsp;SMD';
						if($Macula_SMD_OS_T == 1) $osData.= '&nbsp;T&nbsp;';
						if($Macula_SMD_OS_1 == 1) $osData.= '&nbsp;+1&nbsp;';
						if($Macula_SMD_OS_2 == 1) $osData.= '&nbsp;+2&nbsp;';
						if($Macula_SMD_OS_3 == 1) $osData.= '&nbsp;+3&nbsp;';
						if($Macula_SMD_OS_4 == 1) $osData.= '&nbsp;+4&nbsp;';
					}						
					if($osData!="" || $odData!="" || $testresults_desc_od!="" || $testresults_desc_os!=""){
						?>
                                    <tr>
                                        <td class="tb_subheading bdrbtm" colspan="2">Test Results</td>
                                    </tr>
                                    <tr>
                                        <td class="bdrbtm" style="width:350px"><?php odLable();?></td>
                                        <td class="bdrbtm" style="width:350px"><?php osLable();?></td>
                                    </tr>
                                <?php	
                                    if($odData || $osData)
									{
                                    ?>
                                    <tr>
                                        <td style="width:350px" class="bdrbtm"><?php echo $odData; ?></td>
                                        <td style="width:350px" class="bdrbtm"><?php echo $osData; ?></td>
                                    </tr>
                                <?php
                                    }	
                                if($testresults_desc_od || $testresults_desc_os)
									{
                                    ?>
                                        <tr>
                                            <td style="width:350px" class="bdrbtm"><?php echo $testresults_desc_od; ?></td>
                                            <td style="width:350px" class="bdrbtm"><?php echo $testresults_desc_os; ?></td>
                                        </tr>
                               	     <?php				
                                	}
                                ?>
                                  
                              
						<?php
					}
					?>
					
					<?php
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
						<!--<table> -->
							<tr>
								<td colspan="2" class="bdrbtm" style="width:700px;"><b>Treatment/Prognosis:</b>&nbsp;<?php echo $treatment; ?></td>
							</tr>
						<!--</table> -->
						<?php
					}
					if($ArgonLaser || $ArgonLaserEye || $ArgonLaserEyeOptions || $FuRetinaComments){
						if($ArgonLaser) {
							$ArgonLaser = 'Argon Laser Surgery&nbsp;&nbsp;';
							if($ArgonLaserEye) $ArgonLaser.= ''.$ArgonLaserEye.'&nbsp;&nbsp;';
							if($ArgonLaserEyeOptions) $ArgonLaser.= $ArgonLaserEyeOptions.'&nbsp;&nbsp;';
						}else{
							$ArgonLaser =  "";
						}
						if($FuRetina == 1) $ArgonLaser.= 'F/U Retina &nbsp;';
						if($FuRetinaComments){
						$ArgonLaser.=$FuRetinaComments;
						}
						if($rptTst1yr==1){$ArgonLaser.= '&nbsp;&nbsp;Repeat test 1 year ';}
						?>
                        
						<!--<table> -->
							<tr>
								<td colspan="2" class="bdrbtm" style="width:700px;">
									<?php echo $ArgonLaser; ?>
								</td>						
							</tr>
						<!--</table> -->
                        
						<?php
					}
					
					
						if($ivfaComments !=""){
						?>
						<!--<table> -->
							<tr>
								<td colspan="2" class="bdrbtm" style="width:700px;"><strong>Comments:</strong><?php echo $ivfaComments; ?></td>								
							</tr>
						<!--</table> -->
						<?php				
					}
					
				
					///Add IVFA Images//
					$imagesHtml=getTestImages($vf_id,$sectionImageFrom="IVFA",$patient_id);

					//GET IMAGES FOR PDFs THAT ARE HIGHER THAN PDF VERSION 1.4 . HIGHER VERSION IMAGES ARE SKIPPED IN merge_pdf.php FILE.
					$arr_alternative_images=array();
					$str_alternative_images='';
					if($vf_id!=""){
						$arrPDFs = getAllTestPdf($vf_id);
						$str_alternative_images= checknMakeImagesHTML($arrPDFs);
						$imagesHtml.=$str_alternative_images;
					}	
					//------------------------------------
										
					if($imagesHtml!=""){
						echo('<tr><td colspan="2" style="width:700px;" class="bdrbtm"><table >
							 	'.$imagesHtml.' 
							</table></td></tr>');
					} 
					$imagesHtml="";
					//End IVFA Images
					if($phy)
					{
						/*$getphysicianQry = imw_query("SELECT CONCAT_WS(', ', lname, fname) as physician FROM users WHERE id = '$phy'");
						$getphysicianRow = imw_fetch_assoc($getphysicianQry);
						$physicianName = str_replace(", , "," ",$getphysicianRow['physician']);*/
						$physicianName = print_phyInitial($phy);
						?>
					<!--<table > -->
						<tr >
							<td colspan="2" style="width:700px;" class="bdrbtm"><span class="text_lable">Interpreted By:&nbsp;</span> <?php echo $physicianName; ?></td>
						</tr>
					<!--</table> -->
						<?php
					}
					?>				
                    </table>
				
		<?php
		}//END IVFA WHILE
	}else{ ?>
<table style="width:740px;" class="border" cellpadding="0" cellspacing="0">
            <tr>
                <td class="tb_heading">IVFA</td>
            </tr>
            <tr>
            	<td>No Data</td>
            </tr>		
       </table>  
<?php 
}

}


function print_icg($patient_id,$form_id,$req)
{
	$stable = "";
	$fuApa= "";
	$ptInforme = "";
	$monitorIOP = "";
	$tech2InformPt = "";
	if(is_array($req) && count($req)>0)
	{
		$sqlICG = "SELECT * FROM icg WHERE patient_id = '".$patient_id."' AND icg_id in(".implode(",",$req).") ";
	}
	else if($form_id)
	{
		$sqlICG = "SELECT * FROM icg WHERE patient_id = '".$patient_id."' AND form_id = '$form_id' AND purged='0' ";
	}
	$rowICG = imw_query($sqlICG);
	if(imw_num_rows($rowICG)>0){
	while($row=imw_fetch_array($rowICG))
	{
		extract($row);
		$osData = '';
		$odData = '';
		?>
        

			<table cellpadding="0" cellspacing="0" class="border" style="width:750px;">
				<tr>
					<td colspan="2" class="tb_heading">ICG (<span class="text_value">Exam Date:&nbsp;<?php print get_date_format($exam_date);?></span>)</td>
				</tr>
			
            
				<?php
				if($icg_od == "1") $icg_od = '<span style="color:purple;">OU</span>';
				if($icg_od == "2" || $icg_od == "3") $icg_od = '<span style="color:blue;">OD</span> > <span style="color:green;">OS</span>';
				if($icg_early == 1) $icg_early = 'Early and late shots&nbsp;&nbsp;';
				if($icg_extra == 1) $icg_extra = 'Extra Copy';
				if($icg_od || $icg_early == 1 || $icg_extra == 1)
				{
					?>
			
					<tr>
						<td colspan="2" class="bdrbtm text_lable"><?php if($icg_od) echo '<span class="text_lable">ICG:&nbsp;</span>'.$icg_od.'&nbsp;'; if($icg_early) echo $icg_early; if($icg_extra) echo $icg_extra; ?></td>
					</tr>
			
					<?php
				}
				if($comments_icg)
				{
					?>
				
						<tr>
							<td colspan="2" class="bdrbtm" style="width:700px;"><span class="text_lable">Description:&nbsp;</span> <?php echo $comments_icg; ?></td>
						</tr>
					
						<?php
					}
					if($performed_by || $pa_under){
						/*$getNameQry = imw_query("SELECT CONCAT_WS(', ', lname, fname) as performed_by FROM users WHERE id = '$performed_by'");						
						if(imw_num_rows($getNameQry)>0){
							$getNameRow = imw_fetch_assoc($getNameQry);
							$performed_by = $getNameRow['performed_by'];
						}*/
							$performed_by = print_phyInitial($performed_by);
						?>
					
						<?php if($techComments!=""){?>
							<tr>
								<td colspan="2" class="bdrbtm" style="width:700px;"><b>Technician Comments:</b>&nbsp;<?php echo($techComments);?></td>
							</tr>
						<?php }?>
							<tr>
								
								<td colspan="2" class="bdrbtm" style="width:700px;">
									<?php 
										if($performed_by) echo '<span class="text_lable">Performed By:&nbsp;</span> '.$performed_by.'&nbsp;&nbsp;';
										if($pa_under) echo '<span class="text_lable">Patient Understanding & Cooperation</span>:&nbsp;'.$pa_under; 
										if($diagnosis=="Other"&&!empty($diagnosisOther)){$diagnosis=$diagnosisOther;}
										if(!empty($diagnosis) && $diagnosis!="--Select--") echo '&nbsp;&nbsp;<span class="text_lable">Diagnosis</span>:&nbsp;'.$diagnosis;
									?>
								</td>					
							</tr>
						
					
					<?php
						}
						if($pa_inter || $pa_inter1)
						{
						?>
                        
                        
					<tr>
                    	<td style="width:700px;" colspan="2">
					<table style="width:700px;" cellpadding="0" cellspacing="0">
						<tr>	
						  <td style="width:180px;" class="bdrbtm text_lable">Physician Interpretation:&nbsp;</td>
						  <td style="width:80px;" class="bdrbtm text_lable">Reliability</td>
						  <td style="width:50px;" class="bdrbtm text_lable"><?php odLable();?></td>
						  <td style="width:150px;" class="bdrbtm text_value"><?php echo $pa_inter; ?></td>
						  <td style="width:50px;" class="bdrbtm text_lable"><?php osLable();?></td>
						  <td style="width:150px;" class="bdrbtm text_value"><?php echo $pa_inter1; ?></td>
						</tr>
					</table>
                    </td>
                    </tr>
						<?php
						}
					// OD DATA
				// $Retina_Ischemia_OD ||$Retina_BRVO_OD || $Retina_CRVO_OD||$SR_Heme_OD ||$Classic_CNV_OD||$Occult_CNV_OD
				// $Retina_Ischemia_OS ||$Retina_BRVO_OS || $Retina_CRVO_OS||$SR_Heme_OS ||$Classic_CNV_OS||$Occult_CNV_OS 
				//$ivfaComments 
					
					
					if($Sharp_Pink_OD || $Pale_OD || $Large_Cap_OD || $Sloping_OD || $Notch_OD || $NVD_OD || $Leakage_OD){
						$odData.= 'Disc';
						if($Sharp_Pink_OD == 1){ $odData.= '&nbsp;Sharp &amp; Pink&nbsp;' ; ++$c;}
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
						|| $Retina_Ischemia_OD || 	$Retina_BRVO_OD || $Retina_CRVO_OD || $Retina_Nevus_OD){
						$odData.= '<br>Retina';
					}
	
					if($Retina_Hemorrhage_OD || $Retina_Microaneurysms_OD || $Retina_Exudates_OD || $Retina_Laser_Scars_OD || $Retina_NEVI_OD || $Retina_SRVNM_OD || $Retina_Edema_OD || $Retina_Nevus_OD || $Retina_Ischemia_OD || 	$Retina_BRVO_OD || $Retina_CRVO_OD  ){
						$c = 0;
						if($Retina_Hemorrhage_OD == 1){ $odData.= '&nbsp;Hemorrhage&nbsp;' ; ++$c; }
						if($Retina_Microaneurysms_OD == 1){ $odData.= '&nbsp;Microaneurysms&nbsp;' ; ++$c; }
						if($Retina_Exudates_OD == 1){ $odData.= '&nbsp;Exudates&nbsp;' ; ++$c; }
							if($c>=3){ $odData.= '<br>'; $c = 0; }
						if($Retina_Laser_Scars_OD == 1){ $odData.= '&nbsp;Laser Scars&nbsp;' ; ++$c; }
							if($c>=3){ $odData.= '<br>'; $c = 0; }
						if($Retina_NEVI_OD == 1){ $odData.= '&nbsp;NVE&nbsp;' ; ++$c; }
							if($c>=3){ $odData.= '<br>'; $c = 0; }
						if($Retina_SRVNM_OD == 1){ $odData.= '&nbsp;SRVNM&nbsp;' ; ++$c; }
							if($c>=3){ $odData.= '<br>'; $c = 0; }
						if($Retina_Edema_OD == 1){ $odData.= '&nbsp;Edema&nbsp;' ; ++$c; }
						if($Retina_Nevus_OD == 1){ $odData.= '&nbsp;Nevus&nbsp;' ; ++$c; }
							if($c>=3){ $odData.= '<br>'; $c = 0; }
						if($Retina_Ischemia_OD==1){$odData.= '&nbsp;Ischemia&nbsp;' ;	++$c; }
							if($c>=3){ $odData.= '<br>'; $c = 0; }
							
						if($Retina_BRVO_OD==1){$odData.= '&nbsp;BRVO&nbsp;' ;	++$c; }
							if($c>=3){ $odData.= '<br>'; $c = 0; }
							
						if($Retina_CRVO_OD==1){$odData.= '&nbsp;CRVO&nbsp;' ;++$c; }
							if($c>=3){ $odData.= '<br>'; $c = 0; } 
						
					}
					if($Retina_BDR_OD_T || $Retina_BDR_OD_1 || $Retina_BDR_OD_2 || $Retina_BDR_OD_3 || $Retina_BDR_OD_4){
						$odData.= '<br>';
						$odData.= '&nbsp;BDR';
						if($Retina_BDR_OD_T == 1) $odData.= '&nbsp;T&nbsp;';
						if($Retina_BDR_OD_1 == 1) $odData.= '&nbsp;+1&nbsp;';
						if($Retina_BDR_OD_2 == 1) $odData.= '&nbsp;+2&nbsp;';
						if($Retina_BDR_OD_3 == 1) $odData.= '&nbsp;+3&nbsp;';
						if($Retina_BDR_OD_4 == 1) $odData.= '&nbsp;+4&nbsp;';
					}		
					if($Retina_Druse_OD_T || $Retina_Druse_OD_1 || $Retina_Druse_OD_2 || $Retina_Druse_OD_3 || $Retina_Druse_OD_4){
						$odData.= '<br>';
						$odData.= '&nbsp;Drusen';
						if($Retina_Druse_OD_T == 1) $odData.= '&nbsp;T&nbsp;';
						if($Retina_Druse_OD_1 == 1) $odData.= '&nbsp;+1&nbsp;';
						if($Retina_Druse_OD_2 == 1) $odData.= '&nbsp;+2&nbsp;';
						if($Retina_Druse_OD_3 == 1) $odData.= '&nbsp;+3&nbsp;';
						if($Retina_Druse_OD_4 == 1) $odData.= '&nbsp;+4&nbsp;';
					}		
					if($Retina_RPE_Change_OD_T || $Retina_RPE_Change_OD_1 || $Retina_RPE_Change_OD_2 || $Retina_RPE_Change_OD_3 || $Retina_RPE_Change_OD_4){
						$odData.= '<br>';
						$odData.= '&nbsp;RPE&nbsp;Change';
						if($Retina_RPE_Change_OD_T == 1) $odData.= '&nbsp;T&nbsp;';
						if($Retina_RPE_Change_OD_1 == 1) $odData.= '&nbsp;+1&nbsp;';
						if($Retina_RPE_Change_OD_2 == 1) $odData.= '&nbsp;+2&nbsp;';
						if($Retina_RPE_Change_OD_3 == 1) $odData.= '&nbsp;+3&nbsp;';
						if($Retina_RPE_Change_OD_4 == 1) $odData.= '&nbsp;+4&nbsp;';
					}
					if($Druse_OD || $RPE_Changes_OD || $SRNVM_OD || $Edema_OD || $Scars_OD || $Hemorrhage_OD || $Microaneurysms_OD || $Exudates_OD
						|| $Macula_BDR_OD_T || $Macula_BDR_OD_1 || $Macula_BDR_OD_2 || $Macula_BDR_OD_3 || $Macula_BDR_OD_4
						|| $Macula_SMD_OD_T || $Macula_SMD_OD_1 || $Macula_SMD_OD_2 || $Macula_SMD_OD_3 || $Macula_SMD_OD_4||
							$PED_OD || $SR_Heme_OD || $Classic_CNV_OD || $Occult_CNV_OD ){
							$odData.= '<br>Macula';
					}
					if($Druse_OD || $RPE_Changes_OD || $SRNVM_OD || $Edema_OD || $Scars_OD || $Hemorrhage_OD || $Microaneurysms_OD || $Exudates_OD||
						$PED_OD || $SR_Heme_OD || $Classic_CNV_OD || $Occult_CNV_OD ){
						$c = 0;					
						if($Druse_OD == 1){ $odData.= '&nbsp;Drusen&nbsp;' ; ++$c; }
						if($RPE_Changes_OD == 1){ $odData.= '&nbsp;RPE Changes&nbsp;' ; ++$c; }
						if($SRNVM_OD == 1){ $odData.= '&nbsp;SRNVM&nbsp;' ; ++$c; }
							if($c>=3){ $odData.= '<br>'; $c = 0; }
						if($Edema_OD == 1){ $odData.= '&nbsp;Edema&nbsp;' ; ++$c; }
							if($c>=3){ $odData.= '<br>'; $c = 0; }
						
						if($Scars_OD == 1){ $odData.= '&nbsp;Scars&nbsp;' ; ++$c; }
							if($c>=3){ $odData.= '<br>'; $c = 0; }
						if($Hemorrhage_OD == 1){ $odData.= '&nbsp;Hemorrhage&nbsp;' ; ++$c; }
							if($c>=3){ $odData.= '<br>'; $c = 0; }
						if($Microaneurysms_OD == 1){ $odData.= '&nbsp;Microaneurysms&nbsp;' ; ++$c; }
							if($c>=3){ $odData.= '<br>'; $c = 0; }
						if($Exudates_OD == 1){ $odData.= '&nbsp;Exudates&nbsp;' ; ++$c; }
							if($c>=3){ $odData.= '<br>'; $c = 0; }
							
						if($PED_OD==1){	$odData.= '&nbsp;PED&nbsp;' ;++$c; }
							if($c>=3){ $odData.= '<br>'; $c = 0; }	
						
						if($SR_Heme_OD==1){	$odData.= '&nbsp;SR Heme&nbsp;' ;++$c; }
							if($c>=3){ $odData.= '<br>'; $c = 0; }
							
						if($Classic_CNV_OD==1){	$odData.= '&nbsp;Classic CNV&nbsp;' ;++$c; }
							if($c>=3){ $odData.= '<br>'; $c = 0; }
							
						if($Occult_CNV_OD==1){$odData.= '&nbsp;Occult CNV&nbsp;' ; ++$c; }
							if($c>=3){ $odData.= '<br>'; $c = 0; }
						
					}
					if($Macula_BDR_OD_T || $Macula_BDR_OD_1 || $Macula_BDR_OD_2 || $Macula_BDR_OD_3 || $Macula_BDR_OD_4){
						$odData.= '<br>';
						$odData.= '&nbsp;BDR';
						if($Macula_BDR_OD_T == 1) $odData.= '&nbsp;T&nbsp;';
						if($Macula_BDR_OD_1 == 1) $odData.= '&nbsp;+1&nbsp;';
						if($Macula_BDR_OD_2 == 1) $odData.= '&nbsp;+2&nbsp;';
						if($Macula_BDR_OD_3 == 1) $odData.= '&nbsp;+3&nbsp;';
						if($Macula_BDR_OD_4 == 1) $odData.= '&nbsp;+4&nbsp;';
					}		
					if($Macula_SMD_OD_T || $Macula_SMD_OD_1 || $Macula_SMD_OD_2 || $Macula_SMD_OD_3 || $Macula_SMD_OD_4){
						$odData.= '<br>';
						$odData.= '&nbsp;SMD';
						if($Macula_SMD_OD_T == 1) $odData.= '&nbsp;T&nbsp;';
						if($Macula_SMD_OD_1 == 1) $odData.= '&nbsp;+1&nbsp;';
						if($Macula_SMD_OD_2 == 1) $odData.= '&nbsp;+2&nbsp;';
						if($Macula_SMD_OD_3 == 1) $odData.= '&nbsp;+3&nbsp;';
						if($Macula_SMD_OD_4 == 1) $odData.= '&nbsp;+4&nbsp;';
					}
					
					if($Feeder_Vessel_OD || $Central_OD || $Nasal_OD || $Temporal_OD || $Inferior_OD || $Superior_OD || $Hot_Spot_OD){
						$odData.= '<br>';
						if($Feeder_Vessel_OD==1){$odData.= '&nbsp;Feeder  Vessel&nbsp;' ; ++$c; }
							if($c>=3){ $odData.= '<br>'; $c = 0; }
						if($Central_OD==1){$odData.= '&nbsp;Central&nbsp;' ; ++$c; }
							if($c>=3){ $odData.= '<br>'; $c = 0; }
						if($Nasal_OD==1){$odData.= '&nbsp;Nasal&nbsp;' ; ++$c; }
							if($c>=3){ $odData.= '<br>'; $c = 0; }
						if($Temporal_OD==1){$odData.= '&nbsp;Temporal&nbsp;' ; ++$c; }
							if($c>=3){ $odData.= '<br>'; $c = 0; }
						if($Inferior_OD==1){$odData.= '&nbsp;Inferior&nbsp;' ; ++$c; }
							if($c>=3){ $odData.= '<br>'; $c = 0; }
						if($Superior_OD==1){$odData.= '&nbsp;Superior&nbsp;' ; ++$c; }
							if($c>=3){ $odData.= '<br>'; $c = 0; }
						if($Hot_Spot_OD==1){$odData.= '&nbsp;Hot Spot&nbsp;'.$Hot_Spot_Val_OD ; ++$c; }							
					}
					
					// OS DATA
					if($Sharp_Pink_OS || $Pale_OS || $Large_Cap_OS || $Sloping_OS || $Notch_OS || $NVD_OS || $Leakage_OS){
						$osData.= 'Disc';
						if($Sharp_Pink_OS == 1){ $osData.= '&nbsp;Sharp &amp; Pink&nbsp;' ; ++$c;}
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
						|| $Retina_Ischemia_OS ||$Retina_BRVO_OS || $Retina_CRVO_OS || $Retina_Nevus_OS ){
						$osData.= '<br>Retina';
					}
					if($Retina_Hemorrhage_OS || $Retina_Microaneurysms_OS || $Retina_Exudates_OS || $Retina_Laser_Scars_OS || $Retina_NEVI_OS || $Retina_SRVNM_OS || $Retina_Edema_OS || $Retina_Nevus_OS || $Retina_Ischemia_OS ||$Retina_BRVO_OS || $Retina_CRVO_OS ){
						$c = 0;					
						if($Retina_Hemorrhage_OS == 1){ $osData.= '&nbsp;Hemorrhage&nbsp;' ; ++$c; }
						if($Retina_Microaneurysms_OS == 1){ $osData.= '&nbsp;Microaneurysms&nbsp;' ; ++$c; }
						if($Retina_Exudates_OS == 1){ $osData.= '&nbsp;Exudates&nbsp;' ; ++$c; }
							if($c>=3){ $osData.= '<br>'; $c = 0; }
						if($Retina_Laser_Scars_OS == 1){ $osData.= '&nbsp;Laser Scars&nbsp;' ; ++$c; }
							if($c>=3){ $osData.= '<br>'; $c = 0; }
						if($Retina_NEVI_OS == 1){ $osData.= '&nbsp;NVE&nbsp;' ; ++$c; }
							if($c>=3){ $osData.= '<br>'; $c = 0; }
						if($Retina_SRVNM_OS == 1){ $osData.= '&nbsp;SRVNM&nbsp;' ; ++$c; }
							if($c>=3){ $osData.= '<br>'; $c = 0; }
						if($Retina_Edema_OS == 1){ $osData.= '&nbsp;Edema&nbsp;' ; ++$c; }	
						if($Retina_Nevus_OS == 1){ $osData.= '&nbsp;Nevus&nbsp;' ; ++$c; }
							if($c>=3){ $osData.= '<br>'; $c = 0; }
						if($Retina_Ischemia_OS== 1){$osData.= '&nbsp;Ischemia&nbsp;' ;	++$c; }
							if($c>=3){ $osData.= '<br>'; $c = 0; }
						if($Retina_BRVO_OS==1){		$osData.= '&nbsp;BRVO&nbsp;' ;	++$c; }
							if($c>=3){ $osData.= '<br>'; $c = 0; }
						if($Retina_CRVO_OS==1){		$osData.= '&nbsp;CRVO&nbsp;' ;++$c; }
							if($c>=3){ $osData.= '<br>'; $c = 0; } 
						
					}
					if($Retina_BDR_OS_T || $Retina_BDR_OS_1 || $Retina_BDR_OS_2 || $Retina_BDR_OS_3 || $Retina_BDR_OS_4){
						$osData.= '<br>';
						$osData.= '&nbsp;BDR';
						if($Retina_BDR_OS_T == 1) $osData.= '&nbsp;T&nbsp;';
						if($Retina_BDR_OS_1 == 1) $osData.= '&nbsp;+1&nbsp;';
						if($Retina_BDR_OS_2 == 1) $osData.= '&nbsp;+2&nbsp;';
						if($Retina_BDR_OS_3 == 1) $osData.= '&nbsp;+3&nbsp;';
						if($Retina_BDR_OS_4 == 1) $osData.= '&nbsp;+4&nbsp;';
					}		
					if($Retina_Druse_OS_T || $Retina_Druse_OS_1 || $Retina_Druse_OS_2 || $Retina_Druse_OS_3 || $Retina_Druse_OS_4){
						$osData.= '<br>';
						$osData.= '&nbsp;Drusen';
						if($Retina_Druse_OS_T == 1) $osData.= '&nbsp;T&nbsp;';
						if($Retina_Druse_OS_1 == 1) $osData.= '&nbsp;+1&nbsp;';
						if($Retina_Druse_OS_2 == 1) $osData.= '&nbsp;+2&nbsp;';
						if($Retina_Druse_OS_3 == 1) $osData.= '&nbsp;+3&nbsp;';
						if($Retina_Druse_OS_4 == 1) $osData.= '&nbsp;+4&nbsp;';
					}		
					if($Retina_RPE_Change_OS_T || $Retina_RPE_Change_OS_1 || $Retina_RPE_Change_OS_2 || $Retina_RPE_Change_OS_3 || $Retina_RPE_Change_OS_4){
						$osData.= '<br>';
						$osData.= '&nbsp;RPE&nbsp;Change';
						if($Retina_RPE_Change_OS_T == 1) $osData.= '&nbsp;T&nbsp;';
						if($Retina_RPE_Change_OS_1 == 1) $osData.= '&nbsp;+1&nbsp;';
						if($Retina_RPE_Change_OS_2 == 1) $osData.= '&nbsp;+2&nbsp;';
						if($Retina_RPE_Change_OS_3 == 1) $osData.= '&nbsp;+3&nbsp;';
						if($Retina_RPE_Change_OS_4 == 1) $osData.= '&nbsp;+4&nbsp;';
					}
					if($Druse_OS || $RPE_Changes_OS || $SRNVM_OS || $Edema_OS || $Scars_OS || $Hemorrhage_OS || $Microaneurysms_OS || $Exudates_OS
						|| $Macula_BDR_OS_T || $Macula_BDR_OS_1 || $Macula_BDR_OS_2 || $Macula_BDR_OS_3 || $Macula_BDR_OS_4
						|| $Macula_SMD_OS_T || $Macula_SMD_OS_1 || $Macula_SMD_OS_2 || $Macula_SMD_OS_3 || $Macula_SMD_OS_4||
							$PED_OS || $SR_Heme_OS || $Classic_CNV_OS || $Occult_CNV_OS){
							$osData.= '<br>Macula';
					}				
					if($Druse_OS || $RPE_Changes_OS || $SRNVM_OS || $Edema_OS || $Scars_OS || $Hemorrhage_OS || $Microaneurysms_OS || $Exudates_OS||
						$PED_OS || $SR_Heme_OS || $Classic_CNV_OS || $Occult_CNV_OS ){
						$c = 0;
						if($Druse_OS == 1){ $osData.= '&nbsp;Drusen&nbsp;' ; ++$c; }
						if($RPE_Changes_OS == 1){ $osData.= '&nbsp;RPE Changes&nbsp;' ; ++$c; }
						if($SRNVM_OS == 1){ $osData.= '&nbsp;SRNVM&nbsp;' ; ++$c; }
							if($c>=3){ $osData.= '<br>'; $c = 0; }
						if($Edema_OS == 1){ $osData.= '&nbsp;Edema&nbsp;' ; ++$c; }
							if($c>=3){ $osData.= '<br>'; $c = 0; }
						
						if($Scars_OS == 1){ $osData.= '&nbsp;Scars&nbsp;' ; ++$c; }
							if($c>=3){ $osData.= '<br>'; $c = 0; }
						if($Hemorrhage_OS == 1){ $osData.= '&nbsp;Hemorrhage&nbsp;' ; ++$c; }
							if($c>=3){ $osData.= '<br>'; $c = 0; }
						if($Microaneurysms_OS == 1){ $osData.= '&nbsp;Microaneurysms&nbsp;' ; ++$c; }
							if($c>=3){ $osData.= '<br>'; $c = 0; }
						if($Exudates_OS == 1){ $osData.= '&nbsp;Exudates&nbsp;' ; ++$c; }
							if($c>=3){ $osData.= '<br>'; $c = 0; }
						
						if($PED_OS == 1){ $osData.= '&nbsp;PED&nbsp;' ; ++$c; }
							if($c>=3){ $osData.= '<br>'; $c = 0; }
							
						if($SR_Heme_OS==1){		$osData.= '&nbsp;SR Heme&nbsp;' ;++$c; }
							if($c>=3){ $osData.= '<br>'; $c = 0; }
						if($Classic_CNV_OS==1){		$osData.= '&nbsp;Classic CNV&nbsp;' ;++$c; }
							if($c>=3){ $osData.= '<br>'; $c = 0; }
						if($Occult_CNV_OS==1){		$osData.= '&nbsp;Occult CNV&nbsp;' ; ++$c; }									
						
					}
					if($Macula_BDR_OS_T || $Macula_BDR_OS_1 || $Macula_BDR_OS_2 || $Macula_BDR_OS_3 || $Macula_BDR_OS_4){
						$osData.= '<br>';
						$osData.= '&nbsp;BDR';
						if($Macula_BDR_OS_T == 1) $osData.= '&nbsp;T&nbsp;';
						if($Macula_BDR_OS_1 == 1) $osData.= '&nbsp;+1&nbsp;';
						if($Macula_BDR_OS_2 == 1) $osData.= '&nbsp;+2&nbsp;';
						if($Macula_BDR_OS_3 == 1) $osData.= '&nbsp;+3&nbsp;';
						if($Macula_BDR_OS_4 == 1) $osData.= '&nbsp;+4&nbsp;';
					}		
					if($Macula_SMD_OS_T || $Macula_SMD_OS_1 || $Macula_SMD_OS_2 || $Macula_SMD_OS_3 || $Macula_SMD_OS_4){
						$osData.= '<br>';
						$osData.= '&nbsp;SMD';
						if($Macula_SMD_OS_T == 1) $osData.= '&nbsp;T&nbsp;';
						if($Macula_SMD_OS_1 == 1) $osData.= '&nbsp;+1&nbsp;';
						if($Macula_SMD_OS_2 == 1) $osData.= '&nbsp;+2&nbsp;';
						if($Macula_SMD_OS_3 == 1) $osData.= '&nbsp;+3&nbsp;';
						if($Macula_SMD_OS_4 == 1) $osData.= '&nbsp;+4&nbsp;';
					}
					if($Feeder_Vessel_OS || $Central_OS || $Nasal_OS || $Temporal_OS || $Inferior_OS || $Superior_OS || $Hot_Spot_OS){
						$osData.= '<br>';
						if($Feeder_Vessel_OS==1){$osData.= '&nbsp;Feeder  Vessel&nbsp;' ; ++$c; }
							if($c>=3){ $osData.= '<br>'; $c = 0; }
						if($Central_OS==1){$osData.= '&nbsp;Central&nbsp;' ; ++$c; }
							if($c>=3){ $osData.= '<br>'; $c = 0; }
						if($Nasal_OS==1){$osData.= '&nbsp;Nasal&nbsp;' ; ++$c; }
							if($c>=3){ $osData.= '<br>'; $c = 0; }
						if($Temporal_OS==1){$osData.= '&nbsp;Temporal&nbsp;' ; ++$c; }
							if($c>=3){ $osData.= '<br>'; $c = 0; }
						if($Inferior_OS==1){$osData.= '&nbsp;Inferior&nbsp;' ; ++$c; }
							if($c>=3){ $osData.= '<br>'; $c = 0; }
						if($Superior_OS==1){$osData.= '&nbsp;Superior&nbsp;' ; ++$c; }
							if($c>=3){ $osData.= '<br>'; $c = 0; }
						if($Hot_Spot_OS==1){$osData.= '&nbsp;Hot Spot&nbsp;'.$Hot_Spot_Val_OD ; ++$c; }							
					}					
					
					if($osData!="" || $odData!="" || $testresults_desc_od!="" || $testresults_desc_os!=""){
						?>
					
						<tr>
							<td class="tb_subheading bdrbtm" colspan="2">Test Results</td>
						</tr>
						<tr>
							<td style="width:350px" class="bdrbtm" ><?php odLable();?></td>
							<td style="width:350px" class="bdrbtm" ><?php osLable();?></td>
						</tr>
					<?php	
						if($odData || $osData){
						?>
						<tr>
							<td style="width:350px" class="bdrbtm" ><?php echo  $odData; ?></td>
							<td style="width:350px" class="bdrbtm" ><?php echo  $osData; ?></td>
						</tr>
					<?php
						}	
					if($testresults_desc_od || $testresults_desc_os){
						?>
							<tr>
								<td style="width:350px" class="bdrbtm" ><?php echo $testresults_desc_od; ?></td>
								<td style="width:350px" class="bdrbtm" ><?php echo $testresults_desc_os; ?></td>
							</tr>
						<?php				
					}
					?>
					
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
							<tr>
								<td style="width:700px" colspan="2"  class="bdrbtm" ><b>Treatment/Prognosis:</b>&nbsp;<?php echo $treatment; ?></td>
							</tr>
						<?php
					}
					if($ArgonLaser || $ArgonLaserEye || $ArgonLaserEyeOptions || $FuRetinaComments)
					{
						if($ArgonLaser){ 
						$ArgonLaser = 'Argon Laser Surgery&nbsp;&nbsp;';
						if($ArgonLaserEye) $ArgonLaser.= ''.$ArgonLaserEye.'&nbsp;&nbsp;';
						if($ArgonLaserEyeOptions) $ArgonLaser.= $ArgonLaserEyeOptions.'&nbsp;&nbsp;';
						}else{
							$ArgonLaser = "";
						}
						if($FuRetina == 1) $ArgonLaser.= 'F/U Retina &nbsp;';
						if($FuRetinaComments){
						$ArgonLaser.=$FuRetinaComments;
						}
						if($rptTst1yr==1){$ArgonLaser.= '&nbsp;&nbsp;Repeat test 1 year ';}
						?>
						
							<tr >
								<td style="width:700px;" colspan="2" class="bdrbtm" >
									<?php echo $ArgonLaser; ?>
								</td>						
							</tr>
						
						<?php
					}
					
					if($icgComments !=""){
						?>
						
							<tr>
								<td style="width:700px;" colspan="2" class="bdrbtm"><strong>Comments:</strong><?php echo $icgComments; ?></td>
								
							</tr>
						
						<?php				
					}
					
					///Add IVFA Images//
					$imagesHtml=getTestImages($icg_id,$sectionImageFrom="ICG",$patient_id);

					//GET IMAGES FOR PDFs THAT ARE HIGHER THAN PDF VERSION 1.4 . HIGHER VERSION IMAGES ARE SKIPPED IN merge_pdf.php FILE.
					$arr_alternative_images=array();
					$str_alternative_images='';
					if($icg_id!=""){
						$arrPDFs = getAllTestPdf($icg_id);
						$str_alternative_images= checknMakeImagesHTML($arrPDFs);
						$imagesHtml.=$str_alternative_images;
					}	
					//------------------------------------
										
					if($imagesHtml!=""){
						echo('<tr><td colspan="2" style="width:700px;" class="bdrbtm"><table >
							 	'.$imagesHtml.' 
							</table></td></tr>');
					} 
					$imagesHtml="";
					//End IVFA Images
					if($phy){
						/*$getphysicianQry = imw_query("SELECT CONCAT_WS(', ', lname, fname) as physician FROM users WHERE id = '$phy'");
						$getphysicianRow = imw_fetch_assoc($getphysicianQry);
						$physicianName = str_replace(", , "," ",$getphysicianRow['physician']);*/
						$physicianName = print_phyInitial($phy);
						?>
				
						<tr >
							<td style="width:700px;" colspan="2" class="bdrbtm"><span class="text_lable">Interpreted By:&nbsp;</span> <?php echo $physicianName; ?></td>
						</tr>
				
						<?php
					}
					?>				
				</table>
		<?php
		}//END ICG WHILE
	}else{ ?>
	<table style="width:740px;" class="border" cellpadding="0" cellspacing="0">
            <tr>
                <td class="tb_heading">ICG</td>
            </tr>
            <tr>
            	<td>No Data</td>
            </tr>		
       </table>  
<?php 
}

}


function print_disc($patient_id,$form_id,$req){
if(is_array($req) && count($req)>0){
$sql = "SELECT * FROM disc WHERE patientId = '".$patient_id."' AND disc_id in(".implode(",",$req).")";					
}else if($form_id){
$sql = "SELECT * FROM disc WHERE patientId = '".$patient_id."' AND formId = '$form_id' AND purged='0' ";					
}
$rowDISC = imw_query($sql);
if(imw_num_rows($rowDISC)>0){
while($row=imw_fetch_array($rowDISC)){
extract($row);
?>

	<table style="width:750px;" class="border" cellpadding="0" cellspacing="0">
		<tr>
			<td colspan="2" class="tb_heading">Fundus (<span class="text_value">Exam Date:&nbsp;<?php print get_date_format($examDate);?></span>)</td>
		</tr>
	
			
			<tr>
				<td colspan="2" class="bdrbtm text_lable" style="width:700px;">
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
					?>&nbsp;
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
			<?php 	
			/*$getNameQry = imw_query("SELECT CONCAT_WS(', ', lname, fname) as performedBy FROM users WHERE id = '$performedBy'");
			if(imw_num_rows($getNameQry)>0){
				$getNameRow = imw_fetch_assoc($getNameQry);
				$performedBy = $getNameRow['performedBy'];
			}*/
				$performedBy = print_phyInitial($performedBy);
if($discDesc!=""){
			?>
	
			<tr>
				<td colspan="2" class="bdrbtm" style="width:740px;"><b>Technician Comments:</b>&nbsp;<?php echo($discDesc);?></td>
			</tr>
		<?php } ?>
	
		<tr>
		   <td colspan="2" class="bdrbtm" style="width:740px;">
			<?php 
				if($performedBy) echo '<span class="text_lable">Performed By:&nbsp;</span> '.$performedBy.'&nbsp;&nbsp;';
				if($ptUnderstanding) echo '<span class="text_lable">Patient Understanding & Cooperation</span>:&nbsp;'.$ptUnderstanding;
				if($diagnosis=="Other"&&!empty($diagnosisOther)){$diagnosis=$diagnosisOther;}
				if(!empty($diagnosis) && $diagnosis!="--Select--") echo '&nbsp;&nbsp;<span class="text_lable">Diagnosis</span>:&nbsp;'.$diagnosis;
			?>
		</td>					
	</tr>
	
<?php
			if($reliabilityOd || $reliabilityOs){
				?>
				<tr>
				<td colspan="2" style="width:700px;">
			<table style="width:700px;" cellpadding="0" cellspacing="0" >
				<tr>	
					<td style="width:180px;" class="bdrbrm text_lable">Physician Interpretation:&nbsp;</td>						
					<td style="width:80xp;"  class="bdrbtm text_lable">Reliability</td>
					<td style="width:40px;" class="bdrbtm text_lable"><?php odLable();?></td>
					<td style="width:150px;" class="bdrbtm text_value"><?php echo $reliabilityOd; ?></td>
					<td style="width:40px;"  class="bdrbtm text_lable"><?php osLable();?></td>
					<td style="width:150px;"  class="bdrbtm text_value"><?php echo $reliabilityOs; ?></td>
				</tr>
			</table>
				</td>
				</tr>	
				<?php
			}


			$osData = '';
			$odData = '';
			
			// OD DATA
			if($normal_OD) $odData = 'Normal&nbsp;<br>';
			if($Sharp_Pink_OD || $Pale_OD || $Large_Cap_OD || $Sloping_OD || $Notch_OD || $NVD_OD || $Leakage_OD){
				$odData.= 'Disc';
				if($Sharp_Pink_OD == 1){ $odData.= '&nbsp;Sharp &amp; Pink&nbsp;' ; ++$c;}
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
			if($Macula_BDR_OD_T || $Macula_BDR_OD_1 || $Macula_BDR_OD_2 || $Macula_BDR_OD_3 ||$Macula_BDR_OD_4){
				$odData.= '<br>Macula ';
				$odData.= 'BDR ';
				if($Macula_BDR_OD_T == 1) $odData.= '&nbsp;T&nbsp; ';
				if($Macula_BDR_OD_1 == 1) $odData.= '&nbsp;+1&nbsp; ';
				if($Macula_BDR_OD_2 == 1) $odData.= '&nbsp;+2&nbsp; ';
				if($Macula_BDR_OD_3 == 1) $odData.= '&nbsp;+3&nbsp; ';
				if($Macula_BDR_OD_4 == 1) $odData.= '&nbsp;+4&nbsp; ';
			}	
			if($Macula_Rpe_OD_T || $Macula_Rpe_OD_1 || $Macula_Rpe_OD_2 || $Macula_Rpe_OD_3 ||$Macula_Rpe_OD_4){
				$odData.= '<br>';
				$odData.= 'Rpe&nbsp;change';
				if($Macula_Rpe_OD_T == 1) $odData.= '&nbsp;T&nbsp;';
				if($Macula_Rpe_OD_1 == 1) $odData.= '&nbsp;+1&nbsp;';
				if($Macula_Rpe_OD_2 == 1) $odData.= '&nbsp;+2&nbsp;';
				if($Macula_Rpe_OD_3 == 1) $odData.= '&nbsp;+3&nbsp;';
				if($Macula_Rpe_OD_4 == 1) $odData.= '&nbsp;+4&nbsp;';
			}
			if($Macula_Edema_OD_T || $Macula_Edema_OD_1 || $Macula_Edema_OD_2 || $Macula_Edema_OD_3 ||$Macula_Edema_OD_4){							
				$odData.= '<br>';
				$odData.= 'Edema';
				if($Macula_Edema_OD_T == 1) $odData.= '&nbsp;T&nbsp;';
				if($Macula_Edema_OD_1 == 1) $odData.= '&nbsp;+1&nbsp;';
				if($Macula_Edema_OD_2 == 1) $odData.= '&nbsp;+2&nbsp;';
				if($Macula_Edema_OD_3 == 1) $odData.= '&nbsp;+3&nbsp;';
				if($Macula_Edema_OD_4 == 1) $odData.= '&nbsp;+4&nbsp;';
			}
			$arrTmp = array("T","1","2","3","4");
			foreach($arrTmp as $key => $val){
				$tmp = $val;
				$tmpId_od = "Macula_Drusen_OD_".$tmp;
				$tmpId_os = "Macula_Drusen_OS_".$tmp;
				$$tmpId_od = "0";
				$$tmpId_os = "0";
				if(!empty($maculaOd_drusen) && strpos($maculaOd_drusen,$tmp) !== false){
					$$tmpId_od = "1";
				}
				if(!empty($maculaOs_drusen) && strpos($maculaOs_drusen,$tmp) !== false){
					$$tmpId_os = "1";
				}
			}
			if($Macula_Drusen_OD_T || $Macula_Drusen_OD_1 || $Macula_Drusen_OD_2 || $Macula_Drusen_OD_3 ||$Macula_Drusen_OD_4 ||
				$Macula_SRNVM_OD || $Macula_Scars_OD || $Macula_Hemorrhage_OD || $Macula_Microaneurysm_OD || $Macula_Exudates_OD || $Macula_Normal_OD){							
				$odData.= '<br>';
				$odData.= 'Drusen';
				if($Macula_Drusen_OD_T == 1) $odData.= '&nbsp;T&nbsp;';
				if($Macula_Drusen_OD_1 == 1) $odData.= '&nbsp;+1&nbsp;';
				if($Macula_Drusen_OD_2 == 1) $odData.= '&nbsp;+2&nbsp;';
				if($Macula_Drusen_OD_3 == 1) $odData.= '&nbsp;+3&nbsp;';
				if($Macula_Drusen_OD_4 == 1) $odData.= '&nbsp;+4&nbsp;';
				$odData.= "<br/>";
				if($Macula_SRNVM_OD == 1) $odData.= '&nbsp;SRNVM&nbsp; ';
				if($Macula_Scars_OD == 1) $odData.= '&nbsp;Scars&nbsp; ';
				if($Macula_Hemorrhage_OD == 1) $odData.= '&nbsp;hemorrhage&nbsp; ';
				$odData.= "<br/>";
				if($Macula_Microaneurysm_OD == 1) $odData.= '&nbsp;Microaneurysm&nbsp; ';
				if($Macula_Exudates_OD == 1) $odData.= '&nbsp;Exudates&nbsp; ';						
				if($Macula_Normal_OD == 1) $odData.= '&nbsp;Normal&nbsp; ';
			}	
				
			if($Periphery_Hemorrhage_OD || $Periphery_Microaneurysms_OD || $Periphery_Exudates_OD || 
				$Periphery_Cr_Scars_OD || $Periphery_NV_OD || $Periphery_Nevus_OD || $Periphery_Edema_OD){	
				$odData.= '<br>';
				$odData.= 'Periphery:&nbsp;';						
				if($Periphery_Hemorrhage_OD == 1) $odData.= '&nbsp;Hemorrhage&nbsp; ';
				if($Periphery_Microaneurysms_OD == 1) $odData.= '&nbsp;Microaneurysm&nbsp; ';
				if($Periphery_Exudates_OD == 1) $odData.= '&nbsp;Exudates&nbsp; ';
				$odData.= "<br/>";
				if($Periphery_Cr_Scars_OD == 1) $odData.= '&nbsp;Cr Scars&nbsp; ';						
				if($Periphery_NV_OD == 1) $odData.= '&nbsp;NV&nbsp; ';
				if($Periphery_Nevus_OD == 1) $odData.= '&nbsp;Nevus&nbsp; ';
				if($Periphery_Edema_OD == 1) $odData.= '&nbsp;Edema&nbsp; ';
			}	
				
				
			
			// OS DATA
			if($normal_OS) $osData = 'Normal&nbsp;<br>';
			if($Sharp_Pink_OS || $Pale_OS || $Large_Cap_OS || $Sloping_OS || $Notch_OS || $NVD_OS || $Leakage_OS){
				$osData.= 'Disc';
				if($Sharp_Pink_OS == 1){ $osData.= '&nbsp;Sharp &amp; Pink&nbsp;' ; ++$c;}
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
				$osData.= '<br>Macula ';
				$osData.= 'BDR ';
				if($Macula_BDR_OS_T == 1) $osData.= '&nbsp;T&nbsp;';
				if($Macula_BDR_OS_1 == 1) $osData.= '&nbsp;+1&nbsp;';
				if($Macula_BDR_OS_2 == 1) $osData.= '&nbsp;+2&nbsp;';
				if($Macula_BDR_OS_3 == 1) $osData.= '&nbsp;+3&nbsp;';
				if($Macula_BDR_OS_4 == 1) $osData.= '&nbsp;+4&nbsp;';
			}
			if($Macula_Rpe_OS_T || $Macula_Rpe_OS_1 || $Macula_Rpe_OS_2 || $Macula_Rpe_OS_3 ||$Macula_Rpe_OS_4){
				$osData.= '<br>';
				$osData.= 'Rpe&nbsp;change';
				if($Macula_Rpe_OS_T == 1) $osData.= '&nbsp;T&nbsp;';
				if($Macula_Rpe_OS_1 == 1) $osData.= '&nbsp;+1&nbsp;';
				if($Macula_Rpe_OS_2 == 1) $osData.= '&nbsp;+2&nbsp;';
				if($Macula_Rpe_OS_3 == 1) $osData.= '&nbsp;+3&nbsp;';
				if($Macula_Rpe_OS_4 == 1) $osData.= '&nbsp;+4&nbsp;';
			}	
			if($Macula_Edema_OS_T || $Macula_Edema_OS_1 || $Macula_Edema_OS_2 || $Macula_Edema_OS_3 ||$Macula_Edema_OS_4){	
				$osData.= '<br>';
				$osData.= 'Edema';
				if($Macula_Edema_OS_T == 1) $osData.= '&nbsp;T&nbsp;';
				if($Macula_Edema_OS_1 == 1) $osData.= '&nbsp;+1&nbsp;';
				if($Macula_Edema_OS_2 == 1) $osData.= '&nbsp;+2&nbsp;';
				if($Macula_Edema_OS_3 == 1) $osData.= '&nbsp;+3&nbsp;';
				if($Macula_Edema_OS_4 == 1) $osData.= '&nbsp;+4&nbsp;';
			}
			
			if($Macula_Drusen_OS_T || $Macula_Drusen_OS_1 || $Macula_Drusen_OS_2 || $Macula_Drusen_OS_3 ||$Macula_Drusen_OS_4 || 
				$Macula_SRNVM_OS || $Macula_Scars_OS || $Macula_Hemorrhage_OS || $Macula_Microaneurysm_OS || $Macula_Exudates_OS || $Macula_Normal_OS){							
				$osData.= '<br>';
				$osData.= 'Drusen';
				if($Macula_Drusen_OS_T == 1) $osData.= '&nbsp;T&nbsp;';
				if($Macula_Drusen_OS_1 == 1) $osData.= '&nbsp;+1&nbsp;';
				if($Macula_Drusen_OS_2 == 1) $osData.= '&nbsp;+2&nbsp;';
				if($Macula_Drusen_OS_3 == 1) $osData.= '&nbsp;+3&nbsp;';
				if($Macula_Drusen_OS_4 == 1) $osData.= '&nbsp;+4&nbsp;';
				$osData.= "<br/>";
				if($Macula_SRNVM_OS == 1) $osData.= '&nbsp;SRNVM&nbsp; ';
				if($Macula_Scars_OS == 1) $osData.= '&nbsp;Scars&nbsp; ';
				if($Macula_Hemorrhage_OS == 1) $osData.= '&nbsp;hemorrhage&nbsp; ';
				$osData.= "<br/>";
				if($Macula_Microaneurysm_OS == 1) $osData.= '&nbsp;Microaneurysm&nbsp; ';
				if($Macula_Exudates_OS == 1) $osData.= '&nbsp;Exudates&nbsp; ';						
				if($Macula_Normal_OS == 1) $osData.= '&nbsp;Normal&nbsp; ';						
			}
			
			if($Periphery_Hemorrhage_OS || $Periphery_Microaneurysms_OS || $Periphery_Exudates_OS || 
				$Periphery_Cr_Scars_OS || $Periphery_NV_OS || $Periphery_Nevus_OS || $Periphery_Edema_OS){		
				$osData.= '<br>';
				$osData.= 'Periphery:&nbsp;';						
				if($Periphery_Hemorrhage_OS == 1) $osData.= '&nbsp;Hemorrhage&nbsp; ';
				if($Periphery_Microaneurysms_OS == 1) $osData.= '&nbsp;Microaneurysm&nbsp; ';
				if($Periphery_Exudates_OS == 1) $osData.= '&nbsp;Exudates&nbsp; ';
				$osData.= "<br/>";
				if($Periphery_Cr_Scars_OS == 1) $osData.= '&nbsp;Cr Scars&nbsp; ';						
				if($Periphery_NV_OS == 1) $osData.= '&nbsp;NV&nbsp; ';
				if($Periphery_Nevus_OS == 1) $osData.= '&nbsp;Nevus&nbsp; ';
				if($Periphery_Edema_OS == 1) $osData.= '&nbsp;Edema&nbsp; ';
			}	
				
				


if($osData!="" || $odData!="" || $cdOd!="" || $cdOs!="" || $resDescOd!="" || $resDescOs!=""){
				?>
			
				<tr>
					<td class="bdrbtm tb_subheading" colspan="2">Test Results</td>
				</tr>
				<tr>
					<td style="width:360px;" class="bdrbtm" ><?php odLable();?></td>
					<td style="width:360px;" class="bdrbtm" ><?php osLable();?></td>
				</tr>
			<?php	
				if($cdOd!="" || $cdOd!=""){
				?>
				<tr>
					<td style="width:360px;" class="bdrbtm" ><?php echo 'C:D&nbsp;'.$cdOd; ?></td>
					<td style="width:360px;" class="bdrbtm" ><?php echo 'C:D&nbsp;'.$cdOs; ?></td>
				</tr>
			<?php
				}	
			
				if($odData || $osData){
				?>
				<tr>
					<td style="width:360px;" class="bdrbtm" ><?php echo  $odData; ?></td>
					<td style="width:360px;" class="bdrbtm" ><?php echo  $osData; ?></td>
				</tr>
				
			<?php
				}	
			if($resDescOd!="" || $resDescOs!=""){
				?>
					<tr>
						<td style="width:360px;" class="bdrbtm" ><span class="text_lable">Description:&nbsp;</span><?php echo $resDescOd; ?></td>
						<td style="width:360px;" class="bdrbtm" ><span class="text_lable">Description:&nbsp;</span><?php echo $resDescOs; ?></td>
					</tr>
				<?php				
			}
			?>
	
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
			if($fuRetinaDesc){$treatment.= $fuRetinaDesc;}

			if($treatment!=""){

				?>

					<tr>
						<td colspan="2" style="width:700px;" class="bdrbtm"><b>Treatment/Prognosis:</b>&nbsp;<?php echo $treatment; ?></td>
					</tr>
				<?php 
				}
				if($discComments){
				?>

					<tr>
						<td colspan="2" style="width:700px;" class="bdrbtm"><?php echo "<span class='text_lable'>Comments:</span>".$discComments; ?></td>						
					</tr>
			<?php
				}
			///Add Disc Images//
			$imagesHtml=getTestImages($disc_id,$sectionImageFrom="disc",$patient_id);

			//GET IMAGES FOR PDFs THAT ARE HIGHER THAN PDF VERSION 1.4 . HIGHER VERSION IMAGES ARE SKIPPED IN merge_pdf.php FILE.
			$arr_alternative_images=array();
			$str_alternative_images='';
			if($disc_id!=""){
				$arrPDFs = getAllTestPdf($disc_id);
				$str_alternative_images= checknMakeImagesHTML($arrPDFs);
				$imagesHtml.=$str_alternative_images;
			}	
			//------------------------------------
								
			if($imagesHtml!=""){
				echo('<tr><td colspan="2" style="width:700px;" class="bdrbtm"><table style="width:700px;" >
						'.$imagesHtml.'  
					</table></td></tr>');
			} 
			$imagesHtml="";
			//End Disc Images
			
			if($phyName){
				/*$getphysicianQry = imw_query("SELECT CONCAT_WS(', ', lname, fname) as physician FROM users WHERE id = '$phyName'");
				$getphysicianRow = imw_fetch_assoc($getphysicianQry);
				$physicianName = str_replace(", ,"," ",$getphysicianRow['physician']);*/
				$physicianName = print_phyInitial($phyName);
				?>
				
					<tr >
						<td colspan="2" style="width:700px;" class="bdrbtm"><span class="text_lable">Interpreted By:&nbsp;</span> <?php echo $physicianName; ?></td>
					</tr>
				
				<?php
			}						
			?>
			</table>
		<?php }//End of Fundus While	
	}else{ ?>
	<table style="width:740px;" class="border" cellpadding="0" cellspacing="0">
				<tr>
					<td class="tb_heading">Fundus</td>
				</tr>
				<tr>
					<td>No Data</td>
				</tr>		
		   </table>  
	<?php 
	}

}


function print_external($patient_id,$form_id,$req){

	if(is_array($req) && count($req)>0){
		$sql = "SELECT * FROM disc_external WHERE patientId = '".$patient_id."' AND disc_id in(".implode(",",$req).")";		
	}else if($form_id){
		$sql = "SELECT * FROM disc_external WHERE patientId = '".$patient_id."' AND formId = '$form_id' AND purged='0' ";	
	}
	$rowexternal = imw_query($sql);
	if(imw_num_rows($rowexternal)>0){
		while($row=imw_fetch_array($rowexternal)){
		extract($row);
		?>
			<table style="width:750px;" class="border" cellpadding="0" cellspacing="0">
				<tr>
					<td colspan="2" class="tb_heading">External (<span class="text_value">Exam Date:&nbsp;<?php print get_date_format($examDate);?></span>)</td>
				</tr>
				<tr>
					<td colspan="2" class="bdrbtm" style="width:700px;" >
						<?php 
						if($fundusDiscPhoto == 1){
							echo 'ES (External)&nbsp;'; 
						}
						if($fundusDiscPhoto == 2){
							echo 'ASP (Anterior Segment Photos)&nbsp;'; 
						}						
						?>
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
			
			<?php
			if($discDesc){
			?>
			
				<tr>
					<td colspan="2" class="bdrbtm" style="width:700px;" ><span class="text_lable">Technician Comments:&nbsp;</span><?php echo $discDesc; ?></td>
				</tr>
			
			<?php
			}
				
			?>
			<?php 	
			/*$getNameQry = imw_query("SELECT CONCAT_WS(', ', lname, fname) as performedBy FROM users WHERE id = '$performedBy'");
			if(imw_num_rows($getNameQry)>0){
				$getNameRow = imw_fetch_assoc($getNameQry);
				$performedBy = $getNameRow['performedBy'];
			}*/
				$performedBy = print_phyInitial($performedBy);
			?>
			
				<tr>
				   <td colspan="2" class="bdrbtm" style="width:700px;" >
					<?php 
						if($performedBy) echo '<span class="text_lable">Performed By:&nbsp;</span> '.$performedBy.'&nbsp;&nbsp;';
						if($ptUnderstanding) echo '<span class="text_lable">Patient Understanding & Cooperation</span>:&nbsp;'.$ptUnderstanding;
					?>
				  </td>					
				</tr>				
			
			<?php 
			if($diagnosis=="Other"&&!empty($diagnosisOther)){$diagnosis=$diagnosisOther;}
			if($diagnosis && $diagnosis!="--Select--"){
			?>
			
						<tr>
							<td colspan="2" class="bdrbtm" style="width:700px;" ><span class="text_lable">Diagnosis:&nbsp;</span> <?php echo ($diagnosis!="--Select--") ? $diagnosis : ""; ?></td>
						</tr>
			
			<?php
			}			
			if($reliabilityOd || $reliabilityOs){
			?>
            	<tr>
                <td colspan="2" style="width:700px;">
					<table style="width:700px;" cellpadding="0" cellspacing="0" >
						<tr>	
							<td style="width:180px;" class="bdrbtm text_lable">Physician Interpretation:&nbsp;</td>						
							<td style="width:80px;" class="bdrbtm text_lable">Reliability</td>
							<td style="width:40px;" class="bdrbtm text_lable"><?php odLable();?></td>
							<td style="width:150px;" class="bdrbtm text_value"><?php echo $reliabilityOd; ?></td>
							
							<td style="width:40px;" class="bdrbtm text_lable"><?php osLable();?></td>
							<td style="width:150px;" class="text_value"><?php echo $reliabilityOs; ?></td>
						</tr>
					</table>
                </td>
             </tr>       
			<?php
			}
			$osData = '';
			$odData = '';
			// OD DATA
			$odData.= 'Ptosis';			
			if($ptosisOd_neg == 1) $odData.= '&nbsp;-ve&nbsp;';
			if($ptosisOd_T == 1) $odData.= '&nbsp;T&nbsp;';
			if($ptosisOd_pos1 == 1) $odData.= '&nbsp;+1&nbsp;';
			if($ptosisOd_pos2 == 1) $odData.= '&nbsp;+2&nbsp;';
			if($ptosisOd_pos3 == 1) $odData.= '&nbsp;+3&nbsp;';
			if($ptosisOd_pos4 == 1) $odData.= '&nbsp;+4&nbsp;';
			if($ptosisOd_rul == 1) $odData.= '&nbsp;+RUL&nbsp;';
			if($ptosisOd_rll == 1) $odData.= '&nbsp;+RLL&nbsp;';
			
			$odData.= '<br>';
			$odData.= 'Dematochalasis';
			if($dermaOd_neg == 1) $odData.= '&nbsp;-ve&nbsp;';
			if($dermaOd_T == 1) $odData.= '&nbsp;+1&nbsp;';
			if($dermaOd_pos1 == 1) $odData.= '&nbsp;+1&nbsp;';
			if($dermaOd_pos2 == 1) $odData.= '&nbsp;+2&nbsp;';
			if($dermaOd_pos3 == 1) $odData.= '&nbsp;+3&nbsp;';
			if($dermaOd_pos4 == 1) $odData.= '&nbsp;+4&nbsp;';
			if($dermaOd_rul == 1) $odData.= '&nbsp;+RUL&nbsp;';
			if($dermaOd_rll == 1) $odData.= '&nbsp;+RLL&nbsp;';
			
			$odData.= '<br>';
			$odData.= 'Pterygium';
			if($pterygium1mmOd == 1) $odData.= '&nbsp;1mm&nbsp;';
			if($pterygium2mmOd == 1) $odData.= '&nbsp;2mm&nbsp;';
			if($pterygium3mmOd == 1) $odData.= '&nbsp;3mm&nbsp;';
			if($pterygium4mmOd == 1) $odData.= '&nbsp;4mm&nbsp;';
			if($pterygium5mmOd == 1) $odData.= '&nbsp;5mm&nbsp;';
			if($pterygiumNasalOd == 1) $odData.= '&nbsp;Nasal&nbsp;';
			if($pterygiumTemporalOd == 1) $odData.= '&nbsp;Temporal&nbsp;';
			
			$odData.= '<br>';
			$odData.= 'Vascularization';
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
			$odData.= 'Nevus';
			if($NevusOd_neg == 1) $odData.= '&nbsp;-ve&nbsp;';
			if($NevusOd_Pos == 1) $odData.= '&nbsp;+ve&nbsp;';
			if($NevusOd_Inferior == 1) $odData.= '&nbsp;<br>Inferior&nbsp;';
			if($NevusOd_Superior == 1) $odData.= '&nbsp;Superior&nbsp;<br>';
			if($NevusOd_Temporal == 1) $odData.= '&nbsp;Temporal&nbsp;';
			if($NevusOd_Nasal == 1) $odData.= '&nbsp;Nasal&nbsp;';
			
			
			// OS DATA
			$osData = 'Ptosis';			
			if($ptosisOs_neg == 1) $osData.= '&nbsp;-ve&nbsp;';
			if($ptosisOs_T == 1) $osData.= '&nbsp;T&nbsp;';
			if($ptosisOs_pos1 == 1) $osData.= '&nbsp;+1&nbsp;';
			if($ptosisOs_pos2 == 1) $osData.= '&nbsp;+2&nbsp;';
			if($ptosisOs_pos3 == 1) $osData.= '&nbsp;+3&nbsp;';
			if($ptosisOs_pos4 == 1) $oData.= '&nbsp;+4&nbsp;';
			if($ptosisOs_rul == 1) $osData.= '&nbsp;+RUL&nbsp;';
			if($ptosisOs_rll == 1) $osData.= '&nbsp;+RLL&nbsp;';
			
			$osData.= '<br>';
			$osData.= 'Dematochalasis';
			if($dermaOs_neg == 1) $osData.= '&nbsp;-ve&nbsp;';
			if($dermaOs_T == 1) $osData.= '&nbsp;+1&nbsp;';
			if($dermaOs_pos1 == 1) $osData.= '&nbsp;+1&nbsp;';
			if($dermaOs_pos2 == 1) $osData.= '&nbsp;+2&nbsp;';
			if($dermaOs_pos3 == 1) $osData.= '&nbsp;+3&nbsp;';
			if($dermaOs_pos4 == 1) $osData.= '&nbsp;+4&nbsp;';
			if($dermaOs_rul == 1) $osData.= '&nbsp;+RUL&nbsp;';
			if($dermaOs_rll == 1) $osData.= '&nbsp;+RLL&nbsp;';
			
			$osData.= '<br>';
			$osData.= 'Pterygium';
			if($pterygium1mmOs == 1) $osData.= '&nbsp;1mm&nbsp;';
			if($pterygium2mmOs == 1) $osData.= '&nbsp;2mm&nbsp;';
			if($pterygium3mmOs == 1) $osData.= '&nbsp;3mm&nbsp;';
			if($pterygium4mmOs == 1) $osData.= '&nbsp;4mm&nbsp;';
			if($pterygium5mmOs == 1) $osData.= '&nbsp;5mm&nbsp;';
			if($pterygiumNasalOs == 1) $osData.= '&nbsp;Nasal&nbsp;';
			if($pterygiumTemporalOs == 1) $osData.= '&nbsp;Temporal&nbsp;';
			
			$osData.= '<br>';
			$osData.= 'Vascularization';
			if($vascOs_SubEpithelial == 1) $osData.= '&nbsp;Sub-epithelial&nbsp;';
			if($vascOs_Stromal == 1) $osData.= '&nbsp;Stromal&nbsp;';
			if($vascOs_Superficial == 1) $osData.= '&nbsp;Superficial&nbsp;';
			if($vascOs_Deep == 1) $osData.= '&nbsp;Deep&nbsp;';
			if($vascOs_Endothelial == 1) $osData.= '&nbsp;<br>Endothelial&nbsp;';
			if($vascOs_Peripheral == 1) $osData.= '&nbsp;<br>Peripheral&nbsp;';
			if($vascOs_Central == 1) $osData.= '&nbsp;Central&nbsp;<br>';
			if($vascOs_Pannus == 1) $osData.= '&nbsp;Pannus&nbsp;';
			if($vascOs_GhostBV == 1) $osData.= 'Ghost BV&nbsp;';
			if($vascOs_Superior == 1) $osData.= '&nbsp;<br>Superior&nbsp;';
			if($vascOs_Inferior == 1) $osData.= '&nbsp;Inferior&nbsp;<br>';
			if($vascOs_Nasal == 1) $osData.= '&nbsp;Nasal&nbsp;';
			if($vascOs_Temporal  == 1) $osData.= '&nbsp;Temporal&nbsp;';
			
			$osData.= '<br>';
			$osData.= 'Nevus';
			if($irisNevusOs_neg == 1) {$osData.= '&nbsp;-ve&nbsp;';}
			if($irisNevusOs_Pos == 1) $osData.= '&nbsp;+ve&nbsp;';
			if($irisNevusOs_Inferior == 1) $osData.= '&nbsp;<br>Inferior &nbsp;';
			if($irisNevusOs_Superior == 1) $osData.= '&nbsp;Superior &nbsp;<br>';
			if($irisNevusOs_Temporal == 1) $osData.= '&nbsp;Temporal &nbsp;';
			if($irisNevusOs_Nasal == 1) {$osData.= '&nbsp; Nasal &nbsp;';}			
			
			if($osData!="" || $odData!=""  || $resDescOd!="" || $resDescOs!=""){
						?>
					
						<tr>
							<td  class="bdrbtm tb_subheading" colspan="2">Test Results</td>
						</tr>
						<tr>
							<td style="width:350px" class="bdrbtm" ><?php odLable();?></td>
							<td style="width:350px" class="bdrbtm"><?php osLable();?></td>
						</tr>
					<?php	
						
					
						if($odData!="" || $osData!=""){
						?>
						<tr>
							<td style="width:350px" class="bdrbtm"><?php echo  $odData; ?></td>
							<td style="width:350px" class="bdrbtm"><?php echo  $osData; ?></td>
						</tr>
						
					<?php
						}	
					if($resDescOd!="" || $resDescOs!=""){
						?>
							<tr>
								<td style="width:350px" class="bdrbtm"><span class="text_lable">Description:&nbsp;</span><?php echo htmlentities($resDescOd); ?></td>
								<td style="width:350px" class="bdrbtm"><span class="text_lable">Description:&nbsp;</span><?php echo htmlentities($resDescOs); ?></td>
							</tr>
						<?php				
					}
					?>
				
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
			
			if($fuRetina == 1) $treatment.= 'F/U Retina&nbsp;'.$fuRetinaDesc.' &nbsp;&nbsp;';
			if($treatment!="" || $fuRetinaDesc!=""){
			?>
			
				<?php 
				if($treatment!=""){
				?>
				<tr>
					<td colspan="2" style="width:700px" class="bdrbtm"><b>Treatment/Prognosis:</b>&nbsp;<?php echo $treatment; ?></td>
				</tr>
						
				<?php 
				}
				if($discComments){
				?>
				<tr>
					<td colspan="2" style="width:700px" class="bdrbtm"><?php echo "<span class='text_lable'>Comments:</span>".htmlentities($discComments); ?></td>						
				</tr>
				<?php
				}
				?>
			
				<?php
			}
			
			///Add discExternal Images//
			$imagesHtml=getTestImages($disc_id,$sectionImageFrom="discExternal",$patient_id);

			//GET IMAGES FOR PDFs THAT ARE HIGHER THAN PDF VERSION 1.4 . HIGHER VERSION IMAGES ARE SKIPPED IN merge_pdf.php FILE.
			$arr_alternative_images=array();
			$str_alternative_images='';
			if($disc_id!=""){
				$arrPDFs = getAllTestPdf($disc_id);
				$str_alternative_images= checknMakeImagesHTML($arrPDFs);
				$imagesHtml.=$str_alternative_images;
			}	
			//------------------------------------			

			if($imagesHtml!=""){
				echo('<tr><td colspan="2" class="bdrbtm" style="width:700px;" ><table style="width:700px;">
						'.$imagesHtml.' 
					</table></td></tr>');
			} 
			$imagesHtml="";
			//End discExternal Images
			
			if($phyName){
				/*$getphysicianQry = imw_query("SELECT CONCAT_WS(', ', lname, fname) as physician FROM users WHERE id = '$phyName'");
				$getphysicianRow = imw_fetch_assoc($getphysicianQry);
				$physicianName = str_replace(", ,"," ",$getphysicianRow['physician']);*/
				$physicianName = print_phyInitial($phyName);
				?>
				
					<tr >
						<td style="width:700px;" class="bdrbtm" colspan="2"><span class="text_lable">Interpreted By:&nbsp;</span> <?php echo $physicianName; ?></td>
					</tr>
				
				<?php
			}			
	?>
    </table>
    <?php		
	}//end External While
 }else{ ?>
<table style="width:740px;" class="border" cellpadding="0" cellspacing="0">
            <tr>
                <td class="tb_heading">External</td>
            </tr>
            <tr>
            	<td>No Data</td>
            </tr>		
       </table>  
<?php 
}

}

function print_topo($patient_id,$form_id,$req){

	if(is_array($req) && count($req)>0){
		$sql = "SELECT * FROM topography WHERE patientId = '".$patient_id."' AND topo_id in(".implode(",",$req).")";		
	}else if($form_id){
		$sql = "SELECT * FROM topography WHERE patientId = '".$patient_id."' AND formId = '$form_id' AND purged='0' ";		
	}
	$rowtopography = imw_query($sql);
	if(imw_num_rows($rowtopography)>0){
		while($row=imw_fetch_array($rowtopography)){
		extract($row);
		?>
			<table style="width:750px;" class="border" cellpadding="0" cellspacing="0">
				<tr>
					<td colspan="2" class="tb_heading">Topography
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
					(<span class="text_value">Exam Date:&nbsp;<?php print get_date_format($examDate);?></span>)
				</td>
				</tr>
			<?php
			if($techComments!=""){
			?>
			<tr>
				<td colspan="2" style="width:740px;" class="bdrbtm">
					<span class="text_lable">Technician Comments:&nbsp;</span><?php echo htmlentities($techComments); ?>						
				</td>
			</tr>
			<?php }?>
			
			<?php

			/*$getNameQry = imw_query("SELECT CONCAT_WS(', ', lname, fname) as performedBy FROM users WHERE id = '$performedBy'");
			if(imw_num_rows($getNameQry)>0){
				$getNameRow = imw_fetch_assoc($getNameQry);
				$performedBy = $getNameRow['performedBy'];
			}*/
				$performedBy = print_phyInitial($performedBy);
			?>
			
				<tr>
				   <td colspan="2" style="width:740px;" class="bdrbtm">
					<?php 
						if($performedBy) echo '<span class="text_lable">Performed By:&nbsp;</span> '.$performedBy.'&nbsp;&nbsp;';
						if($ptUnderstanding) echo '<span class="text_lable">Patient Understanding & Cooperation</span>:&nbsp;'.$ptUnderstanding;
					?>
				  </td>					
				</tr>				
			
			<?php 
			if($diagnosis=="Other"&&!empty($diagnosisOther)){$diagnosis=$diagnosisOther;}
			if($diagnosis && $diagnosis!="--Select--"){
			?>
			
						<tr>
							<td colspan="2" style="width:740px;" class="bdrbtm"><span class="text_lable">Diagnosis:&nbsp;</span> <?php echo ($diagnosis!="--Select--") ? $diagnosis : ""; ?></td>
						</tr>
			
			<?php
			}			
			if($reliabilityOd || $reliabilityOs || $descOd || $descOs){
			
					if($reliabilityOd || $reliabilityOs){
					?>
                    <tr>
                    	<td colspan="2" style="width:740px;">
                            <table style="width:740px;" cellpadding="0" cellspacing="0" >		
                                <tr>	
                                        <td style="width:180px;" class="bdrbtm text_lable">Physician Interpretation:&nbsp;</td>						
                                        <td style="width:80px;" class="bdrbtm text_lable">Reliability</td>
                                        <td style="width:40px;" class="bdrbtm text_lable"><?php odLable();?></td>
                                        <td style="width:150px;" class="bdrbtm text_value"><?php echo $reliabilityOd; ?></td>
                                        <td style="width:40px;" class="bdrbtm text_lable"><?php osLable();?></td>
                                        <td style="width:150px;" class="bdrbtm text_value"><?php echo $reliabilityOs; ?></td>
                              </tr>
                            </table>
                        </td>
     	           </tr>
				<?php }
				if($descOd || $descOs){
				?>
				
					<tr>
						<td  class="tb_subheading" colspan="2">Test Results</td>
					</tr>
					<tr>
						<td style="width:350px;" class="bdrbtm"><?php odLable();?></td>
						<td style="width:350px;" class="bdrbtm"><?php osLable();?></td>
					</tr>					
					<tr>
						<td style="width:350px;" class="bdrbtm"><?php echo  $descOd; ?></td>
						<td style="width:350px;" class="bdrbtm"><?php echo  $descOs; ?></td>
					</tr>				
				
				
				<?php }
				
			}
			
			$treatment = '';
			if($treat){
				if($treat=="Other" && $treat_other!=""){$treat=$treat_other;}
				$treatment.=$treat."<br>";
			}
			
			if(!empty($prog)){
				$arrTmp = explode(",",$prog);
				if(in_array("Stable",$arrTmp)){
				    $treatment.= 'Stable&nbsp;&nbsp;';
				}
				if(in_array("Progressive",$arrTmp)){
				    $treatment.= 'Progressive&nbsp;&nbsp;';
				}
				if(in_array("Repeat Scan",$arrTmp)){
				    $treatment.= 'Repeat Scan&nbsp;&nbsp;';
				}
			}
			
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
			
					
			<?php if($treatment!=""){	?>
				<tr>
					<td style="width:700px;" colspan="2" class="bdrbtm"><b>Treatment/Prognosis:</b>&nbsp;<?php echo $treatment; ?></td>
				</tr>
			<?php 
				}
				if($comments!=""){
					?>
					
						<tr>
							<td style="width:700px;" colspan="2" class="bdrbtm"><b>Comments:</b>&nbsp;<?php echo nl2br(htmlentities(wordwrap($comments,95,"\n",true))); ?></td>
						</tr>
					
					<?php
					}
					?>

			<?php
			}
			///Add Topogrphy Images//
					$imagesHtml=getTestImages($topo_id,$sectionImageFrom="Topogrphy",$patient_id);

					//GET IMAGES FOR PDFs THAT ARE HIGHER THAN PDF VERSION 1.4 . HIGHER VERSION IMAGES ARE SKIPPED IN merge_pdf.php FILE.
					$arr_alternative_images=array();
					$str_alternative_images='';
					if($topo_id!=""){
						$arrPDFs = getAllTestPdf($topo_id);
						$str_alternative_images= checknMakeImagesHTML($arrPDFs);
						$imagesHtml.=$str_alternative_images;
					}	
					//------------------------------------
										
					if($imagesHtml!=""){
						echo('<tr><td colspan="2" style="width:700px;" class="bdrbtm"><table style="width:700px;">
							 	'.$imagesHtml.' 
							</table></td></tr>');
					} 
					$imagesHtml="";
			//End Topogrphy Images
			if($phyName){
				/*$getphysicianQry = imw_query("SELECT CONCAT_WS(', ', lname, fname) as physician FROM users WHERE id = '$phyName'");
				$getphysicianRow = imw_fetch_assoc($getphysicianQry);
				$physicianName = str_replace(", ,"," ",$getphysicianRow['physician']);*/
				$physicianName = print_phyInitial($phyName);
				?>
				
					<tr >
						<td colspan="2" style="width:700px;" class="bdrbtm"><span class="text_lable">Interpreted By:&nbsp;</span> <?php echo $physicianName; ?></td>
					</tr>
				
				<?php
			}
			?>
            </table>
		<?php
		}		//End Of topogroahy while
	}else{ ?>
    	<table style="width:740px;" class="border" cellpadding="0" cellspacing="0">
                <tr>
                    <td class="tb_heading">Topography</td>
                </tr>
                <tr>
                    <td>No Data</td>
                </tr>		
           </table>  
    <?php 
    }

}



function print_bscan($patient_id,$form_id,$req){

	if(is_array($req) && count($req)>0){
		$sql = "SELECT * FROM test_bscan WHERE patientId = '".$patient_id."' AND test_bscan_id in(".implode(",",$req).")";		
	}else if($form_id){
		$sql = "SELECT * FROM test_bscan WHERE patientId = '".$patient_id."' AND formId = '$form_id' AND purged='0' ";		
	}
	$row = imw_query($sql);
	if(imw_num_rows($row)>0){
		while($row=imw_fetch_array($row)){
		extract($row);
		?>
			<table style="width:750px;" class="border" cellpadding="0" cellspacing="0">
				<tr>
					<td colspan="2" class="tb_heading">B-Scan
					<span class="text_lable">
						<?php 
						if($test_bscan_eye){
							if(trim($test_bscan_eye) == 'OD') $test_bscan_eye = '<span style="color:blue;">'.$test_bscan_eye.'</span>';
							if(trim($test_bscan_eye) == 'OS') $test_bscan_eye = '<span style="color:green;">'.$test_bscan_eye.'</span>';
							if(trim($test_bscan_eye) == 'OU') $test_bscan_eye = '<span style="color:purple;">'.$test_bscan_eye.'</span>';
							echo ''.$test_bscan_eye.'';
						}
						?>
					</span>
					(<span class="text_value">Exam Date:&nbsp;<?php print get_date_format($examDate);?></span>)
				</td>
				</tr>
			<?php
			if($techComments!=""){
			?>
			<tr>
				<td colspan="2" class="bdrbtm" style="width:700px;">
					<span class="text_lable">Technician Comments:&nbsp;</span><?php echo $techComments; ?>						
				</td>
			</tr>
			<?php }?>
			
			<?php

			/*$getNameQry = imw_query("SELECT CONCAT_WS(', ', lname, fname) as performedBy FROM users WHERE id = '$performedBy'");
			if(imw_num_rows($getNameQry)>0){
				$getNameRow = imw_fetch_assoc($getNameQry);
				$performedBy = $getNameRow['performedBy'];
			}*/
				$performedBy = print_phyInitial($performedBy);
			?>
			
				<tr>
				   <td colspan="2" style="width:700px;" class="bdrbtm">
					<?php 
						if($performedBy) echo '<span class="text_lable">Performed By:&nbsp;</span> '.$performedBy.'&nbsp;&nbsp;';
						if($ptUnderstanding) echo '<span class="text_lable">Patient Understanding & Cooperation</span>:&nbsp;'.$ptUnderstanding;
					?>
				  </td>					
				</tr>				
			
			<?php 
			if($diagnosis=="Other"&&!empty($diagnosisOther)){$diagnosis=$diagnosisOther;}
			if($diagnosis && $diagnosis!="--Select--"){
			?>
    
                <tr>
                    <td colspan="2" style="width:700px;" class="bdrbtm"><span class="text_lable">Diagnosis:&nbsp;</span> <?php echo ($diagnosis!="--Select--") ? $diagnosis : ""; ?></td>
                </tr>
			
			<?php
			}			
			if($reliabilityOd || $reliabilityOs || $descOd || $descOs){
			
					if($reliabilityOd || $reliabilityOs){
					?>
                <tr>
                    <td colspan="2" style="width:700px;">
                        <table style="width:700px;" cellpadding="0" cellspacing="0" >		
                            <tr>	
                                    <td style="width:180px;" class="bdrbtm text_lable">Physician Interpretation:&nbsp;</td>						
                                    <td style="width:80px;" class="bdrbtm text_lable">Reliability</td>
                                    <td style="width:40px;" class="bdrbtm text_lable"><?php odLable();?></td>
                                    <td style="width:150px;" class="bdrbtm text_value"><?php echo $reliabilityOd; ?></td>
        
                                    <td style="width:40px;" class="bdrbtm text_lable"><?php osLable();?></td>
                                    <td style="width:150px;" class="bdrbtm text_value"><?php echo $reliabilityOs; ?></td>
                          </tr>
                        </table>
                	</td>
                </tr>
				<?php }
				if($descOd || $descOs){
				?>
				
					<tr>
						<td  class="tb_subheading bdrbtm" colspan="2">Test Results</td>
					</tr>
					<tr>
						<td style="width:350px;" class="bdrbtm"><?php odLable();?></td>
						<td style="width:350px;" class="bdrbtm"><?php osLable();?></td>
					</tr>					
					<tr>
						<td style="width:350px;" class="bdrbtm"><?php echo  $tstod."<br/>".$descOd; ?></td>
						<td style="width:350px;" class="bdrbtm"><?php echo  $tstos."<br/>".$descOs; ?></td>
					</tr>				
				
				
				<?php }
				
			}
			
			$treatment = '';
			if($stable == 1) $treatment.= 'Stable&nbsp;&nbsp;';
			if($contiMeds == 1) $treatment.= 'Continue Meds&nbsp;';
			if($fuApa == 1) $treatment.= 'F/U APA&nbsp;&nbsp;';
			if($tech2InformPt == 1) $treatment.= 'Tech to Inform Pt.&nbsp;';
			if($ptInformed == 1) $treatment.= 'Pt informed of results&nbsp;&nbsp;';
			if($ptInformedNv == 1) $treatment.= 'Inform Pt result next visit&nbsp;';										
			if(!empty($treat)) $treatment.= $treat.'&nbsp;';
			
			if($treatment!=""  || $comments!=""){
			?>
			
			<?php if($treatment!=""){	?>
				<tr>
					<td colspan="2" class="bdrbtm" style="width:700px;"><b>Treatment/Prognosis:</b>&nbsp;<?php echo $treatment; ?></td>
				</tr>
			<?php 
				}
				if($comments!=""){
					?>
						<tr>
							<td colspan="2" class="bdrbtm" style="width:700px;"><b>Comments:</b>&nbsp;<?php echo $comments; ?></td>
						</tr>
					<?php
					}
					?>

			<?php
			}
			///Add bscan Images//
					$imagesHtml=getTestImages($test_bscan_id,$sectionImageFrom="Bscan",$patient_id);

					//GET IMAGES FOR PDFs THAT ARE HIGHER THAN PDF VERSION 1.4 . HIGHER VERSION IMAGES ARE SKIPPED IN merge_pdf.php FILE.
					$arr_alternative_images=array();
					$str_alternative_images='';
					if($test_bscan_id!=""){
						$arrPDFs = getAllTestPdf($test_bscan_id);
						$str_alternative_images= checknMakeImagesHTML($arrPDFs);
						$imagesHtml.=$str_alternative_images;
					}	
					//------------------------------------
										
					if($imagesHtml!=""){
						echo('<tr><td colspan="2" style="width:700px;" class="bdrbtm"><table style="width:700px;" >
							 	'.$imagesHtml.' 
							</table></td></tr>');
					} 
					$imagesHtml="";
			//End bscan Images
			if($phyName){
				/*$getphysicianQry = imw_query("SELECT CONCAT_WS(', ', lname, fname) as physician FROM users WHERE id = '$phyName'");
				$getphysicianRow = imw_fetch_assoc($getphysicianQry);
				$physicianName = str_replace(", ,"," ",$getphysicianRow['physician']);*/
				$physicianName = print_phyInitial($phyName);
				?>
					<tr >
						<td colspan="2" class="bdrbtm" style="width:700px;" ><span class="text_lable">Interpreted By:&nbsp;</span> <?php echo $physicianName; ?></td>
					</tr>
				
				<?php
			}
			?>
            </table>
            <?php
		}		//End Of bscan while
	}else{ ?>
  	  <table style="width:740px;" class="border" cellpadding="0" cellspacing="0">
                <tr>
                    <td class="tb_heading">B-Scan</td>
                </tr>
                <tr>
                    <td>No Data</td>
                </tr>		
           </table>  
    <?php 
    }

}

//================= FUNCTION TO GET K HEADING NAMES
function print_getKHeadingName($ID){		
	$getKreadingIdStr = "SELECT * FROM kheadingnames WHERE kheadingId = '$ID'";
	$getKreadingIdQry = imw_query($getKreadingIdStr);
	$getKreadingIdRow = imw_fetch_array($getKreadingIdQry);
		$kReadingHeadingName = 'K['.$getKreadingIdRow['kheadingName'].']';
	return $kReadingHeadingName;
}
//================= FUNCTION TO GET K HEADING NAMES

//================= FUNCTION TO GET LENSE TYPE
function print_getLenseName($lenseID){
	$getLenseTypeStr = "SELECT * FROM lenses_iol_type WHERE iol_type_id = '$lenseID'";
	$getLenseTypeQry = imw_query($getLenseTypeStr);
	$getLenseTypeRow = imw_fetch_array($getLenseTypeQry);
	$lenses_iol_type = $getLenseTypeRow['lenses_iol_type'];
	return $lenses_iol_type;
}
//================= FUNCTION TO GET LENSE TYPE

//================= FUNCTION TO GET LENSES FORMULA HEADING NAME
function print_getFormulaHeadName($id){
	$getFormulaheadingsStr = "SELECT * FROM formulaheadings WHERE formula_id = '$id'";
	$getFormulaheadingsQry = imw_query($getFormulaheadingsStr);
	$getFormulaheadingsRow = imw_fetch_array($getFormulaheadingsQry);
	$formula_heading_name = $getFormulaheadingsRow['formula_heading_name'];
	return $formula_heading_name;
}
//================= FUNCTION TO GET LENSES FORMULA HEADING NAME

function print_getData($fieldReq, $tableName, $idField, $val){
	$getDetailsStr = "SELECT $fieldReq FROM $tableName WHERE $idField = '$val'";
	$getDetailsQry = imw_query($getDetailsStr);
	$getDetailsRow = imw_fetch_array($getDetailsQry);
	return $getDetailsRow[$fieldReq];
}


//Ascan
function print_ascan($patient_id,$form_id,$req){

if(is_array($req) && count($req)>0){
	$printId = $req[0];
	$getSurgicalRecordStr = "SELECT * FROM surgical_tbl WHERE surgical_id = '$printId'";	
}else{
	$getSurgicalRecordStr = "SELECT * FROM surgical_tbl WHERE patient_id = '$patient_id' AND form_id = '$form_id'";
}
//echo($getSurgicalRecordStr);
$getSurgicalRecordQry = imw_query($getSurgicalRecordStr);
$rowsCount = imw_num_rows($getSurgicalRecordQry);
if($rowsCount>0){
	$getSurgicalRecordRows = imw_fetch_array($getSurgicalRecordQry);
		$surgical_id = $getSurgicalRecordRows['surgical_id'];
		$patient_id = $getSurgicalRecordRows['patient_id'];
		$form_id = $getSurgicalRecordRows['form_id'];
		$vis_mr_od_s = $getSurgicalRecordRows['mrSOD'];
		$vis_mr_od_c = $getSurgicalRecordRows['mrCOD'];
		$vis_mr_od_a = $getSurgicalRecordRows['mrAOD'];
		$visionOD = $getSurgicalRecordRows['visionOD'];
		$glareOD = $getSurgicalRecordRows['glareOD'];
		$performedByOD = $getSurgicalRecordRows['performedByOD'];
		
			$performedByODFname = print_getData('fname', 'users', 'id', $performedByOD);			
			$performedByODLname = print_getData('lname', 'users', 'id', $performedByOD);
			$performedByOD = $performedByODFname." ".$performedByODLname;
		
		$dateOD = $getSurgicalRecordRows['dateOD'];
			list($dateODYear, $dateODMonth, $dateODDay) = split("-", $dateOD);
			$dateOD = $dateODMonth."-".$dateODDay."-".$dateODYear;
			
		$autoSelectOD = $getSurgicalRecordRows['autoSelectOD'];
			$autoSelectOD = print_getKHeadingName($autoSelectOD);
		$iolMasterSelectOD = $getSurgicalRecordRows['iolMasterSelectOD'];
			$iolMasterSelectOD = print_getKHeadingName($iolMasterSelectOD);
		$topographerSelectOD = $getSurgicalRecordRows['topographerSelectOD'];
			$topographerSelectOD = print_getKHeadingName($topographerSelectOD);
		$vis_ak_od_k = $getSurgicalRecordRows['k1Auto1OD'];
		$vis_ak_od_x = $getSurgicalRecordRows['k1Auto2OD'];
		$k1IolMaster1OD = $getSurgicalRecordRows['k1IolMaster1OD'];
		$k1IolMaster2OD = $getSurgicalRecordRows['k1IolMaster2OD'];
		$k1Topographer1OD = $getSurgicalRecordRows['k1Topographer1OD'];
		$k1Topographer2OD = $getSurgicalRecordRows['k1Topographer2OD'];
		$vis_ak_od_slash = $getSurgicalRecordRows['k2Auto1OD'];
		$k2Auto2OD = $getSurgicalRecordRows['k2Auto2OD'];
		$k2IolMaster1OD = $getSurgicalRecordRows['k2IolMaster1OD'];
		$k2IolMaster2OD = $getSurgicalRecordRows['k2IolMaster2OD'];
		$k2Topographer1OD = $getSurgicalRecordRows['k2Topographer1OD'];
		$k2Topographer2OD = $getSurgicalRecordRows['k2Topographer2OD'];
		$cylAuto1OD = $getSurgicalRecordRows['cylAuto1OD'];
		$cylAuto2OD = $getSurgicalRecordRows['cylAuto2OD'];
		$cylIolMaster1OD = $getSurgicalRecordRows['cylIolMaster1OD'];
		$cylIolMaster2OD = $getSurgicalRecordRows['cylIolMaster2OD'];
		$cylTopographer1OD = $getSurgicalRecordRows['cylTopographer1OD'];
		$cylTopographer2OD = $getSurgicalRecordRows['cylTopographer2OD'];
		$aveOD1 = $getSurgicalRecordRows['aveAutoOD'];
		$aveIolMasterOD = $getSurgicalRecordRows['aveIolMasterOD'];
		$aveTopographerOD = $getSurgicalRecordRows['aveTopographerOD'];
		$contactLengthOD = $getSurgicalRecordRows['contactLengthOD'];
		$immersionLengthOD = $getSurgicalRecordRows['immersionLengthOD'];
		$iolMasterLengthOD = $getSurgicalRecordRows['iolMasterLengthOD'];
		$contactNotesOD = $getSurgicalRecordRows['contactNotesOD'];
		$immersionNotesOD = $getSurgicalRecordRows['immersionNotesOD'];
		$iolMasterNotesOD = $getSurgicalRecordRows['iolMasterNotesOD'];
		$provider_idOD = $getSurgicalRecordRows['performedByPhyOD'];
				$performedByODFname = print_getData('fname', 'users', 'id', $provider_idOD);
				$performedByODLname = print_getData('lname', 'users', 'id', $provider_idOD);
				$provider_idOD = $performedByODFname." ".$performedByODLname;
			
		$powerIolOD = $getSurgicalRecordRows['powerIolOD'];
			$powerIolOD = print_getFormulaHeadName($powerIolOD);
		$holladayOD = $getSurgicalRecordRows['holladayOD'];
			$holladayOD = print_getFormulaHeadName($holladayOD);
		$srk_tOD = $getSurgicalRecordRows['srk_tOD'];
			$srk_tOD = print_getFormulaHeadName($srk_tOD);
		$hofferOD = $getSurgicalRecordRows['hofferOD'];
			$hofferOD = print_getFormulaHeadName($hofferOD);
		$iol1OD = $getSurgicalRecordRows['iol1OD'];
			$iol1OD = print_getLenseName($iol1OD);
		$iol1PowerOD = $getSurgicalRecordRows['iol1PowerOD'];
		$iol1HolladayOD = $getSurgicalRecordRows['iol1HolladayOD'];
		$iol1srk_tOD = $getSurgicalRecordRows['iol1srk_tOD'];
		$iol1HofferOD = $getSurgicalRecordRows['iol1HofferOD'];
		$iol2OD = $getSurgicalRecordRows['iol2OD'];
			$iol2OD = print_getLenseName($iol2OD);
		$iol2PowerOD = $getSurgicalRecordRows['iol2PowerOD'];
		$iol2HolladayOD = $getSurgicalRecordRows['iol2HolladayOD'];
		$iol2srk_tOD = $getSurgicalRecordRows['iol2srk_tOD'];
		$iol2HofferOD = $getSurgicalRecordRows['iol2HofferOD'];
		$iol3OD = $getSurgicalRecordRows['iol3OD'];
			$iol3OD = print_getLenseName($iol3OD);
		$iol3PowerOD = $getSurgicalRecordRows['iol3PowerOD'];
		$iol3HolladayOD = $getSurgicalRecordRows['iol3HolladayOD'];
		$iol3srk_tOD = $getSurgicalRecordRows['iol3srk_tOD'];
		$iol3HofferOD = $getSurgicalRecordRows['iol3HofferOD'];
		$iol4OD = $getSurgicalRecordRows['iol4OD'];
			$iol4OD = print_getLenseName($iol4OD);
		$iol4PowerOD = $getSurgicalRecordRows['iol4PowerOD'];
		$iol4HolladayOD = $getSurgicalRecordRows['iol4HolladayOD'];
		$iol4srk_tOD = $getSurgicalRecordRows['iol4srk_tOD'];
		$iol4HofferOD = $getSurgicalRecordRows['iol4HofferOD'];
		$cellCountOD = $getSurgicalRecordRows['cellCountOD'];
		$notesOD = $getSurgicalRecordRows['notesOD'];
		$pachymetryValOD = $getSurgicalRecordRows['pachymetryValOD'];
		$pachymetryCorrecOD = $getSurgicalRecordRows['pachymetryCorrecOD'];
		$cornealDiamOD = $getSurgicalRecordRows['cornealDiamOD'];
		$dominantEyeOD = $getSurgicalRecordRows['dominantEyeOD'];
		$pupilSize1OD = $getSurgicalRecordRows['pupilSize1OD'];
		$pupilSize2OD = $getSurgicalRecordRows['pupilSize2OD'];
		$cataractOD = $getSurgicalRecordRows['cataractOD'];
		$astigmatismOD = $getSurgicalRecordRows['astigmatismOD'];
		$myopiaOD = $getSurgicalRecordRows['myopiaOD'];
		$selecedIOLsOD = $getSurgicalRecordRows['selecedIOLsOD'];
			$selecedIOLsOD = print_getLenseName($selecedIOLsOD);
		$notesAssesmentPlansOD = $getSurgicalRecordRows['notesAssesmentPlansOD'];
		$lriOD = $getSurgicalRecordRows['lriOD'];
		$dlOD = $getSurgicalRecordRows['dlOD'];
		$synechiolysisOD = $getSurgicalRecordRows['synechiolysisOD'];
		$irishooksOD = $getSurgicalRecordRows['irishooksOD'];
		$trypanblueOD = $getSurgicalRecordRows['trypanblueOD'];
		$flomaxOD = $getSurgicalRecordRows['flomaxOD'];
		$cutsOD = $getSurgicalRecordRows['cutsOD'];
		$lengthOD = $getSurgicalRecordRows['lengthOD'];
		$lengthTypeOD = $getSurgicalRecordRows['lengthTypeOD'];
			if($lengthTypeOD == 'percent') $lengthTypeOD = '%';
		$axisOD = $getSurgicalRecordRows['axisOD'];
		$superiorOD = $getSurgicalRecordRows['superiorOD'];
		$inferiorOD = $getSurgicalRecordRows['inferiorOD'];
		$nasalOD = $getSurgicalRecordRows['nasalOD'];
		$temporalOD = $getSurgicalRecordRows['temporalOD'];
		$STOD = $getSurgicalRecordRows['STOD'];
		$SNOD = $getSurgicalRecordRows['SNOD'];
		$ITOD = $getSurgicalRecordRows['ITOD'];
		$INOD = $getSurgicalRecordRows['INOD'];
		$mrSOS = $getSurgicalRecordRows['mrSOS'];
		$mrCOS = $getSurgicalRecordRows['mrCOS'];
		$mrAOS = $getSurgicalRecordRows['mrAOS'];
		$visionOS = $getSurgicalRecordRows['visionOS'];
		$glareOS = $getSurgicalRecordRows['glareOS'];
		$provider_idOS = $getSurgicalRecordRows['performedByOS'];
				$performedByOSFname = print_getData('fname', 'users', 'id', $provider_idOS);
				$performedByOSLname = print_getData('lname', 'users', 'id', $provider_idOS);
				$performedByOS = $performedByOSFname." ".$performedByOSLname;
		
		$dateOS = $getSurgicalRecordRows['dateOS'];
			list($dateOSYear, $dateOSMonth, $dateOSDay) = split("-", $dateOS);
			$dateOS = $dateOSMonth."-".$dateOSDay."-".$dateOSYear;
		
		$autoSelectOS = $getSurgicalRecordRows['autoSelectOS'];
			$autoSelectOS = print_getKHeadingName($autoSelectOS);
		$iolMasterSelectOS = $getSurgicalRecordRows['iolMasterSelectOS'];
			$iolMasterSelectOS = print_getKHeadingName($iolMasterSelectOS);
		$topographerSelectOS = $getSurgicalRecordRows['topographerSelectOS'];
			$topographerSelectOS = print_getKHeadingName($topographerSelectOS);
		$k1Auto1OS = $getSurgicalRecordRows['k1Auto1OS'];
		$vis_ak_os_k = $getSurgicalRecordRows['k1Auto1OS'];
		$vis_ak_os_slash = $getSurgicalRecordRows['k2Auto1OS'];
		$k1Auto2OS = $getSurgicalRecordRows['k1Auto2OS'];		
		$k1IolMaster1OS = $getSurgicalRecordRows['k1IolMaster1OS'];
		$k1IolMaster2OS = $getSurgicalRecordRows['k1IolMaster2OS'];
		$k1Topographer1OS = $getSurgicalRecordRows['k1Topographer1OS'];
		$k1Topographer2OS = $getSurgicalRecordRows['k1Topographer2OS'];
		$k2Auto1OS = $getSurgicalRecordRows['k2Auto1OS'];
		$k2Auto2OS = $getSurgicalRecordRows['k2Auto2OS'];
		$k2IolMaster1OS = $getSurgicalRecordRows['k2IolMaster1OS'];
		$k2IolMaster2OS = $getSurgicalRecordRows['k2IolMaster2OS'];
		$k2Topographer1OS = $getSurgicalRecordRows['k2Topographer1OS'];
		$k2Topographer2OS = $getSurgicalRecordRows['k2Topographer2OS'];
		
		$cylAuto1OS = $getSurgicalRecordRows['cylAuto1OS'];
		$cylAuto2OS = $getSurgicalRecordRows['cylAuto2OS'];		
		$cylIolMaster1OS = $getSurgicalRecordRows['cylIolMaster1OS'];
		$cylIolMaster2OS = $getSurgicalRecordRows['cylIolMaster2OS'];
		$cylTopographer1OS = $getSurgicalRecordRows['cylTopographer1OS'];
		$cylTopographer2OS = $getSurgicalRecordRows['cylTopographer2OS'];
		
		$aveAutoOS = $getSurgicalRecordRows['aveAutoOS'];
		$aveIolMasterOS = $getSurgicalRecordRows['aveIolMasterOS'];
		$aveTopographerOS = $getSurgicalRecordRows['aveTopographerOS'];
		$contactLengthOS = $getSurgicalRecordRows['contactLengthOS'];
		$immersionLengthOS = $getSurgicalRecordRows['immersionLengthOS'];
		$iolMasterLengthOS = $getSurgicalRecordRows['iolMasterLengthOS'];
		$contactNotesOS = $getSurgicalRecordRows['contactNotesOS'];
		$immersionNotesOS = $getSurgicalRecordRows['immersionNotesOS'];
		$iolMasterNotesOS = $getSurgicalRecordRows['iolMasterNotesOS'];
		$performedByPhyOS = $getSurgicalRecordRows['performedByPhyOS'];
		
		$performedIolOS = $getSurgicalRecordRows['performedIolOS'];		
				$performedByPhyOSFname = print_getData('fname', 'users', 'id', $performedIolOS);
				$performedByPhyOSLname = print_getData('lname', 'users', 'id', $performedIolOS);
				$performedIolOS = $performedByPhyOSFname." ".$performedByPhyOSLname;
		
		$powerIolOS = $getSurgicalRecordRows['powerIolOS'];
			$powerIolOS = print_getFormulaHeadName($powerIolOS);
		$holladayOS = $getSurgicalRecordRows['holladayOS'];
			$holladayOS = print_getFormulaHeadName($holladayOS);
		$srk_tOS = $getSurgicalRecordRows['srk_tOS'];
			$srk_tOS = print_getFormulaHeadName($srk_tOS);
		$hofferOS = $getSurgicalRecordRows['hofferOS'];
			$hofferOS = print_getFormulaHeadName($hofferOS);
		$iol1OS = $getSurgicalRecordRows['iol1OS'];
			$iol1OS = print_getData('lenses_iol_type', 'lenses_iol_type', 'iol_type_id', $iol1OS);			
		$iol1PowerOS = $getSurgicalRecordRows['iol1PowerOS'];
		$iol1HolladayOS = $getSurgicalRecordRows['iol1HolladayOS'];
		$iol1srk_tOS = $getSurgicalRecordRows['iol1srk_tOS'];
		$iol1HofferOS = $getSurgicalRecordRows['iol1HofferOS'];
		$iol2OS = $getSurgicalRecordRows['iol2OS'];
			$iol2OS = print_getData('lenses_iol_type', 'lenses_iol_type', 'iol_type_id', $iol2OS);
		$iol2PowerOS = $getSurgicalRecordRows['iol2PowerOS'];
		$iol2HolladayOS = $getSurgicalRecordRows['iol2HolladayOS'];
		$iol2srk_tOS = $getSurgicalRecordRows['iol2srk_tOS'];
		$iol2HofferOS = $getSurgicalRecordRows['iol2HofferOS'];
		$iol3OS = $getSurgicalRecordRows['iol3OS'];
			$iol3OS = print_getData('lenses_iol_type', 'lenses_iol_type', 'iol_type_id', $iol3OS);
		$iol3PowerOS = $getSurgicalRecordRows['iol3PowerOS'];
		$iol3HolladayOS = $getSurgicalRecordRows['iol3HolladayOS'];
		$iol3srk_tOS = $getSurgicalRecordRows['iol3srk_tOS'];
		$iol3HofferOS = $getSurgicalRecordRows['iol3HofferOS'];
		$iol4OS = $getSurgicalRecordRows['iol4OS'];
			$iol4OS = print_getData('lenses_iol_type', 'lenses_iol_type', 'iol_type_id', $iol4OS);
		$iol4PowerOS = $getSurgicalRecordRows['iol4PowerOS'];
		$iol4HolladayOS = $getSurgicalRecordRows['iol4HolladayOS'];
		$iol4srk_tOS = $getSurgicalRecordRows['iol4srk_tOS'];
		$iol4HofferOS = $getSurgicalRecordRows['iol4HofferOS'];
		$cellCountOS = $getSurgicalRecordRows['cellCountOS'];
		$notesOS = $getSurgicalRecordRows['notesOS'];
		$pachymetryValOS = $getSurgicalRecordRows['pachymetryValOS'];
		$pachymetryCorrecOS = $getSurgicalRecordRows['pachymetryCorrecOS'];
		$cornealDiamOS = $getSurgicalRecordRows['cornealDiamOS'];
		$dominantEyeOS = $getSurgicalRecordRows['dominantEyeOS'];
		$pupilSize1OS = $getSurgicalRecordRows['pupilSize1OS'];
		$pupilSize2OS = $getSurgicalRecordRows['pupilSize2OS'];
		$cataractOS = $getSurgicalRecordRows['cataractOS'];
		$astigmatismOS = $getSurgicalRecordRows['astigmatismOS'];
		$myopiaOS = $getSurgicalRecordRows['myopiaOS'];
		$selecedIOLsOS = $getSurgicalRecordRows['selecedIOLsOS'];
			$selecedIOLsOS = print_getLenseName($selecedIOLsOS);
		$notesAssesmentPlansOS = $getSurgicalRecordRows['notesAssesmentPlansOS'];
		$lriOS = $getSurgicalRecordRows['lriOS'];
		$dlOS = $getSurgicalRecordRows['dlOS'];
		$synechiolysisOS = $getSurgicalRecordRows['synechiolysisOS'];
		$irishooksOS = $getSurgicalRecordRows['irishooksOS'];
		$trypanblueOS = $getSurgicalRecordRows['trypanblueOS'];
		$flomaxOS = $getSurgicalRecordRows['flomaxOS'];
		$cutsOS = $getSurgicalRecordRows['cutsOS'];
		$lengthOS = $getSurgicalRecordRows['lengthOS'];
		$lengthTypeOS = $getSurgicalRecordRows['lengthTypeOS'];		
			if($lengthTypeOS == 'percent') $lengthTypeOS = '%';
		$axisOS = $getSurgicalRecordRows['axisOS'];
		$superiorOS = $getSurgicalRecordRows['superiorOS'];
		$inferiorOS = $getSurgicalRecordRows['inferiorOS'];
		$nasalOS = $getSurgicalRecordRows['nasalOS'];
		$temporalOS = $getSurgicalRecordRows['temporalOS'];
		$STOS = $getSurgicalRecordRows['STOS'];
		$SNOS = $getSurgicalRecordRows['SNOS'];
		$ITOS = $getSurgicalRecordRows['ITOS'];
		$INOS = $getSurgicalRecordRows['INOS'];
		$signedById = $getSurgicalRecordRows['signedById'];		
		$signature = $getSurgicalRecordRows['signature'];
		$signedByOSId = $getSurgicalRecordRows['signedByOSId'];
		$signatureOS = $getSurgicalRecordRows['signatureOS'];
}


?>
<table cellpadding="0" cellspacing="0" align="left" border="0" style="display:none;width:100%;">
		
		<tr>
			<td align="center" style="width:100%;">
					
				<table border="0" cellpadding="0" cellspacing="0" style="width:100%;">
					<tr height="50">
						<td align="center" class="text_10b" style="width:50%;">
						<table border="0" cellpadding="0" cellspacing="0">
							<tr>
								<td class="text_10b"><span style="color:#0000FF;">OD</span></td>
								
							</tr>
						</table>
							<table border="0" cellpadding="0" cellspacing="0">
								<tr height="">
									<td class="text_10b" align="">MR</td>
									<td class="text_10b" width="5">&nbsp;</td>
									<td width="45" valign="middle">
										<table border="0" cellpadding="0" cellspacing="0">
											<td class="text_10b" align="left">S:&nbsp;</td>
											<td class="text_9" align="right"><?php if($vis_mr_od_s!='') echo $vis_mr_od_s; ?></td>
										</table>
									</td>
									<td width="10" valign="middle">&nbsp;</td>
									<td width="45" valign="middle">
										<table border="0" cellpadding="0" cellspacing="0">
											<td class="text_10b">C:&nbsp;</td>
											<td class="text_9"><?php if($vis_mr_od_c!='') echo $vis_mr_od_c; ?></td>
										</table>
									</td>
									<td width="10" valign="middle">&nbsp;</td>
									<td width="45" valign="middle">
										<table border="0" cellpadding="0" cellspacing="0">
											<td class="text_10b">A:&nbsp;</td>
											<td class="text_9"><?php if($vis_mr_od_a!='') echo $vis_mr_od_a; ?>&#176;</td>
										</table>
									</td>
									<td width="45" class="text_10b" align="right">Vision:</td>
									<td width="45" class="text_9"><?php if($visionOD!='') echo $visionOD; ?></td>
									<td width="10" class="text_10b" align="center">/</td>
									<td width="45" class="text_9" align="right"><?php if($glareOD!='') echo $glareOD; ?></td>
									<td width="15"></td>
								</tr>
								<tr class="text_9">
									<td class="text" align="center">&nbsp;</td>
									<td class="text" align="center">&nbsp;</td>
									<td class="text" align="center">&nbsp;</td>
									<td class="text" align="center">&nbsp;</td>
									<td class="text" align="center">&nbsp;</td>
									<td class="text" align="center">&nbsp;</td>
									<td class="text" align="center">&nbsp;</td>
									<td class="text" align="center">&nbsp;</td>
									<td width="45" class="text"  align="left" >Vision</td>
									<td width="10" class="text" align="center" >/</td>
									<td width="45" class="text" align="left" >Glare</td>
									<td class="text" align="center">&nbsp;</td>
							  </tr>
						  </table>
						</td>
						
						<td align="center" class="text_10b" style="width:50%;">
						<table border="0" cellpadding="0" cellspacing="0">
							<tr>
								<td class="text_10b"><span style="color:#009900;">OS</span></td>
							</tr>
						</table>
							<table border="0" cellpadding="0" cellspacing="0">
								<tr>
									<td class="text_10b" align="">MR</td>
									<td class="text_10b" width="5">&nbsp;</td>
									<td width="45" valign="middle">
										<table border="0" cellpadding="0" cellspacing="0">
											<td class="text_10b">S:&nbsp;</td>
											<td class="text_9"><?php if($mrSOS!='') echo $mrSOS; else echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"; ?></td>
										</table>
									</td>								
									<td width="10" valign="middle">&nbsp;</td>
									<td width="45" valign="middle">
										<table border="0" cellpadding="0" cellspacing="0">
											<td class="text_10b">C:&nbsp;</td>
											<td class="text_9"><?php if($mrCOS!='') echo $mrCOS; else echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"; ?></td>
										</table>
									</td>
									<td width="10" valign="middle">&nbsp;</td>
									<td width="45" valign="middle">
										<table border="0" cellpadding="0" cellspacing="0">
											<td class="text_10b">A:&nbsp;</td>
											<td class="text_9"><?php if($mrAOS!='') echo $mrAOS; else echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"; ?>&#176;</td>
										</table>
									</td>
									<td width="10" valign="middle">&nbsp;</td>
									<td width="45" class="text_10b" align="">Vision:</td>
									<td width="45" class="text_9"><?php if($visionOS!='') echo $visionOS; else echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"; ?></td>
									<td width="10" class="text_10b" align="">/</td>
									<td width="45" class="text_9"><?php if($glareOS!='') echo $glareOS; else echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"; ?></td>
									<td class="text_10b" align=""></td>
								</tr>
								<tr>
									<td class="text_9" align="center">&nbsp;</td>
									<td class="text_9" align="center">&nbsp;</td>
									<td class="text_9" align="center">&nbsp;</td>
									<td class="text_9" align="center">&nbsp;</td>
									<td class="text_9" align="center">&nbsp;</td>
									<td class="text_9" align="center">&nbsp;</td>
									<td class="text_9" align="center">&nbsp;</td>
									<td class="text_9" align="center">&nbsp;</td>
									<td class="text_9" align="center">&nbsp;</td>
									<td class="text_9" align="left" >Vision</td>
									<td class="text_9" align="center" >/</td>
									<td class="text_9" align="left" >Glare</td>
									<td class="text" align="center">&nbsp;</td>
								</tr>
						  </table>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td align="left" valign="top" style="width:45%;">
				<table border="0" cellpadding="0" cellspacing="0">
					<tr>
						<td style="width:45%;" align="left" valign="top">
							<table border="0" cellpadding="0" cellspacing="1" bordercolor="#FFFFFF" style="width:100%;">
									<tr height="20">
										<td align="left" class="text_10b" ><span style="color:#0000FF;font-size:<?php if($printAscan == 'printFromOutOfAScan') echo "10px"; else echo "14px"; ?>;font-weight:bold;">OD</span></td>
										<td align="left" class="text_10b">&nbsp;</td>
										<td align="left" class="text_10b">Performed By:</td>
										<td align="left" class="text_10b"><?php echo $performedByOD; ?></td>
										<td align="left" class="text_10b">Date:</td>
										<td align="left" class="text_9"><?php echo $dateOD; ?></td>
									</tr>
								</table>
								
									<table border="0" cellpadding="0" cellspacing="0">
										<tr height="10">
											<td colspan="6"></td>
										</tr>
										<tr>
											<td width="5"></td>
											<td align="left" class="text_10b"><?php echo $autoSelectOD; ?></td>
											<td width="5" align="left"></td>
											<td align="left" class="text_10b"><?php echo $iolMasterSelectOD; ?></td>
											<td width="5" align="left"></td>
											<td align="left" class="text_10b"><?php echo $topographerSelectOD; ?></td>
										</tr>
										<tr height="5">
											<td colspan="6"></td>
										</tr>
										<!-- K1 -->
										<tr height="20">
											<td width="5" class="text_10b">K1</td>
											<?php
											if($vis_ak_od_k){
												?>
												<td align="left">
													<table border="0" cellpadding="0" cellspacing="0">
														<tr>
															<td align="left" class="text_9" width="35"><?php if(($vis_ak_od_k!='') && ($vis_ak_od_k!=0)) echo number_format($vis_ak_od_k, 2); else echo "&nbsp;"; ?></td>
															<td align="center" class="text_9" width="10">X</td>
															<td align="left" class="text_9" width="35" style="padding-left:1px;"><?php if(($vis_ak_od_x!='') && ($vis_ak_od_x!=0)) echo $vis_ak_od_x; else echo "&nbsp;"; ?>&#176;</td>
														</tr>
													</table>
												</td>
												<?php
											}
											?>
											<td width="5"></td>
											<td align="left">
												<table border="0" cellpadding="0" cellspacing="0">
													<?php
													if($k1IolMaster1OD){
														?>
														<tr>
															<td align="left" class="text_9" width="35"><?php if(($k1IolMaster1OD!='') && ($k1IolMaster1OD!=0)) echo number_format($k1IolMaster1OD, 2); else echo "&nbsp;"; ?></td>
															<td align="center" class="text_9" width="10">X</td>
															<td align="left" class="text_9" width="35" style="padding-left:1px;"><?php if(($k1IolMaster2OD!='') && ($k1IolMaster2OD!=0)) echo $k1IolMaster2OD; else echo "&nbsp;"; ?>&#176;</td>
														</tr>
														<?php
													}
													?>
												</table>
											</td>
											<td width="5"></td>
											<td align="left">
												<table border="0" cellpadding="0" cellspacing="0">
													<?php
													if($k1Topographer1OD){
														?>
														<tr>
															<td align="left" class="text_9" width="35"><?php if(($k1Topographer1OD!='') && ($k1Topographer1OD!=0)) echo number_format($k1Topographer1OD, 2); else echo "&nbsp;"; ?></td>
															<td align="center" class="text_9" width="10">X</td>
															<td align="left" class="text_9" width="35" style="padding-left:1px;"><?php if(($k1Topographer2OD!='') && ($k1Topographer2OD!=0)) echo $k1Topographer2OD; else echo "&nbsp;"; ?>&#176;</td>
														</tr>
														<?php
													}
													?>
												</table>
											</td>
										</tr>
										<!-- K1 -->
										
										<!-- K2 -->
										<tr height="20">
											<td width="5" class="text_10b">K2</td>
											<td align="left">
												<table border="0" cellpadding="0" cellspacing="0">
													<?php
													if($vis_ak_od_k){
														?>
														<tr>
															<td align="left" class="text_9" width="35"><?php if(($vis_ak_od_slash!='') && ($vis_ak_od_slash!=0)) echo number_format($vis_ak_od_slash, 2); else echo "&nbsp;"; ?></td>
															<td align="center" class="text_9" width="10">X</td>
															<td align="left" class="text_9" width="35" style="padding-left:1px;"><?php if(($k2Auto2OD!='') && ($k2Auto2OD!=0)) echo $k2Auto2OD; else echo "&nbsp;"; ?>&#176;</td>
														</tr>
														<?php
													}
													?>
												</table>
											</td>
											<td width="5"></td>
											<td align="left">
												<table border="0" cellpadding="0" cellspacing="0">
													<?php
													if($k1IolMaster1OD){
														?>
														<tr>
															<td align="left" class="text_9" width="35"><?php if(($k2IolMaster1OD!='') && ($k2IolMaster1OD!=0)) echo number_format($k2IolMaster1OD, 2); else echo "&nbsp;"; ?></td>
															<td align="center" class="text_9" width="10">X</td>
															<td align="left" class="text_9" width="35" style="padding-left:1px;"><?php if(($k2IolMaster2OD!='') && ($k2IolMaster2OD!=0)) echo $k2IolMaster2OD; else echo "&nbsp;"; ?>&#176;</td>
														</tr>
														<?php
													}
													?>
												</table>
											</td>
											<td width="5"></td>
											<td align="left">
												<table border="0" cellpadding="0" cellspacing="0">
													<?php
													if($k1Topographer1OD){
														?>
														<tr>
															<td align="left" class="text_9" width="35"><?php if(($k2Topographer1OD!='') && ($k2Topographer1OD!=0)) echo number_format($k2Topographer1OD, 2); else echo "&nbsp;"; ?></td>
															<td align="center" class="text_9" width="10">X</td>
															<td align="left" class="text_9" width="35" style="padding-left:1px;"><?php if(($k2Topographer2OD!='') && ($k2Topographer2OD!=0)) echo $k2Topographer2OD; else echo "&nbsp;"; ?>&#176;</td>
														</tr>
														<?php
													}
													?>
												</table>
											</td>
										</tr>
										<!-- K2 -->
										
										<!-- CYL -->
										<tr height="20">
											<td width="5" class="text_10b">CYL</td>
											<td align="left">
												<table border="0" cellpadding="0" cellspacing="0">
													<?php
													if($vis_ak_od_k){
														?>
														<tr>
															<td align="left" class="text_9" width="35"><?php if(($cylAuto1OD!='') && ($cylAuto1OD!=0)) echo number_format($cylAuto1OD, 2); else echo "&nbsp;"; ?></td>
															<td align="center" class="text_9">@</td>
															<td align="left" class="text_9" width="35" style="padding-left:1px;"><?php if(($cylAuto2OD!='') && ($cylAuto2OD!=0)) echo $cylAuto2OD; else echo "&nbsp;"; ?>&#176;</td>
														</tr>
														<?php
													}
													?>
												</table>
											</td>
											<td width="5"></td>
											<td align="left">
												<table border="0" cellpadding="0" cellspacing="0">
													<?php
													if($k1IolMaster1OD){
														?>
														<tr>
															<td align="left" class="text_9" width="35"><?php if(($cylIolMaster1OD!='') && ($cylIolMaster1OD!=0)) echo number_format($cylIolMaster1OD, 2); else echo "&nbsp;"; ?></td>
															<td align="center" class="text_9">@</td>
															<td align="left" class="text_9" width="35" style="padding-left:1px;"><?php if(($cylIolMaster2OD!='') && ($cylIolMaster2OD!=0)) echo $cylIolMaster2OD; else echo "&nbsp;"; ?>&#176;</td>
														</tr>
														<?php
													}
													?>
												</table>
											</td>
											<td width="5"></td>
											<td align="left">
												<table border="0" cellpadding="0" cellspacing="0">
													<?php
													if($k1Topographer1OD){
														?>
														<tr>
															<td align="left" class="text_9" width="35"><?php if(($cylTopographer1OD!='') && ($cylTopographer1OD!=0)) echo number_format($cylTopographer1OD, 2); else echo "&nbsp;"; ?></td>
															<td align="center" class="text_9">@</td>
															<td align="left" class="text_9" width="35" style="padding-left:1px;"><?php if(($cylTopographer2OD!='') && ($cylTopographer2OD!=0)) echo $cylTopographer2OD; else echo "&nbsp;"; ?>&#176;</td>
														</tr>
														<?php
													}
													?>
												</table>
											</td>
										</tr>
										<!-- CYL -->
	
										<!-- AVE -->
										<tr height="20">
											<td width="5" class="text_10b">AVE</td>
											<td align="left">
												<table border="0" cellpadding="0" cellspacing="0">
													<?php
													if($vis_ak_od_k){
														?>
														<tr>
															<td align="center" class="text_9"><?php if(($aveOD1!='') && ($aveOD1!=0))  echo number_format($aveOD1, 2); else echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"; ?></td>
														</tr>
														<?php
													}
													?>
												</table>
											</td>
											<td width="5"></td>
											<td align="left">
												<table border="0" cellpadding="0" cellspacing="0">
													<?php
													if($k1IolMaster1OD){
														?>
														<tr>
															<td align="center" class="text_9"><?php if(($aveIolMasterOD!='') && ($aveIolMasterOD!=0)) echo number_format($aveIolMasterOD, 2); else echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"; ?></td>
														</tr>
														<?php
													}
													?>
												</table>
											</td>
											<td width="5"></td>
											<td align="left">
												<table border="0" cellpadding="0" cellspacing="0">
													<?php
													if($k1Topographer1OD){
														?>
														<tr>
															<td align="center" class="text_9"><?php if(($aveTopographerOD!='') && ($aveTopographerOD!=0)) echo number_format($aveTopographerOD, 2); else echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"; ?></td>
														</tr>
														<?php
													}
													?>
												</table>
											</td>
										</tr>
										<!-- AVE -->
										
										<tr height="20">
											<td colspan="6" align="left">
												<table border="0" cellpadding="0" cellspacing="1">
													<tr height="10">
														<td colspan="4"></td>
													</tr>
													<tr height="">
														<td align="left" class="text_10b">Axial</td>
														<td align="left" class="text_10b" width="86" style="padding-left:10px;">Contact</td>
														<td align="left" class="text_10b" width="86" style="padding-left:2px;">Immersion</td>
														<td align="left" class="text_10b" width="86" style="padding-left:2px;">IOL Master</td>
													</tr>
													<tr height="">
														<td align="left" class="text_10b">Length</td>
														<td align="left" class="text_9" style="padding-left:10px;"><?php echo $contactLengthOD; ?></td>
														<td align="left" class="text_9" style="padding-left:2px;"><?php echo $immersionLengthOD; ?></td>
														<td align="left" class="text_9" style="padding-left:2px;"><?php echo $iolMasterLengthOD; ?></td>
													</tr>
													<tr height="20">
														<?php
														if(($contactNotesOD) || ($immersionNotesOD) || ($iolMasterNotesOD)){
															?>
															<td align="left" class="text_10b">Notes</td>
															<td align="left" class="text_9" style="padding-left:2px;"><?php echo nl2br($contactNotesOD); ?></td>
															<td align="left" class="text_9" style="padding-left:2px;"><?php echo nl2br($immersionNotesOD); ?></td>
															<td align="left" class="text_9" style="padding-left:2px;"><?php echo nl2br($iolMasterNotesOD); ?></td>
															<?php
														}
														?>
													</tr>
													<tr height="2">
														<td colspan="4"></td>
													</tr>
											  </table>
											</td>
										</tr>									
								  </table>
								
						</td>
						<td style="width: 12%;">&nbsp;</td>
						<td style="width: 50%; padding-left:5px;" align="left" valign="top">
							<table border="0" cellpadding="0" cellspacing="1" bordercolor="#FFFFFF" style="width:100%;">
									<tr height="20">
										<td align="left" class="text_10b" ><span style="color:#009900;font-size:<?php if($printAscan == 'printFromOutOfAScan') echo "10px"; else echo "14px"; ?>;font-weight:bold;">OS</span></td>
										<td align="left" class="text_10b">&nbsp;</td>
										<td align="left" class="text_10b">Performed By:</td>
										<td align="left" class="text_10b"><?php echo $performedByOS; ?></td>
										<td align="left" class="text_10b">Date:</td>
										<td align="left" class="text_9"><?php echo $dateOS; ?></td>
									</tr>
								</table>
								
									<table border="0" cellpadding="0" cellspacing="0" style="width: 100%;">
										<tr height="10">
											<td colspan="6"></td>
										</tr>
										<tr>
											<td width="5"></td>
											<td align="left" class="text_10b"><?php echo $autoSelectOS; ?></td>
											<td width="5" align="left"></td>
											<td align="left" class="text_10b"><?php echo $iolMasterSelectOS; ?></td>
											<td width="5" align="left"></td>
											<td align="left" class="text_10b"><?php echo $topographerSelectOS; ?></td>
										</tr>
										<tr height="5">
											<td colspan="6"></td>
										</tr>
										<!-- K1 -->
										<tr height="20">
											<td width="5" class="text_10b">K1</td>
											<td align="left">
												<table border="0" cellpadding="0" cellspacing="0">
													<?php
													if($k1Auto1OS){
														?>
														<tr>
															<td align="left" class="text_9" width="35"><?php if(($k1Auto1OS!='') && ($k1Auto1OS!=0)) echo number_format($k1Auto1OS, 2); else echo "&nbsp;"; ?></td>
															<td align="center" class="text_9" width="10">X</td>
															<td align="left" class="text_9" width="35" style="padding-left:1px;"><?php if(($k1Auto2OS!='') && ($k1Auto2OS!=0)) echo $k1Auto2OS; else echo "&nbsp;"; ?>&#176;</td>
														</tr>
														<?php
													}
													?>
												</table>
											</td>
											<td width="5"></td>
											<td align="left">
												<table border="0" cellpadding="0" cellspacing="0">
													<?php
													if($k1IolMaster1OS){
														?>
														<tr>
															<td align="left" class="text_9" width="35"><?php if(($k1IolMaster1OS!='') && ($k1IolMaster1OS!=0)) echo number_format($k1IolMaster1OS, 2); else echo "&nbsp;"; ?></td>
															<td align="center" class="text_9" width="10">X</td>
															<td align="left" class="text_9" width="35" style="padding-left:1px;"><?php if(($k1IolMaster2OS!='') && ($k1IolMaster2OS!=0)) echo $k1IolMaster2OS; else echo "&nbsp;"; ?>&#176;</td>
														</tr>
														<?php
													}
													?>
												</table>
											</td>
											<td width="5"></td>
											<td align="left">
												<table border="0" cellpadding="0" cellspacing="0">
													<?php
													if($k1Topographer1OS){
														?>
														<tr>
															<td align="left" class="text_9" width="35"><?php if(($k1Topographer1OS!='') && ($k1Topographer1OS!=0)) echo number_format($k1Topographer1OS, 2); else echo "&nbsp;"; ?></td>
															<td align="center" class="text_9" width="10">X</td>
															<td align="left" class="text_9" width="35" style="padding-left:1px;"><?php if(($k1Topographer2OS!='') && ($k1Topographer2OS!=0)) echo $k1Topographer2OS; else echo "&nbsp;"; ?>&#176;</td>
														</tr>
														<?php
													}
													?>
												</table>
											</td>
										</tr>
										<!-- K1 -->
	
										<!-- K2 -->
										<tr height="20">
											<td width="5" class="text_10b">K2</td>
											<td align="left">
												<table border="0" cellpadding="0" cellspacing="0">
													<?php
													if($k1Auto1OS){
														?>
														<tr>
															<td align="left" class="text_9" width="35"><?php if(($vis_ak_os_slash!='') && ($vis_ak_os_slash!=0)) echo number_format($vis_ak_os_slash, 2); else echo "&nbsp;"; ?></td>
															<td align="center" class="text_9" width="10">X</td>
															<td align="left" class="text_9" width="35" style="padding-left:1px;"><?php if(($k2Auto2OS!='') && ($k2Auto2OS!=0)) echo $k2Auto2OS; else echo "&nbsp;"; ?>&#176;</td>
														</tr>
														<?php
													}
													?>
												</table>
											</td>
											<td width="5"></td>
											<td align="left">
												<table border="0" cellpadding="0" cellspacing="0">
													<?php
													if($k1IolMaster1OS){
														?>
														<tr>
															<td align="left" class="text_9" width="35"><?php if(($k2IolMaster1OS!='') && ($k2IolMaster1OS!=0)) echo number_format($k2IolMaster1OS, 2); else echo "&nbsp;"; ?></td>
															<td align="center" class="text_9" width="10">X</td>
															<td align="left" class="text_9" width="35" style="padding-left:1px;"><?php if(($k2IolMaster2OS!='') && ($k2IolMaster2OS!=0)) echo $k2IolMaster2OS; else echo "&nbsp;"; ?>&#176;</td>
														</tr>
														<?php
													}
													?>
												</table>
											</td>
											<td width="5"></td>
											<td align="left">
												<table border="0" cellpadding="0" cellspacing="0">
													<?php 
													if($k1Topographer1OS){
														?>
														<tr>
															<td align="left" class="text_9" width="35"><?php if(($k2Topographer1OS!='') && ($k2Topographer1OS!=0)) echo number_format($k2Topographer1OS, 2); else echo "&nbsp;"; ?></td>
															<td align="center" class="text_9" width="10">X</td>
															<td align="left" class="text_9" width="35" style="padding-left:1px;"><?php if(($k2Topographer2OS!='') && ($k2Topographer2OS!=0)) echo $k2Topographer2OS; else echo "&nbsp;"; ?>&#176;</td>
														</tr>
														<?php
													}
													?>
												</table>
											</td>
										</tr>
										<!-- K2 -->
										
										<!-- CYL -->
										<tr height="20">
											<td width="5" class="text_10b">CYL</td>
											<td align="left">
												<table border="0" cellpadding="0" cellspacing="0">
													<?php
													if($k1Auto1OS){
														?>
														<tr>
															<td align="left" class="text_9" width="35"><?php if(($cylAuto1OS!='') && ($cylAuto1OS!=0)) echo number_format($cylAuto1OS, 2); else echo "&nbsp;"; ?></td>
															<td align="center" class="text_9">@</td>
															<td align="left" class="text_9" width="35" style="padding-left:1px;"><?php if(($cylAuto2OS!='') && ($cylAuto2OS!=0)) echo $cylAuto2OS; else echo "&nbsp;"; ?>&#176;</td>
														</tr>
														<?php
													}
													?>
												</table>
											</td>
											<td width="5"></td>
											<td align="left">
												<table border="0" cellpadding="0" cellspacing="0">
													<?php
													if($k1IolMaster1OS){
														?>
														<tr>
															<td align="left" class="text_9" width="35"><?php if(($cylIolMaster1OS!='') && ($cylIolMaster1OS!=0)) echo number_format($cylIolMaster1OS, 2); else echo "&nbsp;"; ?></td>
															<td align="center" class="text_9">@</td>
															<td align="left" class="text_9" width="35" style="padding-left:1px;"><?php if(($cylIolMaster2OS!='') && ($cylIolMaster2OS!=0)) echo $cylIolMaster2OS; else echo "&nbsp;"; ?>&#176;</td>
														</tr>
														<?php
													}
													?>
												</table>
											</td>
											<td width="5"></td>
											<td align="left">
												<table border="0" cellpadding="0" cellspacing="0">
													<?php
													if($k1Topographer1OS){
														?>
														<tr>
															<td align="left" class="text_9" width="35"><?php if(($cylTopographer1OS!='') && ($cylTopographer1OS!=0)) echo number_format($cylTopographer1OS, 2); else echo "&nbsp;"; ?></td>
															<td align="center" class="text_9">@</td>
															<td align="left" class="text_9" width="35" style="padding-left:1px;"><?php if(($cylTopographer2OS!='') && ($cylTopographer2OS!=0)) echo $cylTopographer2OS; else echo "&nbsp;"; ?>&#176;</td>
														</tr>
														<?php
													}
													?>
												</table>
											</td>
										</tr>
										<!-- CYL -->
										
										<!-- AVE -->
										<tr height="20">
											<td width="5" class="text_10b">AVE</td>
											<td align="left">
												<table border="0" cellpadding="0" cellspacing="0">
													<?php
													if($k1Auto1OS){
														?>
														<tr>
															<td align="center" class="text_9"><?php if(($aveAutoOS!='') && ($aveAutoOS!=0)) echo number_format($aveAutoOS, 2); else echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"; ?></td>
														</tr>
														<?php
													}
													?>
												</table>
											</td>
											<td width="5"></td>
											<td align="left">
												<table border="0" cellpadding="0" cellspacing="0">
													<?php
													if($k1IolMaster1OS){
														?>
														<tr>
															<td align="center" class="text_9"><?php if(($aveIolMasterOS!='') && ($aveIolMasterOS!=0)) echo number_format($aveIolMasterOS, 2); else echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"; ?></td>
														</tr>
														<?php
													}
													?>
												</table>
											</td>
											<td width="5"></td>
											<td align="left">
												<table border="0" cellpadding="0" cellspacing="0">
													<?php
													if($k1Topographer1OS){
														?>
														<tr>
															<td align="center" class="text_9"><?php if(($aveTopographerOS!='') && ($aveTopographerOS!=0)) echo number_format($aveTopographerOS, 2); else echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"; ?></td>
														</tr>
														<?php
													}
													?>
												</table>
											</td>
										</tr>
										<!-- AVE -->
										<tr height="20">
											<td colspan="6" align="left">
												<table border="0" cellpadding="0" cellspacing="1" width="100%">
													<tr height="10">
														<td colspan="4"></td>
													</tr>
													<tr height="">
														<td align="left" class="text_10b">Axial</td>
														<td align="left" class="text_10b" width="86" style="padding-left:10px;">Contact</td>
														<td align="left" class="text_10b" width="86" style="padding-left:2px;">Immersion</td>
														<td align="left" class="text_10b" width="86" style="padding-left:2px;">IOL Master</td>
													</tr>
													<tr height="">
														<td align="left" class="text_10b">Length</td>
														<td align="left" class="text_9" style="padding-left:10px;"><?php echo $contactLengthOS; ?></td>
														<td align="left" class="text_9" style="padding-left:2px;"><?php echo $immersionLengthOS; ?></td>
														<td align="left" class="text_9" style="padding-left:2px;"><?php echo $iolMasterLengthOS; ?></td>
													</tr>
													<tr height="20">
														<?php
														if(($contactNotesOS) || ($immersionNotesOS) || ($iolMasterNotesOS)){
															?>
															<td align="left" class="text_10b">Notes</td>
															<td align="left" class="text_9" style="padding-left:10px;"><?php echo nl2br($contactNotesOS); ?></td>
															<td align="left" class="text_9" style="padding-left:2px;"><?php echo nl2br($immersionNotesOS); ?></td>
															<td align="left" class="text_9" style="padding-left:2px;"><?php echo nl2br($iolMasterNotesOS); ?></td>
															<?php
														}
														?>
													</tr>
													<tr height="2">
														<td colspan="4"></td>
													</tr>
											  </table>
											</td>
										</tr>									
								  </table>
								
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr height="10">
			<td align="left"></td>
		</tr>
		<tr>
			<td align="left" valign="top" style="width: 50%;">
				<table border="0" cellpadding="0" cellspacing="5" bordercolor="#FFFFFF" style="width: 100%;">
					<tr>
						<td align="center" valign="top">
							<table border="0" cellpadding="0" cellspacing="1" bordercolor="#FFFFFF" style="width:100%;">
									<tr height="20">
										<td align="left" class="text_10b" ><span class="text_10b" style="color:#0000FF;font-size:<?php if($printAscan == 'printFromOutOfAScan') echo "10px"; else echo "14px"; ?>;">OD</span></td>
										<td align="left" class="text_10b">&nbsp;</td>
										<td align="left" class="text_10b">Performed By:</td>
										<td align="left" class="text_10b"><?php echo $provider_idOD; ?></td>
										
									</tr>
						  </table>
								
								<table border="0" cellpadding="0" cellspacing="1" bordercolor="#FFFFFF" style="padding-left:5px;">
									<tr height="20">
										<td align="left" class="text_10b" width="90">IOL</td>
										<td align="left" style="padding-left:1px;" class="text_9"><?php if($powerIolOD!='') echo $powerIolOD; else echo "&nbsp;"; ?></td>
										<td align="left" style="padding-left:1px;" class="text_9"><?php if($holladayOD!='') echo $holladayOD; else echo "&nbsp;"; ?></td>
										<td align="left" style="padding-left:1px;" class="text_9"><?php if($srk_tOD!='') echo $srk_tOD; else echo "&nbsp;"; ?></td>
										<td align="left" style="padding-left:1px;" class="text_9"><?php if($hofferOD!='') echo $hofferOD; else echo "&nbsp;"; ?></td>
									</tr>
									<tr height="20">
										<td align="left" class="text_9" width="90"><?php if($iol1OD!='') echo $iol1OD; else echo "&nbsp;"; ?></td>
										<td align="left" style="padding-left:1px;" class="text_9"><?php if($iol1PowerOD!='') echo $iol1PowerOD; else echo "&nbsp;"; ?></td>
										<td align="left" style="padding-left:1px;" class="text_9"><?php if($iol1HolladayOD!='') echo $iol1HolladayOD; else echo "&nbsp;"; ?></td>
										<td align="left" style="padding-left:1px;" class="text_9"><?php if($iol1srk_tOD!='') echo $iol1srk_tOD; else echo "&nbsp;"; ?></td>
										<td align="left" style="padding-left:1px;" class="text_9"><?php if($iol1HofferOD!='') echo $iol1HofferOD; else echo "&nbsp;"; ?></td>
									</tr>
									<tr height="20">
										<td align="left" class="text_9" width="90"><?php if($iol2OD!='') echo $iol2OD; else echo "&nbsp;"; ?></td>
										<td align="left" style="padding-left:1px;" class="text_9"><?php if($iol2PowerOD!='') echo $iol2PowerOD; else echo "&nbsp;"; ?></td>
										<td align="left" style="padding-left:1px;" class="text_9"><?php if($iol2HolladayOD!='') echo $iol2HolladayOD; else echo "&nbsp;"; ?></td>
										<td align="left" style="padding-left:1px;" class="text_9"><?php if($iol2srk_tOD!='') echo $iol2srk_tOD; else echo "&nbsp;"; ?></td>
										<td align="left" style="padding-left:1px;" class="text_9"><?php if($iol2HofferOD!='') echo $iol2HofferOD; else echo "&nbsp;"; ?></td>
									</tr>
									<tr height="20">
										<td align="left" class="text_9" width="90"><?php if($iol3OD!='') echo $iol3OD; else echo "&nbsp;"; ?></td>
										<td align="left" style="padding-left:1px;" class="text_9"><?php if($iol3PowerOD!='') echo $iol3PowerOD; else echo "&nbsp;"; ?></td>
										<td align="left" style="padding-left:1px;" class="text_9"><?php if($iol3HolladayOD!='') echo $iol3HolladayOD; else echo "&nbsp;"; ?></td>
										<td align="left" style="padding-left:1px;" class="text_9"><?php if($iol3srk_tOD!='') echo $iol3srk_tOD; else echo "&nbsp;"; ?></td>
										<td align="left" style="padding-left:1px;" class="text_9"><?php if($iol3HofferOD!='') echo $iol3HofferOD; else echo "&nbsp;"; ?></td>
									</tr>
									<tr height="20">
										<td align="left" class="text_9" width="90"><?php if($iol4OD!='') echo $iol4OD; else echo "&nbsp;"; ?></td>
										<td align="left" style="padding-left:1px;" class="text_9"><?php if($iol4PowerOD!='') echo $iol4PowerOD; else echo "&nbsp;"; ?></td>
										<td align="left" style="padding-left:1px;" class="text_9"><?php if($iol4HolladayOD!='') echo $iol4HolladayOD; else echo "&nbsp;"; ?></td>
										<td align="left" style="padding-left:1px;" class="text_9"><?php if($iol4srk_tOD!='') echo $iol4srk_tOD; else echo "&nbsp;"; ?></td>
										<td align="left" style="padding-left:1px;" class="text_9"><?php if($iol4HofferOD!='') echo $iol4HofferOD; else echo "&nbsp;"; ?></td>
									</tr>
									<tr height="20">
										<td align="left" class="text_10b" width="90">Cell Count</td>
										<td align="left" class="text_9"><?php if($cellCountOD!='') echo $cellCountOD; else echo "&nbsp;"; ?></td>
										<?php
										if($notesOD){
											?>
											<td colspan="3" rowspan="6" align="left" valign="top" class="text_9"><b>NOTES:&nbsp;</b><?php echo nl2br($notesOD); ?></td>
											<?php
										}
										?>
									</tr>
									<tr height="20">
										<td align="left" class="text_10b" width="90">Pachymetry</td>
										<td align="left" class="text_9">
											<table border="0" cellpadding="0" cellspacing="0">
												<tr>
													<td width="30" align="left" class="text_9"><?php if($pachymetryValOD!='') echo $pachymetryValOD; else echo "&nbsp;"; ?></td>
													<td width="10" align="center" class="text_9">/</td>
													<td align="center" class="text_9"><?php if($pachymetryCorrecOD!='') echo $pachymetryCorrecOD; else echo "&nbsp;"; ?></td>
												</tr>
											</table>
										</td>
									</tr>
									<tr height="20">
										<td align="left" class="text_10b" width="90">Corneal Diam</td>
										<td align="left" class="text_9"><?php if($cornealDiamOD!='') echo $cornealDiamOD; else echo "&nbsp;"; ?></td>
									</tr>
									<tr height="20">
										<td align="left" class="text_10b" width="90">Dominant Eye</td>
										<td align="left" class="text_9"><?php if($dominantEyeOD!='') echo $dominantEyeOD; else echo "&nbsp;"; ?></td>
									</tr>

									<tr height="20">
										<td align="left" class="text_10b" width="90">Pupil Size</td>
										<td align="left" class="text_9">
											<table border="0" cellpadding="0" cellspacing="0">
												<tr>
													<td align="left" width="20" class="text_9"><?php if($pupilSize1OD!='') echo $pupilSize1OD; else echo "&nbsp;"; ?></td>
													<td width="20" class="text_9" align="center">/</td>
													<td align="center" class="text_9"><?php if($pupilSize2OD!='') echo $pupilSize2OD; else echo "&nbsp;"; ?></td>
												</tr>
											</table>
										</td>
									</tr>
									<tr height="5">
										<td colspan="2" align="right" class="text_9">
											<table border="0" cellpadding="0" cellspacing="0">
												<tr>
													<td align="left" class="text_10b" >Un-Dilated</td>
													<td width="3" >/</td>
													<td align="left" class="text_10b" >Dilated</td>
												</tr>
										  </table>
										</td>
									</tr>
									<tr height="20">
										<td colspan="5" valign="top" align="left" class="text_9">&nbsp;</td>
									</tr>
									<tr height="20">
										<td colspan="5" valign="top" align="left" ><b>CC:</b>&nbsp;&nbsp;Scheduled for Intraocular lens implant<br/> A/Scan reviewed and IOL Selected.</td>
									</tr>
									<tr height="20">
										<td colspan="5" valign="top" align="left" >&nbsp;</td>
									</tr>
									<tr>
										<td colspan="5" align="left">
											<table border="0" cellpadding="0" cellspacing="0">
												<tr>
													<td align="left" class="text_10b" >Assessment:</td>
													<td ><?php if($cataractOD==1) echo "Cataract"; ?></td>
													<td > <?php if($astigmatismOD==1) echo "Astigmatism"; ?> </td>
													<td > <?php if($myopiaOD==1) echo "Myopia"; ?> </td>
												</tr>
											</table>
										</td>
									</tr>
									<tr height="5">
										<td colspan="5" valign="top" align="left" >&nbsp;</td>
									</tr>
									<tr>
										<td colspan="5" valign="top" align="left" >
											<table border="0" cellpadding="0" cellspacing="0">
												<tr>
													<td  align="left" class="text_10b">Plan:</td>
													<td  style="padding-left:5px;"><?php if($selecedIOLsOD) echo $selecedIOLsOD; else echo "&nbsp;"; ?></td>
												</tr>
											</table>
										</td>
									</tr>
									<tr height="5">
										<td colspan="5" valign="top" align="left" >&nbsp;</td>
									</tr>
									<tr height="15">
										<td colspan="5" align="left">
											<table border="0" cellpadding="0" cellspacing="0">
												<td align="left"  valign="top" class="text_10b">Notes:</td>
												<td align="left"  style="padding-left:2px;"><?php if($notesOD!='') echo nl2br($notesOD); else echo "Notes..."; ?></td>
										  </table>
										</td>
									</tr>
									<tr height="20">
										<td colspan="5">
											<table border="0" cellpadding="0" cellspacing="0">
												<tr>
													<td > <?php if($lriOD==1){ echo "LRI"; } ?> </td>
													<td > <?php if($dlOD==1){ echo "DL"; } ?> </td>
													<td > <?php if($synechiolysisOD==1){ echo "Synechiolysis"; } ?> </td>
												</tr>
												<tr>
													<td > <?php if($irishooksOD==1){ echo "IRIS Hooks"; } ?> </td>
													<td ><?php if($trypanblueOD==1){ echo "Trypan Blue"; } ?></td>
													<td > <?php if($flomaxOD==1){ echo "Pt. On Flomax"; } ?></td>
												</tr>
											</table>
										</td>
									</tr>
									<tr height="15" <?php if($lriOD!=1){ ?> style="display:none;" <?php } ?>>
										<td colspan="5" align="left">
											<table border="0" cellpadding="0" cellspacing="0">
												<tr>
													<td width="35"  align="left" class="text_10b">Cuts:</td>
													<td width="25" align="left" ><?php echo $cutsOD; ?></td>
													<td width="10"></td>
													<td width="35" align="right" class="text_10b" >Length:</td>
													<td width="35" align="center" ><?php echo $lengthOD."  ".$lengthTypeOD; ?></td>
													<td width="15"></td>
													<td width="35" align="center" class="text_10b" >Axis:</td>
													<td width="35" align="center" ><?php echo $axisOD; ?></td>
												</tr>
										  </table>
										</td>
									</tr>
									<tr height="20" <?php if($cutsOD!=1){ ?> style="display:none;" <?php } ?>>
									  <td colspan="5" align="left">
											<table width="" border="0" cellpadding="0" cellspacing="0">
												<tr>
													<td ><?php if($superiorOD==1) echo "superior"; ?> </td>
													<td > <?php if($inferiorOD==1) echo "inferior"; ?> </td>
													<td > <?php if($nasalOD==1) echo "nasal"; ?></td>
													<td > <?php if($temporalOD==1) echo "temporal"; ?> </td>
													<td > <?php if($STOD==1) echo "ST"; ?> </td>
													<td > <?php if($SNOD==1) echo "SN"; ?> </td>
												</tr>
												<tr>												
													<td > <?php if($ITOD==1) echo "IT"; ?> </td>
													<td > <?php if($INOD==1) echo "IN"; ?> </td>
												</tr>
										  </table>
									  </td>
									</tr>
						  </table>
								
						</td>
						<td style="width: 12%;">&nbsp;</td>
						<td align="center" valign="top" class="text_9">
							<table border="0" cellpadding="0" cellspacing="1" bordercolor="#FFFFFF" style="width:100%;">
									<tr height="20">
										<td align="left" class="text_10b" ><span style="color:#009900;font-size:<?php if($printAscan == 'printFromOutOfAScan') echo "10px"; else echo "14px"; ?>;"><b>OS</b></span></td>
										<td align="left" class="text_10b">&nbsp;</td>
										<td align="left" class="text_10b">Performed By:</td>
										<td align="left" class="text_10b"><?php echo $performedIolOS; ?></td>
										
									</tr>
						  </table>
								
								<table border="0" cellpadding="0" cellspacing="1" bordercolor="#FFFFFF" style="padding-left:5px;">
									<tr height="20">
										<td align="left" class="text_10b" width="104">IOL</td>
										<td width="75" align="left" class="text_9" style="padding-left:1px;"><?php if($powerIolOS!='') echo $powerIolOS; else echo "&nbsp;"; ?></td>
										<td width="36" align="left" class="text_9" style="padding-left:1px;"><?php if($holladayOS!='') echo $holladayOS; else echo "&nbsp;"; ?></td>
										<td width="35" align="left" class="text_9" style="padding-left:1px;"><?php if($srk_tOS!='') echo $srk_tOS; else echo "&nbsp;"; ?></td>
										<td width="52" align="left" class="text_9" style="padding-left:1px;"><?php if($hofferOS!='') echo $hofferOS; else echo "&nbsp;"; ?></td>
									</tr>
									<tr height="20">
										<td align="left" class="text_9" width="104"><?php if($iol1OS!='') echo $iol1OS; else echo "&nbsp;"; ?></td>
										<td align="left" style="padding-left:1px;" class="text_9"><?php if($iol1PowerOS!='') echo $iol1PowerOS; else echo "&nbsp;"; ?></td>
										<td align="left" style="padding-left:1px;" class="text_9"><?php if($iol1HolladayOS!='') echo $iol1HolladayOS; else echo "&nbsp;"; ?></td>
										<td align="left" style="padding-left:1px;" class="text_9"><?php if($iol1srk_tOS!='') echo $iol1srk_tOS; else echo "&nbsp;"; ?></td>
										<td align="left" style="padding-left:1px;" class="text_9"><?php if($iol1HofferOS!='') echo $iol1HofferOS; else echo "&nbsp;"; ?></td>
									</tr>
									<tr height="20">
										<td align="left" class="text_9" width="104"><?php if($iol2OS!='') echo $iol2OS; else echo "&nbsp;"; ?></td>
										<td align="left" style="padding-left:1px;" class="text_9"><?php if($iol2PowerOS!='') echo $iol2PowerOS; else echo "&nbsp;"; ?></td>
										<td align="left" style="padding-left:1px;" class="text_9"><?php if($iol2HolladayOS!='') echo $iol2HolladayOS; else echo "&nbsp;"; ?></td>
										<td align="left" style="padding-left:1px;" class="text_9"><?php if($iol2srk_tOS!='') echo $iol2srk_tOS; else echo "&nbsp;"; ?></td>
										<td align="left" style="padding-left:1px;" class="text_9"><?php if($iol2HofferOS!='') echo $iol2HofferOS; else echo "&nbsp;"; ?></td>
									</tr>
									<tr height="20">
										<td align="left" class="text_9" width="104"><?php if($iol3OS!='') echo $iol3OS; else echo "&nbsp;"; ?></td>
										<td align="left" style="padding-left:1px;" class="text_9"><?php if($iol3PowerOS!='') echo $iol3PowerOS; else echo "&nbsp;"; ?></td>
										<td align="left" style="padding-left:1px;" class="text_9"><?php if($iol3HolladayOS!='') echo $iol3HolladayOS; else echo "&nbsp;"; ?></td>
										<td align="left" style="padding-left:1px;" class="text_9"><?php if($iol3srk_tOS!='') echo $iol3srk_tOS; else echo "&nbsp;"; ?></td>
										<td align="left" style="padding-left:1px;" class="text_9"><?php if($iol3HofferOS!='') echo $iol3HofferOS; else echo "&nbsp;"; ?></td>
									</tr>
									<tr height="20">
										<td align="left" class="text_9" width="104"><?php if($iol4OS!='') echo $iol4OS; else echo "&nbsp;"; ?></td>
										<td align="left" style="padding-left:1px;" class="text_9"><?php if($iol4PowerOS!='') echo $iol4PowerOS; else echo "&nbsp;"; ?></td>
										<td align="left" style="padding-left:1px;" class="text_9"><?php if($iol4HolladayOS!='') echo $iol4HolladayOS; else echo "&nbsp;"; ?></td>
										<td align="left" style="padding-left:1px;" class="text_9"><?php if($iol4srk_tOS!='') echo $iol4srk_tOS; else echo "&nbsp;"; ?></td>
										<td align="left" style="padding-left:1px;" class="text_9"><?php if($iol4HofferOS!='') echo $iol4HofferOS; else echo "&nbsp;"; ?></td>
									</tr>
									<tr height="20">
										<td align="left" class="text_10b" width="104">Cell Count</td>
										<td align="left" class="text_9"><?php if($cellCountOS!='') echo $cellCountOS; else echo "&nbsp;"; ?></td>
										<?php
										if($notesOS){
											?>
											<td colspan="3" width="75" rowspan="6" align="left" valign="top" class="text_9"><b>NOTES:&nbsp;</b><?php echo nl2br($notesOS); ?></td>
											<?php
										}
										?>
									</tr>
									<tr height="20">
										<td align="left" class="text_10b" width="104">Pachymetry</td>
										<td align="left" class="text_9">
											<table border="0" cellpadding="0" cellspacing="0">
												<tr>
													<td width="30" align="left" class="text_9"><?php if($pachymetryValOS!='') echo $pachymetryValOS; else echo "&nbsp;"; ?></td>
													<td width="10" class="text_9" align="center">/</td>
													<td align="left" class="text_9"><?php if($pachymetryCorrecOS!='') echo $pachymetryCorrecOS; else echo "&nbsp;"; ?></td>
												</tr>
											</table>
										</td>
									</tr>
									<tr height="20">
										<td align="left" class="text_10b" width="104">Corneal Diam</td>
										<td align="left" class="text_9"><?php if($cornealDiamOS!='') echo $cornealDiamOS; else echo "&nbsp;"; ?></td>
									</tr>
									<tr height="20">
										<td align="left" class="text_10b" width="104">Dominant Eye</td>
										<td align="left" class="text_9"><?php if($dominantEyeOS!='') echo $dominantEyeOS; else echo "&nbsp;"; ?></td>
									</tr>
									<tr height="20">
										<td align="left" class="text_10b" width="104">Pupil Size</td>
										<td align="left" class="text_9">
											<table border="0" cellpadding="0" cellspacing="0">
												<tr>
													<td width="30" align="left" class="text_9"><?php if($pupilSize1OS!='') echo $pupilSize1OS; else echo "&nbsp;"; ?></td>
													<td width="10" class="text_9" align="center">/</td>
													<td width="" align="left" class="text_9"><?php if($pupilSize2OS!='') echo $pupilSize2OS; else echo "&nbsp;"; ?></td>
												</tr>
											</table>
										</td>
									</tr>
									<tr height="5">
										<td colspan="2" align="right" class="text_9">
											<table border="0" cellpadding="0" cellspacing="0">
												<tr>
													<td align="left" class="text_10b" >Un-Dilated</td>
													<td width="3" >/</td>
													<td align="left" class="text_10b" >Dilated</td>
												</tr>
										  </table>
										</td>
									</tr>
									<tr height="20">
										<td colspan="5" valign="top" align="left" class="text_9">&nbsp;</td>
									</tr>
									<tr height="20">
										<td colspan="5" valign="top" align="left" ><b>CC:</b>&nbsp;&nbsp;Scheduled for Intraocular lens implant<br/> A/Scan reviewed and IOL Selected.</td>
									</tr>
									<tr height="20">
										<td colspan="5" valign="top" align="left" >&nbsp;</td>
									</tr>
									<tr>
										<td colspan="5" align="left">
											<table border="0" cellpadding="0" cellspacing="0">
												<tr>
													<td align="left" class="text_10b" >Assessment:</td>
													<td > <?php if($cataractOS==1) echo "Cataract"; ?> </td>
													<td ><?php if($astigmatismOS==1) echo "Astigmatism"; ?> </td>
													<td > <?php if($myopiaOS==1) echo "Myopia"; ?> </td>
												</tr>
										  </table>
										</td>
									</tr>
									<tr height="5">
										<td colspan="5" valign="top" align="left" >&nbsp;</td>
									</tr>
									<tr>
										<td colspan="5" valign="top" align="left" >
											<table border="0" cellpadding="0" cellspacing="0">
												<tr>
													<td  align="left" class="text_10b">Plan:</td>
													<td  style="padding-left:5px;"><?php if($selecedIOLsOS) echo $selecedIOLsOS; else echo "&nbsp;"; ?></td>
												</tr>
											</table>
										</td>
									</tr>
									<tr height="5">
										<td colspan="5" valign="top" align="left" >&nbsp;</td>
									</tr>
									<?php 
										//if(($notesAssesmentPlansOS!='Notes...') && ($notesAssesmentPlansOS!='')){
										?>
										<tr height="15">
											<td colspan="5" align="left">
												<table border="0" cellpadding="0" cellspacing="0">
													<td align="left"  valign="top"><b>Notes:</b></td>
													<td align="left"  style="padding-left:2px;"><?php if($notesAssesmentPlansOS!='') echo nl2br($notesAssesmentPlansOS); else echo "Notes..."; ?></td>
												</table>
											</td>
										</tr>
										<?php
									//}
									?>
									<tr height="20">
										<td colspan="5">
											<table border="0" cellpadding="0" cellspacing="0">
												<tr>
													<td ><?php if($lriOS==1){ echo "LRI"; } ?> </td>
													<td > <?php if($dlOS==1){ echo "DL"; } ?> </td>
													<td > <?php if($synechiolysisOS==1){ echo "Synechiolysis"; } ?> </td>
												</tr>
												<tr>
													<td > <?php if($irishooksOS==1){ echo "IRIS Hooks"; } ?> </td>
													<td > <?php if($trypanblueOS==1){ echo "Trypan Blue"; } ?> </td>
													<td ><?php if($flomaxOS==1){ echo "Pt. On Flomax"; } ?> </td>
												</tr>
											</table>
										</td>
									</tr>
									<tr height="15" <?php if($lriOS!=1){ ?> style="display:none;" <?php } ?>>
										<td colspan="5" align="left">
											<table border="0" cellpadding="0" cellspacing="0">
												<tr>
													<td width="35"  align="left" class="text_10b">Cuts:</td>
													<td width="25" align="left" class="text_9" ><?php echo $cutsOS; ?></td>
													<td width="10"></td>
													<td width="35" align="right" class="text_10b" >Length:</td>
													<td width="35" align="center" class="text_9" ><?php echo $lengthOS."  ".$lengthTypeOS; ?></td>
													<td width="15"></td>
													<td width="35" align="center" class="text_10b" >Axis:</td>
													<td width="35" align="center" class="text_9" ><?php echo $axisOS; ?></td>
												</tr>
										  </table>
										</td>
									</tr>
									<tr height="20" <?php if($cutsOS!=1){ ?> style="display:none;" <?php } ?>>
									  <td colspan="5" align="left">
											<table width="" border="0" cellpadding="0" cellspacing="0">
												<tr>
													<td ><?php if($superiorOS==1) echo "superior"; ?> </td>
													<td ><?php if($inferiorOS==1) echo "inferior"; ?> </td>
													<td ><?php if($nasalOS==1) echo "nasal"; ?></td>
													<td ><?php if($temporalOS==1) echo "temporal"; ?></td>
													<td ><?php if($STOS==1) echo "ST"; ?></td>
													<td ><?php if($SNOS==1) echo "SN"; ?> </td>
												</tr>
												<tr>												
													<td ><?php if($ITOS==1) echo "IT"; ?></td>
													<td ><?php if($INOS==1) echo "IN"; ?> </td>
												</tr>
										  </table>
									  </td>
									</tr>
						  </table>
								
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<?php
		/*
		<tr>
			<td align="left" >
				<table border="0" cellpadding="0" cellspacing="0" bordercolor="#FFFFFF" width="100%">
					<tr>
						<td width="60" align="left" class="text_10b" >Signature:</td>
						<td width="275" align="left" >
							
							<?php

								if(isAppletModified($signature)){
									$table = 'surgical_tbl';
									$idName = 'surgical_id';
									$docSign = 'signature';
									$signImage = '../../images/white.jpg';
									$alt = 'Physician Sign'; 
												 
									 if(getAppletImage($surgical_id,$table,$idName,$docSign,$signImage,$alt,"1")){
										@copy("html2pdfprint/".$gdFilename,"../common/new_html2pdf/".$gdFilename);
										echo "<img src='".$gdFilename."' height='45' width='225'/>";
										$ChartNoteImagesString[]=$gdFilename;					 
									} 
									
								}
							?>	
						</td>
						<td width="75" align="left" >&nbsp;</td>
						<td width="60" align="left" class="text_10b" >Signature:</td>
						<td width="250" align="left" >
							
							<?php
								if(isAppletModified($signature)){

									$table = 'surgical_tbl';
									$idName = 'surgical_id';
									$docSign = 'signatureOS';
									$signImage = '../../images/white.jpg';
									$alt = 'Physician Sign'; 
									if(getAppletImage($surgical_id,$table,$idName,$docSign,$signImage,$alt,"1")){
										@copy("html2pdfprint/".$gdFilename,"../common/new_html2pdf/".$gdFilename);										
										echo "<img src='".$gdFilename."' height='45' width='225'/>";
										$ChartNoteImagesString[]=$gdFilename;					 
									} 
									
								}
							?>	
						</td>
					</tr>
			  </table>
			</td>
		</tr>
		*/
		?>
	</table>

<?php
}

//IOL Master
function print_iol_master($patient_id,$form_id,$req){

if(is_array($req) && count($req)>0){
	$printId = $req[0];
	$getSurgicalRecordStr = "SELECT * FROM iol_master_tbl WHERE iol_master_id = '$printId'";	
}else{
	$getSurgicalRecordStr = "SELECT * FROM iol_master_tbl WHERE patient_id = '$patient_id' AND form_id = '$form_id'";
}
//echo($getSurgicalRecordStr);
$getSurgicalRecordQry = imw_query($getSurgicalRecordStr);
$rowsCount = imw_num_rows($getSurgicalRecordQry);
if($rowsCount>0){
	$getSurgicalRecordRows = imw_fetch_array($getSurgicalRecordQry);
		$surgical_id = $getSurgicalRecordRows['iol_master_id'];
		$patient_id = $getSurgicalRecordRows['patient_id'];
		$form_id = $getSurgicalRecordRows['form_id'];
		$vis_mr_od_s = $getSurgicalRecordRows['mrSOD'];
		$vis_mr_od_c = $getSurgicalRecordRows['mrCOD'];
		$vis_mr_od_a = $getSurgicalRecordRows['mrAOD'];
		$visionOD = $getSurgicalRecordRows['visionOD'];
		$glareOD = $getSurgicalRecordRows['glareOD'];
		$performedByOD = $getSurgicalRecordRows['performedByOD'];
		
			$performedByODFname = print_getData('fname', 'users', 'id', $performedByOD);			
			$performedByODLname = print_getData('lname', 'users', 'id', $performedByOD);
			$performedByOD = $performedByODFname." ".$performedByODLname;
		
		$dateOD = $getSurgicalRecordRows['dateOD'];
			list($dateODYear, $dateODMonth, $dateODDay) = split("-", $dateOD);
			$dateOD = $dateODMonth."-".$dateODDay."-".$dateODYear;
			
		$autoSelectOD = $getSurgicalRecordRows['autoSelectOD'];
			$autoSelectOD = print_getKHeadingName($autoSelectOD);
		$iolMasterSelectOD = $getSurgicalRecordRows['iolMasterSelectOD'];
			$iolMasterSelectOD = print_getKHeadingName($iolMasterSelectOD);
		$topographerSelectOD = $getSurgicalRecordRows['topographerSelectOD'];
			$topographerSelectOD = print_getKHeadingName($topographerSelectOD);
		$vis_ak_od_k = $getSurgicalRecordRows['k1Auto1OD'];
		$vis_ak_od_x = $getSurgicalRecordRows['k1Auto2OD'];
		$k1IolMaster1OD = $getSurgicalRecordRows['k1IolMaster1OD'];
		$k1IolMaster2OD = $getSurgicalRecordRows['k1IolMaster2OD'];
		$k1Topographer1OD = $getSurgicalRecordRows['k1Topographer1OD'];
		$k1Topographer2OD = $getSurgicalRecordRows['k1Topographer2OD'];
		$vis_ak_od_slash = $getSurgicalRecordRows['k2Auto1OD'];
		$k2Auto2OD = $getSurgicalRecordRows['k2Auto2OD'];
		$k2IolMaster1OD = $getSurgicalRecordRows['k2IolMaster1OD'];
		$k2IolMaster2OD = $getSurgicalRecordRows['k2IolMaster2OD'];
		$k2Topographer1OD = $getSurgicalRecordRows['k2Topographer1OD'];
		$k2Topographer2OD = $getSurgicalRecordRows['k2Topographer2OD'];
		$cylAuto1OD = $getSurgicalRecordRows['cylAuto1OD'];
		$cylAuto2OD = $getSurgicalRecordRows['cylAuto2OD'];
		$cylIolMaster1OD = $getSurgicalRecordRows['cylIolMaster1OD'];
		$cylIolMaster2OD = $getSurgicalRecordRows['cylIolMaster2OD'];
		$cylTopographer1OD = $getSurgicalRecordRows['cylTopographer1OD'];
		$cylTopographer2OD = $getSurgicalRecordRows['cylTopographer2OD'];
		$aveOD1 = $getSurgicalRecordRows['aveAutoOD'];
		$aveIolMasterOD = $getSurgicalRecordRows['aveIolMasterOD'];
		$aveTopographerOD = $getSurgicalRecordRows['aveTopographerOD'];
		$contactLengthOD = $getSurgicalRecordRows['contactLengthOD'];
		$immersionLengthOD = $getSurgicalRecordRows['immersionLengthOD'];
		$iolMasterLengthOD = $getSurgicalRecordRows['iolMasterLengthOD'];
		$contactNotesOD = $getSurgicalRecordRows['contactNotesOD'];
		$immersionNotesOD = $getSurgicalRecordRows['immersionNotesOD'];
		$iolMasterNotesOD = $getSurgicalRecordRows['iolMasterNotesOD'];
		$provider_idOD = $getSurgicalRecordRows['performedByPhyOD'];
				$performedByODFname = print_getData('fname', 'users', 'id', $provider_idOD);
				$performedByODLname = print_getData('lname', 'users', 'id', $provider_idOD);
				$provider_idOD = $performedByODFname." ".$performedByODLname;
			
		$powerIolOD = $getSurgicalRecordRows['powerIolOD'];
			$powerIolOD = print_getFormulaHeadName($powerIolOD);
		$holladayOD = $getSurgicalRecordRows['holladayOD'];
			$holladayOD = print_getFormulaHeadName($holladayOD);
		$srk_tOD = $getSurgicalRecordRows['srk_tOD'];
			$srk_tOD = print_getFormulaHeadName($srk_tOD);
		$hofferOD = $getSurgicalRecordRows['hofferOD'];
			$hofferOD = print_getFormulaHeadName($hofferOD);
		$iol1OD = $getSurgicalRecordRows['iol1OD'];
			$iol1OD = print_getLenseName($iol1OD);
		$iol1PowerOD = $getSurgicalRecordRows['iol1PowerOD'];
		$iol1HolladayOD = $getSurgicalRecordRows['iol1HolladayOD'];
		$iol1srk_tOD = $getSurgicalRecordRows['iol1srk_tOD'];
		$iol1HofferOD = $getSurgicalRecordRows['iol1HofferOD'];
		$iol2OD = $getSurgicalRecordRows['iol2OD'];
			$iol2OD = print_getLenseName($iol2OD);
		$iol2PowerOD = $getSurgicalRecordRows['iol2PowerOD'];
		$iol2HolladayOD = $getSurgicalRecordRows['iol2HolladayOD'];
		$iol2srk_tOD = $getSurgicalRecordRows['iol2srk_tOD'];
		$iol2HofferOD = $getSurgicalRecordRows['iol2HofferOD'];
		$iol3OD = $getSurgicalRecordRows['iol3OD'];
			$iol3OD = print_getLenseName($iol3OD);
		$iol3PowerOD = $getSurgicalRecordRows['iol3PowerOD'];
		$iol3HolladayOD = $getSurgicalRecordRows['iol3HolladayOD'];
		$iol3srk_tOD = $getSurgicalRecordRows['iol3srk_tOD'];
		$iol3HofferOD = $getSurgicalRecordRows['iol3HofferOD'];
		$iol4OD = $getSurgicalRecordRows['iol4OD'];
			$iol4OD = print_getLenseName($iol4OD);
		$iol4PowerOD = $getSurgicalRecordRows['iol4PowerOD'];
		$iol4HolladayOD = $getSurgicalRecordRows['iol4HolladayOD'];
		$iol4srk_tOD = $getSurgicalRecordRows['iol4srk_tOD'];
		$iol4HofferOD = $getSurgicalRecordRows['iol4HofferOD'];
		$cellCountOD = $getSurgicalRecordRows['cellCountOD'];
		$notesOD = $getSurgicalRecordRows['notesOD'];
		$pachymetryValOD = $getSurgicalRecordRows['pachymetryValOD'];
		$pachymetryCorrecOD = $getSurgicalRecordRows['pachymetryCorrecOD'];
		$cornealDiamOD = $getSurgicalRecordRows['cornealDiamOD'];
		$dominantEyeOD = $getSurgicalRecordRows['dominantEyeOD'];
		$pupilSize1OD = $getSurgicalRecordRows['pupilSize1OD'];
		$pupilSize2OD = $getSurgicalRecordRows['pupilSize2OD'];
		$cataractOD = $getSurgicalRecordRows['cataractOD'];
		$astigmatismOD = $getSurgicalRecordRows['astigmatismOD'];
		$myopiaOD = $getSurgicalRecordRows['myopiaOD'];
		$selecedIOLsOD = $getSurgicalRecordRows['selecedIOLsOD'];
			$selecedIOLsOD = print_getLenseName($selecedIOLsOD);
		$notesAssesmentPlansOD = $getSurgicalRecordRows['notesAssesmentPlansOD'];
		$lriOD = $getSurgicalRecordRows['lriOD'];
		$dlOD = $getSurgicalRecordRows['dlOD'];
		$synechiolysisOD = $getSurgicalRecordRows['synechiolysisOD'];
		$irishooksOD = $getSurgicalRecordRows['irishooksOD'];
		$trypanblueOD = $getSurgicalRecordRows['trypanblueOD'];
		$flomaxOD = $getSurgicalRecordRows['flomaxOD'];
		$cutsOD = $getSurgicalRecordRows['cutsOD'];
		$lengthOD = $getSurgicalRecordRows['lengthOD'];
		$lengthTypeOD = $getSurgicalRecordRows['lengthTypeOD'];
			if($lengthTypeOD == 'percent') $lengthTypeOD = '%';
		$axisOD = $getSurgicalRecordRows['axisOD'];
		$superiorOD = $getSurgicalRecordRows['superiorOD'];
		$inferiorOD = $getSurgicalRecordRows['inferiorOD'];
		$nasalOD = $getSurgicalRecordRows['nasalOD'];
		$temporalOD = $getSurgicalRecordRows['temporalOD'];
		$STOD = $getSurgicalRecordRows['STOD'];
		$SNOD = $getSurgicalRecordRows['SNOD'];
		$ITOD = $getSurgicalRecordRows['ITOD'];
		$INOD = $getSurgicalRecordRows['INOD'];
		$mrSOS = $getSurgicalRecordRows['mrSOS'];
		$mrCOS = $getSurgicalRecordRows['mrCOS'];
		$mrAOS = $getSurgicalRecordRows['mrAOS'];
		$visionOS = $getSurgicalRecordRows['visionOS'];
		$glareOS = $getSurgicalRecordRows['glareOS'];
		$provider_idOS = $getSurgicalRecordRows['performedByOS'];
				$performedByOSFname = print_getData('fname', 'users', 'id', $provider_idOS);
				$performedByOSLname = print_getData('lname', 'users', 'id', $provider_idOS);
				$performedByOS = $performedByOSFname." ".$performedByOSLname;
		
		$dateOS = $getSurgicalRecordRows['dateOS'];
			list($dateOSYear, $dateOSMonth, $dateOSDay) = split("-", $dateOS);
			$dateOS = $dateOSMonth."-".$dateOSDay."-".$dateOSYear;
		
		$autoSelectOS = $getSurgicalRecordRows['autoSelectOS'];
			$autoSelectOS = print_getKHeadingName($autoSelectOS);
		$iolMasterSelectOS = $getSurgicalRecordRows['iolMasterSelectOS'];
			$iolMasterSelectOS = print_getKHeadingName($iolMasterSelectOS);
		$topographerSelectOS = $getSurgicalRecordRows['topographerSelectOS'];
			$topographerSelectOS = print_getKHeadingName($topographerSelectOS);
		$k1Auto1OS = $getSurgicalRecordRows['k1Auto1OS'];
		$vis_ak_os_k = $getSurgicalRecordRows['k1Auto1OS'];
		$vis_ak_os_slash = $getSurgicalRecordRows['k2Auto1OS'];
		$k1Auto2OS = $getSurgicalRecordRows['k1Auto2OS'];		
		$k1IolMaster1OS = $getSurgicalRecordRows['k1IolMaster1OS'];
		$k1IolMaster2OS = $getSurgicalRecordRows['k1IolMaster2OS'];
		$k1Topographer1OS = $getSurgicalRecordRows['k1Topographer1OS'];
		$k1Topographer2OS = $getSurgicalRecordRows['k1Topographer2OS'];
		$k2Auto1OS = $getSurgicalRecordRows['k2Auto1OS'];
		$k2Auto2OS = $getSurgicalRecordRows['k2Auto2OS'];
		$k2IolMaster1OS = $getSurgicalRecordRows['k2IolMaster1OS'];
		$k2IolMaster2OS = $getSurgicalRecordRows['k2IolMaster2OS'];
		$k2Topographer1OS = $getSurgicalRecordRows['k2Topographer1OS'];
		$k2Topographer2OS = $getSurgicalRecordRows['k2Topographer2OS'];
		
		$cylAuto1OS = $getSurgicalRecordRows['cylAuto1OS'];
		$cylAuto2OS = $getSurgicalRecordRows['cylAuto2OS'];		
		$cylIolMaster1OS = $getSurgicalRecordRows['cylIolMaster1OS'];
		$cylIolMaster2OS = $getSurgicalRecordRows['cylIolMaster2OS'];
		$cylTopographer1OS = $getSurgicalRecordRows['cylTopographer1OS'];
		$cylTopographer2OS = $getSurgicalRecordRows['cylTopographer2OS'];
		
		$aveAutoOS = $getSurgicalRecordRows['aveAutoOS'];
		$aveIolMasterOS = $getSurgicalRecordRows['aveIolMasterOS'];
		$aveTopographerOS = $getSurgicalRecordRows['aveTopographerOS'];
		$contactLengthOS = $getSurgicalRecordRows['contactLengthOS'];
		$immersionLengthOS = $getSurgicalRecordRows['immersionLengthOS'];
		$iolMasterLengthOS = $getSurgicalRecordRows['iolMasterLengthOS'];
		$contactNotesOS = $getSurgicalRecordRows['contactNotesOS'];
		$immersionNotesOS = $getSurgicalRecordRows['immersionNotesOS'];
		$iolMasterNotesOS = $getSurgicalRecordRows['iolMasterNotesOS'];
		$performedByPhyOS = $getSurgicalRecordRows['performedByPhyOS'];
		
		$performedIolOS = $getSurgicalRecordRows['performedIolOS'];		
				$performedByPhyOSFname = print_getData('fname', 'users', 'id', $performedIolOS);
				$performedByPhyOSLname = print_getData('lname', 'users', 'id', $performedIolOS);
				$performedIolOS = $performedByPhyOSFname." ".$performedByPhyOSLname;
		
		$powerIolOS = $getSurgicalRecordRows['powerIolOS'];
			$powerIolOS = print_getFormulaHeadName($powerIolOS);
		$holladayOS = $getSurgicalRecordRows['holladayOS'];
			$holladayOS = print_getFormulaHeadName($holladayOS);
		$srk_tOS = $getSurgicalRecordRows['srk_tOS'];
			$srk_tOS = print_getFormulaHeadName($srk_tOS);
		$hofferOS = $getSurgicalRecordRows['hofferOS'];
			$hofferOS = print_getFormulaHeadName($hofferOS);
		$iol1OS = $getSurgicalRecordRows['iol1OS'];
			$iol1OS = print_getData('lenses_iol_type', 'lenses_iol_type', 'iol_type_id', $iol1OS);			
		$iol1PowerOS = $getSurgicalRecordRows['iol1PowerOS'];
		$iol1HolladayOS = $getSurgicalRecordRows['iol1HolladayOS'];
		$iol1srk_tOS = $getSurgicalRecordRows['iol1srk_tOS'];
		$iol1HofferOS = $getSurgicalRecordRows['iol1HofferOS'];
		$iol2OS = $getSurgicalRecordRows['iol2OS'];
			$iol2OS = print_getData('lenses_iol_type', 'lenses_iol_type', 'iol_type_id', $iol2OS);
		$iol2PowerOS = $getSurgicalRecordRows['iol2PowerOS'];
		$iol2HolladayOS = $getSurgicalRecordRows['iol2HolladayOS'];
		$iol2srk_tOS = $getSurgicalRecordRows['iol2srk_tOS'];
		$iol2HofferOS = $getSurgicalRecordRows['iol2HofferOS'];
		$iol3OS = $getSurgicalRecordRows['iol3OS'];
			$iol3OS = print_getData('lenses_iol_type', 'lenses_iol_type', 'iol_type_id', $iol3OS);
		$iol3PowerOS = $getSurgicalRecordRows['iol3PowerOS'];
		$iol3HolladayOS = $getSurgicalRecordRows['iol3HolladayOS'];
		$iol3srk_tOS = $getSurgicalRecordRows['iol3srk_tOS'];
		$iol3HofferOS = $getSurgicalRecordRows['iol3HofferOS'];
		$iol4OS = $getSurgicalRecordRows['iol4OS'];
			$iol4OS = print_getData('lenses_iol_type', 'lenses_iol_type', 'iol_type_id', $iol4OS);
		$iol4PowerOS = $getSurgicalRecordRows['iol4PowerOS'];
		$iol4HolladayOS = $getSurgicalRecordRows['iol4HolladayOS'];
		$iol4srk_tOS = $getSurgicalRecordRows['iol4srk_tOS'];
		$iol4HofferOS = $getSurgicalRecordRows['iol4HofferOS'];
		$cellCountOS = $getSurgicalRecordRows['cellCountOS'];
		$notesOS = $getSurgicalRecordRows['notesOS'];
		$pachymetryValOS = $getSurgicalRecordRows['pachymetryValOS'];
		$pachymetryCorrecOS = $getSurgicalRecordRows['pachymetryCorrecOS'];
		$cornealDiamOS = $getSurgicalRecordRows['cornealDiamOS'];
		$dominantEyeOS = $getSurgicalRecordRows['dominantEyeOS'];
		$pupilSize1OS = $getSurgicalRecordRows['pupilSize1OS'];
		$pupilSize2OS = $getSurgicalRecordRows['pupilSize2OS'];
		$cataractOS = $getSurgicalRecordRows['cataractOS'];
		$astigmatismOS = $getSurgicalRecordRows['astigmatismOS'];
		$myopiaOS = $getSurgicalRecordRows['myopiaOS'];
		$selecedIOLsOS = $getSurgicalRecordRows['selecedIOLsOS'];
			$selecedIOLsOS = print_getLenseName($selecedIOLsOS);
		$notesAssesmentPlansOS = $getSurgicalRecordRows['notesAssesmentPlansOS'];
		$lriOS = $getSurgicalRecordRows['lriOS'];
		$dlOS = $getSurgicalRecordRows['dlOS'];
		$synechiolysisOS = $getSurgicalRecordRows['synechiolysisOS'];
		$irishooksOS = $getSurgicalRecordRows['irishooksOS'];
		$trypanblueOS = $getSurgicalRecordRows['trypanblueOS'];
		$flomaxOS = $getSurgicalRecordRows['flomaxOS'];
		$cutsOS = $getSurgicalRecordRows['cutsOS'];
		$lengthOS = $getSurgicalRecordRows['lengthOS'];
		$lengthTypeOS = $getSurgicalRecordRows['lengthTypeOS'];		
			if($lengthTypeOS == 'percent') $lengthTypeOS = '%';
		$axisOS = $getSurgicalRecordRows['axisOS'];
		$superiorOS = $getSurgicalRecordRows['superiorOS'];
		$inferiorOS = $getSurgicalRecordRows['inferiorOS'];
		$nasalOS = $getSurgicalRecordRows['nasalOS'];
		$temporalOS = $getSurgicalRecordRows['temporalOS'];
		$STOS = $getSurgicalRecordRows['STOS'];
		$SNOS = $getSurgicalRecordRows['SNOS'];
		$ITOS = $getSurgicalRecordRows['ITOS'];
		$INOS = $getSurgicalRecordRows['INOS'];
		$signedById = $getSurgicalRecordRows['signedById'];		
		$signature = $getSurgicalRecordRows['signature'];
		$signedByOSId = $getSurgicalRecordRows['signedByOSId'];
		$signatureOS = $getSurgicalRecordRows['signatureOS'];
}


?>
<table cellpadding="0" cellspacing="0" align="left" border="0" style="display:none;width:100%;">
		
		<tr>
			<td align="center" style="width:100%;">
					
				<table border="0" cellpadding="0" cellspacing="0" style="width:100%;">
					<tr height="50">
						<td align="center" class="text_10b" style="width:50%;">
						<table border="0" cellpadding="0" cellspacing="0">
							<tr>
								<td class="text_10b"><span style="color:#0000FF;">OD</span></td>
								
							</tr>
						</table>
							<table border="0" cellpadding="0" cellspacing="0">
								<tr height="">
									<td class="text_10b" align="">MR</td>
									<td class="text_10b" width="5">&nbsp;</td>
									<td width="45" valign="middle">
										<table border="0" cellpadding="0" cellspacing="0">
											<td class="text_10b" align="left">S:&nbsp;</td>
											<td class="text_9" align="right"><?php if($vis_mr_od_s!='') echo $vis_mr_od_s; ?></td>
										</table>
									</td>
									<td width="10" valign="middle">&nbsp;</td>
									<td width="45" valign="middle">
										<table border="0" cellpadding="0" cellspacing="0">
											<td class="text_10b">C:&nbsp;</td>
											<td class="text_9"><?php if($vis_mr_od_c!='') echo $vis_mr_od_c; ?></td>
										</table>
									</td>
									<td width="10" valign="middle">&nbsp;</td>
									<td width="45" valign="middle">
										<table border="0" cellpadding="0" cellspacing="0">
											<td class="text_10b">A:&nbsp;</td>
											<td class="text_9"><?php if($vis_mr_od_a!='') echo $vis_mr_od_a; ?>&#176;</td>
										</table>
									</td>
									<td width="45" class="text_10b" align="right">Vision:</td>
									<td width="45" class="text_9"><?php if($visionOD!='') echo $visionOD; ?></td>
									<td width="10" class="text_10b" align="center">/</td>
									<td width="45" class="text_9" align="right"><?php if($glareOD!='') echo $glareOD; ?></td>
									<td width="15"></td>
								</tr>
								<tr class="text_9">
									<td class="text" align="center">&nbsp;</td>
									<td class="text" align="center">&nbsp;</td>
									<td class="text" align="center">&nbsp;</td>
									<td class="text" align="center">&nbsp;</td>
									<td class="text" align="center">&nbsp;</td>
									<td class="text" align="center">&nbsp;</td>
									<td class="text" align="center">&nbsp;</td>
									<td class="text" align="center">&nbsp;</td>
									<td width="45" class="text"  align="left" >Vision</td>
									<td width="10" class="text" align="center" >/</td>
									<td width="45" class="text" align="left" >Glare</td>
									<td class="text" align="center">&nbsp;</td>
							  </tr>
						  </table>
						</td>
						
						<td align="center" class="text_10b" style="width:50%;">
						<table border="0" cellpadding="0" cellspacing="0">
							<tr>
								<td class="text_10b"><span style="color:#009900;">OS</span></td>
							</tr>
						</table>
							<table border="0" cellpadding="0" cellspacing="0">
								<tr>
									<td class="text_10b" align="">MR</td>
									<td class="text_10b" width="5">&nbsp;</td>
									<td width="45" valign="middle">
										<table border="0" cellpadding="0" cellspacing="0">
											<td class="text_10b">S:&nbsp;</td>
											<td class="text_9"><?php if($mrSOS!='') echo $mrSOS; else echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"; ?></td>
										</table>
									</td>								
									<td width="10" valign="middle">&nbsp;</td>
									<td width="45" valign="middle">
										<table border="0" cellpadding="0" cellspacing="0">
											<td class="text_10b">C:&nbsp;</td>
											<td class="text_9"><?php if($mrCOS!='') echo $mrCOS; else echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"; ?></td>
										</table>
									</td>
									<td width="10" valign="middle">&nbsp;</td>
									<td width="45" valign="middle">
										<table border="0" cellpadding="0" cellspacing="0">
											<td class="text_10b">A:&nbsp;</td>
											<td class="text_9"><?php if($mrAOS!='') echo $mrAOS; else echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"; ?>&#176;</td>
										</table>
									</td>
									<td width="10" valign="middle">&nbsp;</td>
									<td width="45" class="text_10b" align="">Vision:</td>
									<td width="45" class="text_9"><?php if($visionOS!='') echo $visionOS; else echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"; ?></td>
									<td width="10" class="text_10b" align="">/</td>
									<td width="45" class="text_9"><?php if($glareOS!='') echo $glareOS; else echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"; ?></td>
									<td class="text_10b" align=""></td>
								</tr>
								<tr>
									<td class="text_9" align="center">&nbsp;</td>
									<td class="text_9" align="center">&nbsp;</td>
									<td class="text_9" align="center">&nbsp;</td>
									<td class="text_9" align="center">&nbsp;</td>
									<td class="text_9" align="center">&nbsp;</td>
									<td class="text_9" align="center">&nbsp;</td>
									<td class="text_9" align="center">&nbsp;</td>
									<td class="text_9" align="center">&nbsp;</td>
									<td class="text_9" align="center">&nbsp;</td>
									<td class="text_9" align="left" >Vision</td>
									<td class="text_9" align="center" >/</td>
									<td class="text_9" align="left" >Glare</td>
									<td class="text" align="center">&nbsp;</td>
								</tr>
						  </table>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td align="left" valign="top" style="width:45%;">
				<table border="0" cellpadding="0" cellspacing="0">
					<tr>
						<td style="width:45%;" align="left" valign="top">
							<table border="0" cellpadding="0" cellspacing="1" bordercolor="#FFFFFF" style="width:100%;">
									<tr height="20">
										<td align="left" class="text_10b" ><span style="color:#0000FF;font-size:<?php if($printAscan == 'printFromOutOfAScan') echo "10px"; else echo "14px"; ?>;font-weight:bold;">OD</span></td>
										<td align="left" class="text_10b">&nbsp;</td>
										<td align="left" class="text_10b">Performed By:</td>
										<td align="left" class="text_10b"><?php echo $performedByOD; ?></td>
										<td align="left" class="text_10b">Date:</td>
										<td align="left" class="text_9"><?php echo $dateOD; ?></td>
									</tr>
								</table>
								
									<table border="0" cellpadding="0" cellspacing="0">
										<tr height="10">
											<td colspan="6"></td>
										</tr>
										<tr>
											<td width="5"></td>
											<td align="left" class="text_10b"><?php echo $autoSelectOD; ?></td>
											<td width="5" align="left"></td>
											<td align="left" class="text_10b"><?php echo $iolMasterSelectOD; ?></td>
											<td width="5" align="left"></td>
											<td align="left" class="text_10b"><?php echo $topographerSelectOD; ?></td>
										</tr>
										<tr height="5">
											<td colspan="6"></td>
										</tr>
										<!-- K1 -->
										<tr height="20">
											<td width="5" class="text_10b">K1</td>
											<?php
											if($vis_ak_od_k){
												?>
												<td align="left">
													<table border="0" cellpadding="0" cellspacing="0">
														<tr>
															<td align="left" class="text_9" width="35"><?php if(($vis_ak_od_k!='') && ($vis_ak_od_k!=0)) echo number_format($vis_ak_od_k, 2); else echo "&nbsp;"; ?></td>
															<td align="center" class="text_9" width="10">X</td>
															<td align="left" class="text_9" width="35" style="padding-left:1px;"><?php if(($vis_ak_od_x!='') && ($vis_ak_od_x!=0)) echo $vis_ak_od_x; else echo "&nbsp;"; ?>&#176;</td>
														</tr>
													</table>
												</td>
												<?php
											}
											?>
											<td width="5"></td>
											<td align="left">
												<table border="0" cellpadding="0" cellspacing="0">
													<?php
													if($k1IolMaster1OD){
														?>
														<tr>
															<td align="left" class="text_9" width="35"><?php if(($k1IolMaster1OD!='') && ($k1IolMaster1OD!=0)) echo number_format($k1IolMaster1OD, 2); else echo "&nbsp;"; ?></td>
															<td align="center" class="text_9" width="10">X</td>
															<td align="left" class="text_9" width="35" style="padding-left:1px;"><?php if(($k1IolMaster2OD!='') && ($k1IolMaster2OD!=0)) echo $k1IolMaster2OD; else echo "&nbsp;"; ?>&#176;</td>
														</tr>
														<?php
													}
													?>
												</table>
											</td>
											<td width="5"></td>
											<td align="left">
												<table border="0" cellpadding="0" cellspacing="0">
													<?php
													if($k1Topographer1OD){
														?>
														<tr>
															<td align="left" class="text_9" width="35"><?php if(($k1Topographer1OD!='') && ($k1Topographer1OD!=0)) echo number_format($k1Topographer1OD, 2); else echo "&nbsp;"; ?></td>
															<td align="center" class="text_9" width="10">X</td>
															<td align="left" class="text_9" width="35" style="padding-left:1px;"><?php if(($k1Topographer2OD!='') && ($k1Topographer2OD!=0)) echo $k1Topographer2OD; else echo "&nbsp;"; ?>&#176;</td>
														</tr>
														<?php
													}
													?>
												</table>
											</td>
										</tr>
										<!-- K1 -->
										
										<!-- K2 -->
										<tr height="20">
											<td width="5" class="text_10b">K2</td>
											<td align="left">
												<table border="0" cellpadding="0" cellspacing="0">
													<?php
													if($vis_ak_od_k){
														?>
														<tr>
															<td align="left" class="text_9" width="35"><?php if(($vis_ak_od_slash!='') && ($vis_ak_od_slash!=0)) echo number_format($vis_ak_od_slash, 2); else echo "&nbsp;"; ?></td>
															<td align="center" class="text_9" width="10">X</td>
															<td align="left" class="text_9" width="35" style="padding-left:1px;"><?php if(($k2Auto2OD!='') && ($k2Auto2OD!=0)) echo $k2Auto2OD; else echo "&nbsp;"; ?>&#176;</td>
														</tr>
														<?php
													}
													?>
												</table>
											</td>
											<td width="5"></td>
											<td align="left">
												<table border="0" cellpadding="0" cellspacing="0">
													<?php
													if($k1IolMaster1OD){
														?>
														<tr>
															<td align="left" class="text_9" width="35"><?php if(($k2IolMaster1OD!='') && ($k2IolMaster1OD!=0)) echo number_format($k2IolMaster1OD, 2); else echo "&nbsp;"; ?></td>
															<td align="center" class="text_9" width="10">X</td>
															<td align="left" class="text_9" width="35" style="padding-left:1px;"><?php if(($k2IolMaster2OD!='') && ($k2IolMaster2OD!=0)) echo $k2IolMaster2OD; else echo "&nbsp;"; ?>&#176;</td>
														</tr>
														<?php
													}
													?>
												</table>
											</td>
											<td width="5"></td>
											<td align="left">
												<table border="0" cellpadding="0" cellspacing="0">
													<?php
													if($k1Topographer1OD){
														?>
														<tr>
															<td align="left" class="text_9" width="35"><?php if(($k2Topographer1OD!='') && ($k2Topographer1OD!=0)) echo number_format($k2Topographer1OD, 2); else echo "&nbsp;"; ?></td>
															<td align="center" class="text_9" width="10">X</td>
															<td align="left" class="text_9" width="35" style="padding-left:1px;"><?php if(($k2Topographer2OD!='') && ($k2Topographer2OD!=0)) echo $k2Topographer2OD; else echo "&nbsp;"; ?>&#176;</td>
														</tr>
														<?php
													}
													?>
												</table>
											</td>
										</tr>
										<!-- K2 -->
										
										<!-- CYL -->
										<tr height="20">
											<td width="5" class="text_10b">CYL</td>
											<td align="left">
												<table border="0" cellpadding="0" cellspacing="0">
													<?php
													if($vis_ak_od_k){
														?>
														<tr>
															<td align="left" class="text_9" width="35"><?php if(($cylAuto1OD!='') && ($cylAuto1OD!=0)) echo number_format($cylAuto1OD, 2); else echo "&nbsp;"; ?></td>
															<td align="center" class="text_9">@</td>
															<td align="left" class="text_9" width="35" style="padding-left:1px;"><?php if(($cylAuto2OD!='') && ($cylAuto2OD!=0)) echo $cylAuto2OD; else echo "&nbsp;"; ?>&#176;</td>
														</tr>
														<?php
													}
													?>
												</table>
											</td>
											<td width="5"></td>
											<td align="left">
												<table border="0" cellpadding="0" cellspacing="0">
													<?php
													if($k1IolMaster1OD){
														?>
														<tr>
															<td align="left" class="text_9" width="35"><?php if(($cylIolMaster1OD!='') && ($cylIolMaster1OD!=0)) echo number_format($cylIolMaster1OD, 2); else echo "&nbsp;"; ?></td>
															<td align="center" class="text_9">@</td>
															<td align="left" class="text_9" width="35" style="padding-left:1px;"><?php if(($cylIolMaster2OD!='') && ($cylIolMaster2OD!=0)) echo $cylIolMaster2OD; else echo "&nbsp;"; ?>&#176;</td>
														</tr>
														<?php
													}
													?>
												</table>
											</td>
											<td width="5"></td>
											<td align="left">
												<table border="0" cellpadding="0" cellspacing="0">
													<?php
													if($k1Topographer1OD){
														?>
														<tr>
															<td align="left" class="text_9" width="35"><?php if(($cylTopographer1OD!='') && ($cylTopographer1OD!=0)) echo number_format($cylTopographer1OD, 2); else echo "&nbsp;"; ?></td>
															<td align="center" class="text_9">@</td>
															<td align="left" class="text_9" width="35" style="padding-left:1px;"><?php if(($cylTopographer2OD!='') && ($cylTopographer2OD!=0)) echo $cylTopographer2OD; else echo "&nbsp;"; ?>&#176;</td>
														</tr>
														<?php
													}
													?>
												</table>
											</td>
										</tr>
										<!-- CYL -->
	
										<!-- AVE -->
										<tr height="20">
											<td width="5" class="text_10b">AVE</td>
											<td align="left">
												<table border="0" cellpadding="0" cellspacing="0">
													<?php
													if($vis_ak_od_k){
														?>
														<tr>
															<td align="center" class="text_9"><?php if(($aveOD1!='') && ($aveOD1!=0))  echo number_format($aveOD1, 2); else echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"; ?></td>
														</tr>
														<?php
													}
													?>
												</table>
											</td>
											<td width="5"></td>
											<td align="left">
												<table border="0" cellpadding="0" cellspacing="0">
													<?php
													if($k1IolMaster1OD){
														?>
														<tr>
															<td align="center" class="text_9"><?php if(($aveIolMasterOD!='') && ($aveIolMasterOD!=0)) echo number_format($aveIolMasterOD, 2); else echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"; ?></td>
														</tr>
														<?php
													}
													?>
												</table>
											</td>
											<td width="5"></td>
											<td align="left">
												<table border="0" cellpadding="0" cellspacing="0">
													<?php
													if($k1Topographer1OD){
														?>
														<tr>
															<td align="center" class="text_9"><?php if(($aveTopographerOD!='') && ($aveTopographerOD!=0)) echo number_format($aveTopographerOD, 2); else echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"; ?></td>
														</tr>
														<?php
													}
													?>
												</table>
											</td>
										</tr>
										<!-- AVE -->
										
										<tr height="20">
											<td colspan="6" align="left">
												<table border="0" cellpadding="0" cellspacing="1">
													<tr height="10">
														<td colspan="4"></td>
													</tr>
													<tr height="">
														<td align="left" class="text_10b">Axial</td>
														<td align="left" class="text_10b" width="86" style="padding-left:10px;">Contact</td>
														<td align="left" class="text_10b" width="86" style="padding-left:2px;">Immersion</td>
														<td align="left" class="text_10b" width="86" style="padding-left:2px;">IOL Master</td>
													</tr>
													<tr height="">
														<td align="left" class="text_10b">Length</td>
														<td align="left" class="text_9" style="padding-left:10px;"><?php echo $contactLengthOD; ?></td>
														<td align="left" class="text_9" style="padding-left:2px;"><?php echo $immersionLengthOD; ?></td>
														<td align="left" class="text_9" style="padding-left:2px;"><?php echo $iolMasterLengthOD; ?></td>
													</tr>
													<tr height="20">
														<?php
														if(($contactNotesOD) || ($immersionNotesOD) || ($iolMasterNotesOD)){
															?>
															<td align="left" class="text_10b">Notes</td>
															<td align="left" class="text_9" style="padding-left:2px;"><?php echo nl2br($contactNotesOD); ?></td>
															<td align="left" class="text_9" style="padding-left:2px;"><?php echo nl2br($immersionNotesOD); ?></td>
															<td align="left" class="text_9" style="padding-left:2px;"><?php echo nl2br($iolMasterNotesOD); ?></td>
															<?php
														}
														?>
													</tr>
													<tr height="2">
														<td colspan="4"></td>
													</tr>
											  </table>
											</td>
										</tr>									
								  </table>
								
						</td>
						<td style="width: 12%;">&nbsp;</td>
						<td style="width: 50%; padding-left:5px;" align="left" valign="top">
							<table border="0" cellpadding="0" cellspacing="1" bordercolor="#FFFFFF" style="width:100%;">
									<tr height="20">
										<td align="left" class="text_10b" ><span style="color:#009900;font-size:<?php if($printAscan == 'printFromOutOfAScan') echo "10px"; else echo "14px"; ?>;font-weight:bold;">OS</span></td>
										<td align="left" class="text_10b">&nbsp;</td>
										<td align="left" class="text_10b">Performed By:</td>
										<td align="left" class="text_10b"><?php echo $performedByOS; ?></td>
										<td align="left" class="text_10b">Date:</td>
										<td align="left" class="text_9"><?php echo $dateOS; ?></td>
									</tr>
								</table>
								
									<table border="0" cellpadding="0" cellspacing="0" style="width: 100%;">
										<tr height="10">
											<td colspan="6"></td>
										</tr>
										<tr>
											<td width="5"></td>
											<td align="left" class="text_10b"><?php echo $autoSelectOS; ?></td>
											<td width="5" align="left"></td>
											<td align="left" class="text_10b"><?php echo $iolMasterSelectOS; ?></td>
											<td width="5" align="left"></td>
											<td align="left" class="text_10b"><?php echo $topographerSelectOS; ?></td>
										</tr>
										<tr height="5">
											<td colspan="6"></td>
										</tr>
										<!-- K1 -->
										<tr height="20">
											<td width="5" class="text_10b">K1</td>
											<td align="left">
												<table border="0" cellpadding="0" cellspacing="0">
													<?php
													if($k1Auto1OS){
														?>
														<tr>
															<td align="left" class="text_9" width="35"><?php if(($k1Auto1OS!='') && ($k1Auto1OS!=0)) echo number_format($k1Auto1OS, 2); else echo "&nbsp;"; ?></td>
															<td align="center" class="text_9" width="10">X</td>
															<td align="left" class="text_9" width="35" style="padding-left:1px;"><?php if(($k1Auto2OS!='') && ($k1Auto2OS!=0)) echo $k1Auto2OS; else echo "&nbsp;"; ?>&#176;</td>
														</tr>
														<?php
													}
													?>
												</table>
											</td>
											<td width="5"></td>
											<td align="left">
												<table border="0" cellpadding="0" cellspacing="0">
													<?php
													if($k1IolMaster1OS){
														?>
														<tr>
															<td align="left" class="text_9" width="35"><?php if(($k1IolMaster1OS!='') && ($k1IolMaster1OS!=0)) echo number_format($k1IolMaster1OS, 2); else echo "&nbsp;"; ?></td>
															<td align="center" class="text_9" width="10">X</td>
															<td align="left" class="text_9" width="35" style="padding-left:1px;"><?php if(($k1IolMaster2OS!='') && ($k1IolMaster2OS!=0)) echo $k1IolMaster2OS; else echo "&nbsp;"; ?>&#176;</td>
														</tr>
														<?php
													}
													?>
												</table>
											</td>
											<td width="5"></td>
											<td align="left">
												<table border="0" cellpadding="0" cellspacing="0">
													<?php
													if($k1Topographer1OS){
														?>
														<tr>
															<td align="left" class="text_9" width="35"><?php if(($k1Topographer1OS!='') && ($k1Topographer1OS!=0)) echo number_format($k1Topographer1OS, 2); else echo "&nbsp;"; ?></td>
															<td align="center" class="text_9" width="10">X</td>
															<td align="left" class="text_9" width="35" style="padding-left:1px;"><?php if(($k1Topographer2OS!='') && ($k1Topographer2OS!=0)) echo $k1Topographer2OS; else echo "&nbsp;"; ?>&#176;</td>
														</tr>
														<?php
													}
													?>
												</table>
											</td>
										</tr>
										<!-- K1 -->
	
										<!-- K2 -->
										<tr height="20">
											<td width="5" class="text_10b">K2</td>
											<td align="left">
												<table border="0" cellpadding="0" cellspacing="0">
													<?php
													if($k1Auto1OS){
														?>
														<tr>
															<td align="left" class="text_9" width="35"><?php if(($vis_ak_os_slash!='') && ($vis_ak_os_slash!=0)) echo number_format($vis_ak_os_slash, 2); else echo "&nbsp;"; ?></td>
															<td align="center" class="text_9" width="10">X</td>
															<td align="left" class="text_9" width="35" style="padding-left:1px;"><?php if(($k2Auto2OS!='') && ($k2Auto2OS!=0)) echo $k2Auto2OS; else echo "&nbsp;"; ?>&#176;</td>
														</tr>
														<?php
													}
													?>
												</table>
											</td>
											<td width="5"></td>
											<td align="left">
												<table border="0" cellpadding="0" cellspacing="0">
													<?php
													if($k1IolMaster1OS){
														?>
														<tr>
															<td align="left" class="text_9" width="35"><?php if(($k2IolMaster1OS!='') && ($k2IolMaster1OS!=0)) echo number_format($k2IolMaster1OS, 2); else echo "&nbsp;"; ?></td>
															<td align="center" class="text_9" width="10">X</td>
															<td align="left" class="text_9" width="35" style="padding-left:1px;"><?php if(($k2IolMaster2OS!='') && ($k2IolMaster2OS!=0)) echo $k2IolMaster2OS; else echo "&nbsp;"; ?>&#176;</td>
														</tr>
														<?php
													}
													?>
												</table>
											</td>
											<td width="5"></td>
											<td align="left">
												<table border="0" cellpadding="0" cellspacing="0">
													<?php 
													if($k1Topographer1OS){
														?>
														<tr>
															<td align="left" class="text_9" width="35"><?php if(($k2Topographer1OS!='') && ($k2Topographer1OS!=0)) echo number_format($k2Topographer1OS, 2); else echo "&nbsp;"; ?></td>
															<td align="center" class="text_9" width="10">X</td>
															<td align="left" class="text_9" width="35" style="padding-left:1px;"><?php if(($k2Topographer2OS!='') && ($k2Topographer2OS!=0)) echo $k2Topographer2OS; else echo "&nbsp;"; ?>&#176;</td>
														</tr>
														<?php
													}
													?>
												</table>
											</td>
										</tr>
										<!-- K2 -->
										
										<!-- CYL -->
										<tr height="20">
											<td width="5" class="text_10b">CYL</td>
											<td align="left">
												<table border="0" cellpadding="0" cellspacing="0">
													<?php
													if($k1Auto1OS){
														?>
														<tr>
															<td align="left" class="text_9" width="35"><?php if(($cylAuto1OS!='') && ($cylAuto1OS!=0)) echo number_format($cylAuto1OS, 2); else echo "&nbsp;"; ?></td>
															<td align="center" class="text_9">@</td>
															<td align="left" class="text_9" width="35" style="padding-left:1px;"><?php if(($cylAuto2OS!='') && ($cylAuto2OS!=0)) echo $cylAuto2OS; else echo "&nbsp;"; ?>&#176;</td>
														</tr>
														<?php
													}
													?>
												</table>
											</td>
											<td width="5"></td>
											<td align="left">
												<table border="0" cellpadding="0" cellspacing="0">
													<?php
													if($k1IolMaster1OS){
														?>
														<tr>
															<td align="left" class="text_9" width="35"><?php if(($cylIolMaster1OS!='') && ($cylIolMaster1OS!=0)) echo number_format($cylIolMaster1OS, 2); else echo "&nbsp;"; ?></td>
															<td align="center" class="text_9">@</td>
															<td align="left" class="text_9" width="35" style="padding-left:1px;"><?php if(($cylIolMaster2OS!='') && ($cylIolMaster2OS!=0)) echo $cylIolMaster2OS; else echo "&nbsp;"; ?>&#176;</td>
														</tr>
														<?php
													}
													?>
												</table>
											</td>
											<td width="5"></td>
											<td align="left">
												<table border="0" cellpadding="0" cellspacing="0">
													<?php
													if($k1Topographer1OS){
														?>
														<tr>
															<td align="left" class="text_9" width="35"><?php if(($cylTopographer1OS!='') && ($cylTopographer1OS!=0)) echo number_format($cylTopographer1OS, 2); else echo "&nbsp;"; ?></td>
															<td align="center" class="text_9">@</td>
															<td align="left" class="text_9" width="35" style="padding-left:1px;"><?php if(($cylTopographer2OS!='') && ($cylTopographer2OS!=0)) echo $cylTopographer2OS; else echo "&nbsp;"; ?>&#176;</td>
														</tr>
														<?php
													}
													?>
												</table>
											</td>
										</tr>
										<!-- CYL -->
										
										<!-- AVE -->
										<tr height="20">
											<td width="5" class="text_10b">AVE</td>
											<td align="left">
												<table border="0" cellpadding="0" cellspacing="0">
													<?php
													if($k1Auto1OS){
														?>
														<tr>
															<td align="center" class="text_9"><?php if(($aveAutoOS!='') && ($aveAutoOS!=0)) echo number_format($aveAutoOS, 2); else echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"; ?></td>
														</tr>
														<?php
													}
													?>
												</table>
											</td>
											<td width="5"></td>
											<td align="left">
												<table border="0" cellpadding="0" cellspacing="0">
													<?php
													if($k1IolMaster1OS){
														?>
														<tr>
															<td align="center" class="text_9"><?php if(($aveIolMasterOS!='') && ($aveIolMasterOS!=0)) echo number_format($aveIolMasterOS, 2); else echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"; ?></td>
														</tr>
														<?php
													}
													?>
												</table>
											</td>
											<td width="5"></td>
											<td align="left">
												<table border="0" cellpadding="0" cellspacing="0">
													<?php
													if($k1Topographer1OS){
														?>
														<tr>
															<td align="center" class="text_9"><?php if(($aveTopographerOS!='') && ($aveTopographerOS!=0)) echo number_format($aveTopographerOS, 2); else echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"; ?></td>
														</tr>
														<?php
													}
													?>
												</table>
											</td>
										</tr>
										<!-- AVE -->
										<tr height="20">
											<td colspan="6" align="left">
												<table border="0" cellpadding="0" cellspacing="1" width="100%">
													<tr height="10">
														<td colspan="4"></td>
													</tr>
													<tr height="">
														<td align="left" class="text_10b">Axial</td>
														<td align="left" class="text_10b" width="86" style="padding-left:10px;">Contact</td>
														<td align="left" class="text_10b" width="86" style="padding-left:2px;">Immersion</td>
														<td align="left" class="text_10b" width="86" style="padding-left:2px;">IOL Master</td>
													</tr>
													<tr height="">
														<td align="left" class="text_10b">Length</td>
														<td align="left" class="text_9" style="padding-left:10px;"><?php echo $contactLengthOS; ?></td>
														<td align="left" class="text_9" style="padding-left:2px;"><?php echo $immersionLengthOS; ?></td>
														<td align="left" class="text_9" style="padding-left:2px;"><?php echo $iolMasterLengthOS; ?></td>
													</tr>
													<tr height="20">
														<?php
														if(($contactNotesOS) || ($immersionNotesOS) || ($iolMasterNotesOS)){
															?>
															<td align="left" class="text_10b">Notes</td>
															<td align="left" class="text_9" style="padding-left:10px;"><?php echo nl2br($contactNotesOS); ?></td>
															<td align="left" class="text_9" style="padding-left:2px;"><?php echo nl2br($immersionNotesOS); ?></td>
															<td align="left" class="text_9" style="padding-left:2px;"><?php echo nl2br($iolMasterNotesOS); ?></td>
															<?php
														}
														?>
													</tr>
													<tr height="2">
														<td colspan="4"></td>
													</tr>
											  </table>
											</td>
										</tr>									
								  </table>
								
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr height="10">
			<td align="left"></td>
		</tr>
		<tr>
			<td align="left" valign="top" style="width: 50%;">
				<table border="0" cellpadding="0" cellspacing="5" bordercolor="#FFFFFF" style="width: 100%;">
					<tr>
						<td align="center" valign="top">
							<table border="0" cellpadding="0" cellspacing="1" bordercolor="#FFFFFF" style="width:100%;">
									<tr height="20">
										<td align="left" class="text_10b" ><span class="text_10b" style="color:#0000FF;font-size:<?php if($printAscan == 'printFromOutOfAScan') echo "10px"; else echo "14px"; ?>;">OD</span></td>
										<td align="left" class="text_10b">&nbsp;</td>
										<td align="left" class="text_10b">Performed By:</td>
										<td align="left" class="text_10b"><?php echo $provider_idOD; ?></td>
										
									</tr>
						  </table>
								
								<table border="0" cellpadding="0" cellspacing="1" bordercolor="#FFFFFF" style="padding-left:5px;">
									<tr height="20">
										<td align="left" class="text_10b" width="90">IOL</td>
										<td align="left" style="padding-left:1px;" class="text_9"><?php if($powerIolOD!='') echo $powerIolOD; else echo "&nbsp;"; ?></td>
										<td align="left" style="padding-left:1px;" class="text_9"><?php if($holladayOD!='') echo $holladayOD; else echo "&nbsp;"; ?></td>
										<td align="left" style="padding-left:1px;" class="text_9"><?php if($srk_tOD!='') echo $srk_tOD; else echo "&nbsp;"; ?></td>
										<td align="left" style="padding-left:1px;" class="text_9"><?php if($hofferOD!='') echo $hofferOD; else echo "&nbsp;"; ?></td>
									</tr>
									<tr height="20">
										<td align="left" class="text_9" width="90"><?php if($iol1OD!='') echo $iol1OD; else echo "&nbsp;"; ?></td>
										<td align="left" style="padding-left:1px;" class="text_9"><?php if($iol1PowerOD!='') echo $iol1PowerOD; else echo "&nbsp;"; ?></td>
										<td align="left" style="padding-left:1px;" class="text_9"><?php if($iol1HolladayOD!='') echo $iol1HolladayOD; else echo "&nbsp;"; ?></td>
										<td align="left" style="padding-left:1px;" class="text_9"><?php if($iol1srk_tOD!='') echo $iol1srk_tOD; else echo "&nbsp;"; ?></td>
										<td align="left" style="padding-left:1px;" class="text_9"><?php if($iol1HofferOD!='') echo $iol1HofferOD; else echo "&nbsp;"; ?></td>
									</tr>
									<tr height="20">
										<td align="left" class="text_9" width="90"><?php if($iol2OD!='') echo $iol2OD; else echo "&nbsp;"; ?></td>
										<td align="left" style="padding-left:1px;" class="text_9"><?php if($iol2PowerOD!='') echo $iol2PowerOD; else echo "&nbsp;"; ?></td>
										<td align="left" style="padding-left:1px;" class="text_9"><?php if($iol2HolladayOD!='') echo $iol2HolladayOD; else echo "&nbsp;"; ?></td>
										<td align="left" style="padding-left:1px;" class="text_9"><?php if($iol2srk_tOD!='') echo $iol2srk_tOD; else echo "&nbsp;"; ?></td>
										<td align="left" style="padding-left:1px;" class="text_9"><?php if($iol2HofferOD!='') echo $iol2HofferOD; else echo "&nbsp;"; ?></td>
									</tr>
									<tr height="20">
										<td align="left" class="text_9" width="90"><?php if($iol3OD!='') echo $iol3OD; else echo "&nbsp;"; ?></td>
										<td align="left" style="padding-left:1px;" class="text_9"><?php if($iol3PowerOD!='') echo $iol3PowerOD; else echo "&nbsp;"; ?></td>
										<td align="left" style="padding-left:1px;" class="text_9"><?php if($iol3HolladayOD!='') echo $iol3HolladayOD; else echo "&nbsp;"; ?></td>
										<td align="left" style="padding-left:1px;" class="text_9"><?php if($iol3srk_tOD!='') echo $iol3srk_tOD; else echo "&nbsp;"; ?></td>
										<td align="left" style="padding-left:1px;" class="text_9"><?php if($iol3HofferOD!='') echo $iol3HofferOD; else echo "&nbsp;"; ?></td>
									</tr>
									<tr height="20">
										<td align="left" class="text_9" width="90"><?php if($iol4OD!='') echo $iol4OD; else echo "&nbsp;"; ?></td>
										<td align="left" style="padding-left:1px;" class="text_9"><?php if($iol4PowerOD!='') echo $iol4PowerOD; else echo "&nbsp;"; ?></td>
										<td align="left" style="padding-left:1px;" class="text_9"><?php if($iol4HolladayOD!='') echo $iol4HolladayOD; else echo "&nbsp;"; ?></td>
										<td align="left" style="padding-left:1px;" class="text_9"><?php if($iol4srk_tOD!='') echo $iol4srk_tOD; else echo "&nbsp;"; ?></td>
										<td align="left" style="padding-left:1px;" class="text_9"><?php if($iol4HofferOD!='') echo $iol4HofferOD; else echo "&nbsp;"; ?></td>
									</tr>
									<tr height="20">
										<td align="left" class="text_10b" width="90">Cell Count</td>
										<td align="left" class="text_9"><?php if($cellCountOD!='') echo $cellCountOD; else echo "&nbsp;"; ?></td>
										<?php
										if($notesOD){
											?>
											<td colspan="3" rowspan="6" align="left" valign="top" class="text_9"><b>NOTES:&nbsp;</b><?php echo nl2br($notesOD); ?></td>
											<?php
										}
										?>
									</tr>
									<tr height="20">
										<td align="left" class="text_10b" width="90">Pachymetry</td>
										<td align="left" class="text_9">
											<table border="0" cellpadding="0" cellspacing="0">
												<tr>
													<td width="30" align="left" class="text_9"><?php if($pachymetryValOD!='') echo $pachymetryValOD; else echo "&nbsp;"; ?></td>
													<td width="10" align="center" class="text_9">/</td>
													<td align="center" class="text_9"><?php if($pachymetryCorrecOD!='') echo $pachymetryCorrecOD; else echo "&nbsp;"; ?></td>
												</tr>
											</table>
										</td>
									</tr>
									<tr height="20">
										<td align="left" class="text_10b" width="90">Corneal Diam</td>
										<td align="left" class="text_9"><?php if($cornealDiamOD!='') echo $cornealDiamOD; else echo "&nbsp;"; ?></td>
									</tr>
									<tr height="20">
										<td align="left" class="text_10b" width="90">Dominant Eye</td>
										<td align="left" class="text_9"><?php if($dominantEyeOD!='') echo $dominantEyeOD; else echo "&nbsp;"; ?></td>
									</tr>

									<tr height="20">
										<td align="left" class="text_10b" width="90">Pupil Size</td>
										<td align="left" class="text_9">
											<table border="0" cellpadding="0" cellspacing="0">
												<tr>
													<td align="left" width="20" class="text_9"><?php if($pupilSize1OD!='') echo $pupilSize1OD; else echo "&nbsp;"; ?></td>
													<td width="20" class="text_9" align="center">/</td>
													<td align="center" class="text_9"><?php if($pupilSize2OD!='') echo $pupilSize2OD; else echo "&nbsp;"; ?></td>
												</tr>
											</table>
										</td>
									</tr>
									<tr height="5">
										<td colspan="2" align="right" class="text_9">
											<table border="0" cellpadding="0" cellspacing="0">
												<tr>
													<td align="left" class="text_10b" >Un-Dilated</td>
													<td width="3" >/</td>
													<td align="left" class="text_10b" >Dilated</td>
												</tr>
										  </table>
										</td>
									</tr>
									<tr height="20">
										<td colspan="5" valign="top" align="left" class="text_9">&nbsp;</td>
									</tr>
									<tr height="20">
										<td colspan="5" valign="top" align="left" ><b>CC:</b>&nbsp;&nbsp;Scheduled for Intraocular lens implant<br/> A/Scan reviewed and IOL Selected.</td>
									</tr>
									<tr height="20">
										<td colspan="5" valign="top" align="left" >&nbsp;</td>
									</tr>
									<tr>
										<td colspan="5" align="left">
											<table border="0" cellpadding="0" cellspacing="0">
												<tr>
													<td align="left" class="text_10b" >Assessment:</td>
													<td ><?php if($cataractOD==1) echo "Cataract"; ?></td>
													<td > <?php if($astigmatismOD==1) echo "Astigmatism"; ?> </td>
													<td > <?php if($myopiaOD==1) echo "Myopia"; ?> </td>
												</tr>
											</table>
										</td>
									</tr>
									<tr height="5">
										<td colspan="5" valign="top" align="left" >&nbsp;</td>
									</tr>
									<tr>
										<td colspan="5" valign="top" align="left" >
											<table border="0" cellpadding="0" cellspacing="0">
												<tr>
													<td  align="left" class="text_10b">Plan:</td>
													<td  style="padding-left:5px;"><?php if($selecedIOLsOD) echo $selecedIOLsOD; else echo "&nbsp;"; ?></td>
												</tr>
											</table>
										</td>
									</tr>
									<tr height="5">
										<td colspan="5" valign="top" align="left" >&nbsp;</td>
									</tr>
									<tr height="15">
										<td colspan="5" align="left">
											<table border="0" cellpadding="0" cellspacing="0">
												<td align="left"  valign="top" class="text_10b">Notes:</td>
												<td align="left"  style="padding-left:2px;"><?php if($notesOD!='') echo nl2br($notesOD); else echo "Notes..."; ?></td>
										  </table>
										</td>
									</tr>
									<tr height="20">
										<td colspan="5">
											<table border="0" cellpadding="0" cellspacing="0">
												<tr>
													<td > <?php if($lriOD==1){ echo "LRI"; } ?> </td>
													<td > <?php if($dlOD==1){ echo "DL"; } ?> </td>
													<td > <?php if($synechiolysisOD==1){ echo "Synechiolysis"; } ?> </td>
												</tr>
												<tr>
													<td > <?php if($irishooksOD==1){ echo "IRIS Hooks"; } ?> </td>
													<td ><?php if($trypanblueOD==1){ echo "Trypan Blue"; } ?></td>
													<td > <?php if($flomaxOD==1){ echo "Pt. On Flomax"; } ?></td>
												</tr>
											</table>
										</td>
									</tr>
									<tr height="15" <?php if($lriOD!=1){ ?> style="display:none;" <?php } ?>>
										<td colspan="5" align="left">
											<table border="0" cellpadding="0" cellspacing="0">
												<tr>
													<td width="35"  align="left" class="text_10b">Cuts:</td>
													<td width="25" align="left" ><?php echo $cutsOD; ?></td>
													<td width="10"></td>
													<td width="35" align="right" class="text_10b" >Length:</td>
													<td width="35" align="center" ><?php echo $lengthOD."  ".$lengthTypeOD; ?></td>
													<td width="15"></td>
													<td width="35" align="center" class="text_10b" >Axis:</td>
													<td width="35" align="center" ><?php echo $axisOD; ?></td>
												</tr>
										  </table>
										</td>
									</tr>
									<tr height="20" <?php if($cutsOD!=1){ ?> style="display:none;" <?php } ?>>
									  <td colspan="5" align="left">
											<table width="" border="0" cellpadding="0" cellspacing="0">
												<tr>
													<td ><?php if($superiorOD==1) echo "superior"; ?> </td>
													<td > <?php if($inferiorOD==1) echo "inferior"; ?> </td>
													<td > <?php if($nasalOD==1) echo "nasal"; ?></td>
													<td > <?php if($temporalOD==1) echo "temporal"; ?> </td>
													<td > <?php if($STOD==1) echo "ST"; ?> </td>
													<td > <?php if($SNOD==1) echo "SN"; ?> </td>
												</tr>
												<tr>												
													<td > <?php if($ITOD==1) echo "IT"; ?> </td>
													<td > <?php if($INOD==1) echo "IN"; ?> </td>
												</tr>
										  </table>
									  </td>
									</tr>
						  </table>
								
						</td>
						<td style="width: 12%;">&nbsp;</td>
						<td align="center" valign="top" class="text_9">
							<table border="0" cellpadding="0" cellspacing="1" bordercolor="#FFFFFF" style="width:100%;">
									<tr height="20">
										<td align="left" class="text_10b" ><span style="color:#009900;font-size:<?php if($printAscan == 'printFromOutOfAScan') echo "10px"; else echo "14px"; ?>;"><b>OS</b></span></td>
										<td align="left" class="text_10b">&nbsp;</td>
										<td align="left" class="text_10b">Performed By:</td>
										<td align="left" class="text_10b"><?php echo $performedIolOS; ?></td>
										
									</tr>
						  </table>
								
								<table border="0" cellpadding="0" cellspacing="1" bordercolor="#FFFFFF" style="padding-left:5px;">
									<tr height="20">
										<td align="left" class="text_10b" width="104">IOL</td>
										<td width="75" align="left" class="text_9" style="padding-left:1px;"><?php if($powerIolOS!='') echo $powerIolOS; else echo "&nbsp;"; ?></td>
										<td width="36" align="left" class="text_9" style="padding-left:1px;"><?php if($holladayOS!='') echo $holladayOS; else echo "&nbsp;"; ?></td>
										<td width="35" align="left" class="text_9" style="padding-left:1px;"><?php if($srk_tOS!='') echo $srk_tOS; else echo "&nbsp;"; ?></td>
										<td width="52" align="left" class="text_9" style="padding-left:1px;"><?php if($hofferOS!='') echo $hofferOS; else echo "&nbsp;"; ?></td>
									</tr>
									<tr height="20">
										<td align="left" class="text_9" width="104"><?php if($iol1OS!='') echo $iol1OS; else echo "&nbsp;"; ?></td>
										<td align="left" style="padding-left:1px;" class="text_9"><?php if($iol1PowerOS!='') echo $iol1PowerOS; else echo "&nbsp;"; ?></td>
										<td align="left" style="padding-left:1px;" class="text_9"><?php if($iol1HolladayOS!='') echo $iol1HolladayOS; else echo "&nbsp;"; ?></td>
										<td align="left" style="padding-left:1px;" class="text_9"><?php if($iol1srk_tOS!='') echo $iol1srk_tOS; else echo "&nbsp;"; ?></td>
										<td align="left" style="padding-left:1px;" class="text_9"><?php if($iol1HofferOS!='') echo $iol1HofferOS; else echo "&nbsp;"; ?></td>
									</tr>
									<tr height="20">
										<td align="left" class="text_9" width="104"><?php if($iol2OS!='') echo $iol2OS; else echo "&nbsp;"; ?></td>
										<td align="left" style="padding-left:1px;" class="text_9"><?php if($iol2PowerOS!='') echo $iol2PowerOS; else echo "&nbsp;"; ?></td>
										<td align="left" style="padding-left:1px;" class="text_9"><?php if($iol2HolladayOS!='') echo $iol2HolladayOS; else echo "&nbsp;"; ?></td>
										<td align="left" style="padding-left:1px;" class="text_9"><?php if($iol2srk_tOS!='') echo $iol2srk_tOS; else echo "&nbsp;"; ?></td>
										<td align="left" style="padding-left:1px;" class="text_9"><?php if($iol2HofferOS!='') echo $iol2HofferOS; else echo "&nbsp;"; ?></td>
									</tr>
									<tr height="20">
										<td align="left" class="text_9" width="104"><?php if($iol3OS!='') echo $iol3OS; else echo "&nbsp;"; ?></td>
										<td align="left" style="padding-left:1px;" class="text_9"><?php if($iol3PowerOS!='') echo $iol3PowerOS; else echo "&nbsp;"; ?></td>
										<td align="left" style="padding-left:1px;" class="text_9"><?php if($iol3HolladayOS!='') echo $iol3HolladayOS; else echo "&nbsp;"; ?></td>
										<td align="left" style="padding-left:1px;" class="text_9"><?php if($iol3srk_tOS!='') echo $iol3srk_tOS; else echo "&nbsp;"; ?></td>
										<td align="left" style="padding-left:1px;" class="text_9"><?php if($iol3HofferOS!='') echo $iol3HofferOS; else echo "&nbsp;"; ?></td>
									</tr>
									<tr height="20">
										<td align="left" class="text_9" width="104"><?php if($iol4OS!='') echo $iol4OS; else echo "&nbsp;"; ?></td>
										<td align="left" style="padding-left:1px;" class="text_9"><?php if($iol4PowerOS!='') echo $iol4PowerOS; else echo "&nbsp;"; ?></td>
										<td align="left" style="padding-left:1px;" class="text_9"><?php if($iol4HolladayOS!='') echo $iol4HolladayOS; else echo "&nbsp;"; ?></td>
										<td align="left" style="padding-left:1px;" class="text_9"><?php if($iol4srk_tOS!='') echo $iol4srk_tOS; else echo "&nbsp;"; ?></td>
										<td align="left" style="padding-left:1px;" class="text_9"><?php if($iol4HofferOS!='') echo $iol4HofferOS; else echo "&nbsp;"; ?></td>
									</tr>
									<tr height="20">
										<td align="left" class="text_10b" width="104">Cell Count</td>
										<td align="left" class="text_9"><?php if($cellCountOS!='') echo $cellCountOS; else echo "&nbsp;"; ?></td>
										<?php
										if($notesOS){
											?>
											<td colspan="3" width="75" rowspan="6" align="left" valign="top" class="text_9"><b>NOTES:&nbsp;</b><?php echo nl2br($notesOS); ?></td>
											<?php
										}
										?>
									</tr>
									<tr height="20">
										<td align="left" class="text_10b" width="104">Pachymetry</td>
										<td align="left" class="text_9">
											<table border="0" cellpadding="0" cellspacing="0">
												<tr>
													<td width="30" align="left" class="text_9"><?php if($pachymetryValOS!='') echo $pachymetryValOS; else echo "&nbsp;"; ?></td>
													<td width="10" class="text_9" align="center">/</td>
													<td align="left" class="text_9"><?php if($pachymetryCorrecOS!='') echo $pachymetryCorrecOS; else echo "&nbsp;"; ?></td>
												</tr>
											</table>
										</td>
									</tr>
									<tr height="20">
										<td align="left" class="text_10b" width="104">Corneal Diam</td>
										<td align="left" class="text_9"><?php if($cornealDiamOS!='') echo $cornealDiamOS; else echo "&nbsp;"; ?></td>
									</tr>
									<tr height="20">
										<td align="left" class="text_10b" width="104">Dominant Eye</td>
										<td align="left" class="text_9"><?php if($dominantEyeOS!='') echo $dominantEyeOS; else echo "&nbsp;"; ?></td>
									</tr>
									<tr height="20">
										<td align="left" class="text_10b" width="104">Pupil Size</td>
										<td align="left" class="text_9">
											<table border="0" cellpadding="0" cellspacing="0">
												<tr>
													<td width="30" align="left" class="text_9"><?php if($pupilSize1OS!='') echo $pupilSize1OS; else echo "&nbsp;"; ?></td>
													<td width="10" class="text_9" align="center">/</td>
													<td width="" align="left" class="text_9"><?php if($pupilSize2OS!='') echo $pupilSize2OS; else echo "&nbsp;"; ?></td>
												</tr>
											</table>
										</td>
									</tr>
									<tr height="5">
										<td colspan="2" align="right" class="text_9">
											<table border="0" cellpadding="0" cellspacing="0">
												<tr>
													<td align="left" class="text_10b" >Un-Dilated</td>
													<td width="3" >/</td>
													<td align="left" class="text_10b" >Dilated</td>
												</tr>
										  </table>
										</td>
									</tr>
									<tr height="20">
										<td colspan="5" valign="top" align="left" class="text_9">&nbsp;</td>
									</tr>
									<tr height="20">
										<td colspan="5" valign="top" align="left" ><b>CC:</b>&nbsp;&nbsp;Scheduled for Intraocular lens implant<br/> A/Scan reviewed and IOL Selected.</td>
									</tr>
									<tr height="20">
										<td colspan="5" valign="top" align="left" >&nbsp;</td>
									</tr>
									<tr>
										<td colspan="5" align="left">
											<table border="0" cellpadding="0" cellspacing="0">
												<tr>
													<td align="left" class="text_10b" >Assessment:</td>
													<td > <?php if($cataractOS==1) echo "Cataract"; ?> </td>
													<td ><?php if($astigmatismOS==1) echo "Astigmatism"; ?> </td>
													<td > <?php if($myopiaOS==1) echo "Myopia"; ?> </td>
												</tr>
										  </table>
										</td>
									</tr>
									<tr height="5">
										<td colspan="5" valign="top" align="left" >&nbsp;</td>
									</tr>
									<tr>
										<td colspan="5" valign="top" align="left" >
											<table border="0" cellpadding="0" cellspacing="0">
												<tr>
													<td  align="left" class="text_10b">Plan:</td>
													<td  style="padding-left:5px;"><?php if($selecedIOLsOS) echo $selecedIOLsOS; else echo "&nbsp;"; ?></td>
												</tr>
											</table>
										</td>
									</tr>
									<tr height="5">
										<td colspan="5" valign="top" align="left" >&nbsp;</td>
									</tr>
									<?php 
										//if(($notesAssesmentPlansOS!='Notes...') && ($notesAssesmentPlansOS!='')){
										?>
										<tr height="15">
											<td colspan="5" align="left">
												<table border="0" cellpadding="0" cellspacing="0">
													<td align="left"  valign="top"><b>Notes:</b></td>
													<td align="left"  style="padding-left:2px;"><?php if($notesAssesmentPlansOS!='') echo nl2br($notesAssesmentPlansOS); else echo "Notes..."; ?></td>
												</table>
											</td>
										</tr>
										<?php
									//}
									?>
									<tr height="20">
										<td colspan="5">
											<table border="0" cellpadding="0" cellspacing="0">
												<tr>
													<td ><?php if($lriOS==1){ echo "LRI"; } ?> </td>
													<td > <?php if($dlOS==1){ echo "DL"; } ?> </td>
													<td > <?php if($synechiolysisOS==1){ echo "Synechiolysis"; } ?> </td>
												</tr>
												<tr>
													<td > <?php if($irishooksOS==1){ echo "IRIS Hooks"; } ?> </td>
													<td > <?php if($trypanblueOS==1){ echo "Trypan Blue"; } ?> </td>
													<td ><?php if($flomaxOS==1){ echo "Pt. On Flomax"; } ?> </td>
												</tr>
											</table>
										</td>
									</tr>
									<tr height="15" <?php if($lriOS!=1){ ?> style="display:none;" <?php } ?>>
										<td colspan="5" align="left">
											<table border="0" cellpadding="0" cellspacing="0">
												<tr>
													<td width="35"  align="left" class="text_10b">Cuts:</td>
													<td width="25" align="left" class="text_9" ><?php echo $cutsOS; ?></td>
													<td width="10"></td>
													<td width="35" align="right" class="text_10b" >Length:</td>
													<td width="35" align="center" class="text_9" ><?php echo $lengthOS."  ".$lengthTypeOS; ?></td>
													<td width="15"></td>
													<td width="35" align="center" class="text_10b" >Axis:</td>
													<td width="35" align="center" class="text_9" ><?php echo $axisOS; ?></td>
												</tr>
										  </table>
										</td>
									</tr>
									<tr height="20" <?php if($cutsOS!=1){ ?> style="display:none;" <?php } ?>>
									  <td colspan="5" align="left">
											<table width="" border="0" cellpadding="0" cellspacing="0">
												<tr>
													<td ><?php if($superiorOS==1) echo "superior"; ?> </td>
													<td ><?php if($inferiorOS==1) echo "inferior"; ?> </td>
													<td ><?php if($nasalOS==1) echo "nasal"; ?></td>
													<td ><?php if($temporalOS==1) echo "temporal"; ?></td>
													<td ><?php if($STOS==1) echo "ST"; ?></td>
													<td ><?php if($SNOS==1) echo "SN"; ?> </td>
												</tr>
												<tr>												
													<td ><?php if($ITOS==1) echo "IT"; ?></td>
													<td ><?php if($INOS==1) echo "IN"; ?> </td>
												</tr>
										  </table>
									  </td>
									</tr>
						  </table>
								
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<?php
		/*
		<tr>
			<td align="left" >
				<table border="0" cellpadding="0" cellspacing="0" bordercolor="#FFFFFF" width="100%">
					<tr>
						<td width="60" align="left" class="text_10b" >Signature:</td>
						<td width="275" align="left" >
							
							<?php

								if(isAppletModified($signature)){
									$table = 'surgical_tbl';
									$idName = 'surgical_id';
									$docSign = 'signature';
									$signImage = '../../images/white.jpg';
									$alt = 'Physician Sign'; 
												 
									 if(getAppletImage($surgical_id,$table,$idName,$docSign,$signImage,$alt,"1")){
										@copy("html2pdfprint/".$gdFilename,"../common/new_html2pdf/".$gdFilename);
										echo "<img src='".$gdFilename."' height='45' width='225'/>";
										$ChartNoteImagesString[]=$gdFilename;					 
									} 
									
								}
							?>	
						</td>
						<td width="75" align="left" >&nbsp;</td>
						<td width="60" align="left" class="text_10b" >Signature:</td>
						<td width="250" align="left" >
							
							<?php
								if(isAppletModified($signature)){

									$table = 'surgical_tbl';
									$idName = 'surgical_id';
									$docSign = 'signatureOS';
									$signImage = '../../images/white.jpg';
									$alt = 'Physician Sign'; 
									if(getAppletImage($surgical_id,$table,$idName,$docSign,$signImage,$alt,"1")){
										@copy("html2pdfprint/".$gdFilename,"../common/new_html2pdf/".$gdFilename);										
										echo "<img src='".$gdFilename."' height='45' width='225'/>";
										$ChartNoteImagesString[]=$gdFilename;					 
									} 
									
								}
							?>	
						</td>
					</tr>
			  </table>
			</td>
		</tr>
		*/
		?>
	</table>

<?php
}
?>