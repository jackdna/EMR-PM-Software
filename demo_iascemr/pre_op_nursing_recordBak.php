<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
include_once("common/conDb.php");
include("common/linkfile.php");
//include("common/mainslider.php"); 
extract($_GET);


//CODE TO DISABLE SLIDER LINK AT SINGLE CLICK 
	$patient_id = $_REQUEST["patient_id"];
	$ascId = $_REQUEST["ascId"];
	$pConfId = $_REQUEST["pConfId"];
	
	$thisId = $_REQUEST["thisId"];
	if($innerKey=="") {
		$innerKey = $_REQUEST["innerKey"];
	}
	if($preColor=="") {
		$preColor = $_REQUEST["preColor"];
	}	
	
	$fieldName = "pre_op_nursing_form";
	$pageName = "pre_op_nursing_record.php?patient_id=$patient_id&pConfId=$pConfId&ascId=$ascId";
	if($_REQUEST["cancelRecord"]=="true") {  //IF PRESS CANCEL BUTTON
		$pageName = "blank_mainform.php?patient_id=$patient_id&pConfId=$pConfId&ascId=$ascId";
	}
	include("left_link_hide.php");
//END CODE TO DISABLE SLIDER LINK AT SINGLE CLICK 
$saveLink = '&thisId='.$thisId.'&innerKey='.$innerKey.'&preColor='.$preColor.'&patient_id='.$patient_id.'&pConfId='.$pConfId.'&ascId='.$ascId;

?>
<script>
	top.yellow('<?php echo $innerKey;?>','<?php echo $preColor;?>');
