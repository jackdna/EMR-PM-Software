<?php
$ignoreAuth = true;
include("../../../../config/globals.php");
set_time_limit(0);
//
error_reporting(E_ALL & ~E_NOTICE);
ini_set("display_errors",1);

/**
TRUNCATE table chart_vis_master;
TRUNCATE table chart_acuity;
chart_ak
**/

//$sql_where = " WHERE c1.patient_id = '68051' ";
//$sql_and = " AND c1.patient_id = '68051' ";
$lm=100;
$st = !empty($_GET["st"]) ? $_GET["st"] : 0; 

$q = array();
$msg_info = array();


if(empty($_GET["op"]) || !isset($_GET["op"])){
	echo "Processing 0 .. ";
	//Bak--
	//*
	//*
	$t = date("m_d_Y_His");
	$sql = "CREATE TABLE chart_pc_mr_".$t." LIKE chart_pc_mr";
	imw_query($sql);
	
	$sql = "INSERT INTO chart_pc_mr_".$t." SELECT * from chart_pc_mr ";
	imw_query($sql);
	
	$sql = "CREATE TABLE chart_pc_mr_values_".$t." LIKE chart_pc_mr_values";
	imw_query($sql);
	
	$sql = "INSERT INTO chart_pc_mr_values_".$t." SELECT * from chart_pc_mr_values ";
	imw_query($sql);
	
	$sql = "CREATE TABLE chart_vision_".$t." LIKE chart_vision";
	imw_query($sql);
	
	$sql = "INSERT INTO chart_vision_".$t." SELECT * from chart_vision ";
	imw_query($sql);
	//*/
	echo "Back up of chart_pc_mr, chart_pc_mr_values, chart_vision is made . Now <a href='spl_sep_vision_db.php?op=1&st=0'>click to start</a> ";
	//*/	
	//Bak--
	//exit("TESt".rand());
	//echo "<script>window.location.replace('spl_sep_vision_db.php?op=1&st=0');</script>";
	exit();
}


if(!empty($_GET["op"]) && $_GET["op"] == "1" ){
echo "Processing 1 .. Records: ".$st;

//1
$qry = "SELECT patient_id, form_id, vis_statusElements, ut_elem FROM chart_vision c1 ".$sql_where." order by vis_id LIMIT ".$st.", ".$lm."";
//echo "<br/>".$qry;
$res = imw_query($qry) or $msg_info[]=imw_error();
$chk_lm = imw_num_rows($res);
$qry = "";
while ($row = imw_fetch_assoc($res)) {
	$sql = "SELECT id FROM chart_vis_master WHERE form_id='".$row["form_id"]."'  ";
	$resq = imw_query($sql);
	$chk_q = imw_num_rows($resq);
	if($chk_q<=0){
		$qry .= "(NULL, '".$row["patient_id"]."', '".$row["form_id"]."', '".sqlEscStr($row["vis_statusElements"])."', '".sqlEscStr($row["ut_elem"])."'),";
		
	}
}

if(!empty($qry)){
$qry = trim($qry,",");
$qry = "INSERT INTO chart_vis_master (id, patient_id, form_id, status_elements, ut_elem)
				VALUES ".$qry;
imw_query($qry) or $msg_info[]=imw_error();

}

$op=$_GET["op"];$tst=$st+$lm;
if($chk_lm<$lm){$op=$op+1;$tst="0";}


echo "<script>window.location.replace('spl_sep_vision_db.php?op=".$op."&st=".$tst."');</script>";
exit();
}

if(!empty($_GET["op"]) && $_GET["op"] == "2"){

echo "Processing 2 .. Records: ".$st;
//2
$qry = "SELECT 
		
	c1.exam_date, c1.examDateDistance,
	c1.uid,
	c1.visSnellan, c1.visSnellan_near,
	c1.vis_dis_near_desc, c1.vis_dis_desc, c1.vis_near_desc, c1.vis_dis_act_3, c1.vis_dis_act_4,
	c1.vis_dis_od_sel_1, c1.vis_dis_od_sel_2, c1.vis_near_od_sel_1, c1.vis_near_od_sel_2, c1.vis_dis_od_sel_3, c1.vis_dis_od_sel_4, 
	c1.vis_dis_od_txt_1, c1.vis_dis_od_txt_2, c1.vis_near_od_txt_1, c1.vis_near_od_txt_2, c1.vis_dis_od_txt_3, c1.vis_dis_od_txt_4,
	c1.vis_dis_os_sel_1, c1.vis_dis_os_sel_2, c1.vis_near_os_sel_1, c1.vis_near_os_sel_2, c1.vis_dis_os_sel_3, c1.vis_dis_od_sel_4 ,
	c1.vis_dis_os_txt_1, c1.vis_dis_os_txt_2, c1.vis_near_os_txt_1, c1.vis_near_os_txt_2, c1.vis_dis_os_txt_3, c1.vis_dis_os_txt_4,
	c1.vis_dis_ou_sel_1, c1.vis_dis_ou_sel_2, c1.vis_near_ou_sel_1, c1.vis_near_ou_sel_2, c1.vis_dis_ou_sel_3, c1.vis_dis_od_sel_4,
	c1.vis_dis_ou_txt_1, c1.vis_dis_ou_txt_2, c1.vis_near_ou_txt_1, c1.vis_near_ou_txt_2, c1.vis_dis_ou_txt_3, c1.vis_dis_ou_txt_4,
	
	c2.id AS chart_vis_master_id
	
	FROM chart_vision c1
	INNER JOIN chart_vis_master c2 ON c1.form_id = c2.form_id
	".$sql_where."	
	order by c1.vis_id 
	LIMIT ".$st.", ".$lm." ";

$res = imw_query($qry) or $msg_info[]=imw_error();
$chk_lm = imw_num_rows($res);
$sql_in = "";
while ($row = imw_fetch_assoc($res)) {	
	//
	$chart_vis_master_id = $row["chart_vis_master_id"];
	
	for($i=1; $i<=4; $i++){
		$sec_name = ($i==3) ? "Ad. Acuity" : "Distance"; 
		$sec_indx = $i;
		
		$tmp_desc = ""; $snellen="";
		if($i==1||$i==2){
			$tmp_desc = !empty($row["vis_dis_desc"]) ? $row["vis_dis_desc"] : $row["vis_dis_near_desc"];
			$snellen=$row["visSnellan"]; 
			
		}else if($i==3){
			$tmp_desc = $row["vis_dis_act_3"];
		}else if($i==4){
			$tmp_desc = $row["vis_dis_act_4"];
			
			if(empty($row["vis_dis_os_sel_".$i])){$row["vis_dis_os_sel_".$i]=$row["vis_dis_od_sel_".$i];}
			if(empty($row["vis_dis_ou_sel_".$i])){$row["vis_dis_ou_sel_".$i]=$row["vis_dis_od_sel_".$i];}
		}
		
		
		
		if( !empty($row["vis_dis_od_sel_".$i]) || !empty($row["vis_dis_od_txt_".$i]) ||
			!empty($row["vis_dis_os_sel_".$i]) || !empty($row["vis_dis_os_txt_".$i]) ||
			!empty($row["vis_dis_ou_sel_".$i]) || !empty($row["vis_dis_ou_txt_".$i]) ||
			!empty($tmp_desc)  || !empty($snellen)
		){
		
		$exam_date = (!empty($row["examDateDistance"]) &&  strpos($row["examDateDistance"], "0000") === false) ?  $row["examDateDistance"] : $row["exam_date"];
		$sql_in .= "( NULL, '".$chart_vis_master_id."', '".$exam_date."',  '".$row["uid"]."',  '".$sec_name."',  '".$sec_indx."',
							 '".sqlEscStr($snellen)."',  '".sqlEscStr($tmp_desc)."',  '".sqlEscStr($row["vis_dis_od_sel_".$i])."',  '".sqlEscStr($row["vis_dis_od_txt_".$i])."', 
							 '".sqlEscStr($row["vis_dis_os_sel_".$i])."',  '".sqlEscStr($row["vis_dis_os_txt_".$i])."',  
							 '".sqlEscStr($row["vis_dis_ou_sel_".$i])."',  '".sqlEscStr($row["vis_dis_ou_txt_".$i])."'
						),";
		
		
		
		}
	}	

	
	for($i=1; $i<=2; $i++){
		$sec_name = "Near"; 
		$sec_indx = $i;
		
		$tmp_desc = empty($row["vis_near_desc"]) ? $row["vis_dis_near_desc"] : $row["vis_near_desc"];
		
		$snellen=$row["visSnellan_near"]; 
		
		if( !empty($row["vis_near_od_sel_".$i]) || !empty($row["vis_near_od_txt_".$i]) ||
			!empty($row["vis_near_os_sel_".$i]) || !empty($row["vis_near_os_txt_".$i]) ||
			!empty($row["vis_near_ou_sel_".$i]) || !empty($row["vis_near_ou_txt_".$i]) ||
			!empty($tmp_desc) || !empty($snellen) 
		){
			$exam_date = $row["exam_date"];
			$sql_in .= "( NULL, '".$chart_vis_master_id."', '".$exam_date."',  '".$row["uid"]."',  '".$sec_name."',  '".$sec_indx."',
								 '".sqlEscStr($snellen)."',  '".sqlEscStr($tmp_desc)."',  '".sqlEscStr($row["vis_near_od_sel_".$i])."',  '".sqlEscStr($row["vis_near_od_txt_".$i])."', 
								 '".sqlEscStr($row["vis_near_os_sel_".$i])."',  '".sqlEscStr($row["vis_near_os_txt_".$i])."',  
								 '".sqlEscStr($row["vis_near_ou_sel_".$i])."',  '".sqlEscStr($row["vis_near_ou_txt_".$i])."'
							),";
			
			
		}
		
	}

}

