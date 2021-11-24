<?php
$fdr_pat_img = getcwd()."/../patient_access/patient_photos/";

$yesNo = "no";
// IF PRINT THEN FORM ID
$print_form_id = $_REQUEST['print_form_id'];
if($print_form_id!=""){
	$form_id = $print_form_id;
} 
// IF PRINT DOS SELECT FORM ID THEN FORM ID
if(is_array($_REQUEST["chart_nopro"])){
	if(count($_REQUEST["chart_nopro"])>0){
		$formIdToPrint = $_REQUEST['formIdToPrint'];
		if($formIdToPrint!="" && in_array("Chart Notes",$_REQUEST["chart_nopro"]) ){//&& in_array("This Visit",$_REQUEST["chart_nopro"])
			$form_id = $formIdToPrint;
		}
	}
}
// IF PRINT THEN FORM ID

######Audit For Patient Export PHI###############
$qryGetAuditPolicies = "select policy_status as plPHI from audit_policies where policy_id = 11";
$rsGetAuditPolicies = imw_query($qryGetAuditPolicies);
if($rsGetAuditPolicies){
	if(imw_num_rows($rsGetAuditPolicies)){
		extract(imw_fetch_array($rsGetAuditPolicies));		
	}
}
else{
	$phiError = "Error : ". imw_errno() . ": " . imw_error();
}
	
##############################################

// IF PRINT #THEN FORM ID
#####
//GET ReleaseNumber
$getRelNoQry = imw_query("SELECT releaseNumber FROM chart_master_table WHERE patient_id = '$patient_id' AND id = '$form_id'");
$getRelNoRow = imw_fetch_assoc($getRelNoQry);
$releaseNumber = $getRelNoRow['releaseNumber'];

$tdate = date("m-d-Y");
$ab = "select default_facility from patient_data where id = $pid";		
$c = imw_query($ab);
$r1 = imw_fetch_array($c);
$facility_id_p = $r1['default_facility'];
if($facility_id_p != ''){
	$query = "select name from facility where id = '$facility_id_p'";
	$result = imw_query($query);
	$rows = imw_fetch_array($result);
	$patient_facility = "(".$rows['name'].")";
}

$check_data = "select * from lists where pid = '$pid' and type in(3,7)";
$checkSql = @imw_query($check_data);
if(@imw_num_rows($checkSql)>0){
	while($allergy_row = imw_fetch_array($checkSql)){
		$allergy_array[] = $allergy_row["title"]; 
	}
	if(count($allergy_array) == 1 && $allergy_array[0] == 'NKDA'){
		$allergy = "NKDA ";
	}else{
		$allergy = "<font class='text_value' color='red'>".implode(", ",$allergy_array)."</font>";
	}
}else{
	$allergy = "<font class='text_value'>NKDA</font>";
}
// Chart Vision

//--
$id_chart_vis_master=0;
$sql = "SELECT *, c5.ex_desc as ex_desc_pam, c2.ex_desc as ex_desc_ak, c1.id AS id_chart_vis_master  
		FROM chart_vis_master c1
		LEFT JOIN chart_ak c2 ON c2.id_chart_vis_master = c1.id
		LEFT JOIN chart_exo c3 ON c3.id_chart_vis_master = c1.id
		LEFT JOIN chart_bat c4 ON c4.id_chart_vis_master = c1.id
		LEFT JOIN chart_pam c5 ON c5.id_chart_vis_master = c1.id
		WHERE c1.patient_id = '".$patient_id."' AND c1.form_id = '".$form_id."' ";
$row = sqlQuery($sql);
if($row!=false){
	$statusElements = $row["status_elements"];
	$id_chart_vis_master = $row["id_chart_vis_master"];
	
	//PAM --			
	$visPam = (strpos($statusElements, "elem_visPam=1") !== false) ? $row["pam"] : "";
	$vis_pam_od_txt_1 = (strpos($statusElements, "elem_visPamOdTxt1=1") !== false) ? $row["txt1_od"] : "";
	$vis_pam_os_txt_1 = (strpos($statusElements, "elem_visPamOsTxt1=1") !== false) ? $row["txt1_os"] : "";
	$vis_pam_ou_txt_1 = (strpos($statusElements, "elem_visPamOuTxt1=1") !== false) ? $row["txt1_ou"] : "";
	$vis_pam_od_sel_2 = (strpos($statusElements, "elem_visPamOdSel2=1") !== false) ? $row["sel2"] : "";
	$vis_pam_od_txt_2 = (strpos($statusElements, "elem_visPamOdTxt2=1") !== false) ? $row["txt2_od"] : "";
	$vis_pam_os_txt_2 = (strpos($statusElements, "elem_visPamOsTxt2=1") !== false) ? $row["txt2_os"] : "";
	$vis_pam_ou_txt_2 = (strpos($statusElements, "elem_visPamOuTxt2=1") !== false) ? $row["txt2_ou"] : "";
	$vis_pam_desc = (strpos($statusElements, "elem_pamDesc=1") !== false) ? $row["ex_desc_pam"] : "";
	//PAM --
	
	//BAT --
	$txt_vis_bat_nl_od = (strpos($statusElements, "elem_visBatNlOd=1") !== false)?$row["nl_od"]:"";
	$txt_vis_bat_low_od =(strpos($statusElements, "elem_visBatLowOd=1") !== false)? $row["l_od"]:"";
	$txt_vis_bat_med_od = (strpos($statusElements, "elem_visBatMedOd=1") !== false)? $row["m_od"]:"";
	$txt_vis_bat_high_od =(strpos($statusElements, "elem_visBatHighOd=1") !== false)?$row["h_od"]:"";
	$txt_vis_bat_nl_os = (strpos($statusElements, "elem_visBatNlOs=1") !== false)?$row["nl_os"]:"";
	$txt_vis_bat_low_os = (strpos($statusElements, "elem_visBatLowOs=1") !== false)?$row["l_os"]:"";
	$txt_vis_bat_med_os =(strpos($statusElements, "elem_visBatMedOs=1") !== false)?$row["m_os"]:"";
	$txt_vis_bat_high_os =(strpos($statusElements, "elem_visBatHighOs=1") !== false)?$row["h_os"]:"";
	
	$txt_vis_bat_nl_ou = (strpos($statusElements, "elem_visBatNlOu=1") !== false)?$row["nl_ou"]:"";
	$txt_vis_bat_low_ou = (strpos($statusElements, "elem_visBatLowOu=1") !== false)?$row["l_ou"]:"";
	$txt_vis_bat_med_ou =(strpos($statusElements, "elem_visBatMedOu=1") !== false)?$row["m_ou"]:"";
	$txt_vis_bat_high_ou =(strpos($statusElements, "elem_visBatHighOu=1") !== false)?$row["h_ou"]:"";
	$txt_vis_bat_desc =(strpos($statusElements, "elem_visBatDesc=1") !== false)?$row["vis_bat_desc"]:""; //removeExamDateStr($row[0]["vis_bat_desc"]);
	$txt_vis_bat_examdate = $row["examDateDistance"];//getExamDateStr($row[0]["vis_bat_desc"]);
	//BAT --
	
	//AK --
	$txt_vis_ak_od_k = (strpos($statusElements, "elem_visAkOdK=1") !== false)?$row["k_od"]:"";
	$txt_vis_ak_od_slash = (strpos($statusElements, "elem_visAkOdSlash=1") !== false)?$row["slash_od"]:"";
	$txt_vis_ak_od_x = (strpos($statusElements, "elem_visAkOdX=1") !== false)?$row["x_od"]:"";			
	$txt_vis_ak_os_k = (strpos($statusElements, "elem_visAkOsK=1") !== false)?$row["k_os"]:"";
	$txt_vis_ak_os_slash =(strpos($statusElements, "elem_visAkOsSlash=1") !== false)? $row["slash_os"]:"";
	$txt_vis_ak_os_x = (strpos($statusElements, "elem_visAkOsX=1") !== false)?$row["x_os"]:"";
	$txt_vis_ar_ak_desc =$row["ex_desc_ak"];//removeExamDateStr($row[0]["vis_ar_ak_desc"]) ;
	
	//Comments --
	//Check For old comments
	if(strpos($txt_vis_ar_ak_desc,"<~ED~>")!== false){
		$commentsArrayTmp=explode("<~ED~>",$txt_vis_ar_ak_desc);
		$txt_vis_ar_ak_desc=$commentsArrayTmp[0];
	}
	//Comments --
	//AK --		
}

//Acuity
$sql = "SELECT * FROM chart_vis_master c1
		LEFT JOIN chart_acuity c2 ON c2.id_chart_vis_master = c1.id
		WHERE c1.patient_id = '".$patient_id."' AND c1.form_id = '".$form_id."' 
		ORDER BY sec_indx
		";
$res = sqlStatement($sql);
for($i=1; $row=sqlFetchArray($res); $i++){

	$sec_name = $row["sec_name"];
	$sec_indx = $row["sec_indx"];
	if($sec_name == "Distance"){
		${"sel_vis_dis_od_sel_".$sec_indx} =(strpos($statusElements, "elem_visDisOdTxt".$sec_indx."=1") !== false)?$row["sel_od"]:"";
		${"txt_vis_dis_od_txt_".$sec_indx} =(strpos($statusElements, "elem_visDisOdTxt".$sec_indx."=1") !== false)?$row["txt_od"]:"";
		${"sel_vis_dis_os_sel_".$sec_indx} =(strpos($statusElements, "elem_visDisOsTxt".$sec_indx."=1") !== false)? $row["sel_os"]:"";
		${"txt_vis_dis_os_txt_".$sec_indx} =(strpos($statusElements, "elem_visDisOsTxt".$sec_indx."=1") !== false)? $row["txt_os"]:"";
		${"sel_vis_dis_ou_sel_".$sec_indx} =(strpos($statusElements, "elem_visDisOuTxt".$sec_indx."=1") !== false)? $row["sel_ou"]:"";
		${"txt_vis_dis_ou_txt_".$sec_indx} =(strpos($statusElements, "elem_visDisOuTxt".$sec_indx."=1") !== false)? $row["txt_ou"]:"";
		$txt_vis_dis_near_desc = (strpos($statusElements, "elem_visNearOdTxt".$sec_indx."=1") !== false || strpos($statusElements, "elem_visNearOsTxt".$sec_indx."=1") !== false)?$row["ex_desc"]:"";
		
		//Comments --
		//Check For old comments
		if(strpos($txt_vis_dis_near_desc,"<~ED~>")!== false){
			$commentsArrayTmp=explode("<~ED~>",$txt_vis_dis_near_desc);
			$txt_vis_dis_near_desc=$commentsArrayTmp[0];
		}
		//Comments --
		
	}else if($sec_name == "Near"){
		${"sel_vis_near_od_sel_".$sec_indx} =(strpos($statusElements, "elem_visNearOdTxt".$sec_indx."=1") !== false)? $row["sel_od"]:"";
		${"txt_vis_near_od_txt_".$sec_indx} = (strpos($statusElements, "elem_visNearOdTxt".$sec_indx."=1") !== false)?$row["txt_od"]:"";				
		${"sel_vis_near_os_sel_".$sec_indx} = (strpos($statusElements, "elem_visNearOsTxt".$sec_indx."=1") !== false)?$row["sel_os"]:"";
		${"txt_vis_near_os_txt_".$sec_indx} = (strpos($statusElements, "elem_visNearOsTxt".$sec_indx."=1") !== false)?$row["txt_os"]:"";
		${"sel_vis_near_ou_sel_".$sec_indx} =(strpos($statusElements, "elem_visNearOuTxt".$sec_indx."=1") !== false)? $row["sel_ou"]:"";
		${"txt_vis_near_ou_txt_".$sec_indx} =(strpos($statusElements, "elem_visNearOuTxt".$sec_indx."=1") !== false)? $row["txt_ou"]:"";
		$txt_vis_near_desc =(strpos($statusElements, "elem_visNearDesc=1") !== false)?$row["ex_desc"]:"";// removeExamDateStr($row["vis_near_desc"]);
		$txt_vis_near_examdate = $row["examDateDistance "];
	
	}else if($sec_name == "Ad. Acuity"){
		$vis_dis_od_sel_3=(strpos($statusElements, "elem_visDisOdSel3=1") !== false) ? $row["sel_od"]: "";
		$vis_dis_od_txt_3=(strpos($statusElements, "elem_visDisOdTxt3=1") !== false) ? $row["txt_od"]: "";
		$vis_dis_os_sel_3=(strpos($statusElements, "elem_visDisOsSel3=1") !== false) ? $row["sel_os"]: "";
		$vis_dis_os_txt_3=(strpos($statusElements, "elem_visDisOsTxt3=1") !== false) ? $row["txt_os"]: "";
		$vis_dis_ou_sel_3=(strpos($statusElements, "elem_visDisOuSel3=1") !== false) ? $row["sel_ou"]: "";
		$vis_dis_ou_txt_3=(strpos($statusElements, "elem_visDisOuTxt3=1") !== false) ? $row["txt_ou"]: ""; 
		$vis_dis_act_3  =(strpos($statusElements, "elem_visDisAct3=1") !== false) ? htmlentities($row["ex_desc"]): "";
	}
}

if(!empty($id_chart_vis_master)){
	//sca
	$sql = "SELECT * FROM chart_sca WHERE id_chart_vis_master='".$id_chart_vis_master."' ";
	$res = sqlStatement($sql);
	for($i=1;$row=sqlFetchArray($res);$i++){
		
		$sec_name = $row["sec_name"];
		if($sec_name == "AR"){
		$txt_vis_ar_od_s =(strpos($statusElements, "elem_visArOdS=1") !== false)? $row["s_od"]:"";
		$txt_vis_ar_od_c =(strpos($statusElements, "elem_visArOdC=1") !== false)? $row["c_od"]:"";
		$txt_vis_ar_od_a = (strpos($statusElements, "elem_visArOdA=1") !== false)?$row["a_od"]:"";
		$sel_vis_ar_od_sel_1 =(strpos($statusElements, "elem_visArOdS=1") !== false)? $row["sel_od"]:"";
		$txt_vis_ar_os_s =(strpos($statusElements, "elem_visArOsS=1") !== false)? $row["s_os"]:"";
		$txt_vis_ar_os_c = (strpos($statusElements, "elem_visArOsC=1") !== false)?$row["c_os"]:"";
		$txt_vis_ar_os_a = (strpos($statusElements, "elem_visArOsA=1") !== false)?$row["a_os"]:"";
		$sel_vis_ar_os_sel_1 = (strpos($statusElements, "elem_visArOsA=1") !== false)?$row["sel_os"]:"";
		}else if($sec_name == "ARC"){
		$visCycArOdS =(strpos($statusElements, "elem_visArOdS=1") !== false)? $row["s_od"]:"";
		$visCycArOdC =(strpos($statusElements, "elem_visArOdC=1") !== false)? $row["c_od"]:"";
		$visCycArOdA = (strpos($statusElements, "elem_visArOdA=1") !== false)?$row["a_od"]:"";
		$visCycArOdSel1 =(strpos($statusElements, "elem_visArOdS=1") !== false)? $row["sel_od"]:"";
		$visCycArOsS =(strpos($statusElements, "elem_visArOsS=1") !== false)? $row["s_os"]:"";
		$visCycArOsC = (strpos($statusElements, "elem_visArOsC=1") !== false)?$row["c_os"]:"";
		$visCycArOsA = (strpos($statusElements, "elem_visArOsA=1") !== false)?$row["a_os"]:"";
		$visCycArOsSel1 = (strpos($statusElements, "elem_visArOsA=1") !== false)?$row["sel_os"]:"";	
			
		}
	
	}
	
	//PC/MR
	$sql = "SELECT 
			c1.*,
			c2.sph as sph_r, c2.cyl as cyl_r, c2.axs as axs_r, c2.ad as ad_r, c2.prsm_p as prsm_p_r, c2.prism as prism_r, c2.slash as slash_r, c2.sel_1 as sel_1_r, c2.sel_2 as sel_2_r, 
			c2.txt_1 as txt_1_r, c2.txt_2 as txt_2_r, c2.sel2v as sel2v_r,
			c2.ovr_s as ovr_s_r, c2.ovr_c as ovr_c_r, c2.ovr_v as ovr_v_r, c2.ovr_a as ovr_a_r,			
			c3.sph as sph_l, c3.cyl as cyl_l, c3.axs as axs_l, c3.ad as ad_l, c3.prsm_p as prsm_p_l, c3.prism as prism_l, c3.slash as slash_l, c3.sel_1 as sel_1_l, c3.sel_2 as sel_2_l, 
			c3.txt_1 as txt_1_l, c3.txt_2 as txt_2_l, c3.sel2v as sel2v_l,
			c3.ovr_s as ovr_s_l, c3.ovr_c as ovr_c_l, c3.ovr_v as ovr_v_l, c3.ovr_a as ovr_a_l,
			c4.status_elements as vis_statusElements
			FROM chart_vis_master c4
			LEFT JOIN chart_pc_mr c1 ON c1.id_chart_vis_master = c4.id
			LEFT JOIN chart_pc_mr_values c2 ON c1.id = c2.chart_pc_mr_id AND c2.site='OD'
			LEFT JOIN chart_pc_mr_values c3 ON c1.id = c3.chart_pc_mr_id AND c3.site='OS'				
			WHERE c4.form_id='".$form_id."' AND c4.patient_id = '".$patient_id."' AND c1.ex_type='PC' AND c1.delete_by='0'  
			Order By ex_number;
			";
	$rez = sqlStatement($sql);
	for($i=0; $row= sqlFetchArray($rez); $i++){
		$ex_num = $row["ex_number"];
		
		if($ex_num == "1"){ 
			$ex_num = ""; 
			$indx1 = "";
		}else{
			$indx1 = "_".$ex_num;
		}
		
		//Pc---
		$statusElements = $row["vis_statusElements"];
		${"chk_pc_near".$indx1} = $row["pc_near"];

		${"sel_vis_pc_od_sel_1".$indx1} = (strpos($statusElements, "elem_visPcOdSel1".$ex_num."=1") !== false)?$row["sel_1_r"]:"";
		${"txt_vis_pc_od_s".$indx1} = (strpos($statusElements, "elem_visPcOdS".$ex_num."=1") !== false)?$row["sph_r"]:"";
		${"txt_vis_pc_od_c".$indx1} = (strpos($statusElements, "elem_visPcOdC".$ex_num."=1") !== false)?$row["cyl_r"]:"";
		${"txt_vis_pc_od_a".$indx1} =(strpos($statusElements, "elem_visPcOdA".$ex_num."=1") !== false)?$row["axs_r"]:"";

		${"sel_vis_pc_od_p".$indx1} =(strpos($statusElements, "elem_visPcOsP".$ex_num."=1") !== false)? $row["prsm_p_r"]:"";
		${"sel_vis_pc_od_prism".$indx1} =(strpos($statusElements, "elem_visPcOdPrism".$ex_num."=1") !== false)? $row["prism_r"]:"";
		${"sel_vis_pc_od_slash".$indx1} = (strpos($statusElements, "elem_visPcOdSlash".$ex_num."=1") !== false)?$row["slash_r"]:"";
		${"sel_vis_pc_od_sel_2".$indx1} = (strpos($statusElements, "elem_visPcOdSel2".$ex_num."=1") !== false)?$row["sel_2_r"]:"";

		${"sel_vis_pc_os_sel_1".$indx1} = (strpos($statusElements, "elem_visPcOsSel1".$ex_num."=1") !== false)?$row["sel_1_l"]:"";
		${"txt_vis_pc_os_s".$indx1} = (strpos($statusElements, "elem_visPcOsS".$ex_num."=1") !== false)?$row["sph_l"]:"";
		${"txt_vis_pc_os_c".$indx1} =(strpos($statusElements, "elem_visPcOsC".$ex_num."=1") !== false)? $row["cyl_l"]:"";
		${"txt_vis_pc_os_a".$indx1} = (strpos($statusElements, "elem_visPcOsA".$ex_num."=1") !== false)?$row["axs_l"]:"";
		${"sel_vis_pc_os_p".$indx1} = (strpos($statusElements, "elem_visPcOsP".$ex_num."=1") !== false)?$row["prsm_p_l"]:"";
		${"sel_vis_pc_os_prism".$indx1} = (strpos($statusElements, "elem_visPcOsPrism".$ex_num."=1") !== false)?$row["prism_l"]:"";
		${"sel_vis_pc_os_slash".$indx1} = (strpos($statusElements, "elem_visPcOsSlash".$ex_num."=1") !== false)?$row["slash_l"]:"";
		${"sel_vis_pc_os_sel_2".$indx1} =(strpos($statusElements, "elem_visPcOsSel2".$ex_num."=1") !== false)? $row["sel_2_l"]:"";
		
		${"txt_vis_pc_od_near_txt".$indx1} = $row["txt_1_r"];
		${"txt_vis_pc_os_near_txt".$indx1} = $row["txt_1_l"];											

		${"txt_vis_pc_od_overref_s".$indx1} = (strpos($statusElements, "elem_visPcOdOverrefS".$ex_num."=1") !== false)?$row["ovr_s_r"]:"";
		${"txt_vis_pc_od_overref_c".$indx1} = (strpos($statusElements, "elem_visPcOdOverrefC".$ex_num."=1") !== false)?$row["ovr_c_r"]:"";
		${"txt_vis_pc_od_overref_a".$indx1} = (strpos($statusElements, "elem_visPcOdOverrefA".$ex_num."=1") !== false)?$row["ovr_a_r"]:"";
		${"txt_vis_pc_od_overref_v".$indx1} = (strpos($statusElements, "elem_visPcOdOverrefV".$ex_num."=1") !== false)?$row["ovr_v_r"]:"";
		${"txt_vis_pc_os_overref_s".$indx1} = (strpos($statusElements, "elem_visPcOsOverrefS".$ex_num."=1") !== false)?$row["ovr_s_l"]:"";
		${"txt_vis_pc_os_overref_c".$indx1} = (strpos($statusElements, "elem_visPcOsOverrefC".$ex_num."=1") !== false)?$row["ovr_c_l"]:"";
		${"txt_vis_pc_os_overref_a".$indx1} = (strpos($statusElements, "elem_visPcOsOverrefA".$ex_num."=1") !== false)?$row["ovr_a_l"]:"";
		${"txt_vis_pc_os_overref_v".$indx1} = (strpos($statusElements, "elem_visPcOsOverrefV".$ex_num."=1") !== false)?$row["ovr_v_l"]:"";
		
		${"txt_vis_pc_desc".$ex_num}=$row["ex_desc"];//
		${"txt_vis_pc_od_add".$indx1} = (strpos($statusElements, "elem_visPcOdAdd".$ex_num."=1") !== false)?$row["ad_r"]:"";
		${"txt_vis_pc_os_add".$indx1} =(strpos($statusElements, "elem_visPcOsAdd".$ex_num."=1") !== false)? $row["ad_l"]:"";		
	}
	
	$sql = "SELECT 
		c1.*,
		c2.sph as sph_r, c2.cyl as cyl_r, c2.axs as axs_r, c2.ad as ad_r, c2.prsm_p as prsm_p_r, c2.prism as prism_r, c2.slash as slash_r, c2.sel_1 as sel_1_r, c2.sel_2 as sel_2_r, 
		c2.txt_1 as txt_1_r, c2.txt_2 as txt_2_r, c2.sel2v as sel2v_r,
					
		c3.sph as sph_l, c3.cyl as cyl_l, c3.axs as axs_l, c3.ad as ad_l, c3.prsm_p as prsm_p_l, c3.prism as prism_l, c3.slash as slash_l, c3.sel_1 as sel_1_l, c3.sel_2 as sel_2_l, 
		c3.txt_1 as txt_1_l, c3.txt_2 as txt_2_l, c3.sel2v as sel2v_l,
		
		c4.status_elements 
		FROM chart_vis_master c4  
		LEFT JOIN chart_pc_mr c1 ON c1.id_chart_vis_master = c4.id
		LEFT JOIN chart_pc_mr_values c2 ON c1.id = c2.chart_pc_mr_id AND c2.site='OD'
		LEFT JOIN chart_pc_mr_values c3 ON c1.id = c3.chart_pc_mr_id AND c3.site='OS'				
		WHERE c4.form_id='".$form_id."' AND c4.patient_id = '".$patient_id."' AND c1.ex_type='MR' AND c1.ex_number IN (1,2) AND c1.delete_by='0'  
		Order By ex_number;
		";
		
	$rez = sqlStatement($sql);		
	for($i=0; $row= sqlFetchArray($rez); $i++){
		$ret_str="";
		$ex_num = $row["ex_number"];
		
		$indx1=$indx2=$indx3="";
		if($ex_num>1){
			$indx1="Other";
			$indx3="_given";
			if($ex_num>2){
				$indx2="_".$ex_num;	
			}
		}
		
		$statusElements = $row["status_elements"];
		$rd_vis_mr_none_given=(strpos($statusElements, "elem_mrNoneGiven".$ex_num."=1")!== false)?$row["mr_none_given"] : "" ;
		$providerIdOther_3=$row["provider_id"];
		$vis_mr_desc_3COMMENTS=$row["ex_desc"];
		$vis_mr_prism3COMMENTS=$row["prism_desc"];
		
		${"txt_vis_mr_od".$indx3."_s"}=(strpos($statusElements, "elem_visMr".$indx1."OdS".$indx2."=1") !== false)?$row["sph_r"] : "" ;
		${"txt_vis_mr_od".$indx3."_c"}=(strpos($statusElements, "elem_visMr".$indx1."OdC".$indx2."=1") !== false)?$row["cyl_r"] : "" ;
		${"txt_vis_mr_od".$indx3."_a"}=(strpos($statusElements, "elem_visMr".$indx1."OdA".$indx2."=1") !== false)?$row["axs_r"] : "" ;
		${"txt_vis_mr_od".$indx3."_add"}=(strpos($statusElements, "elem_visMr".$indx1."OdAdd".$indx2."=1") !== false)?$row["ad_r"] : "" ;
		${"txt_vis_mr_od".$indx3."_txt_1"}=(strpos($statusElements, "elem_visMr".$indx1."OdTxt1".$indx2."=1") !== false)?$row["txt_1_r"] : "" ;			
		${"txt_vis_mr_od".$indx3."_txt_2"}=(strpos($statusElements, "elem_visMr".$indx1."OdTxt2".$indx2."=1") !== false)?$row["txt_2_r"] : "" ;
		
		
		
		${"sel_vis_mr_od".$indx3."_p"}=((strpos($statusElements, "elem_visMr".$indx1."OdP".$indx2."=1") !== false) || (strpos($statusElements, "elem_visMr".$indx1."OdSel1".$indx2."=1") !== false)
						|| (strpos($statusElements, "elem_visMr".$indx1."OdSlash".$indx2."=1") !== false) || (strpos($statusElements, "elem_visMr".$indx1."OdPrism".$indx2."=1") !== false)
						|| (strpos($statusElements, "elem_visMr".$indx1."OdTxt1".$indx2."=1") !== false)
						)?$row["prsm_p_r"] : "" ;
		${"sel_vis_mr_od".$indx3."_prism"}=((strpos($statusElements, "elem_visMr".$indx1."OdP".$indx2."=1") !== false) || (strpos($statusElements, "elem_visMr".$indx1."OdSel1".$indx2."=1") !== false)
							|| (strpos($statusElements, "elem_visMr".$indx1."OdSlash".$indx2."=1") !== false) || (strpos($statusElements, "elem_visMr".$indx1."OdPrism".$indx2."=1") !== false)
							)?$row["prism_r"] : "" ;
		${"sel_vis_mr_od".$indx3."_slash"}=((strpos($statusElements, "elem_visMr".$indx1."OdP".$indx2."=1") !== false) || (strpos($statusElements, "elem_visMr".$indx1."OdSel1".$indx2."=1") !== false)
							|| (strpos($statusElements, "elem_visMr".$indx1."OdSlash".$indx2."=1") !== false) || (strpos($statusElements, "elem_visMr".$indx1."OdPrism".$indx2."=1") !== false)
							)?$row["slash_r"] : "" ;
		${"sel_vis_mr_od".$indx3."_sel_1"}=((strpos($statusElements, "elem_visMr".$indx1."OdP".$indx2."=1") !== false) || (strpos($statusElements, "elem_visMr".$indx1."OdSel1".$indx2."=1") !== false)
							|| (strpos($statusElements, "elem_visMr".$indx1."OdSlash".$indx2."=1") !== false) || (strpos($statusElements, "elem_visMr".$indx1."OdPrism".$indx2."=1") !== false)
							)?$row["sel_1_r"] : "" ;
		${"sel_vis_mr_od".$indx3."_sel_2"}=(strpos($statusElements, "elem_visMr".$indx1."OdSel2".$indx2."=1") !== false)?$row["sel_2_r"] : "" ;
		//$visMrOtherOdSel2Vision_3=(strpos($statusElements, "elem_visMr".$indx1."OdSel2Vision".$indx2."=1") !== false)?$row["sel2v_r"] : "" ;
		
		${"txt_vis_mr_os".$indx3."_s"}=(strpos($statusElements, "elem_visMr".$indx1."OsS".$indx2."=1") !== false)?$row["sph_l"] : "" ;
		${"txt_vis_mr_os".$indx3."_c"}=(strpos($statusElements, "elem_visMr".$indx1."OsC".$indx2."=1") !== false)?$row["cyl_l"] : "" ;
		${"txt_vis_mr_os".$indx3."_a"}=(strpos($statusElements, "elem_visMr".$indx1."OsA".$indx2."=1") !== false)?$row["axs_l"] : "" ;
		
		${"txt_vis_mr_os".$indx3."_add"}=(strpos($statusElements, "elem_visMr".$indx1."OsAdd".$indx2."=1") !== false)?$row["ad_l"] : "" ;
		${"txt_vis_mr_os".$indx3."_txt_1"}=(strpos($statusElements, "elem_visMr".$indx1."OsTxt1".$indx2."=1") !== false)?$row["txt_1_l"] : "" ;
		${"txt_vis_mr_os".$indx3."_txt_2"}=(strpos($statusElements, "elem_visMr".$indx1."OsTxt2".$indx2."=1") !== false)?$row["txt_2_l"] : "" ;			
		
		${"sel_vis_mr_os".$indx3."_p"}=((strpos($statusElements, "elem_visMr".$indx1."OsP".$indx2."=1") !== false) || (strpos($statusElements, "elem_visMr".$indx1."OsSel1".$indx2."=1") !== false)
							|| (strpos($statusElements, "elem_visMr".$indx1."OsSlash".$indx2."=1") !== false) || (strpos($statusElements, "elem_visMr".$indx1."OsPrism".$indx2."=1") !== false)
						)?$row["prsm_p_l"] : "" ;
		${"sel_vis_mr_os".$indx3."_prism"}=((strpos($statusElements, "elem_visMr".$indx1."OsP".$indx2."=1") !== false) || (strpos($statusElements, "elem_visMr".$indx1."OsSel1".$indx2."=1") !== false)
							|| (strpos($statusElements, "elem_visMr".$indx1."OsSlash".$indx2."=1") !== false) || (strpos($statusElements, "elem_visMr".$indx1."OsPrism".$indx2."=1") !== false)
						)?$row["prism_l"] : "" ;
		${"sel_vis_mr_os".$indx3."_slash"}=((strpos($statusElements, "elem_visMr".$indx1."OsP".$indx2."=1") !== false) || (strpos($statusElements, "elem_visMr".$indx1."OsSel1".$indx2."=1") !== false)
							|| (strpos($statusElements, "elem_visMr".$indx1."OsSlash".$indx2."=1") !== false) || (strpos($statusElements, "elem_visMr".$indx1."OsPrism".$indx2."=1") !== false)
						)?$row["slash_l"] : "" ;
		${"sel_vis_mr_os".$indx3."_sel_1"}=((strpos($statusElements, "elem_visMr".$indx1."OsP".$indx2."=1") !== false) || (strpos($statusElements, "elem_visMr".$indx1."OsSel1".$indx2."=1") !== false)
							|| (strpos($statusElements, "elem_visMr".$indx1."OsSlash".$indx2."=1") !== false) || (strpos($statusElements, "elem_visMr".$indx1."OsPrism".$indx2."=1") !== false)
							)?$row["sel_1_l"] : "" ;
		${"sel_vis_mr_os".$indx3."_sel_2"}=(strpos($statusElements, "elem_visMr".$indx1."OsSel2".$indx2."=1") !== false)?$row["sel_2_l"] : "" ;
		//$visMrOtherOsSel2Vision_3=(strpos($statusElements, "elem_visMr".$indx1."OsSel2Vision".$indx2."=1") !== false)?$row["sel2v_l"] : "" ;		
		//$elem_mr_type3 = (strpos($statusElements, "elem_mr_type".$ex_num."=1") !== false && !empty($row["vis_mr_type1"])) ?$row["mr_type"] : "" ;
		//if(!empty($elem_mr_type3)){ $elem_mr_type3 = "(".ucfirst($elem_mr_type3).")"; } 		
	}
}

