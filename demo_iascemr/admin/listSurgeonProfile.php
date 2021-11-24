<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.

?><?php
include_once("../globalsSurgeryCenter.php");
include_once("logout.php");
include("adminLinkfile.php");
include_once("classObjectFunction.php");
$objManageData = new manageData;
$profileId = $_REQUEST['profile'];
$surgeonId = $_REQUEST['surgeonsList'];
$andProfileDelCond = "  AND del_status ='' ";
// PROCEDURES LIST
$getProceduresStr = "SELECT procedureId, name, catId FROM procedures";
$getProceduresQry = imw_query($getProceduresStr);
while($getProceduresRows = imw_fetch_array($getProceduresQry)){
	$proceduresId = $getProceduresRows['procedureId'];
	$proceduresList[$proceduresId] = $getProceduresRows['name']; 
}
// PROCEDURES LIST
?>
<html>
<head>
<title>Pre Defines</title>
<script>
function changeColor(id){
	document.getElementById('tr'+1).style.background = '#FFFFFF';
	document.getElementById('tr'+2).style.background = '#FFFFFF';	
	document.getElementById('tr'+id).style.background = '#FFFFCC';
}
function CheckAvail(obj){
	if(obj.value==''){
		alert('The Procedure is selected in other profile.')
		var selIndex = obj.selectedIndex;
		obj.options[selIndex].selected = false;	
	}
}
</script>
</head>
<body><br>
<?php
// GETTING PROFILE PROCEDUREs LIST
if($surgeonId){
	$profileDetails = $objManageData->getArrayRecords('surgeonprofile', 'surgeonId', $surgeonId,'','',$andProfileDelCond);
	if(count($profileDetails)>0){
		foreach($profileDetails as $profile){
			if($profileId!=$profile->surgeonProfileId){
				$proceduresListing = $profile->procedures;
				$proceduresListArrOld[] = explode(",", $proceduresListing);
			}
		}
		if(count($proceduresListArrOld)>0){
			foreach($proceduresListArrOld as $oldValArr){
				foreach($oldValArr as $values){
					$proceduresListArr[] = trim($values);
				}	
			}	
		}
	}
}
// GETTING PROFILE PROCEDUREs LIST
if($surgeonId){
	if($profileId){
		$profile_details = $objManageData->getRowRecord('surgeonprofile', 'surgeonProfileId', $profileId,'','','',$andProfileDelCond);
		$elem_profileName = $profile_details->profileName;
		$procedures = $profile_details->procedures;
			$proceduresArr = explode(", ", $procedures);
		$preOpOrders = $profile_details->preOpOrders;
			$preOpOrderArr = explode(", ", $preOpOrders);
		$opRepTemplate = $profile_details->operativeReportTemplate;
		$surgeonId = $profile_details->surgeonId;
		$insSheet = $profile_details->instructionSheet;	
		$defaultProfile = $profile_details->defaultProfile;
	}
	?>	
	<form name="frmSurgeonProfile" action="surgeonprofile.php" target="_parent" method="post">
	<input type="hidden" name="profile" value="<?php echo $profileId; ?>">
	<input type="hidden" name="frmName" value="frmSurgeonProfile">
	<input type="hidden" name="surgeonId" value="<?php echo $surgeonId; ?>">
	<input type="hidden" name="sbtSaveProfile" value="">
	<input type="hidden" name="sbtDelProfile" value="">	
	<table width="891" border="0" cellpadding="0" cellspacing="0" id="formTr">
		<tr>
			<td width="1" class=""><img border="0" src="../images/left.gif"></td>
			<td bgcolor="#BCD2B0" align="right" class="text_10"><input <?php if($defaultProfile==1) echo "CHECKED"; ?> type="checkbox" value="yes" name="defaultProfile">Default Profile</td>
			<td width="1" class=""><img border="0" src="../images/right.gif"></td>
		</tr>
		<tr>
			<td width="1"><img height="362" width="1px" src="../images/line.GIF"></td>
			<td align="left" valign="top">
				<table border="0" cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td>
							<ul>
								<li>
									<label class="text_10b" id="title7" style="background:background-color:#F1F4F0;">Profile Name</label>
								</li>
							</ul>
						</td>					
						<td>
							<ul>
								<li>
									<label class="text_10b" id="title7" style="background:background-color:#F1F4F0;">Operative Report</label>
								</li>
							</ul>
						</td>					
						<td>
							<ul>
								<li>
									<label class="text_10b" id="title7" style="background:background-color:#F1F4F0;">Instruction Sheet</label>
								</li>
							</ul>
						</td>					
					</tr>
					<tr align="left" valign="top" id="tr1" bgcolor="#FFFFFF">
						<td>
							<ul>
								<li>
									<span class="text_10" style="background:background-color:#F1F4F0;padding-left:8;margin:0px 0 0 0px; white-space:nowrap;">
										<input class="text_10 all_border" type="text" name="elem_profileName" size="33" value="<?php echo $elem_profileName; ?>" onFocus="return changeColor('1')">
									</span>
								</li>
							</ul>
						</td>					
						<td>
							<ul>
								<li>
									<span class="text_10" style="background:background-color:#F1F4F0;padding-left:8;margin:0px 0 0 0px; white-space:nowrap;">
										<select style="width:250px;height:45px;" class="text_10 all_border" name="elem_operativeReportTemplate" onFocus="return changeColor('1')">
											<option value="">Select</option>
											<?php										
											$preOpTemplates = $objManageData->getArrayRecords('operative_template');
											foreach($preOpTemplates as $templates){
												?>
												<option value="<?php echo $templates->template_name; ?>" <?php if($opRepTemplate==$templates->template_name) echo "SELECTED"; ?>><?php echo $templates->template_name; ?></option>
												<?php
											}
											?>
										</select>
									</span>
								</li>
							</ul>
						</td>					
						<td>
							<ul>
								<li>
									<span class="text_10" style="background:background-color:#F1F4F0;padding-left:8;margin:0px 0 0 0px; white-space:nowrap;">
										<select name="elem_instructions" style="width:250px;height:45px;" class="text_10 all_border" onFocus="return changeColor('1')">
											<option value="">Select</option>
											<?php
											$insSheetTemplates = $objManageData->getArrayRecords('instruction_template');
											foreach($insSheetTemplates as $templates){
												?>
												<option value="<?php echo $templates->instruction_name; ?>" <?php if($insSheet==$templates->instruction_name) echo "SELECTED"; ?>><?php echo $templates->instruction_name; ?></option>
												<?php
											}
											?>
										</select>
									</span>
								</li>
							</ul>
						</td>					
					</tr>
					<tr height="20">
						<td colspan="3"></td>
					</tr>
					<tr id="tr2" bgcolor="#FFFFFF">
						<td colspan="3" align="left" valign="top">
							<table border="0" cellpadding="0" cellspacing="0">
								<tr valign="top">
									<td width="425">
										<ul>
											<li>
												<label class="text_10b" id="title7" style="background:background-color:#F1F4F0;">Procedures</label>
												<span class="text_10" style="background:background-color:#F1F4F0;padding-left:8;margin:0px 0 0 0px; white-space:nowrap;">
													<select class="text_10 all_border" id="elem_proceduresId" name="elem_procedures[]" style="height:250px;width:400px;" multiple onFocus="return changeColor('2')" onChange="return CheckAvail(this);">
														<option></option>
														<?php
														foreach($proceduresList as $key => $proc){
															$procValue = trim($proc);
															if(count($proceduresListArr)>0){
																if(in_array($proc, $proceduresListArr)){
																	$procValue = '';
																}
															}
															?>
															<option value="<?php echo $procValue; ?>" <?php if($profileId){ if(in_array($proc, $proceduresArr)) echo "SELECTED"; } ?>><?php echo $proc; ?></option>
															<?php
														}
														?>
													</select>
												</span>
											</li>
										</ul>
									</td>
									<td width="300">
										<ul>
											<li>
											<label class="text_10b" id="title7" style="background:background-color:#F1F4F0;">Pre-Op order</label>
											<span class="text_10" style="background:background-color:#F1F4F0;padding-left:8;margin:0px 0 0 0px; white-space:nowrap;">
												<select style="width:400px;height:250px;" class="text_10 all_border" id="elem_preOpOrders_id" name="elem_preOpOrders[]" multiple onFocus="return changeColor('2')">
													<option></option>
													<?php
													$preOpOrders = $objManageData->getArrayRecords('preopmedicationorder');
													foreach($preOpOrders as $preOpMediList){
														?>
														<option value="<?php echo $preOpMediList->medicationName; ?>" <?php if($profileId){ if(in_array($preOpMediList->medicationName, $preOpOrderArr)) echo "SELECTED"; } ?>><?php echo $preOpMediList->medicationName; ?></option>
														<?php
													}
													?>
												</select>							
											</span>
											</li>
										</ul>
									</td>
								</tr>
							</table>
						</td>
					</tr>
			  </table>			
			</td>
			<td width="1"><img height="362" width="1px" src="../images/line.GIF"></td>
		</tr>
		<tr>
			<td width="1"><img border="0" src="../images/bottomLeft.gif"></td>
			<td align="center" bgcolor="#BCD2B0">
				<!-- <input type="submit" name="" class="button" style="width:150px;" value="<?php if($profileId) echo "Update"; else echo "Save"; ?>"> -->
				<!-- <input type="submit" name="" class="button" style="width:150px;" value="Delete"> -->
			</td>
			<td width="1" class=""><img border="0" src="../images/bottomRight.gif"></td>
		</tr>
	</table>
	</form>
	<?php
}
?>
</body>
</html>
