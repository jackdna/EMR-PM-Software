	<?php
ini_set("memory_limit","1024M");

$start_date = getDateFormatDB($from_date);
$end_date = getDateFormatDB($to_date);
$qry_patient_data="Select id from patient_data where patientStatus='Active'";
$res_patient_data=imw_query($qry_patient_data);
$totalPatient = imw_num_rows($res_patient_data);

//--- GET ALL PROVIDER NAME ----
$providerRs = imw_query("Select id,fname,mname,lname from users");
$userNameTwoCharArr = array();
while($providerResArr = imw_fetch_assoc($providerRs)){
	$id = $providerResArr['id'];

	// two character array
	$operatorInitial = substr($providerResArr['fname'],0,1);
	$operatorInitial .= substr($providerResArr['lname'],0,1);
	$userNameTwoCharArr[$id] = strtoupper($operatorInitial);
}

$qry_select_mand="SELECT heardAboutUs as heard_abt_us, name as fname, name as lname, address as street, address_1 as street2, city as city, state as state, zip as postal_code, eMail as email, provider as providerID, homePhone as phone_home, workPhone as phone_biz, mobilePhone as phone_cell, facility as default_facility, drivingLicense as driving_licence , ptPortalAccess as temp_key, loginId as username, password as password, ptDOB as DOB, ptMaritalStatus as status, ptEmergencyContactName as contact_relationship, ptEmergencyPhone as phone_contact, ptCreatedBy as created_by, ptReferringPhysician as referrerID, ptSex as sex, ptsocialSecurityNumber as ss FROM demographics_mandatory";
$res_select_mand=imw_query($qry_select_mand);
$row_select_mand=imw_fetch_assoc($res_select_mand);
$temp_mand_array = array();
$where_strMandfields ="";
foreach($row_select_mand as $mandKey => $mandValue){
	if($mandValue == $field_type){
		$temp_mand_array[] = $mandKey;
		if($where_strMandfields==""){
			$where_strMandfields.=" $mandKey='' ";
		}else{
			$where_strMandfields.=" or $mandKey='' ";
		}
		if($mandKey == 'heard_abt_us' || $mandKey == 'providerID' || $mandKey == 'created_by'){
			$where_strMandfields.=" or $mandKey=0";
		} elseif($mandKey == 'DOB'){
			$where_strMandfields.=" or $mandKey=0000-00-00";
		}
	}
}
$strMandfields=implode(', ',$temp_mand_array);
		
$qry_select_pt_fields="SELECT id, date_format(date,'".get_sql_date_format()."') as registration_date, lname, fname, created_by, $strMandfields 
from patient_data where patientStatus='Active' AND (DATE_FORMAT(date, '%Y-%m-%d') BETWEEN '".$start_date."' AND '".$end_date."') and ($where_strMandfields)";	
$res_select_pt_fields=imw_query($qry_select_pt_fields);
$fetch_Patient = imw_num_rows($res_select_pt_fields);
$row_select_pt_fields=array();
while($row=imw_fetch_assoc($res_select_pt_fields)) {
	$row_select_pt_fields[]=$row;
}
$Data = "";
$printFile = 0;
foreach($row_select_pt_fields as $fieldKey => $fieldvalues){


	$Data .='<tr>';
	$Data .='<td>'.$fieldvalues['id'].'</td>';
	$Data .='<td>'.$fieldvalues['lname'].','.$fieldvalues['fname'].'</td>';
	$Data .='<td>'.$fieldvalues['registration_date'].'</td>';
	
	$temp='';
	foreach($fieldvalues as $finalKey => $values){
		if($values == "" || $values == '0' || $values == '0000-00-00'){
			switch($finalKey){
				case 'heard_abt_us':
				$finalLbl='Heard about us';
				break;
				case 'fname':
				$finalLbl='First Name';
				break;
				case 'lname':
				$finalLbl='Last Name';
				break;
				case 'street':
				$finalLbl='Street1';
				break;
				case 'street2':
				$finalLbl='Street2';
				break;
				case 'city':
				$finalLbl='City';
				break;
				case 'state':
				$finalLbl='State';
				break;
				case 'postal_code':
				$finalLbl='Zip Code';
				break;
				case 'email':
				$finalLbl='Email';
				break;
				case 'providerID':
				$finalLbl='Primary Eye Care';
				break;
				case 'phone_home':
				$finalLbl='Home Phone #';
				break;
				case 'phone_biz':
				$finalLbl='Work Phone #';
				break;
				case 'phone_cell':
				$finalLbl='Mobile Phone #';
				break;
				case 'default_facility':
				$finalLbl='Home Facility';
				break;
				case 'driving_licence':
				$finalLbl='Driving License';
				break;
				case 'temp_key':
				$finalLbl='Activation Key';
				break;
				case 'username':
				$finalLbl='Portal User Name';
				break;
				case 'password':
				$finalLbl='Portal Password';
				break;
				case 'DOB':
				$finalLbl='DOB';
				break;
				case 'status':
				$finalLbl='Marital Status';
				break;
				case 'contact_relationship':
				$finalLbl='Emergency Name';
				break;
				case 'phone_contact':
				$finalLbl='Emergency Tel #';
				break;
				case 'created_by':
				$finalLbl='Created by';
				break;
				case 'referrerID':
				$finalLbl='Referring Phy.';
				break;
				case 'sex':
				$finalLbl='Sex';
				break;
				case 'ss':
				$finalLbl='Social Security';
				break;
			}
			$temp .=$finalLbl.', ';
		}
	}
	$tempFields = substr($temp, 0,-2);
	$Data .='<td>'.$tempFields.'</td>';
	$Data .='<td>'.$userNameTwoCharArr[$fieldvalues['created_by']].'</td>';
	$Data .='</tr>';
	$printFile = 1;
}

$HTML_SHOW.="<table class=\"rpt rpt_table rpt_table-bordered rpt_padding\" width=\"1050\">
		<tr class=\"rpt_headers\">
			<td class=\"rptbx1\" width=\"350\">
				Patient Registry Fields
			</td>
			<td class=\"rptbx2\" width=\"350\">
				Selected Date Range: $from_date to $to_date
			</td>
			<td class=\"rptbx3\" width=\"350\">
				Created By $createdBy on $curDate
			</td>
		</tr>
</table>";


$HTML_SHOW.="<table class=\"rpt rpt_table rpt_table-bordered\" width=\"1050\" bgcolor=\"#FFF3E8\">
	<tr>
		<td class=\"text_b_w\" style=\"width:10%;\">Patient ID</td>
		<td class=\"text_b_w\" style=\"width:15%;\">Patient Name</td>
		<td class=\"text_b_w\" style=\"width:15%;\">Registration Date </td>
		<td class=\"text_b_w\" style=\"width:45%;\">Fields</td>
		<td class=\"text_b_w\" style=\"width:15%;\">Registered By</td>
	</tr>";
$HTML_SHOW.=$Data;
$HTML_SHOW.="<tr><td class=\"text_b_w\" style=\"width:100%;\" colspan=\"5\">Total Patient: $fetch_Patient</td></tr>";
	
$HTML_SHOW.="</table>";	

if($printFile){
echo $HTML_SHOW;
} else{
	echo '<div class="text-center alert alert-info">No record exists.</div>';
}
?>