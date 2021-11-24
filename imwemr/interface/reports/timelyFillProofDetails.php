<?php
ob_start();
$patientId = trim($_REQUEST['patientId']);
$dateFrom = $_REQUEST['dateFrom'];

if(!isset($_REQUEST['disableDate'])){
	unset($_REQUEST['dateFrom']);
	unset($_REQUEST['dateTo']);
}

$GLOBALDATEFORMAT = $GLOBALS['date_format'];
if($dateFrom!=''){
	if($GLOBALDATEFORMAT = 'dd-mm-yyyy' && $GLOBALDATEFORMAT != ''){
		list($day, $month, $year) = explode("-", $dateFrom);
		$dateFrom = $year."-".$month."-".$day;
	} else {
		list($month, $day, $year) = explode("-", $dateFrom);
		$dateFrom = $year."-".$month."-".$day;
	}
}
$dateTo = $_REQUEST['dateTo'];
if($dateTo!=''){
	if($GLOBALDATEFORMAT = 'dd-mm-yyyy' && $GLOBALDATEFORMAT != ''){
		list($day, $month, $year) = explode("-", $dateTo);
		$dateTo = $year."-".$month."-".$day;
	} else {
		list($month, $day, $year) = explode("-", $dateTo);
		$dateTo = $year."-".$month."-".$day;
	}
}
?>
<html>
<head>
<script type="text/javascript">
	function printWindow(){
		window.focus();
		window.print();
	}
