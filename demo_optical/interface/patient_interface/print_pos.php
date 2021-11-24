<?php 
/*
File: print_pos.php
Coded in PHP7
Purpose: Print POS
Access Type: Direct access
*/
require_once(dirname('__FILE__')."/../../config/config.php"); 
require_once(dirname('__FILE__')."/../../library/classes/functions.php"); 
//if(isset($_REQUEST['order_sel_ids'])){
	//echo $_REQUEST['order_sel_ids']."kkkkkkkkkkkkk";
	$multi_order_ids=(isset($_REQUEST['order_id']))?$_REQUEST['order_id']:$_REQUEST['order_sel_ids'];
//}
function removespecialchar($str)
{
	return preg_replace('/[^A-Za-z0-9\+\/\-.()\[\]&]/', ' ',$str);
}
$pdfHTML = "";
$multi_order_ids = rtrim($multi_order_ids,",");
if($multi_order_ids!=''){
	  $mlti_IDs = explode(",",$multi_order_ids);
for($i=0;$i<count($mlti_IDs);$i++){

$orderId = $mlti_IDs[$i];

$query=imw_query("select module_type_id, patient_id, entered_date, loc_id from in_order_details where order_id='".$orderId."' order by module_type_id asc limit 0,1");
$getResult=imw_fetch_array($query);
//order date
$order_entered_date=date("m-d-Y", strtotime($getResult['entered_date']));
//order facility
$qLoc=imw_query("select loc_name from in_location where id='$getResult[loc_id]'");
$dLoc=imw_fetch_assoc($qLoc);
$order_loaction=$dLoc['loc_name'];
$patient_id=$getResult['patient_id'];

//----------------LAST DOS AND PHYSICIAN NAME - Lens-----------------------------//
$EyephysicianName = $lastDos = "";
$sqldos = 'SELECT DATE_FORMAT(`cm`.`date_of_service`, "%m-%d-%Y") AS "dos", IF(`u`.`fname`<>"" AND `u`.`lname`<>"", CONCAT(`u`.`lname`, ", ", `u`.`fname`), IF(`u`.`lname`<>"", `u`.`lname`, IF(`u`.`fname`<>"",`u`.`fname`,""))) AS "username", `cm`.`providerId` AS "phyId" FROM `chart_master_table` `cm` LEFT JOIN `users` `u` ON `cm`.`providerId`= `u`.`id` WHERE `cm`.`patient_id`="'.$patient_id.'" ORDER BY `cm`.`date_of_service` DESC LIMIT 1';
$sqldos = imw_query($sqldos);
if($sqldos && imw_num_rows($sqldos)>0){
	$row = imw_fetch_assoc($sqldos);
	$EyephysicianName = $row['username'];
	$lastDos = $row['dos'];
}
//-------------END LAST DOS AND PHYSICIAN NAME Lens---------------------------//
if($_REQUEST['section']!="" && $_REQUEST['section']!="undefined"){
	$section=$_REQUEST['section'];
}else if($getResult['module_type_id']==1 || $getResult['module_type_id']==2){
	$section='Frame and Lens Selection';
}
else{
	$section='Contact Lenses Selection';
}
if(isset($orderId))
{
	//$_SESSION['order_id']=$_REQUEST['order_id'];
}
if(isset($_REQUEST['order_det_ids']))
{
	$detIds=$_REQUEST['order_det_ids'];
	if(substr($detIds,strlen($detIds)-1,1)==',')
	$detIds=substr($detIds,0,strlen($detIds)-1);
	
	$whereDetIds=" AND id in ($detIds)";	
}
/*if($_REQUEST['section']=='Frame and Lense Selection'){
	$section=$_REQUEST['section'];
}
else{
	$section='Contact Lenses Selection';
}*/
//$patient_id=$_SESSION['patient_session_id'];
$in_locationID=$_SESSION['pro_fac_id'];


//------------------------	START GETTING DATA FOR MENUS TO Procedure CATEGORY-----------------------//
	$proc_code_arr=array();
	$sql = "select * from cpt_category_tbl order by cpt_category ASC";
	$rez = imw_query($sql);	
	while($row=imw_fetch_array($rez)){
		$cat_id = $row["cpt_cat_id"];		
		$sql = "select * from cpt_fee_tbl WHERE cpt_cat_id='".$cat_id."' AND status='active' AND delete_status = '0' order by cpt_prac_code ASC";
		$rezCodes = imw_query($sql);
		$arrSubOptions = array();
		if(imw_num_rows($rezCodes) > 0){
			while($rowCodes=imw_fetch_array($rezCodes)){
				$arrSubOptions[] = array($rowCodes["cpt_prac_code"]."-".$rowCodes["cpt_desc"],$xyz, $rowCodes["cpt_prac_code"]);
				$arrCptCodesAndDesc[] = $rowCodes["cpt_fee_id"];
				$arrCptCodesAndDesc[] = $rowCodes["cpt_prac_code"];
				$arrCptCodesAndDesc[] = $rowCodes["cpt_desc"];
				
				$code = $rowCodes["cpt_prac_code"];
				$cpt_desc = $rowCodes["cpt_desc"];
				$stringAllProcedures.="'".str_replace("'","",$code)."',";	
				$stringAllProcedures.="'".str_replace("'","",$cpt_desc)."',";
				$proc_code_arr[$rowCodes["cpt_fee_id"]]=$rowCodes["cpt_prac_code"];
			}
		$arrCptCodes[] = array($row["cpt_category"],$arrSubOptions);
		}		
	}

	$stringAllProcedures = substr($stringAllProcedures,0,-1);
	
//------------------------	END GETTING DATA FOR MENUS TO CATEGORY OF Procedures	------------------------//

//------------------------ Start Ins. case Qry -----------------------//

$getins_data="SELECT insct.case_name,insc.ins_caseid, insc.ins_case_type 
				FROM insurance_case_types insct 
				JOIN insurance_case insc ON (insc.ins_case_type=insct.case_id AND insc.case_status ='Open') 
				JOIN insurance_data insd ON (insd.ins_caseid=insc.ins_caseid AND insd.provider >0) 
				JOIN insurance_companies inscomp ON (inscomp.id=insd.provider AND inscomp.in_house_code !='n/a') 
				WHERE insc.patient_id='$patient_id'
				GROUP BY insc.ins_caseid 
				ORDER BY insc.ins_case_type";
$getins_data_qry = imw_query($getins_data);
while($getins_data_row = imw_fetch_array($getins_data_qry)){
	$insCasesArr = $getins_data_row['case_name'].'-'.$getins_data_row['ins_caseid'];
	$ins_case_arr[$getins_data_row['ins_caseid']]=$insCasesArr;
}
//------------------ End Ins. case Qry -----------------------//

// COMMON FUNCTIONS
$arrManufac=array();
$manu_detail_qry = "select id, manufacturer_name from in_manufacturer_details where frames_chk='1' and del_status='0'";
$manu_detail_res = imw_query($manu_detail_qry);
$manu_detail_nums = imw_num_rows($manu_detail_res);
if($manu_detail_nums > 0)
{	
	while($manu_detail_row = imw_fetch_array($manu_detail_res)) 
	{
		$arrManufac[$manu_detail_row['id']] = $manu_detail_row['manufacturer_name'];
	}
}

$order_due_date='';
if($orderId>0)
{
			$order_id=$orderId;
			
			$comm_qry=imw_query("select operator_id,comment, due_date from in_order where id ='$order_id' and patient_id='$patient_id' and del_status='0'");
			$comm_row = imw_fetch_array($comm_qry);
			
			if($comm_row['due_date']!='' && $comm_row['due_date']!='0000-00-00'){
				//order due date
				$order_due_date=date("m-d-Y", strtotime($comm_row['due_date']));
			}
	
			$usr_qry=imw_query("select fname,mname,lname from users where id ='".$comm_row['operator_id']."'");
			$usr_row = imw_fetch_array($usr_qry);
			
			$created_usr_name=$usr_row['lname'].', '.$usr_row['fname'];
			
			$sel_qry_val = "select * from in_order_details where order_id ='$order_id' $whereDetIds and patient_id='$patient_id' and module_type_id='1' and del_status='0'";
			$sel_qry = imw_query($sel_qry_val)or die(imw_error().' 101');
			$sel_order=imw_fetch_array($sel_qry);
			$frame_order_detail_id=$sel_order['id'];
						
			$sel_lens_qry=imw_query("select * from in_order_details where order_id ='$order_id' $whereDetIds and patient_id='$patient_id' and module_type_id='2' and del_status='0'")or die(imw_error().' 107');
			$sel_lens_order=imw_fetch_array($sel_lens_qry);
			$lens_order_detail_id=$sel_lens_order['id'];
			$lens_usage_id=$sel_lens_order['contact_usage'];
			$lens_type_id=$sel_lens_order['contact_type'];
			
			$sel_contact_qry=imw_query("select * from in_order_details where order_id ='$order_id' $whereDetIds and patient_id='$patient_id' and module_type_id='3' and del_status='0'")or die(imw_error().' 114');
			
			$sel_contact_order=imw_fetch_array($sel_contact_qry);
			$cl_order_detail_id=$sel_contact_order['id'];
			$contact_lense_usageID=$sel_contact_order['contact_usage'];
			$contact_lense_typeID=$sel_contact_order['contact_type'];

			$lens_item_id=$sel_lens_order['item_id'];
			$contact_item_id=$sel_contact_order['item_id'];
			
			$sel_qry2=imw_query("select id,name from in_item where id in($lens_item_id,$contact_item_id)");
			while($sel_item2=imw_fetch_array($sel_qry2)){
				if($lens_item_id==$sel_item2['id'])
				{
					$lens_name=$sel_item2['name'];
				}
				if($contact_item_id==$sel_item2['id'])
				{
					$contact_name=$sel_item2['name'];
				}
			}
			
			 //LENS PRESCRIPTION
//echo "Select * FROM in_optical_order_form WHERE order_id='".$order_id."' AND det_order_id='$lens_order_detail_id' AND patient_id='".$_SESSION['patient_session_id']."'";
			 
			$lensRs=imw_query("Select * FROM in_optical_order_form WHERE order_id='".$order_id."' AND patient_id='".$patient_id."' AND del_status=0");
			
			$lensResArr=array();
			
			while($lensResData=imw_fetch_array($lensRs))
			{
					$lensResArr[]=$lensResData;
			}
			//echo count($lensResArr);	
			//CONTACT LENS PRESCRIPTION
			$clLensRs=imw_query("Select * FROM in_cl_prescriptions WHERE order_id='".$order_id."' AND patient_id='".$patient_id."' AND del_status=0");
			while($clLensResData=imw_fetch_array($clLensRs))
			{
				$clLensResArr[]=$clLensResData;
			}
			
		}
       

$manuFacOptions='';
foreach($arrManufac as $id => $manufacName)
{
	$sel=($id==$sel_order['manufacturer_id'])? 'selected': '';
	$manufacName=$manufacName;
}

	$frameBrandOpts='';                                    
	$sql = "SELECT frame_source,id FROM in_frame_sources WHERE del_status = 0 ORDER BY frame_source ASC";
	$res = imw_query($sql);
	while($row = imw_fetch_assoc($res)){
	$sel=($row['id']==$sel_order['brand_id'])? 'selected': '';
	$frameBrandName = $row['frame_source'];
}

$rsShape = imw_query("select * from in_frame_styles where del_status<='1' order by style_name");
while($resShape=imw_fetch_array($rsShape)){
$frame_style_name = ucfirst($resShape['style_name']); 
}
 
$rsColor = imw_query("select * from in_frame_color where del_status='0' order by color_name asc");
while($resColor=imw_fetch_array($rsColor))
{ 
$sel=($resColor['id']==$sel_order['color_id'])? 'selected': '';
$frame_color_name = ucfirst($resColor['color_name']); 
}
//show other color name if entered
if(!$frame_color_name && $sel_order['color_other'])$frame_color_name =$sel_order['color_other'];
	
$lab_qry = imw_query("select * from in_lens_lab where del_status='0' order by lab_name asc");	
while($lab_row = imw_fetch_assoc($lab_qry)){
	if($lab_row['id']==$sel_order['lab_id']){ $frame_lab_name = $lab_row['lab_name']; }   
	$lab_name_id[$lab_row['id']]=$lab_row['lab_name'];
}

$rows=$lense_type_name ="";
$lensTypeRs = imw_query("select * from in_lens_type where del_status='0' order by type_name asc");
while($lensTypeRes=imw_fetch_array($lensTypeRs))
{  
$sel=($lensTypeRes['id']==$sel_lens_order['type_id'])? 'selected': '';
$lense_type_name = $lensTypeRes['type_name'];
}


$rows="";
$lensMatRs= imw_query("select * from in_lens_material where del_status='0' order by material_name asc");
while($lensMatRes=imw_fetch_array($lensMatRs))
{ 
$sel=($lensMatRes['id']==$sel_lens_order['material_id'])? 'selected': '';
$lense_material_name = $lensMatRes['material_name'];	
}	

$rows="";
$lensColorRs= imw_query("select * from in_lens_color where del_status='0' order by color_name asc");
while($lensColorRes=imw_fetch_array($lensColorRs))
{ 
$sel=($lensColorRes['id']==$sel_lens_order['color_id'])? 'selected': '';
$lens_color_name = $lensColorRes['color_name'];
}

$lab_qry = imw_query("select * from in_lens_lab where del_status='0' order by lab_name asc");	
while($lab_row = imw_fetch_assoc($lab_qry)){
if($lab_row['id']==$sel_lens_order['lab_id']){ $lens_lab_name = $lab_row['lab_name']; }
}


$manuFacOptions='';
foreach($arrManufac as $id => $manufacName){
$sel=($id==$sel_contact_order['manufacturer_id'])? 'selected': '';
$manufacName = $manufacName;
}

$rows="";
$rows = data("select * from in_contact_cat where del_status='0' order by cat_name asc");
foreach($rows as $r)
{ 
$sel=($r['id']==$sel_contact_order['contact_cat_id'])? 'selected': '';

$contact_cat_name = ucfirst($r['cat_name']);	
}

$rows = data("select * from in_type where del_status='0' order by type_name asc");
foreach($rows as $r)
{ 
$sel=($r['id']==$sel_contact_order['type_id'])? 'selected': '';
$contacts_type_name = ucfirst($r['type_name']); 
}


$qry="";
$qry = imw_query("select * from in_brand where status='0' order by brand_name asc");
while($rows = imw_fetch_array($qry))
{
$sel=($rows['id']==$sel_contact_order['brand_id'])? 'selected': ''; 

$contacts_brand_name = $rows['brand_name']; 
}	

if($sel_contact_order['color_id']){
	$rows = data("select * from in_color where id = '".$sel_contact_order['color_id']."' order by color_name asc");// del_status='0' and 
	foreach($rows as $r)
	{ 
		$sel=($r['id']==$sel_contact_order['color_id'])? 'selected': ''; 
		$contacts_color_name = ucfirst($r['color_name']); 
	}	
}

$rows="";
$rows = data("select * from in_supply where del_status='0' order by supply_name asc");
foreach($rows as $r)
{ 
$sel=($r['id']==$sel_contact_order['supply_id'])? 'selected': ''; 
$contacts_supply_name = ucfirst($r['supply_name']);
}	
$css = '<style>
.text_b_w{
		font-size:12px;
		font-weight:bold;
}
.paddingLeft{
	padding-left:5px;
}
.paddingTop{
	padding-top:5px;
}
.tb_subheading{
	font-size:10px;
	font-weight:bold;
	color:#000000;
	background-color:#f3f3f3;;
}
.tb_subheading_1{
	font-size:10px;
	color:#000000;
	background-color:#f3f3f3;;
}
.tb_heading{
	font-size:10px;
	font-weight:bold;
	color:#000;
	background-color:#CCC;
	padding:3px 0px 3px 0px;
	vertical-align:middle;
}
.tb_headingHeader{
	font-size:10px;
	
	font-weight:bold;
	color:#FFFFFF;
	background:#4684ab;
}
.text_lable{
		font-size:14px;
		
		background-color:#FFFFFF;
		font-weight:bold;
}
.text_value{
		font-size:14px;
		
		font-weight:100;
		background-color:#FFFFFF;
	}
.text_blue{
		font-size:14px;
		
		color:#0000CC;
		font-weight:bold;
	}
.text_green{
		font-size:14px;
		
		color:#006600;
		font-weight:bold;
}
.imgCon{width:325px;height:auto;}
.border{
	border:1px solid #C0C0C0;
}
.bdrbtm{
	border-bottom:1px solid #C0C0C0;
	height:13px;	
	vertical-align:top;
	padding-top:2px;
	padding-left:3px;
}
.bdrtop{
	border-top:1px solid #C0C0C0;
	height:15px;
	vertical-align:top;	
}
.pdl5{
	padding-left:10px;
		
}
.bdrright{
	border-right:1px solid #C0C0C0;
}
.text_size{
	font-size:11px;
}
.lensDetails{border-bottom:1px solid #ccc;}
.lensDetails td{border: 1px solid #ccc;}
.lensDetails td{border-left: 0px;}
.lensDetails td{border-bottom: 0px;}
.cltd{border-top: 1px solid #ccc;}
</style>
';
	
/*$css = '<style type="text/css">	
.table_collapse {width:100%; padding:0px; border-collapse:collapse;	}
.table_cell_padd5 td{ padding:5px; }
.headingbg{font-size:12px;}
.toprow{background:#4684ab; color:#fff; font-size:12px;}
td { vertical-align:top; font-size:12px;}
</style>';*/
					
					$pinfo = imw_query("select * from patient_data where id = '".$patient_id."'");
					$pinforow = imw_fetch_assoc($pinfo);
					$pname = $pinforow['lname'].", ".$pinforow['fname']." ".$pinforow['mname'];
					$pt_address = $pinforow['street'].", ".$pinforow['street2'].", ".$pinforow['city']." - ".$pinforow['postal_code'].", ".$pinforow['state'];
					$pdob= $pinforow['DOB'];
					$dob=date("m-d-Y",strtotime($pdob));
					$pgender = $pinforow['sex'];
					$pzipcode=$pinforow['postal_code'];
					$pcity=$pinforow['city'];
					$pstate=$pinforow['state'];
					$pstreet=$pinforow['street'];
					$ptPhone = "";
					if($pinforow['preferr_contact']==0){
						$ptPhone = $pinforow['phone_home'];
					}
					elseif($pinforow['preferr_contact']==1){
						$ptPhone = $pinforow['phone_biz'];
					}
					elseif($pinforow['preferr_contact']==2){
						$ptPhone = $pinforow['phone_cell'];
					}
					
					$facility=imw_query("select loc_name,loc_logo,fax,tel_num,zip,city,state,address from in_location where id='".$in_locationID."'");
					$getfacilityInfo=imw_fetch_assoc($facility);
					$facility_name=ucfirst($getfacilityInfo['loc_name']);
					$facility_fax=$getfacilityInfo['fax'];
					$facility_ph=$getfacilityInfo['tel_num'];
					$facility_zip=$getfacilityInfo['zip'];
					$facility_city=$getfacilityInfo['city'];
					$facility_state=$getfacilityInfo['state'];
					$facility_logo=$getfacilityInfo['loc_logo'];
					$facility_address=$getfacilityInfo['address'];
					$logo_path=$GLOBALS['WEB_PATH']."/interface/patient_interface/uploaddir/facility_logo/";
					if($facility_logo!=''){
						$imgWidth = (array_key_exists('LOCAL_SERVER', $GLOBALS) && (strtolower($GLOBALS['LOCAL_SERVER']) == 'fairview') || strtolower($GLOBALS['LOCAL_SERVER']) == 'eyecarearkansas') ? 240 : 120;//90 was default change to 120
						$image= show_image_thumb($facility_logo, $imgWidth, 90);
					}
//if($multi_order_ids==''){					
$pdfHTML.='<page backtop="32mm" backbottom="18mm">
<page_header>
<table width="750" cellpadding="0" cellspacing="0">
<tr>
<td width="210" align="left" class="tb_headingHeader"><strong>'.$section.'</strong></td>
<td width="270" align="left" class="tb_headingHeader">Order ID: '.$orderId.' &nbsp; &nbsp; &nbsp; Created By: '.$created_usr_name.'</td>
<td width="270" align="right" class="tb_headingHeader"><strong>Printed By: '.$_SESSION['authProviderName']." &nbsp; " .date("m-d-Y h:i a",time()).'</strong></td>
</tr>
<tr>
<td width="210" align="left"  valign="top" class="text_size" ><strong>'.$facility_name.'</strong><br>'.$facility_address.'<br />'.$facility_city.',&nbsp;'.$facility_state.'&nbsp;'.$facility_zip.'<br> Ph: '.core_phone_format($facility_ph).'<br> Fax: '.core_phone_format($facility_fax).'<br>Order Facility: '.$order_loaction.'<br>Order Date: '.$order_entered_date;
if($order_due_date!='')$pdfHTML.='<br>Order Due Date: '.$order_due_date;
$pdfHTML.='</td>
<td width="270" align="center" valign="top" class="text_size paddingTop">'.$image.'</td>
<td width="270" align="right" valign="top" class="text_size"><strong>'.$pname.' - '.$patient_id.'</strong><br>'.$dob.'&nbsp;<br>'.$pstreet.'&nbsp;<br>'.$pcity.',&nbsp;'.$pstate.'&nbsp;'.$pzipcode.'<br />'.(($ptPhone!="")?"Ph: ".core_phone_format($ptPhone):"").'<br /></td>
</tr>
</table>
</page_header>';				
//}
$lensRs=imw_query("Select * FROM in_optical_order_form WHERE order_id='".$order_id."' AND det_order_id='$lens_order_detail_id' AND patient_id='".$patient_id."' AND del_status=0");
			
	if(imw_num_rows($lensRs)>0)
	{
				
		foreach($lensResArr as $lensRes)
		{
			$rxDate=$lensRes['rx_dos'];
			$rx_date=date("m-d-Y",strtotime($rxDate));
			$physicianName=$lensRes['physician_name'];
			$base_OD=$lensRes['base_od'];
			$base_OS=$lensRes['base_os'];
			$seg_OD=$lensRes['seg_od'];
			$seg_OS=$lensRes['seg_os'];
			
			/******Frame Lense usage and type*******/
			
			$slectUsage=imw_query("SELECT `id`, `opt_val` FROM `in_options` WHERE `opt_type`='1' AND `module_id`='7' AND `del_status`='0' AND id='".$lens_usage_id."' ORDER BY `opt_val` ASC");
			$getUsage=imw_fetch_array($slectUsage);
			$usage=$getUsage['opt_val'];
			
			$slctType=imw_query("SELECT `id`, `opt_val` FROM `in_options` WHERE `opt_type`='2' AND `module_id`='7' AND `del_status`='0' AND id='".$lens_type_id."' ORDER BY `opt_val` ASC");
			$getType=imw_fetch_array($slctType);
			$type=$getType['opt_val'];
			/*************/
						
$loc_qry = imw_query("select * from in_location where id = '".$lensRes['location_id']."' ");								
while($loc_row = imw_fetch_assoc($loc_qry))                                  
{ 
	if($loc_row['loc_name']!="0"){$loc_name = $loc_row['loc_name'];}
} 
		$pdfHTML.='
		 <table  class="border" width="760" cellpadding="0" cellspacing="0">
            <tr>
              <td class="tb_heading" colspan="11" style="width:760px;"><strong>Lens Prescription </strong></td>
            </tr>			
			<tr>
              <td width="90" class="tb_subheading"><strong>Rx</strong></td>
			  <td width="60" class="tb_subheading"><strong>SPH</strong></td>
              <td width="60" class="tb_subheading"><strong>CYL </strong>  </td>              
              <td width="40" class="tb_subheading"><strong>AXIS </strong>  </td>             
              <td width="52" class="tb_subheading"><strong>VA </strong>  </td>   
			  <td width="40" class="tb_subheading"><strong>ADD </strong>  </td>              
              <td width="52" class="tb_subheading"><strong>VA </strong>  </td>            
              <td width="95" class="tb_subheading"><strong>Prism </strong> </td>
			  <td width="40" class="tb_subheading"><strong>OC</strong></td>
			  <td width="40" class="tb_subheading"><strong>Base </strong></td>
			  <td width="40" class="tb_subheading"><strong>SEG </strong></td>
            </tr>
			<tr>
			  <td class="text_size"><strong>OD</strong> </td>
              <td class="text_size">'.removespecialchar($lensRes["sphere_od"]).' </td>
              <td class="text_size">'.removespecialchar($lensRes["cyl_od"]).' </td>              
              <td class="text_size">'.removespecialchar($lensRes["axis_od"]).' </td>            
              <td class="text_size">'.removespecialchar($lensRes["axis_od_va"]).' </td>              
              <td class="text_size">'.removespecialchar($lensRes["add_od"]).'</td>
              <td class="text_size">'.removespecialchar($lensRes["add_od_va"]).'</td>
              <td class="text_size">'.removespecialchar($lensRes["mr_od_p"]).' &nbsp; '.removespecialchar($lensRes["mr_od_prism"]).' / '.removespecialchar($lensRes["mr_od_splash"]).' &nbsp; '.removespecialchar($lensRes["mr_od_sel"]).'</td>
			  <td class="text_size">'.removespecialchar($lensRes["oc_od"]).'</td>
			  <td class="text_size">'.removespecialchar($base_OD).'</td>                                       
              <td class="text_size">'.removespecialchar($seg_OD).'</td> 
            </tr>
			<tr>
			  <td class="text_size"><strong>OS</strong> </td>
              <td class="text_size">'.removespecialchar($lensRes["sphere_os"]).'</td>
              <td class="text_size"> '.removespecialchar($lensRes["cyl_os"]).' </td>              
              <td class="text_size"> '.removespecialchar($lensRes["axis_os"]).' </td>             
              <td class="text_size"> '.removespecialchar($lensRes["axis_os_va"]).' </td>              
              <td class="text_size"> '.removespecialchar($lensRes["add_os"]).'</td>
              <td class="text_size"> '.removespecialchar($lensRes["add_os_va"]).'</td>
              <td class="text_size"> '.removespecialchar($lensRes["mr_os_p"]).' &nbsp; '.removespecialchar($lensRes["mr_os_prism"]).' / '.removespecialchar($lensRes["mr_os_splash"]).' &nbsp; '.removespecialchar($lensRes["mr_os_sel"]).'</td>
			  <td class="text_size">'.removespecialchar($lensRes["oc_os"]).'</td>
			  <td class="text_size"> '.removespecialchar($base_OS).'</td>                                     
              <td class="text_size"> '.removespecialchar($seg_OS).'</td>   
            </tr>
            <tr>			       
              <td class="text_size"><strong>Rx Date </strong>:'.$rx_date.'</td>        
              <td class="text_size" colspan="2"><strong>DPD</strong> : '.removespecialchar(html_entity_decode($lensRes["dist_pd_od"])).' / '.removespecialchar(html_entity_decode($lensRes["dist_pd_os"])).'</td>
              <td class="text_size" colspan="3"><strong>NPD</strong> : '.removespecialchar(html_entity_decode($lensRes["near_pd_od"])).' / '.removespecialchar(html_entity_decode($lensRes["near_pd_os"])).'</td>              
              <td colspan="4" class="text_size"><strong>Eye Physician </strong>: '.ucfirst(($physicianName=="")?$EyephysicianName:$physicianName).'</td>      
			  <td class="text_size">&nbsp;</td>
              <td class="text_size">&nbsp;</td>
              <td class="text_size">&nbsp;</td>
            </tr>
            ';
			
			/*if($lensRes['location_id']!="0")
			{
				$pdfHTML.='<td width="150" colspan="2"><strong>Location</strong> : '.$loc_name.'</td>';
			}
			else
			{
				$pdfHTML.='<td width="" colspan="4"><strong>Ship</strong> : '.$pt_address.'</td>';
			}*/
            $pdfHTML.='
                             
      </table>';
		}
	}
	
	if($getResult['module_type_id']==3)
	{
		
	$pdfHTML .= '<table class="border" width="760" cellpadding="0" cellspacing="0">
            <tr>
              <td class="tb_heading" colspan="7" width="760"><strong>Contact Lens Prescription</strong></td>
            </tr>';
$clLensRs=imw_query("Select * FROM in_cl_prescriptions WHERE order_id='".$order_id."' AND det_order_id='$cl_order_detail_id' AND patient_id='".$patient_id."' AND del_status=0 ");

//colspan if we do have rx detail
$colspan1='0';
$colspan2='4';
	
if(imw_num_rows($clLensRs)>0)
{
	  
foreach($clLensResArr as $clLensRes)
{
	
 	$cl_physicianName= $clLensRes['physician_name'];
	$loc_qry = imw_query("select * from in_location where id = '".$clLensRes['location_id']."' ");

	while($loc_row = imw_fetch_assoc($loc_qry))
	{ 
		if($loc_row['loc_name']!="0"){$cloc_name = $loc_row['loc_name'];}
	}
	$pdfHTML .= '<tr>
	  <td width="100" class="tb_subheading"><strong>Rx</strong></td>
	  <td width="100" class="tb_subheading"><strong>SPH</strong></td>
	  <td width="100" class="tb_subheading"><strong>CYL </strong>  </td>              
	  <td width="100" class="tb_subheading"><strong>AXIS </strong>  </td>   
	  <td width="100" class="tb_subheading"><strong>BC </strong>  </td>              
	  <td width="100" class="tb_subheading"><strong>DIAM </strong> </td>
	  <td width="110" class="tb_subheading"><strong>ADD </strong></td>			                                            
	</tr>
	<tr>
	  <td width="100" class="text_size"><strong>OD</strong> </td>
	  <td width="100" class="text_size">'.$clLensRes["sphere_od"].' </td>            
	  <td width="100" class="text_size">'.$clLensRes["cylinder_od"].' </td>       
	  <td width="100" class="text_size">'.$clLensRes["axis_od"].'</td>
	  <td width="100" class="text_size">'.$clLensRes["base_od"].'</td>
	  <td width="100" class="text_size">'.$clLensRes["diameter_od"].'</td>
	  <td width="110" class="text_size">'.$clLensRes["add_od"].'</td>
	</tr>
	<tr>
	  <td width="100" class="text_size"><strong>OS</strong> </td>
	  <td width="100" class="text_size">'.$clLensRes["sphere_os"].' </td>            
	  <td width="100" class="text_size">'.$clLensRes["cylinder_os"].' </td>       
	  <td width="100" class="text_size">'.$clLensRes["axis_os"].'</td>
	  <td width="100" class="text_size">'.$clLensRes["base_os"].'</td>
	  <td width="100" class="text_size">'.$clLensRes["diameter_os"].'</td>
	  <td width="110" class="text_size">'.$clLensRes["add_os"].'</td>
	</tr>';
  }
	//colspan if we do have rx detail
	$colspan1='4';
	$colspan2='7';
	
}

			
			/******Contact Lense Usage and Type******/
			$slct_contact_usage=imw_query("SELECT `id`, `opt_val` FROM `in_options` WHERE `opt_type`='1' AND `module_id`='0' AND `del_status`='0' AND id='".$contact_lense_usageID."' ORDER BY `opt_val` ASC");
			$get_contact_usage=imw_fetch_array($slct_contact_usage);
			$contact_usage=$get_contact_usage['opt_val'];

			$slct_contact_type=imw_query("SELECT `id`, `opt_val` FROM `in_options` WHERE `opt_type`='2' AND `module_id`='0' AND `del_status`='0' AND id='".$contact_lense_typeID."' ORDER BY `opt_val` ASC");
			$get_contact_type=imw_fetch_array($slct_contact_type);
			$contact_type=$get_contact_type['opt_val'];
			/*********/

			$pdfHTML.='<tr>
              <td width="100" class="text_size"><strong>Usage :</strong>'.$contact_usage.' </td>
              <td width="100" class="text_size"><strong>Type :</strong> &nbsp;&nbsp;'.$contact_type.' </td>  
              <td width="100" class="text_size"><strong>Color :</strong> &nbsp;&nbsp;'.$contacts_color_name.' </td>            
              <td width="510" class="text_size" colspan="'.$colspan1.'">';
			if($cl_physicianName)$pdfHTML.='<strong>Eye Physician :</strong> &nbsp;&nbsp; '.$cl_physicianName;
			$pdfHTML.='</td>       
              
            </tr>
            
            <tr>';
			if($cloc_name)
			{
				$pdfHTML.='<td width="760" colspan="'.$colspan2.'" class="text_size"><strong>Location</strong> : '.$cloc_name.'</td>';
			}
			else
			{
				$pdfHTML.='<td width="760" colspan="'.$colspan2.'" class="text_size"><strong>Ship</strong> : '.$pt_address.'</td>';
			}
              $pdfHTML.='             
            </tr>      
            
			
                            
        </table>';
	}
$sel_qry_check = imw_query("select * from in_order_details where order_id ='$order_id' $whereDetIds and patient_id='$patient_id' and module_type_id='1' and del_status='0'")or die(imw_error().' 583');
	
	if(imw_num_rows($sel_qry_check)>0)
	{

		$pdfHTML.='<table class="border" width="760" cellpadding="0" cellspacing="0">
		  <tr>
		  	  <td class="tb_heading" colspan="16" width="760"><strong>Frame</strong></td>
		  </tr>
		  <tr>
			  <td width="60px" class="tb_subheading"><strong>UPC</strong></td>
			  <td width="100px" class="tb_subheading"><strong>Manufacturer</strong></td>
			  <td width="25px" class="tb_subheading"><strong>A</strong></td>
			  <td width="25px" class="tb_subheading"><strong>B</strong></td>
			  <td width="60px" class="tb_subheading"><strong>Temple</strong></td>             
			  <td width="40px" class="tb_subheading"><strong>DBL</strong></td>             			  
			  <td width="40px" class="tb_subheading"><strong>ED</strong></td>
			  <td width="40px" class="tb_subheading"><strong>Bridge</strong></td>             
			  <td width="40px" class="tb_subheading"><strong>FPD</strong></td>             
			  <td width="50px" class="tb_subheading"><strong>Type</strong></td>
			  <td width="50px" class="tb_subheading"><strong>Brand</strong></td>
			  <td width="50px" class="tb_subheading"><strong>Style</strong></td>			  
			  <td width="50px" class="tb_subheading"><strong>Shape</strong></td>		  
			  <td width="50px" class="tb_subheading"><strong>Color</strong></td>
			  <td width="30px" class="tb_subheading"><strong>Qty</strong></td>	
			  <td width="50px" class="tb_subheading"><strong>Status</strong></td>				  		  			  
		</tr>';

$sel_qryy = "select * from in_order_details where order_id ='$order_id' and patient_id='$patient_id' $whereDetIds and module_type_id='1' and del_status='0'";

			$sel_qry = imw_query($sel_qryy)or die(imw_error().' 610');
			while($sel_order_row=imw_fetch_array($sel_qry))
			{
				$frame_order_status=$sel_order_row['order_status'];
				if($frame_order_status==''){
					$order_status='Pending';
				}
				else{
					$order_status=$sel_order_row['order_status'];
				}
				$frame_qty=$sel_order_row['qty'];

$frameBrandOpts='';                                    
$frameBrandName='';
$sql2 = "SELECT frame_source,id FROM in_frame_sources WHERE id = '".$sel_order_row['brand_id']."' and del_status = 0";
$res2 = imw_query($sql2);
while($row2 = imw_fetch_assoc($res2))
{
	$frameBrandName = $row2['frame_source'];
}

$frame_style_name='';
$rsShape3 = imw_query("select * from in_frame_styles where id = '".$sel_order_row['style_id']."' and del_status<='1' order by style_name");
while($resShape3=imw_fetch_array($rsShape3)){
	$frame_style_name = ucfirst($resShape3['style_name']);
 }
 $frame_style_name = ($frame_style_name=="")?$sel_order_row['style_other']:$frame_style_name;

$frame_shape_name='';
$rsShapes3 = imw_query("select * from in_frame_shapes where id = '".$sel_order_row['shape_id']."' and del_status<='1' order by shape_name");
while($resShapes3=imw_fetch_array($rsShapes3)){
	$frame_shape_name = ucfirst($resShapes3['shape_name']);
 }
 $frame_shape_name = ($frame_shape_name=="")?$sel_order_row['shape_other']:$frame_shape_name;
 
$frame_color_names=''; 
$rsColor4 = imw_query("select * from in_frame_color where id = '".$sel_order_row['color_id']."' and del_status='0' order by color_name asc");
while($resColor4=imw_fetch_array($rsColor4))
{ 
	$frame_color_names = ucfirst($resColor4['color_name']);
}
//show other color name if entered
if(!$frame_color_names && $sel_order_row['color_other'])$frame_color_names =$sel_order_row['color_other'];

$frame_type_names='';
$rsType5 = imw_query("select type_name from in_frame_types where id = '".$sel_order_row['type_id']."' and del_status='0'");
while($resType5=imw_fetch_array($rsType5))
{ 
	$frame_type_names = ucfirst($resType5['type_name']);
}
				
$manu_detail_qry2 = "select manufacturer_name from in_manufacturer_details where id = '".$sel_order_row['manufacturer_id']."' and frames_chk='1' and del_status='0'";

$manu_detail_res2 = imw_query($manu_detail_qry2);
$manu_detail_nums2 = imw_num_rows($manu_detail_res2);
if($manu_detail_nums2 > 0)
{	
	while($manu_detail_row2 = imw_fetch_array($manu_detail_res2))
	{
		$manufacturer_name = $manu_detail_row2['manufacturer_name'];
	}
}


$pofdetailqry = imw_query("select * from in_frame_pof where order_detail_id = '".$sel_order_row['id']."'");
if(imw_num_rows($pofdetailqry)>0)
{	
	$pofROW = imw_fetch_assoc($pofdetailqry);
	$frameBrandName=$pofROW['brand'];
	$manufacturer_name=$pofROW['manufacturer'];
	$frame_style_name=$pofROW['style'];
	$frame_shape_name=$pofROW['shape'];
}

$ordered = "";
$received = "";
$notified = "";
$dispensed = "";
if($sel_order_row['ordered']!="" && $sel_order_row['ordered']!="0000-00-00")
{
	$ordered = getDateFormat($sel_order_row['ordered']);
}
if($sel_order_row['received']!="" && $sel_order_row['received']!="0000-00-00")
{
	$received = getDateFormat($sel_order_row['received']);
}
if($sel_order_row['notified']!="" && $sel_order_row['notified']!="0000-00-00")
{
	$notified = getDateFormat($sel_order_row['notified']);
}
if($sel_order_row['dispensed']!="" && $sel_order_row['dispensed']!="0000-00-00")
{
	$dispensed = getDateFormat($sel_order_row['dispensed']);
}

	$lens_lab_name="";
	$lens_lab_id=$sel_order_row['id']+1;
	$lab_qry = imw_query("select * from in_order_details where id='".$lens_lab_id."' and order_id ='$order_id' and patient_id='$patient_id'");	
	$lab_row = imw_fetch_assoc($lab_qry);
	$lens_lab_name = $lab_name_id[$lab_row['lab_id']];
	
	$vw_status="Not Submitted";
	if($sel_order_row['vw_order_id']!=""){
		$vw_status="Submitted";
	}
			  $pdfHTML.='<tr>
              <td width="60px" class="text_size">'.$sel_order_row['upc_code'].'</td>
			  <td width="100px" class="text_size">'.$manufacturer_name.'</td>
              <td width="25px" class="text_size">'.$sel_order_row['a'].'</td>
              <td width="25px" class="text_size">'.$sel_order['b'].'</td>
              <td width="60px" class="text_size">'.$sel_order_row["temple"].'</td>              
              <td width="40px" class="text_size">'.$sel_order_row["dbl"].'</td>              			  			  
              <td width="40px" class="text_size">'.$sel_order_row['ed'].'</td>              
              <td width="40px" class="text_size">'.$sel_order_row["bridge"].'</td>              
              <td width="40px" class="text_size">'.$sel_order_row["fpd"].'</td>                 
              <td width="50px" class="text_size">'.$frame_type_names.'</td>              
              <td width="50px" class="text_size">'.$frameBrandName.'</td>
              <td width="50px" class="text_size">'.$frame_style_name.'</td>
              <td width="50px" class="text_size">'.$frame_shape_name.'</td>
              <td width="50px" class="text_size">'.$frame_color_names.'</td>			  
			  <td width="30px" rowspan=2 class="text_size">'.$frame_qty.'</td>
              <td width="50px" rowspan=2 class="text_size">'.$order_status.'</td>			  			  
            </tr>
			<tr>			       
              <td class="text_size" colspan="4"><strong>Lab </strong>: '.$lens_lab_name.'</td>        
              <td class="text_size" colspan="4"><strong>VisionWeb</strong> : '.$vw_status.'</td>
			  <td class="text_size" colspan="8">&nbsp;</td>
            </tr>
			';
			
			}
			
  	$pdfHTML.='</table>';
	}
	
	$sel_qry_check_lense = imw_query("select * from in_order_details where order_id ='$order_id' $whereDetIds and patient_id='$patient_id' and module_type_id='2' and del_status='0'")or die(imw_error().' 809');
	if(imw_num_rows($sel_qry_check_lense)>0)
	{

	$pdfHTML.='<table class="border" width="760" cellpadding="0" cellspacing="0">
        <tr>
          <td class="tb_heading" colspan="7" width="760"><strong>Lenses</strong></td>
        </tr>
		<tr>
		  <td width="90px" class="tb_subheading"><strong>UPC/Name</strong></td>
          <td width="90px" class="tb_subheading"><strong>Seg Type</strong></td>
		  <td width="113px" class="tb_subheading"><strong>Design</strong></td>
          <td width="113px" class="tb_subheading"><strong>Material</strong></td>
		  <td width="220px" class="tb_subheading"><strong>Treatment</strong></td>
		  <td width="44px" class="tb_subheading"><strong>Qty</strong></td>
          <td width="90px" class="tb_subheading"><strong>Status</strong></td>
        </tr>';
		
		/*
		<td width="70" class="tb_subheading"><strong>Transition</strong></td>
		<td width="70" class="tb_subheading"><strong>Tint</strong></td>
		<td width="70" class="tb_subheading"><strong>Polarized</strong></td>
		<td width="70" class="tb_subheading"><strong>Edge</strong></td>
		*/
		
		$lense_type_name ='';
$sel_lens_qryy=imw_query("select * from in_order_details where order_id ='$order_id' $whereDetIds and patient_id='$patient_id' and module_type_id='2' and del_status='0'")or die(imw_error().' 832');

	while($sel_lens_order=imw_fetch_array($sel_lens_qryy))
	{
		//print "select * from in_order_details where order_id ='$order_id' $whereDetIds and patient_id='$patient_id' and module_type_id='2' and del_status='0'";
		//die;
		
		//$sel_lens_qryy_check=imw_query("select * from in_order_details where order_id ='$order_id' $whereDetIds and patient_id='$patient_id' and module_type_id='2' and del_status='0'")or die(imw_error().' 717');
		/*Order Details*/
		//if(imw_num_rows($sel_lens_qryy_check)>0){
			$transition_name="";
			$progressive_name="";
			$ar_name="";
			$tint_type="";
			$polarized_name="";
			$edge_name="";
			$lens_other="";
			$uv400="";
			$pgx="";
			$field="";
			$ds="";
			$qry="";
			if($sel_lens_order['transition_id']>0)
			{
				$field .= " in_lens_transition.transition_name , ";
				$qry .= "inner join in_lens_transition on in_lens_transition.id = '".$sel_lens_order['transition_id']."'";
				$ds .= " in_lens_transition.del_status = 0 and ";
			}
			
			if (preg_match("/progressive/i", strtolower($type_name))) {
				if($sel_lens_order['progressive_id']>0)
				{
					$field .= " in_lens_progressive.progressive_name , ";
					$qry .= " inner join in_lens_progressive on in_lens_progressive.id = '".$sel_lens_order['progressive_id']."'";
					$ds .= " in_lens_progressive.del_status = 0 and ";	
				}
			}
			
			if($sel_lens_order['a_r_id']>0)
			{
				$arids = explode(";", $sel_lens_order['a_r_id']);
				$arids = implode(",", $arids);
				$field .= " GROUP_CONCAT(in_lens_ar.ar_name SEPARATOR ', ') AS ar_name , ";
				$qry .= " inner join in_lens_ar on in_lens_ar.id IN(".$arids.")";
				$ds .= " in_lens_ar.del_status = 0 and ";	
			}
			
			if($sel_lens_order['polarized_id']>0)
			{
				$field .= " in_lens_polarized.polarized_name , ";
				$qry .= " inner join in_lens_polarized on in_lens_polarized.id = '".$sel_lens_order['polarized_id']."'";
				$ds .= " in_lens_polarized.del_status = 0 and ";
			}
			
			if($sel_lens_order['edge_id']>0)
			{
				$field .= " in_lens_edge.edge_name , ";
				$qry .= " inner join in_lens_edge on in_lens_edge.id = '".$sel_lens_order['edge_id']."'";
				$ds .= " in_lens_edge.del_status = 0 and ";
			}
			
			if($sel_lens_order['tint_id']>0)
			{
				$field .= " in_lens_tint.tint_type , ";
				$qry .= " inner join in_lens_tint on in_lens_tint.id = '".$sel_lens_order['tint_id']."'";
				$ds .= " in_lens_tint.del_status = 0 and ";
			}
			//echo $ds;
			
			$field = substr($field,0,-2);
			$ds = substr($ds,0,-4);
			
			$lens_opt_qry = "select in_order_details.id,$field from in_order_details $qry where in_order_details.id='".$sel_lens_order['id']."' and $ds";
			
			$lens_opt = imw_query($lens_opt_qry);	
			while($lensOptionRow = imw_fetch_assoc($lens_opt))
			{ 
				if($lensOptionRow['transition_name']!=""){ $transition_name = $lensOptionRow['transition_name']; }
				if($lensOptionRow['ar_name']!=""){ $ar_name = $lensOptionRow['ar_name']; }
				if($lensOptionRow['tint_type']!=""){ $tint_type = $lensOptionRow['tint_type']; }
				if($lensOptionRow['progressive_name']!=""){ $progressive_name = $lensOptionRow['progressive_name']; }
				if($lensOptionRow['polarized_name']!=""){ $polarized_name = $lensOptionRow['polarized_name']; }
				if($lensOptionRow['edge_name']!=""){ $edge_name = $lensOptionRow['edge_name']; }
			} 
			if($sel_lens_order['lens_other']!="")
			{
				$lens_other = $sel_lens_order['lens_other'];
			}
				
			if($sel_lens_order['uv400']==1)
			{
				$uv400="UV400;";
			}
			if($sel_lens_order['pgx']==1)
			{
				$pgx="PGX";
			}
		//}
		/*End Order Details*/
		
		$lens_order_status=$sel_lens_order['order_status'];
		if($lens_order_status==''){
			$order_status_lens='Pending';
		}
		else{
			$order_status_lens=$sel_lens_order['order_status'];
		}
		$lens_qty=$sel_lens_order['qty'];
		$lensTypeRs = imw_query("select * from in_lens_type where id = '".$sel_lens_order['type_id']."' and del_status='0' order by type_name asc");
		while($lensTypeRes=imw_fetch_array($lensTypeRs))
		{
			$lense_type_name = $lensTypeRes['type_name'];
		}
		
		/*Lens Design*/
		$lens_design_name = "";
		$lensDesign = imw_query("select design_name from in_lens_design where id = '".$sel_lens_order['design_id']."' and del_status='0'");
		while($lensDesignRes=imw_fetch_array($lensDesign))
		{
			$lens_design_name = $lensDesignRes['design_name'];
		}

$rows=$lense_material_name="";
$lensMatRs= imw_query("select * from in_lens_material where id='".$sel_lens_order['material_id']."' and del_status='0' order by material_name asc");
$lense_material_name = "";
while($lensMatRes=imw_fetch_array($lensMatRs))
{ 
	$lense_material_name = $lensMatRes['material_name'];
}	

$rows=$lens_color_name ="";
$lensColorRs= imw_query("select * from in_lens_color where id='".$sel_lens_order['color_id']."' and del_status='0' order by color_name asc");
while($lensColorRes=imw_fetch_array($lensColorRs))
{ 
	$lens_color_name = $lensColorRes['color_name'];
}

$ordered = "";
$received = "";
$notified = "";
$dispensed = "";
if($sel_order_row['ordered']!="" && $sel_order_row['ordered']!="0000-00-00")
{
	$ordered = getDateFormat($sel_lens_order['ordered']);
}
if($sel_order_row['received']!="" && $sel_order_row['received']!="0000-00-00")
{
	$received = getDateFormat($sel_lens_order['received']);
}
if($sel_order_row['notified']!="" && $sel_order_row['notified']!="0000-00-00")
{
	$notified = getDateFormat($sel_lens_order['notified']);
}
if($sel_order_row['dispensed']!="" && $sel_order_row['dispensed']!="0000-00-00")
{
	$dispensed = getDateFormat($sel_lens_order['dispensed']);
}
//OD
$OD_vw_code = 0;
$lens_type_name_od="";
//Lens Type name
$LenstypeOD = imw_query("SELECT `id`, `vw_code`,`type_name` FROM `in_lens_type` WHERE `del_status`='0' AND id = ".$sel_lens_order['seg_type_od']."");
while($lensTypenameOD=imw_fetch_array($LenstypeOD))
{ 
	$lens_type_name_od = $lensTypenameOD['type_name'];
	$OD_vw_code = $lensTypenameOD['vw_code'];
}


//Lens Design name
$lens_design_name_od ="";
$design_fetch_OD = imw_query("SELECT `id`,`design_name` FROM `in_lens_design` WHERE `lens_vw_code`='".$OD_vw_code."' AND id = '".$sel_lens_order['design_id_od']."' ORDER BY `design_name` ASC");
while($design_fetch_od=imw_fetch_array($design_fetch_OD))
{ 
	$lens_design_name_od = $design_fetch_od['design_name'];
}

//Lens Matierial name
$material_fetch_OD = $lense_material_name_od ="";
$material_fetch_OD =imw_query("SELECT 
											`lm`.`id`, 
											`lm`.`material_name` 
										FROM 
											`in_lens_design` `ld` 
											LEFT JOIN `in_lens_material_design` `dm` ON(`ld`.`id` = `dm`.`design_id`) 
											LEFT JOIN `in_lens_material` `lm` ON(`dm`.`material_id` = `lm`.`id`) 
										WHERE 
											`ld`.`lens_vw_code`='".$OD_vw_code."' 
											AND `lm`.`del_status` = 0 AND `lm`.`id` = '".$sel_lens_order['material_id_od']."'
										GROUP BY 
											`dm`.`material_id` ORDER BY `lm`.`material_name` ASC");
while($material_fetch_od=imw_fetch_array($material_fetch_OD))
{ 
	$lense_material_name_od = $material_fetch_od['material_name'];
}


//Lens Treatments Name
	$sel_a_r_vals = explode(";", $sel_lens_order['a_r_id_od']);
	$tretment_list = array();
	/*Fetch List of Treatments connected to the selected material>design>seg_type*/
	
		/*List all materials ids*/
			$material_ids = array();
			$material_qry = imw_query("SELECT 
												DISTINCT(`dm`.`material_id`) AS `material_id` 
											FROM 
												`in_lens_design` `ld` 
												INNER JOIN `in_lens_material_design` `dm` ON(
													`ld`.`lens_vw_code` = '".$OS_vw_code."' 
													AND `ld`.`id` = `dm`.`design_id`
												)");
			while($row_t = imw_fetch_object($material_qry)){
				$material_ids[$row_t->material_id] = true;
			}
			imw_free_result($material_qry);
		/*End material list ids*/
	
		/*List Treatment ids*/
			$treatment_ids = array();
			$treatment_qry = imw_query("SELECT 
												DISTINCT(`material_id`) AS `material_id`, 
												`ar_id` 
											FROM 
												`in_lens_ar_material`");
			while($row_t = imw_fetch_object($treatment_qry)){
				if($material_ids[$row_t->material_id]){
					$treatment_ids[$row_t->ar_id] = true;
				}
			}
			imw_free_result($treatment_qry);
			unset($material_ids);
		/*End list Treatment ids*/
	
		if(count($treatment_ids)>0){
			
			/*List all Treatment Values*/
			$treatments_qry = imw_query("SELECT 
												`id`, 
												`ar_name` 
											FROM 
												`in_lens_ar`
												WHERE `del_status`=0");
			while($row_t = imw_fetch_object($treatments_qry)){
				if($treatment_ids[$row_t->id]){
					$tretment_list[$row_t->id] = $row_t->ar_name;
				}
			}
			imw_free_result($treatments_qry);
			asort($tretment_list, SORT_NATURAL | SORT_FLAG_CASE);
			unset($treatment_ids);
			/*End list all Treatment Values*/
		}
	/*End List of Treatments connected to the selected material>design>seg_type*/
	
	$countLoop = 0;
	$lens_treatment_od = '';
	$len = count($sel_a_r_vals);
	foreach($tretment_list as $key=>$value){
		if(in_array($key, $sel_a_r_vals))
		{
			if($countLoop != $len - 1) {
				$end = ",";
			}else{
				$end = ".";
			}
			$lens_treatment_od .= $value.$end;
			$countLoop++;
			
		}
		
	}
	unset($tretment_list, $row_treatment);


/*******************************************************/

//OS
$OS_vw_code = 0;
//Lens Type name
$lens_design_name_os= $lens_type_name_os ="";
$LenstypeOS = imw_query("SELECT `id`, `vw_code`,`type_name` FROM `in_lens_type` WHERE `del_status`='0' AND id = ".$sel_lens_order['seg_type_os']."");
while($lensTypenameOS=imw_fetch_array($LenstypeOS))
{ 
	$lens_type_name_os = $lensTypenameOS['type_name'];
	$OS_vw_code = $lensTypenameOS['vw_code'];
}

//Lens Design name
$design_fetch_OS = imw_query("SELECT `id`,`design_name` FROM `in_lens_design` WHERE `lens_vw_code`='".$OS_vw_code."' AND id = '".$sel_lens_order['design_id_os']."' ORDER BY `design_name` ASC");
while($design_fetch_os=imw_fetch_array($design_fetch_OS))
{ 
	$lens_design_name_os = $design_fetch_os['design_name'];
}

//Lens Matierial name
$lense_material_name_os="";
$material_fetch_OS =imw_query("SELECT 
											`lm`.`id`, 
											`lm`.`material_name` 
										FROM 
											`in_lens_design` `ld` 
											LEFT JOIN `in_lens_material_design` `dm` ON(`ld`.`id` = `dm`.`design_id`) 
											LEFT JOIN `in_lens_material` `lm` ON(`dm`.`material_id` = `lm`.`id`) 
										WHERE 
											`ld`.`lens_vw_code`='".$OS_vw_code."' 
											AND `lm`.`del_status` = 0 AND `lm`.`id` = '".$sel_lens_order['material_id_os']."'
										GROUP BY 
											`dm`.`material_id` ORDER BY `lm`.`material_name` ASC");
while($material_fetch_os=imw_fetch_array($material_fetch_OS))
{ 
	$lense_material_name_os = $material_fetch_os['material_name'];
}

//Lens Treatments Name
	$sel_a_r_vals = explode(";", $sel_lens_order['a_r_id_os']);
	$tretment_list = array();
	/*Fetch List of Treatments connected to the selected material>design>seg_type*/
	
		/*List all materials ids*/
			$material_ids = array();
			$material_qry = imw_query("SELECT 
												DISTINCT(`dm`.`material_id`) AS `material_id` 
											FROM 
												`in_lens_design` `ld` 
												INNER JOIN `in_lens_material_design` `dm` ON(
													`ld`.`lens_vw_code` = '".$OS_vw_code."' 
													AND `ld`.`id` = `dm`.`design_id`
												)");
			while($row_t = imw_fetch_object($material_qry)){
				$material_ids[$row_t->material_id] = true;
			}
			imw_free_result($material_qry);
		/*End material list ids*/
	
		/*List Treatment ids*/
			$treatment_ids = array();
			$treatment_qry = imw_query("SELECT 
												DISTINCT(`material_id`) AS `material_id`, 
												`ar_id` 
											FROM 
												`in_lens_ar_material`");
			while($row_t = imw_fetch_object($treatment_qry)){
				if($material_ids[$row_t->material_id]){
					$treatment_ids[$row_t->ar_id] = true;
				}
			}
			imw_free_result($treatment_qry);
			unset($material_ids);
		/*End list Treatment ids*/
	
		if(count($treatment_ids)>0){
			
			/*List all Treatment Values*/
			$treatments_qry = imw_query("SELECT 
												`id`, 
												`ar_name` 
											FROM 
												`in_lens_ar`
												WHERE `del_status`=0");
			while($row_t = imw_fetch_object($treatments_qry)){
				if($treatment_ids[$row_t->id]){
					$tretment_list[$row_t->id] = $row_t->ar_name;
				}
			}
			imw_free_result($treatments_qry);
			asort($tretment_list, SORT_NATURAL | SORT_FLAG_CASE);
			unset($treatment_ids);
			/*End list all Treatment Values*/
		}
	/*End List of Treatments connected to the selected material>design>seg_type*/
	$countLoop = 0;
	$lens_treatment_os = '';
	$len = count($sel_a_r_vals);
	foreach($tretment_list as $key=>$value){
		if(in_array($key, $sel_a_r_vals))
		{
			if($countLoop != $len - 1) {
				$end = ",";
			}else{
				$end = ".";
			}
			$lens_treatment_os .= $value.$end;
			$countLoop++;
			
		}
		
	}
	//unset($tretment_list, $row_treatment);

		if($sel_lens_order['design_id_od']){
        $pdfHTML.='
		<tr>
		 <td width="90px" class="text_size"><b style="color: #00f;">OD</b> - '.$sel_lens_order['upc_code'].'<br>'.$sel_lens_order['item_name'].'</td>
          <td width="90px" class="text_size">'.$lens_type_name_od.'</td>
		  <td width="113px" class="text_size">'.$lens_design_name_od.'</td>
          <td width="113px" class="text_size">'.$lense_material_name_od.'</td>
          <td width="220px" class="text_size">'.$lens_treatment_od.'</td>
		  <td width="44px" class="text_size">'.$lens_qty.'</td>
          <td width="90px" class="text_size">'.$order_status_lens.'</td>
        </tr>';
		}
		if($sel_lens_order['design_id_os']){
        $pdfHTML.='<tr>
		 <td width="90px" class="text_size"><b style="color: #008000;">OS</b> - '.$sel_lens_order['upc_code'].'<br>'.$sel_lens_order['item_name'].'</td>
          <td width="90px" class="text_size">'.$lens_type_name_os.'</td>
		  <td width="113px" class="text_size">'.$lens_design_name_os.'</td>
          <td width="113px" class="text_size">'.$lense_material_name_os.'</td>
          <td width="220px" class="text_size">'.$lens_treatment_os.'</td>
		  <td width="44px" class="text_size">'.$lens_qty.'</td>
          <td width="90px" class="text_size">'.$order_status_lens.'</td>
        </tr>';
		}
		/*
		<td width="70" class="text_size">'.$transition_name.'</td>
		<td width="70" class="text_size">'.$tint_type.'</td>
		<td width="70" class="text_size">'.$polarized_name.'</td>
		<td width="70" class="text_size">'.$edge_name.'</td>
		*/
			}
$pdfHTML.='</table><br />';

}


$sel_contact_qry_check=imw_query("select * from in_order_details where order_id ='$order_id' $whereDetIds and patient_id='$patient_id' and module_type_id='3' and del_status='0'")or die(imw_error().' 903');
		
	if(imw_num_rows($sel_contact_qry_check)>0)
	{
			

		$pdfHTML.='<br /><table class="border" width="760" cellpadding="0" cellspacing="0">
        <tr>
          <td class="tb_heading" colspan="9" width="760"><strong>Contacts</strong></td>
        </tr>            
		<tr>
          <td width="87" class="tb_subheading"><strong>UPC</strong></td>
		  <td width="87" class="tb_subheading"><strong>Manufacturer</strong></td>
          <td width="87" class="tb_subheading"><strong>Lens Material</strong></td>
          <td width="100" class="tb_subheading"><strong>Brand</strong></td>
          <td width="97" class="tb_subheading"><strong>Name</strong></td>              
          <td width="87" class="tb_subheading"><strong>Supply</strong></td>
		  <td width="63" class="tb_subheading"><strong>Qty</strong></td>  
          <td width="87" class="tb_subheading"><strong>Status</strong></td>                  
        </tr>';



		$sel_contact_qryy=imw_query("select * from in_order_details where order_id ='$order_id' $whereDetIds and patient_id='$patient_id' and module_type_id='3' and del_status='0'")or die(imw_error().' 927');
		while($sel_contact_order=imw_fetch_array($sel_contact_qryy))
		{	
            $contact_comment = $sel_contact_order['item_comment'];
			if($sel_contact_order['order_status']==''){
				$contact_lense_status='Pending';
			}
			else{
				$contact_lense_status=$sel_contact_order['order_status'];
			}
			$contact_lense_Qty=($sel_contact_order['qty'])+($sel_contact_order['qty_right']);

$rows=$contacts_type_name="";$material_name=array();
$rows = data("select * from in_type where id IN( ".$sel_contact_order['contact_cat_id'].") and del_status='0' order by type_name asc");
foreach($rows as $r)
{ 
$sel=($r['id']==$sel_contact_order['contact_cat_id'])? 'selected': '';
$material_name[] = ucfirst($r['type_name']);	
}
$material_name=implode(', ',$material_name);
$lens_type_name=array();
$rows = data("select * from in_options where id IN (".$sel_contact_order['contact_type'].") and del_status='0' order by opt_val asc");
foreach($rows as $r)
{ 
$sel=($r['id']==$sel_contact_order['contact_type'])? 'selected': '';
$lens_type_name[] = ucfirst($r['opt_val']); 
}
$lens_type_name=implode(', ',$lens_type_name);


$qry=$contacts_color_name=$contacts_brand_name="";
$qry = imw_query("select * from in_brand where id = '".$sel_contact_order['brand_id']."' and status='0' order by brand_name asc");
while($rows = imw_fetch_array($qry))
{
$sel=($rows['id']==$sel_contact_order['brand_id'])? 'selected': ''; 

$contacts_brand_name = $rows['brand_name']; 
}	




$rows = data("select * from in_color where id = '".$sel_contact_order['color_id']."' and del_status='0' order by color_name asc");
foreach($rows as $r)
{ 
$sel=($r['id']==$sel_contact_order['color_id'])? 'selected': '';
$contacts_color_name = ucfirst($r['color_name']); 
}	

$rows=$contacts_supply_name=$manufacturer_name="";
$rows = data("select * from in_supply where id = '".$sel_contact_order['supply_id']."' and del_status='0' order by supply_name asc");
foreach($rows as $r)
{ 
$sel=($r['id']==$sel_contact_order['supply_id'])? 'selected': '';
$contacts_supply_name = ucfirst($r['supply_name']);
}	

$manu_detail_qry2 = "select manufacturer_name from in_manufacturer_details where id = '".$sel_contact_order['manufacturer_id']."' and cont_lenses_chk='1' and del_status='0'";

$manu_detail_res2 = imw_query($manu_detail_qry2);
$manu_detail_nums2 = imw_num_rows($manu_detail_res2);
if($manu_detail_nums2 > 0)
{	
	while($manu_detail_row2 = imw_fetch_array($manu_detail_res2))
	{
		$manufacturer_name = $manu_detail_row2['manufacturer_name'];
	}
}
//OS
$manu_detail_qry2_os = "select manufacturer_name from in_manufacturer_details where id = '".$sel_contact_order['manufacturer_id_os']."' and cont_lenses_chk='1' and del_status='0'";

$manu_detail_res2_os = imw_query($manu_detail_qry2_os);
$manu_detail_nums2_os = imw_num_rows($manu_detail_res2_os);
if($manu_detail_nums2_os > 0)
{	
	while($manu_detail_row2_os = imw_fetch_array($manu_detail_res2_os))
	{
		$manufacturer_name_os = $manu_detail_row2_os['manufacturer_name'];
	}
}

$brand_name="";
$qry="";
$qry = imw_query("select * from `in_contact_brand` where del_status='0' and id = '".$sel_contact_order['brand_id']."' order by brand_name asc");
while($rows = imw_fetch_array($qry))
{
	$brand_name=$rows['brand_name'];
}
//OS
$qry_os = imw_query("select * from `in_contact_brand` where del_status='0' and id = '".$sel_contact_order['brand_id_os']."' order by brand_name asc");
while($rows_os = imw_fetch_array($qry_os))
{
	$brand_name_os=$rows_os['brand_name'];
}

$ordered = "";
$received = "";
$notified = "";
$dispensed = "";
if($sel_order_row['ordered']!="" && $sel_order_row['ordered']!="0000-00-00")
{
	$ordered = getDateFormat($sel_contact_order['ordered']);
}
if($sel_order_row['received']!="" && $sel_order_row['received']!="0000-00-00")
{
	$received = getDateFormat($sel_contact_order['received']);
}
if($sel_order_row['notified']!="" && $sel_order_row['notified']!="0000-00-00")
{
	$notified = getDateFormat($sel_contact_order['notified']);
}
if($sel_order_row['dispensed']!="" && $sel_order_row['dispensed']!="0000-00-00")
{
	$dispensed = getDateFormat($sel_contact_order['dispensed']);
}

		$pdfHTML.=
			'<tr>
				  <td width="77" class="text_size cltd"><strong style="color:#00f">OD</strong> '.$sel_contact_order['upc_code'].'</td>
				  <td width="77" class="text_size cltd">'.$manufacturer_name.'</td>
				  <td width="77" class="text_size cltd">'.$material_name.'</td>
				  <td width="100" class="text_size cltd">'.$brand_name.'</td>              
				  <td width="87" class="text_size cltd">'.$sel_contact_order['item_name'].'</td>              
				  <td width="77" class="text_size cltd">'.$contacts_supply_name.'</td>
				  <td width="53" class="text_size cltd">'.$sel_contact_order['qty_right'].'</td>
				  <td width="77" class="text_size cltd">'.$contact_lense_status.'</td>				  				  
			</tr>
			
			<tr>
				  <td width="77" class="text_size cltd"><strong style="color:#093">OS</strong> '.$sel_contact_order['upc_code_os'].'</td>
				  <td width="77" class="text_size cltd">'.$manufacturer_name_os.'</td>
				  <td width="77" class="text_size cltd">'.$material_name.'</td>
				  <td width="100" class="text_size cltd">'.$brand_name_os.'</td>              
				  <td width="87" class="text_size cltd">'.$sel_contact_order['item_name_os'].'</td>              
				  <td width="77" class="text_size cltd">'.$contacts_supply_name.'</td>
				  <td width="53" class="text_size cltd">'.$sel_contact_order['qty'].'</td>
				  <td width="77" class="text_size cltd">'.$contact_lense_status.'</td>
			</tr>
			<tr>
				 <td class="tb_heading" colspan="8"><strong>Contact Lens Details</strong></td>
			</tr>
			<tr><td colspan="8" style="padding:0px;"><table cellspacing="0" class="lensDetails">
						<tr>
							<td width="25" class="tb_subheading"></td>
							<td width="80" class="tb_subheading" colspan="2" style="text-align:center;">Sphere</td>
							<td width="80" class="tb_subheading" colspan="2" style="text-align:center;">Cylinder</td>
							<td width="40" class="tb_subheading" style="text-align:center;">Axis</td>
							<td width="60" class="tb_subheading" style="text-align:center;">Base Curve</td>
							<td width="60" class="tb_subheading" style="text-align:center;">Diameter</td>
							<td width="60" class="tb_subheading" style="text-align:center;">Type</td>
						</tr>
						<tr>
							<td class="tb_subheading_1"></td>
							<td class="tb_subheading_1" style="text-align:center;">Min</td>
							<td class="tb_subheading_1" style="text-align:center;">Max</td>
							<td class="tb_subheading_1" style="text-align:center;">Min</td>
							<td class="tb_subheading_1" style="text-align:center;">Max</td>
							<td class="tb_subheading_1"></td>
							<td class="tb_subheading_1"></td>
							<td class="tb_subheading_1"></td>
							<td class="tb_subheading_1"></td>
						</tr>
						<tr>
							<td class="text_size"><strong style="color:#00f">OD</strong></td>
							<td class="text_size" style="text-align:center;">'.$sel_contact_order['contact_sphere_min_od'].'</td>
							<td class="text_size" style="text-align:center;">'.$sel_contact_order['contact_sphere_max_od'].'</td>
							<td class="text_size" style="text-align:center;">'.$sel_contact_order['contact_cylinder_min_od'].'</td>
							<td class="text_size" style="text-align:center;">'.$sel_contact_order['contact_cylinder_max_od'].'</td>
							<td class="text_size" style="text-align:center;">'.$sel_contact_order['contact_axis_min_od'].'</td>
							<td class="text_size" style="text-align:center;">'.$sel_contact_order['contact_bc_od'].'</td>
							<td class="text_size" style="text-align:center;">'.$sel_contact_order['contact_diameter_od'].'</td>
							<td class="text_size" style="text-align:center;">'.$lens_type_name.'</td>
						</tr>
						<tr>
							<td class="text_size"><strong style="color:#093">OS</strong></td>
							<td class="text_size" style="text-align:center;">'.$sel_contact_order['contact_sphere_min_os'].'</td>
							<td class="text_size" style="text-align:center;">'.$sel_contact_order['contact_sphere_max_os'].'</td>
							<td class="text_size" style="text-align:center;">'.$sel_contact_order['contact_cylinder_min_os'].'</td>
							<td class="text_size" style="text-align:center;">'.$sel_contact_order['contact_cylinder_max_os'].'</td>
							<td class="text_size" style="text-align:center;">'.$sel_contact_order['contact_axis_min_os'].'</td>
							<td class="text_size" style="text-align:center;">'.$sel_contact_order['contact_bc_os'].'</td>
							<td class="text_size" style="text-align:center;">'.$sel_contact_order['contact_diameter_os'].'</td>
							<td class="text_size" style="text-align:center;">'.$lens_type_name.'</td>
						</tr>
					</table><br />
				 </td>
			</tr>
			';
		}
		
		//Contact Order Comments
		if($contact_comment !="")
		{			
			$pdfHTML.='<tr><td colspan="8" style="padding-top:10px;"><table class="border" width="760" cellpadding="0" cellspacing="0">
			<tr>
			  <td class="tb_heading"><strong>Comment</strong></td>
			</tr>';

			$pdfHTML.=
				'<tr>
					  <td width="760" class="text_size">'.$contact_comment.'</td>
				</tr>';
			$pdfHTML.= '</table></td></tr>';
		}
  		$pdfHTML.= '</table><br />';  
	}
	
	
	$sel_supplies_qry_check=imw_query("select * from in_order_details where order_id ='$order_id' $whereDetIds and patient_id='$patient_id' and module_type_id='5' and del_status='0'")or die(imw_error().' 1038');
		
	if(imw_num_rows($sel_supplies_qry_check)>0)
	{
		$pdfHTML.='<table class="border" width="760" cellpadding="0" cellspacing="0">
        <tr>
          <td class="tb_heading" colspan="12" width="760"><strong>Supplies</strong></td>
        </tr>            
		<tr>
          <td width="87" class="tb_subheading"><strong>UPC</strong></td>
		  <td width="87" class="tb_subheading"><strong>Manufacturer</strong></td>
          <td width="87" class="tb_subheading"><strong>Size</strong></td>
          <td width="87" class="tb_subheading"><strong>Measurment</strong></td>
          <td width="87" class="tb_subheading"><strong>Size</strong></td>
          <td width="87" class="tb_subheading"><strong>Other</strong></td>              
          <td width="87" class="tb_subheading"><strong>Description</strong></td>
          <td width="87" class="tb_subheading"><strong>Status</strong></td>
          <!--<td width="58" class="tb_subheading"><strong>Received</strong></td>
          <td width="58" class="tb_subheading"><strong>Notified</strong></td>
          <td width="58" class="tb_subheading"><strong>Dispensed</strong></td>
          <td width="58" class="tb_subheading"><strong>Notes</strong></td>-->
        </tr>';

		while($supplies_qry_row = imw_fetch_array($sel_supplies_qry_check))
		{
			$sel_supply_qryy=imw_query("select itm.num_size, ss.size_name, sm.measurment_name, md.manufacturer_name, itm.other, itm.type_desc from in_item as itm left join in_manufacturer_details as md on md.id=itm.manufacturer_id left join in_supplies_measurment as sm on sm.id=itm.measurment left join in_supplies_size as ss on ss.id=itm.char_size where itm.id='".$supplies_qry_row['item_id']."' and itm.module_type_id='5'");
			$sel_supply_order=imw_fetch_array($sel_supply_qryy);


			if($supplies_qry_row['order_status']==''){
				$supply_status='Pending';
			}
			else{
				$supply_status=$supplies_qry_row['order_status'];
			}
			$ordered = "";
			$received = "";
			$notified = "";
			$dispensed = "";
			if($supplies_qry_row['ordered']!="" && $supplies_qry_row['ordered']!="0000-00-00")
			{
				$ordered = getDateFormat($supplies_qry_row['ordered']);
			}
			if($supplies_qry_row['received']!="" && $supplies_qry_row['received']!="0000-00-00")
			{
				$received = getDateFormat($supplies_qry_row['received']);
			}
			if($supplies_qry_row['notified']!="" && $supplies_qry_row['notified']!="0000-00-00")
			{
				$notified = getDateFormat($supplies_qry_row['notified']);
			}
			if($supplies_qry_row['dispensed']!="" && $supplies_qry_row['dispensed']!="0000-00-00")
			{
				$dispensed = getDateFormat($supplies_qry_row['dispensed']);
			}

		$pdfHTML.=
			'<tr>
				  <td width="87" class="text_size">'.$supplies_qry_row['upc_code'].'</td>
				  <td width="87" class="text_size">'.$sel_supply_order['manufacturer_name'].'</td>
				  <td width="87" class="text_size">'.$sel_supply_order['size_name'].'</td>
				  <td width="87" class="text_size">'.$sel_supply_order['measurment_name'].'</td>              
				  <td width="87" class="text_size">'.$sel_supply_order['num_size'].'</td>              
				  <td width="87" class="text_size">'.$sel_supply_order['other'].'</td>              
				  <td width="87" class="text_size">'.$sel_supply_order['type_desc'].'</td>
				  <td width="87" class="text_size">'.$supply_status.'</td>
				  <!--<td width="58" class="text_size">'.$received.'</td>
				  <td width="58" class="text_size">'.$notified.'</td>
				  <td width="58" class="text_size">'.$dispensed.'</td>
				  <td width="58" class="text_size">'.$supplies_qry_row['item_comment'].'</td>-->
			</tr>';
		}
  		$pdfHTML.= '</table><br />';  
	}
	
	
	$sel_med_qry_check=imw_query("select * from in_order_details where order_id ='$order_id' $whereDetIds and patient_id='$patient_id' and module_type_id='6' and del_status='0'")or die(imw_error().' 1114');
		
	if(imw_num_rows($sel_med_qry_check)>0)
	{
		$pdfHTML.='<table class="border" width="760" cellpadding="0" cellspacing="0">
        <tr>
          <td class="tb_heading" colspan="5" width="760"><strong>Medicines</strong></td>
        </tr>            
		<tr>
          <td width="140" class="tb_subheading"><strong>UPC</strong></td>
		  <td width="140" class="tb_subheading"><strong>Manufacturer</strong></td>
          <td width="140" class="tb_subheading"><strong>Hazcordous</strong></td>              
          <td width="140" class="tb_subheading"><strong>Description</strong></td>
          <td width="150" class="tb_subheading"><strong>Status</strong></td>
          <!--<td width="77" class="tb_subheading"><strong>Received</strong></td>
          <td width="77" class="tb_subheading"><strong>Notified</strong></td>
          <td width="77" class="tb_subheading"><strong>Dispensed</strong></td>
          <td width="77" class="tb_subheading"><strong>Notes</strong></td>-->
        </tr>';

		while($med_qry_row = imw_fetch_array($sel_med_qry_check))
		{
			$sel_med_qryy=imw_query("select md.manufacturer_name, itm.harcardous, itm.type_desc from in_item as itm left join in_manufacturer_details as md on md.id=itm.manufacturer_id where itm.id='".$med_qry_row['item_id']."' and itm.module_type_id='6'");
			$sel_med_order=imw_fetch_array($sel_med_qryy);
			
			if($med_qry_row['order_status']==''){
				$medicine_status='Pending';
			}
			else{
				$medicine_status=$med_qry_row['order_status'];
			}
			$ordered = "";
			$received = "";
			$notified = "";
			$dispensed = "";
			if($med_qry_row['ordered']!="" && $med_qry_row['ordered']!="0000-00-00")
			{
				$ordered = getDateFormat($med_qry_row['ordered']);
			}
			if($med_qry_row['received']!="" && $med_qry_row['received']!="0000-00-00")
			{
				$received = getDateFormat($med_qry_row['received']);
			}
			if($med_qry_row['notified']!="" && $med_qry_row['notified']!="0000-00-00")
			{
				$notified = getDateFormat($med_qry_row['notified']);
			}
			if($med_qry_row['dispensed']!="" && $med_qry_row['dispensed']!="0000-00-00")
			{
				$dispensed = getDateFormat($med_qry_row['dispensed']);
			}
			if($sel_med_order['harcardous']=="1"){ $harcar = "YES"; } else { $harcar = "NO"; }

		$pdfHTML.=
			'<tr>
				  <td width="140" class="text_size">'.$med_qry_row['upc_code'].'</td>
				  <td width="140" class="text_size">'.$sel_med_order['manufacturer_name'].'</td>             
				  <td width="140" class="text_size">'.$harcar.'</td>              
				  <td width="140" class="text_size">'.$sel_med_order['type_desc'].'</td>
				  <td width="150" class="text_size">'.$medicine_status.'</td>
				  <!--<td width="77" class="text_size">'.$received.'</td>
				  <td width="77" class="text_size">'.$notified.'</td>
				  <td width="77" class="text_size">'.$dispensed.'</td>
				  <td width="77" class="text_size">'.$med_qry_row['item_comment'].'</td>-->
			</tr>';
		}
  		$pdfHTML.= '</table><br />';  
	}
	
	$sel_access_qry_check=imw_query("select * from in_order_details where order_id ='$order_id' $whereDetIds and patient_id='$patient_id' and module_type_id='7' and del_status='0'")or die(imw_error().' 1183');
		
	if(imw_num_rows($sel_access_qry_check)>0)
	{
		$pdfHTML.='<table class="border" width="760" cellpadding="0" cellspacing="0">
        <tr>
          <td class="tb_heading" colspan="8" width="760"><strong>Accessories</strong></td>
        </tr>            
		<tr>
          <td width="87" class="tb_subheading"><strong>UPC</strong></td>
		  <td width="120" class="tb_subheading"><strong>Manufacturer</strong></td>
          <!--td width="87" class="tb_subheading"><strong>Size</strong></td-->
          <td width="87" class="tb_subheading"><strong>Measurment</strong></td>
          <td width="87" class="tb_subheading"><strong>Size</strong></td>
          <!--td width="87" class="tb_subheading"><strong>Other</strong></td-->
          <td width="100" class="tb_subheading"><strong>Description</strong></td>
          <td width="87" class="tb_subheading"><strong>Status</strong></td>
          <!--<td width="58" class="tb_subheading"><strong>Received</strong></td>
          <td width="58" class="tb_subheading"><strong>Notified</strong></td>
          <td width="58" class="tb_subheading"><strong>Dispensed</strong></td>
          <td width="58" class="tb_subheading"><strong>Notes</strong></td>-->
        </tr>';

		while($access_qry_row = imw_fetch_array($sel_access_qry_check))
		{
			$sel_access_qryy=imw_query("select itm.num_size, ss.size_name, sm.measurment_name, md.manufacturer_name, itm.other, itm.type_desc from in_item as itm left join in_manufacturer_details as md on md.id=itm.manufacturer_id left join in_supplies_measurment as sm on sm.id=itm.measurment left join in_supplies_size as ss on ss.id=itm.char_size where itm.id='".$access_qry_row['item_id']."' and itm.module_type_id='7'");
			$sel_access_order=imw_fetch_array($sel_access_qryy);

			if($access_qry_row['order_status']==''){
				$access_status='Pending';
			}
			else{
				$access_status=$access_qry_row['order_status'];
			}
			$ordered = "";
			$received = "";
			$notified = "";
			$dispensed = "";
			if($access_qry_row['ordered']!="" && $access_qry_row['ordered']!="0000-00-00")
			{
				$ordered = getDateFormat($access_qry_row['ordered']);
			}
			if($access_qry_row['received']!="" && $access_qry_row['received']!="0000-00-00")
			{
				$received = getDateFormat($access_qry_row['received']);
			}
			if($access_qry_row['notified']!="" && $access_qry_row['notified']!="0000-00-00")
			{
				$notified = getDateFormat($access_qry_row['notified']);
			}
			if($access_qry_row['dispensed']!="" && $access_qry_row['dispensed']!="0000-00-00")
			{
				$dispensed = getDateFormat($access_qry_row['dispensed']);
			}

		$pdfHTML.=
			'<tr>
				  <td width="87" class="text_size">'.$access_qry_row['upc_code'].'</td>
				  <td width="120" class="text_size">'.$sel_access_order['manufacturer_name'].'</td>
				  <!--td width="87" class="text_size">'.$sel_access_order['size_name'].'</td-->
				  <td width="87" class="text_size">'.$sel_access_order['measurment_name'].'</td>              
				  <td width="87" class="text_size">'.$sel_access_order['num_size'].'</td>              
				  <!--td width="87" class="text_size">'.$sel_access_order['other'].'</td-->
				  <td width="100" class="text_size">'.$sel_access_order['type_desc'].'</td>
				  <td width="87" class="text_size">'.$access_status.'</td>
				  <!--<td width="58" class="text_size">'.$received.'</td>
				  <td width="58" class="text_size">'.$notified.'</td>
				  <td width="58" class="text_size">'.$dispensed.'</td>
				  <td width="58" class="text_size">'.$access_qry_row['item_comment'].'</td>-->
			</tr>';
		}
  		$pdfHTML.= '</table><br />';  
	}
	
	/*Custm Charges*/
	$cs_qry_check=imw_query("select * from in_order_details where order_id ='$order_id' $whereDetIds and patient_id='$patient_id' and module_type_id='9' and del_status='0'")or die(imw_error().' 1660');
	if(imw_num_rows($cs_qry_check)>0)
	{
		$pdfHTML.='<table class="border" width="760" cellpadding="0" cellspacing="0">
        <tr>
          <td class="tb_heading" colspan="12" ><strong>Custom Charges</strong></td>
        </tr>            
		<tr>
          <td class="tb_subheading"><strong>Name</strong></td>
          <td width="45" class="tb_subheading"><strong>Status</strong></td>
        </tr>';

		while($cs_qry_row = imw_fetch_array($cs_qry_check))
		{
			
			if($cs_qry_row['order_status']==''){
				$order_status='Pending';
			}
			else{
				$order_status=ucfirst($cs_qry_row['order_status']);
			}

		$pdfHTML.=
			'<tr>
				  <td width="200" class="text_size">'.$cs_qry_row['item_name'].'</td>
				  <td width="58" class="text_size">'.$order_status.'</td>
			</tr>';
		}
  		$pdfHTML.= '</table><br />';  
	}
	/*End Custom Charges*/
	
	
	if($lens_other!="")
	{
		$pdfHTML.='<table class="border" width="760" cellpadding="0" cellspacing="0">
        <tr>
          <td class="tb_heading"><strong>Comment</strong></td>
        </tr>';

		$pdfHTML.=
			'<tr>
				  <td width="760" class="text_size">'.$lens_other.'</td>
			</tr>';
  		$pdfHTML.= '</table><br />';  
	}
	
	$pdfHTML.='</page>';
	
}
}

  
  $pdfText = $css.$pdfHTML;
  
  file_put_contents('../../library/new_html2pdf/print_pos_'.$_SESSION['authId'].'.html',$pdfText);

?>

<script>

var url = '<?php echo $GLOBALS['WEB_PATH'];?>/library/new_html2pdf/createPdf.php?op=p&file_name=../../library/new_html2pdf/print_pos_<?php echo $_SESSION['authId'];?>';
window.location.href = url;

</script>