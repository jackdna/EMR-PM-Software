<?php
/*
// The MIT License (MIT)
// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
*/
/*
function underLineDemoGraphics($to){
	$NBSP = "<u>";
	for($counter = 1; $counter<=$to; $counter++){
		$NBSP .= "&nbsp;";	
	}
	$NBSP .= "</u>";
	return $NBSP;
} */
if($pid == ""){
?>
	<script>
		alert('Please select Patient to Proceed');
		window.close();
	</script>
<?php
}
//--- Get Patient Image ----
$p_imagename = $pt_data['p_imagename'];
if($p_imagename){
	$pt_images = $cpr->get_pt_images($p_imagename);
	$patient_img = $pt_images['patient_img'];		//Array 
	$patientImage = $pt_images['patientImage'];		//Single img
}
?>
<table cellpadding='0' cellspacing='0' border='<?php echo $border; ?>' rules="none" width='100%'>
	<tr>
		<td colspan="4" height="1" style="width:750px;border-bottom:1px solid #012778;!important;height:1px;">&nbsp;</td>
	</tr>
	<tr>
		<td colspan="4" height="2"></td>
	</tr>
	<tr>
		<td colspan="4" class="text_cap paddingTop10" align="left" style="font-size:14px;color:#012778;font-weight:bold;" ><b><?php echo strtoupper('Patient Demographics'); ?></b></td>
	</tr>
	<tr><td height="7"></td></tr>
	<tr>
	 <td colspan="4">
		<table cellpadding='0' cellspacing='0' border='<?php echo $border; ?>' rules="none" width='100%'>
			<tr align="left" valign="top">
				<td class="text_10b" width="180" valign="bottom" height="5px"><b><?php echo trim($pt_data['title'].' '.ucwords($pt_data['patientName'])); ?></b></td>
				<td class="text_10b" width="180" valign="<?php echo (trim($pt_data['date_of_birth'])!="" && $pt_data['date_of_birth']!="00-00-0000") ? "bottom" : "top"; ?>" height="<?php echo (trim($pt_data['date_of_birth'])!="" && $pt_data['date_of_birth']!="00-00-0000") ? "5px" : "14px"; ?>" >&nbsp;<b><?php echo (trim($pt_data['date_of_birth'])!="" && $pt_data['date_of_birth']!="00-00-0000") ? $pt_data['date_of_birth']."(".$pt_data['age'].")" : ""; ?></b></td>
				<td class="text_10b" width="180" valign="bottom" height="5px"><b><?php echo $pt_data['id']; ?></b></td>		
				<td rowspan="12" valign="top"><?php print $patientImage; ?></td>
			</tr>
			<tr align="left" valign="top">
				<td class="text_10" style="color:#444444" width="180" valign="top" ><i>Patient Name</i></td>
				<td class="text_10" style="color:#444444" width="180" valign="top" ><i>DOB</i></td>
				<td class="text_10" style="color:#444444" width="180" valign="top" ><i>ID</i></td>
				
			</tr>
			<tr height="5"><td></td></tr>
			<?php
			//--- Check That All Fields Are Not Blank -------
			if($pt_data['pt_ss'] != '' || $pt_data['phyName'] != '' || trim($pt_data['status']) != ''){
			?>	
			<tr align="left" valign="top">
				<td class="text_10b" width="180" height="<?php echo ($pt_data['pt_ss'] != "--" && trim($pt_data['pt_ss']) != "") ? "5px" : "14px"; ?>" valign="<?php echo ($pt_data['pt_ss'] != "--" && trim($pt_data['pt_ss']) != "") ? "bottom" : "top"; ?>">&nbsp;<b><?php echo ($pt_data['pt_ss'] != "--" && trim($pt_data['pt_ss']) != "") ? $pt_data['pt_ss'] : ""; ?></b></td>
				<td class="text_10b" width="180" height="<?php echo (trim($pt_data['phyName'] != "")) ? "5px" : "14px"; ?>" valign="<?php echo (trim($pt_data['phyName']) != "") ? "bottom" : "top"; ?>">&nbsp;<b><?php echo (trim($pt_data['phyName']) != "") ? $pt_data['phyName'] : ""; ?></b></td>
				<td class="text_10b" width="180" height="<?php echo (trim(ucwords($pt_data['status']))) ? "5px" : "14px"; ?>" valign="<?php echo (trim(ucwords($pt_data['status']))) ? "bottom" : "top"; ?>">&nbsp;<b><?php echo (trim(ucwords($pt_data['status']))) ? trim(ucwords($pt_data['status'])) : ""; ?></b></td>
			</tr>
			<tr align="left" valign="top">
				<td class="text_10" style="color:#444444" width="180" valign="top"><i>Social Security#</i></td>
				<td class="text_10" style="color:#444444" width="180" valign="top"><i>Physician</i></td>
				<td class="text_10" style="color:#444444" width="180" valign="top"><i>Marital</i></td>
			</tr>
			<tr height="5"><td></td></tr>
			<?php
			}
			//--- Check That All Fields Are Not Blank -------
			if($pt_data['sex'] != '' || $pt_data['reffPhyName'] != '' || $pt_data['facilityPracCode'] != ''){
			?>				
			<tr align="left" valign="top">
				<td class="text_10b"><b><?php print $pt_data['sex']; ?></b></td>
				<td class="text_10b"><b><?php print $pt_data['reffPhyName']; ?></b></td>
				<td class="text_10b" valign="top"><b><?php print $pt_data['facilityPracCode']; ?></b></td>
			</tr>
			<tr align="left" valign="top">
				<td class="text_10" style="color:#444444"><i><?php if($pt_data['sex']) print 'Sex'; else '&nbsp;'; ?></i></td>
				<td class="text_10" style="color:#444444"><i><?php if($pt_data['reffPhyName']) print 'Referring Dr.'; else '&nbsp;'; ?></i></td>
				<td class="text_10" style="color:#444444"><i><?php if($pt_data['facilityPracCode']) print 'Facility'; else '&nbsp;'; ?></i></td>
			</tr>
			<tr height="5"><td></td></tr>
		<?php
		}
    
    	//--- Check That All Fields Are Not Blank -------
			if($pt_data['sexual_orientation'] != '' || $pt_data['gender_identity'] != '' ){
			?>				
			<tr align="left" valign="top">
				<td class="text_10b"><b><?php print $pt_data['sexual_orientation']; ?></b></td>
				<td class="text_10b"><b><?php print $pt_data['gender_identity']; ?></b></td>
				<td class="text_10b" valign="top">&nbsp;</td>
			</tr>
			<tr align="left" valign="top">
				<td class="text_10" style="color:#444444"><i><?php if($pt_data['sexual_orientation']) print 'Sexual Orientation'; else '&nbsp;'; ?></i></td>
				<td class="text_10" style="color:#444444"><i><?php if($pt_data['gender_identity']) print 'Gender Identity'; else '&nbsp;'; ?></i></td>
				<td class="text_10" style="color:#444444">&nbsp;</td>
			</tr>
			<tr height="5"><td></td></tr>
		<?php
		}?>
	</table>
	</td>
	</tr>

	<tr><td height="7"></td></tr>
	<?php
	//--- Check That All Fields Are Not Blank -------
	//if($pt_data['sex'] != '' || $pt_data['reffPhyName'] != '' || $pt_data['driving_licence'] != '' || $pt_data['createByName'] != ''){
	?>				
	<tr align="left" valign="top">
		<td class="text_10b" width="180" height="<?php echo ($pt_data['created_date'] != "--" && trim($pt_data['created_date']) != "") ? "5px" : "14px"; ?>" valign="<?php echo ($pt_data['created_date'] != "--" && trim($pt_data['created_date']) != "") ? "bottom" : "top"; ?>">&nbsp;<b><?php echo ($pt_data['created_date'] != "--" && trim($pt_data['created_date']) != "") ? $pt_data['created_date'] : ""; ?></b></td>
		<td class="text_10b" width="180" height="<?php echo (trim($pt_data['primary_care_phy_name'])) ? "5px" : "14px"; ?>" valign="<?php echo (trim($pt_data['primary_care_phy_name'])) ? "bottom" : "top"; ?>">&nbsp;<b><?php echo (trim($pt_data['primary_care_phy_name'])) ? $pt_data['primary_care_phy_name'] : ""; ?></b></td>
		<td class="text_10b" width="180" height="<?php echo (trim($pt_data['driving_licence'])) ? "5px" : "14px"; ?>" valign="<?php echo (trim($pt_data['driving_licence'])) ? "bottom" : "top"; ?>">&nbsp;<b><?php echo (trim($pt_data['driving_licence'])) ? $pt_data['driving_licence'] : "" ; ?></b></td>
		<td class="text_10b" width="180" height="<?php echo (trim($pt_data['createByName'])) ? "5px" : "14px"; ?>" valign="<?php echo (trim($pt_data['createByName'])) ? "bottom" : "top"; ?>">&nbsp;<b><?php echo (trim($pt_data['createByName'])) ? ucwords($pt_data['createByName']) : "" ;  ?></b></td>
	</tr>
	<tr align="left" valign="top">
		<td class="text_10" style="color:#444444" width="180" valign="top"><i>Registration date</i></td>
		<td class="text_10" style="color:#444444" width="180"><i>Primary Care Phy.</i></td>
		<td class="text_10" style="color:#444444" width="180"><i>DL #</i></td>
		<td class="text_10" style="color:#444444" width="180"><i>Created By</i></td>			
	</tr>
	<tr><td height="7"></td></tr>
	<?php
	//}
	//--- Check That All Fields Are Not Blank -------
	//if(trim($pt_data['street']) != '' || $pt_data['phone_home'] != '' || $pt_data['cityAddress'] != '' || $pt_data['phone_biz'] != '' || $pt_data['phone_cell'] != ''){
	$street = trim(ucwords($pt_data['street']));
	if(trim(ucwords($pt_data['street'])) != '' && trim(ucwords($pt_data['street2'])) != ''){
		$street .= '<br>';
	}
	if(trim(ucwords($pt_data['street2']))){
		$street .= trim(ucwords($pt_data['street2']));
	}
	
	?>
	<tr align="left" valign="top">
		<td class="text_10b" style="width:180px;" height="<?php echo (trim($street)) ? "5px" : "14px"; ?>" valign="<?php echo (trim($street)) ? "bottom" : "top"; ?>">&nbsp;<b><?php echo (trim($street)) ? $street : ""; ?></b></td>
		<td class="text_10b" style="width:180px;" height="<?php echo (trim(ucwords($pt_data['cityAddress']))) ? "5px" : "14px"; ?>" valign="<?php echo (trim(ucwords($pt_data['cityAddress']))) ? "bottom" : "top"; ?>">&nbsp;<b><?php echo (trim(ucwords($pt_data['cityAddress']))) ? trim(ucwords($pt_data['cityAddress'])) : ""; ?></b></td>
		<td class="text_10b" style="width:190px; vertical-align:bottom;" height="<?php echo ($pt_data['phone_home'] != "000-000-0000" && trim($pt_data['phone_home']) != "") ? "5px" : "14px"; ?>">&nbsp;<b><?php echo ($pt_data['phone_home'] != "000-000-0000" && trim($pt_data['phone_home']) != "") ? core_phone_format($pt_data['phone_home']) : ""; ?></b></td>
		<td class="text_10b" style="width:180px;" height="<?php echo ($pt_data['phone_biz']  != "000-000-0000" && trim($pt_data['phone_biz'])  != "") ? "5px" : "14px"; ?>" valign="<?php echo ($pt_data['phone_biz']  != "000-000-0000" && trim($pt_data['phone_biz'])  != "") ? "bottom" : "bottom"; ?>">&nbsp;<b><?php echo ($pt_data['phone_biz']  != "000-000-0000" && trim($pt_data['phone_biz'])  != "") ? core_phone_format($pt_data['phone_biz']) : ""; ?></b></td>
	</tr>
	
	<tr align="left" valign="top">
		<td class="text_10" style="color:#444444" width="180"><i>Street</i></td>
		<td class="text_10" style="color:#444444" width="180"><i>City, State Zip</i></td>
		<td class="text_10" style="color:#444444" width="180"><i>Home Phone#</i></td>
		<td class="text_10" style="color:#444444" width="180"><i>Work Phone#</i></td>
	</tr>
	<tr><td height="7"></td></tr>	
	<tr align="left" valign="top">
		<td class="text_10b" style="width:180px;" height="<?php echo ($pt_data['phone_cell']  != "000-000-0000" && trim($pt_data['phone_cell'])  != "") ? "5px" : "14px"; ?>" valign="<?php echo ($pt_data['phone_cell']  != "000-000-0000" && trim($pt_data['phone_cell'])  != "") ? "bottom" : "bottom"; ?>">&nbsp;<b><?php echo ($pt_data['phone_cell']  != "000-000-0000" && trim($pt_data['phone_cell'])  != "") ? core_phone_format($pt_data['phone_cell']) : ""; ?></b></td>
		<td class="text_10b" style="width:180px;" ></td>
		<td class="text_10b" style="width:180px;"></td>
		<td class="text_10b" style="width:190px; vertical-align:bottom;" ></td>
	</tr>
	<tr align="left" valign="top">
		<td class="text_10" style="color:#444444" width="180"><i>Mobile Phone#</i></td>
		<td class="text_10" style="color:#444444" width="180"></td>
		<td class="text_10" style="color:#444444" width="180"></td>
		<td class="text_10" style="color:#444444" width="180"></td>
	</tr>			
	<tr><td height="7"></td></tr>
	<?php
	//}
	?>
