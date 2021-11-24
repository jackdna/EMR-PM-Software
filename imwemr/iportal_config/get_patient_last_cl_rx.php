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
?><?php
/*
File: highlight_provider_schedules_iportal.php
Purpose: Get Provider's Month Schedule (Avaiable Days Only) for iPortal Appointment Booking
Access Type: Direct
*/
$ignoreAuth=true;
include_once("../config/globals.php");
if($_REQUEST['IPORTAL_REQUEST']!=(md5(constant("IPORTAL_SERVER")))){
	die("[Error]:401 Unauthorized Access ");
}
$arrCL=array();
$patOrders=array();

if($_REQUEST['mode']=='onlyrx' || $_REQUEST['mode']=='alldata'){
	//GETTING PATIENT INFO AND POS FACILITY ADDRESS
	$qry = "SELECT pd.id,pd.fname,pd.mname,pd.lname,pd.street,pd.city,pd.state,pd.postal_code,pd.zip_ext,
	pos_fac.pos_facility_address, pos_fac.pos_facility_city, pos_fac.pos_facility_state, pos_fac.pos_facility_zip,
	pos_fac.zip_ext as 'pos_zip_ext'  
	FROM patient_data pd 
	LEFT JOIN pos_facilityies_tbl pos_fac ON pos_fac.pos_facility_id= pd.default_facility 
	WHERE pd.id = '".$_REQUEST['patient_id']."'";
	$rs = imw_query($qry);
	$res = imw_fetch_assoc($rs);
	$patient_address = $res['fname'].' '.$res['lname'].' - '.$res['id'].',\n';
	$patient_address.=trim(stripslashes($res['street'])).'\n';
	$patient_address.=trim(stripslashes($res['city'].", ".$res['state']." ".$res['postal_code']));
	$patient_address.=(empty($res['zip_ext'])==false) ? '-'.$res['zip_ext'] : '';	
	$arrCL['masterdata']['patient_address']=$patient_address;
	
	if($res['default_facility']>0){
		$facility_address.=trim(stripslashes($res['pos_facility_address'])).'\n';
		$facility_address.=trim(stripslashes($res['pos_facility_city'].", ".$res['pos_facility_state']." ".$res['pos_facility_zip']));
		$facility_address.=(empty($res['pos_zip_ext'])==false) ? '-'.$res['pos_zip_ext'] : '';	
		$arrCL['masterdata']['facility_address']=$facility_address;
	}else{
		//GETTING LAST APPOINTMENT FACILITY ADDRESS
		$qry="Select sa_facility_id FROM schedule_appointments WHERE sa_patient_id='".$_REQUEST['patient_id']."' ORDER BY id DESC limit 0,1";
		$rs = imw_query($qry);
		$res = imw_fetch_assoc($rs);
		$facility_id=$res['sa_facility_id'];
		unset($rs);
	
		if($facility_id>0){
			$qry="Select pos_fac.pos_facility_address, pos_fac.pos_facility_city, pos_fac.pos_facility_state, pos_fac.pos_facility_zip,
			pos_fac.zip_ext as 'pos_zip_ext' FROM facility 
			JOIN pos_facilityies_tbl pos_fac ON pos_fac.pos_facility_id = facility.fac_prac_code 
			WHERE facility.id='".$facility_id."'";
			$rs = imw_query($qry);
			$res = imw_fetch_assoc($rs);
			$facility_address.=trim(stripslashes($res['pos_facility_address'])).'\n';
			$facility_address.=trim(stripslashes($res['pos_facility_city'].", ".$res['pos_facility_state']." ".$res['pos_facility_zip']));
			$facility_address.=(empty($res['pos_zip_ext'])==false) ? '-'.$res['pos_zip_ext'] : '';
			$arrCL['masterdata']['facility_address']=$facility_address;
		}
	}
	unset($rs);
	
	//GETTING LAST CL SHEET
	$qry="Select clws_id FROM contactlensmaster WHERE patient_id='".$_REQUEST['patient_id']."' ORDER BY clws_id DESC LIMIT 0,1";
	$rs=imw_query($qry);
	$res=imw_fetch_assoc($rs);
	$clws_id=$res['clws_id'];
	unset($rs);
	
	if($clws_id>0){
		//ALL MAKE ARRAY
		$arrAllMake=array();
		$qry="Select make_id, manufacturer, style, type FROM contactlensemake";
		$rs=imw_query($qry);
		while($res=imw_fetch_assoc($rs)){
			$id=$res['make_id'];
			$arrAllMake[$id]['brand']=$res['style'];
			$arrAllMake[$id]['manufacturer']=$res['manufacturer'];
			$arrAllMake[$id]['type']=$res['type'];
		}
		unset($rs);
	
		//LAST PRESCRIPTION
		$qry="Select clm.clws_id, cldet.clType, cldet.clEye, 
		cldet.SclsphereOD, cldet.SclCylinderOD, cldet.SclaxisOD, cldet.SclBcurveOD, cldet.SclDiameterOD, cldet.SclTypeOD_ID, 
		cldet.SclsphereOS, cldet.SclCylinderOS, cldet.SclaxisOS, cldet.SclBcurveOS, cldet.SclDiameterOS, cldet.SclTypeOS_ID,
		cldet.RgpPowerOD, cldet.RgpBCOD, cldet.RgpDiameterOD, cldet.RgpTypeOD_ID, 
		cldet.RgpPowerOS, cldet.RgpBCOS, cldet.RgpDiameterOS, cldet.RgpTypeOS_ID,
		cldet.RgpCustomPowerOD, cldet.RgpCustomBCOD, cldet.RgpCustomDiameterOD, cldet.RgpCustomTypeOD_ID, 
		cldet.RgpCustomPowerOS, cldet.RgpCustomBCOS, cldet.RgpCustomDiameterOS, cldet.RgpCustomTypeOS_ID 
		FROM contactlensmaster clm 
		JOIN contactlensworksheet_det cldet ON cldet.clws_id=clm.clws_id 
		WHERE clm.clws_id='".$clws_id."' AND clm.del_status='0' ORDER BY cldet.clEye, cldet.id DESC";
		$rs=imw_query($qry) or die(imw_error());
		
		//TYPE IDS
		$i=$j=0;
		while($res=imw_fetch_assoc($rs)){	
			$arrCL['masterdata']['clws_id']=$res['clws_id'];
			if($res['clEye']=='OD'){
				if($res['clType']=='scl'){
					$od_type_id=$res['SclTypeOD_ID'];
					$arrCL['OD'][$i]['sphere']=$res['SclsphereOD'];
					$arrCL['OD'][$i]['cylinder']=$res['SclCylinderOD'];
					$arrCL['OD'][$i]['bc']=$res['SclBcurveOD'];
					$arrCL['OD'][$i]['diameter']=$res['SclDiameterOD'];
					$arrCL['OD'][$i]['axis']=$res['SclaxisOD'];
					$arrCL['OD'][$i]['brand']=$arrAllMake[$od_type_id]['brand'];
					$arrCL['OD'][$i]['manufacturer']=$arrAllMake[$od_type_id]['manufacturer'];
					$arrCL['OD'][$i]['type']=$arrAllMake[$od_type_id]['type'];
	
				}else if($res['clType']=='rgp'){
					$od_type_id=$res['RgpTypeOD_ID'];
					$arrCL['OD'][$i]['sphere']=$res['RgpPowerOD'];
					$arrCL['OD'][$i]['cylinder']='';
					$arrCL['OD'][$i]['bc']=$res['RgpBCOD'];
					$arrCL['OD'][$i]['diameter']=$res['RgpDiameterOD'];
					$arrCL['OD'][$i]['axis']='';
					$arrCL['OD'][$i]['brand']=$arrAllMake[$od_type_id]['brand'];
					$arrCL['OD'][$i]['manufacturer']=$arrAllMake[$od_type_id]['manufacturer'];
					$arrCL['OD'][$i]['type']=$arrAllMake[$od_type_id]['type'];
					
				}else if($res['clType']=='cust_rgp'){
					$od_type_id=$res['RgpCustomTypeOD_ID'];
					$arrCL['OD'][$i]['sphere']=$res['RgpCustomPowerOD'];
					$arrCL['OD'][$i]['cylinder']='';
					$arrCL['OD'][$i]['bc']=$res['RgpCustomBCOD'];
					$arrCL['OD'][$i]['diameter']=$res['RgpCustomDiameterOD'];
					$arrCL['OD'][$i]['axis']='';
					$arrCL['OD'][$i]['brand']=$arrAllMake[$od_type_id]['brand'];
					$arrCL['OD'][$i]['manufacturer']=$arrAllMake[$od_type_id]['manufacturer'];
					$arrCL['OD'][$i]['type']=$arrAllMake[$od_type_id]['type'];
				}
				$i++;
			}
			if($res['clEye']=='OS'){		
				if($res['clType']=='scl'){
					$os_type_id=$res['SclTypeOS_ID'];
					$arrCL['OS'][$j]['sphere']=$res['SclsphereOS'];
					$arrCL['OS'][$j]['cylinder']=$res['SclCylinderOS'];
					$arrCL['OS'][$j]['bc']=$res['SclBcurveOS'];
					$arrCL['OS'][$j]['diameter']=$res['SclDiameterOS'];
					$arrCL['OS'][$j]['axis']=$res['SclaxisOS'];
					$arrCL['OS'][$j]['brand']=$arrAllMake[$os_type_id]['brand'];
					$arrCL['OS'][$j]['manufacturer']=$arrAllMake[$os_type_id]['manufacturer'];
					$arrCL['OS'][$j]['type']=$arrAllMake[$os_type_id]['type'];
	
				}else if($res['clType']=='rgp'){
					$os_type_id=$res['RgpTypeOS_ID'];
					$arrCL['OS'][$j]['sphere']=$res['RgpPowerOS'];
					$arrCL['OS'][$j]['cylinder']='';
					$arrCL['OS'][$j]['bc']=$res['RgpBCOS'];
					$arrCL['OS'][$j]['diameter']=$res['RgpDiameterOS'];
					$arrCL['OS'][$j]['axis']='';
					$arrCL['OS'][$j]['brand']=$arrAllMake[$os_type_id]['brand'];
					$arrCL['OS'][$j]['manufacturer']=$arrAllMake[$os_type_id]['manufacturer'];
					$arrCL['OS'][$j]['type']=$arrAllMake[$os_type_id]['type'];
					
				}else if($res['clType']=='cust_rgp'){
					$os_type_id=$res['RgpCustomTypeOS_ID'];
					$arrCL['OS'][$j]['sphere']=$res['RgpCustomPowerOS'];
					$arrCL['OS'][$j]['cylinder']='';
					$arrCL['OS'][$j]['bc']=$res['RgpCustomBCOS'];
					$arrCL['OS'][$j]['diameter']=$res['RgpCustomDiameterOS'];
					$arrCL['OS'][$j]['axis']='';
					$arrCL['OS'][$j]['brand']=$arrAllMake[$os_type_id]['brand'];
					$arrCL['OS'][$j]['manufacturer']=$arrAllMake[$os_type_id]['manufacturer'];
					$arrCL['OS'][$j]['type']=$arrAllMake[$os_type_id]['type'];
				}
				$j++;
			}
		}unset($rs);
		
	}
	
	//GET DISPOSABLE
	$arrAllDisposable=array();
	$qry="Select id, cat_name FROM in_contact_cat WHERE del_status='0' order by cat_name asc";
	$rs=imw_query($qry);
	while($res=imw_fetch_assoc($rs)){
		$arrAllDisposable[$res['id']]=$res['cat_name'];
	}unset($rs);

	//GET SUPPLY
	$arrAllSupplies=array();
	$qry="Select id, supply_name FROM in_supply WHERE del_status='0' order by supply_name asc";
	$rs=imw_query($qry);
	while($res=imw_fetch_assoc($rs)){
		$arrAllSupplies[$res['id']]=$res['supply_name'];
	}unset($rs);

	//GET PACKAGES
	$arrAllPackages=array();
	$qry="Select in_options.id, in_options.opt_val, in_options.opt_sub_type, in_contact_cat.cat_name FROM in_options 
	JOIN in_contact_cat ON in_contact_cat.id= in_options.opt_sub_type 
	WHERE in_options.opt_type='5' and in_options.module_id='3' and in_options.del_status='0' 
	order by CAST(in_options.opt_val AS UNSIGNED) asc";
	$rs=imw_query($qry);
	while($res=imw_fetch_assoc($rs)){
		if(strtolower($res['cat_name'])=='daily'){
			$arrAllPackages['daily'][$res['id']]=$res['opt_val'];
		}else if(strtolower($res['cat_name'])=='weekly'){
			$arrAllPackages['weekly'][$res['id']]=$res['opt_val'];
		}else if(strtolower($res['cat_name'])=='monthly'){
			$arrAllPackages['monthly'][$res['id']]=$res['opt_val'];
		}
	}unset($rs);
}