</script>
<body onLoad="top.changeColor('<?php echo $bgcolor_pre_op_nursing_order; ?>');" onClick="return top.main_frmInner.hideSliders();">
<?php
include("common/pre_defined_popup.php");
?>
<table cellpadding="0" cellspacing="0" border="0" align="center" width="100%" onDblClick="closePreDefineDiv()" onMouseOver="closePreDefineDiv();" bgcolor="<?php echo $bgcolor_pre_op_nursing_order; ?>">
	<tr>
		<td ><img src="images/tpixel.gif" width="1" height="2"></td>
	</tr>
	<tr>
		<td  valign="top" align="center">
			<table width="24%" border="0" align="center" cellpadding="0" cellspacing="0">
				<tr>
					<td width="6" align="right"><img src="images/leftyellow_post_op_nurse_order.gif" width="3" height="24"></td>
					<td width="229" align="center" valign="middle" bgcolor="<?php echo $title_pre_op_nursing_order; ?>" class="text_10b1" >	<span style="color:<?php echo $title2_color;?>">Pre-Op Nursing Record</span></td>
				  <td align="left" valign="top" width="10"><img src="images/rightyellow_post_op_nurse_order.gif" width="3" height="24"></td>
				</tr>
		  </table>
		</td>
	</tr>
	<tr>
	  <td><img src="images/tpixel.gif" width="4" height="1"></td>

	</tr>
	
	<tr>
		<td>
		  
		<form name="frm_pre_op_nurs_rec" class="wufoo topLabel" enctype="multipart/form-data" method="post" style="margin:0px; " action="">
		<input type="hidden" name="divId">
		<input type="hidden" name="counter">
		<input type="hidden" name="secondaryValues">
		<input type="hidden" id="selected_frame_name_id" name="selected_frame_name" value="">
		<input type="hidden" name="SaveRecordForm" value="yes">
		<table  width="99%" align="center" cellpadding="0" border="0" cellspacing="0" bgcolor="#FFFFFF" class="all_border " style="border-color:<?php echo $border_pre_op_nursing_order; ?>">
							<tr>
								<td><img src="images/tpixel.gif" width="4" height="1"></td>
								<td width="510"  valign="top">
									<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
										<tr align="left">
											<td height="22" colspan="4" class="text_10b">
												<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
												<tr><td colspan="5"><img src="images/tpixel.gif"></img></td></tr>
													<tr align="left" bgcolor="<?php echo $heading_pre_op_nursing_order; ?>">
														<td colspan="5" height="22"  class="text_10b" nowrap style="color:#800080;cursor:hand;" onClick="return showPreDefineFn('Allergies_preopnurse', 'Reaction_preopnurse', '10', '35', '45'),document.getElementById('selected_frame_name_id').value='iframe_allergies_pre_op_nurse_rec';">
															<img  src="images/tpixel.gif" width="4" height="5">
															Allergies/Drug Reaction
														</td>
														<!-- <td height="22"   width="5%" onClick="javascript:checkSingle('chbx_drug_react_no','chbx_drug_react'),txt_disable('txt_field01',
																														'txt_field02',
																														'txt_field03',
																														'txt_field04',
																														'txt_field05',
																														'txt_field06',
																														'txt_field07',
																														'txt_field08');">
															<img src="images/tpixel.gif" width="4" height="5">
															<input class="checkbox"  type="checkbox" value="No" name="chbx_drug_react" id="chbx_drug_react_no"  tabindex="7" >
														</td>
														<td height="22"  class="text_10b" style="color:#800080;">No Known Allergies</td>
														<td height="22"   width="5%" onClick="javascript:checkSingle('chbx_drug_react_yes','chbx_drug_react'),txt_enable('txt_field01',
																														'txt_field02',
																														'txt_field03',
																														'txt_field04',
																														'txt_field05',
																														'txt_field06',
																														'txt_field07',
																														'txt_field08');">
														
															<img src="images/tpixel.gif" width="4" height="5">
															<input class="checkbox"  type="checkbox" value="Yes" name="chbx_drug_react" id="chbx_drug_react_yes"  tabindex="7" >
														</td>
														<td height="22"  class="text_10b" style="color:#800080;">Allergies Reviewed</td> -->
													</tr>
												</table>
											</td>
										</tr>
										<tr bgcolor="#FFFFFF">
										  <td  colspan="4" align="left" valign="middle">
										  	<table width="100%" border="0" cellpadding="0" cellspacing="0" bordercolor="#333333" >
												<tr height="22" bgcolor="#FFFFFF">
													<td>&nbsp;<img src="images/tpixel.gif" width="25" height="1"></td>
													<td width="436" class="text_10  pad_top_bottom">
														Name
													</td>
													
													<td width="512" colspan="4" class="text_10  pad_top_bottom">
														<img src="images/tpixel.gif" width="25" height="1">
														Reaction
													</td>
													
												</tr>
												 <tr bgcolor="#F1F4F0">
													<td colspan="6" bgcolor="#F1F4F0">
														<iframe name="iframe_allergies_pre_op_nurse_rec" src="pre_op_nurse_allergies_spreadsheet.php" width="100%" height="95"  frameborder="0"  scrolling="yes" ></iframe>   
													</td>
												</tr>  
												   
										    </table>
										  
										  </td>
									  </tr>
										<!-- <tr bgcolor="#D1E0C9">
											<td colspan="4" height="20" class="text_10b">
												<img src="images/tpixel.gif" width="15" height="1"><span class="text_10b1">Allergies</span>
												<img src="images/tpixel.gif" width="15" height="1"><span class="text_10">Dust Allergy</span>
											</td>
											
									   </tr> -->
									   <tr bgcolor="#FFFFFF">
											<td colspan="4" class="text_10b">
												<!-- <img src="images/tpixel.gif" width="5" height="1"> -->
											</td>
											
									   </tr>
									   <tr><td><img src="images/tpixel.gif" width="5" height="2"></td></tr>
										<tr bgcolor="<?php echo $heading_pre_op_nursing_order; ?>">
											<td height="20" class="text_10b"><img src="images/tpixel.gif" width="15" height="1"></td>
											<td height="20" align="left" class="text_10b"><span style="color:<?php echo $title2_color;?>">Yes</span></td>
											<td width="7%" height="20" align="left" class="text_10b"><span style="color:<?php echo $title2_color;?>">No</span></td>
									  		<td align="left" class="text_10 pad_top_bottom"></td>
									  </tr>
										<tr bgcolor="<?php echo $rowcolor_pre_op_nursing_order; ?>">
											<td height="22" align="left" valign="middle" class="text_10 pad_top_bottom"><img src="images/tpixel.gif" width="4" height="1">Food or Drink Today</td>
											<td align="left" valign="top" class="text_10 pad_top_bottom" onClick="javascript:checkSingle('chbx_fdt_yes','chbx_fdt')"><input class="field checkbox" type="checkbox" value="Yes" name="chbx_fdt" id="chbx_fdt_yes" tabindex="7" ></td>
											<td align="left" valign="top" class="text_10 pad_top_bottom" onClick="javascript:checkSingle('chbx_fdt_no','chbx_fdt')"><input class="field checkbox" type="checkbox" value="No" name="chbx_fdt" id="chbx_fdt_no" tabindex="7" ></td>
											<td align="left" class="text_10 pad_top_bottom"></td>
									  </tr>
									  <tr bgcolor="#FFFFFF" >
											<td colspan="4" height="20" align="left" valign="middle" class="text_10 pad_top_bottom">
												<table border="0" cellpadding="0" cellspacing="0" width="100%" align="center"  >
													<tr>
														<td width="20%" nowrap align="left"  class="text_10b pad_top_bottom" style="color:#800080;cursor:hand;" onClick="return showPreDefineFn('Field3', '', 'no', '35', '230'),document.getElementById('selected_frame_name_id').value='';" valign="middle"><img src="images/tpixel.gif" width="4" height="1">List Food Take</td>
													    <td width="80%" nowrap align="center" class="text_10" ><textarea id="Field3" name="txtarea_list_food_take" class="field textarea justi " style="border:1px solid #cccccc; width:330px; " rows="10" cols="50" tabindex="6"  ></textarea></td>
													</tr>
										  		</table>										  
											</td>
								      </tr>
									  <!-- <tr bgcolor="#FFFFFF">
											<td height="22" align="left" valign="middle" class="text_10 pad_top_bottom" style="color:#800080;"><img src="images/tpixel.gif" width="4" height="1">List Food Take</td>
											<td colspan="2" align="left" valign="top" class="text_10 pad_top_bottom" >
												<select name="list_food_take" class="text_10 pad_top_bottom" style="font-family:verdana; width:90px; font-size:11px; border:1px solid #cccccc;">
																<option>Predefined</option>
																<option>Wheet</option>
																<option>Rice</option>
															</select>
											</td>
											<td align="left" class="text_10 pad_top_bottom"></td>
									  </tr> -->
										
										<tr bgcolor="<?php echo $rowcolor_pre_op_nursing_order; ?>">
											<td height="22" align="left" valign="middle" class="text_10 pad_top_bottom"><img src="images/tpixel.gif" width="4" height="1">Lab Test</td>
											<td align="left" class="text_10 pad_top_bottom" onClick="javascript:checkSingle('chbx_lab_test_yes','chbx_lab_test')"><input class="field checkbox" type="checkbox" value="Yes" name="chbx_lab_test" id="chbx_lab_test_yes" tabindex="7" ></td>
											<td align="left" class="text_10 pad_top_bottom" onClick="javascript:checkSingle('chbx_lab_test_no','chbx_lab_test')"><input class="field checkbox" type="checkbox" value="No" name="chbx_lab_test" id="chbx_lab_test_no" tabindex="7" ></td>
											<td align="left" class="text_10 pad_top_bottom"></td>
										</tr>
										<tr bgcolor="#FFFFFF">
											<td width="82%" height="22" align="left" valign="middle" class="text_10 pad_top_bottom"><img src="images/tpixel.gif" width="4" height="1">EKG</td>
										  <td width="8%" align="left" class="text_10 pad_top_bottom" onClick="javascript:checkSingle('chbx_ekg_yes','chbx_ekg')"><input class="field checkbox" type="checkbox" value="Yes" name="chbx_ekg" id="chbx_ekg_yes" tabindex="7" ></td>
										  <td width="7%" align="left" class="text_10 pad_top_bottom" onClick="javascript:checkSingle('chbx_ekg_no','chbx_ekg')"><input class="field checkbox" type="checkbox" value="No" name="chbx_ekg" id="chbx_ekg_no" tabindex="7" ></td>
										  <td width="3%" align="left" class="text_10 pad_top_bottom"></td>
										</tr>
										
										<tr bgcolor="<?php echo $rowcolor_pre_op_nursing_order; ?>">
											<td height="22" align="left" valign="middle" class="text_10 pad_top_bottom"><img src="images/tpixel.gif" width="4" height="1">Consent Signed</td>
										  <td align="left" class="text_10 pad_top_bottom" onClick="javascript:checkSingle('chbx_cons_sign_yes','chbx_cons_sign')"><input class="field checkbox" type="checkbox" value="Yes" name="chbx_cons_sign" id="chbx_cons_sign_yes" tabindex="7" ></td>
										  <td align="left" class="text_10 pad_top_bottom" onClick="javascript:checkSingle('chbx_cons_sign_no','chbx_cons_sign')"><input class="field checkbox" type="checkbox" value="No" name="chbx_cons_sign" id="chbx_cons_sign_no" tabindex="7" ></td>
										  <td align="left" class="text_10 pad_top_bottom"></td>
										</tr>
										<tr bgcolor="#FFFFFF">
											<td height="22" align="left" valign="middle" class="text_10 pad_top_bottom"><img src="images/tpixel.gif" width="4" height="1">H & P</td>
											<td align="left" class="text_10 pad_top_bottom" onClick="javascript:checkSingle('chbx_h_p_yes','chbx_h_p')"><input class="field checkbox" type="checkbox" value="Yes" name="chbx_h_p" id="chbx_h_p_yes" tabindex="7" ></td>
											<td align="left" class="text_10 pad_top_bottom" onClick="javascript:checkSingle('chbx_h_p_no','chbx_h_p')"><input class="field checkbox" type="checkbox" value="No" name="chbx_h_p" id="chbx_h_p_no" tabindex="7" ></td>
											<td align="left" class="text_10 pad_top_bottom"></td>
										</tr>
										
										
										
										<tr bgcolor="<?php echo $rowcolor_pre_op_nursing_order; ?>" id="adm_hosp_id_main">
											<td height="11" align="left" valign="middle" class="text_10 pad_top_bottom">
												<div><img src="images/tpixel.gif" width="2" height="1">Admitted To Hospital in Past 30 Days</div>
												 <table cellpadding="0" cellspacing="0" width="100%" align="center" id="adm_hosp_id"  style="display:none; " >
													<tr><td colspan="3"><img src="images/tpixel.gif" width="2" height="4"></tr>
													<tr>
														<td width="6%">&nbsp;</td>
														<td width="15%" align="left" valign="top" class="text_10 pad_top_bottom">Reason</td>
													    <td width="79%"  align="left"  class="text_10">
														<textarea id="Field3" name="txtarea_admit_to_hosp" class="field textarea justi " style="border:1px solid #cccccc; width:330px; " rows="10" cols="50" tabindex="6"  ></textarea>
														<!--<textarea id="Field3" class="field textarea justi " style=" border:1px solid #cccccc; width:330px; "  tabindex="6"  ></textarea> --></td>
													</tr>
										  </table> 									
											</td>
										  <td  valign="top" align="left" class="text_10 pad_top_bottom" onClick="javascript:checkSingle('chbx_admit_to_hosp_yes','chbx_admit_to_hosp'),disp(document.frm_pre_op_nurs_rec.chbx_admit_to_hosp,'adm_hosp_id')"><input class="field checkbox"   type="checkbox" value="Yes" name="chbx_admit_to_hosp" id="chbx_admit_to_hosp_yes" tabindex="7" ></td>
										  <td valign="top" align="left" class="text_10 pad_top_bottom" onClick="javascript:checkSingle('chbx_admit_to_hosp_no','chbx_admit_to_hosp'),disp_none(document.frm_pre_op_nurs_rec.chbx_admit_to_hosp,'adm_hosp_id')"><input class="field checkbox"   type="checkbox" value="No" name="chbx_admit_to_hosp" id="chbx_admit_to_hosp_no" tabindex="7" ></td>
										  <td id="arr_3" onClick="javascript:disp_rev(document.frm_pre_op_nurs_rec.chbx_hist_can,'adm_hosp_id','arr_3')" align="center" valign="top" class="text_10 "  style="padding-top:3px; "><img  src="images/block.gif" width="11" height="13" border="0" style="cursor:hand; " /></td>
										</tr>
						  		  </table>	
							  	</td>
								<td><img src="images/tpixel.gif" width="2" height="1"></td>
								
								<td width="470"  valign="top">
							
									<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
										
										<tr>
										  <td  colspan="4" align="left"></td>
									   </tr>
									  <tr align="left" bgcolor="<?php echo $heading_pre_op_nursing_order; ?>" valign="top"><td height="22" colspan="4" valign="middle" class="text_10b" style="color:#800080;cursor:hand;" onClick="return showPreDefineFn('medication_peropnurse_name', 'medication_peropnurse_details', '10', '575', '45'),document.getElementById('selected_frame_name_id').value='iframe_medication_pre_op_nurse';"><img src="images/tpixel.gif" width="4" height="5">Meds Taken Today</td>
									  </tr>
										<tr bgcolor="#FFFFFF">
										  <td  colspan="4" align="left" valign="middle">
										  	<table width="100%" border="0" cellpadding="0" cellspacing="0" bordercolor="#333333" >
												<tr bgcolor="#FFFFFF">
													<td height="22">&nbsp;<img src="images/tpixel.gif" width="25" height="1"></td>
													<td width="436" class="text_10  pad_top_bottom">
														Name
													</td>
													
													<td width="512" colspan="4" class="text_10  pad_top_bottom">
														<img src="images/tpixel.gif" width="15" height="1">
														Details
													</td>
													
												</tr>
												<tr bgcolor="#F1F4F0">
													<td colspan="6" bgcolor="#F1F4F0">
														<iframe name="iframe_medication_pre_op_nurse" src="pre_op_nurse_medication_spreadsheet.php" width="100%" height="95"  frameborder="0"  scrolling="yes"></iframe>   
													</td>
												</tr> 
												 <!-- <tr bgcolor="<?php echo $rowcolor_pre_op_nursing_order; ?>">
													<td height="22">&nbsp;</td>
												  <td height="22" class="text_10b"><input type="text" id="medication1" class="field text" style=" border:1px solid #ccccc; width:200px; "   tabindex="1" value=""  /></td>
												  <td colspan="4" class="text_10b"><input type="text" id="medicationDetails1" class="field text" style=" border:1px solid #ccccc; width:200px;" tabindex="1" value=""  /></td>
												</tr>
												<tr bgcolor="#FFFFFF">
													<td width="42" height="22">&nbsp;</td>
												  <td class="text_10b"><input type="text" id="medication2" class="field text" style=" border:1px solid #ccccc; width:200px; " tabindex="1" value=""  /></td>
												  <td colspan="4" class="text_10b"><input type="text" id="medicationDetails2" class="field text" style=" border:1px solid #ccccc; width:200px; " tabindex="1" value=""  /></td>
												</tr>
												<tr bgcolor="<?php echo $rowcolor_pre_op_nursing_order; ?>">
													<td height="22">&nbsp;</td>
												  <td class="text_10b"><input type="text" id="medication3" class="field text" style=" border:1px solid #ccccc; width:200px;" tabindex="1" value=""  /></td>
												  <td colspan="4" class="text_10b"><input type="text" id="medicationDetails3" class="field text" style=" border:1px solid #ccccc;width:200px; " tabindex="1" value=""  /></td>
												</tr> 
												<tr>
													<td height="22">&nbsp;</td>
												  <td class="text_10b"><input type="text"  id="medication4" class="field text" style=" border:1px solid #ccccc; width:200px;" tabindex="1" value=""  /></td>
													<td class="text_10b" colspan="4"><input type="text" id="medicationDetails4" class="field text" style=" border:1px solid #ccccc; width:200px;"tabindex="1" value=""  /></td>
												</tr> -->
										    </table>
										  
										  </td>
									  </tr>	
									  <tr>
										  <td  colspan="4" align="left" valign="middle"><img src="images/tpixel.gif" width="2" height="3"></td>
									   </tr>
										<tr bgcolor="#FFFFFF">
										<td height="20" valign="middle" bgcolor="<?php echo $heading_pre_op_nursing_order; ?>" class="text_10b"><img src="images/tpixel.gif" width="5" height="1"></td>
										<td height="20" bgcolor="<?php echo $heading_pre_op_nursing_order; ?>" align="left" class="text_10b"><span style="color:<?php echo $title2_color;?>">Yes</span></td>
										<td  bgcolor="<?php echo $heading_pre_op_nursing_order; ?>" height="20" align="left" class="text_10b"><span style="color:<?php echo $title2_color;?>">No</span></td>
									 	<td  bgcolor="<?php echo $heading_pre_op_nursing_order; ?>" height="20" align="left" class="text_10b"></td>
									  </tr>
									  <tr bgcolor="<?php echo $rowcolor_pre_op_nursing_order; ?>">
											<td width="83%" height="22" align="left" valign="middle" class="text_10 pad_top_bottom"><img src="images/tpixel.gif" width="4" height="1">Health Questionnaire</td>
											<td width="7%" align="left" valign="top" class="text_10 pad_top_bottom" onClick="javascript:checkSingle('chbx_hlt_ques_yes','chbx_hlt_ques')"><input class="field checkbox" type="checkbox" value="Yes" name="chbx_hlt_ques" id="chbx_hlt_ques_yes" tabindex="7" ></td>
											<td width="7%" align="left" valign="top" class="text_10 pad_top_bottom" onClick="javascript:checkSingle('chbx_hlt_ques_no','chbx_hlt_ques')"><input class="field checkbox" type="checkbox" value="No" name="chbx_hlt_ques" id="chbx_hlt_ques_no" tabindex="7" ></td>
									  		<td width="3%" align="left" valign="top" class="text_10 pad_top_bottom"></td>
									  </tr>
									  <tr bgcolor="#FFFFFF">
										<td height="39" align="left" valign="middle" class="text_10 pad_top_bottom"><img src="images/tpixel.gif" width="4" height="1">Standing Orders</td>
										<td align="left" valign="middle" class="text_10 pad_top_bottom" onClick="javascript:checkSingle('chbx_stnd_odrs_yes','chbx_stnd_odrs')"><input class="field checkbox" type="checkbox" value="Yes" name="chbx_stnd_odrs" id="chbx_stnd_odrs_yes" tabindex="7" ></td>
										<td align="left" valign="middle" class="text_10 pad_top_bottom" onClick="javascript:checkSingle('chbx_stnd_odrs_no','chbx_stnd_odrs')"><input class="field checkbox" type="checkbox" value="No" name="chbx_stnd_odrs" id="chbx_stnd_odrs_no" tabindex="7" ></td>
									  	<td align="left" valign="middle" class="text_10 pad_top_bottom"></td>
									  </tr>
									  <tr bgcolor="#FFFFFF"><td><img src="images/tpixel.gif" width="2" height="2"></td></tr>
									  <tr bgcolor="<?php echo $rowcolor_pre_op_nursing_order; ?>">

											<td height="22" align="left" valign="middle" class="text_10 pad_top_bottom"><img src="images/tpixel.gif" width="4" height="1">Pat. Voided</td>
											<td align="left" valign="top" class="text_10 pad_top_bottom" onClick="javascript:checkSingle('chbx_pat_void_yes','chbx_pat_void')"><input class="field checkbox" type="checkbox" value="Yes" name="chbx_pat_void" id="chbx_pat_void_yes" tabindex="7" ></td>
											<td align="left" valign="top" class="text_10 pad_top_bottom" onClick="javascript:checkSingle('chbx_pat_void_no','chbx_pat_void')"><input class="field checkbox" type="checkbox" value="No" name="chbx_pat_void" id="chbx_pat_void_no" tabindex="7" ></td>
											<td align="left" valign="top" class="text_10 pad_top_bottom"></td>
									  </tr>
									  <tr bgcolor="#FFFFFF">
										<td height="24" align="left" valign="middle" class="text_10 pad_top_bottom"><img src="images/tpixel.gif" width="4" height="1">Hearing Aids & Dentures Removed</td>
										<td align="left" valign="top" class="text_10 pad_top_bottom" onClick="javascript:checkSingle('chbx_hadr_yes','chbx_hadr')"><input class="field checkbox" type="checkbox" value="Yes" name="chbx_hadr" id="chbx_hadr_yes" tabindex="7" ></td>
										<td align="left" valign="top" class="text_10 pad_top_bottom" onClick="javascript:checkSingle('chbx_hadr_no','chbx_hadr')"><input class="field checkbox" type="checkbox" value="No" name="chbx_hadr" id="chbx_hadr_no" tabindex="7" ></td>
									  	<td align="left" valign="top" class="text_10 pad_top_bottom"></td>
									  </tr>
									 <tr height="22" bgcolor="<?php echo $rowcolor_pre_op_nursing_order; ?>">
									 <td colspan="5"></td>
									 </tr>
									 <tr height="22" bgcolor="#FFFFFF">
									 <td colspan="5"></td>
									 </tr>
									 <tr height="22" bgcolor="<?php echo $rowcolor_pre_op_nursing_order; ?>">
									 <td colspan="5"></td>
									 </tr>
					  		  </table>							  </td>
							  <td><img src="images/tpixel.gif" width="4" height="1"></td>
							</tr>
					</table>
			  </form>
				<!-- WHEN CLICK ON CANCEL BUTTON -->
				<form name="frm_return_BlankMainForm" class="wufoo topLabel" enctype="multipart/form-data" method="post" style="margin:0px; " action="pre_op_nursing_record.php?cancelRecord=true<?php echo $saveLink;?>" target="_self">
				</form>
				<!-- END WHEN CLICK ON CANCEL BUTTON -->
		 </td>
	</tr>
	<tr>
		<td> <img src="images/tpixel.gif" width="1" height="2"></td>
	</tr>
	<tr>
		<td valign="top">
			<table align="center" border="0" cellpadding="0" cellspacing="0" width="99%"  bgcolor="#FFFFFF" class="all_border"  style="border-color:<?php echo $border_pre_op_nursing_order; ?>">
				<tr><td colspan="8"><img src="images/tpixel.gif" width="1" height="5"></td></tr>
				<tr align="left" valign="middle" bgcolor="<?php echo $rowcolor_pre_op_nursing_order; ?>">
					<td class="text_10b" style="color:#800080; cursor:hand;" onClick="return showPreDefineFn('list_food_take_area_id', '', 'no', '35', '400'),document.getElementById('selected_frame_name_id').value='';"><img src="images/tpixel.gif" width="10" height="1">Preoperative Comments</td>
					<td colspan="3" class="text_10" align="left">
						<!-- <select name="list_food_take" id="list_food_take_id" onChange="javascript:disp_hide_id('list_food_take_id','list_food_take_area_id');" class="text_10 pad_top_bottom" style="font-family:verdana; width:120px; font-size:11px; border:1px solid #cccccc;">
							<option value="predefined">Predefined</option>
							<option value="need_attention">Need attention</option>
							<option value="good_position">Good Position</option>
							<option value="other">Other</option>
						</select> -->
					</td>
					<td colspan="5" class="text_10" style="width:600px; ">
						<textarea  id="pre_operative_comment_id" name="txtarea_pre_operative_comment" class="field textarea justi text_10" style=" border:1px solid #cccccc; width:600px; " rows="10" cols="50" tabindex="6"  ></textarea>
					</td>
				  
			  </tr>
				<!-- <tr><td colspan="8"><img src="images/tpixel.gif" width="1" height="5"></td></tr> -->
		  </table>
		</td>
	</tr>
	<tr>
		<td> <img src="images/tpixel.gif" width="1" height="48"></td>
	</tr>
	<?php /*echo "<script>footer_bgcolor('$bgcolor_pre_op_nursing_order');</script>";*/?>
	  
	 <!-- <tr height="210">
		<td></td>
	</tr> -->   
	
</table>
</body>
<script>
	top.setPNotesHeight();
</script>