<?php
function underLine($to){
	$NBSP = "<u>";
	for($counter = 1; $counter<=$to; $counter++){
		$NBSP .= "&nbsp;";	
	}
	$NBSP .= "</u>";
	return $NBSP;
}

if($pid == ""){
?>
	<script>
		alert('Please select Patient to Proceed');
		window.close();
	</script>
<?php
}
if($face_sheet_scan == 1 || $insurance_scan == 1 || $patient_info[0] == 'all'){
	$scan_card_var = true;
}
else{
	$scan_card_var = false;
}
$qry = imw_query("select * from insurance_case where patient_id = '".$pid."' and case_status = 'Open' order by ins_case_type");
while($row = imw_fetch_array($qry)){
	$caseDetail[] = $row;
}
$ins_caseidArr=array();
for($r=0;$r<count($caseDetail);$r++){		
	//if(count($caseDetail)>0){
	$ins_caseid = "";
	$ins_caseid = $caseDetail[$r]['ins_caseid'];
	$ins_caseidArr[] = $caseDetail[$r]['ins_caseid'];
	$start_date = substr($caseDetail[$r]['start_date'],0,strpos($caseDetail[$r]['start_date'],' '));
	$end_date = substr($caseDetail[$r]['end_date'],0,strpos($caseDetail[$r]['end_date'],' '));
	$openDate = get_date_format($start_date);
	if(get_number($end_date)!= '00000000'){
		$end_Date = get_date_format($end_date);
	}
	//--- Get Insurance Case Type -------
	$ins_case_type = $caseDetail[$r]['ins_case_type'];
	$qry = imw_query("select case_name from insurance_case_types where case_id = '".$ins_case_type."'");
	while($row = imw_fetch_array($qry)){
		$caseType[] = $row;
	}
	//-- Get Responsible Party Name -----
	$resp_name  = $pt_data['res_party_detail'][0]['lname'].', ';
	$resp_name .= $pt_data['res_party_detail'][0]['fname'].' ';
	$resp_name .= $pt_data['res_party_detail'][0]['mname'];	
	if(trim($resp_name) == ','){
		$resp_name = 'Self';
	}	
	$insType = array('primary','Secondary','Tertiary');
	$ins = 0;
	foreach($insType as $val){
	    $insPriDetails = array();
		$qry = imw_query("select * from insurance_data where pid='".$pid."' and type='".$val."' and actInsComp='1' and ins_caseid='".$ins_caseid."' and provider > 0 and effective_date <= now() 
				and (expiration_date = '0000-00-00 00:00:00' or expiration_date > now())");
		while($row = imw_fetch_array($qry)){
			$insPriDetails[] = $row;
		}		
		if(((count($insPriDetails)>0) && ($val == 'Tertiary')) || (($val == 'primary') || ($val == 'Secondary'))){
			$priInsHeading = $val." insurance carrier details";
			$scan_card = '';
			if($insPriDetails[0]['scan_card'] != '' || $insPriDetails[0]['scan_card2'] != ''){
				$scan_card = '<span class="text_10b" >Ins. Card Scanned</span>';
				//--- Scan Card Print Or Not ------
				if($scan_card_var){					
					if($insPriDetails[0]['scan_card']){
						$scan_card_arr[$ins_caseid][$val]['scan_card'] = $insPriDetails[0]['scan_card'];
					}
					if($insPriDetails[0]['scan_card2']){
						$scan_card_arr[$ins_caseid][$val]['scan_card2'] = $insPriDetails[0]['scan_card2'];
					}
				}
			}										
			if($ins == 0){		
			?>
			<table width="100%" cellpadding="0" cellspacing="0" border="<?php echo $border; ?>" rules="none">
				<tr>
                        <td colspan="4" height="1" style="width:750px;border-bottom:1px solid #012778;!important;height:1px;">&nbsp;</td>
                    </tr>
					<tr>
						<td colspan="4" height="2"></td>
					</tr>
					
				<tr valign="middle" height="25px">
					<td colspan="4" style="font-size:14px;color:#012778;font-weight:bold;" class="bluehed text_cap paddingTop10" ><b>INSURANCE</b></td>
				</tr>		
				<tr><td height="7"></td></tr>				
				<tr> 	
					<td class="text_10b" align="left" width="180" height="<?php echo ($caseDetail[$r]['ins_caseid']) ? "5px" : "14px"; ?>" valign="<?php echo ($caseDetail[$r]['ins_caseid']) ? "bottom" : "top"; ?>">&nbsp;<b><?php echo ($caseDetail[$r]['ins_caseid']) ? $caseDetail[$r]['ins_caseid'] : underLine(35); ?></b></td>
					<td class="text_10b" width="180" height="<?php echo ($caseType[0]['case_name']) ? "5px" : "14px"; ?>" valign="<?php echo ($caseType[0]['case_name']) ? "bottom" : "top"; ?>">&nbsp;<b><?php echo ($caseType[0]['case_name']) ? $caseType[0]['case_name'] : underLine(35); ?></b></td>
					<td class="text_10b" width="180" height="<?php echo ($resp_name) ? "5px" : "14px"; ?>" valign="<?php echo ($resp_name) ? "bottom" : "top"; ?>">&nbsp;<b><?php echo ($resp_name) ? $resp_name : underline(35); ?></b></td>		
					<td class="text_10b" width="180" height="<?php echo (($openDate) ? "5px" : "14px") or (($end_Date) ? "5px" : "14px"); ?>" valign="<?php echo (($openDate) ? "bottom" : "top") or (($end_Date) ? "bottom" : "top"); ?>">&nbsp;<b><?php echo (($openDate) ? $openDate : underLine(20)) .' / '.(($end_Date) ? $end_Date : underLine(20)); ?></b></td>
				</tr>
				<tr>
					<td class="text_10" style="color:#444444"><i>Ins. Case#</i></td>
					<td class="text_10" style="color:#444444"><i>Ins. Case Type</i></td>
					<td class="text_10" style="color:#444444"><i>Responsible Party</i></td>
					<td class="text_10" style="color:#444444"><i>Case Open/End Date</i></td>
				</tr>
			</table>
			<?php
			}
			$ins++;
			?>									
			<table width="100%" cellpadding="0" cellspacing="0" border="<?php echo $border; ?>" rules="none">			
				<tr valign="middle" height="25px" bgcolor="#c0c0c0">
					<td class="text_10" colspan="4"><b><?php echo strtoupper(trim($priInsHeading)).' '.strtoupper(trim($scan_card)); ?></b></td>
				</tr>	
				<tr><td height="7"></td></tr>		
			<?php
				$provider = $insPriDetails[0]['provider'];
				$qry = imw_query("select name from insurance_companies where id = '".$provider."'");
				$insDetails = array();
				while($row = imw_fetch_array($qry)){
					$insDetails[] = $row;
				}
				$insProviderName = "";
				$insProviderName = ucwords(strtolower($insDetails[0]['name']));			
			?>
				<tr align="left"> 	
					<td class="text_10b" width="180" height="<?php echo ($insProviderName) ? "5px" : "14px"; ?>" valign="<?php echo ($insProviderName) ? "bottom" : "top"; ?>">&nbsp;<b><?php echo ($insProviderName) ? $insProviderName : underLine(35); ?></b></td>
					<td class="text_10b" width="180" height="<?php echo ($insPriDetails[0]['policy_number']) ? "5px" : "14px"; ?>" valign="<?php echo ($insPriDetails[0]['policy_number']) ? "bottom" : "top"; ?>">&nbsp;<b><?php echo ($insPriDetails[0]['policy_number']) ? $insPriDetails[0]['policy_number'] : underLine(35); ?></b></td>
					<td class="text_10b" width="180" height="<?php echo ($insPriDetails[0]['group_number']) ? "5px" : "14px"; ?>" valign="<?php echo ($insPriDetails[0]['group_number']) ? "bottom" : "top"; ?>">&nbsp;<b><?php echo ($insPriDetails[0]['group_number']) ? $insPriDetails[0]['group_number'] : underLine(35); ?></b></td>		
					<td class="text_10b" width="180" height="<?php echo ($insPriDetails[0]['plan_name']) ? "5px" : "14px"; ?>" valign="<?php echo ($insPriDetails[0]['plan_name']) ? "bottom" : "top"; ?>">&nbsp;<b><?php echo ($insPriDetails[0]['plan_name']) ? $insPriDetails[0]['plan_name'] : underLine(35); ?></b></td>
				</tr>			
				<tr>
					<td class="text_10" style="color:#444444"><i>Ins. Carrier</i></td>
					<td class="text_10" style="color:#444444"><i>Policy#</i></td>
					<td class="text_10" style="color:#444444"><i>Group#</i></td>
					<td class="text_10" style="color:#444444"><i>Plan Name</i></td>
				</tr>		
				<tr><td height="7"></td></tr>
			<?php
				$actDate = substr($insPriDetails[0]['effective_date'],0,strpos($insPriDetails[0]['effective_date'],' '));
				$activeDate = '';
				if($actDate != '0000-00-00')
					$activeDate = get_date_format($actDate);
				$expDate = substr($insPriDetails[0]['expiration_date'],0,strpos($insPriDetails[0]['expiration_date'],' '));
				$expireDate = '';
				if($expDate != '0000-00-00')
					$expireDate = get_date_format($expDate);
				$copay = '$'.@number_format($insPriDetails[0]['copay'],2);
			?>
				<tr align="left"> 	
					<td class="text_10b" width="180" height="<?php echo ($activeDate != "--") ? "5px" : "14px"; ?>" valign="<?php echo ($activeDate != "--") ? "bottom" : "top"; ?>">&nbsp;<b><?php echo ($activeDate != "--") ? $activeDate : underLine(35); ?></b></td>
					<td class="text_10b" width="180" height="<?php echo ($pt_data['pt_ss'] != "--") ? "5px" : "14px"; ?>" valign="<?php echo ($pt_data['pt_ss'] != "--") ? "bottom" : "top"; ?>">&nbsp;<b><?php echo ($pt_data['pt_ss'] != "--") ? $pt_data['pt_ss'] : underLine(35);//echo $expireDate; ?></b></td>
					<td class="text_10b" width="180" height="<?php echo ($copay != "$0.00") ? "5px" : "14px"; ?>" valign="<?php echo ($copay != "$0.00") ? "bottom" : "top"; ?>">&nbsp;<b><?php echo ($copay != "$0.00") ? $copay : underLine(35); ?></b></td>		
					<td class="text_10b" width="180" height="<?php echo ($insPriDetails[0]['referal_required']) ? "5px" : "14px"; ?>" valign="<?php echo ($insPriDetails[0]['referal_required']) ? "bottom" : "top"; ?>">&nbsp;<b><?php echo ($insPriDetails[0]['referal_required']) ? $insPriDetails[0]['referal_required'] : underLine(35); ?></b></td>
				</tr>
				
				<tr>
					<td class="text_10" style="color:#444444"><i>Activaton Date</i></td>
					<td class="text_10" style="color:#444444"><i>Social Security#</i></td>
					<td class="text_10" style="color:#444444"><i>Copay</i></td>
					<td class="text_10" style="color:#444444"><i>Referal Required</i></td>
				</tr>	
				<tr><td height="7"></td></tr>
			<?php
				$subscriberName = $insPriDetails[0]['subscriber_lname'].', ';
				$subscriberName .= $insPriDetails[0]['subscriber_fname'].' ';
				$subscriberName .= $insPriDetails[0]['subscriber_mname'];
				if(trim($subscriberName) == ","){
					$subscriberName = "";
				}
				
				if($insPriDetails[0]['subscriber_DOB'] != '0000-00-00'){
					$subscriber_DOB = get_date_format($insPriDetails[0]['subscriber_DOB']);
				}	
				else{			
					$subscriber_DOB = '';
				}	
								
				$strToShowRelation = $insPriDetails[0]['subscriber_relationship'];
				if(strtolower($insPriDetails[0]['subscriber_relationship']) == "doughter"){
					$strToShowRelation = "Daughter";
				}
			?>
				<tr align="left"> 
					<td class="text_10b" width="180" height="<?php echo (ucwords($strToShowRelation)) ? "5px" : "14px"; ?>" valign="<?php echo (ucwords($strToShowRelation)) ? "bottom" : "top"; ?>">&nbsp;<b><?php echo (ucwords($strToShowRelation)) ? ucwords($strToShowRelation) : underLine(35); ?></b></td>
					<td class="text_10b" width="180" height="<?php echo (trim($subscriberName)) ? "5px" : "14px"; ?>" valign="<?php echo (trim($subscriberName)) ? "bottom" : "top"; ?>">&nbsp;<b><?php echo (trim($subscriberName)) ? trim($subscriberName) : underLine(35); ?></b></td>
					<td class="text_10b" width="180" height="<?php echo ($subscriber_DOB != "--") ? "5px" : "14px"; ?>" valign="<?php echo ($subscriber_DOB != "--") ? "bottom" : "top"; ?>">&nbsp;<b><?php echo ($subscriber_DOB != "--") ? $subscriber_DOB : underLine(35); ?></b></td>		
					<td class="text_10b" width="180" height="<?php echo ($insPriDetails[0]['subscriber_ss']) ? "5px" : "14px"; ?>" valign="<?php echo ($insPriDetails[0]['subscriber_ss']) ? "bottom" : "top"; ?>">&nbsp;<b><?php echo (($insPriDetails[0]['subscriber_ss']) ? $insPriDetails[0]['subscriber_ss'] : underLine(35)); ?></b></td>
				</tr> 
				
				<tr>
					<td class="text_10" style="color:#444444"><i>Subscriber Relation</i></td>
					<td class="text_10" style="color:#444444"><i>Subscriber Name</i></td>
					<td class="text_10" style="color:#444444"><i>DOB</i></td>
					<td class="text_10" style="color:#444444"><i>Social Security#</i></td>
				</tr>	
				<tr><td height="7"></td></tr>
			<?php
				$subscriberAddress = $insPriDetails[0]['subscriber_city'].', ';
				$subscriberAddress .= $insPriDetails[0]['subscriber_state'].' ';
				$subscriberAddress .= $insPriDetails[0]['subscriber_postal_code'];
				if(trim($subscriberAddress) == ","){
					$subscriberAddress = "";
				}
			?>
				<tr align="left"> 	
					<td class="text_10b" width="180" height="<?php echo ($insPriDetails[0]['subscriber_street']) ? "5px" : "14px"; ?>" valign="<?php echo ($insPriDetails[0]['subscriber_street']) ? "bottom" : "top"; ?>">&nbsp;<b><?php echo ($insPriDetails[0]['subscriber_street']) ? $insPriDetails[0]['subscriber_street'] : underLine(35); ?></b></td>
					<td class="text_10b" width="180" height="<?php echo (trim($subscriberAddress)) ? "5px" : "14px"; ?>" valign="<?php echo (trim($subscriberAddress)) ? "bottom" : "top"; ?>">&nbsp;<b><?php echo (trim($subscriberAddress)) ? trim($subscriberAddress) : underLine(35); ?></b></td>
					<td class="text_10b" width="180">&nbsp;<b>Yes</b></td>		
					<td class="text_10b" width="180">&nbsp;<b>Yes</b></td>
				</tr>			
				<tr>
					<td class="text_10" style="color:#444444"><i>Street</i></td>
					<td class="text_10" style="color:#444444"><i>City, State Zip</i></td>
					<td class="text_10" style="color:#444444"><i>Payment Authorized</i></td>
					<td class="text_10" style="color:#444444"><i>Signature on File</i></td>
				</tr>
			</table>				
			<?php
			if($insPriDetails[0]['referal_required'] == 'Yes'){
				$id = $insPriDetails[0]['id'];
				$qry = imw_query("select * from patient_reff where ins_data_id = $id
						and (no_of_reffs > 0 or now() between effective_date and end_date)");
				while($row= imw_fetch_array($qry)){
					$reffDetails[] = $row;
				}
			?>
			<table width="100%" cellpadding="0" cellspacing="0" border="<?php echo $border; ?>" rules="none">					
				<tr valign="middle" height="25px" bgcolor="#c0c0c0">
					<td class="text_10" colspan="4"><b><?php echo strtoupper('Referral Required '); ?></b></td>
				</tr>	
				<tr><td height="7"></td></tr>	
			<?php
				if($reffDetails[0]['reff_date'] != '0000-00-00')
					$reff_date = get_date_format($reffDetails[0]['reff_date']);
				else
					$reff_date = '';
				$reff_phy_id = $reffDetails[0]['reff_phy_id'];
				//--- get Reffering Physician Name --------
				$qry = imw_query("select concat(LastName,', ',FirstName) as name,MiddleName from
						refferphysician where physician_Reffer_id = $reff_phy_id");
				while($row = imw_fetch_array($qry)){
					$reffPhyDetails[] = $row;
				}
				$reffPhyName = $reffPhyDetails[0]['name'].' ';
				$reffPhyName .= $reffPhyDetails[0]['MiddleName'];
			?>
				<tr align="left"> 	
					<td class="text_10b" width="180" height="<?php echo (trim($reffPhyName)) ? "5px" : "14px"; ?>" valign="<?php echo (trim($reffPhyName)) ? "bottom" : "top"; ?>">&nbsp;<b><?php echo (trim($reffPhyName)) ? trim($reffPhyName) : underLine(35); ?></b></td>
					<td class="text_10b" width="180" height="<?php echo (trim($reffDetails[0]['reffral_no'])) ? "5px" : "14px"; ?>" valign="<?php echo (trim($reffDetails[0]['reffral_no'])) ? "bottom" : "top"; ?>">&nbsp;<b><?php echo (trim($reffDetails[0]['reffral_no'])) ? trim($reffDetails[0]['reffral_no']) : underLine(35); ?></b></td>
					<td class="text_10b" width="180" height="<?php echo ($reff_date) ? "5px" : "14px"; ?>" valign="<?php echo ($reff_date) ? "bottom" : "top"; ?>">&nbsp;<b><?php echo ($reff_date) ? $reff_date : underLine(35); ?></b></td>		
					<td class="text_10b" width="180" height="<?php  if($reffDetails[0]['no_of_reffs'] || $reffDetails[0]['reff_used']) {echo "5px";} else{ echo "14px";} ?>" valign="<?php  if($reffDetails[0]['no_of_reffs'] || $reffDetails[0]['reff_used']) {echo "bottom";} else{ echo "top";} ?>">&nbsp;<b><?php  if($reffDetails[0]['no_of_reffs'] || $reffDetails[0]['reff_used']) {echo $reffDetails[0]['no_of_reffs'] + $reffDetails[0]['reff_used'];} else{ echo underLine(35);} ?></b></td>
				</tr>
			
				<tr>
					<td class="text_10" style="color:#444444" width="180"><i>Referring Dr.</i></td>
					<td class="text_10" style="color:#444444" width="180"><i>Ref #</i></td>
					<td class="text_10" style="color:#444444" width="180"><i>Ref Date</i></td>
					<td class="text_10" style="color:#444444" width="180"><i># of Visits</i></td>
				</tr>		
				<tr><td height="7"></td></tr>
			<?php
				if($reffDetails[0]['effective_date'] != '0000-00-00'){
					$effective_date = get_date_format($reffDetails[0]['effective_date']);
				}	
				else{
					$effective_date = '';
				}	
				if($reffDetails[0]['end_date'] != '0000-00-00'){
					$end_date = get_date_format($reffDetails[0]['end_date']);
				}	
				else{
					$end_date = '';
				}	
			?>
				<tr align="left"> 	
					<td class="text_10b" width="220" height="<?php echo ($effective_date) ? "5px" : "14px"; ?>" valign="<?php echo ($effective_date) ? "bottom" : "top"; ?>">&nbsp;<b><?php echo ($effective_date) ? $effective_date : underLine(35); ?></b></td>
					<td class="text_10b" width="180" height="<?php echo (trim($end_date)) ? "5px" : "14px"; ?>" valign="<?php echo (trim($end_date)) ? "bottom" : "top"; ?>">&nbsp;<b><?php echo (trim($end_date)) ? trim($end_date) : underLine(35); ?></b></td>
					<td class="text_10b" colspan="2" height="<?php echo ($reffDetails[0]['note']) ? "5px" : "14px"; ?>" valign="<?php echo ($reffDetails[0]['note']) ? "bottom" : "top"; ?>">&nbsp;<b><?php echo ($reffDetails[0]['note']) ? $reffDetails[0]['note'] : underLine(35); ?></b></td>		
				</tr>
			
				<tr>
					<td class="text_10" style="color:#444444"><i>Start Date</i></td>
					<td class="text_10" style="color:#444444"><i>End date</i></td>
					<td class="text_10" colspan="2" style="color:#444444"><i>Notes</i></td>
				</tr>
				<tr><td height="7"></td></tr>
			</table>
			<?php
			}
		}	
	}
//}
}
//---  Insurance Details End --------
//---  Show Scan Card Documents --------
$pageHeight = 900;
if(count($scan_card_arr)>0){
	//echo '<newpage>';
	echo '<page>';
    foreach($ins_caseidArr as $ins_caseid) {
        foreach($scan_card_arr as $key => $ins_scan_arr){
            foreach($ins_scan_arr as $scan_key => $scan_val){
                $img_real_path = $GLOBALS['fileroot']."/data/".constant('PRACTICE_PATH').$scan_val;
                //$img_real_path = realpath('../main/uploaddir'.$scan_val);
                $ImageSize = getimagesize($img_real_path);
                $imageSizeArr[] = $ImageSize[1];
                $pageCount ++;
            }
		}
	}
}
$i=1;
if(count($scan_card_arr)>0){
    foreach($ins_caseidArr as $ins_caseid) {
	foreach($scan_card_arr[$ins_caseid] as $key => $ins_scan_arr){
		if(strtolower($key)=="secondary"){
			echo '</page><page>';	
		}
		?>			
		<table width="100%" border="<?php echo $border; ?>" rules="none" cellpadding="1" cellspacing="1">
		<?php
		if(count($ins_scan_arr) > 0){
		?>
			<tr valign="middle"  class="text_10" >
				<td class="text_10" height="1" colspan="2" style="border-bottom:1px solid #012778;!important;height:1px;">&nbsp;</td>
			</tr>
			<tr valign="middle">
				<td class="text_10" height="2" colspan="2" ></td>
			</tr>
			<tr valign="middle"  >
				<td class="text_10" colspan="2" style="font-size:14px;color:#012778;font-weight:bold;"><b><?php echo strtoupper($key.' scan document'); ?></b></td>
			</tr>
		</table>
		<?php	
		
			foreach($ins_scan_arr as $scan_key => $scan_val){	
				$img_path = $cpr->data_file_path.$scan_val;
				$img_real_path = $cpr->data_file_path.$scan_val;
				$copy_dir_path = explode('/',$dir_real_path);
				array_pop($copy_dir_path);
				$copy_dir_path = join("/",$copy_dir_path).'/html2pdfprint/';
				if(file_exists($img_real_path) != '' && is_dir($img_real_path) == ''){
					$img_name = substr($scan_val,strrpos($scan_val,'/')+1);
					$patient_img[$key.'-'.$scan_key] = $img_name;
					copy($img_real_path,$copy_dir_path.$img_name);
					copy($img_real_path,$copy_dir_path.$img_name);
					//$img_path = $img_name;
					$img_path = $GLOBALS['fileroot']."/data/".constant('PRACTICE_PATH')."/PatientId_".$pid."/".$img_name;
					$priImageSize = getimagesize($img_real_path);
					
					$newSize = '';
					if($priImageSize[0] > 650){
						$newSize = $cpr->imageResize($img_real_path,350,400);
						$image_view = '<img src="'.$img_path.'" '.$newSize.'>';												
						$priImageSize[0] = 400;
						$priImageSize[1] = 350;
						
					}else if($priImageSize[0] > 395 && $priImageSize[1] < 840){
						$newSize = $cpr->imageResize(680,400,710);						
						$image_view = '<img src="'.$img_path.'" '.$newSize.'>';
						$priImageSize[0] = 710;
					}
					elseif($priImageSize[0] > 700){
						//width > 700
						//$newSize = ManageData::imageResize($priImageSize[0],$priImageSize[1],840);						
						$newSize = $cpr->imageResize($img_real_path,700);
						$image_view = '<img src="'.$img_path.'" '.$newSize.'>';												
						$priImageSize[1] =700;
						
					}					
					elseif($priImageSize[1] > 840){
						//hight > 700
						//$newSize = ManageData::imageResize($priImageSize[0],$priImageSize[1],840);						
						$newSize = $cpr->imageResize($img_real_path,700,800);
						$image_view = '<img src="'.$img_path.'" '.$newSize.'>';												
						$priImageSize[1] = 800;
						
					}else{					
						$newSize = $priImageSize[3];
						$image_view = '<img src="'.$img_path.'" '.$newSize.'>';
						//echo $image_view; die;
					}							
					if($priImageSize[1] > 800 ){					
						//echo '</newpage><newpage>';
						echo '</page><page>';
					}								
					?>						
					<table width="100%" cellpadding="2" cellspacing="2" border="<?php echo $border; ?>">
						<tr>
							<td class="text_10"><b><?php echo $img_name; ?></b></td>
						</tr>
						<tr>
							<td class="text_10b"><i><?php echo $scan_key; ?></i></td>
						</tr>
						<tr>
							<td colspan="2" align="center"><?php echo $image_view; ?></td>
						</tr>
					</table>
					<?php
					$pageHeight = $pageHeight - $priImageSize[1];	
					if(($pageHeight < $imageSizeArr[$i-1]) && $i < $pageCount ){
						//echo '</newpage><newpage>';
						//echo '</page><page>';	
						$pageHeight = 900;
					}
					
					$i++;				
				}
			}
            }
		}		
	}
	//echo '</newpage>';
	echo '</page>';
}
?>