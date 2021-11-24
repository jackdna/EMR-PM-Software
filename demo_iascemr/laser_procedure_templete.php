<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php 

include_once("../globalsSurgeryCenter.php");
include_once("logout.php"); 
include("adminLinkfile.php");
include_once("classObjectFunction.php");
$objManageData = new manageData;
$laser_ProcedureId = $_REQUEST['laser_ProcedureId'];
$templeteID_show=$_REQUEST['templeteID_show'];
include("../common/lasersurgeon_pop_admin.php");
include("../common/laserchief_complaint_pop_admin.php");
include("../common/laserhx_illness_admin_pop.php");
include("../common/laserpast_med_hx_pop_admin.php");
include("../common/lasermedication_pop_admin.php");
include("../common/lasersle_pop_admin.php");
include("../common/laserfundus_exam_pop_admin.php");
include("../common/lasermental_state_pop_admin.php");
include("../common/laserspot_size_pop_admin.php");
include("../common/laserpower_pop_admin.php");
include("../common/laserexposure_pop_admin.php");
include("../common/lasercount_pop_admin.php");
include("../common/laseranesthesia_pop_admin.php");
include("../common/laserpost_progress_pop_admin.php");
include("../common/laserpost_operative_pop_admin.php");
//Insert and edit
if($_REQUEST['save_id']){
	unset($arrayRecord);
	$laserprocedure_surgeonId=implode(",",$_REQUEST['laserprocedure_surgeonId']);
	$arrayRecord['laser_procedureID'] = addslashes($_REQUEST['laser_ProcedureId']);
	$arrayRecord['template_name'] = addslashes($_REQUEST['template_name']);
	$arrayRecord['laser_surgeonID'] = $laserprocedure_surgeonId;
	$arrayRecord['laser_chief_complaint'] = addslashes($_REQUEST['laser_Procedurechief_complaint']);
	$arrayRecord['laser_present_illness_hx'] = addslashes($_REQUEST['laser_Procedurehx_illness']);
	$arrayRecord['laser_past_med_hx'] = addslashes($_REQUEST['laser_Procedurepast_med_hx']);
	$arrayRecord['laser_medication'] = addslashes($_REQUEST['laser_Proceduremedication']);
	$arrayRecord['laser_sle'] = addslashes($_REQUEST['laser_Proceduresle']);
	$arrayRecord['laser_fundus_exam'] = addslashes($_REQUEST['laser_Procedurefundus_exam']);
	$arrayRecord['laser_mental_state'] = addslashes($_REQUEST['laser_Proceduremental_state']);
	$arrayRecord['laser_spot_size'] = addslashes($_REQUEST['laser_Procedurespot_size']);
	$arrayRecord['laser_power'] = addslashes($_REQUEST['laser_Procedurepower']);
	$arrayRecord['laser_exposure'] = addslashes($_REQUEST['laser_Procedureexposure']);
	$arrayRecord['laser_count'] = addslashes($_REQUEST['laser_Procedurecount']);
	$arrayRecord['laser_anesthesia'] = addslashes($_REQUEST['laser_Procedureanesthesia']);
	$arrayRecord['laser_post_progress'] = addslashes($_REQUEST['laser_Procedurepost_progress']);
	$arrayRecord['laser_post_operative'] = addslashes($_REQUEST['laser_Procedurepost_operative']);
	//echo "sd". $templeteID_show;
	if($templeteID_show){
		$objManageData->updateRecords($arrayRecord, 'laser_procedure_template', 'laser_templateID', $templeteID_show);
	}
	else
	{
		$objManageData->addRecords($arrayRecord, 'laser_procedure_template ');
	}
}
//DELETE SELECTED TEMPLATE
	if($_POST['chkBox']){
		$delChkBoxes = $_POST['chkBox'];
		if(is_array($delChkBoxes)){
			foreach($delChkBoxes as $del_laser_templeteId){
				$objManageData->delRecord('laser_procedure_template', 'laser_templateID', $del_laser_templeteId);
			}
		}
	}
$laserDetails = $objManageData->getRowRecord('laser_procedure_template', 'laser_templateID', $templeteID_show);

?>

<html>
<head>
<title>Laser Procedure Templete</title>
<script>
	function move_templete(val){
				
				location.href='laser_procedure_templete.php?laser_ProcedureId='+val;
	}
	top.frames[0].document.frameSrc.source.value = 'laser_procedure_admin.php';	
	top.frames[0].document.getElementById('saveButton').style.display = 'block';
	top.frames[0].document.getElementById('deleteSelected').style.display = 'block';
	top.frames[0].document.getElementById('cancelButton').style.display = 'block';	
