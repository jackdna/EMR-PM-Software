<?php 
/*
File: lens_rx_list.php
Coded in PHP7
Purpose: Lens RX information
Access Type: Direct access
*/

$rxLimit = (isset($limitRx))?" LIMIT ".$limitRx:"";
/*
################################################################################################
################# BELOW CODE COMMENTED DUE TO TABLES NO MORE EXIST IN NEWER VERSIONS ###########
################################################################################################
$sqlVisionQry = "SELECT *    
FROM chart_master_table
LEFT JOIN chart_left_cc_history ON chart_left_cc_history.form_id = chart_master_table.id
LEFT JOIN chart_vision ON chart_vision.form_id = chart_master_table.id 
WHERE chart_master_table.patient_id = '".$_SESSION['patient_session_id']."' 
AND chart_vision.vis_mr_none_given!='' 
AND chart_master_table.date_of_service!='' 
ORDER BY chart_master_table.date_of_service DESC , chart_vision.exam_date DESC".$rxLimit;
$sqlVisionQry = imw_query($sqlVisionQry);

$arrAllPhy=array();

$cnt=0; $i=0;
$MRHASVALUESORNOT=false;
//$vis_mr_none_given="";
$arrMRGiven = array();

if(imw_num_rows($sqlVisionQry)>0){

	while($sqlVisionRow = imw_fetch_assoc($sqlVisionQry)){
	$Mr3rd=false;
	extract($sqlVisionRow);
	$MRHASVALUESORNOT=true;
	$arrMRGiven=array();
	//$vis_mr_none_given="";1,3 whichever is done by Doctor show that, if all are done by Tech then show MR1
	
$akHasvalue=false;
$mrHasValue=false;

//if(strlen($vis_mr_none_given)>3){	// it will just check if any word exist in column
	$arrMRGiven_all = explode(",", strtolower($vis_mr_none_given));
		
	$mr_statusElements = explode(",",$vis_statusElements);
	if(in_array('elem_mrNoneGiven1=1',$mr_statusElements))
	{
		if(in_array('mr 1',$arrMRGiven_all))array_push($arrMRGiven, 'MR 1');
	}
	if(in_array('elem_mrNoneGiven2=1',$mr_statusElements))
	{
		if(in_array('mr 2',$arrMRGiven_all))array_push($arrMRGiven, 'MR 2');
	}
	if(in_array('elem_mrNoneGiven3=1',$mr_statusElements))
	{
		if(in_array('mr 3',$arrMRGiven_all))array_push($arrMRGiven, 'MR 3');
	}
	

$dos= '';
$dt=explode('-', $date_of_service);
$dos = $dt[1].'-'.$dt[2].'-'.$dt[0];
$dos1 = $dt[1].'-'.$dt[2].'-'.substr($dt[0],-2);
$dos3 =str_replace("-", "", $date_of_service);

if($i==0){
//	$ep=explode(' ', $exam_date);
//	$eD=explode('-', $ep[0]);
//	$lastExamDate = $eD[1].'-'.$eD[2].'-'.$eD[0];
$lastExamDate=$dos;
$lastDocId=$providerId;
}
$i++;

$arrAllPhy[$providerId]=$providerId;


if(in_array("MR 1", $arrMRGiven)){

	if(
	($vis_mr_od_s || $vis_mr_od_c || $vis_mr_od_a || ($vis_mr_od_add && $vis_mr_od_add!='+') || ($vis_mr_od_txt_1 && $vis_mr_od_txt_1!='20/')
	||
	($vis_mr_od_txt_2 && $vis_mr_od_txt_2!='20/')
	||
	$vis_mr_os_s || $vis_mr_os_c || $vis_mr_os_a || ($vis_mr_os_add && $vis_mr_os_add!='+') || ($vis_mr_os_txt_1 && $vis_mr_os_txt_1!='20/')
	||
	($vis_mr_os_txt_2 && $vis_mr_os_txt_2!='20/'))
	){
		$Mr1st = true;
		$mrHasValue=true;
					
		if($vis_ak_od_k!="" ||$vis_ak_od_slash!="" || $vis_ak_od_x!="" ||$vis_ak_os_k!="" ||$vis_ak_os_slash!="" || $vis_ak_os_x!=""){
			$akHasvalue=true;
		}
 	}
if($Mr1st == true){
	$addValue='';
	$cnt++;
	if($vis_mr_od_txt_2!='' || $vis_mr_od_txt_2!='20/') {$addValue = $vis_mr_od_add;}
	
	$arrLensRX[$dos3][$cnt]['OD']['DOS']	=$dos;
	$arrLensRX[$dos3][$cnt]['OD']['DOS1']	=$dos1;
	$arrLensRX[$dos3][$cnt]['OD']['physician_id']	=$providerId;
	$arrLensRX[$dos3][$cnt]['OD']['Sphere']	=$vis_mr_od_s;
	$arrLensRX[$dos3][$cnt]['OD']['Cylinder']=$vis_mr_od_c;
	$arrLensRX[$dos3][$cnt]['OD']['Axis']	=$vis_mr_od_a;
	$arrLensRX[$dos3][$cnt]['OD']['Axis_VA']	=$vis_mr_od_txt_1;
	$arrLensRX[$dos3][$cnt]['OD']['Add']	=$addValue;
	$arrLensRX[$dos3][$cnt]['OD']['Add_VA']	=$vis_mr_od_txt_2;
	$arrLensRX[$dos3][$cnt]['OD']['MR']= "MR1";

	$addValue='';
	if($vis_mr_os_txt_2!='' || $vis_mr_os_txt_2!='20/') {$addValue = $vis_mr_os_add;}
	$arrLensRX[$dos3][$cnt]['OS']['Sphere']	=$vis_mr_os_s;
	$arrLensRX[$dos3][$cnt]['OS']['Cylinder']=$vis_mr_os_c;
	$arrLensRX[$dos3][$cnt]['OS']['Axis']	=$vis_mr_os_a;
	$arrLensRX[$dos3][$cnt]['OS']['Axis_VA']	=$vis_mr_os_txt_1;
	$arrLensRX[$dos3][$cnt]['OS']['Add']		=$addValue;
	$arrLensRX[$dos3][$cnt]['OS']['Add_VA']	=$vis_mr_os_txt_2;
	$arrLensRX[$dos3][$cnt]['OD']['Base'] = '';
	$arrLensRX[$dos3][$cnt]['OS']['Base'] = '';
	
	// PRISM VALUES
	$arrLensPrism[$cnt]['OD']['mr_od_p']	=$vis_mr_od_p;
	$arrLensPrism[$cnt]['OD']['mr_od_prism']	=$vis_mr_od_sel_1;
	$arrLensPrism[$cnt]['OD']['mr_od_splash']	=$vis_mr_od_slash;
	$arrLensPrism[$cnt]['OD']['mr_od_sel']	=$vis_mr_od_prism;

	$arrLensPrism[$cnt]['OS']['mr_os_p']	=$vis_mr_os_p;
	$arrLensPrism[$cnt]['OS']['mr_os_prism']	=$vis_mr_os_sel_1;
	$arrLensPrism[$cnt]['OS']['mr_os_splash']	=$vis_mr_os_slash;
	$arrLensPrism[$cnt]['OS']['mr_os_sel']	=$vis_mr_os_prism;
} 
	$arrLensRX[$dos3][$cnt]['OD']['VIS_ID']	=$vis_id;
	$arrLensRX[$dos3][$cnt]['OD']['ID']= $id;
}

if(in_array("MR 2", $arrMRGiven)){
	
if(($vis_mr_od_given_s || $vis_mr_od_given_given_c || $vis_mr_od_given_given_a || ($vis_mr_od_given_given_add && $vis_mr_od_given_given_add!='+'))
	||
	($vis_mr_od_given_given_txt_1 && $vis_mr_od_given_txt_1!='20/')
	||
	($vis_mr_od_given_txt_2 && $vis_mr_od_given_txt_2!='20/')
	||
	($vis_mr_os_given_s || $vis_mr_os_given_c || $vis_mr_os_given_a || ($vis_mr_os_given_add && $vis_mr_os_given_add!='+'))
	||
	($vis_mr_os_given_txt_1 && $vis_mr_os_given_txt_1!='20/')
	||
	($vis_mr_os_given_txt_2 && $vis_mr_os_given_txt_2!='20/')
	){
	
if(
	($vis_mr_od_given_s || $vis_mr_od_given_c || $vis_mr_od_given_a || $vis_mr_od_given_add || ($vis_mr_od_given_txt_1 && $vis_mr_od_given_txt_1!='20/')
	||
	($vis_mr_od_given_txt_2 && $vis_mr_od_given_txt_2!='20/')
	||
	$vis_mr_os_given_s || $vis_mr_os_given_c || $vis_mr_os_given_a || $vis_mr_os_given_add || ($vis_mr_os_given_txt_1 && $vis_mr_os_given_txt_1!='20/')
	||
	($vis_mr_os_given_txt_2 && $vis_mr_os_given_txt_2!='20/'))
	){
	$Mr2ng = true;
	$mr2HasValue=true;
	
	if($vis_ak_od_k!="" ||$vis_ak_od_slash!="" || $vis_ak_od_x!="" ||$vis_ak_os_k!="" ||$vis_ak_os_slash!="" || $vis_ak_os_x!=""){
		$akHasvalue=true;
	}
 }
if($Mr2ng == true){
	$cnt++;
	
	$addValue='';
	if($vis_mr_od_given_txt_2!='' || $vis_mr_od_given_txt_2!='20/') {$addValue = $vis_mr_od_given_add;}
	$arrLensRX[$dos3][$cnt]['OD']['DOS']	=$dos;
	$arrLensRX[$dos3][$cnt]['OD']['DOS1']	=$dos1;
	$arrLensRX[$dos3][$cnt]['OD']['physician_id']	=$providerId;
	$arrLensRX[$dos3][$cnt]['OD']['Sphere']	=$vis_mr_od_given_s;
	$arrLensRX[$dos3][$cnt]['OD']['Cylinder']=$vis_mr_od_given_c;
	$arrLensRX[$dos3][$cnt]['OD']['Axis']	=$vis_mr_od_given_a;
	$arrLensRX[$dos3][$cnt]['OD']['Axis_VA']	=$vis_mr_od_given_txt_1;
	$arrLensRX[$dos3][$cnt]['OD']['Add']	=$addValue;
	$arrLensRX[$dos3][$cnt]['OD']['Axis_VA']	=$vis_mr_od_given_txt_2;
	$arrLensRX[$dos3][$cnt]['OD']['MR']= "MR2";

	$addValue='';
	if($vis_mr_os_given_txt_2!='' || $vis_mr_os_given_txt_2!='20/') {$addValue = $vis_mr_os_given_add;}
	$arrLensRX[$dos3][$cnt]['OS']['Sphere']	=$vis_mr_os_given_s;
	$arrLensRX[$dos3][$cnt]['OS']['Cylinder']=$vis_mr_os_given_c;
	$arrLensRX[$dos3][$cnt]['OS']['Axis']	=$vis_mr_os_given_a;
	$arrLensRX[$dos3][$cnt]['OS']['Axis_VA']	=$vis_mr_os_given_txt_1;
	$arrLensRX[$dos3][$cnt]['OS']['Add']		=$addValue;
	$arrLensRX[$dos3][$cnt]['OS']['Add_VA']	=$vis_mr_os_given_txt_2;
	$arrLensRX[$dos3][$cnt]['OD']['Base'] = '';
	$arrLensRX[$dos3][$cnt]['OS']['Base'] = '';
	
	// PRISM VALUES
	$arrLensPrism[$cnt]['OD']['mr_od_p']	=$vis_mr_od_given_p;
	$arrLensPrism[$cnt]['OD']['mr_od_prism']	=$vis_mr_od_given_sel_1;
	$arrLensPrism[$cnt]['OD']['mr_od_splash']	=$vis_mr_od_given_slash;
	$arrLensPrism[$cnt]['OD']['mr_od_sel']	=$vis_mr_od_given_prism;

	$arrLensPrism[$cnt]['OS']['mr_os_p']	=$vis_mr_os_given_p;
	$arrLensPrism[$cnt]['OS']['mr_os_prism']	=$vis_mr_os_given_sel_1;
	$arrLensPrism[$cnt]['OS']['mr_os_splash']	=$vis_mr_os_given_slash;
	$arrLensPrism[$cnt]['OS']['mr_os_sel']	=$vis_mr_os_given_prism;
} 
}
$arrLensRX[$dos3][$cnt]['OD']['VIS_ID']	=$vis_id;
$arrLensRX[$dos3][$cnt]['OD']['ID']= $id;
} 


// To Show MR 3 Values
if(in_array("MR 3", $arrMRGiven)){
if(
	($visMrOtherOdS_3 || $visMrOtherOdC_3  || $visMrOtherOdA_3 || $visMrOtherOdA_3 || ($visMrOtherOdTxt1_3 && $visMrOtherOdTxt1_3!='20/') 
	||
	($visMrOtherOdTxt2_3  && $visMrOtherOdTxt2_3 !='20/') 
	||
	$visMrOtherOsS_3 || $visMrOtherOsC_3 || $visMrOtherOsA_3 || ($visMrOtherOsAdd_3 && $visMrOtherOsAdd_3!='+') || ($visMrOtherOsTxt1_3 && $visMrOtherOsTxt1_3!='20/')  
	||
	($visMrOtherOsTxt2_3 && $visMrOtherOsTxt2_3!='20/'))
	){

if(
	($visMrOtherOdS_3 || $visMrOtherOdC_3  || $visMrOtherOdA_3 || $visMrOtherOdA_3 || ($visMrOtherOdTxt1_3 && $visMrOtherOdTxt1_3!='20/')
	||
	($visMrOtherOdTxt2_3  && $visMrOtherOdTxt2_3 !='20/')
	||
	$visMrOtherOsS_3  || $visMrOtherOsC_3 || $visMrOtherOsA_3 || $visMrOtherOsAdd_3 || ($visMrOtherOsTxt1_3 && $visMrOtherOsTxt1_3!='20/')
	||
	($visMrOtherOsTxt2_3 && $visMrOtherOsTxt2_3!='20/'))
	){
	$Mr3rd = true;
	$mr3HasValue=true;
	
	if($vis_ak_od_k!="" ||$vis_ak_od_slash!="" || $vis_ak_od_x!="" ||$vis_ak_os_k!="" ||$vis_ak_os_slash!="" || $vis_ak_os_x!=""){
		$akHasvalue=true;
	}
 }
if($Mr3rd == true){
	$cnt++;
	
	$addValue='';
	if($visMrOtherOdTxt2_3!='' || $visMrOtherOdTxt2_3!='20/') {$addValue = $visMrOtherOdAdd_3;}
	$arrLensRX[$dos3][$cnt]['OD']['DOS']	=$dos;
	$arrLensRX[$dos3][$cnt]['OD']['DOS1']	=$dos1;
	$arrLensRX[$dos3][$cnt]['OD']['physician_id']	=$providerId;
	$arrLensRX[$dos3][$cnt]['OD']['Sphere']	=$visMrOtherOdS_3;
	$arrLensRX[$dos3][$cnt]['OD']['Cylinder']=$visMrOtherOdC_3;
	$arrLensRX[$dos3][$cnt]['OD']['Axis']	=$visMrOtherOdA_3;
	$arrLensRX[$dos3][$cnt]['OD']['Axis_VA']	=$visMrOtherOdTxt1_3;
	$arrLensRX[$dos3][$cnt]['OD']['Add']	=$addValue;
	$arrLensRX[$dos3][$cnt]['OD']['Add_VA']=$visMrOtherOdTxt2_3;
	$arrLensRX[$dos3][$cnt]['OD']['MR']= "MR3";

	$addValue='';
	if($visMrOtherOsTxt2_3!='' || $visMrOtherOsTxt2_3!='20/') {$addValue = $visMrOtherOsAdd_3;}
	$arrLensRX[$dos3][$cnt]['OS']['Sphere']	=$visMrOtherOsS_3;
	$arrLensRX[$dos3][$cnt]['OS']['Cylinder']=$visMrOtherOsC_3;
	$arrLensRX[$dos3][$cnt]['OS']['Axis']	=$visMrOtherOsA_3;
	$arrLensRX[$dos3][$cnt]['OS']['Axis_VA']	=$visMrOtherOsTxt1_3;
	$arrLensRX[$dos3][$cnt]['OS']['Add']		=$addValue;
	$arrLensRX[$dos3][$cnt]['OS']['Add_VA']	=$visMrOtherOsTxt2_3;
	$arrLensRX[$dos3][$cnt]['OD']['Base'] = '';
	$arrLensRX[$dos3][$cnt]['OS']['Base'] = '';
	
	// PRISM VALUES
	$arrLensPrism[$cnt]['OD']['mr_od_p']	=$visMrOtherOdP_3;
	$arrLensPrism[$cnt]['OD']['mr_od_prism']	=$visMrOtherOdSel1_3;
	$arrLensPrism[$cnt]['OD']['mr_od_splash']	=$visMrOtherOdSlash_3;
	$arrLensPrism[$cnt]['OD']['mr_od_sel']	=$visMrOtherOdPrism_3;

	$arrLensPrism[$cnt]['OS']['mr_os_p']	=$visMrOtherOsP_3;
	$arrLensPrism[$cnt]['OS']['mr_os_prism']	=$visMrOtherOsSel1_3;
	$arrLensPrism[$cnt]['OS']['mr_os_splash']	=$visMrOtherOsSlash_3;
	$arrLensPrism[$cnt]['OS']['mr_os_sel']	=$visMrOtherOsPrism_3;
}
}
$arrLensRX[$dos3][$cnt]['OD']['VIS_ID']	=$vis_id;
$arrLensRX[$dos3][$cnt]['OD']['ID']= $id;
}
	
	// NPD / DPD	
	//$arrLensVision[$vis_id]['OD']['DPD']	=$vis_dis_od_sel_1.'&nbsp;&nbsp;'.$vis_dis_od_txt_1;
	//$arrLensVision[$vis_id]['OD']['NPD']	=$vis_near_od_sel_1.'&nbsp;&nbsp;'.$vis_near_od_txt_1;
	$arrLensVision[$vis_id]['OD']['DPD']	= '';
	$arrLensVision[$vis_id]['OD']['NPD']	= '';
	$arrLensVision[$vis_id]['OD']['MINSEG']	= '';
	$arrLensVision[$vis_id]['OD']['OC']		= '';
		
	//$arrLensVision[$vis_id]['OS']['DPD']	=$vis_dis_os_sel_1.'&nbsp;&nbsp;'.$vis_dis_os_txt_1;
	//$arrLensVision[$vis_id]['OS']['NPD']	=$vis_near_os_sel_1.'&nbsp;&nbsp;'.$vis_near_os_txt_1;
	$arrLensVision[$vis_id]['OS']['DPD']	= '';
	$arrLensVision[$vis_id]['OS']['NPD']	= '';
	$arrLensVision[$vis_id]['OS']['MINSEG']	= '';
	$arrLensVision[$vis_id]['OS']['OC']		= '';
	} // END WHILE
}
*/
$moreRxQ=imw_query("SELECT c1.*,cm.date_of_service, 
cvm.form_id, cvm.status_elements, 
c2.site, c2.sph, c2.cyl, c2.axs, c2.ad, c2.prsm_p, c2.prism, c2.slash, c2.sel_1, c2.txt_1, c2.txt_2, c2.sel2v, c2.chart_pc_mr_id, 
cm.providerId
FROM chart_master_table cm
INNER JOIN chart_vis_master cvm ON cm.id = cvm.form_id
LEFT JOIN chart_pc_mr c1 ON c1.id_chart_vis_master = cvm.id
LEFT JOIN chart_pc_mr_values c2 ON c2.chart_pc_mr_id = c1.id
WHERE cm.patient_id=$_SESSION[patient_session_id] AND c1.ex_type='MR' AND c1.mr_none_given!='' AND c1.delete_by='0' AND cm.purge_status='0'
ORDER BY ex_number")or die(imw_error());
	
while($moreRx=imw_fetch_assoc($moreRxQ))
{
	$indx = $moreRx['ex_number'];
	$mr_statusElements = explode(",",$moreRx['status_elements']);
	$mr_statusElements = array_combine($mr_statusElements,$mr_statusElements);
	$sfx1 = $sfx2 = $mrGiven_str = "";
	if($indx>1){
		$sfx1 = "Other";
		if($indx>2){
		$sfx2 = "_".$indx;
		}
	}
	if(strtolower($moreRx['site'])=='od')$siteVal='Od';
	elseif(strtolower($moreRx['site'])=='os') $siteVal='Os';
		
	if(!$siteVal)continue;
	$sph_str=$cyl_str=$axs_str=$desc_str=$givn_mr="";
	$sph_str='elem_visMr'.$sfx1.$siteVal.'S'.$sfx2.'=1';
	$cyl_str='elem_visMr'.$sfx1.$siteVal.'C'.$sfx2.'=1';
	$axs_str='elem_visMr'.$sfx1.$siteVal.'A'.$sfx2.'=1';
	$desc_str='elem_visMrDescOther_'.$indx.'=1';
	$givn_mr='elem_mrNoneGiven'.$indx.'=1';
	
	if((!empty($moreRx['sph']) && $mr_statusElements[$sph_str] ) || (!empty($moreRx['cyl']) && $mr_statusElements[$cyl_str] ) || 
	  (!empty($moreRx['axs']) && $mr_statusElements[$axs_str] )  || (!empty($moreRx['ex_desc']) && $mr_statusElements[$desc_str])){
		if($mr_statusElements[$givn_mr])$mrGiven_str="MR ".$indx;
		if($mrGiven_str!=$moreRx['mr_none_given'])$mrGiven_str='';
	}
	if(empty($mrGiven_str))continue;
	
	if($chart_pc_mr_id[$moreRx['chart_pc_mr_id']])$cnt=$chart_pc_mr_id[$moreRx['chart_pc_mr_id']];
	else {$cnt++;$chart_pc_mr_id[$moreRx['chart_pc_mr_id']]=$cnt;}
	$date_of_service=$moreRx['date_of_service'];
	$dt=explode('-', $date_of_service);
	$dos = $dt[1].'-'.$dt[2].'-'.$dt[0];
	$dos1 = $dt[1].'-'.$dt[2].'-'.substr($dt[0],-2);
	$dos3 =str_replace("-", "", $date_of_service);
	if($i==0)
	{
		$lastExamDate=$dos;
		$lastDocId=$moreRx['providerId'];
	}
	$arrAllPhy[$moreRx['providerId']]=$moreRx['providerId'];
	$i++;
	$site=$moreRx['site'];
	$arrLensRX[$dos3][$cnt][$site]['DOS']			=$dos;
	$arrLensRX[$dos3][$cnt][$site]['DOS1']			=$dos1;
	$arrLensRX[$dos3][$cnt][$site]['physician_id']	=$moreRx['providerId'];
	$arrLensRX[$dos3][$cnt][$site]['Sphere']		=$moreRx['sph'];
	$arrLensRX[$dos3][$cnt][$site]['Cylinder']		=$moreRx['cyl'];
	$arrLensRX[$dos3][$cnt][$site]['Axis']			=$moreRx['axs'];
	$arrLensRX[$dos3][$cnt][$site]['Axis_VA']		=$moreRx['txt_1'];
	$arrLensRX[$dos3][$cnt][$site]['Add']			=$moreRx['ad'];
	$arrLensRX[$dos3][$cnt][$site]['Add_VA']		=$moreRx['txt_2'];
	$arrLensRX[$dos3][$cnt][$site]['MR']			=$moreRx['mr_none_given'];
	$arrLensRX[$dos3][$cnt][$site]['VIS_ID']		=$moreRx['id_chart_vis_master'];
	$arrLensRX[$dos3][$cnt][$site]['ID']			=$moreRx['form_id'];
	// PRISM VALUES
	$siteL=strtolower($site);
	$arrLensPrism[$cnt][$site]['mr_'.$siteL.'_p']			=$moreRx['prsm_p'];
	$arrLensPrism[$cnt][$site]['mr_'.$siteL.'_prism']		=$moreRx['prism'];
	$arrLensPrism[$cnt][$site]['mr_'.$siteL.'_splash']		=$moreRx['slash'];
	$arrLensPrism[$cnt][$site]['mr_'.$siteL.'_sel']			=$moreRx['sel_1'];
}
/*Custom Rx //Optical */
$customRxq=imw_query("select * from in_optical_order_form where custom_rx=1 and patient_id='".$_SESSION['patient_session_id']."'".(($noIncorrect)?' AND incorrect_rx_status=0':'')." ORDER BY id DESC ".$rxLimit)or die(imw_error());
	while($cRx=imw_fetch_object($customRxq))
	{
		$cnt++;
		$dt=explode('-', $cRx->rx_dos);
		$dos = $dt[1].'-'.$dt[2].'-'.$dt[0];
		$dos3 = str_replace("-", "", $cRx->rx_dos);
		$arrLensRX[$dos3][$cnt]['OD']['DOS']	= $dos;
		$arrLensRX[$dos3][$cnt]['rx_id'] 		= $cRx->id;
		$arrLensRX[$dos3][$cnt]['incorrect'] 	= $cRx->incorrect_rx_status;
		$arrLensRX[$dos3][$cnt]['OD']['DOS1'] 	= $dt[1].'-'.$dt[2].'-'.substr($dt[0],-2);
		
		$arrLensRX[$dos3][$cnt]['OD']['physician_id']	=$cRx->physician_id;
		$arrLensRX[$dos3][$cnt]['OD']['physician_name']	=$cRx->physician_name;
		
		$arrLensRX[$dos3][$cnt]['OD']['Sphere']		=$cRx->sphere_od;
		$arrLensRX[$dos3][$cnt]['OD']['Cylinder']	=$cRx->cyl_od;
		$arrLensRX[$dos3][$cnt]['OD']['Axis']		=$cRx->axis_od;
		$arrLensRX[$dos3][$cnt]['OD']['Axis_VA']	=$cRx->axis_od_va;
		$arrLensRX[$dos3][$cnt]['OD']['Add']		=$cRx->add_od;
		$arrLensRX[$dos3][$cnt]['OD']['Add_VA']		=$cRx->add_od_va;
		$arrLensRX[$dos3][$cnt]['OD']['MR'] 		= "custom";
		$arrLensRX[$dos3][$cnt]['OD']['ID']			= $cRx->id;
	
		$arrLensRX[$dos3][$cnt]['OS']['Sphere']		=$cRx->sphere_os;
		$arrLensRX[$dos3][$cnt]['OS']['Cylinder']	=$cRx->cyl_os;
		$arrLensRX[$dos3][$cnt]['OS']['Axis']		=$cRx->axis_os;
		$arrLensRX[$dos3][$cnt]['OS']['Axis_VA']	=$cRx->axis_os_va;
		$arrLensRX[$dos3][$cnt]['OS']['Add']		=$cRx->add_os;
		$arrLensRX[$dos3][$cnt]['OS']['Add_VA']		=$cRx->add_os_va;
		$arrLensRX[$dos3][$cnt]['OD']['Base']		=$cRx->base_od;
		$arrLensRX[$dos3][$cnt]['OS']['Base']		=$cRx->base_os;
		
		// PRISM VALUES
		$arrLensPrism[$cnt]['OD']['mr_od_p']		=$cRx->mr_od_p;
		$arrLensPrism[$cnt]['OD']['mr_od_prism']	=$cRx->mr_od_prism;
		$arrLensPrism[$cnt]['OD']['mr_od_splash']	=$cRx->mr_od_splash;
		$arrLensPrism[$cnt]['OD']['mr_od_sel']		=$cRx->mr_od_sel;
	
		$arrLensPrism[$cnt]['OS']['mr_os_p']		=$cRx->mr_os_p;
		$arrLensPrism[$cnt]['OS']['mr_os_prism']	=$cRx->mr_os_prism;
		$arrLensPrism[$cnt]['OS']['mr_os_splash']	=$cRx->mr_os_splash;
		$arrLensPrism[$cnt]['OS']['mr_os_sel']		=$cRx->mr_os_sel;	
		
		
		$vis_id='5200'+$cnt;
		$arrLensRX[$dos3][$cnt]['OD']['VIS_ID']	=$vis_id;
		$arrLensVision[$vis_id]['OD']['DPD']	=$cRx->dist_pd_od;
		$arrLensVision[$vis_id]['OD']['NPD']	=$cRx->near_pd_od;
		$arrLensVision[$vis_id]['OD']['MINSEG']	=$cRx->seg_od;
		$arrLensVision[$vis_id]['OD']['OC']		=$cRx->oc_od;
		
		$arrLensVision[$vis_id]['OS']['DPD']	=$cRx->dist_pd_os;
		$arrLensVision[$vis_id]['OS']['NPD']	=$cRx->near_pd_os;
		$arrLensVision[$vis_id]['OS']['MINSEG']	=$cRx->seg_os;
		$arrLensVision[$vis_id]['OS']['OC']		=$cRx->oc_os;
		
		$arrLensVision[$vis_id]['outside_rx']	=1;
	}
/*Fix for date wise sorting*/
krsort($arrLensRX);
$arrLensRX1 = $arrLensRX;
/*<!-- End To Show MR 3 Values-->*/
?>