</script>
</head>
<body class="body_c">
<?php
if($submitFRM == 1){
	$qry = "select charge_list_id, encounter_id, patient_id, date_of_service
			from patient_charge_list where patient_id > 0";
	if($patientId){
		$qry .= " and patient_id = $patientId";
	}
	if($dateFrom != '' && $dateTo != ''){
		$qry .= " and date_of_service between '$dateFrom' and '$dateTo'";
	}
	$sql_query = imw_query($qry);
	$charge_res = array();
	while ($res = imw_fetch_assoc($sql_query)) {
		$charge_res[] = $res;
	}
	$chargeListPatientIdArr = array();
	for($i=0;$i<count($charge_res);$i++){
		$encounter_id = $charge_res[$i]['encounter_id'];
		$charge_list_id = $charge_res[$i]['charge_list_id'];
		$patient_id = $charge_res[$i]['patient_id'];
		$date_of_service = $charge_res[$i]['date_of_service'];
		
		$qry = "select count(*) as submit_count from submited_record
				where encounter_id = '$encounter_id' 
				and (DATEDIFF(submited_date, '$date_of_service') <= 60)";
		$submit_res =imw_query($qry);
		if(imw_num_rows($submit_res) > 0){
			$row = imw_fetch_assoc($submit_res);
			$submit_count  = $row['submit_count'];
			$chargeListPatientIdArr[$charge_list_id] = $patient_id;
		}
	}
	foreach($chargeListPatientIdArr as $cLId => $pId){
		$getInsCoStr = "SELECT * FROM patient_charge_list WHERE charge_list_id = '$cLId'";
		$getInsCoQry = imw_query($getInsCoStr);
		$getInsCoRow = imw_fetch_array($getInsCoQry);
			$case_type_id = $getInsCoRow['case_type_id'];
			$encounter_id = $getInsCoRow['encounter_id'];
			$copay = $getInsCoRow['copay'];
			$date_of_service = $getInsCoRow['date_of_service'];
			//By Karan
			if($GLOBALDATEFORMAT = 'dd-mm-yyyy' && $GLOBALDATEFORMAT != '')
			{
				list($dosYear, $dosMonth, $dosDay) = explode("-", $date_of_service);
				$date_of_service = $dosDay."-".$dosMonth."-".$dosYear;
			}
			
			else
			{
				list($dosYear, $dosMonth, $dosDay) = explode("-", $date_of_service);
				$date_of_service = $dosMonth."-".$dosDay."-".$dosYear;
			}
			
			$primaryProviderId = $getInsCoRow['primary_provider_id_for_reports'];
			//================== GETTING PRIMARY PRIVIDER NAME
			$getPrimaryProvNameStr = "SELECT * FROM users WHERE id = '$primaryProviderId'";
			$getPrimaryProvNameQry = imw_query($getPrimaryProvNameStr);
			$getPrimaryProvNameRow = imw_fetch_array($getPrimaryProvNameQry);
			$provFname = $getPrimaryProvNameRow['fname'];
			$provMname = $getPrimaryProvNameRow['mname'];
			$provLname = $getPrimaryProvNameRow['lname'];
			$providerName = ucwords($provFname)." ".strtoupper(substr($provMname, 0, 1))." ".ucwords($provLname);
				if(strlen($providerName)>8){ $providerName = substr($providerName, 0, 10).'..'; }
			//================== GETTING PRIMARY PRIVIDER NAME
		++$seq;
		?>
		<table border="1" width="665" align="center" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" bordercolor="#F3F3F1">
			<?php
			if($pId!=$tempPId){
				if($seq!=1){
					?>
					<tr height="25">
						<td colspan="9"></td>	
					</tr>
					<?php
				}
				?>
				<tr bgcolor="#F1F3F2" height="20">
					<td width="116" rowspan="2" class="text_10b">Patient Name</td>
					<td width="35" rowspan="2" class="text_10b">ID</td>
					<td colspan="3" class="text_10b">Insurance Company</td>
					<td colspan="3" class="text_10b">Insurance Policy #</td>
					<td width="80" rowspan="2" class="text_10b">Provider</td>
				</tr>
				<tr bgcolor="#F1F3F2">
					<td width="85" class="text_10b">Primary</td>
					<td width="85" class="text_10b">Secondary</td>
					<td width="85" class="text_10b">Tertiary</td>
					<td width="85" class="text_10b">Primary</td>
					<td width="85" class="text_10b">Secondary</td>
					<td width="85" class="text_10b">Tertiary</td>
				</tr>
				<?php
			} //=============================== PATIENT CHECK
			$gettingInsCoIdsStr = "SELECT * FROM patient_data 
									WHERE id = '$pId'";
			$gettingInsCoIdsQry = imw_query($gettingInsCoIdsStr);
			$gettingInsCoIdsRow = imw_fetch_array($gettingInsCoIdsQry);
				$fname = $gettingInsCoIdsRow['fname'];
				$mname = $gettingInsCoIdsRow['mname'];
				$lname = $gettingInsCoIdsRow['lname'];
				$patientName = $fname." ".strtoupper(substr($mname, 0, 1))." ".$lname;
				if(strlen($patientName)>15){
					$patientName = $fname." ".$lname;
				}
				//========================= GETTING PROVIDERS INSURANCE 
				$getInsCoNameStr = "SELECT * FROM 
									insurance_data a,
									insurance_companies b
									WHERE a.pid = '$pId' AND a.ins_caseid = '$case_type_id' AND a.provider = b.id";
				$getInsCoNameQry = imw_query($getInsCoNameStr);
				while($getInsCoNameRows = imw_fetch_array($getInsCoNameQry)){
					$type = $getInsCoNameRows['type'];
					switch($type){
						case 'primary':
							$primaryInsProviderName = $getInsCoNameRows['name'];
							$primaryInsPolicy = $getInsCoNameRows['policy_number'];
							if(strlen($primaryInsProviderName)>8){ $primaryInsProviderName = substr($primaryInsProviderName, 0, 8).'..' ; }
							break;
						case 'secondary':
							$secondaryInsProviderName = $getInsCoNameRows['name'];
							$secondaryInsPolicy = $getInsCoNameRows['policy_number'];
							if(strlen($secondaryInsProviderName)>8){ $secondaryInsProviderName = substr($secondaryInsProviderName, 0, 8).'..' ; }
							break;
						case 'tertiary':
							$tertiaryInsProviderName = $getInsCoNameRows['name'];
							$tertiaryInsPolicy = $getInsCoNameRows['policy_number'];
							if(strlen($tertiaryInsProviderName)>8){ $tertiaryInsProviderName = substr($tertiaryInsProviderName, 0, 8).'..' ; }
							break;
					}
				}
				//========================= GETTING PROVIDERS INSURANCE 
				if($pId==$tempPId){
					?>
					<tr height="30">
						<td colspan="9"></td>
					</tr>
					<?php
				}
				?>
				<tr height="20">
					<td width="116" class="text_10" bgcolor="<?php if($pId!=$tempPId) echo ""; ?>"><?php echo $patientName; //if($pId!=$tempPId) echo $patientName;  else echo "&nbsp;"; ?></td>
					<td width="35" class="text_10" bgcolor="<?php if($pId!=$tempPId) echo ""; ?>"><?php echo $pId; //if($pId!=$tempPId) echo $pId; else echo "&nbsp;"; ?></td>
					<td width="85" class="text_10" bgcolor=""><?php if($primaryInsProviderName!='') echo $primaryInsProviderName; else echo "N/A"; ?></td>
					<td width="85" class="text_10" bgcolor=""><?php if($secondaryInsProviderName!='') echo $secondaryInsProviderName; else echo "N/A"; ?></td>
					<td width="85" class="text_10" bgcolor=""><?php if($tertiaryInsProviderName!='') echo $tertiaryInsProviderName; else echo "N/A"; ?></td>
					<td width="85" class="text_10" bgcolor=""><?php if($primaryInsPolicy!='') echo $primaryInsPolicy; else echo "N/A"; ?></td>
					<td width="85" class="text_10" bgcolor=""><?php if($secondaryInsPolicy!='') echo $secondaryInsPolicy; else echo "N/A"; ?></td>
					<td width="85" class="text_10" bgcolor=""><?php if($tertiaryInsPolicy!='') echo $tertiaryInsPolicy; else echo "N/A"; ?></td>
					<td width="80" class="text_10" bgcolor=""><?php echo $providerName; ?></td>
				</tr>
				<tr>
					<td colspan="9" align="center">
						<table border="0" width="95%" cellpadding="2" cellspacing="2" bgcolor="#F1F3F2">
							<?php
							if($date_of_service != $tempdos){
								?>
								<tr height="20" style="font-weight:bold" bgcolor="#F1F3F2">
									<td width="14%" align="center" class="text_10b">DOS</td>
									<td width="9%" align="center" class="text_10b">E Id</td>
									<td width="16%" align="center" class="text_10b">Proc Code</td>
									<td width="20%" align="left" style="padding-left:5px;" class="text_10b">Description</td>
									<td width="19%" align="center" class="text_10b">Provider Charges</td>
									<td width="14%" align="center" class="text_10b">Patient Paid</td>
									<td width="14%" nowrap="nowrap" align="center" class="text_10b">Ins. Paid</td>
									<td width="8%" align="center" class="text_10b">Balance</td>
								</tr>
								<?php
							}
							$totalApproved = 0;
							$totalPaid = 0;
							$totalBal = 0;
							$getDetailsStr = "SELECT * FROM 
												patient_charge_list_details a,
												cpt_fee_tbl b
												WHERE a.charge_list_id = '$cLId'
												AND a.procCode = b.cpt_fee_id";
							$getDetailsQry = imw_query($getDetailsStr);
							$totalPatientPaidForProc = 0;
							$totalinsurancePaidForProc = 0;
							while($getDetailsRows = imw_fetch_array($getDetailsQry)){
									$cpt4_code = $getDetailsRows['cpt4_code'];
									$cpt_desc = $getDetailsRows['cpt_desc'];
									$approvedAmt = $getDetailsRows['approvedAmt'];
									$paidForProc = $getDetailsRows['paidForProc'];
									$newBalance = $getDetailsRows['newBalance'];
									$charge_list_detail_id = $getDetailsRows['charge_list_detail_id'];
									$coPayAdjustedAmount = $getDetailsRows['coPayAdjustedAmount'];
									if($coPayAdjustedAmount=='1'){
										$paidForProc = $paidForProc + $copay;
									}
										$totalApproved = $totalApproved + $approvedAmt;
										$totalPaid = $totalPaid + $paidForProc;
										$totalBal = $totalBal + $newBalance;
									//--- Get patient paid amount ---------
									$qry = "select sum(patient_charges_detail_payment_info.paidForProc) as paidForProc
											from patient_charges_detail_payment_info join 
											patient_chargesheet_payment_info on 
											patient_chargesheet_payment_info.payment_id = 
											patient_charges_detail_payment_info.payment_id
											where patient_charges_detail_payment_info.paidForProc > 0
											and patient_charges_detail_payment_info.paidBy = 'Patient'
											and patient_charges_detail_payment_info.deletePayment != 1
											and patient_charges_detail_payment_info.charge_list_detail_id = $charge_list_detail_id
											and patient_chargesheet_payment_info.encounter_id = '$encounter_id'";
									$submit_res =imw_query($qry);
									$row = imw_fetch_assoc($submit_res);
									$patient_paid_res  = $row['paidForProc'];
									
									$patientPaidForProc = number_format($patient_paid_res['paidForProc'],2);
									$totalPatientPaidForProc += $patient_paid_res['paidForProc'];
									//--- Get insurance paid amount ---------
									$qry = "select sum(patient_charges_detail_payment_info.paidForProc) as paidForProc
											from patient_charges_detail_payment_info join 
											patient_chargesheet_payment_info on 
											patient_chargesheet_payment_info.payment_id = 
											patient_charges_detail_payment_info.payment_id
											where patient_charges_detail_payment_info.paidForProc > 0
											and patient_charges_detail_payment_info.paidBy = 'Insurance'
											and patient_charges_detail_payment_info.deletePayment != 1
											and patient_charges_detail_payment_info.charge_list_detail_id = $charge_list_detail_id
											and patient_chargesheet_payment_info.encounter_id = '$encounter_id'";
									$sub_res =imw_query($qry);
									$row = imw_fetch_assoc($sub_res);
									$insurance_paid_res  = $row['paidForProc'];
									
									$insurancePaidForProc = number_format($insurance_paid_res['paidForProc'],2);
									$totalinsurancePaidForProc += $insurance_paid_res['paidForProc'];
									if($coPayAdjustedAmount=='1'){
										//--- Get patient copay paid amount ---------
										$qry = "select sum(patient_charges_detail_payment_info.paidForProc) as paidForProc
												from patient_charges_detail_payment_info join 
												patient_chargesheet_payment_info on 
												patient_chargesheet_payment_info.payment_id = 
												patient_charges_detail_payment_info.payment_id
												where patient_charges_detail_payment_info.paidForProc > 0
												and patient_charges_detail_payment_info.deletePayment != 1
												and patient_charges_detail_payment_info.paidBy = 'Patient'
												and patient_charges_detail_payment_info.charge_list_detail_id = 0
												and patient_chargesheet_payment_info.encounter_id = '$encounter_id'";
										$sub_res =imw_query($qry);
										$row = imw_fetch_assoc($sub_res);
										$patient_copay_paid_res  = $row['paidForProc'];
																		
										$patientPaidForProc += number_format($patient_copay_paid_res['paidForProc'],2);
										$totalPatientPaidForProc += $patient_copay_paid_res['paidForProc'];
										//--- Get insurance copay paid amount ---------
										$qry = "select sum(patient_charges_detail_payment_info.paidForProc) as paidForProc
												from patient_charges_detail_payment_info join 
												patient_chargesheet_payment_info on 
												patient_chargesheet_payment_info.payment_id = 
												patient_charges_detail_payment_info.payment_id
												where patient_charges_detail_payment_info.paidForProc > 0
												and patient_charges_detail_payment_info.paidBy = 'Insurance'
												and patient_charges_detail_payment_info.deletePayment != 1
												and patient_charges_detail_payment_info.charge_list_detail_id = 0
												and patient_chargesheet_payment_info.encounter_id = '$encounter_id'";
										$sub_res =imw_query($qry);
										$row = imw_fetch_assoc($sub_res);
										$insurance_copay_piad_res  = $row['paidForProc'];
										
										$insurancePaidForProc += number_format($patient_copay_paid_res['paidForProc'],2);
										$totalinsurancePaidForProc += $patient_copay_paid_res['paidForProc'];
									}
								?>
								<tr>
									<td width="14%" bgcolor="#FFFFFF" class="text_10"><?php if($date_of_service != $tempdos) echo $date_of_service; else echo "&nbsp;"; ?></td>
									<td width="9%" bgcolor="#FFFFFF" class="text_10"><?php if($tempEncId != $encounter_id) echo $encounter_id; else echo "&nbsp;"; ?></td>
									<td width="16%" bgcolor="#FFFFFF" class="text_10"><?php echo $cpt4_code; ?></td>
									<td width="20%" bgcolor="#FFFFFF" class="text_10"><?php echo $cpt_desc; ?></td>
									<td width="19%" bgcolor="#FFFFFF" class="text_10" style="padding-right:10px;"><?php echo ''.show_currency().''.number_format($approvedAmt, 2); ?></td>
									<td width="14%" bgcolor="#FFFFFF" class="text_10" style="padding-right:10px;"><?php echo ''.show_currency().''.number_format($patientPaidForProc, 2); ?></td>
									<td width="14%" bgcolor="#FFFFFF" class="text_10" style="padding-right:10px;"><?php echo ''.show_currency().''.number_format($insurancePaidForProc, 2); ?></td>
									<td width="8%" bgcolor="#FFFFFF" class="text_10" style="padding-right:10px;"><?php echo ''.show_currency().''.number_format($newBalance, 2); ?></td>
								</tr>
								<?php
								$date_of_service = $tempdos;
								$tempEncId = $encounter_id;
							}
							?>
							<tr>
								<td colspan="4" bgcolor="#FFFFFF"></td>
								<td height="2" colspan="4" align="right" bgcolor="#009933"></td>
							</tr>
							<tr>	
								<td colspan="4" bgcolor="#FFFFFF" class="text_10b" align="right">Total:</td>
								<td class="text_10" bgcolor="#FFFFFF"><?php echo ''.show_currency().''.number_format($totalApproved, 2); ?></td>
								<td class="text_10" bgcolor="#FFFFFF"><?php echo ''.show_currency().''.number_format($totalPatientPaidForProc, 2); ?></td>
								<td class="text_10" bgcolor="#FFFFFF"><?php echo ''.show_currency().''.number_format($totalinsurancePaidForProc, 2); ?></td>
								<td class="text_10" bgcolor="#FFFFFF"><?php echo ''.show_currency().''.number_format($totalBal, 2); ?></td>
							</tr>
							<tr>
								<td colspan="4" bgcolor="#FFFFFF"></td>
								<td colspan="4" height="2" bgcolor="#009933"></td>
							</tr>
					  </table>
					</td>
				</tr>
				<tr>
					<td colspan="9" align="left" style="padding-left:65px;">
						<table border="0" cellpadding="0" cellspacing="0">
							<tr>
								<td class="text_10b" valign="top">Submitted Date</td>
								<td class="text_10b" width="15">&nbsp;</td>
								<td class="text_10b" valign="top">Submitted Ins. Name</td>
								<td class="text_10b" width="15" valign="top">&nbsp;</td>
								<td class="text_10b" valign="top">Operator</td>
							</tr>
							<tr>
								<td height="2" bgcolor="#CCCCCC"></td>
								<td height="2"></td>
								<td height="2" bgcolor="#CCCCCC"></td>
							</tr>
							<?php
							$qry = "SELECT operator_id, date_format(submited_date, '".get_sql_date_format()."') as submit_date,Ins_company_id as ins_com
									FROM submited_record WHERE encounter_id = '$encounter_id'";
								$sql_query = imw_query($qry);
								$submit_res = array();
								while ($res = imw_fetch_assoc($sql_query)) {
									$submit_res[] = $res;
								}
							for($s=0;$s<count($submit_res);$s++){
								$submited_date = $submit_res[$s]['submit_date'];
								$operator_id = $submit_res[$s]['operator_id'];
								$insComID = $submit_res[$s]['ins_com'];
								if($operator_id){
									$qry = "select concat(lname,', ',fname) as name , mname 
											from users where id = '$operator_id'";
									$sub_res =imw_query($qry);
									$submit_phy_res = imw_fetch_assoc($sub_res);
									$opName = $submit_phy_res['name'].' ';
									$opName .= $submit_phy_res['mname'];
								}
								else{
									$opName = '';
								}
								$submit_res =imw_query($qry);
								if(imw_num_rows($submit_res) > 0){
									$row = imw_fetch_assoc($submit_res);
									$Ins_company_id  = $row['Ins_company_id'];								
									$qry = "select in_house_code from insurance_companies
											where id = '$insComID'";
											$sub_res =imw_query($qry);
											$submit_res = imw_fetch_assoc($sub_res);
											$ins_name_res = $submit_res['in_house_code'].' ';
									}						

							?>
								<tr>
									<td valign="top" class="text_10"><?php echo $submited_date; ?></td>
									<td class="text_10b" width="15" valign="top">&nbsp;</td>
									<td valign="top" class="text_10"><?php echo $ins_name_res; ?></td>
									<td class="text_10b" width="15" valign="top">&nbsp;</td>
									<td class="text_10"><?php echo $opName; ?></td>
								</tr>
							<?php
							}
						?>
						</table>
					</td>
				</tr>
			<?php
			$tempPId = $pId;
			?>
		</table>
		<?php
	}
	if(count($chargeListPatientIdArr) <= 0){?> <div class="text-center alert alert-info">No Record Exists.</div> <?php
	}
}
?>
</body>
</html>
<?php
$fileData = ob_get_contents();	
ob_clean();
if(count($chargeListPatientIdArr)>0){
	$divHeight= $_SESSION['wn_height']-200;
	$fileData='<div style="height:'.$divHeight.'; overflow-y:scroll;">'.$fileData.'</div>';
	$file_location =  write_html($fileData);
	echo '<div class="text-center alert alert-info">Result is populated in separate window.</div>';
	?>
	<script type="text/javascript">
		var winHeight= '<?php echo $_SESSION['wn_height'] - 150;?>';
		var file_name=  'printTimelyFilling.php?file_loc=<?php echo $file_location; ?>';
		window.open(file_name,'printTimelyFilling','width=725,height='+winHeight+',scrollbars=0,resizable=1');
	</script>
	<?php
}
else{
	print $fileData;
}
?>
<script type="text/javascript">
	top.show_loading_image("hide");
</script>