if($_REQUEST['mode']=='onlyorders' || $_REQUEST['mode']=='alldata'){
	//GETTTING ORDERS
	$qry="Select *, DATE_FORMAT(ordered_date, '%m-%d-%Y') as 'orderedDate' FROM iportal_req_orders WHERE patient_id='".$_REQUEST['patient_id']."' AND order_for='cl' ORDER BY id DESC";
	$rs=imw_query($qry)or die(imw_error());
	while($res=imw_fetch_assoc($rs)){
		$id=$res['id'];
		$ordNum=$res['temp_order_num'];
		$eye=$res['eye'];
		
		$patOrders[$ordNum][$eye][$id]['patient_id']=$res['patient_id'];
		$patOrders[$ordNum][$eye][$id]['clws_id']=$res['clws_id'];
		$patOrders[$ordNum][$eye][$id]['eye']=$res['eye'];
		$patOrders[$ordNum][$eye][$id]['brand']=$res['brand'];
		$patOrders[$ordNum][$eye][$id]['manufacturer']=$res['manufacturer'];
		$patOrders[$ordNum][$eye][$id]['disposable']=$res['disposable'];
		$patOrders[$ordNum][$eye][$id]['package']=$res['package'];
		$patOrders[$ordNum][$eye][$id]['supplies']=$res['supplies'];
		$patOrders[$ordNum][$eye][$id]['boxes']=$res['boxes'];
		$patOrders[$ordNum][$eye][$id]['ordered_data']=$res['ordered_data'];
		$patOrders[$ordNum][$eye][$id]['ship_to']=$res['ship_to'];
		$patOrders[$ordNum][$eye][$id]['shipping_address']=$res['shipping_address'];
		$patOrders[$ordNum][$eye][$id]['comments']=$res['comments'];
		$patOrders[$ordNum][$eye][$id]['ordered_date']=$res['orderedDate'];
		$patOrders[$ordNum][$eye][$id]['is_approved']=$res['is_approved'];
	}
	unset($rs);
}

print json_encode(array('arrCL'=>$arrCL, 'patOrders'=>$patOrders, 'arrAllDisposable'=>$arrAllDisposable, 'arrAllSupplies'=>$arrAllSupplies, 'arrAllPackages'=>$arrAllPackages));
?>