</table>
<?php
//--- Emergency Contact Details Check ------
//if(trim($pt_data['contact_relationship']) != '' || $pt_data['phone_contact'] != ''){
?>
<table width="100%" rules="none" cellpadding="0" cellspacing="0" border="<?php echo $border; ?>">
	<tr valign="middle" height="25px" class="text_10" bgcolor="#c0c0c0">
		<td align="left" colspan="3"><b><?php echo strtoupper('Emergency Contact Information'); ?></b></td>
	</tr>
	<tr><td height="7"></td></tr>
	<tr align="left" valign="top">
		<td class="text_10b" width="180" height="<?php echo (trim($pt_data['contact_relationship']) != "") ? "5px" : "14px"; ?>" valign="<?php echo (trim($pt_data['contact_relationship']) != "") ? "bottom," : "top"; ?>">&nbsp;<b><?php echo (trim($pt_data['contact_relationship'])) ? $pt_data['contact_relationship'] : "";  ?></b></td>
		<td class="text_10b" width="180" height="<?php echo (trim($pt_data['phone_contact']) != "" && $pt_data['phone_contact'] != "000-000-0000") ? "5px" : "14px"; ?>" valign="<?php echo (trim($pt_data['phone_contact']) != "" && $pt_data['phone_contact'] != "000-000-0000") ? "bottom," : "top"; ?>">&nbsp;<b><?php echo (trim($pt_data['phone_contact']) && $pt_data['phone_contact'] != "000-000-0000" ) ? $pt_data['phone_contact'] : ""; ?></b></td>
		
	</tr>
	<tr align="left" valign="top">
		<td class="text_10" style="color:#444444" width="180"><i>Emergency Contact</i></td>
		<td class="text_10" style="color:#444444" width="180"><i>Emergency Phone</i></td>
		
	</tr>
	<tr height="10"><td></td></tr>