</script>
<script language="javascript">
var preDefineCloseOut;
function preDefineOpenCloseFun() {
	document.getElementById("hiddPreDefineId").value = "preDefineOpenYes";
}
function preCloseFun(Id) {
	if(document.getElementById("hiddPreDefineId")) {
		if(document.getElementById("hiddPreDefineId").value=="preDefineOpenYes") {
			if(document.getElementById(Id)) {
				if(document.getElementById(Id).style.display == "block"){
					document.getElementById(Id).style.display = "none"; 
					//document.getElementById("hiddPreDefineId").value = "";
				}
			}
			if(top.frames[0].frames[0].document.getElementById(Id)) {
				if(top.frames[0].frames[0].document.getElementById(Id).style.display == "block"){
					top.frames[0].frames[0].document.getElementById(Id).style.display = "none"; 
					//top.frames[0].frames[0].document.getElementById("hiddPreDefineId").value = "";
				}
			}
		}
		
	}
}
function showsurgeon(name1, name2, c, posLeft, posTop){	
//	alert(top.frames[0].frames[0].document.getElementById("evaluationchief_complaint_div"));
	top.frames[0].frames[0].frames[0].document.getElementById("evaluationsurgeon_div").style.display = 'block';
	top.frames[0].frames[0].frames[0].document.getElementById("evaluationsurgeon_div").style.left = posLeft;
	top.frames[0].frames[0].frames[0].document.getElementById("evaluationsurgeon_div").style.top = posTop;
	document.getElementById("divId").value = name1;
	document.getElementById("counter").value = c;
	document.getElementById("secondaryValues").value = name2;	
	//CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
		if(document.getElementById("hiddPreDefineId")) {
			document.getElementById("hiddPreDefineId").value = "";
			preDefineCloseOut = setTimeout("preDefineOpenCloseFun()", 100);
		}
	//END CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE) 
}
function showChiefComplaintAdminFn(name1, name2, c, posLeft, posTop){	
//	alert(top.frames[0].frames[0].document.getElementById("evaluationchief_complaint_div"));
	top.frames[0].frames[0].frames[0].document.getElementById("evaluationchief_complaint_div").style.display = 'block';
	top.frames[0].frames[0].frames[0].document.getElementById("evaluationchief_complaint_div").style.left = posLeft;
	top.frames[0].frames[0].frames[0].document.getElementById("evaluationchief_complaint_div").style.top = posTop;
	document.getElementById("divId").value = name1;
	document.getElementById("counter").value = c;
	document.getElementById("secondaryValues").value = name2;	
	//CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
		if(document.getElementById("hiddPreDefineId")) {
			document.getElementById("hiddPreDefineId").value = "";
			preDefineCloseOut = setTimeout("preDefineOpenCloseFun()", 100);
		}
	//END CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE) 
}
function showhx_illnessAdminFn(name1, name2, c, posLeft, posTop){	
	//alert(top.frames[0].frames[0].document.getElementById("evaluationhx_illness_div"));
	top.frames[0].frames[0].frames[0].document.getElementById("evaluationhx_illness_div").style.display = 'block';
	top.frames[0].frames[0].frames[0].document.getElementById("evaluationhx_illness_div").style.left = posLeft;
	top.frames[0].frames[0].frames[0].document.getElementById("evaluationhx_illness_div").style.top = posTop;
	document.getElementById("divId").value = name1;
	document.getElementById("counter").value = c;
	document.getElementById("secondaryValues").value = name2;	
	//CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
		if(document.getElementById("hiddPreDefineId")) {
			document.getElementById("hiddPreDefineId").value = "";
			preDefineCloseOut = setTimeout("preDefineOpenCloseFun()", 100);
		}
	//END CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE) 
}
function showpast_med_hx(name1, name2, c, posLeft, posTop){	
	top.frames[0].frames[0].frames[0].document.getElementById("evaluationpast_med_hx_div").style.display = 'block';
	top.frames[0].frames[0].frames[0].document.getElementById("evaluationpast_med_hx_div").style.left = posLeft;
	top.frames[0].frames[0].frames[0].document.getElementById("evaluationpast_med_hx_div").style.top = posTop;
	document.getElementById("divId").value = name1;







	document.getElementById("counter").value = c;
	document.getElementById("secondaryValues").value = name2;	
	//CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
		if(document.getElementById("hiddPreDefineId")) {
			document.getElementById("hiddPreDefineId").value = "";
			preDefineCloseOut = setTimeout("preDefineOpenCloseFun()", 100);
		}
	//END CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE) 
}
function showmedication(name1, name2, c, posLeft, posTop){	
	top.frames[0].frames[0].frames[0].document.getElementById("evaluationmedication_div_admin").style.display = 'block';
	top.frames[0].frames[0].frames[0].document.getElementById("evaluationmedication_div_admin").style.left = posLeft;
	top.frames[0].frames[0].frames[0].document.getElementById("evaluationmedication_div_admin").style.top = posTop;
	document.getElementById("divId").value = name1;
	document.getElementById("counter").value = c;
	document.getElementById("secondaryValues").value = name2;	
	//CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
		if(document.getElementById("hiddPreDefineId")) {
			document.getElementById("hiddPreDefineId").value = "";
			preDefineCloseOut = setTimeout("preDefineOpenCloseFun()", 100);
		}
	//END CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE) 
}
function showsle(name1, name2, c, posLeft, posTop){	
	top.frames[0].frames[0].frames[0].document.getElementById("evaluationsle_div_admin").style.display = 'block';
	top.frames[0].frames[0].frames[0].document.getElementById("evaluationsle_div_admin").style.left = posLeft;
	top.frames[0].frames[0].frames[0].document.getElementById("evaluationsle_div_admin").style.top = posTop;
	document.getElementById("divId").value = name1;
	document.getElementById("counter").value = c;
	document.getElementById("secondaryValues").value = name2;	
	//CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
		if(document.getElementById("hiddPreDefineId")) {
			document.getElementById("hiddPreDefineId").value = "";
			preDefineCloseOut = setTimeout("preDefineOpenCloseFun()", 100);
		}
	//END CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE) 
}
function showfundus_exam(name1, name2, c, posLeft, posTop){	
	top.frames[0].frames[0].frames[0].document.getElementById("evaluationfundus_exam_div_admin").style.display = 'block';
	top.frames[0].frames[0].frames[0].document.getElementById("evaluationfundus_exam_div_admin").style.left = posLeft;
	top.frames[0].frames[0].frames[0].document.getElementById("evaluationfundus_exam_div_admin").style.top = posTop;
	document.getElementById("divId").value = name1;
	document.getElementById("counter").value = c;
	document.getElementById("secondaryValues").value = name2;	
	//CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
		if(document.getElementById("hiddPreDefineId")) {
			document.getElementById("hiddPreDefineId").value = "";
			preDefineCloseOut = setTimeout("preDefineOpenCloseFun()", 100);
		}
	//END CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE) 
}
function showmental_state(name1, name2, c, posLeft, posTop){	
	top.frames[0].frames[0].frames[0].document.getElementById("evaluationmental_state_div_admin").style.display = 'block';
	top.frames[0].frames[0].frames[0].document.getElementById("evaluationmental_state_div_admin").style.left = posLeft;
	top.frames[0].frames[0].frames[0].document.getElementById("evaluationmental_state_div_admin").style.top = posTop;
	document.getElementById("divId").value = name1;
	document.getElementById("counter").value = c;
	document.getElementById("secondaryValues").value = name2;	
	//CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
		if(document.getElementById("hiddPreDefineId")) {
			document.getElementById("hiddPreDefineId").value = "";
			preDefineCloseOut = setTimeout("preDefineOpenCloseFun()", 100);
		}
	//END CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE) 
}
function showspot_size(name1, name2, c, posLeft, posTop){	
	top.frames[0].frames[0].frames[0].document.getElementById("evaluationspot_size_div_admin").style.display = 'block';
	top.frames[0].frames[0].frames[0].document.getElementById("evaluationspot_size_div_admin").style.left = posLeft;
	top.frames[0].frames[0].frames[0].document.getElementById("evaluationspot_size_div_admin").style.top = posTop;
	document.getElementById("divId").value = name1;
	document.getElementById("counter").value = c;
	document.getElementById("secondaryValues").value = name2;	
	//CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
		if(document.getElementById("hiddPreDefineId")) {
			document.getElementById("hiddPreDefineId").value = "";
			preDefineCloseOut = setTimeout("preDefineOpenCloseFun()", 100);
		}
	//END CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE) 
}
function showpower(name1, name2, c, posLeft, posTop){	
	top.frames[0].frames[0].frames[0].document.getElementById("evaluationpower_div_admin").style.display = 'block';
	top.frames[0].frames[0].frames[0].document.getElementById("evaluationpower_div_admin").style.left = posLeft;
	top.frames[0].frames[0].frames[0].document.getElementById("evaluationpower_div_admin").style.top = posTop;
	document.getElementById("divId").value = name1;
	document.getElementById("counter").value = c;
	document.getElementById("secondaryValues").value = name2;	
	//CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
		if(document.getElementById("hiddPreDefineId")) {
			document.getElementById("hiddPreDefineId").value = "";
			preDefineCloseOut = setTimeout("preDefineOpenCloseFun()", 100);
		}
	//END CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE) 
}
function showexposure(name1, name2, c, posLeft, posTop){	
	top.frames[0].frames[0].frames[0].document.getElementById("evaluationexposure_div_admin").style.display = 'block';
	top.frames[0].frames[0].frames[0].document.getElementById("evaluationexposure_div_admin").style.left = posLeft;
	top.frames[0].frames[0].frames[0].document.getElementById("evaluationexposure_div_admin").style.top = posTop;
	document.getElementById("divId").value = name1;
	document.getElementById("counter").value = c;
	document.getElementById("secondaryValues").value = name2;	
	//CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
		if(document.getElementById("hiddPreDefineId")) {
			document.getElementById("hiddPreDefineId").value = "";
			preDefineCloseOut = setTimeout("preDefineOpenCloseFun()", 100);
		}
	//END CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE) 
}
function showcount(name1, name2, c, posLeft, posTop){	
	top.frames[0].frames[0].frames[0].document.getElementById("evaluationcount_div_admin").style.display = 'block';
	top.frames[0].frames[0].frames[0].document.getElementById("evaluationcount_div_admin").style.left = posLeft;
	top.frames[0].frames[0].frames[0].document.getElementById("evaluationcount_div_admin").style.top = posTop;
	document.getElementById("divId").value = name1;
	document.getElementById("counter").value = c;
	document.getElementById("secondaryValues").value = name2;	
	//CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
		if(document.getElementById("hiddPreDefineId")) {
			document.getElementById("hiddPreDefineId").value = "";
			preDefineCloseOut = setTimeout("preDefineOpenCloseFun()", 100);
		}
	//END CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE) 
}
function showanesthesia(name1, name2, c, posLeft, posTop){	
	top.frames[0].frames[0].frames[0].document.getElementById("evaluationanesthesia_div_admin").style.display = 'block';
	top.frames[0].frames[0].frames[0].document.getElementById("evaluationanesthesia_div_admin").style.left = posLeft;
	top.frames[0].frames[0].frames[0].document.getElementById("evaluationanesthesia_div_admin").style.top = posTop;
	document.getElementById("divId").value = name1;
	document.getElementById("counter").value = c;
	document.getElementById("secondaryValues").value = name2;	
	//CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
		if(document.getElementById("hiddPreDefineId")) {
			document.getElementById("hiddPreDefineId").value = "";
			preDefineCloseOut = setTimeout("preDefineOpenCloseFun()", 100);
		}
	//END CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE) 
} 
function showpost_progress(name1, name2, c, posLeft, posTop){	
	top.frames[0].frames[0].frames[0].document.getElementById("evaluationpost_progress_div_admin").style.display = 'block';
	top.frames[0].frames[0].frames[0].document.getElementById("evaluationpost_progress_div_admin").style.left = posLeft;
	top.frames[0].frames[0].frames[0].document.getElementById("evaluationpost_progress_div_admin").style.top = posTop;
	document.getElementById("divId").value = name1;
	document.getElementById("counter").value = c;
	document.getElementById("secondaryValues").value = name2;	
	//CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
		if(document.getElementById("hiddPreDefineId")) {
			document.getElementById("hiddPreDefineId").value = "";
			preDefineCloseOut = setTimeout("preDefineOpenCloseFun()", 100);
		}
	//END CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE) 
} 
function showpost_operative(name1, name2, c, posLeft, posTop){	
	top.frames[0].frames[0].frames[0].document.getElementById("evaluationpost_operative_div_admin").style.display = 'block';
	top.frames[0].frames[0].frames[0].document.getElementById("evaluationpost_operative_div_admin").style.left = posLeft;
	top.frames[0].frames[0].frames[0].document.getElementById("evaluationpost_operative_div_admin").style.top = posTop;
	document.getElementById("divId").value = name1;
	document.getElementById("counter").value = c;
	document.getElementById("secondaryValues").value = name2;	
	//CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
		if(document.getElementById("hiddPreDefineId")) {
			document.getElementById("hiddPreDefineId").value = "";
			preDefineCloseOut = setTimeout("preDefineOpenCloseFun()", 100);
		}
	//END CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE) 
}

