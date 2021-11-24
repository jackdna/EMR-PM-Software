<?php
/*
File: cl_rx_list.php
Coded in PHP7
Purpose: Contact Lens RX Information
Access Type: Direct access
*/
$secLastFinalId='';
$qryPart='';
$arrRxDetails=array();
$rs=imw_query("Select clws_id FROM contactlensmaster WHERE patient_id='".$_SESSION['patient_session_id']."' 
AND LOWER(clws_type)='final' and del_status=0 ORDER BY clws_id DESC LIMIT 0,2");
while($res=imw_fetch_array($rs)){
	$arrWSFinal[]=$res['clws_id'];
}
if(sizeof($arrWSFinal)>1){
	$secLastFinalId=$arrWSFinal[1];
	$qryPart=" AND clMaster.clws_id>".$secLastFinalId;
}

$qry="Select clDet.SclTypeOD_ID, clDet.SclTypeOS_ID, clDet.RgpTypeOD_ID, clDet.RgpTypeOS_ID, clDet.RgpCustomTypeOD_ID, clDet.RgpCustomTypeOS_ID, clMaster.clws_id, clMaster.clws_type, clMaster.clws_trial_number, clMaster.dos as 'DOS', clMaster.form_id, clMaster.cl_comment, clDet.id, clDet.clEye, clDet.clType, clDet.SclsphereOD, clDet.SclCylinderOD, clDet.SclaxisOD, clDet.SclBcurveOD, clDet.SclDiameterOD, clDet.SclAddOD,
clDet.SclsphereOS, clDet.SclCylinderOS, clDet.SclaxisOS, clDet.SclBcurveOS, clDet.SclDiameterOS, clDet.SclAddOS,
clDet.RgpPowerOD, clDet.RgpBCOD ,clDet.RgpDiameterOD, clDet.RgpOZOD, clDet.RgpCTOD, clDet.RgpLatitudeOD, clDet.RgpAddOD, clDet.RgpColorOD,  
clDet.RgpPowerOS, clDet.RgpBCOS ,clDet.RgpDiameterOS, clDet.RgpOZOS, clDet.RgpCTOS, clDet.RgpLatitudeOS, clDet.RgpAddOS, clDet.RgpColorOS,
clDet.RgpCustomBCOD, clDet.RgpCustomPowerOD, clDet.RgpCustomOZOD, clDet.RgpCustomLatitudeOD, clDet.RgpCustomAddOD, clDet.RgpCustomDiameterOD, clDet.RgpCustomColorOD, 
clDet.RgpCustomBCOS, clDet.RgpCustomPowerOS, clDet.RgpCustomOZOS, clDet.RgpCustomLatitudeOS, clDet.RgpCustomAddOS, clDet.RgpCustomDiameterOS, clDet.RgpCustomColorOS 
FROM contactlensmaster clMaster LEFT JOIN contactlensworksheet_det clDet 
ON clDet.clws_id = clMaster.clws_id 
WHERE clMaster.patient_id='".$_SESSION['patient_session_id']."' ".$qryPart." 
AND clMaster.del_status=0
ORDER BY clMaster.clws_id DESC, clDet.clType DESC, clDet.clEye ASC";  
$rs=imw_query($qry);
if(imw_num_rows($rs)>0){
 while($res=imw_fetch_array($rs)){
	$phyName='';
	$dos= '';
	//$cnt++;
	$dt=explode('-', $res['DOS']);
	$dos = $dt[1].'-'.$dt[2].'-'.$dt[0];
	$dos1 = $dt[1].'-'.$dt[2].'-'.substr($dt[0],-2);
	$dos3 =str_replace("-", "", $res['DOS']);
	
	 //get physician name
	 $phyQ=imw_query("SELECT CONCAT(u.lname,', ',u.fname) as phy_name, u.id FROM chart_master_table as cms INNER JOIN users as u ON u.id=cms.providerId WHERE cms.id ='".$res['form_id']."' and cms.patient_id ='".$_SESSION['patient_session_id']."'");
	 $phyRes=imw_fetch_array($phyQ);
	
		$clType=($res['clType']=='cust_rgp')? 'Custom RGP' : strtoupper($res['clType']);
		$arrWS[$res['clws_id']]=$res['clws_id'];
		$arrWS[$res['clws_id']]['DOS']=$res['DOS'];
		if($phyRes['id']>0){
			$phyName=$phyRes['phy_name'];
		}
	
		if($res['clType']=='scl'){
			//clubing od and os value in one array as it is saved in different lines from idoc
			if($clwidArr[$res['clws_id']][$clType])$cnt=$clwidArr[$res['clws_id']][$clType];
			else $cnt++;
			$clwidArr[$res['clws_id']][$clType]=$cnt;
			
			if($res['clEye']=='OD'){
				$makeId = $res['SclTypeOD_ID'];
				$makeDetails = "";
				if($makeId!=""){
					$sqlMake=imw_query("SELECT `manufacturer`, `style`, `type` FROM `contactlensemake` WHERE `make_id`='".$makeId."'");
					if($sqlMake && imw_num_rows($sqlMake)>0){
						$sqlMake = imw_fetch_assoc($sqlMake);
						$sqlMake = array_filter($sqlMake, "remove_blank");
						$makeDetails = implode(" - ", $sqlMake);
					}
				}
				$arrRxDetails[$dos3][$cnt]['clType']=$clType;
				$arrRxDetails[$dos3][$cnt]['clws_id']=$res['clws_id'];
				$arrRxDetails[$dos3][$cnt]['make_od'] = $makeDetails;
				$arrRxDetails[$dos3][$cnt]['DOS'] 	= $dos;
				$arrRxDetails[$dos3][$cnt]['cl_comment'] 	= $res['cl_comment'];
				
				$arrRxDetails[$dos3][$cnt]['Provider'] 	= $phyRes['provider_id'];
				$arrRxDetails[$dos3][$cnt]['ProviderName'] 	= $phyName;
				$arrRxDetails[$dos3][$cnt]['clws_type'] 	= $res['clws_type'];
				$arrRxDetails[$dos3][$cnt]['clws_trial_number'] 	= $res['clws_trial_number'];
				
				$arrRxDetails[$dos3][$cnt]['OD'] 	= 'OD';
				$arrRxDetails[$dos3][$cnt]['orderHxS'] 	= $res['SclsphereOD'];
				$arrRxDetails[$dos3][$cnt]['orderHxC']	= $res['SclCylinderOD'];
				$arrRxDetails[$dos3][$cnt]['orderHxA'] 	= $res['SclaxisOD']."&deg;";
				$arrRxDetails[$dos3][$cnt]['orderHxDia'] = $res['SclDiameterOD'];
				$arrRxDetails[$dos3][$cnt]['orderHxBc'] 	= $res['SclBcurveOD'];
				$arrRxDetails[$dos3][$cnt]['orderHxAdd'] = $res['SclAddOD'];
			}else{
				$makeId = $res['SclTypeOS_ID'];
				$makeDetails = "";
				if($makeId!=""){
					$sqlMake=imw_query("SELECT `manufacturer`, `style`, `type` FROM `contactlensemake` WHERE `make_id`='".$makeId."'");
					if($sqlMake && imw_num_rows($sqlMake)>0){
						$sqlMake = imw_fetch_assoc($sqlMake);
						$sqlMake = array_filter($sqlMake, "remove_blank");
						$makeDetails = implode(" - ", $sqlMake);
					}
				}
				$arrRxDetails[$dos3][$cnt]['clType']=$clType;
				$arrRxDetails[$dos3][$cnt]['clws_id']=$res['clws_id'];
				$arrRxDetails[$dos3][$cnt]['make_os'] = $makeDetails;
				$arrRxDetails[$dos3][$cnt]['DOS'] 	= $dos;
				$arrRxDetails[$dos3][$cnt]['cl_comment'] 	= $res['cl_comment'];
				
				$arrRxDetails[$dos3][$cnt]['Provider'] 	= $phyRes['provider_id'];
				$arrRxDetails[$dos3][$cnt]['clws_type'] 	= $res['clws_type'];
				$arrRxDetails[$dos3][$cnt]['clws_trial_number'] 	= $res['clws_trial_number'];
				
				$arrRxDetails[$dos3][$cnt]['ProviderName'] 	= $phyName;
				$arrRxDetails[$dos3][$cnt]['OS'] 	= 'OS';
				$arrRxDetails[$dos3][$cnt]['orderHxS_OS'] 	= $res['SclsphereOS'];
				$arrRxDetails[$dos3][$cnt]['orderHxC_OS']	= $res['SclCylinderOS'];
				$arrRxDetails[$dos3][$cnt]['orderHxA_OS'] 	= $res['SclaxisOS']."&deg;";
				$arrRxDetails[$dos3][$cnt]['orderHxDia_OS'] = $res['SclDiameterOS'];
				$arrRxDetails[$dos3][$cnt]['orderHxBc_OS'] 	= $res['SclBcurveOS'];
				$arrRxDetails[$dos3][$cnt]['orderHxAdd_OS'] = $res['SclAddOS'];
			}
		}
		if($res['clType']=='rgp'){
			//clubing od and os value in one array as it is saved in different lines from idoc
			if($clwidArr[$res['clws_id']][$clType])$cnt=$clwidArr[$res['clws_id']][$clType];
			else $cnt++;
			$clwidArr[$res['clws_id']][$clType]=$cnt;
			
			if($res['clEye']=='OD'){
				$makeId = $res['RgpTypeOD_ID'];
				$makeDetails = "";
				if($makeId!=""){
					$sqlMake=imw_query("SELECT `manufacturer`, `style`, `type` FROM `contactlensemake` WHERE `make_id`='".$makeId."'");
					if($sqlMake && imw_num_rows($sqlMake)>0){
						$sqlMake = imw_fetch_assoc($sqlMake);
						$sqlMake = array_filter($sqlMake, "remove_blank");
						$makeDetails = implode(" - ", $sqlMake);
					}
				}
				$arrRxDetails[$dos3][$cnt]['clType']=$clType;
				$arrRxDetails[$dos3][$cnt]['clws_id']=$res['clws_id'];
				$arrRxDetails[$dos3][$cnt]['make_od'] = $makeDetails;
				$arrRxDetails[$dos3][$cnt]['DOS'] 	= $dos;
				$arrRxDetails[$dos3][$cnt]['cl_comment'] 	= $res['cl_comment'];
				
				$arrRxDetails[$dos3][$cnt]['Provider'] 	= $phyRes['provider_id'];
				$arrRxDetails[$dos3][$cnt]['ProviderName'] 	= $phyName;
				$arrRxDetails[$dos3][$cnt]['clws_type'] 	= $res['clws_type'];
				$arrRxDetails[$dos3][$cnt]['clws_trial_number'] 	= $res['clws_trial_number'];
				
				$arrRxDetails[$dos3][$cnt]['OD'] 	= 'OD';
				$arrRxDetails[$dos3][$cnt]['orderHxS'] 	= $res['RgpPowerOD'];
				$arrRxDetails[$dos3][$cnt]['orderHxC']	= $res['RgpOZOD']." ".$res['RgpCTOD'];
				//$arrRxDetails[$dos3][$cnt]['orderHxA'] 	= $res['RgpColorOD'];
				$arrRxDetails[$dos3][$cnt]['orderHxDia'] = $res['RgpDiameterOD'];
				$arrRxDetails[$dos3][$cnt]['orderHxBc'] 	= $res['RgpBCOD'];
				$arrRxDetails[$dos3][$cnt]['orderHxAdd'] = $res['RgpAddOD'];
			}else{
				$makeId = $res['RgpTypeOS_ID'];
				$makeDetails = "";
				if($makeId!=""){
					$sqlMake=imw_query("SELECT `manufacturer`, `style`, `type` FROM `contactlensemake` WHERE `make_id`='".$makeId."'");
					if($sqlMake && imw_num_rows($sqlMake)>0){
						$sqlMake = imw_fetch_assoc($sqlMake);
						$sqlMake = array_filter($sqlMake, "remove_blank");
						$makeDetails = implode(" - ", $sqlMake);
					}
				}
				$arrRxDetails[$dos3][$cnt]['clType']=$clType;
				$arrRxDetails[$dos3][$cnt]['clws_id']=$res['clws_id'];
				$arrRxDetails[$dos3][$cnt]['make_os'] = $makeDetails;
				$arrRxDetails[$dos3][$cnt]['DOS'] 	= $dos;
				$arrRxDetails[$dos3][$cnt]['cl_comment'] 	= $res['cl_comment'];
				
				$arrRxDetails[$dos3][$cnt]['Provider'] 	= $phyRes['provider_id'];
				$arrRxDetails[$dos3][$cnt]['clws_type'] 	= $res['clws_type'];
				$arrRxDetails[$dos3][$cnt]['clws_trial_number'] 	= $res['clws_trial_number'];
				
				$arrRxDetails[$dos3][$cnt]['ProviderName'] 	= $phyName;
				$arrRxDetails[$dos3][$cnt]['OS'] 	= 'OS';
				$arrRxDetails[$dos3][$cnt]['orderHxS_OS'] 	= $res['RgpPowerOS'];
				$arrRxDetails[$dos3][$cnt]['orderHxC_OS']	= $res['RgpOZOS']." ".$res['RgpCTOS'];
				//$arrRxDetails[$dos3][$cnt]['orderHxA_OS'] 	= $res['RgpColorOS'];
				$arrRxDetails[$dos3][$cnt]['orderHxDia_OS']  = $res['RgpDiameterOS'];
				$arrRxDetails[$dos3][$cnt]['orderHxBc_OS'] 	= $res['RgpBCOS'];
				$arrRxDetails[$dos3][$cnt]['orderHxAdd_OS']  = $res['RgpAddOS'];
			}
		}
		if($res['clType']=='cust_rgp'){
			//clubing od and os value in one array as it is saved in different lines from idoc
			if($clwidArr[$res['clws_id']][$clType])$cnt=$clwidArr[$res['clws_id']][$clType];
			else $cnt++;
			$clwidArr[$res['clws_id']][$clType]=$cnt;
			
			if($res['clEye']=='OD'){
				$makeId = $res['RgpCustomTypeOD_ID'];
				$makeDetails = "";
				if($makeId!=""){
					$sqlMake=imw_query("SELECT `manufacturer`, `style`, `type` FROM `contactlensemake` WHERE `make_id`='".$makeId."'");
					if($sqlMake && imw_num_rows($sqlMake)>0){
						$sqlMake = imw_fetch_assoc($sqlMake);
						$sqlMake = array_filter($sqlMake, "remove_blank");
						$makeDetails = implode(" - ", $sqlMake);
					}
				}
				$arrRxDetails[$dos3][$cnt]['clType']=$clType;
				$arrRxDetails[$dos3][$cnt]['clws_id']=$res['clws_id'];
				$arrRxDetails[$dos3][$cnt]['make_od'] = $makeDetails;
				$arrRxDetails[$dos3][$cnt]['DOS'] 	= $dos;
				$arrRxDetails[$dos3][$cnt]['cl_comment'] 	= $res['cl_comment'];
				
				$arrRxDetails[$dos3][$cnt]['Provider'] 	= $phyRes['provider_id'];
				$arrRxDetails[$dos3][$cnt]['ProviderName'] 	= $phyName;
				$arrRxDetails[$dos3][$cnt]['clws_type'] 	= $res['clws_type'];
				$arrRxDetails[$dos3][$cnt]['clws_trial_number'] 	= $res['clws_trial_number'];
				
				$arrRxDetails[$dos3][$cnt]['OD'] 	= 'OD';
				$arrRxDetails[$dos3][$cnt]['orderHxS'] 	= $res['RgpCustomPowerOD'];
				$arrRxDetails[$dos3][$cnt]['orderHxC']	= $res['RgpCustomOZOD']." ".$res['RgpCustomCTOD'];
				//$arrRxDetails[$dos3][$cnt]['orderHxA'] 	= $res['RgpCustomColorOD'];
				$arrRxDetails[$dos3][$cnt]['orderHxDia'] = $res['RgpCustomDiameterOD'];
				$arrRxDetails[$dos3][$cnt]['orderHxBc'] 	= $res['RgpCustomBCOD'];
				$arrRxDetails[$dos3][$cnt]['orderHxAdd'] = $res['RgpCustomAddOD'];
			}else{
				$makeId = $res['RgpCustomTypeOS_ID'];
				$makeDetails = "";
				if($makeId!=""){
					$sqlMake=imw_query("SELECT `manufacturer`, `style`, `type` FROM `contactlensemake` WHERE `make_id`='".$makeId."'");
					if($sqlMake && imw_num_rows($sqlMake)>0){
						$sqlMake = imw_fetch_assoc($sqlMake);
						$sqlMake = array_filter($sqlMake, "remove_blank");
						$makeDetails = implode(" - ", $sqlMake);
					}
				}
				$arrRxDetails[$dos3][$cnt]['clType']=$clType;
				$arrRxDetails[$dos3][$cnt]['clws_id']=$res['clws_id'];
				$arrRxDetails[$dos3][$cnt]['make_os'] = $makeDetails;
				$arrRxDetails[$dos3][$cnt]['DOS'] 	= $dos;
				$arrRxDetails[$dos3][$cnt]['cl_comment'] 	= $res['cl_comment'];
				
				$arrRxDetails[$dos3][$cnt]['Provider'] 	= $phyRes['provider_id'];
				$arrRxDetails[$dos3][$cnt]['clws_type'] 	= $res['clws_type'];
				$arrRxDetails[$dos3][$cnt]['clws_trial_number'] 	= $res['clws_trial_number'];
				
				$arrRxDetails[$dos3][$cnt]['ProviderName'] 	= $phyName;
				$arrRxDetails[$dos3][$cnt]['OS'] 	= 'OS';
				$arrRxDetails[$dos3][$cnt]['orderHxS_OS'] 	= $res['RgpCustomPowerOS'];
				$arrRxDetails[$dos3][$cnt]['orderHxC_OS']	= $res['RgpCustomOZOS']." ".$res['RgpCustomCTOS'];
				//$arrRxDetails[$dos3][$cnt]['orderHxA_OS'] 	= $res['RgpCustomColorOS'];
				$arrRxDetails[$dos3][$cnt]['orderHxDia_OS']  = $res['RgpCustomDiameterOS'];
				$arrRxDetails[$dos3][$cnt]['orderHxBc_OS'] 	= $res['RgpCustomBCOS'];
				$arrRxDetails[$dos3][$cnt]['orderHxAdd_OS']  = $res['RgpCustomAddOS'];
			}
		}
 }
}
$q=imw_query("select * from in_cl_prescriptions where patient_id='$_SESSION[patient_session_id]' and custom_rx=1");
while($res=imw_fetch_assoc($q))
{
	$dos= '';
	$cnt++;
	$dt=explode('-', $res['rx_dos']);
	$dos = $dt[1].'-'.$dt[2].'-'.$dt[0];
	$dos1 = $dt[1].'-'.$dt[2].'-'.substr($dt[0],-2);
	$dos3 =str_replace("-", "", $res['rx_dos']);
	
	$clType='';
	$arrRxDetails[$dos3][$cnt]['custom_id'] 	= $res['id'];
	
	$arrRxDetails[$dos3][$cnt]['DOS'] 	= $dos;
	$arrRxDetails[$dos3][$cnt]['Provider'] 	= $res['physician_id'];
	$arrRxDetails[$dos3][$cnt]['ProviderName'] 	= $res['physician_name'];
	

	$arrRxDetails[$dos3][$cnt]['OD'] 	= 'OD';
	$arrRxDetails[$dos3][$cnt]['make_od'] = $res['rx_make_od'];
	$arrRxDetails[$dos3][$cnt]['orderHxS'] 	= $res['sphere_od'];
	$arrRxDetails[$dos3][$cnt]['orderHxC']	= $res['cylinder_od'];
	$arrRxDetails[$dos3][$cnt]['orderHxA'] 	= $res['axis_od']."&deg;";
	$arrRxDetails[$dos3][$cnt]['orderHxDia'] = $res['diameter_od'];
	$arrRxDetails[$dos3][$cnt]['orderHxBc'] 	= $res['base_od'];
	$arrRxDetails[$dos3][$cnt]['orderHxAdd'] = $res['add_od'];


	$arrRxDetails[$dos3][$cnt]['OS'] 	= 'OS';
	$arrRxDetails[$dos3][$cnt]['make_os'] = $res['rx_make_os'];
	$arrRxDetails[$dos3][$cnt]['orderHxS_OS'] 	= $res['sphere_os'];
	$arrRxDetails[$dos3][$cnt]['orderHxC_OS']	= $res['cylinder_os'];
	$arrRxDetails[$dos3][$cnt]['orderHxA_OS'] 	= $res['axis_os']."&deg;";
	$arrRxDetails[$dos3][$cnt]['orderHxDia_OS'] = $res['diameter_os'];
	$arrRxDetails[$dos3][$cnt]['orderHxBc_OS'] 	= $res['base_os'];
	$arrRxDetails[$dos3][$cnt]['orderHxAdd_OS'] = $res['add_os'];
	$arrRxDetails[$dos3][$cnt]['outside_rx']	=1;
}

krsort($arrRxDetails);
$arrRxDetails1 = $arrRxDetails;

function remove_blank($value){
	return !empty($value) || $value === 0;
}
?>