if(!empty($sql_in)){
$sql_in = trim($sql_in,",");
$sql_in = "INSERT INTO chart_acuity (id, id_chart_vis_master, exam_date, uid, sec_name,sec_indx,
								snellen, ex_desc, sel_od, txt_od, sel_os, txt_os, sel_ou, txt_ou )
						VALUES ".$sql_in;
imw_query($sql_in) or $msg_info[]=imw_error();

}


$op=$_GET["op"];$tst=$st+$lm;
if($chk_lm<$lm){$op=$op+1;$tst="0";}

//OK
echo "<script>window.location.replace('spl_sep_vision_db.php?op=".$op."&st=".$tst."');</script>";
exit();
}//


if(!empty($_GET["op"]) && $_GET["op"] == "3"){

echo "Processing 3 .. Records: ".$st;

$qry = "SELECT 
		c1.exam_date, c1.examDateARAK,
		c1.uid,
		c1.vis_ak_od_k, c1.vis_ak_od_slash, c1.vis_ak_od_x,
		c1.vis_ak_os_k, c1.vis_ak_os_slash, c1.vis_ak_os_x, 
		c1.vis_ktype, c1.vis_dis_near_desc,		
		c2.id AS chart_vis_master_id	
	FROM chart_vision c1
	INNER JOIN chart_vis_master c2 ON c1.form_id = c2.form_id
	WHERE (vis_ak_od_k!='' || vis_ak_od_slash!='' || vis_ak_od_x!='' || vis_ak_os_k!='' ||
			vis_ak_os_slash!='' || vis_ak_os_x!='' || vis_ktype!='' || vis_ar_ak_desc!='' )
	".$sql_and."			
	order by c1.vis_id 
	LIMIT ".$st.", ".$lm."
	";

$res = imw_query($qry); //or $msg_info[]=imw_error()
$chk_lm = imw_num_rows($res);
$sql_in = "";
while ($row = imw_fetch_assoc($res)) {
	
	$chart_vis_master_id = $row["chart_vis_master_id"];
	if( !empty($row["vis_ak_od_k"]) || !empty($row["vis_ak_od_slash"]) ||
			!empty($row["vis_ak_od_x"]) || !empty($row["vis_ak_os_k"]) ||
			!empty($row["vis_ak_os_slash"]) || !empty($row["vis_ak_os_x"]) ||
			!empty($row["vis_ktype"]) || !empty($row["vis_dis_near_desc"]) 
		){
		
		$exam_date = !empty($row["examDateARAK"]) ? $row["examDateARAK"] : $row["exam_date"] ;
		$uid = $row["uid"];
		//vis_ar_ak_descs
		$sql_in .= "( NULL, '".$chart_vis_master_id."', '".$exam_date."',  '".$uid."',  
							 '".sqlEscStr($row["vis_ak_od_k"])."',  '".sqlEscStr($row["vis_ak_od_slash"])."', 
							 '".sqlEscStr($row["vis_ak_od_x"])."',  '".sqlEscStr($row["vis_ak_os_k"])."',  
							 '".sqlEscStr($row["vis_ak_os_slash"])."',  '".sqlEscStr($row["vis_ak_os_x"])."',
							 '".sqlEscStr($row["vis_ktype"])."',  '".sqlEscStr($row["vis_dis_near_desc"])."'
						),";
		
		
		
	}
}

if(!empty($sql_in)){
$sql_in = trim($sql_in,",");
$sql_in = "INSERT INTO chart_ak (id, id_chart_vis_master, exam_date, uid, 
							k_od, slash_od, x_od, k_os, slash_os, x_os, k_type, ex_desc )
					VALUES ".$sql_in;
imw_query($sql_in) or $msg_info[]=imw_error();

}


$op=$_GET["op"];$tst=$st+$lm;
if($chk_lm<$lm){$op=$op+1;$tst="0";}

echo "<script>window.location.replace('spl_sep_vision_db.php?op=".$op."&st=".$tst."');</script>";
exit();
}

