<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title><?php echo date('m-d-Y');?></title>
<link href="<?php echo $GLOBALS['webroot'];?>/library/css/bootstrap.min.css" rel="stylesheet">
<!-- including javascript function file common to provider module-->
<style>
	body, td, th{font-size:12px;}
	th{background-color:#999999;}
	.text_10b{font-weight:bold;}
</style>
<script>
function do_print(){
	window.print();
}
</script>
</head>
<body >

<?php
if(empty($encounter_id)){
?>
<table>
	
	<tr>
		<td align="center">
			<table border="1" cellpadding="0" cellspacing="0" >
				<tr >
					<td colspan="6"  align="left">Patient Encounter's Account</td>
				</tr>
				<tr height="15px">
					<td colspan="6"  align="center">&nbsp;</td>
				</tr>
				<tr  height="20" bgcolor="#999999">
					<td align="center" >#</td>
					<td align="center"  width="75">Form Id</td>
					<td align="center"  width="100">Case</td>
					<td align="center"  width="75">E. Id</td>
					<td align="center"  width="100">DOS</td>
					<td align="center"  width="100">Ref. Physician</td>
					<td align="center"  width="100">&nbsp;</td>
				</tr>
				<?php
				$numRows = count($arr_pt_sb);
				if($numRows<=0){
					?>
					<tr>
						<td align="center" colspan="6" class="text_10">No Record Found.</td>
					</tr>
					<?php	
				}else{
					for($i=0; $i<$numRows; $i++){
						$formId=$arr_pt_sb[$i]['formId'];
						$vipStatus=$arr_pt_sb[$i]['vipSuperBill'];
						$encounterId=$arr_pt_sb[$i]['encounterId'];
						$refferingPhysician=$arr_pt_sb[$i]['refferingPhysician'];
						
						$ins_case=$arr_pt_sb[$i]['ins_case'];
						$date_of_service=$arr_pt_sb[$i]['dateOfService'];
						
						$seq = $i+1;
					?>
					<tr height="15px" <?php if($seq%2!=0){ ?> bgcolor="#FFFFFF" <?php } ?>>
						<td align="center" >
							<?php echo $seq; ?>
						</td>
						<td align="center" >
							<?php echo $formId; ?>
						</td>
						<td align="center" >
							<?php echo $ins_case; ?>
						</td>
						<td align="center" >
							<?php echo $encounterId; ?>
						</td>
						<td align="center" >
							<?php echo $dateOfService; ?>
						</td>
						<td align="center" >
							<?php echo $refferingPhysician; ?>
						</td>
						<td width="150" align="center" >
							<?php
								$url = $GLOBALS["rootdir"]."//chart_notes/requestHandler.php?elem_formAction=SuperBill_Print&e_id=".$encounterId."&neww=1";
							?>
							<a  href="<?php echo $url; ?>">
								Make Superbill
							</a>
						</td>
					</tr>
				<?php
				}
			}	
			?>
			</table>
		</td>	
	</tr>
</table>

<?php
}else{
?>

<table border="0" width="100%" cellpadding="0" cellspacing="0">
<!--	<tr height="25px">
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td><a class="text_10b" href="superbill_printing.php">[Back]</a></td>
	</tr>
 -->
	<tr>
		<td align="center">
			<table width="100%" border="1" cellpadding="0" cellspacing="0" bordercolor="Gainsboro">
				<tr height="20">
					<th  >Encounter Details</th>
					<th   >PRACTICE Details</th>					
				</tr>
				<tr>
					<td align="center">
						<table width="100%" border="0">
							<tr><td>
								<!-- 1st Row -->
								<table width="100%" border="0" cellpadding="0" cellspacing="0">
								<tr>
									<td width="10%" align="left" class="text_10b">DOS:</td>
									<td width="23%" align="left" class="text_9"><?php echo $dos; ?></td>
									    <td width="29%">
										<table width="98%" cellpadding="0" cellspacing="0">
											<tr>
											<td width="5%" align="left" class="text_10b">Time:</td>
											<td width="6%" align="left" class="text_9"><?php echo $time; ?></td>
											<td align="left" width="10%" class="text_10b">
												<input type="checkbox" class="text_9" <?php if($isVip) echo "checked"; ?> />VIP 
											</td>
										    </tr>
										</table>
									    </td>
									<td width="11%" align="left" class="text_10b">Physician:</td>
									<td align="left" class="text_9"><?php echo $physicianName; ?></td>
								</tr>
								<tr><td style="height:5px;"></td></tr>
								<tr>
									<td align="left" class="text_10b">Encounter:</td>
									<td align="left" class="text_9"><?php echo $encounter_id; ?></td>
									    <td>
										<table width="94%" cellpadding="0" cellspacing="0">
											<tr>
											<td width="9%" align="left" class="text_10b">TOS:</td>
											<td width="12%"  align="left" class="text_9"><?php if($type_of_service) echo $type_of_service; else echo "-"; ?></td>
											<td align="left" width="7%" class="text_10b">POS:</td>
											<td width="10%"  align="left" class="text_9">
																		<?php if($place_of_service) echo $place_of_service; else echo "-"; ?>
											</td>
										    </tr>
										</table>
									    </td>
									    <td align="left" class="text_10b">Notes:</td>
									<td align="left" class="text_9"><?php echo $patientNotes; ?></td>
								</tr>
								</table>
								<!-- 2nd Row -->							
							</td></tr>
							<tr><td style="height:5px;"></td></tr>
						</table>
					</td>
					<td rowspan="5" align="left" valign="middle" style="padding-left:30px;">						
						<table align="left" border="0" cellpadding="0" cellspacing="0">							
							<tr>
								<td align="left">
									<?php										
										if(isset($facilityLogo) && !empty($facilityLogo)){
											echo "<img src=\"".$GLOBALS['webroot']."/data/".PRACTICE_PATH."/facilitylogo/".$facilityLogo."\" alt=\"Facility\" width=\"100\" />";	
										}
									?>
								</td>
							</tr>
							<tr><td style="height:5px;"></td></tr>						
							<tr>
								<td align="left" class="text_10">
									<?php echo "<b>".$facilityName."</b>"; ?>
								</td>
							</tr>
							<tr><td style="height:15px;"></td></tr>							
							<tr>
								<td align="left" class="text_10" >
									<?php echo $facilityStreet."<br>".$facilityCity.", ".$facilityState." ".$facilityZip; ?>
								</td>
							</tr>							
							<tr>
								<td align="left" class="text_10" >
									<?php echo "<b>Ph. # </b> ".$facilityPhone."<br>"."<b>Fax # </b> ".$facilityFax; ?>
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr height="20" >
					<th width="674" >Patient Details</th>
				</tr>
				<tr>
					<td align="center" valign="top">
						<table width="100%" border="0">
							<tr><td>
								<!-- 1st -->
								<table cellpadding="0" cellspacing="0" width="100%" border="0">
								<tr>
									<td width="10%" align="left" class="text_10b">Name:</td>
									<td  align="left" width="20%" class="text_9"><?php echo $patientName." - ".$patient_id; ?></td>
									<td width="8%" align="left" class="text_10b">Sex:</td>
									<td align="left" width="18%" class="text_9"><?php echo $patientSex; ?></td>
									<td width="6%" align="left" class="text_10b">Age:</td>
									<td width="28%" align="left" class="text_9"><?php echo $age." Yrs."; ?></td>
								</tr>
								<tr><td style="height:5px;"></td></tr>
								<tr>	
									<td align="left" class="text_10b">Ref. Phy.:</td><!--P. Phy.:-->
									<td align="left" class="text_9"><?php echo $referrer; ?></td>
									<?php //echo $primaryCare; ?>
									<td align="left" class="text_10b">Ph. #:</td><!--Ref. Phy.:-->
									<td align="left" class="text_9"><?php echo $phoneHome;//echo $referrer; ?></td>
									<td align="left" class="text_10b"></td><!--Ph. #:-->
									<td align="left" class="text_9"><?php //echo $phoneHome; ?></td>
								</tr>
								<tr><td style="height:5px;"></td></tr>
								</table>
								<!-- 2nd -->
							</td></tr>
						</table>
					</td>
				</tr>
				<tr class='text_b' height="20" bgcolor="#999999">
					<th width="674" >Responsible Party</th>
				</tr>
				<tr>
					<td align="center">
						<table width="100%" border="0">
							<tr><td>
							<!-- 1st Row -->
							<table cellpadding="0" cellspacing="0" width="100%" border="0">
							<tr valign="top">
								<td width="8%"  align="left" class="text_10b">Name:</td>
								<td align="left" width="16%" class="text_9"><?php echo $ref_name; ?></td>
								<td width="8%" align="left" class="text_10b">Home #:</td>
								<td align="left" width="14%" class="text_9"><?php echo ($ref_homePh); ?></td>
								<td width="12%" align="left" class="text_10b">Mobile/Work:</td>
								<td width="16%" align="left" class="text_9"><?php echo ($ref_workPh); ?></td>
							</tr>
							<tr><td style="height:5px;"></td></tr>
							<tr valign="top">
								<td align="left" class="text_10b">Address:</td>
								<td align="left" class="text_9"><?php echo $ref_address."<br> ".$ref_city.", ".$ref_state." ".$ref_postal_code;?></td>
								<td align="left" class="text_10b">Pt. Status:</td>
								<td align="left" class="text_9"><?php echo $patientStatus; ?></td>
								<td align="left" class="text_10b">Financial Status:</td>
								<td align="left" class="text_9"><?php echo $financialStatus; ?></td>
							</tr>
							<tr><td style="height:5px;"></td></tr>
							</table>
							<!-- 2nd Row -->
							</td></tr>							
						</table>
					</td>
				</tr>
				<tr height="20" bgcolor="#999999">
					<th style="width:804px;">Insurance <?php echo " (".$caseName.")"; ?></th>
					<th style="width:auto;">Financials</th>
				</tr>
				<tr>
					<td align="center" valign="top">
						<table width="100%" border="1" align="center" bordercolor="#FFFFFF" cellpadding="0" cellspacing="0">
							<tr class='text_b' height="20" bgcolor="#CCCCCC">
								<td align="center" class="text_10b">#</td>
								<td align="center" class="text_10b">Insurance</td>
								<td align="center" class="text_10b">Policy & Group</td>
								<td align="center" class="text_10b">Copay</td>
								<td align="center" class="text_10b">Start Date</td>
								<td align="center" class="text_10b">End Date</td>
							</tr>
							<?php
							
							if(count($arr_Ins_info)>0){
							$sr=1;
							foreach($arr_Ins_info as $ins_type => $ar_ins){
								extract($ar_ins);	
							
							?>
							
							<tr class="text_9">
								<td align="center"><?php echo $sr;?>.</td>
								<td align="center" ><?php if($insCoInHouseCode) echo $insCoInHouseCode; else echo substr($insProviderName,0,4).".."; ?></td>
								<td align="center"><?php echo $PG; ?></td>
								<td align="center" ><?php if($copay) echo $showCurrencySymbol.number_format($copay,2); else echo "-"; ?></td>
								<td align="center" ><?php if(!empty($effective_date) && (trim($effective_date) != "00-00-0000")) { echo $effective_date; }else{ echo "-";} ?></td>
								<td align="center" ><?php if(!empty($expiration_date) && (trim($expiration_date) != "00-00-0000")) { echo $expiration_date; }else{ echo "-";} ?></td>
							</tr>
							
							<?php
							$sr++;
							}
							}
							?>
							
						</table>
					</td>
					<td align="center" valign="top">
						<table width="100%" border="0">
						<tr>
						<td style="width:50%; vertical-align:top;">
							<table width="100%" border="0">
							<tr>
							    <td align="left" class="text_9" style="width:60px;"><b>Pat Due:</b></td>
								<td align="left" class="text_9"><?php if(empty($amountDue)) echo "-"; else echo $showCurrencySymbol.$amountDue; ?></td>
							</tr>
							<tr>
							    <td align="left" class="text_9"><b>Ins Due:</b></td>
							    <td align="left" class="text_9"><?php if(empty($insuranceDue)) echo "-"; else echo $showCurrencySymbol.$insuranceDue; ?></td>
							</tr>
							<tr>
							    <td align="left" class="text_9"><b>Last Paid:</b></td>
							    <td align="left" class="text_9"><?php if(empty($paymentAmountLast)) echo "No Payment"; else echo $showCurrencySymbol.$paymentAmountLast; ?></td>
							</tr>
							<tr>
							    <td align="left" class="text_9"><b>Today Chg:</b></td>
							    <td align="left" class="text_9"><?php if(empty($todaysCharges_f)) echo "0.00"; else echo $todaysCharges_f; ?></td>
							</tr>
							<tr>
							    <td nowrap align="left" class="text_9"><b>Today Pmt:</b></td>
							    <td nowrap align="left" class="text_9"><?php echo $showCurrencySymbol.number_format($today_total_pay,2); ?></td>
							</tr>
							<?php
								if($today_pay_method_cc!=""){
							?>
							<tr>
													<td align="left" class="text_9"><b>CC Type:</b></td>
													<td align="left" class="text_9"><?php if(empty($today_pay_method_cc_type)) echo "-"; else echo $today_pay_method_cc_type; ?></td>
												</tr>								
							<?php
								}
							?>	
							<?php if($today_pay_method_check!=""){?>
							<tr>
							    <td align="left" class="text_9"><b>Check#:</b></td>
							    <td align="left" class="text_9"><?php if(empty($today_pay_method_check)) echo "-"; else echo $today_pay_method_check; ?></td>
							</tr>
							<?php  } ?>	
							</table>
						</td>
						<td  style="width:50%; vertical-align:top;">
								<table width="100%" border="0">
								<tr>
								    <td align="left" class="text_9" style="width:60px;"><b>Credit Bal:</b></td>
								    <td align="left" class="text_9"><?php if(empty($creditBalance)) echo "-"; else echo $showCurrencySymbol.$creditBalance; ?></td>
								</tr>
								<tr>
								    <td align="left" class="text_9"><b>Total Bal:</b></td>
								    <td align="left" class="text_9"><?php if(empty($tBalance)) echo "-"; else echo $showCurrencySymbol.$tBalance; ?></td>
								</tr>
								<tr>
								    <td align="left" class="text_9"><b>On Date:</b></td>
								    <td align="left" class="text_9"><?php if(($PaidLastDate=="--") || ($PaidLastDate=="00-00-0000")) echo "-"; else echo $PaidLastDate; ?></td>
								</tr>
								<tr>
								    <td align="left" class="text_9"><b>CoPay:</b></td>
								    <td align="left" class="text_9"><?php if(empty($CoPay)) echo "0.00"; else echo $showCurrencySymbol.number_format($CoPay,2); ?></td>
								</tr>
								<tr>
								    <td class="text_9" style="vertical-align:top;"> <b>Method:</b></td>
								    <td class="text_9" style="text-align:left;"> <?php if(empty($today_pay_method)) echo "-"; else echo $today_pay_method; ?></td>
								</tr>
								<?php
									if($today_pay_method_cc!=""){
								?>
								<tr>
									<td align="left" class="text_9"><b>CC#:</b></td>
									<td align="left" class="text_9"><?php if(empty($today_pay_method_cc)) echo "-"; else echo $today_pay_method_cc; ?></td>
								</tr>							
								<?php
									}
								?>	
								</table>
						</td>
						</tr>
						</table>
					</td>
				</tr>
				<tr height="20" bgcolor="#999999">
					<th colspan="2" >Procedure Details</th>
				</tr>
				<tr>
					<td colspan="2" align="left">
						<table width="100%" border="1" cellpadding="0" cellspacing="0" bordercolor="Gainsboro">
						<tr height="20" bgcolor="#CCCCCC">
							<td width="17" class="text_10b" align="left">#</td>
							<td width="110" class="text_10b" align="left">Procedure Code</td>
							<td width="210" class="text_10b" align="left">Procedure Description</td>
							<td width="240" class="text_10b" align="left">Dx Code - Description</td>
							<td width="180" class="text_10b" align="left">Mod Code - Description</td>
							<td width="30" class="text_10b" align="left">Unit</td>
							<td width="60" class="text_10b" align="left">Fee/Unit</td>
							<td width="100" class="text_10b" align="right">Total Charges</td>
						</tr>
						<?php
						if(count($arr_proc_info)){
						$cntr=1;
						foreach($arr_proc_info as $key => $ar_proc){
						extract($ar_proc);
						?>
							<tr valign="top">	
								<td width="17" class="text_10b" align="left"><?php echo $cntr++; ?></td>
								<td align="left" valign="top" class="text_9"><?php echo $procedurePracCode; ?></td>
								<td align="left" valign="top" class="text_9"><?php echo $cptDesc; ?>&nbsp;</td>
								
								<td class="text_9" align="left" valign="top">
									<table border="0" bordercolor="Gainsboro" cellpadding="0" cellspacing="0">
									<?php
									if(count($ar_dx)>0){
									foreach($ar_dx as $k_ar_dx => $ardx_val){
									$diagCode1 = $ardx_val[0];
									$diagDesc1 = $ardx_val[1];
									?>
									<tr>
										<td align="left" valign="top" class="text_9"><?php echo $diagCode1; ?> -&nbsp;</td>
										<td align="left" valign="top" class="text_9"><?php echo $diagDesc1; ?></td>
									</tr>
									<?php
									}
									}
									?>
									</table>
								</td>
								<td align="left" valign="top" class="text_9">
									<table border="0" bordercolor="Gainsboro" cellpadding="0" cellspacing="0">
									<?php
									if(count($ar_md)>0){
									foreach($ar_md as $k_ar_md => $armd_val){
									$modPracCode1 = $armd_val[0];
									$modDesc1 = $armd_val[1];
									?>
									<tr>
										<td align="left" valign="top" class="text_9"><?php echo $modPracCode1; ?> -&nbsp;</td>
										<td align="left" valign="top" class="text_9"><?php echo $modDesc1; ?></td>
									</tr>
									<?php
									}
									}
									?>
									</table>
								</td>
								<td align="left" valign="top" class="text_9"><?php echo $cptUnits; ?>&nbsp;</td>
								<td align="left" valign="top" class="text_9"><?php echo $fee; ?> &nbsp;</td>								
								<td align="right" valign="top" class="text_9"><?php echo $tcharge; ?></td>
							</tr>
						
						<?php
						}
						}
						?>
						
						<tr>
								<td align="right" colspan="7" class="text_10b" >Today's Charges:</td>
								<td align="right" class="text_9"><?php echo $todaysCharges_f; ?></td>
						</tr>
						
						</table>
					</td>
				</tr>
				<tr height="20" bgcolor="#999999">
					<th colspan="2" >Follow Up</th>
				</tr>
				<tr>
					<td colspan="2" align="center">
						<table border="1" cellpadding="0" cellspacing="0" bordercolor="#FFFFFF" width="100%">
							<tr height="20" bgcolor="#CCCCCC">
								<td align="center" class="text_10b" width="25%">Follow-up</td>
								<td align="center" class="text_10b" width="25%">Testing</td>
								<td align="center" class="text_10b" width="25%">Schedule Sx</td>
								<td align="center" class="text_10b" width="25%">Notes</td>
							</tr>
							<tr height="20">
								<td align="left" class="text_9" valign="top" style="padding:2px;"><?php echo $follow_up; ?></td>
								<td align="left" class="text_9" valign="top" style="padding:2px;"><?php echo $testing; ?></td>
								<td align="left" class="text_9" valign="top" style="padding:2px;"><?php echo $scheduleSx; ?></td>
								<td align="left" class="text_9" valign="top" style="padding:2px;"><?php echo $plan_notes; ?></td>
							</tr>
						</table>	
					</td>		
				</tr>			
				<tr>
					<td colspan="2">
						<table border="1" cellpadding="0" cellspacing="0" bordercolor="#FFFFFF" width="100%">
							<tr height="20" bgcolor="#999999">
							<th colspan="4" >Future Appointments</th>
						</tr>
						<tr height="20" bgcolor="#CCCCCC">
							<td align="left" class="text_10b" width="25%">Appointment Date</td>
							<td align="left" class="text_10b" width="25%">Appointment Time</td>
							<td align="left" class="text_10b" width="25%">Procedure</td>
							<td align="left" class="text_10b" width="25%">Status</td>
						</tr>  
						<?php
						if(count($ar_future_app)>0){
						foreach($ar_future_app as $k => $ar_fp){
							extract($ar_fp);
						?>
						<tr height="20">
						    <td align="left" class="text_9" valign="top" style="padding:2px;"><?php echo $sch_app_date_imp; ?></td>
						    <td align="left" class="text_9" valign="top" style="padding:2px;"><?php echo $sch_app_time_imp; ?></td>
						    <td align="left" class="text_9" valign="top" style="padding:2px;"><?php echo $procName; ?></td>
						    <td align="left" class="text_9" valign="top" style="padding:2px;"><?php echo $strAppStatus; ?></td>
						</tr>
						<?php }} ?>
						
						</table>
					</td>
				</tr>	
				</table>
			</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td align="center"><input type="button"  value="  Print Page  " onClick="return do_print();" /></td>
		</tr>
		<tr>
			<td align="left">
				<table border="0" cellpadding="0" cellspacing="0">
					<tr height="25px">
						<td class="text_10b" align="left">&nbsp;</td>
					</tr>
				</table>
			</td>	
		</tr>
		</table>
<?php
}
?>		
</body>
</html>		