</table>
<?php

//---- Get Responsible Party Details if patient has -------
//for($r=0;$r<count($pt_data['res_party_detail']);$r++){
	$resp_name = $pt_data['res_party_detail'][0]['lname'].', ';
	$resp_name .= $pt_data['res_party_detail'][0]['fname'].' ';
	$resp_name .= $pt_data['res_party_detail'][0]['mname'];
	if(trim($resp_name) == ","){
		$resp_name = "";
	}	
	$dob = $pt_data['res_party_detail'][0]['dob'];
	$dateOfBirth = get_date_Format($dob);
	list($resp_y,$resp_m,$resp_d) = explode('-',$dob);
	$resp_age = date('Y') - $resp_y;
	$city_address = $pt_data['res_party_detail'][0]['city'].', ';
	$city_address .= $pt_data['res_party_detail'][0]['state'].' ';
	$city_address .= $pt_data['res_party_detail'][0]['zip'];
	if(trim($city_address) == ","){
		$city_address = "";
	}
	?>
<table width="100%" rules="none" cellpadding="0" cellspacing="0" border="<?php echo $border; ?>">	
	<tr valign="middle" height="25px" class="text_10" bgcolor="#c0c0c0">
		<td align="left" width="220" colspan="4"><b><?php echo strtoupper('Responsibility Party'); ?></b></td>
	</tr>
	<tr><td height="7"></td></tr>
	<tr align="left">
		<td class="text_10b" width="180" height="<?php echo (trim(ucwords($resp_name))) ? "5px" : "14px"; ?>" valign="<?php echo (trim(ucwords($resp_name))) ? "bottom" : "top"; ?>">&nbsp;<b><?php echo (trim(ucwords($resp_name))) ? trim(ucwords($resp_name)) : ""; ?></b></td>
		<td class="text_10b" width="180" height="<?php echo (trim(ucwords($pt_data['res_party_detail'][0]['relation']))) ? "5px" : "14px"; ?>" valign="<?php echo (trim(ucwords($pt_data['res_party_detail'][0]['relation']))) ? "bottom" : "top"; ?>">&nbsp;<b><?php echo (trim(ucwords($pt_data['res_party_detail'][0]['relation']))) ? trim(ucwords($pt_data['res_party_detail'][0]['relation'])) : ""; ?></b></td>
		<td class="text_10b" width="180" height="<?php echo ($dateOfBirth != "--") ? "5px" : "14px"; ?>" valign="top">&nbsp;<b><?php echo ($dateOfBirth != "--") ? $dateOfBirth.' ( '.$resp_age.' ) ' : ""; ?></b></td>
		<td class="text_10b" width="180" height="<?php echo (trim(ucwords($pt_data['res_party_detail'][0]['sex']))) ? "5px" : "14px"; ?>" valign="top">&nbsp;<b><?php echo (trim(ucwords($pt_data['res_party_detail'][0]['sex']))) ? trim(ucwords($pt_data['res_party_detail'][0]['sex'])) : ""; ?></b></td>
	</tr>

	<tr align="left">
		<td class="text_10" style="color:#444444" width="180">&nbsp;<i>Name</i></td>
		<td class="text_10"style="color:#444444" width="180">&nbsp;<i>Relation</i></td>
		<td class="text_10" width="180" style="color:#444444">&nbsp;<i>DOB</i></td>
		<td class="text_10"style="color:#444444" width="180">&nbsp;<i>Sex</i></td>
	</tr>
	<tr><td height="7"></td></tr>	
	<tr align="left">
		<td class="text_10b" width="180" height="<?php echo ($pt_data['res_party_detail'][0]['ss']) ? "5px" : "14px"; ?>" valign="top">&nbsp;<b><?php echo ($pt_data['res_party_detail'][0]['ss']) ? $pt_data['res_party_detail'][0]['ss'] : ""; ?></b></td>
		<td class="text_10b" width="180" height="<?php echo ($pt_data['res_party_detail'][0]['licence']) ? "5px" : "14px"; ?>" valign="top">&nbsp;<b><?php echo ($pt_data['res_party_detail'][0]['licence']) ? $pt_data['res_party_detail'][0]['licence'] : ""; ?></b></td>
		<td class="text_10b" width="180" height="<?php echo (trim(ucwords($pt_data['res_party_detail'][0]['marital']))) ? "5px" : "14px"; ?>" valign="<?php echo (trim(ucwords($pt_data['res_party_detail'][0]['marital']))) ? "bottom" : "top"; ?>">&nbsp;<b><?php echo (trim(ucwords($pt_data['res_party_detail'][0]['marital']))) ? trim(ucwords($pt_data['res_party_detail'][0]['marital'])) : ""; ?></b></td>
		<td class="text_10b" width="180" height="<?php echo (trim(ucwords($pt_data['res_party_detail'][0]['work_ph']))) ? "5px" : "14px"; ?>" valign="<?php echo (trim(ucwords($pt_data['res_party_detail'][0]['work_ph']))) ? "bottom" : "top"; ?>">&nbsp;<b><?php echo (trim(ucwords($pt_data['res_party_detail'][0]['work_ph']))) ? trim(core_phone_format($pt_data['res_party_detail'][0]['work_ph'])) : ""; ?></b></td>
	</tr>
	
	<tr align="left">
		<td class="text_10" width="180" style="color:#444444"><i>Social Security#</i></td>
		<td class="text_10" width="180" style="color:#444444" align="left"><i>DL #</i></td>
		<td class="text_10" width="180" style="color:#444444" align="left"><i>Marital</i></td>
		<td class="text_10" width="180" style="color:#444444" align="left"><i>Work Phone#</i></td>
	</tr>
	<tr><td height="7"></td></tr>
	<tr align="left">
		<td class="text_10b" width="180" height="<?php  if($pt_data['res_party_detail'][0]['address'] || $pt_data['res_party_detail'][0]['address2']){echo "5px";} else{ echo "14px";} ?>" valign="<?php  if($pt_data['res_party_detail'][0]['address'] || $pt_data['res_party_detail'][0]['address2']){echo "bottom";} else{ echo "top";} ?>">&nbsp;<b><?php  if($pt_data['res_party_detail'][0]['address'] || $pt_data['res_party_detail'][0]['address2']){echo $pt_data['res_party_detail'][0]['address']." ".$pt_data['res_party_detail'][0]['address2'];} else{ echo "";} ?></b></td>
		<td class="text_10b" width="180" height="<?php echo ($city_address) ? "5px" : "14px"; ?>" valign="<?php echo ($city_address) ? "bottom" : "top"; ?>">&nbsp;<b><?php echo ($city_address) ? $city_address : "" ; ?></b></td>
		<td class="text_10b" width="180" height="<?php echo (trim(ucwords($pt_data['res_party_detail'][0]['home_ph']))) ? "5px" : "14px"; ?>" valign="<?php echo (trim(ucwords($pt_data['res_party_detail'][0]['home_ph']))) ? "bottom" : "bottom"; ?>">&nbsp;<b><?php echo (trim(ucwords($pt_data['res_party_detail'][0]['home_ph']))) ? core_phone_format(trim(ucwords($pt_data['res_party_detail'][0]['home_ph']))) : ""; ?></b></td>
		<td class="text_10b" width="180" height="<?php echo (trim(ucwords($pt_data['res_party_detail'][0]['mobile']))) ? "5px" : "14px"; ?>" valign="<?php echo (trim(ucwords($pt_data['res_party_detail'][0]['mobile']))) ? "bottom" : "bottom"; ?>">&nbsp;<b><?php echo (trim(ucwords($pt_data['res_party_detail'][0]['mobile']))) ? core_phone_format(trim(ucwords($pt_data['res_party_detail'][0]['mobile']))) : ""; ?></b></td>
	</tr>
	
	<tr align="left">
		<td class="text_10" width="180" style="color:#444444"><i>Street</i></td>
		<td class="text_10" width="180" style="color:#444444"><i>City, State Zip</i></td>
		<td class="text_10" width="180" style="color:#444444"><i>Home Phone#</i></td>
		<td class="text_10" width="180" style="color:#444444"><i>Mobile#</i></td>
	</tr>
	<tr><td height="7"></td></tr>