if(!empty($_GET["op"]) && $_GET["op"] == "4"){

echo "Processing 4 .. Records: ".$st;

$qry = "SELECT 
		c1.exam_date, 
		c1.uid,
		c1.vis_ar_od_s, c1.vis_exo_od_s, c1.visCycloOdS, c1.visCycArOdS,
		c1.vis_ar_od_c, c1.vis_exo_od_c, c1.visCycloOdC, c1.visCycArOdC,
		c1.vis_ar_od_a, c1.vis_exo_od_a, c1.visCycloOdA, c1.visCycArOdA,
		c1.vis_ar_od_sel_1, c1.visCycArOdSel1,
		c1.vis_ar_os_s, c1.vis_exo_os_s, c1.visCycloOsS, c1.visCycArOsS,
		c1.vis_ar_os_c, c1.vis_exo_os_c, c1.visCycloOsC, c1.visCycArOsC,
		c1.vis_ar_os_a, c1.vis_exo_os_a, c1.visCycloOsA, c1.visCycArOsA,
		c1.vis_ar_os_sel_1, c1.visCycArOsSel1,
		c1.visCycArDesc, c1.vis_ar_ak_desc,
		c1.vis_ar_ref_place,

		c2.id AS chart_vis_master_id	
	FROM chart_vision c1
	INNER JOIN chart_vis_master c2 ON c1.form_id = c2.form_id
	".$sql_where."
	order by c1.vis_id 
	LIMIT ".$st.", ".$lm."
	";

$res = imw_query($qry); //or $msg_info[]=imw_error()
$chk_lm = imw_num_rows($res);
$sql_in = ""; $sql_in_2="";
while ($row = imw_fetch_assoc($res)) {
	
	$chart_vis_master_id = $row["chart_vis_master_id"];
	$uid = $row["uid"];
	$exam_date = $row["exam_date"] ;
	
	$ex_desc = $row["vis_ar_ak_desc"]; 
	
	if(empty($row["vis_ar_os_sel_1"])){ $row["vis_ar_os_sel_1"] = $row["vis_ar_od_sel_1"]; }
	
	//ar
	if(!empty($row["vis_ar_od_s"]) || !empty($row["vis_ar_od_c"]) || !empty($row["vis_ar_od_a"]) || !empty($row["vis_ar_od_sel_1"]) ||
		!empty($row["vis_ar_os_s"]) || !empty($row["vis_ar_os_c"]) || !empty($row["vis_ar_os_a"]) || !empty($row["vis_ar_os_sel_1"]) ||
		!empty($row["vis_ar_ref_place"]) || !empty($ex_desc) ){
		$sql_in .= "(NULL, '".$chart_vis_master_id."', '".$exam_date."', '".$uid."',
									'AR', '".sqlEscStr($row["vis_ar_od_s"])."', '".sqlEscStr($row["vis_ar_od_c"])."', '".sqlEscStr($row["vis_ar_od_a"])."', '".sqlEscStr($row["vis_ar_od_sel_1"])."', 
									'".sqlEscStr($row["vis_ar_os_s"])."', '".sqlEscStr($row["vis_ar_os_c"])."', '".sqlEscStr($row["vis_ar_os_a"])."', '".sqlEscStr($row["vis_ar_os_sel_1"])."', 
									'".sqlEscStr($ex_desc)."', '".sqlEscStr($row["vis_ar_ref_place"])."' 
									),";
							
	}
	
	//arc
	if(empty($row["visCycArOsSel1"])){ $row["visCycArOsSel1"] = $row["visCycArOdSel1"]; }
	if(!empty($row["visCycArOdS"]) || !empty($row["visCycArOdC"]) || !empty($row["visCycArOdA"]) || !empty($row["visCycArOdSel1"]) ||
		!empty($row["visCycArOsS"]) || !empty($row["visCycArOsC"]) || !empty($row["visCycArOsA"]) || !empty($row["visCycArOsSel1"]) ||
		!empty($row["visCycArDesc"]) ){
		$sql_in .= "(NULL, '".$chart_vis_master_id."', '".$exam_date."', '".$uid."',
									'ARC', '".sqlEscStr($row["visCycArOdS"])."', '".sqlEscStr($row["visCycArOdC"])."', '".sqlEscStr($row["visCycArOdA"])."', '".sqlEscStr($row["visCycArOdSel1"])."', 
									'".sqlEscStr($row["visCycArOsS"])."', '".sqlEscStr($row["visCycArOsC"])."', '".sqlEscStr($row["visCycArOsA"])."', '".sqlEscStr($row["visCycArOsSel1"])."', 
									'".sqlEscStr($row["visCycArDesc"])."', '' 
									),";
								
	}
	
	
	//RETINOSCOPY
	if(!empty($row["vis_exo_od_s"]) || !empty($row["vis_exo_od_c"]) || !empty($row["vis_exo_od_a"])  ||
		!empty($row["vis_exo_os_s"]) || !empty($row["vis_exo_os_c"]) || !empty($row["vis_exo_os_a"]) 
		 ){
		$sql_in_2 .= "(NULL, '".$chart_vis_master_id."', '".$exam_date."', '".$uid."',
									'RETINOSCOPY', '".sqlEscStr($row["vis_exo_od_s"])."', '".sqlEscStr($row["vis_exo_od_c"])."', '".sqlEscStr($row["vis_exo_od_a"])."',
									'".sqlEscStr($row["vis_exo_os_s"])."', '".sqlEscStr($row["vis_exo_os_c"])."', '".sqlEscStr($row["vis_exo_os_a"])."'
									),";
						
	}
	
	//CYCLOPLEGIC RETINO
	if(!empty($row["visCycloOdS"]) || !empty($row["visCycloOdC"]) || !empty($row["visCycloOdA"])  ||
		!empty($row["visCycloOsS"]) || !empty($row["visCycloOsC"]) || !empty($row["visCycloOsA"]) 
		 ){
		$sql_in_2 .= "(NULL, '".$chart_vis_master_id."', '".$exam_date."', '".$uid."',
									'CYCLOPLEGIC RETINO', '".sqlEscStr($row["visCycloOdS"])."', '".sqlEscStr($row["visCycloOdC"])."', '".sqlEscStr($row["visCycloOdA"])."',
									'".sqlEscStr($row["visCycloOsS"])."', '".sqlEscStr($row["visCycloOsC"])."', '".sqlEscStr($row["visCycloOsA"])."'
									),";
							
	}	
}

if(!empty($sql_in)){
$sql_in = trim($sql_in,",");
$sql_in = "INSERT INTO chart_sca (id, id_chart_vis_master, exam_date, uid, 
							sec_name, s_od, c_od, a_od, sel_od, 
								s_os, c_os, a_os, sel_os, ex_desc, ar_ref_place)
								VALUES ".$sql_in;
imw_query($sql_in) or $msg_info[]=imw_error();

}

if(!empty($sql_in_2)){
$sql_in_2 = trim($sql_in_2,",");
$sql_in_2 = "INSERT INTO chart_sca (id, id_chart_vis_master, exam_date, uid, 
							sec_name, s_od, c_od, a_od,  
								s_os, c_os, a_os)
								VALUES ".$sql_in_2;
imw_query($sql_in_2) or $msg_info[]=imw_error();

}

$op=$_GET["op"];$tst=$st+$lm;
if($chk_lm<$lm){$op=$op+1;$tst="0";}

echo "<script>window.location.replace('spl_sep_vision_db.php?op=".$op."&st=".$tst."');</script>";
exit();
}


if(!empty($_GET["op"]) && $_GET["op"] == "5"){

echo "Processing 5 .. Records: ".$st;

$qry = "SELECT 
		c1.exam_date, 
		c1.uid,
		vis_ret_pd, vis_ret_pd_od, vis_ret_pd_os,
		
		c2.id AS chart_vis_master_id	
	FROM chart_vision c1
	INNER JOIN chart_vis_master c2 ON c1.form_id = c2.form_id
	".$sql_where."
	order by c1.vis_id 
	LIMIT ".$st.", ".$lm."
	";

$res = imw_query($qry); //or $msg_info[]=imw_error()
$chk_lm = imw_num_rows($res);
$sql_in = "";
while ($row = imw_fetch_assoc($res)) {
	
	$chart_vis_master_id = $row["chart_vis_master_id"];
	$uid = $row["uid"];
	$exam_date = $row["exam_date"] ;
	
	//EXOPHTHALMOMETER
	if(!empty($row["vis_ret_pd"]) || !empty($row["vis_ret_pd_od"]) || !empty($row["vis_ret_pd_os"]) 
		 ){
		$sql_in .= "(NULL, '".$chart_vis_master_id."', '".$exam_date."', '".$uid."',
									'".sqlEscStr($row["vis_ret_pd"])."', '".sqlEscStr($row["vis_ret_pd_od"])."', '".sqlEscStr($row["vis_ret_pd_os"])."'
									),";
							
	}
}

if(!empty($sql_in)){
$sql_in = trim($sql_in,",");
$sql_in = "INSERT INTO chart_exo (id, id_chart_vis_master, exam_date, uid, 
							pd, pd_od, pd_os)
								VALUES ".$sql_in;
imw_query($sql_in) or $msg_info[]=imw_error();

}

$op=$_GET["op"];$tst=$st+$lm;
if($chk_lm<$lm){$op=$op+1;$tst="0";}

echo "<script>window.location.replace('spl_sep_vision_db.php?op=".$op."&st=".$tst."');</script>";
exit();
}

