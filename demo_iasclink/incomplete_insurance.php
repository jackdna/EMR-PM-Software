<?php
// Under MIT License
// Use, Modify, Distribute under MIT License.
// MIT License 2019
?>
<?php
function getIncompleteInsFun($patient_id,$bgHeadingImage,$patient_name) {
	include("common/conDb.php");
	$inCompIns='';
	if($patient_id) {
		
		$inCompleteInsDataQry 		= "SELECT insCase.case_name,insData.id,insData.type,insData.ins_provider 
										FROM insurance_data insData 
										LEFT JOIN iolink_insurance_case insCase ON insCase.ins_caseid = insData.ins_caseid
										WHERE insData.patient_id='".$patient_id."' 
										AND insData.inCompleteInsStatus='Yes' 
										AND insData.actInsComp='1'
										ORDER BY insData.type";
		$inCompleteInsDataRes 		= imw_query($inCompleteInsDataQry) or die(imw_error().$inCompleteInsDataQry);
		$inCompleteInsDataNumRow 	= imw_num_rows($inCompleteInsDataRes);
		if($inCompleteInsDataNumRow>0) {//background="'.$bgHeadingImage.'"
			$inCompIns.='
			<div id="divIncompleteInsId'.$patient_id.'" style="position:absolute; border:1px solid; border-color:#000000; display:none; width:600px;  overflow:auto; left:260px; top:30px; " >
				<table class=" table_collapse alignCenter" style="background-color:#FFFFFF;" >
					<tr class="text_10b" style="height:22px; background-color:#E0E0E0;">
						<td class="alignRight"><img alt="Incomplete Insurance" src="images/left_new.gif" style="height:24px; width:3px;"></td>
						<td class="text_10b alignLeft valignMiddle" style="padding-left:10px; background-color:#c0aa1e;" >
							Incomplete Insurance&nbsp;&nbsp;&nbsp;&nbsp;<span style="background-color:#0066FF; font-weight:bold; color:#FFFFFF; font-family:Verdana, Arial, Helvetica, sans-serif;">'.$patient_name.'&nbsp;-&nbsp;'.$patient_id.'&nbsp;</span>
						</td>
						<td class="text_10b alignLeft valignMiddle" style="background-color:#c0aa1e;" ><img alt="Close" src="images/close.jpg" onClick="document.getElementById(\'divIncompleteInsId'.$patient_id.'\').style.display=\'none\'" /></td>
						<td class="alignLeft valignTop"><img alt="Incomplete Insurance" src="images/right_new.gif" style="width:3px; height:24px;"></td>
					</tr>
				</table>	
				<table class="table_pad_bdr alignCenter" style="width:99%; border:none; background-color:#FFFFFF;" >	
					<tr class="text_10b valignMiddle" style="background-color:#FFFFFF;">
						<td class="alignLeft valignTop" >Type</td>
						<td >Provider</td>
						<td >Case</td>
					</tr>';
				$inCompCntr=0;
				while($inCompleteInsDataRow = imw_fetch_array($inCompleteInsDataRes)) {
					$inCompCntr++;
					$inCompColor='#E0E0E0';//FBD78D
					if($inCompCntr%2==0) { $inCompColor='#FFFFFF';}//#F3F8F2
					$inCompIns.='
					<tr class="text_10" style="background-color:'.$inCompColor.'">
						<td class="alignLeft valignTop">'.ucfirst($inCompleteInsDataRow['type']).'</td>
						<td >'.$inCompleteInsDataRow['ins_provider'].'</td>
						<td >'.$inCompleteInsDataRow['case_name'].'</td>
					</tr>';
				}
			$inCompIns.='
				</table>
			</div>';
		}
		
	}
	return $inCompIns;	
}


function incompleteInsProviderDiv($type,$insProviderArr,$insCaseNameArr,$patient_name,$patient_id) {
	$inCompInsProvider='';
	$inCompInsProvider.='
	<div id="divIncompleteInsProviderId'.$type.$patient_id.'" style="position:absolute; border:1px solid; border-color:#000000; display:none; width:600px;  overflow:auto; left:260px; top:30px; " >
		<table class="table_collapse alignCenter" style="background-color:#FFFFFF;" >
			<tr class="text_10b" style="height:22px; background-color:#E0E0E0;">
				<td class="alignRight"><img alt="Unmatched Provider(s)" src="images/left_new.gif" style="width:3px; height:24px;"></td>
				<td class="text_10b alignLeft valignMiddle" style="padding-left:10px; background-color:#c0aa1e;" >
					Unmatched Provider(s) With iASC&nbsp;&nbsp;&nbsp;&nbsp;<span style="background-color:#0066FF; font-weight:bold; color:#FFFFFF; font-family:Verdana, Arial, Helvetica, sans-serif;">'.$patient_name.'&nbsp;-&nbsp;'.$patient_id.'&nbsp;</span>
				</td>
				<td class="text_10b alignLeft valignMiddle" style="background-color:#c0aa1e;" ><img alt="Close" src="images/close.jpg" onClick="document.getElementById(\'divIncompleteInsProviderId'.$type.$patient_id.'\').style.display=\'none\'" /></td>
				<td class="alignLeft valignTop"><img alt="Unmatched Provider(s)" src="images/right_new.gif" style="width:3px; height:24px;"></td>
			</tr>
		</table>	
		<table class="table_pad_bdr" style="width:99%; border:none; background-color:#FFFFFF;" >	
			<tr class="text_10b valignMiddle" style="background-color:#FFFFFF;">
				<td class="alignLeft valignTop" >Type</td>
				<td >Provider</td>
				<td >Case</td>
			</tr>';
		$inCompPrvdCntr=0;
		foreach($insProviderArr as $keyProvd => $insProvdNme) {
			$inCompPrvdCntr++;
			$inCompPrvdColor='#E0E0E0';//FBD78D
			if($inCompPrvdCntr%2==0) { $inCompPrvdColor='#FFFFFF';}//#F3F8F2
			$inCompInsProvider.='
			<tr class="text_10" style="background-color:'.$inCompPrvdColor.'">
				<td class="alignLeft valignTop" >'.ucfirst($type).'</td>
				<td >'.$insProvdNme.'</td>
				<td >'.$insCaseNameArr[$keyProvd].'</td>
			</tr>';
		}
	$inCompInsProvider.='
		</table>
	</div>';
	
	//echo $inCompInsProvider;
	return $inCompInsProvider;		
	
}
?>	