<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
	<?php
		$top=154;
		include("common/header.php");
	?>
	<tr>
		<td ><img src="images/tpixel.gif" width="1" height="2"></td>
	</tr>
	<tr>
		<td valign="top"  >
		
			<table width="99%" align="center" height="25" border="0" cellpadding="0" cellspacing="0" bgcolor="#B3DD8C" class="all_border" style="border-color:#993300; ">
				<tr bgcolor="#F1F4F0" >
					<td width="26%" align="center" bgcolor="#F5F5F5" class="text_10b1" style="border-right:1px solid #993300; color:#993300; "  ><img src="images/tpixel.gif" width="10">Base Line Vital Signs</td>
					<td width="23%" align="left" bgcolor="#FFFFFF" class="text_10b1" style="border-right:1px solid #993300; "  ><img src="images/tpixel.gif" width="10">B/P<img src="images/tpixel.gif" width="10"><span class="text_10 red_txt">456/15</span></td>
					<td width="16%" align="left" bgcolor="#FFFFFF" class="text_10b1" style="border-right:1px solid #993300; " ><img src="images/tpixel.gif" width="10">P<img src="images/tpixel.gif" width="10"><span class="text_10 red_txt">56</span></td>
					<td width="13%" align="left" bgcolor="#FFFFFF" class="text_10b1" style="border-right:1px solid #993300; " ><img src="images/tpixel.gif" width="10">R<img src="images/tpixel.gif" width="10"><span class="text_10 red_txt">45</span></td>
					<td width="22%" align="left" bgcolor="#FFFFFF" class="text_10b1"  ><img src="images/tpixel.gif" width="10">Temp</td>
		      </tr>
	 	  </table>
		</td>
	</tr>
	<tr>
		<td ><img src="images/tpixel.gif" width="1" height="5"></td>
	</tr>
	<tr>
		<td  valign="top" align="center">
			<table width="24%" border="0" align="center" cellpadding="0" cellspacing="0">
							<tr>
								<td width="6" align="right"><img src="images/left.gif" width="3" height="24"></td>
								<td width="229" align="center" valign="middle" bgcolor="#BCD2B0" class="text_10b" >Pre-Op Health Questionnaire</td>
							  <td align="left" valign="top" width="10"><img src="images/right.gif" width="3" height="24"></td>
							</tr>
		  </table>
		</td>
	</tr>
	<tr>
	  <td><img src="images/tpixel.gif" width="4" height="1"></td>

	</tr>
	
	<tr>
		<td bgcolor="#ECF1EA"  >
		  
		<form name="frm_health_ques" class="wufoo topLabel" enctype="multipart/form-data" method="post" style="margin:0px; " action="">
				  		<table  width="99%" align="center" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" class="all_border ">
							<tr>
								<td><img src="images/tpixel.gif" width="4" height="1"></td>
								<td width="510"  valign="top">
									<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
										<tr bgcolor="#D1E0C9">
											<td height="20" class="text_10b"><img src="images/tpixel.gif" width="15" height="1">Have you ever had</td>
											<td height="20" align="left" class="text_10b">Yes</td>
											<td width="7%" height="20" align="left" class="text_10b">No</td>
									  		<td align="left" class="text_10 pad_top_bottom"></td>
									  </tr>
										<tr bgcolor="#F1F4F0">
											<td height="22" align="left" valign="middle" class="text_10 pad_top_bottom"><img src="images/tpixel.gif" width="4" height="1">Heart Trouble</td>
											<td align="left" valign="top" class="text_10 pad_top_bottom" onClick="javascript:checkSingle('chbx_ht_yes','chbx_ht')"><input class="field checkbox" type="checkbox" value="Yes" name="chbx_ht" id="chbx_ht_yes" tabindex="7" ></td>
											<td align="left" valign="top" class="text_10 pad_top_bottom" onClick="javascript:checkSingle('chbx_ht_no','chbx_ht')"><input class="field checkbox" type="checkbox" value="No" name="chbx_ht" id="chbx_ht_no" tabindex="7" ></td>
											<td align="left" class="text_10 pad_top_bottom"></td>
									  </tr>
										<tr>
											<td height="22" align="left" valign="middle" class="text_10 pad_top_bottom"><img src="images/tpixel.gif" width="4" height="1">Stroke, Heart Attack</td>
											<td align="left" class="text_10 pad_top_bottom" onClick="javascript:checkSingle('chbx_sht_yes','chbx_sht')"><input class="field checkbox" type="checkbox" value="Yes" name="chbx_sht" id="chbx_sht_yes" tabindex="7" ></td>
											<td align="left" class="text_10 pad_top_bottom" onClick="javascript:checkSingle('chbx_sht_no','chbx_sht')"><input class="field checkbox" type="checkbox" value="No" name="chbx_sht" id="chbx_sht_no" tabindex="7" ></td>
											<td align="left" class="text_10 pad_top_bottom"></td>
										</tr>
										<tr bgcolor="#F1F4F0">
											<td height="22" align="left" valign="middle" class="text_10 pad_top_bottom"><img src="images/tpixel.gif" width="4" height="1">Anticogulation therapy (i.e. Blood Thinners)</td>
											<td align="left" class="text_10 pad_top_bottom" onClick="javascript:checkSingle('chbx_anti_thrp_yes','chbx_anti_thrp')"><input class="field checkbox" type="checkbox" value="Yes" name="chbx_anti_thrp" id="chbx_anti_thrp_yes" tabindex="7" ></td>
											<td align="left" class="text_10 pad_top_bottom" onClick="javascript:checkSingle('chbx_anti_thrp_no','chbx_anti_thrp')"><input class="field checkbox" type="checkbox" value="No" name="chbx_anti_thrp" id="chbx_anti_thrp_no" tabindex="7" ></td>
											<td align="left" class="text_10 pad_top_bottom"></td>
										</tr>
										<tr bgcolor="#FFFFFF">
											<td width="82%" height="22" align="left" valign="middle" class="text_10 pad_top_bottom"><img src="images/tpixel.gif" width="4" height="1">Asthma, Sleep Apnea, Breathing Problems, Tuberculosis</td>
										  <td width="8%" align="left" class="text_10 pad_top_bottom" onClick="javascript:checkSingle('chbx_ast_slp_yes','chbx_ast_slp')"><input class="field checkbox" type="checkbox" value="Yes" name="chbx_ast_slp" id="chbx_ast_slp_yes" tabindex="7" ></td>
										  <td width="7%" align="left" class="text_10 pad_top_bottom" onClick="javascript:checkSingle('chbx_ast_slp_no','chbx_ast_slp')"><input class="field checkbox" type="checkbox" value="No" name="chbx_ast_slp" id="chbx_ast_slp_no" tabindex="7" ></td>
										  <td width="3%" align="left" class="text_10 pad_top_bottom"></td>
										</tr>
										<tr bgcolor="#F1F4F0" >
											<td height="22" align="left" valign="middle" class="text_10 pad_top_bottom">
											<div><img src="images/tpixel.gif" width="4" height="1">Diabities</div>
												<table cellpadding="0" cellspacing="0" width="98%" align="center" id="diab_yes"   style="display:none; " >
													
													<tr height="22">
														<td width="5%">&nbsp;</td>
														<td width="51%" valign="top" class="text_10">Insulin Dependent</td>
													    <td width="13%" align="left" class="text_10 " valign="top" onClick="checkSingle('chbx_subdiab_yes','chbx_subdiab')"><input class="field checkbox" type="checkbox" name="chbx_subdiab" value="Yes" id="chbx_subdiab_yes" tabindex="7" ></td>
														<td width="31%">&nbsp;</td>
													</tr>
													<tr height="22">
														<td>&nbsp;</td>
														<td class="text_10" valign="top">Non Depend</td>
														<td align="left" class="text_10" valign="top" onClick="checkSingle('chbx_subdiab_no','chbx_subdiab')"><input class="field checkbox" type="checkbox" name="chbx_subdiab" value="No" id="chbx_subdiab_no" tabindex="7" ></td>
														<td width="31%">&nbsp;</td>
													</tr>
										  </table>										  </td>
											<td onClick="javascript:checkSingle('chbx_diab_yes','chbx_diab'),disp(document.frm_health_ques.chbx_diab,'diab_yes')" align="left" class="text_10 pad_top_bottom"><input class="field checkbox"  name="chbx_diab" type="checkbox" value="Yes" id="chbx_diab_yes"  tabindex="7" ></td>
											<td onClick="javascript:checkSingle('chbx_diab_no','chbx_diab'),disp_none(document.frm_health_ques.chbx_diab,'diab_yes')" align="left" class="text_10 pad_top_bottom"><input class="field checkbox"  name="chbx_diab" type="checkbox" value="No" id="chbx_diab_no" tabindex="7" ></td>
											<td id="arr_1" onClick="javascript:disp_rev(document.frm_health_ques.chbx_diab,'diab_yes','arr_1')" align="center" valign="top" class="text_10 "  style="padding-top:3px; "><img  src="images/block.gif" width="11" height="13" border="0" style="cursor:hand; " /></td>
										</tr>
										<tr bgcolor="#FFFFFF">
											<td height="22" align="left" valign="middle" class="text_10 pad_top_bottom"><img src="images/tpixel.gif" width="4" height="1">Epilepsy, Convulsions, Parkinson�s, Vertigo</td>
										  <td align="left" class="text_10 pad_top_bottom" onClick="javascript:checkSingle('chbx_epile_yes','chbx_epile')"><input class="field checkbox" type="checkbox" value="Yes" name="chbx_epile" id="chbx_epile_yes" tabindex="7" ></td>
										  <td align="left" class="text_10 pad_top_bottom" onClick="javascript:checkSingle('chbx_epile_no','chbx_epile')"><input class="field checkbox" type="checkbox" value="No" name="chbx_epile" id="chbx_epile_no" tabindex="7" ></td>
										  <td align="left" class="text_10 pad_top_bottom"></td>
										</tr>
										<tr bgcolor="#F1F4F0">
											<td height="22" align="left" valign="middle" class="text_10 pad_top_bottom"><img src="images/tpixel.gif" width="4" height="1">Restless Leg Syndrome</td>
											<td align="left" class="text_10 pad_top_bottom" onClick="javascript:checkSingle('chbx_restless_yes','chbx_restless')"><input class="field checkbox" type="checkbox" value="Yes" name="chbx_restless" id="chbx_restless_yes" tabindex="7" ></td>
											<td align="left" class="text_10 pad_top_bottom" onClick="javascript:checkSingle('chbx_restless_no','chbx_restless')"><input class="field checkbox" type="checkbox" value="No" name="chbx_restless" id="chbx_restless_no" tabindex="7" ></td>
											<td align="left" class="text_10 pad_top_bottom"></td>
										</tr>
										<tr bgcolor="#FFFFFF">
											<td height="22" align="left" valign="middle" class="text_10 pad_top_bottom"><img src="images/tpixel.gif" width="4" height="1">Hepatitis</td>
										  <td align="left" class="text_10 pad_top_bottom" onClick="javascript:checkSingle('chbx_hepat_yes','chbx_hepat')"><input class="field checkbox" type="checkbox" value="Yes" name="chbx_hepat" id="chbx_hepat_yes" tabindex="7" ></td>
										  <td align="left" class="text_10 pad_top_bottom" onClick="javascript:checkSingle('chbx_hepat_no','chbx_hepat')"><input class="field checkbox" type="checkbox" value="No" name="chbx_hepat" id="chbx_hepat_no" tabindex="7" ></td>
										  <td align="left" class="text_10 pad_top_bottom"></td>
										</tr>
										<tr bgcolor="#F1F4F0">
											<td height="22" align="left" valign="middle" class="text_10 pad_top_bottom">
												<div><img src="images/tpixel.gif" width="4" height="1">Kidney Disease, Dialysis</div>
												<table cellpadding="0" cellspacing="0" width="98%" align="center" id="diab_yes1"   style="display:none; " >
													<tr height="22">
														<td width="5%">&nbsp;</td>
														<td width="51%" align="left" valign="top" class="text_10 pad_top_bottom">Do you have a Shunt</td>
													    <td width="13%" align="left" class="text_10 " onClick="checkSingle('chbx_subkidn_yes','chbx_subkidn')"><input class="field checkbox" type="checkbox" value="Yes" name="chbx_subkidn" id="chbx_subkidn_yes" tabindex="7" ></td>
														<td width="31%">&nbsp;</td>
													</tr>
													<tr height="22" >
														<td>&nbsp;</td>
														<td align="left" valign="top" class="text_10">Fistula</td>
														<td align="left" class="text_10 " onClick="checkSingle('chbx_subkidn_no','chbx_subkidn')"><input class="field checkbox" type="checkbox" value="No" name="chbx_subkidn" id="chbx_subkidn_no" tabindex="7" ></td>
														<td width="31%">&nbsp;</td>
													</tr>
											</table>											</td>
											<td onClick="javascript:checkSingle('chbx_kidn_yes','chbx_kidn'),disp(document.frm_health_ques.chbx_kidn,'diab_yes1')" align="left" class="text_10 pad_top_bottom"><input class="field checkbox"  name="chbx_kidn" id="chbx_kidn_yes" type="checkbox" value="Yes"  tabindex="7" ></td>
											<td onClick="javascript:checkSingle('chbx_kidn_no','chbx_kidn'),disp_none(document.frm_health_ques.chbx_kidn,'diab_yes1')" align="left" class="text_10 pad_top_bottom"><input class="field checkbox"  name="chbx_kidn" id="chbx_kidn_no" type="checkbox" value="No"  tabindex="7" ></td>
											<td id="arr_2" onClick="javascript:disp_rev(document.frm_health_ques.chbx_kidn,'diab_yes1','arr_2')" align="center" valign="top" class="text_10 "  style="padding-top:3px; "><img  src="images/block.gif" width="11" height="13" border="0" style="cursor:hand; " /></td>
										</tr>
										<tr bgcolor="#FFFFFF">
											<td height="22" align="left" valign="middle" class="text_10 pad_top_bottom"><img src="images/tpixel.gif" width="4" height="1">HIV, Autoimmune Diseases</td>
											<td align="left" class="text_10 pad_top_bottom" onClick="checkSingle('chbx_hiv_auto_yes','chbx_hiv_auto')"><input class="field checkbox" type="checkbox" value="Yes" name="chbx_hiv_auto" id="chbx_hiv_auto_yes" tabindex="7" ></td>
											<td align="left" class="text_10 pad_top_bottom" onClick="checkSingle('chbx_hiv_auto_no','chbx_hiv_auto')"><input class="field checkbox" type="checkbox" value="No" name="chbx_hiv_auto" id="chbx_hiv_auto_no" tabindex="7" ></td>
											<td align="left" class="text_10 pad_top_bottom"></td>
										</tr>
										<tr bgcolor="#F1F4F0" id="diab_yes2_main">
											<td height="11" align="left" valign="middle" class="text_10 pad_top_bottom">
												<div><img src="images/tpixel.gif" width="2" height="1">History of cancer</div>
												 <table cellpadding="0" cellspacing="0" width="100%" align="center" id="diab_yes2"  style="display:none; " >
													<tr><td colspan="3"><img src="images/tpixel.gif" width="2" height="4"></tr>
													<tr>
														<td width="6%">&nbsp;</td>
														<td width="15%" align="left" valign="top" class="text_10 pad_top_bottom">Describe</td>
													    <td width="79%" align="left" ><textarea id="Field3" class="field textarea justi" style="font-family:verdana; font-size:11px; border:1px solid #cccccc; width:330px; " rows="10" cols="50" tabindex="6"  ></textarea></td>
													</tr>
										  </table> 									
											</td>
										  <td align="left" class="text_10 pad_top_bottom" onClick="javascript:checkSingle('chbx_hist_can_yes','chbx_hist_can'),disp(document.frm_health_ques.chbx_hist_can,'diab_yes2')"><input class="field checkbox"   type="checkbox" value="Yes" name="chbx_hist_can" id="chbx_hist_can_yes" tabindex="7" ></td>
										  <td align="left" class="text_10 pad_top_bottom" onClick="javascript:checkSingle('chbx_hist_can_no','chbx_hist_can'),disp_none(document.frm_health_ques.chbx_hist_can,'diab_yes2')"><input class="field checkbox"   type="checkbox" value="No" name="chbx_hist_can" id="chbx_hist_can_no" tabindex="7" ></td>
										  <td id="arr_3" onClick="javascript:disp_rev(document.frm_health_ques.chbx_hist_can,'diab_yes2','arr_3')" align="center" valign="top" class="text_10 "  style="padding-top:3px; "><img  src="images/block.gif" width="11" height="13" border="0" style="cursor:hand; " /></td>
										</tr>
										
										
										<tr bgcolor="#FFFFFF" >
											<td height="22" align="left" valign="middle" class="text_10 pad_top_bottom">
												<div><img src="images/tpixel.gif" width="2" height="1">Organ Transplant</div>
												<table cellpadding="0" cellspacing="0" width="100%" align="center" id="diab_yes3"  style="display:none; " >
													<tr><td colspan="3"><img src="images/tpixel.gif" width="2" height="4"></tr>
													<tr>
														<td width="6%">&nbsp;</td>
														<td width="15%" align="left" valign="top" class="text_10 pad_top_bottom">Describe</td>
													    <td width="79%" align="left" ><textarea id="Field3" class="field textarea justi" style="font-family:verdana; font-size:11px; border:1px solid #cccccc; width:330px; " rows="10" cols="50" tabindex="6"  ></textarea></td>
													</tr>
										  </table>										  </td>
										  <td align="left" class="text_10 pad_top_bottom" onClick="javascript:checkSingle('chbx_org_trns_yes','chbx_org_trns'),disp(document.frm_health_ques.chbx_org_trns,'diab_yes3')"><input class="field checkbox"  name="chbx_org_trns" type="checkbox" value="Yes" id="chbx_org_trns_yes" tabindex="7" ></td>
										  <td align="left" class="text_10 pad_top_bottom" onClick="javascript:checkSingle('chbx_org_trns_no','chbx_org_trns'),disp_none(document.frm_health_ques.chbx_org_trns,'diab_yes3')"><input class="field checkbox"  name="chbx_org_trns" type="checkbox" value="No" id="chbx_org_trns_no" tabindex="7" ></td>
										  <td id="arr_4" onClick="javascript:disp_rev(document.frm_health_ques.chbx_diab,'diab_yes3','arr_4')" align="center" valign="top" class="text_10 "  style="padding-top:3px; "><img  src="images/block.gif" width="11" height="13" border="0" style="cursor:hand; " /></td>
									  </tr>
										<tr bgcolor="#F1F4F0">
											<td height="22" align="left" valign="middle" class="text_10 pad_top_bottom" ><img src="images/tpixel.gif" width="2" height="1">A Bad Reaction to Local or General Anesthesia</td>
										  <td align="left" class="text_10 pad_top_bottom" onClick="javascript:checkSingle('chbx_bad_react_yes','chbx_bad_react')"><input class="field checkbox" type="checkbox" value="Yes" name="chbx_bad_react" id="chbx_bad_react_yes" tabindex="7" ></td>
										  <td align="left" class="text_10 pad_top_bottom" onClick="javascript:checkSingle('chbx_bad_react_no','chbx_bad_react')"><input class="field checkbox" type="checkbox" value="No" name="chbx_bad_react" id="chbx_bad_react_no" tabindex="7" ></td>
										  <td align="left" class="text_10 pad_top_bottom"></td>
									  </tr>
									  <tr bgcolor="#FFFFFF">
											<td height="22" align="left" valign="middle" class="text_10 pad_top_bottom"><img src="images/tpixel.gif" width="2" height="1">Exposure to Crutzfield-Jacob Disease (Mad Cow)</td>
										  <td align="left" class="text_10 pad_top_bottom" onClick="javascript:checkSingle('chbx_expos_crut_yes','chbx_expos_crut')"><input class="field checkbox" type="checkbox" value="Yes" name="chbx_expos_crut" id="chbx_expos_crut_yes" tabindex="7" ></td>
										  <td align="left" class="text_10 pad_top_bottom" onClick="javascript:checkSingle('chbx_expos_crut_no','chbx_expos_crut')"><input class="field checkbox" type="checkbox" value="No" name="chbx_expos_crut" id="chbx_expos_crut_no" tabindex="7" ></td>
										  <td align="left" class="text_10 pad_top_bottom"></td>
									  </tr>
									  <tr bgcolor="#F1F4F0" >
											<td colspan="4" height="20" align="left" valign="middle" class="text_10 pad_top_bottom">
												
												<table border="0" cellpadding="0" cellspacing="0" width="100%" align="center" id="diab_yes3"   >
													
													<tr>
														
														<td width="17%" align="left" valign="top" class="text_10 pad_top_bottom">Other</td>
													    <td width="83%" align="left" ><textarea id="Field3" class="field textarea justi" style="font-family:verdana; font-size:11px; border:1px solid #cccccc; width:330px; " rows="10" cols="50" tabindex="6"  ></textarea></td>
													</tr>
										  		</table>										  
											</td>
								      </tr>
						  		  </table>	
							  	</td>
								<td><img src="images/tpixel.gif" width="2" height="1"></td>
								
								<td width="470"  valign="top">
									<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
										<tr align="left" bgcolor="#D1E0C9">
											<td height="22" colspan="4" class="text_10b">
												<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
													<tr align="left" bgcolor="#D1E0C9">
														<td height="22"  class="text_10b" nowrap style="color:#BF544F;">
															<img src="images/tpixel.gif" width="4" height="5">Allergies/Drug Reaction
														</td>
														<td height="22"   width="5%" onClick="javascript:checkSingle('chbx_drug_react_no','chbx_drug_react'),txt_disable('txt_field01',
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
														<td height="22"  class="text_10b" style="color:#BF544F;">No Known Allergies</td>
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
														<td height="22"  class="text_10b" style="color:#BF544F;">Allergies Reviewed</td>
													</tr>
												</table>
											</td>
										</tr>
										<tr bgcolor="#FFFFFF">
										  <td  colspan="4" align="left" valign="middle">
										  	<table width="100%" border="0" cellpadding="0" cellspacing="0" bordercolor="#333333" >
												<tr bgcolor="#FFFFFF">
													<td>&nbsp;</td>
													<td width="436" class="text_10  pad_top_bottom">
														Name
													</td>
													
													<td width="512" colspan="4" class="text_10  pad_top_bottom">
														Reaction
													</td>
													
												</tr>
												<tr bgcolor="#F1F4F0">
													<td height="22">&nbsp;</td>
												  <td height="22" class="text_10b"><input type="text" id="txt_field01" class="field text" style=" border:1px solid #ccccc; width:200px; "   tabindex="1" value=""  /></td>
												  <td colspan="4" class="text_10b"><input type="text" id="txt_field02" class="field text" style=" border:1px solid #ccccc; width:200px;" tabindex="1" value=""  /></td>
												</tr>
												<tr bgcolor="#FFFFFF">
													<td width="41" height="22">&nbsp;</td>
												  <td class="text_10b"><input type="text" id="txt_field03" class="field text" style=" border:1px solid #ccccc; width:200px; " tabindex="1" value=""  /></td>
												  <td colspan="4" class="text_10b"><input type="text" id="txt_field04" class="field text" style=" border:1px solid #ccccc; width:200px; " tabindex="1" value=""  /></td>
												</tr>
												<tr bgcolor="#F1F4F0">
													<td height="22">&nbsp;</td>
												  <td class="text_10b"><input type="text" id="txt_field05" class="field text" style=" border:1px solid #ccccc; width:200px;" tabindex="1" value=""  /></td>
												  <td colspan="4" class="text_10b"><input type="text" id="txt_field06" class="field text" style=" border:1px solid #ccccc;width:200px; " tabindex="1" value=""  /></td>
												</tr>
												<tr>
													<td height="22">&nbsp;</td>
												  <td class="text_10b"><input type="text" id="txt_field07" class="field text" style=" border:1px solid #ccccc; width:200px;" tabindex="1" value=""  /></td>
													<td class="text_10b" colspan="4"><input type="text" id="txt_field08" class="field text" style=" border:1px solid #ccccc; width:200px;"tabindex="1" value=""  /></td>
												</tr>
										    </table>
										  
										  </td>
									  </tr>
										  <td  colspan="4" align="left" valign="middle"><img src="images/tpixel.gif" width="4" height="5"></td>
									  
									  <tr align="left" bgcolor="#D1E0C9"><td height="22" colspan="4" class="text_10b" style="color:#BF544F;"><img src="images/tpixel.gif" width="4" height="5">Take Prescription Medications</td>
									  </tr>
										<tr bgcolor="#FFFFFF">
										  <td  colspan="4" align="left" valign="middle">
										  	<table width="100%" border="0" cellpadding="0" cellspacing="0" bordercolor="#333333" >
												<tr bgcolor="#FFFFFF">
													<td height="22">&nbsp;</td>
													<td width="436" class="text_10  pad_top_bottom">
														Name
													</td>
													
													<td width="512" colspan="4" class="text_10  pad_top_bottom">
														Details
													</td>
													
												</tr>
												<tr bgcolor="#F1F4F0">
													<td height="22">&nbsp;</td>
												  <td height="22" class="text_10b"><input type="text" id="Field0" class="field text" style=" border:1px solid #ccccc; width:200px; "   tabindex="1" value=""  /></td>
												  <td colspan="4" class="text_10b"><input type="text" id="Field0" class="field text" style=" border:1px solid #ccccc; width:200px;" tabindex="1" value=""  /></td>
												</tr>
												<tr bgcolor="#FFFFFF">
													<td width="41" height="22">&nbsp;</td>
												  <td class="text_10b"><input type="text" id="Field0" class="field text" style=" border:1px solid #ccccc; width:200px; " tabindex="1" value=""  /></td>
												  <td colspan="4" class="text_10b"><input type="text" id="Field0" class="field text" style=" border:1px solid #ccccc; width:200px; " tabindex="1" value=""  /></td>
												</tr>
												<tr bgcolor="#F1F4F0">
													<td height="22">&nbsp;</td>
												  <td class="text_10b"><input type="text" id="Field0" class="field text" style=" border:1px solid #ccccc; width:200px;" tabindex="1" value=""  /></td>
												  <td colspan="4" class="text_10b"><input type="text" id="Field0" class="field text" style=" border:1px solid #ccccc;width:200px; " tabindex="1" value=""  /></td>
												</tr>
												<tr>
													<td height="22">&nbsp;</td>
												  <td class="text_10b"><input type="text" id="Field0" class="field text" style=" border:1px solid #ccccc; width:200px;" tabindex="1" value=""  /></td>
													<td class="text_10b" colspan="4"><input type="text" id="Field0" class="field text" style=" border:1px solid #ccccc; width:200px;"tabindex="1" value=""  /></td>
												</tr>
										    </table>
										  
										  </td>
									  </tr>	
										<tr bgcolor="#FFFFFF">
										<td height="20"  bgcolor="#D1E0C9" class="text_10b"><img src="images/tpixel.gif" width="5" height="1">Do You</td>
										<td height="20" bgcolor="#D1E0C9" align="left" class="text_10b">Yes</td>
										<td  bgcolor="#D1E0C9" height="20" align="left" class="text_10b">No</td>
									 	<td  bgcolor="#D1E0C9" height="20" align="left" class="text_10b"></td>
									  </tr>
									  <tr bgcolor="#F1F4F0">
											<td width="83%" height="22" align="left" valign="middle" class="text_10 pad_top_bottom"><img src="images/tpixel.gif" width="4" height="1">Use a Wheel Chair, Walker or Cane</td>
											<td width="7%" align="left" valign="top" class="text_10 pad_top_bottom" onClick="javascript:checkSingle('chbx_use_wheel_yes','chbx_use_wheel')"><input class="field checkbox" type="checkbox" value="Yes" name="chbx_use_wheel" id="chbx_use_wheel_yes" tabindex="7" ></td>
											<td width="7%" align="left" valign="top" class="text_10 pad_top_bottom" onClick="javascript:checkSingle('chbx_use_wheel_no','chbx_use_wheel')"><input class="field checkbox" type="checkbox" value="No" name="chbx_use_wheel" id="chbx_use_wheel_no" tabindex="7" ></td>
									  		<td width="3%" align="left" valign="top" class="text_10 pad_top_bottom"></td>
									  </tr>
									  <tr bgcolor="#FFFFFF">
										<td height="22" align="left" valign="middle" class="text_10 pad_top_bottom"><img src="images/tpixel.gif" width="4" height="1">Wear Contact lenses</td>
										<td align="left" valign="top" class="text_10 pad_top_bottom" onClick="javascript:checkSingle('chbx_wear_cont_yes','chbx_wear_cont')"><input class="field checkbox" type="checkbox" value="Yes" name="chbx_wear_cont" id="chbx_wear_cont_yes" tabindex="7" ></td>
										<td align="left" valign="top" class="text_10 pad_top_bottom" onClick="javascript:checkSingle('chbx_wear_cont_no','chbx_wear_cont')"><input class="field checkbox" type="checkbox" value="No" name="chbx_wear_cont" id="chbx_wear_cont_no" tabindex="7" ></td>
									  	<td align="left" valign="top" class="text_10 pad_top_bottom"></td>
									  </tr>
									  <tr bgcolor="#F1F4F0" >
											<td height="22" align="left" valign="middle" class="text_10 pad_top_bottom">
											<div><img src="images/tpixel.gif" width="4" height="1">Smoke</div>
												<table cellpadding="0" cellspacing="0" width="100%" align="center" id="smoke_yes"   style="display:none; ">
													
													<tr align="left" valign="middle">
														<td width="5%" height="22">&nbsp;</td>
														<td width="20%" height="22" class="text_10">How much</td>
													    <td width="57%" height="22" class="text_10 pad_top_bottom"><input type="text" id="Field0" class="field text" size="30" tabindex="1" value="" style="border: 1px solid #cccccc; "  /></td>
														<td width="18%" height="22">&nbsp;</td>
												  </tr>
										  </table>										  </td>
											<td onClick="javascript:checkSingle('chbx_smoke_yes','chbx_smoke'),disp(document.frm_health_ques.chbx_smoke,'smoke_yes')" align="left" class="text_10 pad_top_bottom"><input class="field checkbox"  name="chbx_smoke" type="checkbox" value="Yes" id="chbx_smoke_yes" tabindex="7" ></td>
											<td onClick="javascript:checkSingle('chbx_smoke_no','chbx_smoke'),disp_none(document.frm_health_ques.chbx_smoke,'smoke_yes')" align="left" class="text_10 pad_top_bottom"><input class="field checkbox"  name="chbx_smoke" type="checkbox" value="No" id="chbx_smoke_no" tabindex="7" ></td>
									  		<td id="arr_5" onClick="javascript:disp_rev(document.frm_health_ques.chbx_smoke,'smoke_yes','arr_5')" align="center" valign="top" class="text_10 "  style="padding-top:3px; "><img  src="images/block.gif" width="11" height="13" border="0" style="cursor:hand; " /></td>
									  </tr>
										<tr bgcolor="#FFFFFF" >
											<td height="22" align="left" valign="middle" class="text_10 pad_top_bottom">
											<div><img src="images/tpixel.gif" width="4" height="1">Drink Alcohol</div>
												<table cellpadding="0" cellspacing="0" width="100%" align="center" id="drink_yes"  style="display:none; "  >
													
													<tr align="left" valign="middle" height="25">
														<td width="5%">&nbsp;</td>
														<td width="20%" class="text_10 ">How much</td>
													    <td width="54%" height="22" class="text_10 pad_top_bottom"><input type="text" id="Field0" class="field text" size="30" tabindex="1" value="" style="border: 1px solid #cccccc; "  /></td>
														<td width="21%">&nbsp;</td>
												  </tr>
										  </table>										  </td>
										  <td align="left" class="text_10 pad_top_bottom" onClick="javascript:checkSingle('chbx_drink_yes','chbx_drink'),disp(document.frm_health_ques.chbx_drink,'drink_yes')"><input class="field checkbox"  name="chbx_drink" type="checkbox" value="Yes" id="chbx_drink_yes" tabindex="7" ></td>
										  <td align="left" class="text_10 pad_top_bottom" onClick="javascript:checkSingle('chbx_drink_no','chbx_drink'),disp_none(document.frm_health_ques.chbx_drink,'drink_yes')"><input class="field checkbox"  name="chbx_drink" type="checkbox" value="No" id="chbx_drink_no" tabindex="7" ></td>
										  <td id="arr_6" onClick="javascript:disp_rev(document.frm_health_ques.chbx_drink,'drink_yes','arr_6')" align="center" valign="top" class="text_10 "  style="padding-top:3px; "><img  src="images/block.gif" width="11" height="13" border="0" style="cursor:hand; " /></td>	
										</tr>
										<tr bgcolor="#F1F4F0">
											<td height="22" align="left" valign="middle" class="text_10 pad_top_bottom"><img src="images/tpixel.gif" width="4" height="1">Have an automatic internal defibrillator</td>
											<td align="left" valign="top" class="text_10 pad_top_bottom" onClick="javascript:checkSingle('chbx_hav_auto_int_yes','chbx_hav_auto_int')"><input class="field checkbox" type="checkbox" value="Yes" name="chbx_hav_auto_int" id="chbx_hav_auto_int_yes" tabindex="7" ></td>
											<td align="left" valign="top" class="text_10 pad_top_bottom" onClick="javascript:checkSingle('chbx_hav_auto_int_no','chbx_hav_auto_int')"><input class="field checkbox" type="checkbox" value="No" name="chbx_hav_auto_int" id="chbx_hav_auto_int_no" tabindex="7" ></td>
											<td align="left" valign="top" class="text_10 pad_top_bottom"></td>
									  </tr>
									  <tr bgcolor="#FFFFFF">
											<td height="22" align="left" valign="middle" class="text_10 pad_top_bottom">
												<div><img src="images/tpixel.gif" width="2" height="1">Have any Metal Prosthetics</div>
												<table cellpadding="0" cellspacing="0" width="100%" align="center" id="pro_yes3"  style="display:none; "  >
													<tr><td colspan="3"><img src="images/tpixel.gif" width="2" height="4"></tr>
													<tr align="left" valign="top">
														<td width="6%">&nbsp;&nbsp;&nbsp;</td>
														<td width="19%" class="text_10 pad_top_bottom">  Notes </td>
													    <td width="75%"><textarea id="Field3" class="field textarea justi" style="font-family:verdana; font-size:11px; border:1px solid #cccccc; " rows="10" cols="50" tabindex="6"  ></textarea></td>
														
													</tr>
										  </table>										  </td>
										  <td align="left" class="text_10 pad_top_bottom" onClick="javascript:checkSingle('chbx_hav_any_met_yes','chbx_hav_any_met'),disp(document.frm_health_ques.chbx_hav_any_met,'pro_yes3')"><input class="field checkbox"  name="chbx_hav_any_met" type="checkbox" value="Yes" id="chbx_hav_any_met_yes" tabindex="7" ></td>
										  <td align="left" class="text_10 pad_top_bottom" onClick="javascript:checkSingle('chbx_hav_any_met_no','chbx_hav_any_met'),disp_none(document.frm_health_ques.chbx_hav_any_met,'pro_yes3')"><input class="field checkbox"  name="chbx_hav_any_met" type="checkbox" value="No" id="chbx_hav_any_met_no" tabindex="7" ></td>
										  <td id="arr_7" onClick="javascript:disp_rev(document.frm_health_ques.chbx_hav_any_met,'pro_yes3','arr_7')" align="center" valign="top" class="text_10 " style="padding-top:3px; "><img  src="images/block.gif" width="11" height="13" border="0" style="cursor:hand; " /></td>	
									  </tr>
					  		  </table>							  </td>
							  <td><img src="images/tpixel.gif" width="4" height="1"></td>
							</tr>
					</table>
			  </form>
		 </td>
	</tr>
	<tr>
		<td> <img src="images/tpixel.gif" width="1" height="5"></td>
	</tr>
	<tr>
		<td valign="top">
			<table align="center" cellpadding="0" cellspacing="0" width="99%"  bgcolor="#FFFFFF" class="all_border"  >
				<tr><td colspan="8"><img src="images/tpixel.gif" width="1" height="5"></td></tr>
				<tr align="left" valign="middle" bgcolor="#F1F4F0">
					<td width="12%" class="text_10"><img src="images/tpixel.gif" width="10" height="1">Patient Signature</td>
				  <td width="17%"><input type="text"  class="all_border" style="height:50px; width:150px; " disabled></td>
					<td width="5%" class="text_10"><img src="images/tpixel.gif" width="10" height="1">Date</td>
				  <td width="8%" bgcolor="#F1F4F0" class="text_10"><input type="text"  class="all_border"style="width:90px; "  >
			      </td>
				  <td width="6%"><img src="images/tpixel.gif" width="7" height="1"><img src="images/icon_cal.jpg" width="20" height="20" border="0" align="middle" ></td>
					<td width="18%" class="text_10"><img src="images/tpixel.gif" width="10" height="1">Emergency Contact Person</td>
				  <td width="17%" class="text_10"><input type="text"  class="all_border"style="width:150px; "  ></td>
					<td width="3%" class="text_10"> Tel.</td>
				  <td width="14%" class="text_10"><input type="text"  class="all_border"style="width:120px; "  ></td>
			  </tr>
				<tr><td colspan="8"><img src="images/tpixel.gif" width="1" height="5"></td></tr>
		  </table>
		</td>
	</tr>
	<tr>
		<td> <img src="images/tpixel.gif" width="1" height="10"></td>
	</tr>
	<tr>
		<td valign="top">
			<table width="99%" border="0" align="center" cellpadding="0" cellspacing="0">
				<tr >
					<td align="right" valign="top">
						<input type="button" class="button" style="width:70px; " value="Save">
						<input type="button" class="button" style="width:70px; " value="Cancel">
						<input type="button" class="button" style="width:70px; " value="Print">
						<input type="button" class="button" style="width:70px; " value="Save & Print"></td>
					<td align="right" valign="top"><img src="images/logo1.gif" width="168" height="24"></td>
				</tr>	
		  </table>		
		</td>
	</tr>
	<tr>
		<td ><img src="images/tpixel.gif" width="1" height="5"></td>
	</tr>
</table>
</div>
</body>
</html>