</script>
</head>
<body >
<form name="frmlaserprocedure_templete" action="laser_procedure_templete.php?templeteID_show=<?php echo $templeteID_show;?>" method="post">
	<div id="div" style="height:980; overflow:auto; overflow-x:hidden;">
		<input type="hidden" id="selected_frame_name_id" name="selected_frame_name" value="">
		<input type="hidden" name="templateid" value="<?php echo $templateId; ?>">
		<input type="hidden" name="sbtTemplate" value="">	
		<input type="hidden" name="divId">
		<input type="hidden" name="counter">
		<input type="hidden" name="secondaryValues">
		<input type="hidden" name="hiddPreDefineId" id="hiddPreDefineId">
		<input type="hidden" name="save_id" value="">
		<span id="selectedLaserProcedureNameId"  class="text_10b blue_txt" style="padding-left:8;white-space:nowrap; ">
		<?php
			if($_REQUEST['laser_ProcedureId']) {
				$laserProcedureNameDetails = $objManageData->getArrayRecords('procedures', 'procedureId', $_REQUEST['laser_ProcedureId']);
				foreach($laserProcedureNameDetails as $laserProcedureMainName){
					echo "&nbsp;&nbsp;".ucfirst($laserProcedureMainName->name); 
				}
													
												}
											?>							
		</span>
		<table align="left" cellpadding="0" cellspacing="0" bgcolor="#ECF1EA"  border="0" width="920" onClick="preCloseFun('evaluationsurgeon_div');preCloseFun('evaluationchief_complaint_div');preCloseFun('evaluationhx_illness_div');preCloseFun('evaluationpast_med_hx_div');preCloseFun('evaluationmedication_div_admin');preCloseFun('evaluationsle_div_admin');preCloseFun('evaluationfundus_exam_div_admin');preCloseFun('evaluationmental_state_div_admin');preCloseFun('evaluationspot_size_div_admin');preCloseFun('evaluationpower_div_admin');preCloseFun('evaluationexposure_div_admin');preCloseFun('evaluationcount_div_admin');preCloseFun('evaluationanesthesia_div_admin');preCloseFun('evaluationpost_progress_div_admin');preCloseFun('evaluationpost_operative_div_admin');">
			<tr><td colspan="2" height="5"></td></tr>
			<tr>
			<!--display templetes for laser procedure selected-->
				<td width="250" valign="top">
					<table border="0" cellpadding="0" cellspacing="0" width="250">
						<tr height="25">
							<td colspan="2" align="left" class="text_10b" style="font-size:14px;">
								<table border="0" cellpadding="0" cellspacing="0" width="250">
									<tr>
										<td align="right"><img src="../images/left_new.gif" width="3" height="24"></td>
										<td width="225" align="left" valign="middle" bgcolor="#c0aa1e" class="text_10b" style="padding-left:10px;">Laser Procedure Templates </td>
										<td align="left" valign="top"><img src="../images/right_new.gif" width="3" height="24"></td>
									</tr>
									<tr>
										<td></td>
										<td width="225" align="center" valign="middle">
											<table width="236" height="21" border="0" cellpadding="0" cellspacing="0">
												<?php
												
														$laser_procedure_templete=addslashes($_REQUEST['laser_ProcedureId']);
														$sel_laser_templete="select * from laser_procedure_template where laser_procedureID='$laser_procedure_templete'";
														$res_sel_laser_templete=imw_query($sel_laser_templete)or die("not selected".imw_error());
														while($result_res_sel_laser_templete=imw_fetch_array($res_sel_laser_templete))
														{
												?>
															<tr height="20" bgcolor="<?php if(($seq%2)!=0) echo '#FFFFFF'; ?>">
																<td class="text_10" width="10">
																	<input type="checkbox" name="chkBox[]" value="<?php echo $result_res_sel_laser_templete['laser_templateID']; ?>">
																</td>
																<td class="text_10" style="padding-left:10px;">
																	<a href="laser_procedure_templete.php?templeteID_show=<?php echo $result_res_sel_laser_templete['laser_templateID'];?>&laser_ProcedureId=<?php echo $laser_ProcedureId; ?>" class="black"><?php echo stripslashes(ucfirst($result_res_sel_laser_templete['template_name'])); ?></a>
																</td>
															</tr>
													<?php }
													?>	
														
										  </table>
										</td>
										<td></td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
				</td>
	<!--display templetes form -->
			   <td align="center" valign="top" width="670px">&nbsp;</td>
	<!--end display templetes form -->
				<td>
					<table border="0" align="center" cellpadding="0" cellspacing="0" width="100%">
						<tr>
						  <!--laser procedure drop down-->
						  <td  class="text_10b" align="right" nowrap="nowrap">Select Laser Procedure:</td>
						  <td  style="padding-left:3px;"  align="left" class="text_10b"><select name="laser_ProcedureId" class="text_10" style="width:200px;" onChange="javascript:move_templete(this.value);">
							  <option value="">Select Laser Procedure</option>
							  <?php
													$category_laser_select='2';
													$laserprocedure_Select = $objManageData->getArrayRecords('procedures','catId' ,$category_laser_select,'name','ASC');
													if($laserprocedure_Select) {
														foreach($laserprocedure_Select as $laser_procedureDetail){
															
													?>
							  <option value="<?php echo $laser_procedureDetail->procedureId; ?>" <?php if($laser_procedureDetail->procedureId==$laser_ProcedureId) { echo "Selected";} else {}?>><?php echo ucfirst($laser_procedureDetail->name);?></option>
							  <?php
															}
														}
													
													?>
							</select>
						  </td>
						  <!--End laser procedure drop down-->
						  <!-- Template name-->
						  <td width="150" class="text_10b" align="right" nowrap="nowrap">Template Name:</td>
						  <td width="8"></td>
						  <td width="250" align="left" class="text_10b"><input type="text" class="text_10" name="template_name" value="<?php echo stripslashes(ucfirst($laserDetails->template_name));?>" size="15">
						  </td>
						</tr>
						<!--End Template name-->
						<tr>
						  <td height="2px"></td>
						</tr>
						<!--Select surgeon-->
						<tr >
						  <td colspan="5"><table width="100%" border="0" cellpadding="0" cellspacing="0">
							  <tr>
								<td width="0" class=""><img border="0" src="../images/left_new.gif"></td>
								<td  class="text_10bAdmin all_border" width="100%"><table border="0" cellpadding="0" cellspacing="0" width="100%">
									<tr height="22">
									  <td   class="text_10b" bgcolor="#c0aa1e">Surgeon</td>
									  <td  align="left" class="text_10b" bgcolor="#c0aa1e"><!-- Select by default --></td>
									</tr>
								</table></td>
								<td width="1" class=""><img border="0" src="../images/right_new.gif"></td>
							  </tr>
						  </table></td>
						</tr>
						
						<tr>
						  <td colspan="5">
						  	<table  border="0" width="100%" cellpadding="0" cellspacing="0">
							  <tr >
								<td width="32%"  nowrap colspan="4" align="left" valign="middle" class="text_10b pad_top_bottom"><img src="../images/tpixel.gif" width="4" height="1">Select Surgeon <img src="../images/tpixel.gif" width="8" height="8"></td>
								<td width="68%" align="left" style="padding-left:15px; border:hidden;">
									<?php
									$select_surID=explode(",",$laserDetails->laser_surgeonID);
									//echo $select_surID;
									?>
									
									<select name="laserprocedure_surgeonId[]" class="text_10" style="width:250px; border:hidden;" multiple="multiple" size="3">
										<option value="all" <?php if($laserDetails->laser_surgeonID=="all") { echo "selected";}?>>Select All Surgeon</option>
											<?php
											//echo $laserDetails->laser_surgeonID;
											$userSurgeonsDetails = $objManageData->getArrayRecords('users', 'user_type', 'Surgeon','lname','ASC');
											if($userSurgeonsDetails) {
												foreach($userSurgeonsDetails as $surgeon){
													
													?>
														<option value="<?php echo $surgeon->usersId; ?>" <?php if(in_array($surgeon->usersId,$select_surID)) echo "SELECTED"; ?>><?php echo $surgeon->lname.', '.$surgeon->fname; ?></option>
													<?php
												}
											}	
											?>
										</select> 
								</td>
							  </tr>
						  	</table>
							</td>
						</tr>
						<!--End Select surgeon-->
						<!--History-->
						<tr >
						  <td colspan="5"><table width="100%" border="0" cellpadding="0" cellspacing="0">
							  <tr>
								<td width="0" class=""><img border="0" src="../images/left_new.gif"></td>
								<td  class="text_10bAdmin all_border" width="100%"><table border="0" cellpadding="0" cellspacing="0" width="100%">
									<tr height="22">
									  <td   class="text_10b" bgcolor="#c0aa1e">History</td>
									  <td  align="left" class="text_10b" bgcolor="#c0aa1e"><!-- Select by default --></td>
									</tr>
								</table></td>
								<td width="1" class=""><img border="0" src="../images/right_new.gif"></td>
							  </tr>
						  </table></td>
						</tr>
						<tr height="22">
						  <td colspan="5"><table bgcolor="#FFFFFF"  border="0" width="100%" cellpadding="0" cellspacing="0">
							  <tr>
								<td width="32%" nowrap colspan="4" align="left" valign="middle" class="text_10b pad_top_bottom"><span style="color:#800080;cursor:hand; " onClick="return showChiefComplaintAdminFn('txt_areachief_complaint_admin', '', 'no', '20', '70'),document.getElementById('selected_frame_name_id').value='';"><img src="../images/tpixel.gif" width="4" height="1">Chief Complaint <img src="../images/tpixel.gif" width="4" height="1"></span></td>
								<td width="68%" class="text_10" align="left" style="padding-left:18px;"><textarea name="laser_Procedurechief_complaint" id="txt_areachief_complaint_admin" class="field textarea justi text_10" style="border:1px solid #cccccc; width:250px; " rows="4" cols="100" tabindex="6"><?php echo stripslashes(ucfirst($laserDetails->laser_chief_complaint));?></textarea>
								</td>
							  </tr>
						  </table></td>
						</tr>
						<tr height="22">
						  <td colspan="5"><table  border="0" width="100%" cellpadding="0" cellspacing="0">
							  <tr>
								<td width="32%" nowrap colspan="4" align="left" valign="middle" class="text_10b pad_top_bottom"><span  style="color:#800080;cursor:hand; " onClick="return showhx_illnessAdminFn('txt_areahx_illness', '', 'no', '20', '120'),document.getElementById('selected_frame_name_id').value='';" ><img src="../images/tpixel.gif" width="4" height="1">Hx. of Present Illness <img src="../images/tpixel.gif" width="4" height="1"></span></td>
								<td width="68%" class="text_10" align="left" style="padding-left:18px;"><textarea name="laser_Procedurehx_illness" id="txt_areahx_illness" class="field textarea justi text_10" style="border:1px solid #cccccc; width:250px; " rows="4" cols="100" tabindex="6"><?php echo stripslashes(ucfirst($laserDetails->laser_present_illness_hx)); ?></textarea>
								</td>
							  </tr>
						  </table></td>
						</tr>
						<tr height="22">
						  <td colspan="5"><table bgcolor="#FFFFFF"   border="0" width="100%" cellpadding="0" cellspacing="0">
							  <tr>
								<td width="32%" nowrap colspan="4" align="left" valign="middle" class="text_10b pad_top_bottom"><span  style="color:#800080;cursor:hand; " onClick="return showpast_med_hx('txt_areapast_med_hx', '', 'no', '20', '160'),document.getElementById('selected_frame_name_id').value='';" ><img src="../images/tpixel.gif" width="4" height="1">Past Med. Hx <img src="../images/tpixel.gif" width="4" height="1"></span></td>
								<td width="68%" class="text_10" align="left" style="padding-left:18px;"><textarea name="laser_Procedurepast_med_hx" id="txt_areapast_med_hx" class="field textarea justi text_10" style="border:1px solid #cccccc;  width:250px; " rows="4" cols="100" tabindex="6"><?php echo stripslashes(ucfirst($laserDetails->laser_past_med_hx)); ?></textarea>
								</td>
							  </tr>
						  </table></td>
						</tr>
						<tr height="22">
						  <td colspan="5"><table  border="0" width="100%" cellpadding="0" cellspacing="0">
							  <tr>
								<td width="32%" nowrap colspan="4" align="left" valign="middle" class="text_10b pad_top_bottom"><span  style="color:#800080;cursor:hand; " onClick="return showmedication('txt_areamedication_admin', '', 'no', '20', '210'),document.getElementById('selected_frame_name_id').value='';" ><img src="../images/tpixel.gif" width="4" height="1">Ocular Medication & Dosage <img src="../images/tpixel.gif" width="4" height="1"></span></td>
								<td width="68%" class="text_10" align="left"><textarea name="laser_Proceduremedication" id="txt_areamedication_admin" class="field textarea justi text_10" style="border:1px solid #cccccc; width:250px; " rows="4" cols="100" tabindex="6"><?php echo stripslashes(ucfirst($laserDetails->laser_medication));?></textarea>
								</td>
							  </tr>
						  </table></td>
						</tr>
						<!--End History-->
						<!--Physical Exam-->
						<tr >
						  <td colspan="5"><table width="100%" border="0" cellpadding="0" cellspacing="0">
							  <tr>
								<td width="0" class=""><img border="0" src="../images/left_new.gif"></td>
								<td  class="text_10bAdmin all_border" width="100%"><table border="0" cellpadding="0" cellspacing="0" width="100%">
									<tr height="22">
									  <td   class="text_10b" bgcolor="#c0aa1e">Physical Exam</td>
									  <td  align="left" class="text_10b" bgcolor="#c0aa1e"><!-- Select by default --></td>
									</tr>
								</table></td>
								<td width="1" class=""><img border="0" src="../images/right_new.gif"></td>
							  </tr>
						  </table></td>
						</tr>
						<tr height="22">
						  <td colspan="5"><table bgcolor="#FFFFFF"   border="0" width="100%" cellpadding="0" cellspacing="0">
							  <tr>
								<td width="32%" nowrap colspan="4" align="left" valign="middle" class="text_10b pad_top_bottom"><span  style="color:#800080;cursor:hand; " onClick="return showsle('txt_areasle_admin', '', 'no', '20', '280'),document.getElementById('selected_frame_name_id').value='';" ><img src="../images/tpixel.gif" width="4" height="1">SLE <img src="../images/tpixel.gif" width="4" height="1"></span></td>
								<td width="68%" class="text_10" align="left" style="padding-left:18px;"><textarea name="laser_Proceduresle" id="txt_areasle_admin" class="field textarea justi text_10" style="border:1px solid #cccccc; width:250px; " rows="4" cols="100" tabindex="6"><?php echo stripslashes(ucfirst($laserDetails->laser_sle));?></textarea>
								</td>
							  </tr>
						  </table></td>
						</tr>
						<tr height="22">
						  <td colspan="5"><table  border="0" width="100%" cellpadding="0" cellspacing="0">
							  <tr>
								<td width="32%" nowrap colspan="4" align="left" valign="middle" class="text_10b pad_top_bottom"><span  style="color:#800080;cursor:hand; " onClick="return showfundus_exam('txt_areafundus_exam_admin', '', 'no', '20', '330'),document.getElementById('selected_frame_name_id').value='';"><img src="../images/tpixel.gif" width="4" height="1">Fundus Exam <img src="../images/tpixel.gif" width="4" height="1"></span></td>
								<td width="68%" class="text_10" align="left" style="padding-left:18px;"><textarea name="laser_Procedurefundus_exam" id="txt_areafundus_exam_admin" class="field textarea justi text_10" style="border:1px solid #cccccc; width:250px; " rows="4" cols="100" tabindex="6"><?php echo stripslashes(ucfirst($laserDetails->laser_fundus_exam));?></textarea>
								</td>
							  </tr>
						  </table></td>
						</tr>
						<tr height="22">
						  <td colspan="5"><table  bgcolor="#FFFFFF"  border="0" width="100%" cellpadding="0" cellspacing="0">
							  <tr>
								<td width="32%" nowrap colspan="4" align="left" valign="middle" class="text_10b pad_top_bottom"><span  style="color:#800080;cursor:hand; " onClick="return showmental_state('txt_areamental_state_admin', '', 'no', '20', '375'),document.getElementById('selected_frame_name_id').value='';"><img src="../images/tpixel.gif" width="4" height="1">Mental State <img src="../images/tpixel.gif" width="4" height="1"></span></td>
								<td width="68%" class="text_10" align="left" style="padding-left:18px;"><textarea name="laser_Proceduremental_state" id="txt_areamental_state_admin" class="field textarea justi text_10" style="border:1px solid #cccccc; width:250px; " rows="4" cols="100" tabindex="6"><?php echo stripslashes(ucfirst($laserDetails->laser_mental_state));?></textarea>
								</td>
							  </tr>
						  </table></td>
						</tr>
						<!--End Physical Exam-->
						<!--History-->
						<tr >
						  <td colspan="5"><table width="100%" border="0" cellpadding="0" cellspacing="0">
							  <tr>
								<td width="0" class=""><img border="0" src="../images/left_new.gif"></td>
								<td  class="text_10bAdmin all_border" width="100%"><table border="0" cellpadding="0" cellspacing="0" width="100%">
									<tr height="22">
									  <td   class="text_10b" bgcolor="#c0aa1e">Procedure Notes</td>
									  <td  align="left" class="text_10b" bgcolor="#c0aa1e"><!-- Select by default --></td>
									</tr>
								</table></td>
								<td width="1" class=""><img border="0" src="../images/right_new.gif"></td>
							  </tr>
						  </table></td>
						</tr>
						<tr height="22">
						  <td colspan="5"><table  border="0" width="100%" cellpadding="0" cellspacing="0">
							  <tr>
								<td width="32%" nowrap colspan="4" align="left" valign="middle" class="text_10b pad_top_bottom"><span  style="color:#800080;cursor:hand; " onClick="return showspot_size('txt_areaspot_size_admin', '', 'no', '20', '450'),document.getElementById('selected_frame_name_id').value='';" ><img src="../images/tpixel.gif" width="4" height="1">Spot Size <img src="../images/tpixel.gif" width="4" height="1"></span></td>
								<td width="68%" class="text_10" align="left" style="padding-left:18px;"><textarea name="laser_Procedurespot_size" id="txt_areaspot_size_admin" class="field textarea justi text_10" style="border:1px solid #cccccc; width:250px; " rows="4" cols="100" tabindex="6"><?php echo stripslashes(ucfirst($laserDetails->laser_spot_size));?></textarea>
								</td>
							  </tr>
						  </table></td>
						</tr>
						<tr height="22">
						  <td colspan="5"><table bgcolor="#FFFFFF"   border="0" width="100%" cellpadding="0" cellspacing="0">
							  <tr>
								<td width="32%" nowrap colspan="4" align="left" valign="middle" class="text_10b pad_top_bottom"><span  style="color:#800080;cursor:hand; " onClick="return showpower('txt_areapower_admin', '', 'no', '20', '490'),document.getElementById('selected_frame_name_id').value='';" ><img src="../images/tpixel.gif" width="4" height="1">Power <img src="../images/tpixel.gif" width="4" height="1"></span></td>
								<td width="68%" class="text_10" align="left" style="padding-left:18px;"><textarea name="laser_Procedurepower" id="txt_areapower_admin" class="field textarea justi text_10" style="border:1px solid #cccccc; width:250px; " rows="4" cols="100" tabindex="6"><?php echo stripslashes(ucfirst($laserDetails->laser_power));?></textarea>
								</td>
							  </tr>
						  </table></td>
						</tr>
						<tr height="22">
						  <td colspan="5"><table border="0" width="100%" cellpadding="0" cellspacing="0">
							  <tr>
								<td width="32%" nowrap colspan="4" align="left" valign="middle" class="text_10b pad_top_bottom"><span  style="color:#800080;cursor:hand; " onClick="return showexposure('txt_areaexposure_admin', '', 'no', '20', '540'),document.getElementById('selected_frame_name_id').value='';"><img src="../images/tpixel.gif" width="4" height="1">Exposure <img src="../images/tpixel.gif" width="4" height="1"></span></td>
								<td width="68%" class="text_10" align="left" style="padding-left:18px;"><textarea name="laser_Procedureexposure" id="txt_areaexposure_admin" class="field textarea justi text_10" style="border:1px solid #cccccc; width:250px; " rows="4" cols="100" tabindex="6"><?php echo stripslashes(ucfirst($laserDetails->laser_exposure)); ?></textarea>
								</td>
							  </tr>
						  </table></td>
						</tr>
						<tr height="22">
						  <td colspan="5"><table bgcolor="#FFFFFF"   border="0" width="100%" cellpadding="0" cellspacing="0">
							  <tr>
								<td width="32%" nowrap colspan="4" align="left" valign="middle" class="text_10b pad_top_bottom"><span  style="color:#800080;cursor:hand; "  onClick="return showcount('txt_areacount_admin', '', 'no', '20', '585'),document.getElementById('selected_frame_name_id').value='';" ><img src="../images/tpixel.gif" width="4" height="1">Count <img src="../images/tpixel.gif" width="4" height="1"></span></td>
								<td width="68%" class="text_10" align="left" style="padding-left:18px;"><textarea name="laser_Procedurecount" id="txt_areacount_admin" class="field textarea justi text_10" style="border:1px solid #cccccc; width:250px; " rows="4" cols="100" tabindex="6"><?php echo stripslashes(ucfirst($laserDetails->laser_count));?></textarea>
								</td>
							  </tr>
						  </table></td>
						</tr>
						<tr height="22">
						  <td colspan="5"><table  border="0" width="100%" cellpadding="0" cellspacing="0">
							  <tr>
								<td width="32%" nowrap colspan="4" align="left" valign="middle" class="text_10b pad_top_bottom"><span  style="color:#800080;cursor:hand; " onClick="return showanesthesia('txt_areaanesthesia_admin', '', 'no', '20', '635'),document.getElementById('selected_frame_name_id').value='';" ><img src="../images/tpixel.gif" width="4" height="1">Anesthesia <img src="../images/tpixel.gif" width="4" height="1"></span></td>
								<td width="68%" class="text_10" align="left" style="padding-left:18px;"><textarea name="laser_Procedureanesthesia" id="txt_areaanesthesia_admin" class="field textarea justi text_10" style="border:1px solid #cccccc; width:250px; " rows="4" cols="100" tabindex="6"><?php echo stripslashes(ucfirst($laserDetails->laser_anesthesia));?></textarea>
								</td>
							  </tr>
						  </table></td>
						</tr>
						<tr >
						  <td colspan="5"><table width="100%" border="0" cellpadding="0" cellspacing="0">
							  <tr>
								<td width="0" class=""><img border="0" src="../images/left_new.gif"></td>
								<td  class="text_10bAdmin all_border" width="100%"><table border="0" cellpadding="0" cellspacing="0" width="100%">
									<tr height="22">
									  <td   class="text_10b" bgcolor="#c0aa1e">Post Progress Notes </td>
									  <td  align="left" class="text_10b" bgcolor="#c0aa1e"><!-- Select by default --></td>
									</tr>
								</table></td>
								<td width="1" class=""><img border="0" src="../images/right_new.gif"></td>
							  </tr>
						  </table></td>
						</tr>
						<tr height="22">
						  <td colspan="5"><table  border="0" width="100%" cellpadding="0" cellspacing="0">
							  <tr>
								<td width="32%" nowrap colspan="4" align="left" valign="middle" class="text_10b pad_top_bottom"><span  style="color:#800080;cursor:hand; " onClick="return showpost_progress('txt_areapost_progress_admin', '', 'no', '20', '705'),document.getElementById('selected_frame_name_id').value='';" ><img src="../images/tpixel.gif" width="4" height="1">Post Progress Notes <img src="../images/tpixel.gif" width="4" height="1"></span></td>
								<td width="68%" class="text_10" align="left" style="padding-left:18px;"><textarea name="laser_Procedurepost_progress" id="txt_areapost_progress_admin" class="field textarea justi text_10" style="border:1px solid #cccccc; width:250px; " rows="4" cols="100" tabindex="6"><?php echo stripslashes(ucfirst($laserDetails->laser_post_progress));?></textarea>
								</td>
							  </tr>
						  </table></td>
						</tr>
						<tr >
						  <td colspan="5"><table width="100%" border="0" cellpadding="0" cellspacing="0">
							  <tr>
								<td width="0" class=""><img border="0" src="../images/left_new.gif"></td>
								<td  class="text_10bAdmin all_border" width="100%"><table border="0" cellpadding="0" cellspacing="0" width="100%">
									<tr height="22">
									  <td   class="text_10b" bgcolor="#c0aa1e">Post Operative Status </td>
									  <td  align="left" class="text_10b" bgcolor="#c0aa1e"><!-- Select by default --></td>
									</tr>
								</table></td>
								<td width="1" class=""><img border="0" src="../images/right_new.gif"></td>
							  </tr>
						  </table></td>
						</tr>
						<tr height="22">
						  <td colspan="5"><table bgcolor="#FFFFFF"  border="0" width="100%" cellpadding="0" cellspacing="0">
							  <tr>
								<td width="32%" nowrap colspan="4" align="left" valign="middle" class="text_10b pad_top_bottom"><span  style="color:#800080;cursor:hand; " onClick="return showpost_operative('txt_areapost_operative_admin', '', 'no', '20', '775'),document.getElementById('selected_frame_name_id').value='';" ><img src="../images/tpixel.gif" width="4" height="1">Post Operative Status</span></td>
								<td width="68%" class="text_10" align="left" style="padding-left:18px;"><textarea name="laser_Procedurepost_operative" id="txt_areapost_operative_admin" class="field textarea justi text_10" style="border:1px solid #cccccc; width:250px; " rows="4" cols="100" tabindex="6"><?php echo stripslashes(ucfirst($laserDetails->laser_post_operative));?></textarea>
								</td>
							  </tr>
						  </table></td>
						</tr>
					  </table>
				</td>
			</tr>
			<tr>
				<td></td>
				<td align="center"></td>
			</tr>
		</table>
	</div>
	</form>

</body>
</html>