if(!empty($_GET["op"]) && $_GET["op"] == "6"){

echo "Processing 6 .. Records: ".$st;

$qry = "SELECT 
		c1.exam_date, 
		c1.uid,
		vis_bat_nl_od,
		vis_bat_low_od,
		vis_bat_med_od,
		vis_bat_high_od,
		vis_bat_nl_os,
		vis_bat_low_os,
		vis_bat_med_os,
		vis_bat_high_os,
		vis_bat_nl_ou,
		vis_bat_low_ou,
		vis_bat_med_ou,
		vis_bat_high_ou,
		vis_bat_desc,
		
		c2.id AS chart_vis_master_id	
	FROM chart_vision c1
	INNER JOIN chart_vis_master c2 ON c1.form_id = c2.form_id
	".$sql_where."
	order by c1.vis_id 
	LIMIT ".$st.", ".$lm."
	";

$res = imw_query($qry); //or $msg_info[]=imw_error()
$chk_lm = imw_num_rows($res);
$sql_in = "";
while ($row = imw_fetch_assoc($res)) {
	
	$chart_vis_master_id = $row["chart_vis_master_id"];
	$uid = $row["uid"];
	$exam_date = $row["exam_date"] ;
	
	//BAT
	if(!empty($row["vis_bat_nl_od"]) || !empty($row["vis_bat_low_od"]) || !empty($row["vis_bat_med_od"]) || !empty($row["vis_bat_high_od"]) ||
		!empty($row["vis_bat_nl_os"]) || !empty($row["vis_bat_low_os"]) || !empty($row["vis_bat_med_os"]) || !empty($row["vis_bat_high_os"]) ||
		!empty($row["vis_bat_nl_ou"]) || !empty($row["vis_bat_low_ou"]) || !empty($row["vis_bat_med_ou"]) || !empty($row["vis_bat_high_ou"]) ||
		!empty($row["vis_bat_desc"]) 
		 ){
		$sql_in .= "(NULL, '".$chart_vis_master_id."', '".$exam_date."', '".$uid."',
									'".sqlEscStr($row["vis_bat_nl_od"])."', '".sqlEscStr($row["vis_bat_low_od"])."', '".sqlEscStr($row["vis_bat_med_od"])."', '".sqlEscStr($row["vis_bat_high_od"])."',
									'".sqlEscStr($row["vis_bat_nl_os"])."', '".sqlEscStr($row["vis_bat_low_os"])."', '".sqlEscStr($row["vis_bat_med_os"])."', '".sqlEscStr($row["vis_bat_high_os"])."',
									'".sqlEscStr($row["vis_bat_nl_ou"])."', '".sqlEscStr($row["vis_bat_low_ou"])."', '".sqlEscStr($row["vis_bat_med_ou"])."', '".sqlEscStr($row["vis_bat_high_ou"])."',
									'".sqlEscStr($row["vis_bat_desc"])."'
									),";
								
								
	}
}

if(!empty($sql_in)){
$sql_in = trim($sql_in,",");
$sql_in = "INSERT INTO chart_bat (id, id_chart_vis_master, exam_date, uid, 
							nl_od,l_od,m_od,h_od,nl_os,l_os,m_os,h_os,nl_ou,l_ou,m_ou,h_ou,ex_desc)
								VALUES ".$sql_in;
imw_query($sql_in) or $msg_info[]=imw_error();

}

$op=$_GET["op"];$tst=$st+$lm;
if($chk_lm<$lm){$op=$op+1;$tst="0";}

echo "<script>window.location.replace('spl_sep_vision_db.php?op=".$op."&st=".$tst."');</script>";
exit();
}

if(!empty($_GET["op"]) && $_GET["op"] == "7"){

echo "Processing 7 .. Records: ".$st;

$qry = "SELECT 
		c1.exam_date, 
		c1.uid,
		vis_pam_od_txt_1,
		vis_pam_od_txt_2,
		vis_pam_os_txt_1,
		vis_pam_os_txt_2,
		vis_pam_ou_txt_1,
		vis_pam_ou_txt_2,
		vis_pam_od_sel_1,
		vis_pam_od_sel_2,
		vis_pam_desc,
		visPam,
		c2.id AS chart_vis_master_id	
	FROM chart_vision c1
	INNER JOIN chart_vis_master c2 ON c1.form_id = c2.form_id
	".$sql_where."
	order by c1.vis_id 
	LIMIT ".$st.", ".$lm."
	";

$res = imw_query($qry); //or $msg_info[]=imw_error()
$chk_lm = imw_num_rows($res);
$sql_in = "";
while ($row = imw_fetch_assoc($res)) {
	
	$chart_vis_master_id = $row["chart_vis_master_id"];
	$uid = $row["uid"];
	$exam_date = $row["exam_date"] ;
	
	//PAM
	if(!empty($row["vis_pam_od_txt_1"]) || !empty($row["vis_pam_od_txt_2"]) || !empty($row["vis_pam_os_txt_1"]) || !empty($row["vis_pam_os_txt_2"]) ||
		!empty($row["vis_pam_ou_txt_1"]) || !empty($row["vis_pam_ou_txt_2"]) ||
		!empty($row["vis_pam_od_sel_1"]) || !empty($row["vis_pam_od_sel_2"]) ||
		!empty($row["vis_pam_desc"]) ||
		!empty($row["visPam"]) 
		 ){
		$sql_in .= "(NULL, '".$chart_vis_master_id."', '".$exam_date."', '".$uid."',
									'".sqlEscStr($row["vis_pam_od_txt_1"])."', '".sqlEscStr($row["vis_pam_od_txt_2"])."', '".sqlEscStr($row["vis_pam_os_txt_1"])."', '".sqlEscStr($row["vis_pam_os_txt_2"])."',
									'".sqlEscStr($row["vis_pam_ou_txt_1"])."', '".sqlEscStr($row["vis_pam_ou_txt_2"])."', '".sqlEscStr($row["vis_pam_od_sel_1"])."', '".sqlEscStr($row["vis_pam_od_sel_2"])."',
									'".sqlEscStr($row["vis_pam_desc"])."', '".sqlEscStr($row["visPam"])."'
									),";
							
	}
}

if(!empty($sql_in)){
$sql_in = trim($sql_in,",");
$sql_in = "INSERT INTO chart_pam (id, id_chart_vis_master, exam_date, uid, 
							txt1_od,txt2_od,txt1_os,txt2_os,txt1_ou,txt2_ou,sel1,sel2,ex_desc,pam)
								VALUES ".$sql_in;
imw_query($sql_in) or $msg_info[]=imw_error();

}

$op=$_GET["op"];$tst=$st+$lm;
if($chk_lm<$lm){$op=$op+1;$tst="0";}

echo "<script>window.location.replace('spl_sep_vision_db.php?op=".$op."&st=".$tst."');</script>";
exit();
}

if(!empty($_GET["op"]) && $_GET["op"] == "8"){

	echo "Processing 8 .. Records: ".$st;
	
	
	
	$qry = "SELECT c1.id, c2.id AS id_chart_vis_master  FROM chart_pc_mr c1
		INNER JOIN chart_vis_master c2 ON c1.form_id = c2.form_id
		ORDER BY c1.id
		LIMIT ".$st.", ".$lm."
		";
	$res = imw_query($qry); //or $msg_info[]=imw_error()
	$chk_lm = imw_num_rows($res);
	while ($row = imw_fetch_assoc($res)) {
		$sql = "UPDATE chart_pc_mr SET id_chart_vis_master='".$row["id_chart_vis_master"]."' WHERE id ='".$row["id"]."'  ";
		imw_query($sql);
	}
	
	$op=$_GET["op"];$tst=$st+$lm;
	if($chk_lm<$lm){$op=$op+1;$tst="0";}
	
	echo "<script>window.location.replace('spl_sep_vision_db.php?op=".$op."&st=".$tst."');</script>";
	exit();
}