</table>
		<?php
//}
//--- get Occupation Details if patient fill in demographics --------
//for($f=0;$f<count($emp_details);$f++){
	if($emp_details[0]['city'])
		$emp_adderss .= $emp_details[0]['city'];
	if($emp_details[0]['state'])
		$emp_adderss .= ', '.$emp_details[0]['state'];
	if($emp_details[0]['postal_code'])
		$emp_adderss .= ' '.$emp_details[0]['postal_code'];
?>
<table width="100%" cellpadding="0" cellspacing="0" border="<?php echo $border; ?>" rules="none">
	<tr valign="middle" height="25px" class="text_10" bgcolor="#c0c0c0">
		<td align="left" colspan="4"><b><?php echo strtoupper('Patient Occupation, Race & Language'); ?></b></td>
	</tr>
	<tr><td height="7"></td></tr>
	<?php
	//--- Check That All Fields Are Not Blank -------
	//if(trim($pt_data['occupation']) != '' || trim($emp_details[0]['name']) != ''){
	?>
	<tr align="left">
		<td class="text_10b" width="180" height="<?php echo (trim(ucwords($pt_data['occupation']))) ? "5px" : "14px"; ?>" valign="<?php echo (trim(ucwords($pt_data['occupation']))) ? "bottom" : "top"; ?>">&nbsp;<b><?php echo (trim(ucwords($pt_data['occupation']))) ? trim(ucwords($pt_data['occupation'])) : ""; ?></b></td>
		<td class="text_10b" width="180" height="<?php echo (trim(ucwords($emp_details[0]['name']))) ? "5px" : "14px"; ?>" valign="<?php echo (trim(ucwords($emp_details[0]['name']))) ? "bottom" : "top"; ?>">&nbsp;<b><?php echo (trim(ucwords($emp_details[0]['name']))) ? trim(ucwords($emp_details[0]['name'])) : ""; ?></b></td>
		<td class="text_10b" width="180" height="<?php if(trim(ucwords($emp_details[0]['street'])) || trim(ucwords($emp_details[0]['street2']))){echo "5px";}else {echo "14px";} ?>" valign="<?php if(trim(ucwords($emp_details[0]['street'])) || trim(ucwords($emp_details[0]['street2']))){echo "bottom";}else {echo "top";} ?>">&nbsp;<b><?php if(trim(ucwords($emp_details[0]['street'])) || trim(ucwords($emp_details[0]['street2']))){echo trim(ucwords($emp_details[0]['street']))." ".trim(ucwords($emp_details[0]['street2']));}else {echo "";} ?></b></td>		
		<td class="text_10b" width="180" height="<?php echo (trim(ucwords($emp_adderss))) ? "5px" : "14px"; ?>" valign="<?php echo (trim(ucwords($emp_adderss))) ? "bottom" : "top"; ?>">&nbsp;<b><?php echo (trim(ucwords($emp_adderss))) ? trim(ucwords($emp_adderss)) : ""; ?></b></td>
	</tr>
	<tr align="left">
		<td class="text_10" style="color:#444444" width="180"><i>Occupation</i></td>
		<td class="text_10"  style="color:#444444" width="180"><i>Employer Name</i></td>
		<td class="text_10" style="color:#444444" width="180"><i>Employer Address</i></td>		
		<td class="text_10" style="color:#444444" width="180"><i>City, State Zip</i></td>
	</tr>
	<tr><td height="7" colspan="4"></td></tr>
	<?php
	//}
	//--- Check That All Fields Are Not Blank -------
	//if(trim($emp_details[0]['street']) != '' || trim($emp_details[0]['street2']) != '' || trim($emp_adderss) != ''){
	?>
	<tr align="left">		
		<td class="text_10b" width="180" height="<?php echo (trim(ucwords($pt_data['hipaa_mail']))) ? "5px" : "14px"; ?>" valign="<?php echo (trim(ucwords($pt_data['hipaa_mail']))) ? "bottom" : "top"; ?>">&nbsp;<b><?php if(trim($pt_data['hipaa_mail'])==1){ echo("Yes");}else if(trim($pt_data['hipaa_mail'])==0) { echo("No");}else{echo(underLine(35));} ?></b></td>
		<td class="text_10b" width="180" height="<?php echo (trim(ucwords($pt_data['hipaa_voice']))) ? "5px" : "14px"; ?>" valign="<?php echo (trim(ucwords($pt_data['hipaa_voice']))) ? "bottom" : "top"; ?>">&nbsp;<b><?php if(trim($pt_data['hipaa_voice'])==1){ echo("Yes");}else if(trim($pt_data['hipaa_voice'])==0) { echo("No");}else{echo(underLine(35));} ?></b></td>
		<td class="text_10b" width="180" height="<?php echo (trim(ucwords($pt_data['genericval1']))) ? "5px" : "14px"; ?>" valign="<?php echo (trim(ucwords($pt_data['genericval1']))) ? "bottom" : "top"; ?>">&nbsp;<b><?php echo (trim(ucwords($pt_data['genericval1']))) ? trim(ucwords($pt_data['genericval1'])) : ""; ?></b></td>
		<td class="text_10b" width="180" height="<?php echo (trim(ucwords($pt_data['genericval2']))) ? "5px" : "14px"; ?>" valign="<?php echo (trim(ucwords($pt_data['genericval2']))) ? "bottom" : "top"; ?>">&nbsp;<b><?php echo (trim(ucwords($pt_data['genericval2']))) ? trim(ucwords($pt_data['genericval2'])) : ""; ?></b></td>
	</tr>
	<tr align="left">		
		<td class="text_10" style="color:#444444" width="180"><i>Allow Mail</i></td>
		<td class="text_10" style="color:#444444" width="180"><i>Allow Voice Msg</i></td>
		<td class="text_10" style="color:#444444" width="180"><i>User defined 1</i></td>
		<td class="text_10" style="color:#444444" width="180"><i>User defined 2</i></td>
	</tr>
	<?php
	//} 
	?>	
	<tr><td height="7" colspan="4"></td></tr>
	<?php
	//if($pt_data['genericval2'] != '' || $pt_data['language'] != '' || $pt_data['ethnoracial'] != '' || $pt_data['interpretter'] != ''){
	?>
	<tr align="left">		
		<td class="text_10b" width="180" height="<?php echo (trim(ucwords($pt_data['language']))) ? "5px" : "14px"; ?>" valign="<?php echo (trim(ucwords($pt_data['language']))) ? "bottom" : "top"; ?>">&nbsp;<b><?php echo (trim(ucwords($pt_data['language']))) ? trim(ucwords($pt_data['language'])) : ""; ?></b></td>
		<td class="text_10b" width="180" height="<?php echo (trim(ucwords($pt_data['otherEthnicity'])) || $pt_data['race']!="" || $pt_data['ethnicity']!="") ? "5px" : "14px"; ?>" valign="<?php echo (trim(ucwords($pt_data['otherEthnicity'])) || $pt_data['race']!=""  || $pt_data['ethnicity']!="") ? "bottom" : "top"; ?>">&nbsp;<b><?php echo (trim(ucwords($pt_data['otherEthnicity'])) || $pt_data['race']!="" || $pt_data['ethnicity']!="") ? trim(ucwords($pt_data['ethnoracial'])).$pt_data['race'].$pt_data['otherRace'].'/'.$pt_data['ethnicity'].$pt_data['otherEthnicity']: ""; ?></b></td>
		<td class="text_10b" width="180" height="<?php echo (trim(ucwords($pt_data['interpretter']))) ? "5px" : "14px"; ?>" valign="<?php echo (trim(ucwords($pt_data['interpretter']))) ? "bottom" : "top"; ?>">&nbsp;<b><?php echo (trim(ucwords($pt_data['interpretter']))) ? trim(ucwords($pt_data['interpretter'])) : ""; ?></b></td>

	</tr>
	<tr align="left">		
		<td class="text_10" style="color:#444444" width="180"><i>Language</i></td>
		<td class="text_10" style="color:#444444" width="180"><i>Race / Ethnicity</i></td>
		<td class="text_10" style="color:#444444" width="180"><i>Interpretter</i></td>
	</tr>
	<tr><td height="7"></td></tr>
	<?php	
	//}
	?>
</table>