//--

//End Chart Vision
//Chart Left Provider	
$qry = imw_query("select * from chart_left_provider_issue where patient_id = '$pid' and form_id = '$form_id'");
//Update Records
$prow = imw_fetch_array($qry);
$idChartLeftProviderIssue = $prow["pr_is_id"];
//End Chart Left Provider	

//* End of Code to Show Ocular Medication data/*
/* 

// Chart Optic
$sql_optic = imw_query("SELECT * FROM chart_optic WHERE patient_id = '$patient_id' AND form_id = '$form_id'");
$row_optic = array();					
while($res = imw_fetch_assoc($sql_optic)){	
	$row_optic = $res;
}
if($row_optic != false && count($row_optic)>0){
	//Set Default When Old Record	
	$op_mode = "update";
	$op_edid = $row_optic["optic_id"];
	$idOptic = $row_optic["optic_id"];
	$txt_od_text = $row_optic["od_text"];
	$txt_os_text = $row_optic["os_text"];			
	$hd_examined_no_change = $row_optic["examined_no_change"];
	$opticNerveOd = $row_optic["optic_nerve_od"];
	$opticNerveOs = $row_optic["optic_nerve_os"];
	$rvopticod_summaryLabel = $row_optic["optic_nerve_od_summary"]; //($row["optic_nerve_od_summary"] != "") ? $row["optic_nerve_od_summary"] : "WNL";
	$rvopticos_summaryLabel = $row_optic["optic_nerve_os_summary"]; //($row["optic_nerve_os_summary"] != "") ? $row["optic_nerve_os_summary"] : "WNL";					
	$todate = formatDate4display($row_optic["exam_date"]);
	$optic_not_applicable = $row_optic["not_applicable"];	
}
//End Chart Optic
/*end of Code to show chart_optic data*/

// IF PRINT THEN FORM ID