if(!empty($_GET["op"]) && $_GET["op"] == "9"){

echo "Processing 9 .. Records: ".$st;

$id_pc_mr = 0;
$sql = "SELECT max(id) as num FROM chart_pc_mr ";
$res = imw_query($sql);
$row = imw_fetch_assoc($res);
if($row!=false){
	$id_pc_mr = $row["num"];
}




$qry = "SELECT 
		c1.exam_date, 
		c1.uid,
		
		examDatePC, pc_distance, 
		pc_near, pc_near_2,pc_near_3,
		vis_pc_desc,  vis_pc_desc_2, vis_pc_desc_3,
		visPcPrismDesc_1,visPcPrismDesc_2,visPcPrismDesc_3,
		
		vis_pc_od_s,vis_pc_os_s, vis_pc_od_s_2,vis_pc_os_s_2,vis_pc_od_s_3,vis_pc_os_s_3,
		vis_pc_od_c,vis_pc_os_c, vis_pc_od_c_2,vis_pc_os_c_2,vis_pc_od_c_3,vis_pc_os_c_3,
		vis_pc_od_a,vis_pc_os_a,vis_pc_od_a_2,vis_pc_os_a_2,vis_pc_od_a_3,vis_pc_os_a_3,
		vis_pc_od_add,vis_pc_os_add,vis_pc_od_add_2,vis_pc_os_add_2,vis_pc_od_add_3,vis_pc_os_add_3,
		vis_pc_od_p,vis_pc_os_p, vis_pc_od_p_2,vis_pc_os_p_2,vis_pc_od_p_3,vis_pc_os_p_3,
		vis_pc_od_prism,vis_pc_os_prism, vis_pc_od_prism_2,vis_pc_os_prism_2,vis_pc_od_prism_3,vis_pc_os_prism_3,
		vis_pc_od_slash,vis_pc_os_slash,vis_pc_od_slash_2,vis_pc_os_slash_2,vis_pc_od_slash_3,vis_pc_os_slash_3,
		vis_pc_od_sel_1, vis_pc_od_sel_1_2, vis_pc_od_sel_1_3,vis_pc_os_sel_1,vis_pc_os_sel_1_2,vis_pc_os_sel_1_3,
		vis_pc_od_sel_2,vis_pc_os_sel_2,vis_pc_od_sel_2_2,vis_pc_os_sel_2_2,vis_pc_od_sel_2_3,vis_pc_os_sel_2_3,
		vis_pc_od_overref_s, vis_pc_os_overref_s,vis_pc_od_overref_s_2,vis_pc_os_overref_s_2,vis_pc_od_overref_s_3,vis_pc_os_overref_s_3,
		vis_pc_od_overref_c, vis_pc_os_overref_c,vis_pc_od_overref_c_2,vis_pc_os_overref_c_2,vis_pc_od_overref_c_3,vis_pc_os_overref_c_3,
		vis_pc_od_overref_v, vis_pc_os_overref_v,vis_pc_od_overref_v_2,vis_pc_os_overref_v_2,vis_pc_od_overref_v_3,vis_pc_os_overref_v_3,
		vis_pc_od_overref_a, vis_pc_os_overref_a,vis_pc_od_overref_a_2,vis_pc_os_overref_a_2,vis_pc_od_overref_a_3,vis_pc_os_overref_a_3,
		vis_pc_od_near_txt,vis_pc_os_near_txt,vis_pc_od_near_txt_2,vis_pc_os_near_txt_2,vis_pc_od_near_txt_3,vis_pc_os_near_txt_3,
		
		c2.id AS chart_vis_master_id	
	FROM chart_vision c1
	INNER JOIN chart_vis_master c2 ON c1.form_id = c2.form_id
	".$sql_where."
	order by c1.vis_id 
	LIMIT ".$st.", ".$lm."
	";

$res = imw_query($qry); //or $msg_info[]=imw_error()
$chk_lm = imw_num_rows($res);

$sql_in = "";
$sql_in_2 = "";



while ($row = imw_fetch_assoc($res)) {
	
	$chart_vis_master_id = $row["chart_vis_master_id"];
	$uid = $row["uid"];
	$exam_date = (!empty($row["examDatePC"]) && strpos($row["examDatePC"],"0000") === false) ? $row["examDatePC"] : $row["exam_date"];
	
	//Pc1
	for($i=1;$i<=3;$i++){
		
		$sfx1 = ($i>1) ? "_".$i : "";
		
		if(!empty($row["vis_pc_od_s".$sfx1]) || !empty($row["vis_pc_os_s".$sfx1]) || 
			!empty($row["vis_pc_od_c".$sfx1]) || !empty($row["vis_pc_os_c".$sfx1]) ||
			!empty($row["vis_pc_od_a".$sfx1]) || !empty($row["vis_pc_os_a".$sfx1]) ||
			!empty($row["vis_pc_od_add".$sfx1]) || !empty($row["vis_pc_os_add".$sfx1]) ||
			!empty($row["vis_pc_od_p".$sfx1]) || !empty($row["vis_pc_os_p".$sfx1]) ||
			!empty($row["vis_pc_od_prism".$sfx1]) || !empty($row["vis_pc_os_prism".$sfx1]) ||
			!empty($row["vis_pc_od_slash".$sfx1]) || !empty($row["vis_pc_os_slash".$sfx1]) ||
			!empty($row["vis_pc_od_sel_1".$sfx1]) || !empty($row["vis_pc_os_sel_1".$sfx1]) ||
			!empty($row["vis_pc_od_sel_2".$sfx1]) || !empty($row["vis_pc_os_sel_2".$sfx1]) ||
			
			!empty($row["vis_pc_od_overref_s".$sfx1]) || !empty($row["vis_pc_os_overref_s".$sfx1]) ||
			!empty($row["vis_pc_od_overref_c".$sfx1]) || !empty($row["vis_pc_os_overref_c".$sfx1]) ||
			!empty($row["vis_pc_od_overref_v".$sfx1]) || !empty($row["vis_pc_os_overref_v".$sfx1]) ||
			!empty($row["vis_pc_od_overref_a".$sfx1]) || !empty($row["vis_pc_os_overref_a".$sfx1]) ||
			
			!empty($row["vis_pc_od_near_txt".$sfx1]) || !empty($row["vis_pc_os_near_txt".$sfx1]) ||
			
			
			!empty($row["pc_near".$sfx1]) ||		
			!empty($row["vis_pc_desc".$sfx1]) ||		
			!empty($row["visPcPrismDesc_".$i]) 
			
			){
				$id_pc_mr = $id_pc_mr+1;
				$chart_pc_mr_id = $id_pc_mr;
				$tmp_pc_distance = (strpos($row["pc_distance"], "".$i) !== false) ? "1" : "0";
				$tmp_pc_near = ($row["pc_near".$sfx1] == "Near") ? "1" : "0";
				
				$sql_in .= " ('".$chart_pc_mr_id."', '".$chart_vis_master_id."', '".$exam_date."', 'PC', '".$i."', '".sqlEscStr($tmp_pc_distance)."', '".sqlEscStr($tmp_pc_near)."', '".sqlEscStr($row["vis_pc_desc".$sfx1])."', '".sqlEscStr($row["visPcPrismDesc_".$i])."', '".$uid."'),";
				
				
				
				//OD
				if(!empty($row["vis_pc_od_s".$sfx1]) || 
					!empty($row["vis_pc_od_c".$sfx1]) || !empty($row["vis_pc_od_a".$sfx1]) || !empty($row["vis_pc_od_add".$sfx1]) || !empty($row["vis_pc_od_p".$sfx1]) ||
					!empty($row["vis_pc_od_prism".$sfx1]) || !empty($row["vis_pc_od_slash".$sfx1]) || !empty($row["vis_pc_od_sel_1".$sfx1]) || 
					!empty($row["vis_pc_od_sel_2".$sfx1]) || !empty($row["vis_pc_od_overref_s".$sfx1]) ||  !empty($row["vis_pc_od_overref_c".$sfx1]) ||  
					!empty($row["vis_pc_od_overref_v".$sfx1]) ||  !empty($row["vis_pc_od_overref_a".$sfx1]) || !empty($row["vis_pc_od_near_txt".$sfx1]) 					
				){
				$sql_in_2 .= "(NULL,'".$chart_pc_mr_id."', 'OD',
								'".sqlEscStr($row["vis_pc_od_s".$sfx1])."','".sqlEscStr($row["vis_pc_od_c".$sfx1])."','".sqlEscStr($row["vis_pc_od_a".$sfx1])."','".sqlEscStr($row["vis_pc_od_add".$sfx1])."'
								,'".sqlEscStr($row["vis_pc_od_p".$sfx1])."','".sqlEscStr($row["vis_pc_od_prism".$sfx1])."','".sqlEscStr($row["vis_pc_od_slash".$sfx1])."','".sqlEscStr($row["vis_pc_od_sel_1".$sfx1])."'
								,'".sqlEscStr($row["vis_pc_od_sel_2".$sfx1])."','".sqlEscStr($row["vis_pc_od_overref_s".$sfx1])."','".sqlEscStr($row["vis_pc_od_overref_c".$sfx1])."','".sqlEscStr($row["vis_pc_od_overref_v".$sfx1])."'
								,'".sqlEscStr($row["vis_pc_od_overref_a".$sfx1])."','".sqlEscStr($row["vis_pc_od_near_txt".$sfx1])."'),";
				
				
				}	
				
				//OS
				if(!empty($row["vis_pc_os_s".$sfx1]) || 
					!empty($row["vis_pc_os_c".$sfx1]) || !empty($row["vis_pc_os_a".$sfx1]) || !empty($row["vis_pc_os_add".$sfx1]) || !empty($row["vis_pc_os_p".$sfx1]) ||
					!empty($row["vis_pc_os_prism".$sfx1]) || !empty($row["vis_pc_os_slash".$sfx1]) || !empty($row["vis_pc_os_sel_1".$sfx1]) || 
					!empty($row["vis_pc_os_sel_2".$sfx1]) || !empty($row["vis_pc_os_overref_s".$sfx1]) ||  !empty($row["vis_pc_os_overref_c".$sfx1]) ||  
					!empty($row["vis_pc_os_overref_v".$sfx1]) ||  !empty($row["vis_pc_os_overref_a".$sfx1]) || !empty($row["vis_pc_os_near_txt".$sfx1]) 					
				){
				$sql_in_2 .= "(NULL,'".$chart_pc_mr_id."', 'OS',
								'".sqlEscStr($row["vis_pc_os_s".$sfx1])."','".sqlEscStr($row["vis_pc_os_c".$sfx1])."','".sqlEscStr($row["vis_pc_os_a".$sfx1])."','".sqlEscStr($row["vis_pc_os_add".$sfx1])."'
								,'".sqlEscStr($row["vis_pc_os_p".$sfx1])."','".sqlEscStr($row["vis_pc_os_prism".$sfx1])."','".sqlEscStr($row["vis_pc_os_slash".$sfx1])."','".sqlEscStr($row["vis_pc_os_sel_1".$sfx1])."'
								,'".sqlEscStr($row["vis_pc_os_sel_2".$sfx1])."','".sqlEscStr($row["vis_pc_os_overref_s".$sfx1])."','".sqlEscStr($row["vis_pc_os_overref_c".$sfx1])."','".sqlEscStr($row["vis_pc_os_overref_v".$sfx1])."'
								,'".sqlEscStr($row["vis_pc_os_overref_a".$sfx1])."','".sqlEscStr($row["vis_pc_os_near_txt".$sfx1])."'),";
				
				
				}	
		}
	}

}

