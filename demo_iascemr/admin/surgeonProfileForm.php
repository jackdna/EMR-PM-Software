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
//Surgeon Details
	$getSurgeonDetails = $objManageData->getArrayRecords('users', 'type', 'Surgeon');
//Surgeon Details

$getProceduresStr = "SELECT procedureId, name FROM procedures";
$getProceduresQry = imw_query($getProceduresStr);
while($getProceduresRows = imw_fetch_array($getProceduresQry)){
	$proceduresId = $getProceduresRows['procedureId'];
	$proceduresList[$proceduresId] = $getProceduresRows['name'];
}
?>
<html>
<head>
<title>Surgeon Profile</title>
<script>
function chkFrmFn(type){
	var msg = 'Please fill following fields.\n';
	var flag = 0;
	f1 = document.getElementById('elem_profileName').value;
		f2 = document.getElementById('surgeonsIdList').value;
		if(f2==''){ msg+= '\n� Surgeon Name.'; ++flag; }
	f3 = document.getElementById('elem_operativeReportTemplate').value;
	f4 = document.getElementById('elem_preOpOrders[]').value;
	f5 = document.getElementById('elem_procedures[]').value;
	f6 = document.getElementById('elem_instructions').value;

	if(f1==''){ msg+= '\n� Profile Name.'; ++flag; }
	
	if(f3==''){ msg+= '\n� Operative Template.'; ++flag; }
	if(f4==''){ msg+= '\n� Pre - Op Order.'; ++flag; }
	if(f5==''){ msg+= '\n� Procedures.'; ++flag; }
	if(f6==''){ msg+= '\n� Instructions.'; ++flag; }
	if(flag>0){
		alert(msg)
		return false;
	}
	return true;
}
</script>
</head>
<body>
	<form name="frmSurgeonProfile" action="surgeonprofile.php" target="_parent" method="post" onSubmit="return chkFrmFn('<?php $userType; ?>');">
	<input type="hidden" name="idProfile" value="<?php ?>">
	<input type="hidden" name="frmName" value="frmSurgeonProfile">
	<table width="925" border="0" cellpadding="0" cellspacing="0">
		<tr>
			<td width="1" class=""><img border="0" src="../images/left.gif"></td>
			<td bgcolor="#BCD2B0" align="right"><img border="0" src="../images/chk_off1.gif" onClick="top.frames[0].frames[0].document.getElementById('formTr').style.display = 'none';"></td>
			<td width="1" class=""><img border="0" src="../images/right.gif"></td>
		</tr>
		<tr>
			<td width="1"><img height="150" width="1px" src="../images/line.GIF"></td>
			<td align="center" valign="top">
				<table border="0" cellpadding="0" cellspacing="0">
					<tr>
						<td>
							<ul>
								<li>
								<label class="text_10b" id="title7" style="background:background-color:#F1F4F0;">Profile Name</label>
								<span class="text_10" style="background:background-color:#F1F4F0;padding-left:8;margin:0px 0 0 0px; white-space:nowrap;">
									<input class="text_10 all_border" type="text" name="elem_profileName" size="31" value="<?php echo $elem_profileName; ?>">
								</span>
								</li>
							</ul>
						</td>
						<td>
							<ul>
								<li>
								<label class="text_10b" id="title7" style="background:background-color:#F1F4F0;">Surgeon</label>
								<span class="text_10" style="background:background-color:#F1F4F0;padding-left:8;margin:0px 0 0 0px; white-space:nowrap;">
									<select <?php if($userType=='Surgeon') echo "disabled"; ?> class="text_10 all_border" name="surgeonsIdList" style="width:200px;">
										<option value="">Select</option>
										<?php
										foreach($getSurgeonDetails as $surgeon){
											?>
											<option value="<?php echo $surgeon->usersId; ?>"><?php echo $surgeon->fname.' '.$surgeon->mname.' '.$surgeon->lname; ?></option>
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
								<label class="text_10b" id="title7" style="background:background-color:#F1F4F0;">Operative Report</label>
								<span class="text_10" style="background:background-color:#F1F4F0;padding-left:8;margin:0px 0 0 0px; white-space:nowrap;">
									<select style="width:200px;height:45px;" class="text_10 all_border" name="elem_operativeReportTemplate">
										<option value="">Select</option>
										<?php										
										$preOpTemplates = $objManageData->getArrayRecords('operative_template');
										foreach($preOpTemplates as $templates){
											?>
											<option value="<?php echo $templates->template_name; ?>"><?php echo $templates->template_name; ?></option>
											<?php
										}
										?>
									</select>
								</span>
								</li>
							</ul>
						</td>
					</tr>
					<tr>
						<td>
							<ul>
								<li>
								<label class="text_10b" id="title7" style="background:background-color:#F1F4F0;">Pre-Op order</label>
								<span class="text_10" style="background:background-color:#F1F4F0;padding-left:8;margin:0px 0 0 0px; white-space:nowrap;">
									<select style="width:200px;height:45px;" class="text_10 all_border" name="elem_preOpOrders[]" multiple>
										<option></option>
										<?php
										$preOpOrders = $objManageData->getArrayRecords('preopmedicationorder');
										foreach($preOpOrders as $preOpMediList){
											?>
											<option value="<?php echo $preOpMediList->medicationName; ?>"><?php echo $preOpMediList->medicationName; ?></option>
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
								<label class="text_10b" id="title7" style="background:background-color:#F1F4F0;">Procedures</label>
								<span class="text_10" style="background:background-color:#F1F4F0;padding-left:8;margin:0px 0 0 0px; white-space:nowrap;">
									<select class="text_10 all_border" name="elem_procedures[]" style="height:45px;width:200px;" multiple>
										<option></option>
										<?php
										foreach($proceduresList as $key => $proc){
											?>
											<option value="<?php echo $proc; ?>"><?php echo $proc; ?></option>
											<?php
										}
										?>
									</select>
								</span>
								</li>
							</ul>
						</td>
						<td valign="top">
							<ul>
								<li>
								<label class="text_10b" id="title7" style="background:background-color:#F1F4F0;">Instruction Sheet</label>
								<span class="text_10" style="background:background-color:#F1F4F0;padding-left:8;margin:0px 0 0 0px; white-space:nowrap;">
									<select name="elem_instructions" style="width:200px;height:45px;" class="text_10 all_border">
										<option value=""></option>
										<?php
										$insSheetTemplates = $objManageData->getArrayRecords('instruction_template');
										foreach($insSheetTemplates as $templates){
											?>
											<option value="<?php echo $templates->instruction_name; ?>"><?php echo $templates->instruction_name; ?></option>
											<?php
										}
										?>
									</select>
									<!-- <textarea class="text_10 all_border" name="" rows="2" cols="30"><?php echo $elem_instructions; ?></textarea> -->
								</span>
								</li>
							</ul>
						</td>
					</tr>
				</table>
			</td>
			<td width="1"><img height="150" width="1px" src="../images/line.GIF"></td>
		</tr>
		<tr>
			<td width="1"><img border="0" src="../images/bottomLeft.gif"></td>
			<td align="center" bgcolor="#BCD2B0"><input type="submit" name="elem_submit" value="Save" class="button" style="width:75px;"></td>
			<td width="1" class=""><img border="0" src="../images/bottomRight.gif"></td>
		</tr>
	</table>
</form>
</body>
</html>