$qry1 = imw_query("select *, date_format(date_of_service,'%m-%d-%Y') as dateOfService from  chart_master_table 
		where patient_id = '$pid' and id = '$form_id'");
$crow = imw_fetch_array($qry1);
$date_of_service = $crow["dateOfService"];
/////End date of sevice Code////////////////
ob_start();
//***88End Code To show pupil Tab Data**/
//HeadingTableHr()
?>
<page backtop="5mm" backbottom="5mm">
<page_footer>
<table style="width: 100%;">
	<tr>
		<td style="text-align:center;width:100%" class="text_value">Page [[page_cu]]/[[page_nb]]</td>
	</tr>
</table>
</page_footer>		
<page_header>

<table style="width:100%;" border="0" cellspacing="0"  cellpadding="0">
				<tr>
					<td style="width:40%" class="tb_headingHeader"><?php if(!empty($patientName)){ print $patientDetails[0]['title'].' '.$patientName."-".$patientDetails[0]['id']; }?> </td>
				
					<td style="width:30%" class="tb_headingHeader"><?php print $patientDetails[0]['sex'];if(@!in_array("HIPPA",$_REQUEST["chart_nopro"])){ print("&nbsp;($age)&nbsp;".$date_of_birth); }?>&nbsp; </td>
				    <td style="width:30%; text-align:right" class="tb_headingHeader"><?php if($date_of_service){ print 'Date of Service:&nbsp;'.$date_of_service."";} else '&nbsp;'; ?> </td>
				</tr>
			</table>
</page_header>					
<style>
.text_b_w{
		font-size:12px;
		font-family:Arial, Helvetica, sans-serif;
		font-weight:bold;
}
.paddingLeft{
	padding-left:5px;
}
.paddingTop{
	padding-top:5px;
}
.tb_subheading{
	font-size:12px;
	font-family:Arial, Helvetica, sans-serif;
	font-weight:bold;
	color:#000000;
	background-color:#f3f3f3;;
}
.tb_heading{
	font-size:11px;
	font-family:Arial, Helvetica, sans-serif;
	font-weight:bold;
	color:#FFFFFF;
	background-color:#999999;
	margin-top:10px;
}
.tb_headingHeader{
	font-size:12px;
	font-family:Arial, Helvetica, sans-serif;
	font-weight:bold;
	color:#FFFFFF;
	background-color:#4684ab;
}
.text_lable{
		font-size:12px;
		font-family:Arial, Helvetica, sans-serif;
		background-color:#FFFFFF;
		font-weight:bold;
}
.text_value{
		font-size:12px;
		font-family:Arial, Helvetica, sans-serif;
		font-weight:100;
		background-color:#FFFFFF;
	}
.text_blue{
		font-size:12px;
		font-family:Arial, Helvetica, sans-serif;
		color:#0000CC;
	font-weight:bold;
	}
.text_green{
		font-size:12px;
		font-family:Arial, Helvetica, sans-serif;
		color:#006600;
		font-weight:bold;
}
.imgCon{width:325px;height:auto;}
</style>
<table style="width:100%;" cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td class="text_b_w" style="width:30%;" align="left"><?php echo($reportName);?></td>
		<td class="text_b_w" style="width:1%;"></td>
		<td class="text_b_w" style="width:69%;" align="right">Printed by:&nbsp;<?php echo($opertator_name);?>&nbsp;on&nbsp;<?php echo(date("m-d-Y H:i:s"));?></td>

	</tr>
	<tr>
		<td class="text_b_w" style="width:100%;" colspan="3"><hr/></td>
	</tr>
</table>
<!--- New Header Appplied--->
<table style="width:100%;" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td style="width:40%" align="left"> 
<table style="width:100%;" border="0" cellspacing="0"  cellpadding="0">
				<tr>
					<td style="width:100%" class="text_lable"><?php if(!empty($patientName)){ print $patientDetails[0]['title'].' '.$patientName."-".$patientDetails[0]['id']; }?> </td>
				</tr>
				<tr>
					<td style="width:100%" class="text_value"><?php print $patientDetails[0]['sex'];if(@!in_array("HIPPA",$_REQUEST["chart_nopro"])){ print("&nbsp;($age)&nbsp;".$date_of_birth); }?>&nbsp; </td>
				</tr>
				<tr>
					<td style="width:100%" class="text_value"> <?php if(@!in_array("HIPPA",$_REQUEST["chart_nopro"])){ print $patientDetails[0]['street']."&nbsp;"; }?>&nbsp;</td>
				</tr>
				<tr>
					<td style="width:100%" class="text_value"><?php if(@!in_array("HIPPA",$_REQUEST["chart_nopro"])){ print $patientDetails[0]['street2']; }?>&nbsp; </td>
				</tr>
				<tr>
					<td style="width:100%" class="text_value"><?php if(@!in_array("HIPPA",$_REQUEST["chart_nopro"])){ print ($patientDetails[0]['city']!="")?$patientDetails[0]['city'].",":""; print("&nbsp;".$patientDetails[0]['state']."&nbsp;".$patientDetails[0]['postal_code']); }?>&nbsp; </td>
				</tr>
				<tr>
					<td style="width:100%" class="text_lable"><?php if($date_of_service){ print 'Date of Service:&nbsp;'.$date_of_service."";} else '&nbsp;'; ?> </td>
				</tr>
			</table>
		</td>
	<?php if(!empty($patientImage)){ ?>
			<td style="width:20%"  valign="top">
				<table style="width:100%;" border="0" cellspacing="0" rules="none" cellpadding="0">
					 <tr>
						<td align="center"><?php print $patientImage; ?></td>
					</tr>
				</table> 
			</td>
	<?php }else{ echo('<td style="width:20%"  valign="top">&nbsp;</td>');} ?>
			<td style="width:40%" align="right">
			<table style="width:100%;" border="0" cellspacing="0"  cellpadding="0">
				<tr>
					<td style="width:100%" class="text_lable"><?php print $groupDetails[0]['name']; ?> </td>
				</tr>
				<tr>
					<td style="width:100%" class="text_value"><?php print ucwords($groupDetails[0]['group_Address1']); ?></td>
				</tr>
				<tr>
					<td style="width:100%" class="text_value"><?php print ucwords($groupDetails[0]['group_Address2']);?>&nbsp;</td>
				</tr>
				<tr>
					<td style="width:100%" class="text_value"><?php print $groupDetails[0]['group_City'].', '.$groupDetails[0]['group_State'].' '.$groupDetails[0]['group_Zip']; ?>  </td>
				</tr>
				<tr>
					<td style="width:100%" class="text_value">Ph.:&nbsp;<?php print $groupDetails[0]['group_Telephone']; ?> </td>
				</tr>
				<tr>
					<td style="width:100%" class="text_value">Fax:&nbsp;<?php print $groupDetails[0]['group_Fax']; ?> </td>
				</tr>
			</table>
		</td>
	</tr>
</table>
<!--- End New Header Appplied--->
<?php


//if(@in_array("Record Release",$_REQUEST["chart_nopro"]) && ($_REQUEST["DisclosedBy"]!="" && $_REQUEST["DisclosedTo"]!="" && $_REQUEST["Specialty"]!="")) {	
if(@in_array("Record Release",$_REQUEST["chart_nopro"])) {
//$date_of_service//
$insertQuery = "insert into `disclosed_details` set patient_id = '".$patient_id."', 
		disclosedBy = '".addslashes($_REQUEST["DisclosedBy"])."',
		disclosedTo = '".addslashes($_REQUEST["DisclosedTo"])."',     
		disclosedToSpecialty = '".addslashes($_REQUEST["Specialty"])."', 
		disclosedReason = '".addslashes($_REQUEST["Reason"])."',
		dateRequested = now(),operator_id = '".$_SESSION['authId']."', savedDateTime = now()";
$insertidTemp = @imw_query($insertQuery);        
$boolDisclose = false;
$selDisclosedQ = "select DATE_FORMAT(dateRequested,'%m/%d/%y') as dateRequestedTEMP,disclosedBy,disclosedTo,
		disclosedToSpecialty,disclosedReason from `disclosed_details` where patient_id = '".$patient_id."' 
		order by dateRequested desc ";
$resultDisclosed = imw_query($selDisclosedQ);
if(imw_num_rows($resultDisclosed)>0 && $boolDisclose==true){
?>
<table style="width:100%;"  border="0" class="paddingTop"  cellspacing="0" rules="none" cellpadding="0">
	<tr>
		<td style="width: 100%;" colspan="5" class="tb_heading">Disclosed Details:</td> 
	</tr>		
	<tr>
        <td class="text_lable" style="width:20%;">Date Requested</td>
        <td class="text_lable" style="width:20%;">Disclosed By</td> 
        <td class="text_lable" style="width:20%;">Disclosed To</td>
        <td class="text_lable" style="width:20%;">Specialty </td>
        <td class="text_lable" style="width:20%;">Reason</td>
    </tr>
	<?php 
	while($rowDisclosed=imw_fetch_array($resultDisclosed)){?>
		<tr>
			<td class="text_value"><?php echo($rowDisclosed["dateRequestedTEMP"]);?></td>
			<td class="text_value">
				<?php
				if($rowDisclosed["disclosedBy"]!=""){   
					$t_provider = "select id,fname,lname,mname from users where id = '".$rowDisclosed["disclosedBy"]."'";							
					$sqlt_provider = imw_query($t_provider);	
					while($vrst_provider = imw_fetch_array($sqlt_provider)){
						$phyName_drop="";
						$phyName_drop = $vrst_provider['fname'];
						if($vrst_provider['fname'] != '' && $vrst_provider['lname'] != ''){
							$phyName_drop = $vrst_provider['lname'].', '.$vrst_provider['fname'];
						}
						else if($vrs['fname'] == '' && $vrst_provider['lname'] != ''){
							$phyName_drop = $vrst_provider['lname'];
						}
						$phyName_drop .= $vrst_provider['mname'];									
						echo trim(ucwords($phyName_drop));	
					}
				}
				?>
			</td> 
			<td class="text_value"><?php echo($rowDisclosed["disclosedTo"]);?></td>
			<td class="text_value"><?php echo($rowDisclosed["disclosedToSpecialty"]);?></td>
			<td class="text_value"><?php echo($rowDisclosed["disclosedReason"]);?></td>
		</tr>
		<?php
		}
		?>
</table>
	<?php
	}
}
//-- Code Ro Add Allergies,Medication,Surgries --

//--- GET ALL PROBLEM LIST ---
if($_REQUEST["problem_testActive"] == "Active"){
	$getProblemList = "select *, date_format(onset_date,'%m/%d/%y') as DateDiagnosed from pt_problem_list 
			where pt_id = '".$patient_id."' and status = 'Active' order by id";
}else{
	$getProblemList = "select *, date_format(onset_date,'%m/%d/%y') as DateDiagnosed from pt_problem_list 
			where pt_id = '".$patient_id."' order by id";
}
$rsGetProblemList = imw_query($getProblemList);

if (imw_num_rows($rsGetProblemList) > 0 && (@in_array("Problem List",$_REQUEST["chart_nopro"]) || @in_array("Medical History",$_REQUEST["chart_nopro"]))) {	
?>
<table style="width: 100%;"  class="paddingTop" cellpadding="2" cellspacing="0">	
	<tr>
		<td style="width: 100%;" colspan="3" class="tb_heading">Problem list:&nbsp;</td>				
	</tr>
	<tr>
		<td class="text_lable" nowrap valign="top">Patient Problem - Dx Code</td>
		<td class="text_lable" nowrap valign="top">Status</td>
		<td class="text_lable" nowrap valign="top">Date Diagnosed</td>
	</tr>
	<?php 	
	while($rowProblemList = imw_fetch_assoc($rsGetProblemList)){
	?>
		<tr>
			<td class="text_value" valign="top">
				<?php echo $rowProblemList["problem_name"]; ?>&nbsp;
			</td>			
			<td class="text_value" valign="top">
				<?php echo ($rowProblemList["status"]); ?>&nbsp;
			</td>
			<td class="text_value" valign="top">
				<?php if($row["DateDiagnosed"]!="00/00/00"){echo($rowProblemList["DateDiagnosed"]);} ?>&nbsp;
			</td>
		</tr>				
		<?php								
	}
	?>
</table>
<?php
}

//--- GET ALL ALLERGIES DATA ----
if($_REQUEST["allergies_testActive"]=="Active"){
	$getAllergies = "select type,title,begdate,acute,allergy_status,chronic,reactions,ag_occular_drug,comments,
			date_format(begdate,'%m/%d/%y') as DateStart from lists where pid = '".$patient_id."' 
			and type in(3,7) and allergy_status = 'Active' order by id";
}else{
	$getAllergies = "select type,title,begdate,acute,allergy_status,chronic,reactions,ag_occular_drug,comments,
			date_format(begdate,'%m/%d/%y') as DateStart from lists where pid = '".$patient_id."' 
			and type in(3,7) and allergy_status != 'Deleted' order by id";
}
$rsGetAllergies = imw_query($getAllergies);

if (imw_num_rows($rsGetAllergies) > 0 && (@in_array("Allergies List",$_REQUEST["chart_nopro"]) || @in_array("Medical History",$_REQUEST["chart_nopro"]))) {	
?>
<table style="width: 100%;" class="paddingTop" cellpadding="2" cellspacing="0">	
	<tr>
		<td style="width: 100%;" colspan="7" class="tb_heading">Allergies:&nbsp;</td>				
	</tr>
	<tr>
		<td class="text_lable" nowrap valign="top">Type</td>
		<td class="text_lable" nowrap valign="top">Name</td>		
		<td class="text_lable" nowrap valign="top">Adverse Reactions</td>
		<td class="text_lable" nowrap valign="top">Comments</td>
		<td class="text_lable" nowrap valign="top">Date Started</td>		
		<td class="text_lable" nowrap valign="top">Status</td>		
	</tr>
<?php
while($row = imw_fetch_assoc($rsGetAllergies)){
	$typeAllergy="";
	if($row["ag_occular_drug"] == 'fdbATDrugName'){
			$typeAllergy = 'Drug';
		}
		if($row["ag_occular_drug"] == 'fdbATIngredient'){
			$typeAllergy = 'Ingredient';
		}
		if($row["ag_occular_drug"] == 'fdbATAllergenGroup'){
			$typeAllergy = 'Allergen';
		}
	?>
		<tr>
			<td class="text_value" valign="top"><?php echo $typeAllergy; ?>&nbsp;</td>			
			<td class="text_value" valign="top"><?php echo $row["title"]; ?>&nbsp;</td>
			<td class="text_value" valign="top"><?php echo ucwords($row["reactions"]); ?>&nbsp;</td>	
			<td class="text_value" valign="top"><?php echo ucwords($row["comments"]); ?>&nbsp;</td>	
			<td class="text_value" valign="top">
				<?php if($row["DateStart"]!="00/00/00"){echo $row["DateStart"];} ?>&nbsp;
			</td>
			<td class="text_value" valign="top"><?php print $row["allergy_status"];?>&nbsp;</td>	
		</tr>
	<?php								
	}
	?>
</table>
<?php
}

//--- GET ocular SX / PROCEDURES ---
$getSurgeries = "select type,title,begdate,referredby,comments,date_format(begdate,'%m/%d/%y') as surgerydate 
		from lists where pid = '".$patient_id."' and type in (6) and allergy_status != 'Deleted' order by id";
$rsGetSurgeries = imw_query($getSurgeries);

if(imw_num_rows($rsGetSurgeries) > 0 && (@in_array("Surgeries Procedure",$_REQUEST["chart_nopro"]))) {	
?>
<table style="width: 100%;" class="paddingTop" cellpadding="2" cellspacing="0">	
	<tr>
		<td style="width: 100%;" colspan="5" class="tb_heading">Ocular Surgeries/Procedure:</td>				
	</tr>
	<tr>
		<td valign="top" nowrap class="text_lable">Ocular</td>
		<td valign="top" nowrap class="text_lable">Name</td>
		<td valign="top" nowrap class="text_lable">Date of Surgery</td>		
		<td valign="top" nowrap class="text_lable">Physician</td>
		<td  valign="top" nowrap class="text_lable">Comments</td>		
	</tr>
	<?php 		
	while($row = imw_fetch_assoc($rsGetSurgeries)){
	?>
		<tr>
        	<td class="text_value" valign="top">
            	<?php print $row["type"] == "6" ? 'Yes' : ''; ?>
            </td>
            <td class="text_value" valign="top">
                <?php echo $row["title"]; ?>&nbsp;
            </td>
            <td class="text_value" valign="top">
                <?php if($row["surgerydate"]!="00/00/00"){ echo ($row["surgerydate"]);} ?>&nbsp;
            </td>
            <td class="text_value" valign="top">
                <?php echo ucwords($row["referredby"]); ?>&nbsp;
            </td>
            <td width="6%" valign="top" class="text_value">
                <?php echo ucwords($row["comments"]); ?>&nbsp;
            </td>			
        </tr>				
		<?php								
	}
	?>
</table>
<?php
}

//--- GET OCULAR MEDICATION DATA ---

	$getMedication = "select type,title,sig,qty,allergy_status,referredby,destination,begdate,enddate,comments,
			date_format(begdate,'%m/%d/%y') as DateStart from lists where pid='".$patient_id."' and allergy_status = 'Active' 
			and type in (4) and allergy_status != 'Deleted' order by id";
	$rsGetMedication = imw_query($getMedication);

if(imw_num_rows($rsGetMedication) > 0 && (@in_array("Medication List",$_REQUEST["chart_nopro"])|| @in_array("Medical History",$_REQUEST["chart_nopro"]))){
?>
<table style="width: 100%;"  class="paddingTop" cellpadding="2" cellspacing="0">	
	<tr>
		<td style="width:100%;" colspan="6" class="tb_heading">Ocular Medication List:&nbsp;</td>				
	</tr>	
	<tr>
		<td class="text_lable" nowrap valign="top">Name</td>
		<td class="text_lable" nowrap valign="top">Dosage</td>
		<td class="text_lable" nowrap valign="top">Sig</td>
		<td class="text_lable" nowrap valign="top">Qty</td>
		<td class="text_lable" nowrap valign="top">Date Started</td>
		<td class="text_lable" nowrap valign="top">Status</td>		
	</tr>
	<?php 	
	while($row = imw_fetch_assoc($rsGetMedication)){
	?>
		<tr>		
			<td class="text_value" valign="top"><?php echo $row["title"]; ?>&nbsp;</td>			
			<td class="text_value" valign="top"><?php echo $row["destination"]; ?>&nbsp;</td>			
			<td class="text_value" valign="top"><?php echo $row["sig"]; ?>&nbsp;</td>			
			<td class="text_value" valign="top"><?php echo $row["qty"]; ?>&nbsp;</td>			
			<td class="text_value" valign="top">
				<?php if($row["DateStart"]!="00/00/00"){echo $row["DateStart"];} ?>&nbsp;
			</td>			
			<td class="text_value" valign="top"><?php echo $row["allergy_status"]; ?>&nbsp;</td>
		</tr>				
	<?php								
	}
	?>
	
</table>
<?php
}
if (@in_array("Medical History",$_REQUEST["chart_nopro"])) {
	//include(getcwd()."/../main/labresults_print.php");	
}

if (@in_array("Medical History",$_REQUEST["chart_nopro"])) {
	//include(getcwd()."/../main/radiology_print.php");	
}
//Include general Health,Immunization and Ocular Medical history//
//include(getcwd()."/../main/immunization_ocular_print.php");
//Include general Health,Immunization and Ocular Medical history//

// CC History//

if($crow["reason"]!=""){
	//HeadingTableHr();
	HeadingTable($titleName="CC & History:");
	SingleTdData($data=nl2br($crow["reason"]));
	HeadingTableHr();
}
if(!empty($crow["neuroPsych"])){
		$data="<span class='text_lable'>Neuro/Psych:&nbsp;</span>".$crow["neuroPsych"];
		SingleTdData($data);
}

$ocularHistory = ""; 
$ocularHistory .= ($prow["ocularhx_lens"]==1) ? "CL, " : ""; 
$ocularHistory .= ($prow["ocularhx_glaucoma"]==1) ? "Glaucoma, " : ""; 
$ocularHistory .= ($prow["ocularhx_sx"]==1) ? "Sx/Laser, " : "";
$ocularHistory .= ($prow["ocularhx_glasses"]==1) ? "Glasses, " : "";
$ocularHistory .= ($prow["ocularhx_fhx_ret"]==1) ? "R.Detach, " : "";
$ocularHistory .= ($prow["ocularhx_fhx_mac"]==1) ? "Mac. deg., " : "";
$ocularHistory .= ($prow["ocularhx_fhx_cat"]==1) ? "Cataracts, " : "";
$ocularHistory .= ($prow["ocularhx_fhx_bli"]==1) ? "Blindness, " : "";
$ocularHistory .= ($prow["ocularhx_other"]<>"") ? $prow["ocularhx_other"]."," : "" ;					  	
$ocularHistory = trim(strip_tags($ocularHistory)); 
$rvs = "";
$rvs .= ($prow["rvs_blurred"]==1) ? "Blurred, " : "";
$rvs .= ($prow["rvs_poor_night"]==1) ? "Poor night vision, " : "";
$rvs .= ($prow["rvs_poor_dept"]==1) ? "Poor depth, " : "";
$rvs .= ($prow["rvs_pglare"]==1) ? "Glare/Halos, " : "";
$rvs .= ($prow["rvs_tear"]==1) ? "Tearing/Dry eyes, " : "";
$rvs .= ($prow["rvs_diplopia"]==1) ? "Diplopia, " : "";
$rvs .= ($prow["rvs_spot"]==1) ? "Spots/Floaters, " : "";
$rvs .= ($prow["rvs_itiching"]==1) ? "Itching/Burning, " : "";
$rvs .= ($prow["rvs_red"]==1) ? "Red eyes, " : "";
$rvs .= ($prow["rvs_other"]<>"") ? $prow["rvs_other"]."," : ""; 
$rvs = trim(strip_tags($rvs));
$medicalHistory = "";
$medicalHistory = ($prow["medicalhx_id"]==1) ? "DM, " : "";
$medicalHistory .= ($prow["medicalhx_id"]==2) ? "IDDM, " : "";
$medicalHistory .= ($prow["medicalhx_id"]==3) ? "NIDDM, " : "";
$medicalHistory .= ($prow["medicalhx_htn"]==1) ? "HTN, " : ""; 
$medicalHistory .= ($prow["medicalhx_hear"]==1) ? "Heart, " : "";  
$medicalHistory .= ($prow["medicalhx_lungs"]==1) ? "Lungs, " : "";  
$medicalHistory .= ($prow["medicalhx_neuro"]==1) ? "Neuro, " : "";
$medicalHistory .= ($prow["medicalhx_other"]<>"") ? $prow["medicalhx_other"]."," : ""; 
$medicalHistory = trim( strip_tags($medicalHistory));
if($ocularHistory != ''){
	//HeadingTableHr();
	HeadingTable($titleName="Ocular Hx:&nbsp;");
	SingleTdData(substr($ocularHistory,0,strlen($ocularHistory)-1));
	HeadingTableHr();
	if($rvs<>""){
		DoubleTdData($lable="Review Visual:&nbsp;",substr($rvs,0,strlen($rvs)-1));
	}
	if($medicalHistory<>""){
		DoubleTdData($lable="Medical Hx:&nbsp;",substr($medicalHistory,0,strlen($medicalHistory)-1));
	}
	HeadingTableHr();
}

// End of ocularHistory//
// SET NOTES 


if(is_array($_REQUEST["chart_nopro"])){
	if(@in_array("Include Provider Notes",$_REQUEST["chart_nopro"])){	
	$tmpOcuDx = getShowTitle( $pid, "Ocular Dx.", "Diagnosis" );
	$tmpOcuSx = getShowTitle( $pid, "Ocular Sx.", "OcularSx" );
	$tmpCon = getShowTitle( $pid, "Consult", "Consult" );
	$tmpMedDx = getShowTitle( $pid, "Med Dx.", "Medical Dx." );
	}
}
if($tmpOcuDx["pnotes"]<>"" || $tmpOcuSx["pnotes"]<>"" || $tmpMedDx["pnotes"]<>"" || $tmpCon["pnotes"]<>""){
	?>
    <table style="width:100%;" class="paddingTop" border="0" cellspacing="0" cellpadding="0">
        <tr >
            <td valign="middle" class="tb_heading" style="width:100%;">Provider Notes View:&nbsp;</td>
        </tr>
    </table>	
	<?php
}
if($tmpMedDx["pnotes"]<>""){
	?>
    <table cellpadding="0" cellspacing="0" border="0" style="width:100%;">
        <tr>
            <td style='width:100%;' class='tb_subheading' ><?php  echo($tmpMedDx["title"]);?>.</td>
        </tr>
        <?php 
			echo " <tr>";
			echo "  <td align='left' style='width:100%;' class='text_value'>\n";
			echo  addslashes($tmpMedDx["pnotes"]) ;
			echo "  </td>";
			echo " </tr>";
        ?>
	</table>
	<?php
}
//$qry=imw_query("select * from pnotes where pid='$pid' and (title='Ocular Dx.' and form_id=$form_id)");
if($tmpOcuDx["pnotes"]<>""){
	?>
    <table cellpadding="0" cellspacing="0" width="100%"  border="0">
        <tr>
            <td style='width:100%;' class='tb_subheading'><?php  echo($tmpOcuDx["title"]);?>.</td>
        </tr>
		<?php 
			echo "<tr>";
			echo "<td style='width:100%;' class='text_value'>\n";
			echo  addslashes($tmpOcuDx["pnotes"]);
			echo "</td>";
			echo "</tr>";            
        ?>
    </table>		
	<?php
	HeadingTableHr();
}
// ocular sx
//$qry = imw_query("select * from pnotes where pid='$pid' and (title='Ocular Sx.' and form_id=$form_id)");
if($tmpOcuSx["pnotes"]<>""){
	?>
    <table cellpadding="0" cellspacing="0" width="100%"  border="0">
        <tr>
            <td style='width:100%;' class='tb_subheading' ><?php  echo($tmpOcuSx["title"]);?></td>
        </tr>
		<?php 
            echo "<tr align='left'>";
            echo "<td align='left'  style='width:100%;' class='text_value'>\n";
            echo  addslashes($tmpOcuSx["pnotes"]) ;
            echo "</td>";            
            echo " </tr>";
        ?>
    </table>
	<?php
	HeadingTableHr();
}
// consult
//$qry = imw_query("select * from pnotes where pid='$pid' and (title='Consult' and form_id=$form_id)");
if($tmpCon["pnotes"]<>""){
	?>
    <table cellpadding="0" cellspacing="0" style="width:100%;" class="paddingTop"  border="0">
        <tr>
            <td style='width:100%;' class='tb_subheading' ><?php  echo($tmpCon["title"]);?></td>
        </tr>
        <?php 
            echo "<tr align='left'>";
            echo "<td align='left' style='width:100%;' class='text_value'>\n";
            echo  addslashes($tmpCon["pnotes"]);
            echo "</td>";
            echo "</tr>";
        ?>
    </table>
	<?php
	HeadingTableHr();
}
//-- End Of Provider Notes--

// VISION START//
if(($sel_vis_dis_od_sel_1!="" && $txt_vis_dis_od_txt_1!="20/" && $txt_vis_dis_od_txt_1!="") && ($sel_vis_dis_os_sel_1!="" && $txt_vis_dis_os_txt_1!="20/" && $txt_vis_dis_os_txt_1!="")){ 
	$show_vis_dist_val=true;
	$visdateCheckpass=true;
	}	
if(($chk_vis_near == 1)&&($txt_vis_near_od_txt_1!="" && $txt_vis_near_od_txt_1!="20/")){ 
	$show_vis_near_val=true;
	$visNeardateCheckpass=true;
	}
	if($date_of_service!="" && $txt_vis_near_examdate!="" && $txt_vis_near_examdate!=0){												
		$start2 =FormatDate_insert($date_of_service);
		$end2 =$txt_vis_near_examdate;
		if($start2==$end2){
			$visNeardateCheckpass=true;
			$visdateCheckpass=true;
		}
		if($diff=@get_time_difference($start2, $end2)){
			$visNeardateCheckpass=true;
			$visdateCheckpass=true;
			
		}
	}
	/* End Code To Compare VISExamDate WITH DOS*/ 	
	$show_vis_BAT_val=false;
	/* Code To Compare VISBATExamDate WITH DOS*/
	$visCRdateCheckpass=false;
	if($date_of_service!="" && $txt_vis_bat_examdate!="" && $txt_vis_bat_examdate!=0){
		$start3 =FormatDate_insert($date_of_service);
		$end3 =$txt_vis_bat_examdate;
		if($start3==$end3){
			$visCRdateCheckpass=true;
		}
		if($diff=@get_time_difference($start3, $end3)){
			$visCRdateCheckpass=true;
			// echo "$start3 You  $end3 have $txt_vis_near_examdate  logged in after <br>".$diff['days']." Days ".$diff['hours']."Hours ".$diff['minutes']."Minutes <br>" ;
		}
	}
	//echo($txt_vis_dis_examdate."d".$txt_vis_near_examdate);
	//
if($visNeardateCheckpass==true || $visdateCheckpass==true){
HeadingTableHr();
HeadingTable($titleName="Vision:");
HeadingTableHr();
}
// End of VISION//
?>
<table border="0" cellpadding="0" cellspacing="0" style="width:100%;">

<?php
if($date_of_service!="" || $txt_vis_near_examdate!="" || $txt_vis_near_examdate!=0){												
		$start2 = FormatDate_insert($date_of_service);
		$end2 = $txt_vis_near_examdate;
		if($start2 == $end2){
			$visNeardateCheckpass=true;
			$visdateCheckpass=true;
		}
		if($diff = @get_time_difference($start2, $end2)){
			$visNeardateCheckpass=true;
			$visdateCheckpass=true;
			
		}
	}
	
	/* End Code To Compare VISExamDate WITH DOS*/ 	
	$show_vis_BAT_val=false;
	/* Code To Compare VISBATExamDate WITH DOS*/
	$visCRdateCheckpass=false;
	if($date_of_service!="" || $txt_vis_bat_examdate!="" || $txt_vis_bat_examdate!=0){
		$start3 =FormatDate_insert($date_of_service);
		$end3 =$txt_vis_bat_examdate;
		if($start3==$end3){
			$visCRdateCheckpass=true;
		}
		if($diff=get_time_difference($start3, $end3)){
			$visCRdateCheckpass=true;			
		}
	}
	
	/* End Code To Compare VISBATExamDate WITH DOS*/ 	
	$vis_arCheckpass=false;
	if($date_of_service!="" || $txt_vis_ar_ak_examdate!="" || $txt_vis_ar_ak_examdate!=0){									
		$startar = FormatDate_insert($date_of_service);
		$endar = $txt_vis_ar_ak_examdate;
		if($startar==$endar){
			$vis_arCheckpass=true;
		}
		if($diff=@get_time_difference($startar, $endar)){
			$vis_arCheckpass=true;
		}
	}
	?>
		<tr>
			<?php 
			
			if($visdateCheckpass==true){
				?>
				<td class="text_lable">Distance</td>
				<?php 
			}else{
				echo("<td class='text_value'>&nbsp;</td>");
			}
			
			if(($chk_vis_near == 1 && $visNeardateCheckpass==true) &&($txt_vis_near_od_txt_1!="" && $txt_vis_near_od_txt_1!="20/")) {
				?> 
				<td class="text_lable paddingLeft"><?php echo"Near";?></td>
				<?php 
			} 
			else{
				echo("<td class='text_value paddingLeft' >&nbsp;</td>");
			}
			
            #Start AR AK Heading			
			if( $vis_arCheckpass==true && ($txt_vis_ar_od_s!="" || $txt_vis_ar_od_c!="" || $txt_vis_ar_od_a!="" || $txt_vis_ar_os_s!="" || $txt_vis_ar_os_c!="" || 			$txt_vis_ar_os_a!="")){
				if($txt_vis_ar_od_s!="" || $txt_vis_ar_od_c!="" || $txt_vis_ar_od_a!="" || $txt_vis_ar_os_s!="" || $txt_vis_ar_os_c!="" || $txt_vis_ar_os_a!=""){?>
					<td class="text_lable paddingLeft">AR</td>
				<?php }
				else{
					echo("<td class='text_value'>&nbsp;</td>");
				}
				
				if($txt_vis_ak_od_k!="" ||$txt_vis_ak_od_slash!="" || $txt_vis_ak_od_x!="" ||$txt_vis_ak_os_k!="" ||$txt_vis_ak_os_slash!="" || $txt_vis_ak_os_x!=""){?>
					<td class="text_lable paddingLeft">AK</td>
				<?php }
				else{
					echo("<td class='text_value'>&nbsp;</td>");
				}
			}
			#End AR AK Heading
			
            if($chk_vis_bat == 1 &&($txt_vis_bat_nl_od!="" && $txt_vis_bat_nl_od!="20/")){
            ?>
                <td class="text_lable paddingLeft">B.A.T</td>
            <?php }
			else{
				echo("<td class='text_value'>&nbsp;</td>");
			}
			?>
	</tr>
<tr>
<?php	
$odinfo_vis_dis = $odinfo_vis_near = $odinfo_vis_ar = $odinfo_vis_ak = array();
if($visdateCheckpass==true){
	$odinfo_vis_dis[]="";
	$odinfo_vis_dis[]=($sel_vis_dis_od_sel_1!="" && $txt_vis_dis_od_txt_1!="20/")?$sel_vis_dis_od_sel_1:"";
	//$osinfo_vis_dis.=($sel_vis_dis_od_sel_1!="" && $txt_vis_dis_od_txt_1!="20/")?$sel_vis_dis_od_sel_1."RM":"jj"; 								
	$odinfo_vis_dis[]=($sel_vis_dis_od_sel_1!="" && $txt_vis_dis_od_txt_1!="20/")?"".$txt_vis_dis_od_txt_1."":""; 
	
	if(($sel_vis_dis_os_sel_2!="" && $txt_vis_dis_os_txt_2!="20/" && $txt_vis_dis_os_txt_2!="") and ($sel_vis_dis_od_sel_2!="" && $txt_vis_dis_od_txt_2!="20/" && $txt_vis_dis_od_txt_2!="")){
		$odinfo_vis_dis[]=($sel_vis_dis_od_sel_2!="" && $txt_vis_dis_od_txt_2!="20/")?$sel_vis_dis_od_sel_2:""; 								
		$odinfo_vis_dis[]=($sel_vis_dis_od_sel_2!="" && $txt_vis_dis_od_txt_2!="20/")?"".$txt_vis_dis_od_txt_2."":""; 
	}
}
elseif(($sel_vis_dis_os_sel_2!="" && $txt_vis_dis_os_txt_2!="20/" && $txt_vis_dis_os_txt_2!="") and ($sel_vis_dis_od_sel_2!="" && $txt_vis_dis_od_txt_2!="20/" && $txt_vis_dis_od_txt_2!="")){
	$odinfo_vis_dis[]="";
	$odinfo_vis_dis[]=($sel_vis_dis_od_sel_2!="" && $txt_vis_dis_od_txt_2!="20/")?$sel_vis_dis_od_sel_2:""; 								
	$odinfo_vis_dis[]=($sel_vis_dis_od_sel_2!="" && $txt_vis_dis_od_txt_2!="20/")?"".$txt_vis_dis_od_txt_2."":""; 
}
?>
<td class="text_value"><?php if(count($odinfo_vis_dis)>0){odLable();} print(@implode("&nbsp;",$odinfo_vis_dis));?></td>	

<?php 
$haveValue = 0;
if(($chk_vis_near == 1 && $visNeardateCheckpass==true) &&(($sel_vis_near_od_sel_1!="" && $txt_vis_near_od_txt_1!="" && $txt_vis_near_od_txt_1!="20/") and ($sel_vis_near_os_sel_1!="" && $txt_vis_near_os_txt_1!="" && $txt_vis_near_os_txt_1!="20/"))){ 
 $haveValue = 1;
 $odinfo_vis_near[]="";
 $odinfo_vis_near[]=($sel_vis_near_od_sel_1!="" && $txt_vis_near_od_txt_1!="20/")?$sel_vis_near_od_sel_1:"";							
 $odinfo_vis_near[]=($sel_vis_near_od_sel_1!="" && $txt_vis_near_od_txt_1!="" && $txt_vis_near_od_txt_1!="20/")?"".$txt_vis_near_od_txt_1."":""; 
 if(($sel_vis_near_od_sel_2!="" && $txt_vis_near_od_txt_2!="20/" && $txt_vis_near_od_txt_2!="") and ($sel_vis_near_os_sel_2!="" && $txt_vis_near_os_txt_2!="20/" && $txt_vis_near_os_txt_2!="")){
	$odinfo_vis_near[]=($sel_vis_near_od_sel_2!="" && $txt_vis_near_od_txt_2!="20/")?$sel_vis_near_od_sel_2:""; 
	$odinfo_vis_near[]=($txt_vis_near_od_txt_2!="" && $txt_vis_near_od_txt_2!="20/")?"".$txt_vis_near_od_txt_2."":""; 
	}
}
elseif(($sel_vis_near_od_sel_2!="" && $txt_vis_near_od_txt_2!="20/" && $txt_vis_near_od_txt_2!="") and ($sel_vis_near_os_sel_2!="" && $txt_vis_near_os_txt_2!="20/" && $txt_vis_near_os_txt_2!="")){
	$odinfo_vis_near[]=($sel_vis_near_od_sel_2!="" && $txt_vis_near_od_txt_2!="20/")?$sel_vis_near_od_sel_2:""; 
	$odinfo_vis_near[]=($txt_vis_near_od_txt_2!="" && $txt_vis_near_od_txt_2!="20/")?"".$txt_vis_near_od_txt_2."":""; 
}
 ?>
<td class="text_value paddingLeft"><?php if($haveValue == 1){odLable(); print implode("&nbsp;",$odinfo_vis_near);}?></td>	
<?php 
#start AR AK (OD) 
if( $vis_arCheckpass==true && ($txt_vis_ar_od_s!="" || $txt_vis_ar_od_c!="" || $txt_vis_ar_od_a!="" || $txt_vis_ar_os_s!="" || $txt_vis_ar_os_c!="" || $txt_vis_ar_os_a!="")){

$odinfo_vis_ar[]="";
	if($txt_vis_ar_od_s!=""){$odinfo_vis_ar[]="S"; }
	$odinfo_vis_ar[]="".$txt_vis_ar_od_s."";
	if($txt_vis_ar_od_c!=""){$odinfo_vis_ar[]="C"; }
		$odinfo_vis_ar[]="".$txt_vis_ar_od_c."";							
		if($txt_vis_ar_od_a!=""){$odinfo_vis_ar[]="A"; }
		if($txt_vis_ar_od_a!=""){$odinfo_vis_ar[]="".$txt_vis_ar_od_a."&#176;";}						
		if($txt_vis_ar_od_a!=""){$odinfo_vis_ar[]="".$sel_vis_ar_od_sel_1."";}
		?>
<td class="text_value paddingLeft"><?php odLable(); print implode("&nbsp;",$odinfo_vis_ar);?></td>	
<?php
} 
?>

<?php if($txt_vis_ak_od_k!="" ||$txt_vis_ak_od_slash!="" || $txt_vis_ak_od_x!=""){
$odinfo_vis_ak[]="";
if($txt_vis_ak_od_k!=""){$odinfo_vis_ak[]="K:";}
$odinfo_vis_ak[]="".$txt_vis_ak_od_k."";
//if($txt_vis_ak_od_slash!=""){$odinfo_vis_ak[]="/"; }
$odinfo_vis_ak[]="/";
$odinfo_vis_ak[]="".$txt_vis_ak_od_slash."";
//if($txt_vis_ak_od_x!=""){$odinfo_vis_ak[]="X"; }
$odinfo_vis_ak[]="X";
if($txt_vis_ak_od_x!=""){$odinfo_vis_ak[]="".$txt_vis_ak_od_x."&#176; ";}
?>
<td class="text_value paddingLeft"><?php odLable(); print implode("&nbsp;",$odinfo_vis_ak);?></td>	
<?php } 
#End AR AK (OD)

?>

<?php
if($txt_vis_bat_nl_od!="" && $txt_vis_bat_nl_od!="20/"){
  $odinfo_vis_bat=array();
  $odinfo_vis_bat[]="";
 if($txt_vis_bat_nl_od!="" && $txt_vis_bat_nl_od!="20/" ){$odinfo_vis_bat[]="NL"; }
 if($txt_vis_bat_nl_od!="" && $txt_vis_bat_nl_od!="20/"){ $odinfo_vis_bat[]="".$txt_vis_bat_nl_od.""; } 
 if($txt_vis_bat_low_od!="" && $txt_vis_bat_low_od!="20/" ){$odinfo_vis_bat[]="L"; }
 if($txt_vis_bat_low_od!="" && $txt_vis_bat_low_od!="20/"){$odinfo_vis_bat[]="".$txt_vis_bat_low_od.""; } 
 if($txt_vis_bat_med_od!="" && $txt_vis_bat_med_od!="20/"){$odinfo_vis_bat[]= "M"; }
 if($txt_vis_bat_med_od!="" && $txt_vis_bat_med_od!="20/"){$odinfo_vis_bat[]="".$txt_vis_bat_med_od.""; }
 if($txt_vis_bat_high_od!="" && $txt_vis_bat_high_od!="20/"){$odinfo_vis_bat[]="H"; }
 if($txt_vis_bat_high_od!="" && $txt_vis_bat_high_od!="20/" ){ $odinfo_vis_bat[]="".$txt_vis_bat_high_od."";}
?>
<td class="text_value paddingLeft"><?php odLable(); print implode("&nbsp;",$odinfo_vis_bat);?></td>
<?php
 } ?>
</tr>
<tr>
<?php
$osinfo_vis_dis = $osinfo_vis_near = $osinfo_vis_ar = $osinfo_vis_ak = array();
if(($sel_vis_dis_od_sel_1!="" && $txt_vis_dis_od_txt_1!="20/" && $txt_vis_dis_od_txt_1!="") && ($sel_vis_dis_os_sel_1!="" && $txt_vis_dis_os_txt_1!="20/" && $txt_vis_dis_os_txt_1!="")){ 
	$osinfo_vis_dis[]="";
	$osinfo_vis_dis[]=($sel_vis_dis_os_sel_1!="" && $txt_vis_dis_os_txt_1!="20/" && $txt_vis_dis_os_txt_1!="")?$sel_vis_dis_os_sel_1:"";
	$osinfo_vis_dis[]=($sel_vis_dis_os_sel_1!="" && $txt_vis_dis_os_txt_1!="20/" && $txt_vis_dis_os_txt_1!="")?"".$txt_vis_dis_os_txt_1."":""; 						
	if(($sel_vis_dis_os_sel_2!="" && $txt_vis_dis_os_txt_2!="20/" && $txt_vis_dis_os_txt_2!="") and ($sel_vis_dis_od_sel_2!="" && $txt_vis_dis_od_txt_2!="20/" && $txt_vis_dis_od_txt_2!="")){
		$osinfo_vis_dis[]=($sel_vis_dis_os_sel_2!="" && $txt_vis_dis_os_txt_2!="20/")?$sel_vis_dis_os_sel_2:""; 
		$osinfo_vis_dis[]=($sel_vis_dis_os_sel_2!="" && $txt_vis_dis_os_txt_2!="20/")?"".$txt_vis_dis_os_txt_2."" :""; 
	}
}
elseif(($sel_vis_dis_os_sel_2!="" && $txt_vis_dis_os_txt_2!="20/" && $txt_vis_dis_os_txt_2!="") and ($sel_vis_dis_od_sel_2!="" && $txt_vis_dis_od_txt_2!="20/" && $txt_vis_dis_od_txt_2!="")){
	$osinfo_vis_dis[]=($sel_vis_dis_os_sel_2!="" && $txt_vis_dis_os_txt_2!="20/")?$sel_vis_dis_os_sel_2:""; 
	$osinfo_vis_dis[]=($sel_vis_dis_os_sel_2!="" && $txt_vis_dis_os_txt_2!="20/")?"".$txt_vis_dis_os_txt_2."" :""; 
}
?>
<td class="text_value"><?php if(count($osinfo_vis_dis)>0){osLable();} print implode("&nbsp;",$osinfo_vis_dis);?></td>


<?php		
$haveValue = 0;
if(($chk_vis_near == 1 && $visNeardateCheckpass==true) &&(($sel_vis_near_od_sel_1!="" && $txt_vis_near_od_txt_1!="" && $txt_vis_near_od_txt_1!="20/") and ($sel_vis_near_os_sel_1!="" && $txt_vis_near_os_txt_1!="" && $txt_vis_near_os_txt_1!="20/"))){ 
	$osinfo_vis_near[]="";	
	$haveValue = 1;
	$osinfo_vis_near[]=($sel_vis_near_os_sel_1!="" && $txt_vis_near_os_txt_1!="20/")?$sel_vis_near_os_sel_1:""; 	
	$osinfo_vis_near[]=($sel_vis_near_os_sel_1!="" && $txt_vis_near_os_txt_1!="" && $txt_vis_near_os_txt_1!="20/")?"".$txt_vis_near_os_txt_1."":"";		
	
	if(($sel_vis_near_od_sel_2!="" and $txt_vis_near_od_txt_2!="20/") and ($sel_vis_near_os_sel_2!="" && $txt_vis_near_os_txt_2!="20/")){	
		$osinfo_vis_near[]=($sel_vis_near_os_sel_2!="" && $txt_vis_near_os_txt_2!="20/")?$sel_vis_near_os_sel_2:""; 	
		$osinfo_vis_near[]=($sel_vis_near_os_sel_2!="" && $txt_vis_near_os_txt_2!="" && $txt_vis_near_os_txt_2!="20/")?"".$txt_vis_near_os_txt_2."":""; 	
	}
 } 
elseif(($sel_vis_near_od_sel_2!="" and $txt_vis_near_od_txt_2!="20/") and ($sel_vis_near_os_sel_2!="" && $txt_vis_near_os_txt_2!="20/")){	
		$osinfo_vis_near[]=($sel_vis_near_os_sel_2!="" && $txt_vis_near_os_txt_2!="20/")?$sel_vis_near_os_sel_2:""; 	
		$osinfo_vis_near[]=($sel_vis_near_os_sel_2!="" && $txt_vis_near_os_txt_2!="" && $txt_vis_near_os_txt_2!="20/")?"".$txt_vis_near_os_txt_2."":""; 	
}
?>
	<td class="text_value paddingLeft"><?php if($haveValue == 1){osLable(); print implode("&nbsp;",$osinfo_vis_near);}?></td>		
<?php
#start AR AK (OS) 
if($txt_vis_ar_os_s!="" || $txt_vis_ar_os_c!="" || $txt_vis_ar_os_a!=""){
		$osinfo_vis_ar[]="";
		if($txt_vis_ar_os_s!=""){
			$osinfo_vis_ar[]="S";
			$osinfo_vis_ar[]="".$txt_vis_ar_os_s."";
		}
		if($txt_vis_ar_os_c!=""){
			$osinfo_vis_ar[]="C";
			$osinfo_vis_ar[]="".$txt_vis_ar_os_c."";							
		}
		if($txt_vis_ar_os_a!=""){
			$osinfo_vis_ar[]="A";
			$osinfo_vis_ar[]="".$txt_vis_ar_os_a."&#176; ";
			$osinfo_vis_ar[]="".$sel_vis_ar_os_sel_1."";
		}
?>
<td class="text_value paddingLeft"><?php osLable(); print implode("&nbsp;",$osinfo_vis_ar);?></td>	

<?php } ?>
<?php if($txt_vis_ak_os_k!="" ||$txt_vis_ak_os_slash!="" || $txt_vis_ak_os_x!=""){ 
$osinfo_vis_ak[]="";
if($txt_vis_ak_os_k!=""){$osinfo_vis_ak[]="K:"; }
$osinfo_vis_ak[]="".$txt_vis_ak_os_k."";
//if($txt_vis_ak_os_slash!=""){$osinfo_vis_ak[]="/";}
$osinfo_vis_ak[]="/";
$osinfo_vis_ak[]="".$txt_vis_ak_os_slash."";
//if($txt_vis_ak_os_x!=""){$osinfo_vis_ak[]="X"; }
$osinfo_vis_ak[]="X";
if($txt_vis_ak_os_x!=""){$osinfo_vis_ak[]="".$txt_vis_ak_os_x."&#176;";}
?>
<td class="text_value paddingLeft"><?php osLable(); print implode("&nbsp;",$osinfo_vis_ak);?></td>	
<?php }
# End AR AK (OS) 
?>
    
<?php
if($chk_vis_bat == 1 &&($txt_vis_bat_nl_od!="" && $txt_vis_bat_nl_od!="20/")){
$osinfo_vis_bat = array();
$osinfo_vis_bat[]="";
if($txt_vis_bat_nl_os!="" && $txt_vis_bat_nl_os!="20/"){$osinfo_vis_bat[]="NL"; }
if($txt_vis_bat_nl_os!="" && $txt_vis_bat_nl_os!="20/" ){ $osinfo_vis_bat[]="".$txt_vis_bat_nl_os."";}						
if($txt_vis_bat_low_os!="" && $txt_vis_bat_low_os!="20/"){$osinfo_vis_bat[]="L"; }
if($txt_vis_bat_low_os!="" && $txt_vis_bat_low_os!="20/" ){$osinfo_vis_bat[]="".$txt_vis_bat_low_os."";}						
if($txt_vis_bat_med_os!="" && $txt_vis_bat_med_os!="20/"){$osinfo_vis_bat[]="M";}
if($txt_vis_bat_med_os!="" && $txt_vis_bat_med_os!="20/" ){ $osinfo_vis_bat[]="".$txt_vis_bat_med_os."";}
if($txt_vis_bat_high_os!="" && $txt_vis_bat_high_os!="20/"){$osinfo_vis_bat[]="H"; }
if($txt_vis_bat_high_os!="" && $txt_vis_bat_high_os!="20/" ){ $osinfo_vis_bat[]="".$txt_vis_bat_high_os."";}
?>
<td class="text_value paddingLeft"><?php osLable(); print implode("&nbsp;",$osinfo_vis_bat);?></td>
<?php } ?>
</tr>
</table>

<?php 
if($txt_vis_dis_desc!="" ){
	?>
<table cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td class="text_value"><span class="text_lable">Distance Desc.</span>&nbsp;&nbsp;<?php echo $txt_vis_dis_desc;//txt_vis_dis_near_desc; ?></td>
	</tr>
</table>
	<?php 
} 
if($chk_vis_near == 1 && $txt_vis_near_desc!="" ){
	?>
<table cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td class="text_value"><span class="text_lable">Near Desc.</span>&nbsp;&nbsp;<?php echo $txt_vis_near_desc;//txt_vis_dis_near_desc; ?></td>
	</tr>
</table>
	<?php 
}
if($chk_vis_bat == 1 && $txt_vis_bat_desc!="" ){
	?>
<table  cellpadding="0" cellspacing="0">
	<tr>
		<td class="text_value"> <span class="text_lable">B.A.T Desc.&nbsp;&nbsp;</span><?php echo $txt_vis_bat_desc;//txt_vis_dis_near_desc; ?></td>
	</tr>
</table>
	<?php 
} 
?>
<!-- Vision Table Row 2-->
<?php
 
$vis_arCheckpass=false;
if($date_of_service!="" || $txt_vis_ar_ak_examdate!="" || $txt_vis_ar_ak_examdate!=0){									
	$startar = FormatDate_insert($date_of_service);
	$endar = $txt_vis_ar_ak_examdate;
if($startar==$endar){
	$vis_arCheckpass=true;
}
if($diff=@get_time_difference($startar, $endar)){
	$vis_arCheckpass=true;
}
}
?>	
<?php 

if($txt_vis_ar_ak_desc!="" && $vis_arCheckpass==true ){
?>
<table cellpadding="0" cellspacing="0" border="0" style="width:100%;">
	<tr>
		<td class="text_value" style="width:100%;">
			<?php if($txt_vis_ar_ak_desc!=""){?><span class="text_lable">AR Desc.&nbsp;</span><?php echo $txt_vis_ar_ak_desc;}?>
		</td>
	</tr>	
</table>	
<?php 
}if($txt_vis_AK_desc!="" && $vis_arCheckpass==true ){
?>
<table cellpadding="0" cellspacing="0" border="0" style="width:100%;">
	<tr>
		<td class="text_value" style="width:100%;">
			<?php if($txt_vis_AK_desc!=""){?><span class="text_lable">AK Desc.&nbsp;</span><?php echo $txt_vis_AK_desc;}?>
		</td>	
	</tr>	
</table>	
<?php 
}
// Start PC Code//
?><!-- Initial -->
<?php 
if($sel_vis_pc_od_sel_1!="" &&($txt_vis_pc_od_s!=""||$txt_vis_pc_od_c!=""||$txt_vis_pc_od_a!="" || $txt_vis_pc_od_add!="")){
?>
<!-- 1st PC -->
<table style="width:100%;" class="paddingTop" cellpadding="0" cellspacing="0" border="0">
  <tr>
    <td  class='tb_subheading'>PC 1st</td>
	<td  class='tb_subheading'>&nbsp;</td>	
	<?php 
	$prismTxt = '';	
	if($elem_prismPc1 || ($sel_vis_pc_od_sel_2!="" && $sel_vis_pc_od_slash != "") || ($sel_vis_pc_os_sel_2!="" && $sel_vis_pc_os_slash != "") ||
					($sel_vis_pc_od_sel_2_2!="" && $sel_vis_pc_od_slash_2 != "") || ($sel_vis_pc_os_sel_2_2!="" && $sel_vis_pc_os_slash_2 != "") ||
					($sel_vis_pc_od_sel_2_3!="" && $sel_vis_pc_od_slash_3 != "" ) || ($sel_vis_pc_os_sel_2_3!="" && $sel_vis_pc_os_slash_3 != "")
					){
		$prismTxt = 'Prism';	
	}	
		
	$overRefractionTxt = "";
	if($chk_pc_near == "Over Refraction"){
		$overRefractionTxt = "Over Refraction";
	}
	?>
	<td  class='tb_subheading'><?php echo $prismTxt; ?></td>
	<td  class='tb_subheading' style="width:482px;"><?php echo $overRefractionTxt; ?></td>
  </tr>
  <!-- OD -->
  <tr>
    <?php 
	$pc1gp1_od = $pc1gp1_os = $pc1gp2_addod = $pc1gp2_addos = $pc1gp3_prismod = $pc1gp3_prismos = array();
	$pc1gp1_od[]="";
	$pc1gp1_os[]="";
	$pc1gp2_addod[]="";
	$pc1gp2_addos[]="";
	$pc1gp3_prismod[]="";
	$pc1gp3_prismos[]="";
	/*$pc1gp1_od[]="OD";*/

	if(($sel_vis_pc_od_sel_1!="" && $txt_vis_pc_od_s!="" || ($sel_vis_pc_os_sel_1!="" && $txt_vis_pc_os_s!="")) ||
	($sel_vis_pc_od_sel_1_2!="" && $txt_vis_pc_od_s_2!="" || ($sel_vis_pc_os_sel_1_2!="" && $txt_vis_pc_os_s_2!="")) ||
	($sel_vis_pc_od_sel_1_3!="" && $txt_vis_pc_od_s_3!="" || ($sel_vis_pc_os_sel_1_3!="" && $txt_vis_pc_os_s_3!=""))
	){
		$pc1gp1_od[]="$sel_vis_pc_od_sel_1";
		 
	}
	if(($txt_vis_pc_od_s!="" || $txt_vis_pc_os_s!="") ||
	($txt_vis_pc_od_s_2!="" || $txt_vis_pc_os_s_2!="") ||
	($txt_vis_pc_od_s_3!="" || $txt_vis_pc_os_s_3!="")
	){
		if($txt_vis_pc_od_s!=""){$pc1gp1_od[]="S"; }
		$pc1gp1_od[]="$txt_vis_pc_od_s";
		 
	}
	if(($txt_vis_pc_od_c!="" || $txt_vis_pc_os_c!="") ||
	($txt_vis_pc_od_c_2!="" || $txt_vis_pc_os_c_2!="") ||
	($txt_vis_pc_od_c_3!="" || $txt_vis_pc_os_c_3!="")
	){
		if($txt_vis_pc_od_c!=""){$pc1gp1_od[]="C" ; }
		$pc1gp1_od[]="$txt_vis_pc_od_c";		
	}
	if(($txt_vis_pc_od_a!="" || $txt_vis_pc_os_a!="") ||
	($txt_vis_pc_od_a_2!="" || $txt_vis_pc_os_a_2!="") ||
	($txt_vis_pc_od_a_3!="" || $txt_vis_pc_os_a_3!="")
	){
		 if($txt_vis_pc_od_a!="" ){$pc1gp1_od[]="A"; }
		 if($txt_vis_pc_od_a!="" ){ $pc1gp1_od[]="".$txt_vis_pc_od_a."&#176;";}
	}
	?>
    <td class='text_value'><?php odLable(); print implode("&nbsp;",$pc1gp1_od);?></td>
    <?php
	
	if(
	($txt_vis_pc_od_add!="" and ($txt_vis_pc_od_s!="" || $txt_vis_pc_od_c!="" || $txt_vis_pc_od_a!="")) 
	){		 
		$pc1gp2_addod[]="Add";
		$pc1gp2_addod[]="$txt_vis_pc_od_add";		
	}?>
    <td class='text_value'><?php  print implode("&nbsp;",$pc1gp2_addod);?></td>
    <?php
	if(($elem_prismPc1 == "1") || ($elem_prismPc2 == "1") || ($elem_prismPc3 == "1")){
		if(($sel_vis_pc_od_p!="") || ($sel_vis_pc_os_p!="") ||
		($sel_vis_pc_od_p_2!="") || ($sel_vis_pc_os_p_2!="") ||
		($sel_vis_pc_od_p_3!="") || ($sel_vis_pc_os_p_3!="")
		){
		 $pc1gp3_prismod[]=(!empty($sel_vis_pc_od_p)) ? "P" : "";
		
		$pc1gp3_prismod[]="$sel_vis_pc_od_p";
	
		}
		if((($sel_vis_pc_od_prism!="" && $sel_vis_pc_od_p != "") || ($sel_vis_pc_os_prism!="" && $sel_vis_pc_os_p != "")) ||
		(($sel_vis_pc_od_prism_2!=""  && $sel_vis_pc_od_p_2 != "" ) || ($sel_vis_pc_os_prism_2!=""  && $sel_vis_pc_os_p_2 != "")) ||
		(($sel_vis_pc_od_prism_3!="" && $sel_vis_pc_od_p_3 != "") || ($sel_vis_pc_os_prism_3!=""  && $sel_vis_pc_os_p_3 != ""))
		){
			
			$pc1gp3_prismod[]=(!empty($sel_vis_pc_od_prism) && !empty($sel_vis_pc_od_slash)) ? "<img src='pic_vision_pc.jpg'/>" : "";
			if(!empty($sel_vis_pc_od_prism) && !empty($sel_vis_pc_od_slash)){$pc1gp3_prismod[]="$sel_vis_pc_od_prism";}
			
		}					
		if(($sel_vis_pc_od_slash!="") || ($sel_vis_pc_os_slash!="") ||
		($sel_vis_pc_od_slash_2!="") || ($sel_vis_pc_os_slash_2!="") ||
		($sel_vis_pc_od_slash_3!="") || ($sel_vis_pc_os_slash_3!="")
		){
			
			$pc1gp3_prismod[]=(!empty($sel_vis_pc_od_slash)) ? "<img src='pic_vision_pc.jpg'/>" : "";
			$pc1gp3_prismod[]="$sel_vis_pc_od_slash";
			 
		}
		if(($sel_vis_pc_od_sel_2!="" && $sel_vis_pc_od_slash != "") || ($sel_vis_pc_os_sel_2!="" && $sel_vis_pc_os_slash != "") ||
		($sel_vis_pc_od_sel_2_2!="" && $sel_vis_pc_od_slash_2 != "") || ($sel_vis_pc_os_sel_2_2!="" && $sel_vis_pc_os_slash_2 != "") ||
		($sel_vis_pc_od_sel_2_3!="" && $sel_vis_pc_od_slash_3 != "" ) || ($sel_vis_pc_os_sel_2_3!="" && $sel_vis_pc_os_slash_3 != "")
		){
			$pc1gp3_prismod[]="$sel_vis_pc_od_sel_2";
		}
	}
	?>
    <td class="text_value" style="width:165px"><?php print implode("&nbsp;",$pc1gp3_prismod);?></td>
    <?php 
	if($sel_vis_pc_od_sel_1!="" &&($txt_vis_pc_od_s!=""||$txt_vis_pc_od_c!=""||$txt_vis_pc_od_a!="" || $txt_vis_pc_od_add!="")){
		$overrefraction1od=array();
		$overrefraction1od[]="";
		if($chk_pc_near <> "Over Refraction"){
			 if($txt_vis_pc_od_near_txt!=""){$overrefraction1od[]="OD"; }
			 $overrefraction1od[]="$txt_vis_pc_od_near_txt";
		}else{
			 if($txt_vis_pc_od_overref_s!="" || $txt_vis_pc_od_overref_c!="" || $txt_vis_pc_od_overref_a!="" || $txt_vis_pc_od_overref_v!="" ){/*$overrefraction1od[]="OD";*/}
			 if($txt_vis_pc_od_overref_s!=""){ $overrefraction1od[]="S";}
			 $overrefraction1od[]="".trim($txt_vis_pc_od_overref_s)."";
			 if($txt_vis_pc_od_overref_c!=""){ $overrefraction1od[]="C";}
			 $overrefraction1od[]="".trim($txt_vis_pc_od_overref_c)."";
			 if($txt_vis_pc_od_overref_a!=""){ $overrefraction1od[]="A";}
			 $overrefraction1od[]="".trim($txt_vis_pc_od_overref_a)."";
			 if($txt_vis_pc_od_overref_v!="" && $txt_vis_pc_od_overref_v!="20/"){ $overrefraction1od[]="V";}
			 if($txt_vis_pc_od_overref_v!="" && $txt_vis_pc_od_overref_v!="20/" ){ $overrefraction1od[]="".trim($txt_vis_pc_od_overref_v)."";
		}
		?>
	<td class="text_value" style="width:482px;"><?php odLable(); print(implode("&nbsp;",$overrefraction1od));?></td>
	<?php 
	}
	?>
	<!-- Prism -->
	</tr>
	<tr>
	<?php
	if(
		($sel_vis_pc_os_sel_1!="" and $txt_vis_pc_os_s!="") ||
		($sel_vis_pc_os_sel_1_2!="" and $txt_vis_pc_os_s_2!="")||
		($sel_vis_pc_os_sel_1_3!="" and $txt_vis_pc_os_s_3!="")
	){
		$pc1gp1_os[]="$sel_vis_pc_os_sel_1";
	} 
	if(
		($sel_vis_pc_os_sel_1!="" and $txt_vis_pc_os_s!="") ||
		($sel_vis_pc_os_sel_1_2!="" and $txt_vis_pc_os_s_2!="")||
		($sel_vis_pc_os_sel_1_3!="" and $txt_vis_pc_os_s_3!="")
	){
		if($txt_vis_pc_os_s!="" and $sel_vis_pc_os_sel_1!=""){
			$pc1gp1_os[]="S"; 
			$pc1gp1_os[]="$txt_vis_pc_os_s";						
		}
	}
	if(
		($sel_vis_pc_os_sel_1!="" and $txt_vis_pc_os_c!="") ||
		($sel_vis_pc_os_sel_1_2!="" and $txt_vis_pc_os_c_2!="") ||
		($sel_vis_pc_os_sel_1_3!="" and $txt_vis_pc_os_c_3!="")
	){
		if($txt_vis_pc_os_c!="" and $sel_vis_pc_os_sel_1!=""){
			$pc1gp1_os[]="C"; 
			$pc1gp1_os[]="$txt_vis_pc_os_c";
		}
	}
	if(
		($sel_vis_pc_os_sel_1!="" and $txt_vis_pc_os_a!="") ||
		($sel_vis_pc_os_sel_1_2!="" and $txt_vis_pc_os_a_2!="") ||
		($sel_vis_pc_os_sel_1_3!="" and $txt_vis_pc_os_a_3!="")
	){
		 if($txt_vis_pc_os_a!="" and $sel_vis_pc_os_sel_1!=""){
			$pc1gp1_os[]="A";
			$pc1gp1_os[]="".$txt_vis_pc_os_a."&#176;";
		 }
	}
	?>
		<td class="text_value" style="width:231px;"><?php osLable(); print implode("&nbsp;",$pc1gp1_os);?></td>
	<?php
    if(
        ($txt_vis_pc_os_add!="" and ($txt_vis_pc_os_s!="" || $txt_vis_pc_os_c!="" || $txt_vis_pc_os_a!=""))
    ){		 
         if(strlen($txt_vis_pc_os_add)<>1){
            $pc1gp2_addos[]="Add"; 						 
            $pc1gp2_addos[]="$txt_vis_pc_os_add"; 						
        }		
    }?>
		<td class="text_value" style="width:74px"><?php print implode("&nbsp;",$pc1gp2_addos);?></td>
	<?php
    if(($elem_prismPc1 == "1") || ($elem_prismPc2 == "1") || ($elem_prismPc3 == "1")){
        if(($sel_vis_pc_od_p!="") || ($sel_vis_pc_os_p!="") ||
        ($sel_vis_pc_od_p_2!="") || ($sel_vis_pc_os_p_2!="") ||
        ($sel_vis_pc_od_p_3!="") || ($sel_vis_pc_os_p_3!="")
        ){
            $pc1gp3_prismos[]=(!empty($sel_vis_pc_os_p)) ? "P" : "";
            $pc1gp3_prismos[]="$sel_vis_pc_os_p";
             
        }
        if((($sel_vis_pc_od_prism!="" && $sel_vis_pc_od_p != "") || ($sel_vis_pc_os_prism!="" && $sel_vis_pc_os_p != "")) ||
        (($sel_vis_pc_od_prism_2!=""  && $sel_vis_pc_od_p_2 != "" ) || ($sel_vis_pc_os_prism_2!=""  && $sel_vis_pc_os_p_2 != "")) ||
        (($sel_vis_pc_od_prism_3!="" && $sel_vis_pc_od_p_3 != "") || ($sel_vis_pc_os_prism_3!=""  && $sel_vis_pc_os_p_3 != ""))
        ){
            
            
            $pc1gp3_prismos[]=(!empty($sel_vis_pc_os_prism) && !empty($sel_vis_pc_os_slash)) ? "<img src='pic_vision_pc.jpg'/>" : "";								
            if(!empty($sel_vis_pc_os_prism) && !empty($sel_vis_pc_os_slash)){$pc1gp3_prismos[]="$sel_vis_pc_os_prism";}
            
        }
        if(($sel_vis_pc_od_slash!="") || ($sel_vis_pc_os_slash!="") ||
        ($sel_vis_pc_od_slash_2!="") || ($sel_vis_pc_os_slash_2!="") ||
        ($sel_vis_pc_od_slash_3!="") || ($sel_vis_pc_os_slash_3!="")
        ){
            
            $pc1gp3_prismos[]=(!empty($sel_vis_pc_os_slash)) ? "<img src='pic_vision_pc.jpg'/>" : "";
            $pc1gp3_prismos[]="$sel_vis_pc_os_slash";
            
        }
        if(($sel_vis_pc_od_sel_2!="" && $sel_vis_pc_od_slash != "") || ($sel_vis_pc_os_sel_2!="" && $sel_vis_pc_os_slash != "") ||
        ($sel_vis_pc_od_sel_2_2!="" && $sel_vis_pc_od_slash_2 != "") || ($sel_vis_pc_os_sel_2_2!="" && $sel_vis_pc_os_slash_2 != "") ||
        ($sel_vis_pc_od_sel_2_3!="" && $sel_vis_pc_od_slash_3 != "" ) || ($sel_vis_pc_os_sel_2_3!="" && $sel_vis_pc_os_slash_3 != "")
        ){
            $pc1gp3_prismos[]="$sel_vis_pc_os_sel_2";
        }
    }
    ?>
		<td class="text_value" style="width:165px"><?php print implode("&nbsp;",$pc1gp3_prismos);?></td>
	<?php $haveValue=0;
	if($sel_vis_pc_od_sel_1!="" &&($txt_vis_pc_od_s!=""||$txt_vis_pc_od_c!=""||$txt_vis_pc_od_a!="" || $txt_vis_pc_od_add!="")){
		$overrefraction1os = array();
		$overrefraction1os[]="";
		if($chk_pc_near <> "Over Refraction"){
			 if($txt_vis_pc_os_near_txt!=""){
				//$overrefraction1os[]="OS";
				//$overrefraction1os[]="$txt_vis_pc_os_near_txt";
			 }
		}else{
			$haveValue=0;
			if($txt_vis_pc_os_overref_s!="" || $txt_vis_pc_os_overref_c!="" || $txt_vis_pc_os_overref_a!="" || $txt_vis_pc_os_overref_v!=""){
				$haveValue=1;
			}
			if($txt_vis_pc_os_overref_s!=""){
				$overrefraction1os[]="S";
				$overrefraction1os[]="".trim($txt_vis_pc_os_overref_s)."";
			}
			if($txt_vis_pc_os_overref_c!=""){
				$overrefraction1os[]="C"; 
				$overrefraction1os[]="".trim($txt_vis_pc_os_overref_c)."";
			}
			if($txt_vis_pc_os_overref_a!=""){
				$overrefraction1os[]="A"; 
				$overrefraction1os[]="".trim($txt_vis_pc_os_overref_a)."";
			}
			if($txt_vis_pc_os_overref_v!="" && $txt_vis_pc_os_overref_v!="20/" ){
				$overrefraction1os[]="V";
				$overrefraction1os[]="". trim($txt_vis_pc_os_overref_v)."";
			}
		}
	?>
		<td class='text_value'><?php if($haveValue==1){osLable(); print(implode("&nbsp;",$overrefraction1os));}?></td>
	<?php
	}
	?>
	</tr>
	  <!-- OS -->
	  <?php 
	}
	if($sel_vis_pc_od_sel_1!="" &&($txt_vis_pc_od_s!=""||$txt_vis_pc_od_c!=""||$txt_vis_pc_od_a!="" || $txt_vis_pc_od_add!="")){
	?>
	</table>
	<?php 
	} 
	?>
<?php 
}
?>	
<!--End 1st PC -->
<!-- 2nd PC -->
<?php
if($sel_vis_pc_od_sel_1_2!="" &&($txt_vis_pc_od_s_2!=""||$txt_vis_pc_od_c_2!=""||$txt_vis_pc_od_a_2!="" || $txt_vis_pc_od_add_2!="")){
	?>
<!-- Start 2nd PC -->
<table style="width:100%;" class="paddingTop" cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td class='tb_subheading'>PC 2nd</td>
		<td class='tb_subheading'>&nbsp;</td>
		<?php 
			$prismTxt2 = '';	
			if($elem_prismPc2){
				$prismTxt2 = 'Prism';	
			}	
			
			$overRefractionTxt2 = "";
			if($chk_pc_near_2 == "Over Refraction"){
				$overRefractionTxt2 = "Over Refraction";
			}
		?>
		<td  class="tb_subheading"><?php echo $prismTxt2; ?></td>
		<td class="tb_subheading" style="width:484px;" ><?php echo $overRefractionTx2; ?></td>
	</tr>
	<!-- OD -->
	<tr height="20">
	<?php 
		$pc2gp1_od = $pc2gp1_os = $pc2gp2_addod = $pc2gp2_addos = $pc2gp3_prismod = $pc2gp3_prismos = array();
		$pc2gp1_od[]="";
		$pc2gp1_os[]="";
		$pc2gp2_addod[]="";
		$pc2gp2_addos[]="";
		$pc2gp3_prismod[]="";
		$pc2gp3_prismos[]="";
		if(($sel_vis_pc_od_sel_1!="" && $txt_vis_pc_od_s!="" || ($sel_vis_pc_os_sel_1!="" && $txt_vis_pc_os_s!="")) ||
		($sel_vis_pc_od_sel_1_2!="" && $txt_vis_pc_od_s_2!="" || ($sel_vis_pc_os_sel_1_2!="" && $txt_vis_pc_os_s_2!="")) ||
		($sel_vis_pc_od_sel_1_3!="" && $txt_vis_pc_od_s_3!="" || ($sel_vis_pc_os_sel_1_3!="" && $txt_vis_pc_os_s_3!=""))
		){
			$pc2gp1_od[]="$sel_vis_pc_od_sel_1_2";
		}
		if(($txt_vis_pc_od_s!="" || $txt_vis_pc_os_s!="") ||
		($txt_vis_pc_od_s_2!="" || $txt_vis_pc_os_s_2!="") ||
		($txt_vis_pc_od_s_3!="" || $txt_vis_pc_os_s_3!="")
		){
			if($txt_vis_pc_od_s_2!=""){$pc2gp1_od[]="S"; }
			$pc2gp1_od[]="$txt_vis_pc_od_s_2";
		}
		if(($txt_vis_pc_od_c!="" || $txt_vis_pc_os_c!="") ||
		($txt_vis_pc_od_c_2!="" || $txt_vis_pc_os_c_2!="") ||
		($txt_vis_pc_od_c_3!="" || $txt_vis_pc_os_c_3!="")
		){
			if($txt_vis_pc_od_c_2!=""){$pc2gp1_od[]="C"; }
			$pc2gp1_od[]="$txt_vis_pc_od_c_2";
		}
		if(($txt_vis_pc_od_a!="" || $txt_vis_pc_os_a!="") ||
		($txt_vis_pc_od_a_2!="" || $txt_vis_pc_os_a_2!="") ||
		($txt_vis_pc_od_a_3!="" || $txt_vis_pc_os_a_3!="")
		){
			 if($txt_vis_pc_od_a_2!="" ){$pc2gp1_od[]="A"; } 
			 if($txt_vis_pc_od_a_2!="" ){ $pc2gp1_od[]="".$txt_vis_pc_od_a_2."&#176;";}
		}
		?>
			<td class='text_value'><?php odLable(); print implode("&nbsp;",$pc2gp1_od);?></td>
		<?php
		if(
			($txt_vis_pc_od_add_2!="" and ($txt_vis_pc_od_s_2!="" || $txt_vis_pc_od_c_2!="" || $txt_vis_pc_od_a_2!=""))
		){
			 $pc2gp2_addod[]="Add"; 
			 $pc2gp2_addod[]="$txt_vis_pc_od_add_2";
		}
		?>
			<td class='text_value'><?php  print implode("&nbsp;",$pc2gp2_addod);?></td>
		<?php 
		if(($elem_prismPc1 == "1") || ($elem_prismPc2 == "1") || ($elem_prismPc3 == "1")){
			if(($sel_vis_pc_od_p!="") || ($sel_vis_pc_os_p!="") ||
			($sel_vis_pc_od_p_2!="") || ($sel_vis_pc_os_p_2!="") ||
			($sel_vis_pc_od_p_3!="") || ($sel_vis_pc_os_p_3!="")
			){
				$pc2gp3_prismod[]= (!empty($sel_vis_pc_od_p_2)) ? "P" : "";				
				$pc2gp3_prismod[]="$sel_vis_pc_od_p_2";
			}
			if((($sel_vis_pc_od_prism!="" && $sel_vis_pc_od_p != "") || ($sel_vis_pc_os_prism!="" && $sel_vis_pc_os_p != "")) ||
			(($sel_vis_pc_od_prism_2!=""  && $sel_vis_pc_od_p_2 != "" ) || ($sel_vis_pc_os_prism_2!=""  && $sel_vis_pc_os_p_2 != "")) ||
			(($sel_vis_pc_od_prism_3!="" && $sel_vis_pc_od_p_3 != "") || ($sel_vis_pc_os_prism_3!=""  && $sel_vis_pc_os_p_3 != ""))
			){
				$pc2gp3_prismod[]=(!empty($sel_vis_pc_od_prism_2) && !empty($sel_vis_pc_od_slash_2)) ? "<img src='pic_vision_pc.jpg'/>" : "";
				if(!empty($sel_vis_pc_od_prism_2) && !empty($sel_vis_pc_od_slash_2)){$pc2gp3_prismod[]="$sel_vis_pc_od_prism_2";}
			}
			if(($sel_vis_pc_od_slash!="") || ($sel_vis_pc_os_slash!="") ||
			($sel_vis_pc_od_slash_2!="") || ($sel_vis_pc_os_slash_2!="") ||
			($sel_vis_pc_od_slash_3!="") || ($sel_vis_pc_os_slash_3!="")
			){
				$pc2gp3_prismod[]= (!empty($sel_vis_pc_od_slash_2)) ? "<img src='pic_vision_pc.jpg'/>" : "";
				$pc2gp3_prismod[]="$sel_vis_pc_od_slash_2";
			}
			if(($sel_vis_pc_od_sel_2!="" && $sel_vis_pc_od_slash != "") || ($sel_vis_pc_os_sel_2!="" && $sel_vis_pc_os_slash != "") ||
			($sel_vis_pc_od_sel_2_2!="" && $sel_vis_pc_od_slash_2 != "") || ($sel_vis_pc_os_sel_2_2!="" && $sel_vis_pc_os_slash_2 != "") ||
			($sel_vis_pc_od_sel_2_3!="" && $sel_vis_pc_od_slash_3 != "" ) || ($sel_vis_pc_os_sel_2_3!="" && $sel_vis_pc_os_slash_3 != "")
			){
				$pc2gp3_prismod[]="$sel_vis_pc_od_sel_2_2";
			}																								
		}
		?>
		<td class="text_value" style="width:165px"><?php print implode("&nbsp;",$pc2gp3_prismod);?></td>
<?php
$haveValue=0;
if($sel_vis_pc_od_sel_1_2!="" &&($txt_vis_pc_od_s_2!=""||$txt_vis_pc_od_c_2!=""||$txt_vis_pc_od_a_2!="" || $txt_vis_pc_od_add_2!="")){
	$overrefraction2od = array();
	$overrefraction2od[]="";
	if($chk_pc_near_2 <> "Over Refraction"){	
		if($txt_vis_pc_od_near_txt_2!=""){
			//$overrefraction2od[]="OD";
			//$overrefraction2od[]="$txt_vis_pc_od_near_txt_2";
		}
	}else{
		 if($txt_vis_pc_od_overref_s_2!="" || $txt_vis_pc_od_overref_c_2!="" || $txt_vis_pc_od_overref_a_2!="" || $txt_vis_pc_od_overref_v_2!="" ){
		 	$haveValue=1;
		 }
		 if($txt_vis_pc_od_overref_s_2!=""){
			$overrefraction2od[]="S";
			$overrefraction2od[]="$txt_vis_pc_od_overref_s_2";
		 }
		 if($txt_vis_pc_od_overref_c_2!=""){
			$overrefraction2od[]="C"; 
			$overrefraction2od[]="$txt_vis_pc_od_overref_c_2";
		 }
		 if($txt_vis_pc_od_overref_a_2!=""){
			$overrefraction2od[]="A"; 
			 $overrefraction2od[]="$txt_vis_pc_od_overref_a_2";
		 }
		 if($txt_vis_pc_od_overref_v_2!="" && $txt_vis_pc_od_overref_v_2!="20/"){
			$overrefraction2od[]="V" ;
			$overrefraction2od[]=" $txt_vis_pc_od_overref_v_2";
		}
	}
?>
	<td class='text_value'><?php if($haveValue==1){odLable(); print implode("&nbsp;",$overrefraction2od);}?></td>
<?php 
}
?>
	</tr>
	<!-- OD -->
	<!-- OS -->
	<tr height="20">
	<?php
		if(
			($sel_vis_pc_os_sel_1!="" && $txt_vis_pc_os_s!="") ||
			($sel_vis_pc_os_sel_1_2!="" && $txt_vis_pc_os_s_2!="") ||
			($sel_vis_pc_os_sel_1_3!="" && $txt_vis_pc_os_s_3!="")
		){
			$pc2gp1_os[]="$sel_vis_pc_os_sel_1_2";
			
		}
		if(
			($sel_vis_pc_os_sel_1!="" || $txt_vis_pc_os_s!="") ||
			($sel_vis_pc_os_sel_1_2!="" || $txt_vis_pc_os_s_2!="") ||
			($sel_vis_pc_os_sel_1_3!="" || $txt_vis_pc_os_s_3!="")
		){
			if($txt_vis_pc_os_s_2!="" and $sel_vis_pc_os_sel_1_2!=""){
				$pc2gp1_os[]="S"; 
				$pc2gp1_os[]= "$txt_vis_pc_os_s_2";
			} 
		}
		if(
			($sel_vis_pc_os_sel_1!="" || $txt_vis_pc_os_c!="") ||
			($sel_vis_pc_os_sel_1_2!="" || $txt_vis_pc_os_c_2!="") ||
			($sel_vis_pc_os_sel_1_3!="" || $txt_vis_pc_os_c_3!="")
		){
			if($txt_vis_pc_os_c_2!="" and $sel_vis_pc_os_sel_1_2!=""){
				$pc2gp1_os[]="C";
			 	$pc2gp1_os[]="$txt_vis_pc_os_c_2";
			}
		}
		if(($sel_vis_pc_os_sel_1!="" || $txt_vis_pc_os_a!="") ||
		($sel_vis_pc_os_sel_1_2!="" || $txt_vis_pc_os_a_2!="") ||
		($sel_vis_pc_os_sel_1_3!="" || $txt_vis_pc_os_a_3!="")
		){			
			if($txt_vis_pc_os_a_2!="" and $sel_vis_pc_os_sel_1_2!=""){
				$pc2gp1_os[]="A";
				$pc2gp1_os[]="".$txt_vis_pc_os_a_2."&#176;";
			}
		}
	?>
		<td class="text_value" style="width:229px;"><?php osLable(); print implode("&nbsp;",$pc2gp1_os);?></td>
	<?php
		if(
			($txt_vis_pc_os_add_2!="" and ($txt_vis_pc_os_s_2!="" || $txt_vis_pc_os_c_2!="" || $txt_vis_pc_os_a_2!=""))		
		){
			 if(strlen($txt_vis_pc_os_add_2)<>1){
				 $pc2gp2_addos[]="Add";
				 $pc2gp2_addos[]="$txt_vis_pc_os_add_2"; 
			 }
		}
		?>
		<td class="text_value" style="width:74px"><?php  print implode("&nbsp;",$pc2gp2_addos);?></td>
		<!-- Prism -->
		<?php 
		if(($elem_prismPc1 == "1") || ($elem_prismPc2 == "1") || ($elem_prismPc3 == "1")){
			if(($sel_vis_pc_od_p!="") || ($sel_vis_pc_os_p!="") ||
			($sel_vis_pc_od_p_2!="") || ($sel_vis_pc_os_p_2!="") ||
			($sel_vis_pc_od_p_3!="") || ($sel_vis_pc_os_p_3!="")
			){
				$pc2gp3_prismos[] = (!empty($sel_vis_pc_os_p_2)) ? "P" : "";
				$pc2gp3_prismos[] = "$sel_vis_pc_os_p_2";
			}
			if((($sel_vis_pc_od_prism!="" && $sel_vis_pc_od_p != "") || ($sel_vis_pc_os_prism!="" && $sel_vis_pc_os_p != "")) ||
			(($sel_vis_pc_od_prism_2!=""  && $sel_vis_pc_od_p_2 != "" ) || ($sel_vis_pc_os_prism_2!=""  && $sel_vis_pc_os_p_2 != "")) ||
			(($sel_vis_pc_od_prism_3!="" && $sel_vis_pc_od_p_3 != "") || ($sel_vis_pc_os_prism_3!=""  && $sel_vis_pc_os_p_3 != ""))
			){
				$pc2gp3_prismos[]= (!empty($sel_vis_pc_os_prism_2) && !empty($sel_vis_pc_os_slash_2)) ? "<img src='pic_vision_pc.jpg'/>" : "";
				if(!empty($sel_vis_pc_os_prism_2) && !empty($sel_vis_pc_os_slash_2)){$pc2gp3_prismos[]="$sel_vis_pc_os_prism_2";}
			}
			if(($sel_vis_pc_od_slash!="") || ($sel_vis_pc_os_slash!="") ||
			($sel_vis_pc_od_slash_2!="") || ($sel_vis_pc_os_slash_2!="") ||
			($sel_vis_pc_od_slash_3!="") || ($sel_vis_pc_os_slash_3!="")
			){
				$pc2gp3_prismos[]=(!empty($sel_vis_pc_os_slash_2)) ? "<img src='pic_vision_pc.jpg'/>" : "";
				$pc2gp3_prismos[]="$sel_vis_pc_os_slash_2";
				
			}
			if(($sel_vis_pc_od_sel_2!="" && $sel_vis_pc_od_slash != "") || ($sel_vis_pc_os_sel_2!="" && $sel_vis_pc_os_slash != "") ||
			($sel_vis_pc_od_sel_2_2!="" && $sel_vis_pc_od_slash_2 != "") || ($sel_vis_pc_os_sel_2_2!="" && $sel_vis_pc_os_slash_2 != "") ||
			($sel_vis_pc_od_sel_2_3!="" && $sel_vis_pc_od_slash_3 != "" ) || ($sel_vis_pc_os_sel_2_3!="" && $sel_vis_pc_os_slash_3 != "")
			){
				$pc2gp3_prismos[]="$sel_vis_pc_os_sel_2_2";				
			}
		}
		?>
		<td class="text_value" style="width:165px"><?php  print implode("&nbsp;",$pc2gp3_prismos);?></td>
		<?php
		if($sel_vis_pc_od_sel_1_2!="" &&($txt_vis_pc_od_s_2!=""||$txt_vis_pc_od_c_2!=""||$txt_vis_pc_od_a_2!="" || $txt_vis_pc_od_add_2!="")){
			$overrefraction2os = array();
			$overrefraction2os[]="";
			if($chk_pc_near_2 <> "Over Refraction"){	
				if($txt_vis_pc_os_near_txt_2!=""){
					/*$overrefraction2os[]="OS";*/ 
					//$overrefraction2os[]="$txt_vis_pc_os_near_txt_2";		
				}		
			}else{		
				$haveValue=0;
				 if($txt_vis_pc_os_overref_s_2!="" || $txt_vis_pc_os_overref_c_2!="" || $txt_vis_pc_os_overref_a_2!="" || $txt_vis_pc_os_overref_v_2!=""){
					$haveValue=1;
				 }
				 if($txt_vis_pc_os_overref_s_2!=""){
					$overrefraction2os[]="S";
					$overrefraction2os[]="".trim($txt_vis_pc_os_overref_s_2)."";		
				 } 
				 if($txt_vis_pc_os_overref_c_2!=""){
					$overrefraction2os[]="C";
					$overrefraction2os[]="$txt_vis_pc_os_overref_c_2";		
				 }
				 if($txt_vis_pc_os_overref_a_2!=""){
					$overrefraction2os[]="A";
					$overrefraction2os[]="$txt_vis_pc_os_overref_a_2";		
				 }
				 if($txt_vis_pc_os_overref_v_2!="" && $txt_vis_pc_os_overref_v_2!="20/"){
					$overrefraction2os[]="V";
					$overrefraction2os[]="$txt_vis_pc_os_overref_v_2";
				 }		
			}
			?>
				<td class="text_value"><?php if($haveValue==1){osLable(); print implode("&nbsp;",$overrefraction2os);}?></td>
			<?php 
		}
		?>
  </tr>
</table>
<!-- End  2nd Pc-->
<?php 
}
?>
<!-- 2nd PC -->
<!-- End OverRefraction 2nd Pc-->
<?php 
if($sel_vis_pc_od_sel_1_3!="" &&($txt_vis_pc_od_s_3!=""||$txt_vis_pc_od_c_3!=""||$txt_vis_pc_od_a_3!="" || $txt_vis_pc_od_add_3!="")){
?>
<!-- 3rd PC -->
<table style="width:100%;" class="paddingTop" cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td class='tb_subheading'>3rd</td>
		<td  class='tb_subheading'>&nbsp;</td>
		<?php 
			$prismTxt3 = '';	
			if($elem_prismPc32){
				$prismTxt3 = 'Prism';	
			}				
			$overRefractionTxt3 = "";
			if($chk_pc_near_3 == "Over Refraction"){
				$overRefractionTxt3 = "Over Refraction";
			}
		?>
		<td  class='tb_subheading'><?php echo $prismTxt3; ?></td>
		<td class='tb_subheading' style="width:484px;" ><?php echo $overRefractionTx3; ?></td>
	</tr>
	<tr>
		<?php 
			$pc3gp1_od = $pc3gp1_os = $pc3gp2_addod = $pc3gp2_addos = $pc3gp2_prismod = $pc3gp2_prismos = array();
			$pc3gp1_od[]="";
			$pc3gp1_os[]="";
			$pc3gp2_addod[]="";
			$pc3gp2_addos[]="";
			$pc3gp2_prismod[]="";
			$pc3gp2_prismos[]="";
			if(($sel_vis_pc_od_sel_1!="" && $txt_vis_pc_od_s!="" || ($sel_vis_pc_os_sel_1!="" && $txt_vis_pc_os_s!="")) ||
			($sel_vis_pc_od_sel_1_2!="" && $txt_vis_pc_od_s_2!="" || ($sel_vis_pc_os_sel_1_2!="" && $txt_vis_pc_os_s_2!="")) ||
			($sel_vis_pc_od_sel_1_3!="" && $txt_vis_pc_od_s_3!="" || ($sel_vis_pc_os_sel_1_3!="" && $txt_vis_pc_os_s_3!=""))
			){
				$pc3gp1_od[]="$sel_vis_pc_od_sel_1_3";
			}
			if(($txt_vis_pc_od_s!="" || $txt_vis_pc_os_s!="") ||
			($txt_vis_pc_od_s_2!="" || $txt_vis_pc_os_s_2!="") ||
			($txt_vis_pc_od_s_3!="" || $txt_vis_pc_os_s_3!="")
			){
				 if($txt_vis_pc_od_s_3!=""){$pc3gp1_od[]="S"; }
				 $pc3gp1_od[]="$txt_vis_pc_od_s_3";
			}
			if(($txt_vis_pc_od_c!="" || $txt_vis_pc_os_c!="") ||
			($txt_vis_pc_od_c_2!="" || $txt_vis_pc_os_c_2!="") ||
			($txt_vis_pc_od_c_3!="" || $txt_vis_pc_os_c_3!="")
			){
				 if($txt_vis_pc_od_c_3!=""){$pc3gp1_od[]="C";  }
				 $pc3gp1_od[]="".$txt_vis_pc_od_c_3."";
			}
			if(($txt_vis_pc_od_a!="" || $txt_vis_pc_os_a!="") ||
			($txt_vis_pc_od_a_2!="" || $txt_vis_pc_os_a_2!="") ||
			($txt_vis_pc_od_a_3!="" || $txt_vis_pc_os_a_3!="")
			){
				if($txt_vis_pc_od_a_3!="" ){$pc3gp1_od[]="A"; }
				if($txt_vis_pc_od_a_3!="" ){$pc3gp1_od[]="".$txt_vis_pc_od_a_3."&#176;";}
			}
	?>
		<td class="text_value" style="width:232px;"><?php odLable(); print implode("&nbsp;",$pc3gp1_od);?></td>
	<?php	
	if(
		($txt_vis_pc_od_add_3!="" and ($txt_vis_pc_od_s_3!="" || $txt_vis_pc_od_c_3!="" || $txt_vis_pc_od_a_3!=""))
	){
		if(strlen($txt_vis_pc_od_add_3)<>1){
			$pc3gp2_addod[]="Add";
			$pc3gp2_addod[]="$txt_vis_pc_od_add_3"; 
		}
	}
	?>
		<td class="text_value" style="width:74px"><?php  print implode("&nbsp;",$pc3gp2_addod);?></td>
	<?php
		if(($elem_prismPc1 == "1") || ($elem_prismPc2 == "1") || ($elem_prismPc3 == "1")){
			if(($sel_vis_pc_od_p!="") || ($sel_vis_pc_os_p!="") ||
			($sel_vis_pc_od_p_2!="") || ($sel_vis_pc_os_p_2!="") ||
			($sel_vis_pc_od_p_3!="") || ($sel_vis_pc_os_p_3!="")
			){
			$pc3gp2_prismod[]= (!empty($sel_vis_pc_od_p_3)) ? "P" : "";
			$pc3gp2_prismod[]="$sel_vis_pc_od_p_3";
		}
		if((($sel_vis_pc_od_prism!="" && $sel_vis_pc_od_p != "") || ($sel_vis_pc_os_prism!="" && $sel_vis_pc_os_p != "")) ||
		(($sel_vis_pc_od_prism_2!=""  && $sel_vis_pc_od_p_2 != "" ) || ($sel_vis_pc_os_prism_2!=""  && $sel_vis_pc_os_p_2 != "")) ||
		(($sel_vis_pc_od_prism_3!="" && $sel_vis_pc_od_p_3 != "") || ($sel_vis_pc_os_prism_3!=""  && $sel_vis_pc_os_p_3 != ""))
		){
			$pc3gp2_prismod[]=(!empty($sel_vis_pc_od_prism_3) && !empty($sel_vis_pc_od_slash_3)) ? "<img src='pic_vision_pc.jpg'/>" : "";
			if(!empty($sel_vis_pc_od_prism_3) && !empty($sel_vis_pc_od_slash_3)){$pc3gp2_prismod[]="$sel_vis_pc_od_prism_3";}
		}
		if(($sel_vis_pc_od_slash!="") || ($sel_vis_pc_os_slash!="") ||
		($sel_vis_pc_od_slash_2!="") || ($sel_vis_pc_os_slash_2!="") ||
		($sel_vis_pc_od_slash_3!="") || ($sel_vis_pc_os_slash_3!="")
		){
			$pc3gp2_prismod[]=(!empty($sel_vis_pc_od_slash_3)) ? "<img src='pic_vision_pc.jpg'/>" : "";
			$pc3gp2_prismod[]="$sel_vis_pc_od_slash_3";
		}
		if(($sel_vis_pc_od_sel_2!="" && $sel_vis_pc_od_slash != "") || ($sel_vis_pc_os_sel_2!="" && $sel_vis_pc_os_slash != "") ||
		($sel_vis_pc_od_sel_2_2!="" && $sel_vis_pc_od_slash_2 != "") || ($sel_vis_pc_os_sel_2_2!="" && $sel_vis_pc_os_slash_2 != "") ||
		($sel_vis_pc_od_sel_2_3!="" && $sel_vis_pc_od_slash_3 != "" ) || ($sel_vis_pc_os_sel_2_3!="" && $sel_vis_pc_os_slash_3 != "")
		){
			
			$pc3gp2_prismod[]="$sel_vis_pc_od_sel_2_3";
			
		}
	}
	?>
		<td class="text_value" style="width:159px;"><?php  print implode("&nbsp;",$pc3gp2_prismod);?></td>
	<?php 
	if($sel_vis_pc_od_sel_1_3!="" &&($txt_vis_pc_od_s_3!=""||$txt_vis_pc_od_c_3!=""||$txt_vis_pc_od_a_3!="" || $txt_vis_pc_od_add_3!="")){
		$overrefraction3od[] = "";
		if($chk_pc_near_3 <> "Over Refraction"){	
			 if($txt_vis_pc_od_near_txt_3!=""){
			 /*$overrefraction3od[]="OD";*/ 
			 //$overrefraction3od[]="$txt_vis_pc_od_near_txt_3";
			 }
		}else{	
			$haveValue=0;
			 if($txt_vis_pc_od_overref_s_3!="" || $txt_vis_pc_od_overref_c_3!="" || $txt_vis_pc_od_overref_a_3!="" || $txt_vis_pc_od_overref_v_3!="" ){
				$haveValue=1;
			 }
			if($txt_vis_pc_od_overref_s_3!=""){
				$overrefraction3od[]="S";
				$overrefraction3od[]="$txt_vis_pc_od_overref_s_3";
			}
			if($txt_vis_pc_od_overref_c_3!=""){
				$overrefraction3od[]="C"; 
				$overrefraction3od[]="$txt_vis_pc_od_overref_c_3";
			}
			if($txt_vis_pc_od_overref_a_3!=""){$overrefraction3od[]="A"; 
				$overrefraction3od[]="$txt_vis_pc_od_overref_a_3";
			}
			if($txt_vis_pc_od_overref_v_3!="" && $txt_vis_pc_od_overref_v_3!="20/"){
				$overrefraction3od[]="V"; 
				$overrefraction3od[]="$txt_vis_pc_od_overref_v_3";
			}	
		}
		?>
			<td class="text_value" style="width:487px;"><?php if($haveValue==1){odLable(); print implode("&nbsp;",$overrefraction3od);}?></td>
		<?php 
		} 
		?>
	</tr>
	<tr>
		<?php
		if(
			($sel_vis_pc_os_sel_1!="" && $txt_vis_pc_os_s!="") ||
			($sel_vis_pc_os_sel_1_2!="" && $txt_vis_pc_os_s_2!="") ||
			($sel_vis_pc_os_sel_1_3!="" && $txt_vis_pc_os_s_3!="")
		){
			$pc3gp1_os[]="$sel_vis_pc_os_sel_1_3";
		}
		if(
			($sel_vis_pc_os_sel_1!="" || $txt_vis_pc_os_s!="") ||
			($sel_vis_pc_os_sel_1_2!="" || $txt_vis_pc_os_s_2!="") ||
			($sel_vis_pc_os_sel_1_3!="" || $txt_vis_pc_os_s_3!="")
		){
			 if($txt_vis_pc_os_s_3!="" and $sel_vis_pc_os_sel_1_3!=""){
			 	$pc3gp1_os[]="S"; 
			 	$pc3gp1_os[]="$txt_vis_pc_os_s_3";
			 } 
		}
		if(
			($sel_vis_pc_os_sel_1!="" || $txt_vis_pc_os_c!="") ||
			($sel_vis_pc_os_sel_1_2!="" || $txt_vis_pc_os_c_2!="") ||
			($sel_vis_pc_os_sel_1_3!="" || $txt_vis_pc_os_c_3!="")
		){
			 if($txt_vis_pc_os_c_3!="" and $sel_vis_pc_os_sel_1_3!=""){
				 $pc3gp1_os[]="C"; 
				 $pc3gp1_os[]="$txt_vis_pc_os_c_3";
			 }
		}
		if(
			($sel_vis_pc_os_sel_1!="" || $txt_vis_pc_os_a!="") ||
			($sel_vis_pc_os_sel_1_2!="" || $txt_vis_pc_os_a_2!="") ||
			($sel_vis_pc_os_sel_1_3!="" || $txt_vis_pc_os_a_3!="")
		){
			 if($txt_vis_pc_os_a_3!="" and $sel_vis_pc_os_sel_1_3!=""){
			 	$pc3gp1_os[]="A";
				$pc3gp1_os[]="".$txt_vis_pc_os_a_3."&#176;";
			}
		}
	?>
		<td class="text_value" style="width:232px;"><?php osLable(); print implode("&nbsp;",$pc3gp1_os);?></td>
	<?php
		if(
			($txt_vis_pc_os_add_3!="" and ($txt_vis_pc_os_s_3!="" || $txt_vis_pc_os_c_3!="" || $txt_vis_pc_os_a_3!=""))
		){
			 if(strlen($txt_vis_pc_os_add_3)<>1){
				$pc3gp2_addos[]="Add";
				$pc3gp2_addos[]="$txt_vis_pc_os_add_3"; 
			}
		}
	?>
		<td class="text_value" style="width:74px"><?php  print implode("&nbsp;",$pc3gp2_addos);?></td>
	<?php
		if(($elem_prismPc1 == "1") || ($elem_prismPc2 == "1") || ($elem_prismPc3 == "1")){
			if(($sel_vis_pc_od_p!="") || ($sel_vis_pc_os_p!="") ||
			($sel_vis_pc_od_p_2!="") || ($sel_vis_pc_os_p_2!="") ||
			($sel_vis_pc_od_p_3!="") || ($sel_vis_pc_os_p_3!="")
			){
				$pc3gp2_prismos[]=(!empty($sel_vis_pc_os_p_3)) ? "P" : "";
				
				$pc3gp2_prismos[]="$sel_vis_pc_os_p_3";
				
			}
			if((($sel_vis_pc_od_prism!="" && $sel_vis_pc_od_p != "") || ($sel_vis_pc_os_prism!="" && $sel_vis_pc_os_p != "")) ||
			(($sel_vis_pc_od_prism_2!=""  && $sel_vis_pc_od_p_2 != "" ) || ($sel_vis_pc_os_prism_2!=""  && $sel_vis_pc_os_p_2 != "")) ||
			(($sel_vis_pc_od_prism_3!="" && $sel_vis_pc_od_p_3 != "") || ($sel_vis_pc_os_prism_3!=""  && $sel_vis_pc_os_p_3 != ""))
			){
				$pc3gp2_prismos[]= (!empty($sel_vis_pc_os_prism_3) && !empty($sel_vis_pc_os_slash_3)) ? "<img src='pic_vision_pc.jpg'/>" : "";
				 if(!empty($sel_vis_pc_os_prism_3) && !empty($sel_vis_pc_os_slash_3)){$pc3gp2_prismos[]="$sel_vis_pc_os_prism_3";}
			}
			if(($sel_vis_pc_od_slash!="") || ($sel_vis_pc_os_slash!="") ||
			($sel_vis_pc_od_slash_2!="") || ($sel_vis_pc_os_slash_2!="") ||
			($sel_vis_pc_od_slash_3!="") || ($sel_vis_pc_os_slash_3!="")
			){
				$pc3gp2_prismos[]=(!empty($sel_vis_pc_os_slash_3)) ?"<img src='pic_vision_pc.jpg'/>" : "";
				$pc3gp2_prismos[]="$sel_vis_pc_os_slash_3";
			}
			if(($sel_vis_pc_od_sel_2!="" && $sel_vis_pc_od_slash != "") || ($sel_vis_pc_os_sel_2!="" && $sel_vis_pc_os_slash != "") ||
			($sel_vis_pc_od_sel_2_2!="" && $sel_vis_pc_od_slash_2 != "") || ($sel_vis_pc_os_sel_2_2!="" && $sel_vis_pc_os_slash_2 != "") ||
			($sel_vis_pc_od_sel_2_3!="" && $sel_vis_pc_od_slash_3 != "" ) || ($sel_vis_pc_os_sel_2_3!="" && $sel_vis_pc_os_slash_3 != "")
			){
				$pc3gp2_prismos[]="$sel_vis_pc_os_sel_2_3";				
			}
		}
	?>
		<td class="text_value" style="width:159px;"><?php  print implode("&nbsp;",$pc3gp2_prismos);?></td> 
	<?php 
	if($sel_vis_pc_od_sel_1_3!="" &&($txt_vis_pc_od_s_3!=""||$txt_vis_pc_od_c_3!=""||$txt_vis_pc_od_a_3!="" || $txt_vis_pc_od_add_3!="")){
		$overrefraction3os = array();
		$overrefraction3os[]="";
		if($chk_pc_near_3 <> "Over Refraction"){		
			 if($txt_vis_pc_os_near_txt_3!=""){
				//$overrefraction3os[]="OS";
				//$overrefraction3os[]="$txt_vis_pc_os_near_txt_3";
			}
		}else{
			$haveValue=0;
			 if($txt_vis_pc_os_overref_s_3!="" || $txt_vis_pc_os_overref_c_3!="" || $txt_vis_pc_os_overref_a_3!="" || $txt_vis_pc_os_overref_v_3!=""){
				$haveValue=1;
			 }
			 if($txt_vis_pc_os_overref_s_3!=""){
				$overrefraction3os[]="S"; 
				$overrefraction3os[]="$txt_vis_pc_os_overref_s_3";
			 }
			 if($txt_vis_pc_os_overref_c_3!=""){
				$overrefraction3os[]="C"; 
				$overrefraction3os[]="$txt_vis_pc_os_overref_c_3";
			 }
			 if($txt_vis_pc_os_overref_a_3!=""){
				$overrefraction3os[]="A";
				$overrefraction3os[]="$txt_vis_pc_os_overref_a_3";
			 }
			 if($txt_vis_pc_os_overref_v_3!="" && $txt_vis_pc_os_overref_v_3!="20/" ){
				$overrefraction3os[]="V"; 
				$overrefraction3os[]="$txt_vis_pc_os_overref_v_3";
			 }
		}
		?>
			<td class='text_value'><?php if($haveValue==1){osLable(); print implode("&nbsp;",$overrefraction3os);}?></td>
		<?php 
		} 
	?>
  </tr>									
</table>	
<!-- End 3rd PC -->		
<?php 
}
?>
<!-- End 3rd Pc overrefraction-->
<?php
//End Pc Code
//MR Code//
$show_mrval=false;
if($show_mrList1==true || $show_mrList2==true || $show_mrList3==true){
	$show_mrval=true;
}

if($show_mrval==true){
	/*$sqlVisionQry = imw_query("SELECT * FROM chart_vision WHERE patient_id = '$patient_id' AND form_id = '$form_id'");
	if(imw_num_rows($sqlVisionQry)>0){
		$sqlVisionRow = imw_fetch_assoc($sqlVisionQry);
		extract($sqlVisionRow);
	}*/
	
	//--
	$id_chart_vis_master=0;
	$sql = "SELECT *, c5.ex_desc as ex_desc_pam, c2.ex_desc as ex_desc_ak, c1.id AS id_chart_vis_master  
			FROM chart_vis_master c1
			LEFT JOIN chart_ak c2 ON c2.id_chart_vis_master = c1.id
			LEFT JOIN chart_exo c3 ON c3.id_chart_vis_master = c1.id
			LEFT JOIN chart_bat c4 ON c4.id_chart_vis_master = c1.id
			LEFT JOIN chart_pam c5 ON c5.id_chart_vis_master = c1.id
			WHERE c1.patient_id = '".$patient_id."' AND c1.form_id = '".$form_id."' ";
	$row = sqlQuery($sql);
	if($row!=false){
		$statusElements = $row["status_elements"];
		$id_chart_vis_master = $row["id_chart_vis_master"];
		
		//PAM --			
		$visPam = (strpos($statusElements, "elem_visPam=1") !== false) ? $row["pam"] : "";
		$vis_pam_od_txt_1 = (strpos($statusElements, "elem_visPamOdTxt1=1") !== false) ? $row["txt1_od"] : "";
		$vis_pam_os_txt_1 = (strpos($statusElements, "elem_visPamOsTxt1=1") !== false) ? $row["txt1_os"] : "";
		$vis_pam_ou_txt_1 = (strpos($statusElements, "elem_visPamOuTxt1=1") !== false) ? $row["txt1_ou"] : "";
		$vis_pam_od_sel_2 = (strpos($statusElements, "elem_visPamOdSel2=1") !== false) ? $row["sel2"] : "";
		$vis_pam_od_txt_2 = (strpos($statusElements, "elem_visPamOdTxt2=1") !== false) ? $row["txt2_od"] : "";
		$vis_pam_os_txt_2 = (strpos($statusElements, "elem_visPamOsTxt2=1") !== false) ? $row["txt2_os"] : "";
		$vis_pam_ou_txt_2 = (strpos($statusElements, "elem_visPamOuTxt2=1") !== false) ? $row["txt2_ou"] : "";
		$vis_pam_desc = (strpos($statusElements, "elem_pamDesc=1") !== false) ? $row["ex_desc_pam"] : "";
		//PAM --
		
		//BAT --
		$txt_vis_bat_nl_od = (strpos($statusElements, "elem_visBatNlOd=1") !== false)?$row["nl_od"]:"";
		$txt_vis_bat_low_od =(strpos($statusElements, "elem_visBatLowOd=1") !== false)? $row["l_od"]:"";
		$txt_vis_bat_med_od = (strpos($statusElements, "elem_visBatMedOd=1") !== false)? $row["m_od"]:"";
		$txt_vis_bat_high_od =(strpos($statusElements, "elem_visBatHighOd=1") !== false)?$row["h_od"]:"";
		$txt_vis_bat_nl_os = (strpos($statusElements, "elem_visBatNlOs=1") !== false)?$row["nl_os"]:"";
		$txt_vis_bat_low_os = (strpos($statusElements, "elem_visBatLowOs=1") !== false)?$row["l_os"]:"";
		$txt_vis_bat_med_os =(strpos($statusElements, "elem_visBatMedOs=1") !== false)?$row["m_os"]:"";
		$txt_vis_bat_high_os =(strpos($statusElements, "elem_visBatHighOs=1") !== false)?$row["h_os"]:"";
		
		$txt_vis_bat_nl_ou = (strpos($statusElements, "elem_visBatNlOu=1") !== false)?$row["nl_ou"]:"";
		$txt_vis_bat_low_ou = (strpos($statusElements, "elem_visBatLowOu=1") !== false)?$row["l_ou"]:"";
		$txt_vis_bat_med_ou =(strpos($statusElements, "elem_visBatMedOu=1") !== false)?$row["m_ou"]:"";
		$txt_vis_bat_high_ou =(strpos($statusElements, "elem_visBatHighOu=1") !== false)?$row["h_ou"]:"";
		$txt_vis_bat_desc =(strpos($statusElements, "elem_visBatDesc=1") !== false)?$row["vis_bat_desc"]:""; //removeExamDateStr($row[0]["vis_bat_desc"]);
		$txt_vis_bat_examdate = $row["examDateDistance"];//getExamDateStr($row[0]["vis_bat_desc"]);
		//BAT --
		
		//AK --
		$txt_vis_ak_od_k = (strpos($statusElements, "elem_visAkOdK=1") !== false)?$row["k_od"]:"";
		$txt_vis_ak_od_slash = (strpos($statusElements, "elem_visAkOdSlash=1") !== false)?$row["slash_od"]:"";
		$txt_vis_ak_od_x = (strpos($statusElements, "elem_visAkOdX=1") !== false)?$row["x_od"]:"";			
		$txt_vis_ak_os_k = (strpos($statusElements, "elem_visAkOsK=1") !== false)?$row["k_os"]:"";
		$txt_vis_ak_os_slash =(strpos($statusElements, "elem_visAkOsSlash=1") !== false)? $row["slash_os"]:"";
		$txt_vis_ak_os_x = (strpos($statusElements, "elem_visAkOsX=1") !== false)?$row["x_os"]:"";
		$txt_vis_ar_ak_desc =$row["ex_desc_ak"];//removeExamDateStr($row[0]["vis_ar_ak_desc"]) ;
		
		//Comments --
		//Check For old comments
		if(strpos($txt_vis_ar_ak_desc,"<~ED~>")!== false){
			$commentsArrayTmp=explode("<~ED~>",$txt_vis_ar_ak_desc);
			$txt_vis_ar_ak_desc=$commentsArrayTmp[0];
		}
		//Comments --
		//AK --		
	}

	//Acuity
	$sql = "SELECT * FROM chart_vis_master c1
			LEFT JOIN chart_acuity c2 ON c2.id_chart_vis_master = c1.id
			WHERE c1.patient_id = '".$patient_id."' AND c1.form_id = '".$form_id."' 
			ORDER BY sec_indx
			";
	$res = sqlStatement($sql);
	for($i=1; $row=sqlFetchArray($res); $i++){

		$sec_name = $row["sec_name"];
		$sec_indx = $row["sec_indx"];
		if($sec_name == "Distance"){
			${"sel_vis_dis_od_sel_".$sec_indx} =(strpos($statusElements, "elem_visDisOdTxt".$sec_indx."=1") !== false)?$row["sel_od"]:"";
			${"txt_vis_dis_od_txt_".$sec_indx} =(strpos($statusElements, "elem_visDisOdTxt".$sec_indx."=1") !== false)?$row["txt_od"]:"";
			${"sel_vis_dis_os_sel_".$sec_indx} =(strpos($statusElements, "elem_visDisOsTxt".$sec_indx."=1") !== false)? $row["sel_os"]:"";
			${"txt_vis_dis_os_txt_".$sec_indx} =(strpos($statusElements, "elem_visDisOsTxt".$sec_indx."=1") !== false)? $row["txt_os"]:"";
			${"sel_vis_dis_ou_sel_".$sec_indx} =(strpos($statusElements, "elem_visDisOuTxt".$sec_indx."=1") !== false)? $row["sel_ou"]:"";
			${"txt_vis_dis_ou_txt_".$sec_indx} =(strpos($statusElements, "elem_visDisOuTxt".$sec_indx."=1") !== false)? $row["txt_ou"]:"";
			$txt_vis_dis_near_desc = (strpos($statusElements, "elem_visNearOdTxt".$sec_indx."=1") !== false || strpos($statusElements, "elem_visNearOsTxt".$sec_indx."=1") !== false)?$row["ex_desc"]:"";
			
			//Comments --
			//Check For old comments
			if(strpos($txt_vis_dis_near_desc,"<~ED~>")!== false){
				$commentsArrayTmp=explode("<~ED~>",$txt_vis_dis_near_desc);
				$txt_vis_dis_near_desc=$commentsArrayTmp[0];
			}
			//Comments --
			
		}else if($sec_name == "Near"){
			${"sel_vis_near_od_sel_".$sec_indx} =(strpos($statusElements, "elem_visNearOdTxt".$sec_indx."=1") !== false)? $row["sel_od"]:"";
			${"txt_vis_near_od_txt_".$sec_indx} = (strpos($statusElements, "elem_visNearOdTxt".$sec_indx."=1") !== false)?$row["txt_od"]:"";				
			${"sel_vis_near_os_sel_".$sec_indx} = (strpos($statusElements, "elem_visNearOsTxt".$sec_indx."=1") !== false)?$row["sel_os"]:"";
			${"txt_vis_near_os_txt_".$sec_indx} = (strpos($statusElements, "elem_visNearOsTxt".$sec_indx."=1") !== false)?$row["txt_os"]:"";
			${"sel_vis_near_ou_sel_".$sec_indx} =(strpos($statusElements, "elem_visNearOuTxt".$sec_indx."=1") !== false)? $row["sel_ou"]:"";
			${"txt_vis_near_ou_txt_".$sec_indx} =(strpos($statusElements, "elem_visNearOuTxt".$sec_indx."=1") !== false)? $row["txt_ou"]:"";
			$txt_vis_near_desc =(strpos($statusElements, "elem_visNearDesc=1") !== false)?$row["ex_desc"]:"";// removeExamDateStr($row["vis_near_desc"]);
			$txt_vis_near_examdate = $row["examDateDistance "];
		
		}else if($sec_name == "Ad. Acuity"){
			$vis_dis_od_sel_3=(strpos($statusElements, "elem_visDisOdSel3=1") !== false) ? $row["sel_od"]: "";
			$vis_dis_od_txt_3=(strpos($statusElements, "elem_visDisOdTxt3=1") !== false) ? $row["txt_od"]: "";
			$vis_dis_os_sel_3=(strpos($statusElements, "elem_visDisOsSel3=1") !== false) ? $row["sel_os"]: "";
			$vis_dis_os_txt_3=(strpos($statusElements, "elem_visDisOsTxt3=1") !== false) ? $row["txt_os"]: "";
			$vis_dis_ou_sel_3=(strpos($statusElements, "elem_visDisOuSel3=1") !== false) ? $row["sel_ou"]: "";
			$vis_dis_ou_txt_3=(strpos($statusElements, "elem_visDisOuTxt3=1") !== false) ? $row["txt_ou"]: ""; 
			$vis_dis_act_3  =(strpos($statusElements, "elem_visDisAct3=1") !== false) ? htmlentities($row["ex_desc"]): "";
		}
	}

	if(!empty($id_chart_vis_master)){
		//sca
		$sql = "SELECT * FROM chart_sca WHERE id_chart_vis_master='".$id_chart_vis_master."' ";
		$res = sqlStatement($sql);
		for($i=1;$row=sqlFetchArray($res);$i++){
			
			$sec_name = $row["sec_name"];
			if($sec_name == "AR"){
			$txt_vis_ar_od_s =(strpos($statusElements, "elem_visArOdS=1") !== false)? $row["s_od"]:"";
			$txt_vis_ar_od_c =(strpos($statusElements, "elem_visArOdC=1") !== false)? $row["c_od"]:"";
			$txt_vis_ar_od_a = (strpos($statusElements, "elem_visArOdA=1") !== false)?$row["a_od"]:"";
			$sel_vis_ar_od_sel_1 =(strpos($statusElements, "elem_visArOdS=1") !== false)? $row["sel_od"]:"";
			$txt_vis_ar_os_s =(strpos($statusElements, "elem_visArOsS=1") !== false)? $row["s_os"]:"";
			$txt_vis_ar_os_c = (strpos($statusElements, "elem_visArOsC=1") !== false)?$row["c_os"]:"";
			$txt_vis_ar_os_a = (strpos($statusElements, "elem_visArOsA=1") !== false)?$row["a_os"]:"";
			$sel_vis_ar_os_sel_1 = (strpos($statusElements, "elem_visArOsA=1") !== false)?$row["sel_os"]:"";
			}else if($sec_name == "ARC"){
			$visCycArOdS =(strpos($statusElements, "elem_visArOdS=1") !== false)? $row["s_od"]:"";
			$visCycArOdC =(strpos($statusElements, "elem_visArOdC=1") !== false)? $row["c_od"]:"";
			$visCycArOdA = (strpos($statusElements, "elem_visArOdA=1") !== false)?$row["a_od"]:"";
			$visCycArOdSel1 =(strpos($statusElements, "elem_visArOdS=1") !== false)? $row["sel_od"]:"";
			$visCycArOsS =(strpos($statusElements, "elem_visArOsS=1") !== false)? $row["s_os"]:"";
			$visCycArOsC = (strpos($statusElements, "elem_visArOsC=1") !== false)?$row["c_os"]:"";
			$visCycArOsA = (strpos($statusElements, "elem_visArOsA=1") !== false)?$row["a_os"]:"";
			$visCycArOsSel1 = (strpos($statusElements, "elem_visArOsA=1") !== false)?$row["sel_os"]:"";	
				
			}
		
		}
		
		//PC/MR
		$sql = "SELECT 
				c1.*,
				c2.sph as sph_r, c2.cyl as cyl_r, c2.axs as axs_r, c2.ad as ad_r, c2.prsm_p as prsm_p_r, c2.prism as prism_r, c2.slash as slash_r, c2.sel_1 as sel_1_r, c2.sel_2 as sel_2_r, 
				c2.txt_1 as txt_1_r, c2.txt_2 as txt_2_r, c2.sel2v as sel2v_r,
				c2.ovr_s as ovr_s_r, c2.ovr_c as ovr_c_r, c2.ovr_v as ovr_v_r, c2.ovr_a as ovr_a_r,			
				c3.sph as sph_l, c3.cyl as cyl_l, c3.axs as axs_l, c3.ad as ad_l, c3.prsm_p as prsm_p_l, c3.prism as prism_l, c3.slash as slash_l, c3.sel_1 as sel_1_l, c3.sel_2 as sel_2_l, 
				c3.txt_1 as txt_1_l, c3.txt_2 as txt_2_l, c3.sel2v as sel2v_l,
				c3.ovr_s as ovr_s_l, c3.ovr_c as ovr_c_l, c3.ovr_v as ovr_v_l, c3.ovr_a as ovr_a_l,
				c4.status_elements as vis_statusElements
				FROM chart_vis_master c4
				LEFT JOIN chart_pc_mr c1 ON c1.id_chart_vis_master = c4.id
				LEFT JOIN chart_pc_mr_values c2 ON c1.id = c2.chart_pc_mr_id AND c2.site='OD'
				LEFT JOIN chart_pc_mr_values c3 ON c1.id = c3.chart_pc_mr_id AND c3.site='OS'				
				WHERE c4.form_id='".$form_id."' AND c4.patient_id = '".$patient_id."' AND c1.ex_type='PC' AND c1.delete_by='0'  
				Order By ex_number;
				";
		$rez = sqlStatement($sql);
		for($i=0; $row= sqlFetchArray($rez); $i++){
			$ex_num = $row["ex_number"];
			
			if($ex_num == "1"){ 
				$ex_num = ""; 
				$indx1 = "";
			}else{
				$indx1 = "_".$ex_num;
			}
			
			//Pc---
			$statusElements = $row["vis_statusElements"];
			${"chk_pc_near".$indx1} = $row["pc_near"];

			${"sel_vis_pc_od_sel_1".$indx1} = (strpos($statusElements, "elem_visPcOdSel1".$ex_num."=1") !== false)?$row["sel_1_r"]:"";
			${"txt_vis_pc_od_s".$indx1} = (strpos($statusElements, "elem_visPcOdS".$ex_num."=1") !== false)?$row["sph_r"]:"";
			${"txt_vis_pc_od_c".$indx1} = (strpos($statusElements, "elem_visPcOdC".$ex_num."=1") !== false)?$row["cyl_r"]:"";
			${"txt_vis_pc_od_a".$indx1} =(strpos($statusElements, "elem_visPcOdA".$ex_num."=1") !== false)?$row["axs_r"]:"";

			${"sel_vis_pc_od_p".$indx1} =(strpos($statusElements, "elem_visPcOsP".$ex_num."=1") !== false)? $row["prsm_p_r"]:"";
			${"sel_vis_pc_od_prism".$indx1} =(strpos($statusElements, "elem_visPcOdPrism".$ex_num."=1") !== false)? $row["prism_r"]:"";
			${"sel_vis_pc_od_slash".$indx1} = (strpos($statusElements, "elem_visPcOdSlash".$ex_num."=1") !== false)?$row["slash_r"]:"";
			${"sel_vis_pc_od_sel_2".$indx1} = (strpos($statusElements, "elem_visPcOdSel2".$ex_num."=1") !== false)?$row["sel_2_r"]:"";

			${"sel_vis_pc_os_sel_1".$indx1} = (strpos($statusElements, "elem_visPcOsSel1".$ex_num."=1") !== false)?$row["sel_1_l"]:"";
			${"txt_vis_pc_os_s".$indx1} = (strpos($statusElements, "elem_visPcOsS".$ex_num."=1") !== false)?$row["sph_l"]:"";
			${"txt_vis_pc_os_c".$indx1} =(strpos($statusElements, "elem_visPcOsC".$ex_num."=1") !== false)? $row["cyl_l"]:"";
			${"txt_vis_pc_os_a".$indx1} = (strpos($statusElements, "elem_visPcOsA".$ex_num."=1") !== false)?$row["axs_l"]:"";
			${"sel_vis_pc_os_p".$indx1} = (strpos($statusElements, "elem_visPcOsP".$ex_num."=1") !== false)?$row["prsm_p_l"]:"";
			${"sel_vis_pc_os_prism".$indx1} = (strpos($statusElements, "elem_visPcOsPrism".$ex_num."=1") !== false)?$row["prism_l"]:"";
			${"sel_vis_pc_os_slash".$indx1} = (strpos($statusElements, "elem_visPcOsSlash".$ex_num."=1") !== false)?$row["slash_l"]:"";
			${"sel_vis_pc_os_sel_2".$indx1} =(strpos($statusElements, "elem_visPcOsSel2".$ex_num."=1") !== false)? $row["sel_2_l"]:"";
			
			${"txt_vis_pc_od_near_txt".$indx1} = $row["txt_1_r"];
			${"txt_vis_pc_os_near_txt".$indx1} = $row["txt_1_l"];											

			${"txt_vis_pc_od_overref_s".$indx1} = (strpos($statusElements, "elem_visPcOdOverrefS".$ex_num."=1") !== false)?$row["ovr_s_r"]:"";
			${"txt_vis_pc_od_overref_c".$indx1} = (strpos($statusElements, "elem_visPcOdOverrefC".$ex_num."=1") !== false)?$row["ovr_c_r"]:"";
			${"txt_vis_pc_od_overref_a".$indx1} = (strpos($statusElements, "elem_visPcOdOverrefA".$ex_num."=1") !== false)?$row["ovr_a_r"]:"";
			${"txt_vis_pc_od_overref_v".$indx1} = (strpos($statusElements, "elem_visPcOdOverrefV".$ex_num."=1") !== false)?$row["ovr_v_r"]:"";
			${"txt_vis_pc_os_overref_s".$indx1} = (strpos($statusElements, "elem_visPcOsOverrefS".$ex_num."=1") !== false)?$row["ovr_s_l"]:"";
			${"txt_vis_pc_os_overref_c".$indx1} = (strpos($statusElements, "elem_visPcOsOverrefC".$ex_num."=1") !== false)?$row["ovr_c_l"]:"";
			${"txt_vis_pc_os_overref_a".$indx1} = (strpos($statusElements, "elem_visPcOsOverrefA".$ex_num."=1") !== false)?$row["ovr_a_l"]:"";
			${"txt_vis_pc_os_overref_v".$indx1} = (strpos($statusElements, "elem_visPcOsOverrefV".$ex_num."=1") !== false)?$row["ovr_v_l"]:"";
			
			${"txt_vis_pc_desc".$ex_num}=$row["ex_desc"];//
			${"txt_vis_pc_od_add".$indx1} = (strpos($statusElements, "elem_visPcOdAdd".$ex_num."=1") !== false)?$row["ad_r"]:"";
			${"txt_vis_pc_os_add".$indx1} =(strpos($statusElements, "elem_visPcOsAdd".$ex_num."=1") !== false)? $row["ad_l"]:"";		
		}
		
		$sql = "SELECT 
			c1.*,
			c2.sph as sph_r, c2.cyl as cyl_r, c2.axs as axs_r, c2.ad as ad_r, c2.prsm_p as prsm_p_r, c2.prism as prism_r, c2.slash as slash_r, c2.sel_1 as sel_1_r, c2.sel_2 as sel_2_r, 
			c2.txt_1 as txt_1_r, c2.txt_2 as txt_2_r, c2.sel2v as sel2v_r,
						
			c3.sph as sph_l, c3.cyl as cyl_l, c3.axs as axs_l, c3.ad as ad_l, c3.prsm_p as prsm_p_l, c3.prism as prism_l, c3.slash as slash_l, c3.sel_1 as sel_1_l, c3.sel_2 as sel_2_l, 
			c3.txt_1 as txt_1_l, c3.txt_2 as txt_2_l, c3.sel2v as sel2v_l,
			
			c4.status_elements 
			FROM chart_vis_master c4  
			LEFT JOIN chart_pc_mr c1 ON c1.id_chart_vis_master = c4.id
			LEFT JOIN chart_pc_mr_values c2 ON c1.id = c2.chart_pc_mr_id AND c2.site='OD'
			LEFT JOIN chart_pc_mr_values c3 ON c1.id = c3.chart_pc_mr_id AND c3.site='OS'				
			WHERE c4.form_id='".$form_id."' AND c4.patient_id = '".$patient_id."' AND c1.ex_type='MR' AND c1.ex_number IN (1,2) AND c1.delete_by='0'  
			Order By ex_number;
			";
			
		$rez = sqlStatement($sql);		
		for($i=0; $row= sqlFetchArray($rez); $i++){
			$ret_str="";
			$ex_num = $row["ex_number"];
			
			$indx1=$indx2=$indx3="";
			if($ex_num>1){
				$indx1="Other";
				$indx3="_given";
				if($ex_num>2){
					$indx2="_".$ex_num;	
				}
			}
			
			$statusElements = $row["status_elements"];
			$rd_vis_mr_none_given=(strpos($statusElements, "elem_mrNoneGiven".$ex_num."=1")!== false)?$row["mr_none_given"] : "" ;
			$providerIdOther_3=$row["provider_id"];
			$vis_mr_desc_3COMMENTS=$row["ex_desc"];
			$vis_mr_prism3COMMENTS=$row["prism_desc"];
			
			${($ex_num==3) ? "visMrOtherOdS_3" : "vis_mr_od".$indx3."_s"}=(strpos($statusElements, "elem_visMr".$indx1."OdS".$indx2."=1") !== false)?$row["sph_r"] : "" ;
			${($ex_num==3) ? "visMrOtherOdC_3" : "vis_mr_od".$indx3."_c"}=(strpos($statusElements, "elem_visMr".$indx1."OdC".$indx2."=1") !== false)?$row["cyl_r"] : "" ;
			${($ex_num==3) ? "visMrOtherOdA_3" : "vis_mr_od".$indx3."_a"}=(strpos($statusElements, "elem_visMr".$indx1."OdA".$indx2."=1") !== false)?$row["axs_r"] : "" ;
			${($ex_num==3) ? "visMrOtherOdAdd_3" : "vis_mr_od".$indx3."_add"}=(strpos($statusElements, "elem_visMr".$indx1."OdAdd".$indx2."=1") !== false)?$row["ad_r"] : "" ;
			${($ex_num==3) ? "visMrOtherOdTxt1_3" : "vis_mr_od".$indx3."_txt_1"}=(strpos($statusElements, "elem_visMr".$indx1."OdTxt1".$indx2."=1") !== false)?$row["txt_1_r"] : "" ;			
			${($ex_num==3) ? "visMrOtherOdTxt2_3" : "vis_mr_od".$indx3."_txt_2"}=(strpos($statusElements, "elem_visMr".$indx1."OdTxt2".$indx2."=1") !== false)?$row["txt_2_r"] : "" ;
			
			
			
			${($ex_num==3) ? "visMrOtherOdP_3" : "vis_mr_od".$indx3."_p"}=((strpos($statusElements, "elem_visMr".$indx1."OdP".$indx2."=1") !== false) || (strpos($statusElements, "elem_visMr".$indx1."OdSel1".$indx2."=1") !== false)
							|| (strpos($statusElements, "elem_visMr".$indx1."OdSlash".$indx2."=1") !== false) || (strpos($statusElements, "elem_visMr".$indx1."OdPrism".$indx2."=1") !== false)
							|| (strpos($statusElements, "elem_visMr".$indx1."OdTxt1".$indx2."=1") !== false)
							)?$row["prsm_p_r"] : "" ;
			${($ex_num==3) ? "visMrOtherOdPrism_3" : "vis_mr_od".$indx3."_prism"}=((strpos($statusElements, "elem_visMr".$indx1."OdP".$indx2."=1") !== false) || (strpos($statusElements, "elem_visMr".$indx1."OdSel1".$indx2."=1") !== false)
								|| (strpos($statusElements, "elem_visMr".$indx1."OdSlash".$indx2."=1") !== false) || (strpos($statusElements, "elem_visMr".$indx1."OdPrism".$indx2."=1") !== false)
								)?$row["prism_r"] : "" ;
			${($ex_num==3) ? "visMrOtherOdSlash_3" : "vis_mr_od".$indx3."_slash"}=((strpos($statusElements, "elem_visMr".$indx1."OdP".$indx2."=1") !== false) || (strpos($statusElements, "elem_visMr".$indx1."OdSel1".$indx2."=1") !== false)
								|| (strpos($statusElements, "elem_visMr".$indx1."OdSlash".$indx2."=1") !== false) || (strpos($statusElements, "elem_visMr".$indx1."OdPrism".$indx2."=1") !== false)
								)?$row["slash_r"] : "" ;
			${($ex_num==3) ? "visMrOtherOdSel1_3" : "vis_mr_od".$indx3."_sel_1"}=((strpos($statusElements, "elem_visMr".$indx1."OdP".$indx2."=1") !== false) || (strpos($statusElements, "elem_visMr".$indx1."OdSel1".$indx2."=1") !== false)
								|| (strpos($statusElements, "elem_visMr".$indx1."OdSlash".$indx2."=1") !== false) || (strpos($statusElements, "elem_visMr".$indx1."OdPrism".$indx2."=1") !== false)
								)?$row["sel_1_r"] : "" ;
			${($ex_num==3) ? "visMrOtherOdSel2_3" : "vis_mr_od".$indx3."_sel_2"}=(strpos($statusElements, "elem_visMr".$indx1."OdSel2".$indx2."=1") !== false)?$row["sel_2_r"] : "" ;
			//$visMrOtherOdSel2Vision_3=(strpos($statusElements, "elem_visMr".$indx1."OdSel2Vision".$indx2."=1") !== false)?$row["sel2v_r"] : "" ;
			
			${($ex_num==3) ? "visMrOtherOsS_3" : "vis_mr_os".$indx3."_s"}=(strpos($statusElements, "elem_visMr".$indx1."OsS".$indx2."=1") !== false)?$row["sph_l"] : "" ;
			${($ex_num==3) ? "visMrOtherOsC_3" : "vis_mr_os".$indx3."_c"}=(strpos($statusElements, "elem_visMr".$indx1."OsC".$indx2."=1") !== false)?$row["cyl_l"] : "" ;
			${($ex_num==3) ? "visMrOtherOsA_3" : "vis_mr_os".$indx3."_a"}=(strpos($statusElements, "elem_visMr".$indx1."OsA".$indx2."=1") !== false)?$row["axs_l"] : "" ;
			
			${($ex_num==3) ? "visMrOtherOsAdd_3" : "vis_mr_os".$indx3."_add"}=(strpos($statusElements, "elem_visMr".$indx1."OsAdd".$indx2."=1") !== false)?$row["ad_l"] : "" ;
			${($ex_num==3) ? "visMrOtherOsTxt1_3" : "vis_mr_os".$indx3."_txt_1"}=(strpos($statusElements, "elem_visMr".$indx1."OsTxt1".$indx2."=1") !== false)?$row["txt_1_l"] : "" ;
			${($ex_num==3) ? "visMrOtherOsTxt2_3" : "vis_mr_os".$indx3."_txt_2"}=(strpos($statusElements, "elem_visMr".$indx1."OsTxt2".$indx2."=1") !== false)?$row["txt_2_l"] : "" ;			
			
			${($ex_num==3) ? "visMrOtherOsP_3" : "vis_mr_os".$indx3."_p"}=((strpos($statusElements, "elem_visMr".$indx1."OsP".$indx2."=1") !== false) || (strpos($statusElements, "elem_visMr".$indx1."OsSel1".$indx2."=1") !== false)
								|| (strpos($statusElements, "elem_visMr".$indx1."OsSlash".$indx2."=1") !== false) || (strpos($statusElements, "elem_visMr".$indx1."OsPrism".$indx2."=1") !== false)
							)?$row["prsm_p_l"] : "" ;
			${($ex_num==3) ? "visMrOtherOsPrism_3" : "vis_mr_os".$indx3."_prism"}=((strpos($statusElements, "elem_visMr".$indx1."OsP".$indx2."=1") !== false) || (strpos($statusElements, "elem_visMr".$indx1."OsSel1".$indx2."=1") !== false)
								|| (strpos($statusElements, "elem_visMr".$indx1."OsSlash".$indx2."=1") !== false) || (strpos($statusElements, "elem_visMr".$indx1."OsPrism".$indx2."=1") !== false)
							)?$row["prism_l"] : "" ;
			${($ex_num==3) ? "visMrOtherOsSlash_3" : "vis_mr_os".$indx3."_slash"}=((strpos($statusElements, "elem_visMr".$indx1."OsP".$indx2."=1") !== false) || (strpos($statusElements, "elem_visMr".$indx1."OsSel1".$indx2."=1") !== false)
								|| (strpos($statusElements, "elem_visMr".$indx1."OsSlash".$indx2."=1") !== false) || (strpos($statusElements, "elem_visMr".$indx1."OsPrism".$indx2."=1") !== false)
							)?$row["slash_l"] : "" ;
			${($ex_num==3) ? "visMrOtherOsSel1_3" : "vis_mr_os".$indx3."_sel_1"}=((strpos($statusElements, "elem_visMr".$indx1."OsP".$indx2."=1") !== false) || (strpos($statusElements, "elem_visMr".$indx1."OsSel1".$indx2."=1") !== false)
								|| (strpos($statusElements, "elem_visMr".$indx1."OsSlash".$indx2."=1") !== false) || (strpos($statusElements, "elem_visMr".$indx1."OsPrism".$indx2."=1") !== false)
								)?$row["sel_1_l"] : "" ;
			${($ex_num==3) ? "visMrOtherOsSel2_3" : "vis_mr_os".$indx3."_sel_2"}=(strpos($statusElements, "elem_visMr".$indx1."OsSel2".$indx2."=1") !== false)?$row["sel_2_l"] : "" ;
			//$visMrOtherOsSel2Vision_3=(strpos($statusElements, "elem_visMr".$indx1."OsSel2Vision".$indx2."=1") !== false)?$row["sel2v_l"] : "" ;		
			//$elem_mr_type3 = (strpos($statusElements, "elem_mr_type".$ex_num."=1") !== false && !empty($row["vis_mr_type1"])) ?$row["mr_type"] : "" ;
			//if(!empty($elem_mr_type3)){ $elem_mr_type3 = "(".ucfirst($elem_mr_type3).")"; } 		
		}
	}
}

if($show_mrList1==true){
?>
<table border="0" cellspacing="0" cellpadding="0" style="width:100%;" class="paddingTop">  
    <?php
	if(
	$vis_mr_od_s || $vis_mr_od_c || $vis_mr_od_a || $vis_mr_od_add || ($vis_mr_od_txt_1 && $vis_mr_od_txt_1!='20/')
	||
	($vis_mr_od_txt_2 && $vis_mr_od_txt_2!='20/')
	||
	$vis_mr_os_s || $vis_mr_os_c || $vis_mr_os_a || $vis_mr_os_add || ($vis_mr_os_txt_1 && $vis_mr_os_txt_1!='20/')
	||
	($vis_mr_os_txt_2 && $vis_mr_os_txt_2!='20/')
	){
	$Mr1st = true;
	?>
		<tr>
			<?php 
				$givenMr = ($rd_vis_mr_none_given=="MR 1") ? "(Given)" : "";
			?>
			<td colspan="4" class='tb_subheading' style="width:100%;">Mr 1st <?php echo $givenMr.showDoctorName($provider_id); ?></td>
		</tr>
		<tr>
			<td class="text_value" style="width:31%;">&nbsp;</td>
			<td class="text_value" style="width:20%;">&nbsp;</td>
			<?php    
			}
			if(
				($prism_mr == 1) 
				&& 
				(
				($vis_mr_od_p || $vis_mr_od_prism || $vis_mr_od_slash || $vis_mr_od_sel_1)
				||
				($vis_mr_os_p || $vis_mr_os_prism || $vis_mr_os_slash || $vis_mr_os_sel_1)
				)
				){
				$Mr1stPrism = true;
				?>
		<td class="text_lable" style="width:26%;">Prism</td>
			<?php } ?>
			<?php
			if(($mrGLPH1 == 1)
				&&
				($vis_mr_od_sel_2 || ($visMrOdSel2Vision && $visMrOdSel2Vision!='20/'))
				||
				($vis_mr_os_sel_2 || ($visMrOsSel2Vision && $visMrOsSel2Vision!='20/'))
				){
				$Mr1stPrismGlPH = true;
			?>
		<td class="text_value" style="width:23%;"><!-- GL/PH --></td>
		<?php } 
		if($Mr1stPrismGlPH==true || $Mr1st){
			?>
		</tr>
		<?php		
		}
		$mr1gp1_od=$mr1gp1_os=$mr1gp2_addod=$mr1gp2_addos=$mr1gp3_prismod=$mr1gp3_prismos=$mr1gp4_GL_PHod=$mr1gp4_GL_PHos=array();
		
		$mr1gp1_od[]="";
		$mr1gp1_os[]="";
		$mr1gp2_addod[]="";
		$mr1gp2_addos[]="";
		$mr1gp3_prismod[]="";
		$mr1gp3_prismos[]="";
		$mr1gp4_GL_PHod[]="";
		$mr1gp4_GL_PHos[]="";
		if(
			$vis_mr_od_s || $vis_mr_od_c || $vis_mr_od_a || $vis_mr_od_add || ($vis_mr_od_txt_1 && $vis_mr_od_txt_1!='20/')
			||
			($vis_mr_od_txt_2 && $vis_mr_od_txt_2!='20/')
			||
			$vis_mr_os_s || $vis_mr_os_c || $vis_mr_os_a || $vis_mr_os_add || ($vis_mr_os_txt_1 && $vis_mr_os_txt_1!='20/')
			||
			($vis_mr_os_txt_2 && $vis_mr_os_txt_2!='20/')
			){
				$Mr1st = true;
				if($vis_mr_od_s)
				{
					$mr1gp1_od[] = "S";
					$mr1gp1_od[] = "$vis_mr_od_s"; 
				}
				if($vis_mr_od_c)
				{ 
					$mr1gp1_od[] = "C";
					$mr1gp1_od[] = "$vis_mr_od_c"; 
				}
				if($vis_mr_od_a)
				{
					$mr1gp1_od[] = "A";
					$mr1gp1_od[] = "$vis_mr_od_a"; 
				}
				if($vis_mr_od_txt_1 && $vis_mr_od_txt_1!='20/')
				{
					$mr1gp1_od[] = "$vis_mr_od_txt_1";
				}
		?>
		<tr>
			<td class='text_value'>
				<?php odLable(); print implode("&nbsp;",$mr1gp1_od); ?>
			</td>
			   <?php 
				if($vis_mr_od_add!="" && ($vis_mr_od_s!="" || $vis_mr_od_c!="" || $vis_mr_od_a!=""))
				{
					$mr1gp2_addod[] = "Add";
					$mr1gp2_addod[] = "$vis_mr_od_add";
				}
				if($vis_mr_od_txt_2 != '' && $vis_mr_od_txt_2!='20/') 
				{
					$mr1gp2_addod[] = "$vis_mr_od_txt_2";		
				}								
				?>
			<td class='text_value'>
				<?php  print implode("&nbsp;",$mr1gp2_addod); ?>
			</td>
			<?php   
			}
		
			if(
			($prism_mr == 1) 
			&& 
			(
			($vis_mr_od_p || $vis_mr_od_prism || $vis_mr_od_slash || $vis_mr_od_sel_1)
			||
			($vis_mr_os_p || $vis_mr_os_prism || $vis_mr_os_slash || $vis_mr_os_sel_1)
			)
			){
				$Mr1stPrism = true;
		
				if($vis_mr_od_p)
				{
					$mr1gp3_prismod[] = "P";
					$mr1gp3_prismod[] = "$vis_mr_od_p";
				}
				if($vis_mr_od_p || $vis_mr_od_prism){
					$mr1gp3_prismod[] = "<img src='pic_vision_pc.jpg'/>";
				}
				if($vis_mr_od_prism)
				{
					$mr1gp3_prismod[] = "$vis_mr_od_prism";
				}
				if($vis_mr_od_slash)
				{
					$mr1gp3_prismod[] = "/";
					$mr1gp3_prismod[] = "$vis_mr_od_slash";
				}
				if($vis_mr_od_sel_1)
				{
					$mr1gp3_prismod[] = "$vis_mr_od_sel_1";
				}
			?>
			<td class='text_value'>
			  <?php print implode("&nbsp;",$mr1gp3_prismod);  ?>
			</td>
		  <?php
			}
			
			  if(
				($mrGLPH1 == 1)
				&&
				(
					($vis_mr_od_sel_2) 
					|| 
					($visMrOdSel2Vision && $visMrOdSel2Vision!='20/')
					||
					(
						($vis_mr_os_sel_2) 
						|| 
						($visMrOsSel2Vision && $visMrOsSel2Vision!='20/')
					)
				)
				){								
				$Mr1stPrismGlPH = true;
				if($vis_mr_od_sel_2<>"")
				{
					$mr1gp4_GL_PHod[]="GL/PH";
					$mr1gp4_GL_PHod[]="$vis_mr_od_sel_2";
				}												
				if($visMrOdSel2Vision && $visMrOdSel2Vision!='20/')
				{
					$mr1gp4_GL_PHod[]="Vision";
					$mr1gp4_GL_PHod[]="$visMrOdSel2Vision";
				}
			?>
		<td class='text_value'>
			<?php /* odLable(); */ print implode("&nbsp;",$mr1gp4_GL_PHod); ?>
		</td>
   <?php
	}
	if(
		$vis_mr_od_s || $vis_mr_od_c || $vis_mr_od_a || $vis_mr_od_add || ($vis_mr_od_txt_1 && $vis_mr_od_txt_1!='20/')
		||
		($vis_mr_od_txt_2 && $vis_mr_od_txt_2!='20/')
		||
		$vis_mr_os_s || $vis_mr_os_c || $vis_mr_os_a || $vis_mr_os_add || ($vis_mr_os_txt_1 && $vis_mr_os_txt_1!='20/')
		||
		($vis_mr_os_txt_2 && $vis_mr_os_txt_2!='20/')
		){?>	
		</tr>
	<?php 
	} 
	if(
		$vis_mr_od_s || $vis_mr_od_c || $vis_mr_od_a || $vis_mr_od_add || ($vis_mr_od_txt_1 && $vis_mr_od_txt_1!='20/')
		||
		($vis_mr_od_txt_2 && $vis_mr_od_txt_2!='20/')
		||
		$vis_mr_os_s || $vis_mr_os_c || $vis_mr_os_a || $vis_mr_os_add || ($vis_mr_os_txt_1 && $vis_mr_os_txt_1!='20/')
		||
		($vis_mr_os_txt_2 && $vis_mr_os_txt_2!='20/')
		){
		$Mr1st = true;
	?>
    	<tr>
			<?php				
            if($vis_mr_os_s) 
            {
                $mr1gp1_os[]="S";
                $mr1gp1_os[]="$vis_mr_os_s";
            }
            if($vis_mr_os_c)
            {
                $mr1gp1_os[]="C";
                $mr1gp1_os[]="$vis_mr_os_c";
            }
            if($vis_mr_os_a)
            {
                $mr1gp1_os[]="A";
                $mr1gp1_os[]="$vis_mr_os_a";
            }
            if($vis_mr_os_txt_1 && $vis_mr_os_txt_1!='20/')
            {
                $mr1gp1_os[]="$vis_mr_os_txt_1";
            }
            ?>
            <td class='text_value'><?php osLable(); print implode("&nbsp;",$mr1gp1_os); ?></td>
            <?php
            if($vis_mr_os_add!="" and ($vis_mr_os_s!="" || $vis_mr_os_c!="" || $vis_mr_os_a!="")){
            $mr1gp2_addos[]="Add";
            $mr1gp2_addos[]="$vis_mr_os_add";
            }
            if($vis_mr_os_txt_2!="" && $vis_mr_os_txt_2!='20/'){
            $mr1gp2_addos[]="$vis_mr_os_txt_2";
            }
            ?>
            <td class='text_value'><?php  print implode("&nbsp;",$mr1gp2_addos); ?></td>
            <!-- MR 1st -->
            <?php   
			}
            ?>
            <!-- MR 1st Prism -->
            <?php
			if(
				($prism_mr == 1) 
				&& 
				(
				($vis_mr_od_p || $vis_mr_od_prism || $vis_mr_od_slash || $vis_mr_od_sel_1)
				||
				($vis_mr_os_p || $vis_mr_os_prism || $vis_mr_os_slash || $vis_mr_os_sel_1)
				)
				){
				$Mr1stPrism = true;
				if($vis_mr_os_p)
				{
					$mr1gp3_prismos[]="P";
					$mr1gp3_prismos[]="$vis_mr_os_p";
				}
				if($vis_mr_os_p || $vis_mr_os_prism){
					$mr1gp3_prismos[]="<img src='pic_vision_pc.jpg'/>";
				}	
				if($vis_mr_os_prism)
				{
					$mr1gp3_prismos[]="$vis_mr_os_prism";
				}
				if($vis_mr_os_slash)
				{
					$mr1gp3_prismos[]="/";
					$mr1gp3_prismos[]="$vis_mr_os_slash";
				}
				if($vis_mr_os_sel_1) 
				{
					$mr1gp3_prismos[]="$vis_mr_os_sel_1";
				}
				?>
				<td class='text_value'><?php /* osLable(); */ print implode("&nbsp;",$mr1gp3_prismos); ?></td>
				<?php
			}
			if(($mrGLPH1 == 1)
			&&
			(
			($vis_mr_od_sel_2)
			|| 
			($visMrOdSel2Vision && $visMrOdSel2Vision!='20/')								
			||
			($vis_mr_os_sel_2)
			||
			($visMrOsSel2Vision && $visMrOsSel2Vision!='20/')
			)
			){
				$Mr1stPrismGlPH = true;
				if($vis_mr_os_sel_2)
				{
					$mr1gp4_GL_PHos[] = "GL/PH";
					$mr1gp4_GL_PHos[] = "$vis_mr_os_sel_2";
				}
				if($visMrOsSel2Vision && $visMrOsSel2Vision!='20/') 
				{
					$mr1gp4_GL_PHos[] = "Vision";
					$mr1gp4_GL_PHos[] = "$visMrOsSel2Vision";
				}
				?>
				<td class='text_value'><?php  /* osLable(); */ print implode("&nbsp;",$mr1gp4_GL_PHos); ?></td>
				<?php
			}

            if(
            $vis_mr_od_s || $vis_mr_od_c || $vis_mr_od_a || $vis_mr_od_add || ($vis_mr_od_txt_1 && $vis_mr_od_txt_1!='20/')
            ||
            ($vis_mr_od_txt_2 && $vis_mr_od_txt_2!='20/')
            ||
            $vis_mr_os_s || $vis_mr_os_c || $vis_mr_os_a || $vis_mr_os_add || ($vis_mr_os_txt_1 && $vis_mr_os_txt_1!='20/')
            ||
            ($vis_mr_os_txt_2 && $vis_mr_os_txt_2!='20/')
            ){
            $Mr1st = true;?>  
         </tr>
		<?php 
		}
		?>
</table>
<?php 
}//END OF MR1
//Start MR2

if($show_mrList2==true){
	if(($vis_mr_od_given_s || $vis_mr_od_given_c || $vis_mr_od_given_a || $vis_mr_od_given_add)
	||
	($vis_mr_od_given_txt_1 && $vis_mr_od_given_txt_1!='20/')
	||
	($vis_mr_od_given_txt_2 && $vis_mr_od_given_txt_2!='20/')
	||
	($vis_mr_os_given_s || $vis_mr_os_given_c || $vis_mr_os_given_a || $vis_mr_os_given_add)
	||
	($vis_mr_os_given_txt_1 && $vis_mr_os_given_txt_1!='20/')
	||
	($vis_mr_os_given_txt_2 && $vis_mr_os_given_txt_2!='20/')
	){
		$MR2nd = true;		
		
		$mr2gp1_od = $mr2gp1_os = $mr2gp2_addod = $mr2gp2_addos = $mr2gp3_prismod = $mr2gp3_prismos = $mr2gp4_GL_PHod = $mr2gp4_GL_PHos = array();
		
		$mr2gp1_od[]="";
		$mr2gp1_os[]="";
		$mr2gp2_addod[]="";
		$mr2gp2_addos[]="";
		$mr2gp3_prismod[]="";
		$mr2gp3_prismos[]="";
		$mr2gp4_GL_PHod[]="";
		$mr2gp4_GL_PHos[]="";
		?>
        <table style="width:100%;" class="paddingTop" border="0" cellpadding="0" cellspacing="0">
            <tr>
				<?php 
                    $givenMr = ($rd_vis_mr_none_given=="MR 2") ? "(Given)" : "";
                ?>
				<td colspan="4" class='tb_subheading' style="width:100%;">Mr 2nd <?php echo $givenMr.showDoctorName($providerIdOther); ?></td>
            </tr>
            <tr>
                <td class="text_value" style="width:31%;">&nbsp;</td>
                <td class="text_value" style="width:20%;">&nbsp;</td>

            <?php
            
             if(($mrPrism2 == 1)
            &&
            ($vis_mr_od_given_p || $vis_mr_od_given_prism || $vis_mr_od_given_slash || $vis_mr_od_given_sel_1)
            ||
            ($vis_mr_os_given_p || $vis_mr_os_given_prism || $vis_mr_os_given_slash || $vis_mr_os_given_sel_1)
            ){
            $MR2ndPrism = true;
            ?>
            <td class="text_lable" style="width:26%;">Prism</td>
            <?php 
            }
                if(($mrGLPH2 == 1)
                &&
                ($vis_mr_od_given_sel_2 || ($visMrOtherOdSel2Vision && $visMrOtherOdSel2Vision!='20/'))
                ||
                ($vis_mr_os_given_sel_2 || ($visMrOtherOsSel2Vision && $visMrOtherOsSel2Vision!='20/'))
                ){
                $MR2ndPrismGLPH = true;
            ?>
            <td class="text_value" style="width:23%;"><!-- GL/PH --></td>
            <?php
            }
            ?>
        </tr>
        <tr>				
        <?php
        if(($vis_mr_od_given_s || $vis_mr_od_given_c || $vis_mr_od_given_a || $vis_mr_od_given_add)
        ||
        ($vis_mr_od_given_txt_1 && $vis_mr_od_given_txt_1!='20/')
        ||
        ($vis_mr_od_given_txt_2 && $vis_mr_od_given_txt_2!='20/')
        ||
        ($vis_mr_os_given_s || $vis_mr_os_given_c || $vis_mr_os_given_a || $vis_mr_os_given_add)
        ||
        ($vis_mr_os_given_txt_1 && $vis_mr_os_given_txt_1!='20/')
        ||
        ($vis_mr_os_given_txt_2 && $vis_mr_os_given_txt_2!='20/')
		){
			$MR2nd = true;
			if($vis_mr_od_given_s)
			{
				$mr2gp1_od[] = "S";
				$mr2gp1_od[] = "$vis_mr_od_given_s";
			}
			if($vis_mr_od_given_c)
			{
				$mr2gp1_od[] = "C";
				$mr2gp1_od[] = "$vis_mr_od_given_c";
			}
			if($vis_mr_od_given_a)
			{
				$mr2gp1_od[] = "A";
				$mr2gp1_od[] = "$vis_mr_od_given_a";
			}
			if($vis_mr_od_given_txt_1 && $vis_mr_od_given_txt_1!='20/')
			{
				$mr2gp1_od[]="$vis_mr_od_given_txt_1";
			}
			?>
			<td class='text_value'><?php odLable(); print implode("&nbsp;",$mr2gp1_od); ?></td>
			<?php
			if($vis_mr_od_given_add!='' and ($vis_mr_od_given_s!="" || $vis_mr_od_given_c!="" || $vis_mr_od_given_a!=""))
			{
				$mr2gp2_addod[]="Add";
				$mr2gp2_addod[]="$vis_mr_od_given_add";
			}
			if($vis_mr_od_given_txt_2 && $vis_mr_od_given_txt_2!='20/')
			{
				$mr2gp2_addod[]="$vis_mr_od_given_txt_2";
			}
			?>
			<td class='text_value'><?php print implode("&nbsp;",$mr2gp2_addod); ?></td>
			<?php 
			}
			if(($mrPrism2 == 1)
			&&
			($vis_mr_od_given_p || $vis_mr_od_given_prism || $vis_mr_od_given_slash || $vis_mr_od_given_sel_1)
			||
			($vis_mr_os_given_p || $vis_mr_os_given_prism || $vis_mr_os_given_slash || $vis_mr_os_given_sel_1)
			){
				$MR2ndPrism = true;
				if($vis_mr_od_given_p)
				{
					$mr2gp3_prismod[]="P";
					$mr2gp3_prismod[]="$vis_mr_od_given_p";
					$mr2gp3_prismod[]="<img src='pic_vision_pc.jpg'/>";
				}
				if($vis_mr_od_given_prism)
				{
					$mr2gp3_prismod[]="$vis_mr_od_given_prism";
				}
				if($vis_mr_od_given_slash)
				{
					$mr2gp3_prismod[]="/";
					$mr2gp3_prismod[]="$vis_mr_od_given_slash";
				}
				if($vis_mr_od_given_sel_1)
				{
					$mr2gp3_prismod[]="$vis_mr_od_given_sel_1";
				}
				?>
				<td class='text_value'><?php print implode("&nbsp;",$mr2gp3_prismod); ?></td>
				<?php 
			}
			if(($mrGLPH2 == 1)
			&&
			($vis_mr_od_given_sel_2 || ($visMrOtherOdSel2Vision && $visMrOtherOdSel2Vision!='20/'))
			||
			($vis_mr_os_given_sel_2 || ($visMrOtherOsSel2Vision && $visMrOtherOsSel2Vision!='20/'))
			){
				$MR2ndPrismGLPH = true;
				if($vis_mr_od_given_sel_2)
				{
					$mr2gp4_GL_PHod[] = "GL/PH";
					$mr2gp4_GL_PHod[] = "$vis_mr_od_given_sel_2";
				} 
				if($visMrOtherOdSel2Vision && $visMrOtherOdSel2Vision!='20/') 
				{
					$mr2gp4_GL_PHod[] = "Vision";
					$mr2gp4_GL_PHod[] = "$visMrOtherOdSel2Vision";
				}
				?>
				<td class='text_value'><?php print implode("&nbsp;",$mr2gp4_GL_PHod); ?></td>
				<?php
			}
			?>
        </tr>
        <tr>				
        <?php
        if(($vis_mr_od_given_s || $vis_mr_od_given_c || $vis_mr_od_given_a || $vis_mr_od_given_add)
        ||
        ($vis_mr_od_given_txt_1 && $vis_mr_od_given_txt_1!='20/')
        ||
        ($vis_mr_od_given_txt_2 && $vis_mr_od_given_txt_2!='20/')
        ||
        ($vis_mr_os_given_s || $vis_mr_os_given_c || $vis_mr_os_given_a || $vis_mr_os_given_add)
        ||
        ($vis_mr_os_given_txt_1 && $vis_mr_os_given_txt_1!='20/')
        ||
        ($vis_mr_os_given_txt_2 && $vis_mr_os_given_txt_2!='20/')
        ){
        	$MR2nd = true;        
			if($vis_mr_os_given_s)
			{
				$mr2gp1_os[] = "S";
				$mr2gp1_os[] = "$vis_mr_os_given_s";
			}
			if($vis_mr_os_given_c)
			{
				$mr2gp1_os[] = "C";
				$mr2gp1_os[] = "$vis_mr_os_given_c";
			}
			if($vis_mr_os_given_a)
			{
				$mr2gp1_os[] = "A";
				$mr2gp1_os[] = "$vis_mr_os_given_a";
			}
			if($vis_mr_os_given_txt_1 && $vis_mr_os_given_txt_1!='20/')
			{
				$mr2gp1_os[] = "$vis_mr_os_given_txt_1";
			}
			?>
			<td class='text_value'><?php osLable(); print implode("&nbsp;",$mr2gp1_os); ?></td>
			<?php
			if($vis_mr_os_given_add!='' and ($vis_mr_os_given_s!="" || $vis_mr_os_given_c!="" || $vis_mr_os_given_a!=""))
			{
				$mr2gp2_addos[] = "Add";
				$mr2gp2_addos[] = "$vis_mr_os_given_add";
			}
			if($vis_mr_os_given_txt_2 && $vis_mr_os_given_txt_2!='20/')
			{
				$mr2gp2_addos[] = "$vis_mr_os_given_txt_2";
			}
			?>
			<td class='text_value'><?php  print implode("&nbsp;",$mr2gp2_addos); ?></td>			
			<?php 
			}
			if(($mrPrism2 == 1)
			&&
			($vis_mr_od_given_p || $vis_mr_od_given_prism || $vis_mr_od_given_slash || $vis_mr_od_given_sel_1)
			||
			($vis_mr_os_given_p || $vis_mr_os_given_prism || $vis_mr_os_given_slash || $vis_mr_os_given_sel_1)
			){
				$MR2ndPrism = true;
				if($vis_mr_os_given_p)
				{
					$mr2gp3_prismos[] ="P";
					$mr2gp3_prismos[] = "$vis_mr_os_given_p";
				}
				if($vis_mr_os_given_p || $vis_mr_os_given_prism){
					$mr2gp3_prismos[] = "<img src='pic_vision_pc.jpg'/>";
				}
				if($vis_mr_os_given_prism)
				{
					$mr2gp3_prismos[] = "$vis_mr_os_given_prism";
				}
				if($vis_mr_os_given_slash)
				{
					$mr2gp3_prismos[] = "/";
					$mr2gp3_prismos[] = "$vis_mr_os_given_slash";
				}
				if($vis_mr_os_given_sel_1)
				{
					$mr2gp3_prismos[] = "$vis_mr_os_given_sel_1";
				}
				?>
				<td class='text_value'><?php print implode("&nbsp;",$mr2gp3_prismos); ?></td>
			<?php 
			}
			if(($mrGLPH2 == 1)
			&&
			($vis_mr_od_given_sel_2 || ($visMrOtherOdSel2Vision && $visMrOtherOdSel2Vision!='20/'))
			||
			($vis_mr_os_given_sel_2 || ($visMrOtherOsSel2Vision && $visMrOtherOsSel2Vision!='20/'))
			){
				$MR2ndPrismGLPH = true;
				
				if($vis_mr_os_given_sel_2)
				{
					$mr2gp4_GL_PHos[] = "GL/PH";
					$mr2gp4_GL_PHos[] = "$vis_mr_os_given_sel_2";
				}
				if($visMrOtherOsSel2Vision && $visMrOtherOsSel2Vision!='20/') 
				{
					$mr2gp4_GL_PHos[] = "Vision";
					$mr2gp4_GL_PHos[] = "$visMrOtherOsSel2Vision";
				}
				?>
				<td class='text_value'><?php print implode("&nbsp;",$mr2gp4_GL_PHos); ?></td>
			<?php
            }
            ?>
			</tr>
        </table>
	<?php
	}
}//END OF MR2

if($show_mrList3==true){
	//Start MR3
	if(($visMrOtherOdS_3 || $visMrOtherOdC_3 || $visMrOtherOdA_3 || $visMrOtherOdAdd_3)
	||
	($visMrOtherOdTxt1_3 && $visMrOtherOdTxt1_3!='20/')
	||
	($visMrOtherOsS_3 || $visMrOtherOsC_3 || $visMrOtherOsA_3 || $visMrOtherOsAdd_3)
	||
	($visMrOtherOsTxt1_3 && $visMrOtherOsTxt1_3!='20/')
	){
	$MR3rd = true;
	?>
	<table style="width:100%;" class="paddingTop" cellpadding="0" cellspacing="0">
		<tr>
			<?php 
				$givenMr = ($rd_vis_mr_none_given=="MR 3") ? "(Given)" : "";
			?>
			<td colspan="4" class="tb_subheading" style="width:100%;">MR3 <?php echo $givenMr.showDoctorName($providerIdOther_3); ?></td>
		</tr>
		<tr>
			<td class="text_value" style="width:31%;">&nbsp;</td>
			<td class="text_value" style="width:20%;">&nbsp;</td>    
			<td class="text_lable" style="width:26%;">
			<?php 
				if(
				($mrPrism3 == 1)
				&&
				($visMrOtherOdP_3 || $visMrOtherOdPrism_3 || $visMrOtherOdSlash_3 || $visMrOtherOdSel1_3)
				||
				($visMrOtherOsP_3 || $visMrOtherOsPrism_3 || $visMrOtherOsSlash_3 || $visMrOtherOsSel1_3)
				){?>Prism<?php }?>
			</td>
			<td class="text_value" style="width:23%;"><!-- GL/PH --></td>
		</tr> 
		<tr>
			<?php
			$mr3gp1_od = $mr3gp1_os = $mr3gp2_addod = $mr3gp2_addos = $mr3gp3_prismod = $mr3gp3_prismos = $mr3gp4_GL_PHod = $mr3gp4_GL_PHos = array();
			$mr3gp1_od[]="";
			$mr3gp1_os[]="";
			$mr3gp2_addod[]="";
			$mr3gp2_addos[]="";
			$mr3gp3_prismod[]="";
			$mr3gp3_prismos[]="";
			$mr3gp4_GL_PHod[]="";
			$mr3gp4_GL_PHos[]="";
			if($visMrOtherOdS_3)
			{
				$mr3gp1_od[] = "S";
				$mr3gp1_od[] = "$visMrOtherOdS_3"; 
			}
			if($visMrOtherOdC_3)
			{
				$mr3gp1_od[] = "C";
				$mr3gp1_od[] = "$visMrOtherOdC_3";
			}
			if($visMrOtherOdA_3)
			{
				$mr3gp1_od[] = "A";
				$mr3gp1_od[] = "$visMrOtherOdA_3";
			}
			if($visMrOtherOdTxt1_3 && $visMrOtherOdTxt1_3!='20/')
			{
				$mr3gp1_od[] = "$visMrOtherOdTxt1_3";
			}
			?>
				<td class="text_value"><?php odLable(); print implode("&nbsp;",$mr3gp1_od);?></td>
			<?php
			if($visMrOtherOdAdd_3!="" && ($visMrOtherOdS_3!="" || $visMrOtherOdC_3!="" || $visMrOtherOdA_3!=""))
			{
				$mr3gp2_addod[] = "Add";
				$mr3gp2_addod[] = "$visMrOtherOdAdd_3";
			}
			if($visMrOtherOdTxt2_3 != '' && $visMrOtherOdTxt2_3 != '20/')
			{
				$mr3gp2_addod[] = "$visMrOtherOdTxt2_3";
			}
			?>
			<td class="text_value"><?php print implode("&nbsp;",$mr3gp2_addod);?></td>
			<?php
			if(
			($mrPrism3 == 1)
			&&
			($visMrOtherOdP_3 || $visMrOtherOdPrism_3 || $visMrOtherOdSlash_3 || $visMrOtherOdSel1_3)
			||
			($visMrOtherOsP_3 || $visMrOtherOsPrism_3 || $visMrOtherOsSlash_3 || $visMrOtherOsSel1_3)
			){
				$MR3rdPrism = true;
				$mr3gp3_prismod[] = "";							
				if($visMrOtherOdP_3)
				{
					$mr3gp3_prismod[] = "P";							
					$mr3gp3_prismod[] = "$visMrOtherOdP_3";
					$mr3gp3_prismod[] = "<img src='pic_vision_pc.jpg'/>"; 
				}													
				if($visMrOtherOdPrism_3)
				{ 
					$mr3gp3_prismod[] = "$visMrOtherOdPrism_3";
				}										
				if($visMrOtherOdSlash_3)
				{
					$mr3gp3_prismod[] = "/";
					$mr3gp3_prismod[] = "$visMrOtherOdSlash_3";
				}
				if($visMrOtherOdSel1_3)
				{
					$mr3gp3_prismod[] = "$visMrOtherOdSel1_3";
				}
				?>							
				<td class="text_value"><?php print implode("&nbsp;",$mr3gp3_prismod);?></td>
				<?php 
			}
			$mr3gp4_GL_PHod[]="";//"OD";
			if($visMrOtherOdSel2_3 != '')
			{
				$mr3gp4_GL_PHod[] = "GL/PH";
				$mr3gp4_GL_PHod[] = "$visMrOtherOdSel2_3";
			}
			if($visMrOtherOdSel2Vision_3 != '' && $visMrOtherOdSel2Vision_3 != '20/')
			{
				$mr3gp4_GL_PHod[] = "Vision";
				$mr3gp4_GL_PHod[] = "$visMrOtherOdSel2Vision_3";
			}
			?>
			<td class="text_value"><?php print implode("&nbsp;",$mr3gp4_GL_PHod);?></td>
		</tr>
		<tr>
			<?php
			if($visMrOtherOsS_3)
			{
				$mr3gp1_os[]="S";
				$mr3gp1_os[]="$visMrOtherOsS_3";
			}
			if($visMrOtherOsC_3) 
			{
				$mr3gp1_os[]="C";
				$mr3gp1_os[]="$visMrOtherOsC_3";
			}
			if($visMrOtherOsA_3)
			{
				$mr3gp1_os[]="A";
				$mr3gp1_os[]="$visMrOtherOsA_3";
			}
			if($visMrOtherOsTxt1_3 && $visMrOtherOsTxt1_3!='20/')
			{
				$mr3gp1_os[]="$visMrOtherOsTxt1_3";
			}
			?>
			<td class="text_value"><?php osLable(); print implode("&nbsp;",$mr3gp1_os);?></td>
			<?php 
				if($visMrOtherOsAdd_3!="" && ($visMrOtherOsS_3!="" || $visMrOtherOsC_3!="" || $visMrOtherOsA_3!=""))
				{
					$mr3gp2_addos[]="Add";
					$mr3gp2_addos[]="$visMrOtherOsAdd_3";
				}
				if($visMrOtherOsTxt2_3 && $visMrOtherOsTxt2_3!='20/')
				{
					$mr3gp2_addos[]="$visMrOtherOsTxt2_3";
				}
				?>
			<td class="text_value"><?php print implode("&nbsp;",$mr3gp2_addos);?></td>
			<?php
				$mr3gp3_prismos = array();
				$mr3gp3_prismos[]="";								
				if($visMrOtherOsP_3)
				{
					$mr3gp3_prismos[]="P";								
					$mr3gp3_prismos[]="$visMrOtherOsP_3";
					$mr3gp3_prismos[]="<img src='pic_vision_pc.jpg'/>";
				}
				
				if($visMrOtherOsPrism_3)
				{
					$mr3gp3_prismos[]="$visMrOtherOsPrism_3";
				}
				if($visMrOtherOsSlash_3)
				{
					$mr3gp3_prismos[]="/";
					$mr3gp3_prismos[]="$visMrOtherOsSlash_3";
				}													
				if($visMrOtherOsSel1_3)
				{
					$mr3gp3_prismos[]="$visMrOtherOsSel1_3";
				}
			?>
			<td class="text_value"><?php print implode("&nbsp;",$mr3gp3_prismos);?></td>
			<?php
				$mr3gp4_GL_PHos[] = "";							
				if($visMrOtherOsSel2_3)
				{
					$mr3gp4_GL_PHos[] = "GL/PH";
					$mr3gp4_GL_PHos[] = "$visMrOtherOsSel2_3"; 
				}
				if($visMrOtherOsSel2Vision_3 && $visMrOtherOsSel2Vision_3!='20/') 
				{
					$mr3gp4_GL_PHos[] = "Vision";
					$mr3gp4_GL_PHos[] = "$visMrOtherOsSel2Vision_3";
				}
			?>
			<td class="text_value"><?php print implode("&nbsp;",$mr3gp4_GL_PHos);?></td>
		</tr>
	</table>
	<?php
	}//End MR3
}//END OF MR3

if($vis_mr_desc1COMMENTS!=""){
	?>
    <!-- Description -->
    <table style="width:100%;" cellpadding="0" cellspacing="0" border="0">
        <tr>
            <td class="text_value" style="width:11%;" valign="top">Mr1-Comments:</td>
            <td class="text_value" style="width:89%;" >&nbsp;<?php echo $vis_mr_desc1COMMENTS; ?></td>
        </tr>
    </table>
    <!-- Description -->					
	<?php 
}
if($vis_mr_desc_other2COMMENTS!=""){
	?>
    <!-- Description -->
    <table style="width:100%;" cellpadding="0" cellspacing="0" border="0">
        <tr>
            <td class="text_value" style="width:11%;" valign="top">Mr2-Comments:</td>
            <td class="text_value" style="width:89%;" >&nbsp;<?php echo $vis_mr_desc_other2COMMENTS; ?></td>
        </tr>
    </table>
    <!-- Description -->					
	<?php 
}

if($vis_mr_desc_3COMMENTS!=""){
	?>
    <!-- Description -->
    <table style="width:100%;" cellpadding="0" cellspacing="0" border="0">
        <tr>
            <td class="text_value" style="width:11%;" valign="top">Mr3-Comments:</td>
            <td class="text_value" style="width:89%;" >&nbsp;<?php echo $vis_mr_desc_3COMMENTS; ?></td>
        </tr>
    </table>
    <!-- Description -->					
	<?php 
}

//-- COLOR PLATE --
//-- Color Plate Data----//
$vis_controlValueOd = (strpos($vis_statusElements, "elem_controlValueOd=1") !== false)?$vis_controlValueOd:"";
$vis_controlValueOs = (strpos($vis_statusElements, "elem_controlValueOs=1") !== false)?$vis_controlValueOs:"";
$vis_steropsis = (strpos($vis_statusElements, "elem_steropsis=1") !== false)?$vis_steropsis:"";
$vis_exo_od_s = (strpos($vis_statusElements, "elem_visExoOdS=1") !== false)?$vis_exo_od_s:"";
$vis_exo_od_c = (strpos($vis_statusElements, "elem_visExoOdC=1") !== false)?$vis_exo_od_c:"";
$vis_exo_os_s = (strpos($vis_statusElements, "elem_visExoOsS=1") !== false)?$vis_exo_os_s:""; 
$vis_exo_os_c = (strpos($vis_statusElements, "elem_visExoOsC=1") !== false)?$vis_exo_os_c:"";
$vis_exo_os_a = (strpos($vis_statusElements, "elem_visExoOsA=1") !== false)?$vis_exo_os_a:"";
$vis_ret_pd_od = (strpos($vis_statusElements, "elem_visRetOd=1") !== false)?$vis_ret_pd_od:"";
$vis_ret_pd_os = (strpos($vis_statusElements, "elem_visRetOs=1") !== false)?$vis_ret_pd_os:"";
$vis_ret_pd = (strpos($vis_statusElements, "elem_visRetPD=1") !== false)?$vis_ret_pd:"";

if($vis_controlValueOd || $vis_controlValueOs || $vis_steropsis
	||
	$vis_exo_od_s || $vis_exo_od_c || $vis_exo_od_a || $vis_exo_os_s || $vis_exo_os_c || $vis_exo_os_a
	||
	$vis_ret_pd_od || $vis_ret_pd_os || $vis_ret_pd
	){
	$colorPlate = true;
	if($vis_controlValueOd || $vis_controlValueOs || $vis_steropsis){
	?>
	<table style="width:100%;" class="paddingTop" border="0" cellspacing="0" cellpadding="0" >
		<tr>
			<td colspan="5" valign="middle" class="tb_heading" style="width:100%;">Color Plate:</td>
		</tr>
		<?php
	   	if($vis_controlValueOd && $vis_controlValueOs && $vis_steropsis){
		?>
			<tr>
				<td class="text_value" width="12%" >IPL&nbsp;Control</td>
				<td class="text_value" width="12%"  ><?php odLable();?></td>
				<td class="text_value" width="12%" >IPL&nbsp;Control</td>
				<td class="text_value" width="14%"  ><?php osLable();?></td>
				<td class="text_value" width="50%"  >Steropsis</td>
			</tr>
		<?php 
		} 
		if($vis_controlValueOd && $vis_controlValueOs && $vis_steropsis){
		?>  
        <!-- vis_controlValueOs vis_control_os  -->
		<tr>
			 <td class='text_value'><?php if($vis_control == 'plus') echo '+'; elseif($vis_control == 'minus') echo '-'; ?></td>
			 <td class='text_value'><?php if($vis_controlValueOd and $vis_control) echo $vis_controlValueOd.'/10'; elseif($vis_controlValueOd=="" and $vis_control) echo "N/A"; ?></td>
			 <td class='text_value'><?php if($vis_control_os == 'plus') echo '+'; elseif($vis_control_os == 'minus') echo '-'; ?></td>
			 <td class='text_value'><?php if($vis_controlValueOs and $vis_control_os) echo $vis_controlValueOs.'/10'; ?></td>
			 <td class='text_value'><?php if($vis_steropsis) echo $vis_steropsis; else echo '&nbsp;';?></td>
		</tr>
		<?php 
		}
		?>
	</table>
	<?php 
	}
	if(($vis_controlValueOd && $vis_controlValueOs) &&($vis_exo_od_s || $vis_exo_od_c || $vis_exo_od_a || $vis_exo_os_s || $vis_exo_os_c || $vis_exo_os_a)){
	?>
    	<table style="width:100%;" border="0" cellspacing="0" cellpadding="0" bordercolor="#EEEEEE">					
            <tr>
                <td colspan="4" style="width:100%;" class="text_lable">Retinoscopy</td>
            </tr>
			<?php
            if($vis_exo_od_s || $vis_exo_od_c || $vis_exo_od_a || $vis_exo_os_s || $vis_exo_os_c || $vis_exo_os_a){
				$RetinoscopyAray = $RetinoscopyArays = array();
				$RetinoscopyAray[]="";
				$RetinoscopyArays[]="";
            ?>
                <tr>	
                    <?php if($vis_exo_od_s) $RetinoscopyAray[]="S";$RetinoscopyAray[]="".$vis_exo_od_s.""; ?>
                    <?php if($vis_exo_od_c) $RetinoscopyAray[]="C";$RetinoscopyAray[]="".$vis_exo_od_c.""; ?>
                    <?php if($vis_exo_od_a) $RetinoscopyAray[]="A";$RetinoscopyAray[]="".$vis_exo_od_a.""; ?>
                    <td class='text_value'><?php odLable(); print implode("&nbsp;",$RetinoscopyAray);?></td>
                </tr> 
			<?php 
			} 
            if($vis_exo_od_s || $vis_exo_od_c || $vis_exo_od_a || $vis_exo_os_s || $vis_exo_os_c || $vis_exo_os_a){
            ?> 
                <tr>
					<?php if($vis_exo_os_s) $RetinoscopyArays[]="S";$RetinoscopyArays[]="".$vis_exo_os_s.""; ?>
                    <?php if($vis_exo_os_c) $RetinoscopyArays[]="C";$RetinoscopyArays[]="".$vis_exo_os_c.""; ?>
                    <?php if($vis_exo_os_a) $RetinoscopyArays[]="A";$RetinoscopyArays[]="".$vis_exo_os_a.""; ?>
                    <td class='text_value'><?php osLable(); print implode("&nbsp;",$RetinoscopyArays);?></td>
            	</tr>
            <?php 
			} 
			?>
            </table>
			<?php } 
		   if($vis_ret_pd_od || $vis_ret_pd_os || $vis_ret_pd){
			?>
            <table style="width:100%;" border="0" cellspacing="0" cellpadding="0" bordercolor="#EEEEEE">					
                <tr>
                    <td colspan="2" style="width:100%;" class="text_lable">Exopholmalmeter</td>
                </tr>
                <?php
                 if($vis_ret_pd_od || $vis_ret_pd_os || $vis_ret_pd){
                   ?>
                <tr>
                   <td colspan="2" align="left" style="width:100%;" class="text_value"><?php echo "PD "; if($vis_ret_pd) {echo $vis_ret_pd;} //else echo '&nbsp'; ?>
                   </td>
                </tr>  
                <?php } ?>
                 <tr>
                   <?php
                   if($vis_ret_pd_od || $vis_ret_pd_os || $vis_ret_pd){
                    ?>
                    <td style="width:13%;" class="text_value"><?php odLable(); if($vis_ret_pd_od) {echo "&nbsp;".$vis_ret_pd_od; }//else echo '&nbsp'; ?>
                    </td>
                    <td class="text_value" style="width:87%;"><?php osLable(); if($vis_ret_pd_os) {echo "&nbsp;".$vis_ret_pd_os;} //else echo '&nbsp';?>
                    </td>		
                 <?php } ?>
              </tr>
        </table>
	<?php } ?>
<?php
}

//-- COLOR PLATE -->
$sql = imw_query("SELECT * FROM chart_cvf WHERE patientId = '$patient_id' and formId = '$form_id'");					
$row = array();					
while($res = imw_fetch_assoc($sql)){	
	$row = $res;
}
if($row != false && count($row)>0){
	extract($row);
	?>
    <table border="0" cellpadding="0" cellspacing="0" style="width:100%;" class="paddingTop">
        <tr>
            <td valign="middle" colspan="3" class="tb_heading" style="width:100%;">CVF (Confrontation Field)</td>
        </tr>
            <tr>
            <td class="text_value" align="left" valign="top" style="width: 10%;">&nbsp;</td>
            <td class="text_value"  align="left" style="width: 45%;"><?php odLable();?></td>
            <td class="text_value" align="left" style="width: 45%;"><?php osLable();?></td>
        </tr>						
        <tr>
            <td class="text_value" align="left" valign="top" style="width: 10%;">&nbsp;</td>
            <td class="text_value" align="left" valign="top" style="width: 45%;"><?php if($summaryOd) echo $summaryOd; else echo '&nbsp;'; ?></td>
            <td class="text_value" align="left" valign="top" style="width: 45%;"><?php if($summaryOs) echo $summaryOs; else echo '&nbsp;'; ?></td>
        </tr>						
        <tr>
            <td class="text_value" align="left" valign="top" style="width: 10%;">&nbsp;</td>
            <td align="left" style="width: 45%;">
            <?php
            if(isAppletModified(trim($drawOd))){
                $imagecvf1 = "";
                $tableLA = 'chart_cvf';
                $idNameLA = 'cvf_id';
                $pixelLaDrawing = 'drawOd';
                $imageLA = dirname(__FILE__).'/../../images/picEomCvf.jpg';
                $altLA = 'CVF'; 
            if(getAppletImage($cvf_id,$tableLA,$idNameLA,$pixelLaDrawing,$imageLA,$altLA,"1")){
				$gdFilename = dirname(__FILE__)."/../../library/html_to_pdf/tmp/".$gdFilename;
                if(file_exists($gdFilename)){
					echo("<img src=\"".$gdFilename."\" width=\"200\" height=\"90\"/>");					 
						$ChartNoteImagesString[]=$gdFilename;								
					}
                }							
            }
            ?>
            </td>
            <td align="left" style="width: 45%;">
            <?php
            if(isAppletModified(trim($drawOd))){
                $imagecvf1 = "";
                $tableLA = 'chart_cvf';
                $idNameLA = 'cvf_id';
                $pixelLaDrawing = 'drawOs';
                $imageLA = dirname(__FILE__).'/../../images/picEomCvf.jpg';
                $altLA = 'CVF'; 
                if(getAppletImage($cvf_id,$tableLA,$idNameLA,$pixelLaDrawing,$imageLA,$altLA,"1")){
					$gdFilename = dirname(__FILE__)."/../../library/html_to_pdf/tmp/".$gdFilename;
                    if(file_exists($gdFilename)){									
						echo("<img src=\"".$gdFilename."\" width=\"200\" height=\"90\"/>");				
						$ChartNoteImagesString[]=$gdFilename;								
                    }
                }						
            }
            ?>
            </td>
        </tr>
    </table>
<?php 
}
//-- CVF --
//-- Diplopia --
$sql = imw_query("SELECT * FROM chart_diplopia WHERE patientId = '$patient_id' AND formId = '$form_id'");					
$row = array();					
while($res = imw_fetch_assoc($sql)){	
	$row = $res;
}
if($row != false && count($row)>0){
	extract($row);
	?>
	<table border="0" cellpadding="0" cellspacing="0" style="width:100%;" class="paddingTop">
		<tr>
			<td valign="middle" colspan="3" class="tb_heading" style="width:100%;">Diplopia</td>
		</tr>
		<tr>
			<td style="width:10%;">&nbsp;</td>
			<td class="text_value" align="left" style="width:45%;"><?php odLable();?></td>
			<td class="text_value" align="left" style="width:45%;"><?php osLable();?></td>
		</tr>
		<tr>
			<td style="width:10%;">&nbsp;</td>
			<td align="left" class="text_value" valign="top" style="width:45%;"><?php echo $summaryOd; ?></td>
			<td align="left" class="text_value" valign="top"style="width:45%;"><?php echo $summaryOs; ?></td>
		</tr>
		<?php
		if($drawing!=""){?>
			<tr>
			<td style="width:10%;">&nbsp;</td>
			<td align="center" colspan="2" style="width:90%;">
			<?php
				$imageNAme=drawOnImageImage($drawing);
				$imageNAme=drawOnImageImage($drawing);
				echo('<img src="'.$imageNAme.'" width="300" height="100"/>');
				$ChartNoteImagesString[] = $imageNAme;			
				$ChartNoteImagesString[] = $gdFilename;
			?>
			</td>
		</tr>
		<?php 
		} 
		?>
	</table>        
<?php
}
//-- Diplopia --
//-- W4Dot  --
if($elem_worth4dot){			
?>
<table border="0" cellpadding="0" cellspacing="0" style="width:100%;" class="paddingTop">
	<tr>
		<td valign="middle" colspan="2"  class="tb_heading" style="width:100%;">W4Dot</td>
	</tr>											
	<tr>
		<td class="text_value" style="width:10%;">&nbsp;</td>
		<td align="left" class="text_value" valign="top" style="width:90%;"><?php echo $elem_worth4dot; ?></td>			
	</tr>				
</table>
<?php
}

$l_and_a = true;
$pupilPrint = true;
$externalPrint = true;
$eomPrint = true;
$IOPPrint = true;
$SLEPrint = true;
$OptNevPrint = true;
$RandVPrint = true;
$assessmentPlanPrint=true;
//include(dirname(__FILE__)."/../chart_notes/examsSummarypdfNew.php");
?>
</page>
<?php 
include(dirname(__FILE__)."/ascan-pdf-print_report.php");
$patient_workprint_data = ob_get_contents();
ob_end_clean();
?>