if(!empty($sql_in)){
$sql_in = trim($sql_in,",");
$sql_in = "INSERT INTO chart_pc_mr (id, id_chart_vis_master, exam_date, ex_type, ex_number, pc_distance, pc_near, ex_desc, prism_desc, uid)
						VALUES ".$sql_in;
imw_query($sql_in) or $msg_info[]=imw_error();

}

if(!empty($sql_in_2)){
$sql_in_2 = trim($sql_in_2,",");
$sql_in_2 = "INSERT INTO chart_pc_mr_values (id,chart_pc_mr_id,site,
							sph,cyl,axs,ad,
							prsm_p,prism,slash,sel_1,
							sel_2,ovr_s,ovr_c,ovr_v,
							ovr_a,txt_1) 
						VALUES ".$sql_in_2;
imw_query($sql_in_2) or $msg_info[]=imw_error();

}


$op=$_GET["op"];$tst=$st+$lm;
if($chk_lm<$lm){$op=$op+1;$tst="0";}

echo "<script>window.location.replace('spl_sep_vision_db.php?op=".$op."&st=".$tst."');</script>";
exit();
}

if(!empty($_GET["op"]) && $_GET["op"] == "10"){

echo "Processing 10 .. Records: ".$st;

$id_pc_mr = 0;
$sql = "SELECT max(id) as num FROM chart_pc_mr ";
$res = imw_query($sql);
$row = imw_fetch_assoc($res);
if($row!=false){
	$id_pc_mr = $row["num"];
}

$ar_idn = array(
				"1"=>array("od_s"=>"vis_mr_od_s","od_c"=>"vis_mr_od_c","od_a"=>"vis_mr_od_a","od_add"=>"vis_mr_od_add",
						"od_p"=>"vis_mr_od_p","od_prism"=>"vis_mr_od_prism","od_slash"=>"vis_mr_od_slash","od_sel_1"=>"vis_mr_od_sel_1",
						"od_sel_2"=>"vis_mr_od_sel_2","od_txt_1"=>"vis_mr_od_txt_1","od_txt_2"=>"vis_mr_od_txt_2","od_Sel2Vis"=>"visMrOdSel2Vision",
						
						"os_s"=>"vis_mr_os_s","os_c"=>"vis_mr_os_c","os_a"=>"vis_mr_os_a","os_add"=>"vis_mr_os_add",
						"os_p"=>"vis_mr_os_p","os_prism"=>"vis_mr_os_prism","os_slash"=>"vis_mr_os_slash","os_sel_1"=>"vis_mr_os_sel_1",
						"os_sel_2"=>"vis_mr_os_sel_2","os_txt_1"=>"vis_mr_os_txt_1","os_txt_2"=>"vis_mr_os_txt_2","os_Sel2Vis"=>"visMrOsSel2Vision",
						
						"pro_id"=>"provider_id","ou_txt_1"=>"vis_mr_ou_txt_1","desc"=>"vis_mr_desc"),
				
				"2"=>array("od_s"=>"vis_mr_od_given_s","od_c"=>"vis_mr_od_given_c","od_a"=>"vis_mr_od_given_a","od_add"=>"vis_mr_od_given_add",
						"od_p"=>"vis_mr_od_given_p","od_prism"=>"vis_mr_od_given_prism","od_slash"=>"vis_mr_od_given_slash","od_sel_1"=>"vis_mr_od_given_sel_1",
						"od_sel_2"=>"vis_mr_od_given_sel_2","od_txt_1"=>"vis_mr_od_given_txt_1","od_txt_2"=>"vis_mr_od_given_txt_2","od_Sel2Vis"=>"visMrOtherOdSel2Vision",
						
						"os_s"=>"vis_mr_os_given_s","os_c"=>"vis_mr_os_given_c","os_a"=>"vis_mr_os_given_a","os_add"=>"vis_mr_os_given_add",
						"os_p"=>"vis_mr_os_given_p","os_prism"=>"vis_mr_os_given_prism","os_slash"=>"vis_mr_os_given_slash","os_sel_1"=>"vis_mr_os_given_sel_1",
						"os_sel_2"=>"vis_mr_os_given_sel_2","os_txt_1"=>"vis_mr_os_given_txt_1","os_txt_2"=>"vis_mr_os_given_txt_2","os_Sel2Vis"=>"visMrOtherOsSel2Vision",
						
						"pro_id"=>"providerIdOther","ou_txt_1"=>"vis_mr_ou_given_txt_1","desc"=>"vis_mr_desc_other"),
						
				"3"=>array("od_s"=>"visMrOtherOdS_3","od_c"=>"visMrOtherOdC_3","od_a"=>"visMrOtherOdA_3","od_add"=>"visMrOtherOdAdd_3",
						"od_p"=>"visMrOtherOdP_3","od_prism"=>"visMrOtherOdPrism_3","od_slash"=>"visMrOtherOdSlash_3","od_sel_1"=>"visMrOtherOdSel1_3",
						"od_sel_2"=>"visMrOtherOdSel2_3","od_txt_1"=>"visMrOtherOdTxt1_3","od_txt_2"=>"visMrOtherOdTxt2_3","od_Sel2Vis"=>"visMrOtherOdSel2Vision_3",
						
						"os_s"=>"visMrOtherOsS_3","os_c"=>"visMrOtherOsC_3","os_a"=>"visMrOtherOsA_3","os_add"=>"visMrOtherOsAdd_3",
						"os_p"=>"visMrOtherOsP_3","os_prism"=>"visMrOtherOsPrism_3","os_slash"=>"visMrOtherOsSlash_3","os_sel_1"=>"visMrOtherOsSel1_3",
						"os_sel_2"=>"visMrOtherOsSel2_3","os_txt_1"=>"visMrOtherOsTxt1_3","os_txt_2"=>"visMrOtherOsTxt2_3","os_Sel2Vis"=>"visMrOtherOsSel2Vision_3",						
						
						"pro_id"=>"providerIdOther_3","ou_txt_1"=>"visMrOtherOuTxt1_3","desc"=>"vis_mr_desc_3")
			);

$qry = "SELECT 
		c1.exam_date, 
		c1.uid,
		
		examDateMR,
		provider_id, providerIdOther,providerIdOther_3,
		vis_mr_none_given,
		vis_mrcyclopegic,
		vis_mr_pres_dt_1,vis_mr_pres_dt_2,vis_mr_pres_dt_3,
		vis_mr_ou_txt_1,vis_mr_ou_given_txt_1,visMrOtherOuTxt1_3,
		vis_mr_type1,vis_mr_type2,vis_mr_type3,
		vis_mr_desc_other, vis_mr_desc_3, vis_mr_desc,
		visMrPrismDesc_1,visMrPrismDesc_2,visMrPrismDesc_3,
		provider_id, providerIdOther,providerIdOther_3,
		mr_hash,

		vis_mr_od_s,vis_mr_os_s,vis_mr_od_given_s,vis_mr_os_given_s,visMrOtherOdS_3,visMrOtherOsS_3,
		vis_mr_od_c,vis_mr_os_c,vis_mr_od_given_c,vis_mr_os_given_c,visMrOtherOdC_3,visMrOtherOsC_3,
		vis_mr_od_a,vis_mr_os_a,vis_mr_od_given_a,vis_mr_os_given_a,visMrOtherOdA_3,visMrOtherOsA_3,
		vis_mr_od_add,vis_mr_os_add,vis_mr_od_given_add,vis_mr_os_given_add,visMrOtherOdAdd_3,visMrOtherOsAdd_3,
		vis_mr_od_p,vis_mr_os_p,vis_mr_od_given_p,vis_mr_os_given_p,visMrOtherOdP_3,visMrOtherOsP_3,
		vis_mr_od_prism,vis_mr_os_prism,vis_mr_od_given_prism,vis_mr_os_given_prism,visMrOtherOdPrism_3,visMrOtherOsPrism_3,
		vis_mr_od_slash,vis_mr_os_slash,vis_mr_od_given_slash,vis_mr_os_given_slash,visMrOtherOdSlash_3,visMrOtherOsSlash_3,
		vis_mr_od_sel_1,vis_mr_os_sel_1,vis_mr_od_given_sel_1,vis_mr_os_given_sel_1,visMrOtherOdSel1_3,visMrOtherOsSel1_3,
		vis_mr_od_sel_2,vis_mr_os_sel_2,vis_mr_od_given_sel_2, vis_mr_os_given_sel_2, visMrOtherOdSel2_3,visMrOtherOsSel2_3,
		vis_mr_od_txt_1,vis_mr_os_txt_1,vis_mr_od_given_txt_1,vis_mr_os_given_txt_1,visMrOtherOdTxt1_3,visMrOtherOsTxt1_3,
		vis_mr_od_txt_2,vis_mr_os_txt_2,vis_mr_od_given_txt_2,vis_mr_os_given_txt_2,visMrOtherOdTxt2_3,visMrOtherOsTxt2_3,
		visMrOdSel2Vision,visMrOsSel2Vision,visMrOtherOdSel2Vision,visMrOtherOsSel2Vision,visMrOtherOdSel2Vision_3,visMrOtherOsSel2Vision_3,
		
		c2.id AS chart_vis_master_id	
	FROM chart_vision c1
	INNER JOIN chart_vis_master c2 ON c1.form_id = c2.form_id
	".$sql_where."
	order by c1.vis_id 
	LIMIT ".$st.", ".$lm."
	";

$res = imw_query($qry); //or $msg_info[]=imw_error()
$chk_lm = imw_num_rows($res);

$sql_in = "";
$sql_in_2 = "";

while ($row = imw_fetch_assoc($res)) {
	
	$chart_vis_master_id = $row["chart_vis_master_id"];
	$uid = $row["uid"];
	$exam_date = (!empty($row["examDateMR"]) && strpos($row["examDateMR"],"0000") === false) ? $row["examDateMR"] : $row["exam_date"];
	
	//Pc1
	for($i=1;$i<=3;$i++){
		
		if(!empty($row[$ar_idn[$i]["od_s"]]) || !empty($row[$ar_idn[$i]["os_s"]]) || 
			!empty($row[$ar_idn[$i]["od_c"]]) || !empty($row[$ar_idn[$i]["os_c"]]) ||
			!empty($row[$ar_idn[$i]["od_a"]]) || !empty($row[$ar_idn[$i]["os_a"]]) ||
			!empty($row[$ar_idn[$i]["od_add"]]) || !empty($row[$ar_idn[$i]["os_add"]]) ||
			!empty($row[$ar_idn[$i]["od_p"]]) || !empty($row[$ar_idn[$i]["os_p"]]) ||
			!empty($row[$ar_idn[$i]["od_prism"]]) || !empty($row[$ar_idn[$i]["os_prism"]]) ||
			!empty($row[$ar_idn[$i]["od_slash"]]) || !empty($row[$ar_idn[$i]["os_slash"]]) ||
			!empty($row[$ar_idn[$i]["od_sel_1"]]) || !empty($row[$ar_idn[$i]["os_sel_1"]]) ||
			!empty($row[$ar_idn[$i]["od_sel_2"]]) || !empty($row[$ar_idn[$i]["os_sel_2"]]) ||
			
			!empty($row[$ar_idn[$i]["od_txt_1"]]) || !empty($row[$ar_idn[$i]["os_txt_1"]]) ||
			!empty($row[$ar_idn[$i]["od_txt_2"]]) || !empty($row[$ar_idn[$i]["os_txt_2"]]) ||
			!empty($row[$ar_idn[$i]["od_Sel2Vis"]]) || !empty($row[$ar_idn[$i]["os_Sel2Vis"]]) ||
			
			!empty($row[$ar_idn[$i]["ou_txt_1"]]) ||
			!empty($row["vis_mr_none_given"]) ||
			!empty($row["vis_mrcyclopegic"]) ||
			!empty($row["vis_mr_pres_dt_".$i]) || !empty($row["visMrPrismDesc_".$i]) ||
			!empty($row["vis_mr_type".$i]) ||
			!empty($row[$ar_idn[$i]["desc"]]) ||	
			!empty($row[$ar_idn[$i]["pro_id"]]) 
			
			){
				$id_pc_mr = $id_pc_mr+1;
				$chart_pc_mr_id = $id_pc_mr;
				
				$tmp_mr_given = trim($row["vis_mr_none_given"]);
				if(!empty($tmp_mr_given)){
					if($tmp_mr_given=="None Given" || $tmp_mr_given=="None"){$tmp_mr_given="";}
					else if($tmp_mr_given=="Given"){$tmp_mr_given="MR 1";}
					else{						
						if(strpos($tmp_mr_given, "MR ".$i)!==false){$tmp_mr_given="MR ".$i;}else{ $tmp_mr_given=""; }
					}
				}				
				
				$tmp_mr_cyclopegic =  $row["vis_mrcyclopegic"];
				if(!empty($tmp_mr_cyclopegic)){
					if(strpos($tmp_mr_cyclopegic, "".$i)!==false){$tmp_mr_cyclopegic="1";}else{ $tmp_mr_cyclopegic=""; }
				}
				
				$ar_strhash = unserialize($row["mr_hash"]);
				$tmp_strhash = $ar_strhash[$i-1];
				
				$sql_in .= "('".$chart_pc_mr_id."', '".$chart_vis_master_id."', '".$exam_date."', '".$row[$ar_idn[$i]["pro_id"]]."', 
								'MR', '".$i."', '".sqlEscStr($tmp_mr_given)."', '".sqlEscStr($tmp_mr_cyclopegic)."', '".$row["vis_mr_pres_dt_".$i]."', '".sqlEscStr($row[$ar_idn[$i]["ou_txt_1"]])."',
								'".sqlEscStr($row["vis_mr_type".$i])."', '".sqlEscStr($row[$ar_idn[$i]["desc"]])."', '".sqlEscStr($row["visMrPrismDesc_".$i])."', '".$uid."', '".sqlEscStr($tmp_strhash)."'),";
				
				
				
				//OD
				if(!empty($row[$ar_idn[$i]["od_s"]]) || 
					!empty($row[$ar_idn[$i]["od_c"]]) || !empty($row[$ar_idn[$i]["od_a"]]) || !empty($row[$ar_idn[$i]["od_add"]]) || !empty($row[$ar_idn[$i]["od_p"]]) ||
					!empty($row[$ar_idn[$i]["od_prism"]]) || !empty($row[$ar_idn[$i]["od_slash"]]) || !empty($row[$ar_idn[$i]["od_sel_1"]]) || 
					!empty($row[$ar_idn[$i]["od_sel_2"]]) || !empty($row[$ar_idn[$i]["od_txt_1"]]) ||  !empty($row[$ar_idn[$i]["od_txt_2"]]) ||  
					!empty($row[$ar_idn[$i]["od_Sel2Vis"]]) 					
				){
				$sql_in_2 .= "(NULL,'".$chart_pc_mr_id."', 'OD',
								'".sqlEscStr($row[$ar_idn[$i]["od_s"]])."','".sqlEscStr($row[$ar_idn[$i]["od_c"]])."','".sqlEscStr($row[$ar_idn[$i]["od_a"]])."','".sqlEscStr($row[$ar_idn[$i]["od_add"]])."'
								,'".sqlEscStr($row[$ar_idn[$i]["od_p"]])."','".sqlEscStr($row[$ar_idn[$i]["od_prism"]])."','".sqlEscStr($row[$ar_idn[$i]["od_slash"]])."','".sqlEscStr($row[$ar_idn[$i]["od_sel_1"]])."'
								,'".sqlEscStr($row[$ar_idn[$i]["od_sel_2"]])."','".sqlEscStr($row[$ar_idn[$i]["od_txt_1"]])."','".sqlEscStr($row[$ar_idn[$i]["od_txt_2"]])."','".sqlEscStr($row[$ar_idn[$i]["od_Sel2Vis"]])."'
								),";
				
				
				}	
				
				//OS
				if(!empty($row[$ar_idn[$i]["os_s"]]) || 
					!empty($row[$ar_idn[$i]["os_c"]]) || !empty($row[$ar_idn[$i]["os_a"]]) || !empty($row[$ar_idn[$i]["os_add"]]) || !empty($row[$ar_idn[$i]["os_p"]]) ||
					!empty($row[$ar_idn[$i]["os_prism"]]) || !empty($row[$ar_idn[$i]["os_slash"]]) || !empty($row[$ar_idn[$i]["os_sel_1"]]) || 
					!empty($row[$ar_idn[$i]["os_sel_2"]]) || !empty($row[$ar_idn[$i]["os_txt_1"]]) ||  !empty($row[$ar_idn[$i]["os_txt_2"]]) ||  
					!empty($row[$ar_idn[$i]["os_Sel2Vis"]]) 					
				){
				$sql_in_2 .= "(NULL,'".$chart_pc_mr_id."', 'OS',
								'".sqlEscStr($row[$ar_idn[$i]["os_s"]])."','".sqlEscStr($row[$ar_idn[$i]["os_c"]])."','".sqlEscStr($row[$ar_idn[$i]["os_a"]])."','".sqlEscStr($row[$ar_idn[$i]["os_add"]])."'
								,'".sqlEscStr($row[$ar_idn[$i]["os_p"]])."','".sqlEscStr($row[$ar_idn[$i]["os_prism"]])."','".sqlEscStr($row[$ar_idn[$i]["os_slash"]])."','".sqlEscStr($row[$ar_idn[$i]["os_sel_1"]])."'
								,'".sqlEscStr($row[$ar_idn[$i]["os_sel_2"]])."','".sqlEscStr($row[$ar_idn[$i]["os_txt_1"]])."','".sqlEscStr($row[$ar_idn[$i]["os_txt_2"]])."','".sqlEscStr($row[$ar_idn[$i]["os_Sel2Vis"]])."'
								),";
				
				
				}
						
		}
	}
}


