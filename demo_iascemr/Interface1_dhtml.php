<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
$top=155;
include("common/header.php");
?>

	<tr>
		<td ><img src="images/tpixel.gif" width="1" height="3"></td>
	</tr>
	<tr>
		<td valign="top"  >
		
			<table width="99%" align="center" height="25" border="0" cellpadding="0" cellspacing="0" bgcolor="#B3DD8C" class="all_border">
				<tr bgcolor="#EFEFEF" >
					<td align="left" class="text_10b1" ><img src="images/tpixel.gif" width="5">Patient Name</td>
					<td align="left" bgcolor="#FFFFFF" class="text_10"><img src="images/tpixel.gif" width="2">William R Duphorn</td>
				
					<td align="left" class="text_10b1"><img src="images/tpixel.gif" width="2">ASC-ID</td>
					<td align="left" bgcolor="#FFFFFF" class="text_10"><img src="images/tpixel.gif" width="2">9998499854</td>
				
					<td align="left" class="text_10b1"><img src="images/tpixel.gif" width="2">Date</td>
					<td align="left" bgcolor="#FFFFFF" class="text_10"><img src="images/tpixel.gif" width="2">03-19-2008</td>
					
					<td align="left" class="text_10b1"><img src="images/tpixel.gif" width="2">Nurse</td>
					<td align="left" bgcolor="#FFFFFF" class="text_10"><img src="images/tpixel.gif" width="2">Micky James</td>
					
					<td align="left" class="text_10b1"><img src="images/tpixel.gif" width="2">Progress Notes</td>
					<td align="left" bgcolor="#FFFFFF" class="text_10"><img src="images/tpixel.gif" width="2">Special test is Ok</td>
					
					<td align="left" class="text_10b1"><img src="images/tpixel.gif" width="2">Time</td>
					<td align="left" bgcolor="#FFFFFF" class="text_10"><img src="images/tpixel.gif" width="2">16:27</td>
			  </tr>
	 	  </table>
		</td>
	</tr>	
	<tr>
		<td ><img src="images/tpixel.gif" width="1" height="5"></td>
	</tr>
	<tr>
		<td  valign="top" align="center">
			<table width="30%" border="0" cellpadding="0" cellspacing="0">
							<tr>
								<td width="3" align="right"><img src="images/left.gif" width="3" height="24"></td>
								<td valign="middle" bgcolor="#BCD2B0" align="center" class="text_10b" >Pre-Op General Anesthesia Record</td>
							  <td align="left" valign="top" width="3"><img src="images/right.gif" width="3" height="24"></td>
							</tr>
		  </table>
		</td>
	</tr>
	<tr>
	  <td><img src="images/tpixel.gif" width="4" height="1"></td>

	</tr>
	
	<tr>
		<td bgcolor="#ECF1EA"  >
		  
		<form name="frm_pre_gn_an_rec" class="wufoo topLabel" enctype="multipart/form-data" method="post" style="margin:0px; " action="">
				  		<table border="0"  width="99%" align="center" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" class="all_border ">
							<tr>
								<td width="4"><img src="images/tpixel.gif" width="4" height="1"></td>
								<td width="371"  valign="top">
									<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
										<tr bgcolor="#D1E0C9">
											<td height="20" class="text_10b"><img src="images/tpixel.gif" width="15" height="1">Medical History</td>
											<td height="20" align="left" class="text_10b">Yes</td>
											<td width="6%" height="20" align="left" class="text_10b">No</td>
									  		<td align="left" class="text_10 pad_top_bottom"></td>
									  </tr>
										<tr bgcolor="#F1F4F0">
											<td height="22" align="left" valign="middle" class="text_10 pad_top_bottom"><img src="images/tpixel.gif" width="4" height="1">Heart Problem</td>
											<td align="left" valign="top" class="text_10 pad_top_bottom" onClick="checkSingle('chbx_hp_yes','chbx_hp')"><input class="field checkbox" type="checkbox" value="Yes" name="chbx_hp" id="chbx_hp_yes" tabindex="7" ></td>
											<td align="left" valign="top" class="text_10 pad_top_bottom" onClick="checkSingle('chbx_hp_no','chbx_hp')"><input class="field checkbox" type="checkbox" value="No" name="chbx_hp" id="chbx_hp_no" tabindex="7" ></td>
											<td align="left" class="text_10 pad_top_bottom"></td>
									  </tr>
										<tr>
											<td height="22" align="left" valign="middle" class="text_10 pad_top_bottom"><img src="images/tpixel.gif" width="4" height="1">High Blood Pressure</td>
											<td align="left" class="text_10 pad_top_bottom" onClick="checkSingle('chbx_hbp_yes','chbx_hbp')"><input class="field checkbox" type="checkbox" value="Yes" name="chbx_hbp" id="chbx_hbp_yes" tabindex="7" ></td>
											<td align="left" class="text_10 pad_top_bottom" onClick="checkSingle('chbx_hbp_no','chbx_hbp')"><input class="field checkbox" type="checkbox" value="No" name="chbx_hbp" id="chbx_hbp_no" tabindex="7" ></td>
											<td align="left" class="text_10 pad_top_bottom"></td>
										</tr>
										<tr bgcolor="#F1F4F0">
											<td height="22" align="left" valign="middle" class="text_10 pad_top_bottom"><img src="images/tpixel.gif" width="4" height="1">Stroke</td>
											<td align="left" class="text_10 pad_top_bottom" onClick="checkSingle('chbx_stroke_yes','chbx_stroke')"><input class="field checkbox" type="checkbox" value="Yes" name="chbx_stroke" id="chbx_stroke_yes" tabindex="7" ></td>
											<td align="left" class="text_10 pad_top_bottom" onClick="checkSingle('chbx_stroke_no','chbx_stroke')"><input class="field checkbox" type="checkbox" value="No" name="chbx_stroke" id="chbx_stroke_no" tabindex="7" ></td>
											<td align="left" class="text_10 pad_top_bottom"></td>
										</tr>
										<tr bgcolor="#FFFFFF">
											<td width="82%" height="22" align="left" valign="middle" class="text_10 pad_top_bottom"><img src="images/tpixel.gif" width="4" height="1">Diabetes</td>
										  <td width="9%" align="left" class="text_10 pad_top_bottom" onClick="checkSingle('chbx_db_yes','chbx_db')"><input class="field checkbox" type="checkbox" value="Yes" name="chbx_db" id="chbx_db_yes" tabindex="7" ></td>
										  <td width="6%" align="left" class="text_10 pad_top_bottom" onClick="checkSingle('chbx_db_no','chbx_db')"><input class="field checkbox" type="checkbox" value="No" name="chbx_db" id="chbx_db_no" tabindex="7" ></td>
										  <td width="3%" align="left" class="text_10 pad_top_bottom"></td>
										</tr>
										
										<tr bgcolor="#F1F4F0">
											<td height="22" align="left" valign="middle" class="text_10 pad_top_bottom"><img src="images/tpixel.gif" width="4" height="1">Bleeding Problems</td>
											<td align="left" class="text_10 pad_top_bottom" onClick="checkSingle('chbx_ble_prb_yes','chbx_ble_prb')"><input class="field checkbox" type="checkbox" value="Yes" name="chbx_ble_prb" id="chbx_ble_prb_yes" tabindex="7" ></td>
											<td align="left" class="text_10 pad_top_bottom" onClick="checkSingle('chbx_ble_prb_no','chbx_ble_prb')"><input class="field checkbox" type="checkbox" value="No" name="chbx_ble_prb" id="chbx_ble_prb_no" tabindex="7" ></td>
											<td align="left" class="text_10 pad_top_bottom"></td>
										</tr>
										<tr bgcolor="#FFFFFF">
											<td height="22" align="left" valign="middle" class="text_10 pad_top_bottom"><img src="images/tpixel.gif" width="4" height="1">Asthma Lung Disease</td>
										  <td align="left" class="text_10 pad_top_bottom" onClick="checkSingle('chbx_ast_ln_dis_yes','chbx_ast_ln_dis')"><input class="field checkbox" type="checkbox" value="Yes" name="chbx_ast_ln_dis" id="chbx_ast_ln_dis_yes" tabindex="7" ></td>
										  <td align="left" class="text_10 pad_top_bottom" onClick="checkSingle('chbx_ast_ln_dis_no','chbx_ast_ln_dis')"><input class="field checkbox" type="checkbox" value="No" name="chbx_ast_ln_dis" id="chbx_ast_ln_dis_no" tabindex="7" ></td>
										  <td align="left" class="text_10 pad_top_bottom"></td>
										</tr>
										<tr bgcolor="#F1F4F0">
											<td height="22" align="left" valign="middle" class="text_10 pad_top_bottom"><img src="images/tpixel.gif" width="4" height="1">Hiatal Hernia</td>
											<td align="left" class="text_10 pad_top_bottom" onClick="checkSingle('chbx_hia_her_yes','chbx_hia_her')"><input class="field checkbox" type="checkbox" value="Yes" name="chbx_hia_her" id="chbx_hia_her_yes" tabindex="7" ></td>
											<td align="left" class="text_10 pad_top_bottom" onClick="checkSingle('chbx_hia_her_no','chbx_hia_her')"><input class="field checkbox" type="checkbox" value="No" name="chbx_hia_her" id="chbx_hia_her_no" tabindex="7" ></td>
											<td align="left" class="text_10 pad_top_bottom"></td>
										</tr>
							  		</table>	
							  	</td>
								<td width="3"><img src="images/tpixel.gif" width="2" height="1"></td>
								
								<td width="371"  valign="top">
									<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
										 <tr align="left" bgcolor="#D1E0C9">
											<td height="22" colspan="4" class="text_10b">
												<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
													<tr bgcolor="#FFFFFF">
														<td height="20"  bgcolor="#D1E0C9" class="text_10b"><img src="images/tpixel.gif" width="5" height="1"></td>
														<td height="20" bgcolor="#D1E0C9" align="left" class="text_10b">Yes</td>
														<td  bgcolor="#D1E0C9" height="20" align="left" class="text_10b">No</td>
														<td  bgcolor="#D1E0C9" height="20" align="left" class="text_10b"></td>
												    </tr>
													<tr bgcolor="#F1F4F0">
													  <td width="82%" height="22" align="left" valign="middle" class="text_10 pad_top_bottom"><img src="images/tpixel.gif" width="4" height="1">Liver or Kidney Disease</td>
													  <td width="9%" align="left" class="text_10 pad_top_bottom" onClick="checkSingle('chbx_liv_kd_yes','chbx_liv_kd')"><input class="field checkbox" type="checkbox" value="Yes" name="chbx_liv_kd" id="chbx_liv_kd_yes" tabindex="7" ></td>
													  <td width="7%" align="left" class="text_10 pad_top_bottom" onClick="checkSingle('chbx_liv_kd_no','chbx_liv_kd')"><input class="field checkbox" type="checkbox" value="No" name="chbx_liv_kd" id="chbx_liv_kd_no" tabindex="7" ></td>
													  <td width="2%" align="left" class="text_10 pad_top_bottom"></td>
													</tr>
													<tr bgcolor="#FFFFFF">
														<td height="22" align="left" valign="middle" class="text_10 pad_top_bottom"><img src="images/tpixel.gif" width="4" height="1">Motion Sickness</td>
														<td align="left" class="text_10 pad_top_bottom" onClick="checkSingle('chbx_mot_sic_yes','chbx_mot_sic')"><input class="field checkbox" type="checkbox" value="Yes" name="chbx_mot_sic" id="chbx_mot_sic_yes" tabindex="7" ></td>
														<td align="left" class="text_10 pad_top_bottom" onClick="checkSingle('chbx_mot_sic_no','chbx_mot_sic')"><input class="field checkbox" type="checkbox" value="No" name="chbx_mot_sic" id="chbx_mot_sic_no" tabindex="7" ></td>
														<td align="left" class="text_10 pad_top_bottom"></td>
													</tr>
													<tr bgcolor="#F1F4F0">
														<td height="22" align="left" valign="middle" class="text_10 pad_top_bottom"><img src="images/tpixel.gif" width="4" height="1">Thyroid Disease</td>
													  <td align="left" class="text_10 pad_top_bottom" onClick="checkSingle('chbx_thy_dis_yes','chbx_thy_dis')"><input class="field checkbox" type="checkbox" value="Yes" name="chbx_thy_dis" id="chbx_thy_dis_yes" tabindex="7" ></td>
													  <td align="left" class="text_10 pad_top_bottom" onClick="checkSingle('chbx_thy_dis_no','chbx_thy_dis')"><input class="field checkbox" type="checkbox" value="No" name="chbx_thy_dis" id="chbx_thy_dis_no" tabindex="7" ></td>
													  <td align="left" class="text_10 pad_top_bottom"></td>
													</tr>
													<tr bgcolor="#FFFFFF">
														<td height="22" align="left" valign="middle" class="text_10 pad_top_bottom"><img src="images/tpixel.gif" width="4" height="1">Seizures, Fainting</td>
														<td align="left" class="text_10 pad_top_bottom" onClick="checkSingle('chbx_sei_fai_yes','chbx_sei_fai')"><input class="field checkbox" type="checkbox" value="Yes" name="chbx_sei_fai" id="chbx_sei_fai_yes" tabindex="7" ></td>
														<td align="left" class="text_10 pad_top_bottom" onClick="checkSingle('chbx_sei_fai_no','chbx_sei_fai')"><input class="field checkbox" type="checkbox" value="No" name="chbx_sei_fai" id="chbx_sei_fai_no" tabindex="7" ></td>
														<td align="left" class="text_10 pad_top_bottom"></td>
													</tr>
													<tr bgcolor="#F1F4F0">
														<td height="22" align="left" valign="middle" class="text_10 pad_top_bottom"><img src="images/tpixel.gif" width="4" height="1">Neurological Disease (E.G.)</td>
													  <td align="left" class="text_10 pad_top_bottom" onClick="checkSingle('chbx_neur_dis_yes','chbx_neur_dis')"><input class="field checkbox" type="checkbox" value="Yes" name="chbx_neur_dis" id="chbx_neur_dis_yes" tabindex="7" ></td>
													  <td align="left" class="text_10 pad_top_bottom" onClick="checkSingle('chbx_neur_dis_no','chbx_neur_dis')"><input class="field checkbox" type="checkbox" value="No" name="chbx_neur_dis" id="chbx_neur_dis_no" tabindex="7" ></td>
													  <td align="left" class="text_10 pad_top_bottom"></td>
													</tr>
													<tr bgcolor="#FFFFFF">
														<td height="22" align="left" valign="middle" class="text_10 pad_top_bottom"><img src="images/tpixel.gif" width="4" height="1">Mental Disease</td>
														<td align="left" class="text_10 pad_top_bottom" onClick="checkSingle('chbx_ment_dis_yes','chbx_ment_dis')"><input class="field checkbox" type="checkbox" value="Yes" name="chbx_ment_dis" id="chbx_ment_dis_yes" tabindex="7" ></td>
														<td align="left" class="text_10 pad_top_bottom" onClick="checkSingle('chbx_ment_dis_no','chbx_ment_dis')"><input class="field checkbox" type="checkbox" value="No" name="chbx_ment_dis" id="chbx_ment_dis_no" tabindex="7" ></td>
														<td align="left" class="text_10 pad_top_bottom"></td>
													</tr>
												</table>
											</td>
											
										</tr>
					  		  </table>							 
							 </td>
							 <td width="3"><img src="images/tpixel.gif" width="2" height="1"></td>
							 <td width="268"  valign="top">
									<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
										 <tr align="left" bgcolor="#D1E0C9">
											<td height="22" colspan="4" class="text_10b">
												<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
													<tr bgcolor="#FFFFFF">
														<td colspan="3" height="20"  bgcolor="#D1E0C9" class="text_10b"><img src="images/tpixel.gif" width="5" height="1"></td>
												    </tr>
													  <tr bgcolor="#F1F4F0" valign="top">
															<td height="20" align="left" valign="middle" class="text_10" nowrap><img src="images/tpixel.gif" width="7" height="1">Last Menstrual Period</td>
															<td height="20" align="left" valign="middle" class="text_10"><input type="text"  class="field text" size="30" style="width:90px;border: 1px solid #cccccc; "  ></td>
															<td height="20" align="left" valign="middle" class="text_10"><img src="images/tpixel.gif" width="7" height="1"><img src="images/icon_cal.jpg" width="20" height="20" border="0" align="middle" ></td>
															
													  </tr>
													  <tr bgcolor="#FFFFFF" >
															<td height="20" align="left" valign="middle" class="text_10"><img src="images/tpixel.gif" width="7" height="1">Pregnant, Due Date</td>
															<td height="20" align="left" valign="middle" class="text_10"><input type="text"  class="field text" size="30" style="width:90px;border: 1px solid #cccccc; "  ></td>
															<td height="20" align="left" valign="middle" class="text_10"><img src="images/tpixel.gif" width="7" height="1"><img src="images/icon_cal.jpg" width="20" height="20" border="0" align="middle" ></td>
													  </tr>	
													  <tr bgcolor="#F1F4F0" >
														<td colspan="3" height="20" align="left" valign="middle" class="text_10 pad_top_bottom">
															
															<table border="0" cellpadding="0" cellspacing="0" width="100%" align="center" id="diab_yes3"   >
																
																<tr>
																	
																	<td width="17%" align="left" valign="top" class="text_10 pad_top_bottom"><img src="images/tpixel.gif" width="4" height="1">Other</td>
																	<td width="83%" align="left" ><textarea id="Field3" class="field textarea justi" style="font-family:verdana; font-size:11px; border:1px solid #cccccc; width:220px; " rows="10" cols="50" tabindex="6"  ></textarea></td>
																</tr>
															</table>										  
														</td>
												  </tr> 												
												</table>
											</td>
											
										</tr>
					  		  </table>							 
							 </td>
							 
							  <td width="4"><img src="images/tpixel.gif" width="4" height="1"></td>
							</tr>
							<tr bgcolor="#FFFFFF">
								<td valign="top" colspan="7">
									<table border="0" align="center" cellpadding="0" cellspacing="0" width="99%"  bgcolor="#FFFFFF"   >
										<tr><td colspan="9"><img src="images/tpixel.gif" width="1" height="5"></td></tr>
										<tr  height="20">
											<td width="347" class="text_10b1"  valign="middle" >
												<!-- <img src="images/tpixel.gif" width="3" height="5">Current to Medications -->
												<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
													 <tr align="left" bgcolor="#D1E0C9">
														<td height="22" colspan="4" class="text_10b">
															<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
																<tr align="left" bgcolor="#D1E0C9">
																	<td width="75%" height="22" nowrap  class="text_10b" style="color:#BF544F;">
																		<img src="images/tpixel.gif" width="4" height="5">Allergies to Medications 
																	</td>
																	<td height="22"   width="7%" onClick="javascript:txt_rev('chbx_curr_to_med_id',
																																	'txt_atm_field01',
																																	'txt_atm_field02',
																																	'txt_atm_field03',
																																	'txt_atm_field04',
																																	'txt_atm_field05',
																																	'txt_atm_field06',
																																	'txt_atm_field07',
																																	'txt_atm_field08');">
																		<img src="images/tpixel.gif" width="4" height="5">
																		<input class="checkbox"  type="checkbox" value="No" id="chbx_curr_to_med_id" name="chbx_curr_to_med"  tabindex="7" >
																	</td>
																	<td width="18%" height="22"  class="text_10b" style="color:#BF544F;">None</td>
																	
																</tr>
															</table>
														</td>
													</tr>
													<tr bgcolor="#FFFFFF">
													  <td  colspan="4" align="left" valign="middle">
														<table  width="100%" border="0" cellpadding="0" cellspacing="0" bordercolor="#333333" >
															<tr bgcolor="#FFFFFF">
																<td>&nbsp;</td>
																<td width="436" class="text_10  pad_top_bottom">
																	Name
																</td>
																
																<td width="512" colspan="4" class="text_10  pad_top_bottom">
																	Details
																</td>
																
															</tr>
															<tr bgcolor="#F1F4F0">
																<td height="22">&nbsp;</td>
															  <td height="22" class="text_10b"><input type="text" id="txt_atm_field01" class="field text" style=" border:1px solid #ccccc; width:160px; "   tabindex="1" value=""  /></td>
															  <td colspan="4" class="text_10b"><input type="text" id="txt_atm_field02" class="field text" style=" border:1px solid #ccccc; width:160px;" tabindex="1" value=""  /></td>
															</tr>
															<tr bgcolor="#FFFFFF">
																<td width="5" height="22">&nbsp;</td>
															  <td class="text_10b"><input type="text" id="txt_atm_field03" class="field text" style=" border:1px solid #ccccc; width:160px; " tabindex="1" value=""  /></td>
															  <td colspan="4" class="text_10b"><input type="text" id="txt_atm_field04" class="field text" style=" border:1px solid #ccccc; width:160px; " tabindex="1" value=""  /></td>
															</tr>
															<tr bgcolor="#F1F4F0">
																<td height="22">&nbsp;</td>
															  <td class="text_10b"><input type="text" id="txt_atm_field05" class="field text" style=" border:1px solid #ccccc; width:160px;" tabindex="1" value=""  /></td>
															  <td colspan="4" class="text_10b"><input type="text" id="txt_atm_field06" class="field text" style=" border:1px solid #ccccc;width:160px; " tabindex="1" value=""  /></td>
															</tr>
															<tr>
																<td height="22">&nbsp;</td>
															  <td class="text_10b"><input type="text" id="txt_atm_field07" class="field text" style=" border:1px solid #ccccc; width:160px;" tabindex="1" value=""  /></td>
																<td class="text_10b" colspan="4"><input type="text" id="txt_atm_field08" class="field text" style=" border:1px solid #ccccc; width:160px;"tabindex="1" value=""  /></td>
															</tr>
														</table>
													  
													  </td>
												  </tr> 
					  		  					</table>												
											</td>
											<!-- <td width="1"><img src="images/tpixel.gif" width="1" height="1"></td> -->
											<td width="357"  class="text_10b1" bgcolor="#FFFFFF" valign="top">
												<!-- <img src="images/tpixel.gif" width="3" height="5">Do You -->
											  <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
												<tr align="left" bgcolor="#D1E0C9">
														<td height="22" colspan="4" class="text_10b">
															<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
																<tr align="left" bgcolor="#D1E0C9">
																	<td width="75%" height="22" nowrap  class="text_10b" style="color:#BF544F;">
																		<img src="images/tpixel.gif" width="4" height="5">Current Medications
																	</td>
																	<td height="22"   width="7%" onClick="javascript:txt_rev('chbx_curr_to_med_id',
																																	'txt_ctm_field01',
																																	'txt_ctm_field02',
																																	'txt_ctm_field03',
																																	'txt_ctm_field04',
																																	'txt_ctm_field05',
																																	'txt_ctm_field06',
																																	'txt_ctm_field07',
																																	'txt_ctm_field08');">
																		<img src="images/tpixel.gif" width="4" height="5">
																		<input class="checkbox"  type="checkbox" value="No" id="chbx_curr_to_med_id" name="chbx_curr_to_med"  tabindex="7" >
																	</td>
																	<td width="18%" height="22"  class="text_10b" style="color:#BF544F;">None</td>
																	
																</tr>
															</table>
														</td>
													</tr>
													<tr bgcolor="#FFFFFF">
													  <td  colspan="4" align="left" valign="middle">
														<table  width="100%" border="0" cellpadding="0" cellspacing="0" bordercolor="#333333" >
															<tr bgcolor="#FFFFFF">
																<td>&nbsp;</td>
																<td width="166" class="text_10  pad_top_bottom">
																	Name
																</td>
																
																<td width="180" colspan="4" class="text_10  pad_top_bottom">
																	Reaction
																</td>
																
															</tr>
															<tr bgcolor="#F1F4F0">
																<td height="22">&nbsp;</td>
															  <td height="22" class="text_10b"><input type="text" id="txt_ctm_field01" class="field text" style=" border:1px solid #ccccc; width:160px; "   tabindex="1" value=""  /></td>
															  <td colspan="4" class="text_10b"><input type="text" id="txt_ctm_field02" class="field text" style=" border:1px solid #ccccc; width:160px;" tabindex="1" value=""  /></td>
															</tr>
															<tr bgcolor="#FFFFFF">
																<td width="5" height="22">&nbsp;</td>
															  <td class="text_10b"><input type="text" id="txt_ctm_field03" class="field text" style=" border:1px solid #ccccc; width:160px; " tabindex="1" value=""  /></td>
															  <td colspan="4" class="text_10b"><input type="text" id="txt_ctm_field04" class="field text" style=" border:1px solid #ccccc; width:160px; " tabindex="1" value=""  /></td>
															</tr>
															<tr bgcolor="#F1F4F0">
																<td height="22">&nbsp;</td>
															  <td class="text_10b"><input type="text" id="txt_ctm_field05" class="field text" style=" border:1px solid #ccccc; width:160px;" tabindex="1" value=""  /></td>
															  <td colspan="4" class="text_10b"><input type="text" id="txt_ctm_field06" class="field text" style=" border:1px solid #ccccc;width:160px; " tabindex="1" value=""  /></td>
															</tr>
															<tr>
																<td height="22">&nbsp;</td>
															  <td class="text_10b"><input type="text" id="txt_ctm_field07" class="field text" style=" border:1px solid #ccccc; width:160px;" tabindex="1" value=""  /></td>
																<td class="text_10b" colspan="4"><input type="text" id="txt_ctm_field08" class="field text" style=" border:1px solid #ccccc; width:160px;"tabindex="1" value=""  /></td>
															</tr>
														</table>
													  </td>
												  </tr> 
												</table>  
											</td>
											<!-- <td width="1"><img src="images/tpixel.gif" width="1" height="1"></td> -->
											<td width="270"  class="text_10b1">
												<!-- <img src="images/tpixel.gif" width="1" height="5">Do You -->
												<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
												<tr align="left" bgcolor="#D1E0C9">
														<td height="22" colspan="4" class="text_10b">
															<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
																<tr align="left" bgcolor="#D1E0C9">
																	<td width="75%" height="22" nowrap  class="text_10b" style="color:#BF544F;">
																		<img src="images/tpixel.gif" width="4" height="5">Previous Operations
																	</td>
																	<td height="22"   width="8%" onClick="javascript:txt_rev('chbx_curr_to_med_id',
																																	'txt_pr_op_field01',
																																	'txt_pr_op_field02',
																																	'txt_pr_op_field03',
																																	'txt_pr_op_field04',
																																	'txt_pr_op_field05',
																																	'txt_pr_op_field06',
																																	'txt_pr_op_field07',
																																	'txt_pr_op_field08');">
																		<img src="images/tpixel.gif" width="4" height="5">
																		<input class="checkbox"  type="checkbox" value="No" id="chbx_curr_to_med_id" name="chbx_curr_to_med"  tabindex="7" >
																	</td>
																	<td width="17%" height="22"  class="text_10b" style="color:#BF544F;">None</td>
																	
																</tr>
															</table>
														</td>
													</tr>
													<tr bgcolor="#FFFFFF">
													  <td  colspan="4" align="left" valign="middle">
														<table  width="100%" border="0" cellpadding="0" cellspacing="0" bordercolor="#333333" >
															<tr bgcolor="#FFFFFF">
																<td>&nbsp;</td>
																<td width="436" class="text_10  pad_top_bottom">
																	Name
																</td>
																
																<td width="512" colspan="4" class="text_10  pad_top_bottom">
																	Reason
																</td>
																
															</tr>
															<tr bgcolor="#F1F4F0">
																<td height="22">&nbsp;</td>
															  <td height="22" class="text_10b"><input type="text" id="txt_pr_op_field01" class="field text" style=" border:1px solid #ccccc; width:125px; "   tabindex="1" value=""  /></td>
															  <td colspan="4" class="text_10b"><input type="text" id="txt_pr_op_field02" class="field text" style=" border:1px solid #ccccc; width:125px;" tabindex="1" value=""  /></td>
															</tr>
															<tr bgcolor="#FFFFFF">
																<td width="41" height="22">&nbsp;</td>
															  <td class="text_10b"><input type="text" id="txt_pr_op_field03" class="field text" style=" border:1px solid #ccccc; width:125px; " tabindex="1" value=""  /></td>
															  <td colspan="4" class="text_10b"><input type="text" id="txt_pr_op_field04" class="field text" style=" border:1px solid #ccccc; width:125px; " tabindex="1" value=""  /></td>
															</tr>
															<tr bgcolor="#F1F4F0">
																<td height="22">&nbsp;</td>
															  <td class="text_10b"><input type="text" id="txt_pr_op_field05" class="field text" style=" border:1px solid #ccccc; width:125px;" tabindex="1" value=""  /></td>
															  <td colspan="4" class="text_10b"><input type="text" id="txt_pr_op_field06" class="field text" style=" border:1px solid #ccccc;width:125px; " tabindex="1" value=""  /></td>
															</tr>
															<tr>
																<td height="22">&nbsp;</td>
															  <td class="text_10b"><input type="text" id="txt_pr_op_field07" class="field text" style=" border:1px solid #ccccc; width:125px;" tabindex="1" value=""  /></td>
																<td class="text_10b" colspan="4"><input type="text" id="txt_pr_op_field08" class="field text" style=" border:1px solid #ccccc; width:125px;"tabindex="1" value=""  /></td>
															</tr>
														</table>
													  
													  </td>
												  </tr>
												</table>
											</td>
											<!-- <td width="2" ><img src="images/tpixel.gif" width="4" height="1"></td> -->
										</tr>
									</table>
								</td>
							</tr>	
							<tr bgcolor="#FFFFFF">
								<td valign="top" colspan="7">
									<table border="0" align="center" cellpadding="0" cellspacing="0" width="99%"  bgcolor="#FFFFFF"   >
										<tr><td colspan="9"><img src="images/tpixel.gif" width="1" height="5"></td></tr>
										<tr  height="20">
											<td bgcolor="#D1E0C9" colspan="4" class="text_10b1">
												<img src="images/tpixel.gif" width="3" height="5">Problem w/Previous Anesthesia
											</td>
											<td bgcolor="#FFFFFF" width="5" class="text_10b1">
												<img src="images/tpixel.gif" width="3" height="5">
											</td>
											<td colspan="4" bgcolor="#D1E0C9" class="text_10b1">
												<img src="images/tpixel.gif" width="3" height="5">Family History Of Anesthesia Problems
											</td>
										</tr>
										<tr  height="20" bgcolor="#F1F4F0">
											<td width="202"    class="text_10b1" align="left">
												<table border="0" cellpadding="0" cellspacing="0" width="100%" align="left" >
													<tr>
														<td width="17%" align="left" valign="top" class="text_10 pad_top_bottom"><img src="images/tpixel.gif" width="4" height="1">None</td>
														<td width="10%" align="left" onClick="checkSingle('chbx_prob_pre_anes_yes','chbx_prob_pre_anes')"><input class="field checkbox" type="checkbox" value="Yes" name="chbx_prob_pre_anes" id="chbx_prob_pre_anes_yes" tabindex="7" ></td>
														<!-- <td rowspan="2" width="73%" align="left" ><textarea id="Field3" class="field textarea justi" style="font-family:verdana; font-size:11px; border:1px solid #cccccc; width:250px; " rows="10" cols="50" tabindex="6"  ></textarea></td> -->
													</tr>
													<tr>
														<td width="17%" align="left" valign="top" class="text_10 pad_top_bottom"><img src="images/tpixel.gif" width="4" height="1">Yes</td>
														<td width="10%" align="left" onClick="checkSingle('chbx_prob_pre_anes_no','chbx_prob_pre_anes')"><input class="field checkbox" type="checkbox" value="No" name="chbx_prob_pre_anes" id="chbx_prob_pre_anes_no" tabindex="7" ></td>
														
													</tr>
												</table>
											</td>
											<td colspan="3"   class="text_10b1" align="left">
												<textarea id="Field3" class="field textarea justi" style="font-family:verdana; font-size:11px; border:1px solid #cccccc; width:250px; " rows="10" cols="50" tabindex="6"  ></textarea>
											</td>
											<td  width="5" class="text_10b1" bgcolor="#FFFFFF">
												<img src="images/tpixel.gif" width="3" height="5">
											</td>
											<td width="212"  class="text_10b1">
												
												<table border="0" cellpadding="0" cellspacing="0" width="100%" align="left" >
													<tr>
														<td width="17%" align="left" valign="top" class="text_10 pad_top_bottom"><img src="images/tpixel.gif" width="4" height="1">None</td>
														<td width="10%" align="left" onClick="checkSingle('chbx_fam_hist_anes_yes','chbx_fam_hist_anes')"><input class="field checkbox" type="checkbox" value="Yes" name="chbx_fam_hist_anes" id="chbx_fam_hist_anes_yes" tabindex="7" ></td>
														<!-- <td rowspan="2" width="73%" align="left" ><textarea id="Field3" class="field textarea justi" style="font-family:verdana; font-size:11px; border:1px solid #cccccc; width:250px; " rows="10" cols="50" tabindex="6"  ></textarea></td> -->
													</tr>
													<tr>
														<td width="17%" align="left" valign="top" class="text_10 pad_top_bottom"><img src="images/tpixel.gif" width="4" height="1">Yes</td>
														<td width="10%" align="left" onClick="checkSingle('chbx_fam_hist_anes_no','chbx_fam_hist_anes')"><input class="field checkbox" type="checkbox" value="No" name="chbx_fam_hist_anes" id="chbx_fam_hist_anes_no" tabindex="7" ></td>
														
													</tr>
												</table>
											</td> 
											<td width="284" colspan="3" align="left"   class="text_10b1">
												<textarea id="Field3" class="field textarea justi" style="font-family:verdana; font-size:11px; border:1px solid #cccccc; width:250px; " rows="10" cols="50" tabindex="6"  ></textarea>
											</td>
										</tr>
									</table>			
								</td>
							</tr>		
							<tr bgcolor="#FFFFFF">
								<td valign="top" colspan="7">
									<table border="0" align="center" cellpadding="0" cellspacing="0" width="99%"  bgcolor="#FFFFFF"   >
										<tr><td colspan="9"><img src="images/tpixel.gif" width="1" height="5"></td></tr>
										<tr bgcolor="#D1E0C9" height="20"><td colspan="9" class="text_10b1"><img src="images/tpixel.gif" width="3" height="5">Do You</td></tr>
										<tr align="left" valign="middle" bgcolor="#F1F4F0">
											<td class="text_10b"><img src="images/tpixel.gif" width="3" height="5">Smoke</td>
											<td class="text_10" colspan="8">
												<table width="100%" border="0" cellpadding="0" cellspacing="0">
													<tr>
														<td colspan="15"><img src="images/tpixel.gif" width="1" height="3"></td>
													</tr>
													<tr height="20" >
														<td valign="middle" class="text_10" width="29"><img src="images/tpixel.gif" width="1" height="5"></td>
														<td valign="middle" class="text_10" width="30" bgcolor="#F1F4F0" align="right">No</td>
														<td valign="middle"  width="27" onClick="chk_unchk_smoke('chbx_no','chbx_cigaret','chbx_cigar','chbx_pipe','packday','no_year','stop_when');"><input class="field checkbox" type="checkbox" value="Yes" name="chbx_no" id="chbx_no" tabindex="7" ></td>
														<td valign="middle" class="text_10" width="79" align="right">Cigarettes</td>
														<td valign="middle"  width="47"><input class="field checkbox" type="checkbox" value="Yes" name="chbx_cigaret" id="chbx_cigaret" tabindex="7" ></td>
														<td valign="middle" class="text_10" width="63" align="right">Cigars</td>
														<td valign="middle" class="text_10" width="27" ><input class="field checkbox" type="checkbox" value="Yes" name="chbx_cigar" id="chbx_cigar" tabindex="7" ></td>
														<td valign="middle" class="text_10" width="45" align="right">Pipe</td>
														<td valign="middle" class="text_10" width="27"><input class="field checkbox" type="checkbox" value="Yes" name="chbx_pipe" id="chbx_pipe" tabindex="7" ></td>
														<td valign="middle" class="text_10" width="92" align="right">Packs/Day</td>
														<td valign="middle" class="text_10" width="45" ><input type="text"  class="field text" maxlength="2"  size="10" id="packday" style="width:20px;border: 1px solid #cccccc; "  >##</td>
														<td  class="text_10" width="105" nowrap align="right">No. of Years</td>
														<td valign="middle" class="text_10" width="54"><input type="text"  class="field text" maxlength="2"  size="10" id="no_year" style="width:20px;border: 1px solid #cccccc; "  >##</td>
														<td  class="text_10" width="137" nowrap align="right">If Stopped when</td>
														<td valign="middle" class="text_10" width="90" nowrap><input type="text"  class="field text"  size="20" id="stop_when" style="width:60px;border: 1px solid #cccccc; "  ><img src="images/tpixel.gif" width="7" height="1"><img src="images/icon_cal.jpg" width="20" height="20" border="0" align="middle" ></td>
														
														
													</tr>
													
													
													<tr>
														<td colspan="15"><img src="images/tpixel.gif" width="1" height="3"></td>
													</tr>
												</table>
												
											</td>
											<!-- <td class="text_10"><input class="field checkbox" type="checkbox" value="Yes" name="chbx_ment_dis" id="Field7" tabindex="7" ></td>
											<td class="text_10"><input class="field checkbox" type="checkbox" value="Yes" name="chbx_ment_dis" id="Field7" tabindex="7" ></td>
											 -->
										</tr>
										<tr align="left" valign="middle" bgcolor="#FFFFFF">
											<td class="text_10b"><img src="images/tpixel.gif" width="3" height="5">Alcohol</td>
											<td class="text_10" colspan="8">
												<table width="100%" border="0" cellpadding="0" cellspacing="0">
													<tr>
														<td colspan="15"><img src="images/tpixel.gif" width="1" height="3"></td>
													</tr>
													<tr height="20" >
														<td valign="middle" class="text_10" width="27"><img src="images/tpixel.gif" width="1" height="5"></td>
														<td valign="middle" class="text_10" width="30" align="right">No</td>
														<td valign="middle"  width="28" onClick="chk_unchk_alcohol('chbx_acl_no','chbx_acl_yes','chbx_acl_dr_wek','chbx_acl_2to4','chbx_acl_4to6','acl_number');"><input class="field checkbox" type="checkbox" value="Yes" name="chbx_acl_no" id="chbx_acl_no" tabindex="7" ></td>
														<td valign="middle" class="text_10" width="79" align="right">Yes</td>
														<td valign="middle"  width="47"><input class="field checkbox" type="checkbox" value="Yes" name="chbx_acl_yes" id="chbx_acl_yes" tabindex="7" ></td>
														<td valign="middle" class="text_10" width="135" align="right">Drinks/Week	0 � 2 </td>
														<td valign="middle" class="text_10" width="30" ><input class="field checkbox" type="checkbox" value="Yes" name="chbx_acl_dr_wek" id="chbx_acl_dr_wek" tabindex="7" ></td>
														<td valign="middle" class="text_10" width="92" align="right">2 � 4</td>
														<td valign="middle" class="text_10" width="25"><input class="field checkbox" type="checkbox" value="Yes" name="chbx_acl_2to4" id="chbx_acl_2to4" tabindex="7" ></td>
														<td valign="middle" class="text_10" width="122" align="right"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;4 � 6</td>
														<td valign="middle" class="text_10" width="21"><input class="field checkbox" type="checkbox" value="Yes" name="chbx_acl_4to6" id="chbx_acl_4to6" tabindex="7" ></td>
														
														<td  class="text_10" width="170" nowrap align="right">Number</td>
														<td valign="middle" class="text_10" width="83"><input type="text"  class="field text" maxlength="2"  size="10" id="acl_number" style="width:20px;border: 1px solid #cccccc; "  >##</td>
														<td width="5" colspan="2"></td>
													</tr>
													
													
													<tr>
														<td colspan="15"><img src="images/tpixel.gif" width="1" height="3"></td>
													</tr>
												</table>
												
											</td>
										</tr>
										<tr align="left" valign="middle" bgcolor="#F1F4F0">
											<td class="text_10b"><img src="images/tpixel.gif" width="3" height="5">Dentures</td>
											<td class="text_10" colspan="8">
												<table width="100%" border="0" cellpadding="0" cellspacing="0">
													<tr>
														<td colspan="15"><img src="images/tpixel.gif" width="1" height="3"></td>
													</tr>
													<tr height="20" >
														<td valign="middle" class="text_10" width="31"><img src="images/tpixel.gif" width="1" height="5"></td>
														<td valign="middle" class="text_10" width="24" align="right"></td>
														<td valign="middle"  width="26" ><input class="field checkbox" type="checkbox" value="Yes" name="chbx_dnt_capteth" id="Field7" tabindex="7" ></td>
														<td valign="middle" class="text_10" width="136" align="left">Capped Teeth</td>
														<td valign="middle"  width="24"><input class="field checkbox" type="checkbox" value="Yes" name="chbx_dnt_prmbrig" id="Field7" tabindex="7" ></td>
														<td valign="middle" class="text_10" width="170" align="left">Permanent Bridge</td>
														<td valign="middle" class="text_10" width="24" ><input class="field checkbox" type="checkbox" value="Yes" name="chbx_dnt_lbt" id="Field7" tabindex="7" ></td>
														<td valign="middle" class="text_10" width="174" align="left">Loose or Broken Teeth</td>
														<td valign="middle" class="text_10" width="23"><input class="field checkbox" type="checkbox" value="Yes" name="chbx_dnt_odp" id="Field7" tabindex="7" ></td>
														<td valign="middle" class="text_10" width="253" align="left"> Other Dental Problems</td>
													</tr>
													
													
													<tr>
														<td colspan="15"><img src="images/tpixel.gif" width="1" height="3"></td>
													</tr>
												</table>
												
											</td>
										</tr>
										<tr><td colspan="9"><img src="images/tpixel.gif" width="1" height="5"></td></tr>
								  </table>
								</td>
						</tr>
						<tr bgcolor="#FFFFFF">
							<td valign="top" colspan="7">
								<table border="0" align="center" cellpadding="0" cellspacing="0" width="99%"  bgcolor="#FFFFFF"   >
									<tr><td colspan="9"><img src="images/tpixel.gif" width="1" height="5"></td></tr>
									<tr bgcolor="#D1E0C9" height="20">
										<td colspan="2" class="text_10b1" width="7%">
											<img src="images/tpixel.gif" width="3" height="5">P.E
										</td>
										<td colspan="2" class="text_10b1" width="31%">
											<img src="images/tpixel.gif" width="3" height="5">Airways
										</td>
										<td colspan="2" class="text_10b1" width="31%">
											<img src="images/tpixel.gif" width="3" height="5">Heart
										</td>
										<td colspan="3" class="text_10b1" width="31%">
											<img src="images/tpixel.gif" width="3" height="5">Lungs
										</td>
									</tr>
									<tr bgcolor="#F1F4F0" height="20">
										<td colspan="2" rowspan="2" class="text_10">
											<img src="images/tpixel.gif" width="3" height="5">LAB
										</td>
									</tr>
									<tr bgcolor="#F1F4F0" height="20">
										<td colspan="9" class="text_10">
											<img src="images/tpixel.gif" width="3" height="5">
											<table cellpadding="0" cellspacing="0" border="0">
												<tr bgcolor="#FFFFFF" height="20" class="text_10">
													<td width="16%"><img src="images/tpixel.gif" width="3" height="5"><b>HGB</b> 123.1</td>
													<td width="16%"><b>HCT</b> 123.1</td>
													<td width="16%"><b>WBC</b> 456.1</td>
													<td width="16%"><b>X10<sup>3</sup> Platelets</b> 789.1</td>
													<td width="16%"><b>X10<sup>5</sup> Platelets</b> 123.1</td>
													<td width="16%"><b>DIFF</b> 456.1</td>
												</tr>
												<tr bgcolor="#F1F4F0" height="20" class="text_10">
													<td width="16%"><img src="images/tpixel.gif" width="3" height="5"><b>URINE</b></td>
													<td width="16%">
														<table border="0" cellpadding="0" cellspacing="0" >
															<tr class="text_10" height="20">
																<td width="3%"><b>OK</b></td>
																<td width="13%"><input class="checkbox" type="checkbox" value="Yes" name="chbx_urine_ok" id="Field7" tabindex="7" ></td>
																
															</tr>
														</table>
													</td>
													<td width="20%">
														<table border="0" cellpadding="0" cellspacing="0" >
															<tr class="text_10" height="20">
																<td width="3%"><b>INR</b></td>
																<td width="13%">123.1</td>
															</tr>
														</table>
													</td>
													<td width="16%"><b>PT</b></td>
													<td width="16%"><b>SEC</b></td>
													<td width="8%">
														<table border="0" cellpadding="0" cellspacing="0" >
															<tr class="text_10" height="20">
																<td width="8%"><b>PTT</b></td>
																<td width="8%"><b>SEC</b></td>
																
															</tr>
														</table>
													</td>
													<td width="16%">&nbsp;&nbsp;&nbsp;<img src="images/tpixel.gif" width="3" height="5"></td>
													
												</tr>
												<tr bgcolor="#FFFFFF" height="20" class="text_10">
													<td width="16%"><img src="images/tpixel.gif" width="3" height="5"><b>BMP</b> 123.1</td>
													<td width="16%"><b>FBS</b> 456.1</td>
													<td width="16%">
														<table border="0" cellpadding="0" cellspacing="0" >
															<tr class="text_10" height="20">
																<td width="8%"><b>Ns</b> 789.1</td>
																<td width="8%"><b>Cl</b> 123.1</td>
																
															</tr>
														</table>
													</td>
													<td width="20%">
														<table border="0" cellpadding="0" cellspacing="0" >
															<tr class="text_10" height="20">
																<td width="10%" nowrap><b>ROOM AIR</b></td>
																<td width="8%" align="right"><img src="images/tpixel.gif" width="3" height="5">Yes<img src="images/tpixel.gif" width="5" height="5"></td>
																<td width="6%" onClick="checkSingle('chbx_rm_air_yes','chbx_rm_air')"><input class="checkbox" type="checkbox" value="Yes" name="chbx_rm_air" id="chbx_rm_air_yes" tabindex="7" ></td>
																<td width="6%" align="right"><img src="images/tpixel.gif" width="3" height="5">No<img src="images/tpixel.gif" width="5" height="5"></td>
																<td width="8%" onClick="checkSingle('chbx_rm_air_no','chbx_rm_air')"><input class="checkbox" type="checkbox" value="No" name="chbx_rm_air" id="chbx_rm_air_no" tabindex="7" ></td>
															</tr>
														</table>
													</td>
													<td width="16%"><b>O3</b> 453.1%</td>
													<td width="16%"><b>O<sub>2</sub></b> SAT  456.1</td>
												</tr>
												<tr bgcolor="#F1F4F0" height="20" class="text_10">
													<td width="16%"><img src="images/tpixel.gif" width="3" height="5"><b>BMP</b> 123.1</td>
													<td width="16%"><b>BUN</b> 456.1</td>
													<td width="20%">
														<table border="0" cellpadding="0" cellspacing="0" >
															<tr class="text_10" height="20">
																<td width="8%"><b>HC03</b> 789.1</td>
																<td width="8%"><b>P0<sub>2</sub></b> 123.1</td>
																
															</tr>
														</table>
													</td>
													<td width="20%">
														<table border="0" cellpadding="0" cellspacing="0" >
															<tr class="text_10" height="20">
																<td width="8%" nowrap><b>PCC<sub>2</sub></b> 123.1</td>
																<td width="8%" align="right"><img src="images/tpixel.gif" width="3" height="5"></td>
																<td width="6%"></td>
																<td nowrap width="12%" align="right"><img src="images/tpixel.gif" width="3" height="5"><b>pH</b> 123.1</td>
																<td width="8%"></td>
															</tr>
														</table>
													</td>
													<td width="16%"><b>HCO<sub>3</sub></b> 579.1%</td>
													<td width="16%"><b>BE</b> 493.1</td>
												</tr>
											</table>
										</td>
									</tr>
									<tr bgcolor="#FFFFFF" height="20">
										<td colspan="3" class="text_10">
											<img src="images/tpixel.gif" width="3" height="5">CXR
										</td>
										<td colspan="6" class="text_10">
											<input type="text" id="txt_ctm_field01" class="field text" style=" border:1px solid #ccccc; width:650px; "   tabindex="1" value=""  />
										</td>
										
									</tr>	
									<tr bgcolor="#F1F4F0" height="20">
										<td colspan="3" class="text_10">
											<img src="images/tpixel.gif" width="3" height="5">ECG
										</td>
										<td colspan="6" class="text_10">
											<input type="text" id="txt_ctm_field01" class="field text" style=" border:1px solid #ccccc; width:650px; "   tabindex="1" value=""  />
										</td>
									</tr>
									<tr bgcolor="#FFFFFF" height="20">
										<td colspan="3" class="text_10">
											<img src="images/tpixel.gif" width="3" height="5">OTHER
										</td>
										<td colspan="6" class="text_10">
											<input type="text" id="txt_ctm_field01" class="field text" style=" border:1px solid #ccccc; width:650px; "   tabindex="1" value=""  />
										</td>
									</tr>
									<tr bgcolor="#F1F4F0" height="20">
										<td colspan="3" class="text_10">
											<img src="images/tpixel.gif" width="3" height="5">PMED & TIME
										</td>
										<td colspan="6" class="text_10">
											<input type="text" id="txt_ctm_field01" class="field text" style=" border:1px solid #ccccc; width:650px; "   tabindex="1" value=""  />
										</td>
									</tr>
									<tr bgcolor="#FFFFFF" height="24">
										<td colspan="3" class="text_10" >
											<img src="images/tpixel.gif" width="3" height="5">BP RANGE
										</td>
										<td  class="text_10" width="100" align="right">
											<img src="images/tpixel.gif" width="3" height="5">TPR
										</td>
										<td colspan="5" class="text_10">
											<!-- <img src="images/tpixel.gif" width="3" height="5">PS -->
											<table border="0" cellpadding="0" cellspacing="0" >
												<tr class="text_10" height="20">
													<td width="2%" nowrap>PS</td>
													<td width="1%" nowrap align="right">1<img src="images/tpixel.gif" width="5" height="5"></td>
													<td width="1%" align="left"><img src="images/tpixel.gif" width="5" height="5"><input class="checkbox" type="checkbox" value="Yes" name="chbx_ps1" id="Field7" tabindex="7" ></td>
													<td width="1%" nowrap align="right">2<img src="images/tpixel.gif" width="5" height="5"></td>
													<td width="1%" align="right"><img src="images/tpixel.gif" width="5" height="5"><input class="checkbox" type="checkbox" value="Yes" name="chbx_ps1" id="Field7" tabindex="7" ></td>
													<td width="1%" nowrap align="right">3<img src="images/tpixel.gif" width="5" height="5"></td>
													<td width="1%" align="right"><img src="images/tpixel.gif" width="5" height="5"><input class="checkbox" type="checkbox" value="Yes" name="chbx_ps1" id="Field7" tabindex="7" ></td>
													<td width="1%" nowrap align="right">4<img src="images/tpixel.gif" width="5" height="5"></td>
													<td width="1%" align="right"><img src="images/tpixel.gif" width="5" height="5"><input class="checkbox" type="checkbox" value="Yes" name="chbx_ps1" id="Field7" tabindex="7" ></td>
													<td width="1%" nowrap align="right">5<img src="images/tpixel.gif" width="5" height="5"></td>
													<td width="1%" align="right"><img src="images/tpixel.gif" width="5" height="5"><input class="checkbox" type="checkbox" value="Yes" name="chbx_ps1" id="Field7" tabindex="7" ></td>
													<td width="1%" nowrap align="right">6<img src="images/tpixel.gif" width="5" height="5"></td>
													<td width="1%" align="right"><img src="images/tpixel.gif" width="5" height="5"><input class="checkbox" type="checkbox" value="Yes" name="chbx_ps1" id="Field7" tabindex="7" ></td>
													<td width="1%" nowrap align="right">E<img src="images/tpixel.gif" width="5" height="5"></td>
													<td width="1%" align="right"><img src="images/tpixel.gif" width="5" height="5"><input class="checkbox" type="checkbox" value="Yes" name="chbx_ps1" id="Field7" tabindex="7" ></td>
													
													<td width="2%"></td>
													<td nowrap width="2%" align="right"></td>
													<td width="2%"></td>
												</tr>
											</table>
										</td>
									</tr>
									
									<tr bgcolor="#F1F4F0" height="20">
										<td colspan="2" class="text_10" nowrap>
											<img src="images/tpixel.gif" width="3" height="5">PROPOSED ANESTHESIA<img src="images/tpixel.gif" width="3" height="5">
										</td>
										<td colspan="2" class="text_10">
											<input type="text" id="txt_ctm_field01" class="field text" style=" border:1px solid #ccccc; width:200px; "   tabindex="1" value=""  />
										</td>
										<td colspan="5" class="text_10">
											<img src="images/tpixel.gif" width="3" height="5">LAST ORAL INTAKE
											<img src="images/tpixel.gif" width="3" height="5">
											<input type="text" id="txt_ctm_field01" class="field text" style=" border:1px solid #ccccc; width:200px; "   tabindex="1" value=""  />
										</td>
										
									</tr>
									
								</table>
							</td>
						</tr>	
								
	  	</table>
	  </form>
	  </td>
	</tr>
	<tr>
		<td> <img src="images/tpixel.gif" width="1" height="2"></td>
	</tr>
	<tr>
		<td valign="top">
			<table align="center" cellpadding="0" cellspacing="0" width="99%"  bgcolor="#FFFFFF" class="all_border"  >
				<tr><td colspan="8"><img src="images/tpixel.gif" width="1" height="5"></td></tr>
				<tr align="left" valign="middle" bgcolor="#F1F4F0">
					<td class="text_10b"><img src="images/tpixel.gif" width="10" height="1">PREOPERATIVE COMPLICATIONS</td>
					<td colspan="8" class="text_10">
					<textarea id="Field3" class="field textarea justi" style="font-family:verdana; font-size:11px; border:1px solid #cccccc; width:600px; " rows="10" cols="50" tabindex="6"  ></textarea>
					</td>
				  
			  </tr>
				<tr><td colspan="8"><img src="images/tpixel.gif" width="1" height="5"></td></tr>
		  </table>
		</td>
	</tr>
	<tr>
		<td> <img src="images/tpixel.gif" width="1" height="5"></td>
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