if(!empty($sql_in)){
$sql_in = trim($sql_in,",");
$sql_in = "INSERT INTO chart_pc_mr (id, id_chart_vis_master, exam_date, provider_id, 
										ex_type, ex_number, mr_none_given, mr_cyclopegic,mr_pres_date,
										mr_ou_txt_1, mr_type, ex_desc, prism_desc, uid, strhash)
						VALUES ".$sql_in;
imw_query($sql_in) or $msg_info[]=imw_error();

}

if(!empty($sql_in_2)){
$sql_in_2 = trim($sql_in_2,",");
$sql_in_2 = "INSERT INTO chart_pc_mr_values (id,chart_pc_mr_id,site,
							sph,cyl,axs,ad,
							prsm_p,prism,slash,sel_1,
							sel_2,
							txt_1, txt_2, sel2v) 
						VALUES ".$sql_in_2;
imw_query($sql_in_2) or $msg_info[]=imw_error();

}


$op=$_GET["op"];$tst=$st+$lm;
if($chk_lm<$lm){$op=$op+1;$tst="0";}

echo "<script>window.location.replace('spl_sep_vision_db.php?op=".$op."&st=".$tst."');</script>";
exit();
}


if(!empty($_GET["op"]) && $_GET["op"] == "11"){
echo "Processing complete. ";
//echo "<script>window.location.replace('spl_sep_vision_db.php?op=7');</script>";
exit();
}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Separate Vision Data (CN)</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body>
<br><br>
        <font face="Arial, Helvetica, sans-serif" color="<?php echo $color;?>" size="2">
            <?php echo(implode("<br>",$msg_info));?>
        </font>
</body>
</html>