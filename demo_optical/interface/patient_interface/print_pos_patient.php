<?php 
/*
File: print_pos.php
Coded in PHP7
Purpose: Print POS
Access Type: Direct access
*/
require_once(dirname('__FILE__')."/../../config/config.php"); 
require_once(dirname('__FILE__')."/../../library/classes/functions.php"); 

$patient_id=$_SESSION['patient_session_id'];
$seperator = "";
if(isset($_REQUEST['order_id']))
{
	$_SESSION['order_id']=$_REQUEST['order_id'];
    $query=imw_query("select patient_id, entered_date, loc_id, due_date from in_order where id='".$_REQUEST['order_id']."' limit 0,1");
    $getResult=imw_fetch_array($query);
	//order date
	$order_entered_date=date("m-d-Y", strtotime($getResult['entered_date']));
	//order due date
	$order_due_date='';
	if($getResult['due_date']!='0000-00-00'){
	$order_due_date=date("m-d-Y", strtotime($getResult['due_date']));}
	//order facility
	$qLoc=imw_query("select loc_name from in_location where id='$getResult[loc_id]'");
	$dLoc=imw_fetch_assoc($qLoc);
	$order_location=$dLoc['loc_name'];
	
    $patient_id=$getResult['patient_id'];
	//print_r($_SESSION['order_id']);
}
//$section=($_REQUEST['section']?$_REQUEST['section']:"Optical Frames");
//get master header and footer
$qHeader=imw_query("select * from in_print_header ")or die(imw_error());
while($dHeader=imw_fetch_object($qHeader))
{
	$print_setting[$dHeader->pid][$dHeader->label]['value']=trim($dHeader->value);
	$print_setting[$dHeader->pid][$dHeader->label]['margin']=trim($dHeader->margin);
}

$print_header=$print_setting[0]['Master Header']['value'];
$print_footer=$print_setting[0]['Master Footer']['value'];
$margin_top=$print_setting[0]['Master Header']['margin'];
$margin_bot=$print_setting[0]['Master Footer']['margin'];
	
if($_REQUEST['section']=='Frame and Lens Selection'){
	$section=$_REQUEST['section'];
	//get custom header for frames
	if(strlen(trim(strip_tags($print_setting[1]['Header']['value']))))$print_header=$print_setting[1]['Header']['value'];
	if(strlen(trim(strip_tags($print_setting[1]['Footer']['value']))))$print_footer=$print_setting[1]['Footer']['value'];
	
	if($print_setting[1]['Header']['margin']>0)$margin_top=$print_setting[1]['Header']['margin'];
	if($print_setting[1]['Footer']['margin']>0)$margin_bot=$print_setting[1]['Footer']['margin'];
}else if($_REQUEST['section']=="Other Selection"){
	$section=$_REQUEST['section'];
	//get custom header for frames
	if(strlen(trim(strip_tags($print_setting[1]['Header']['value']))))$print_header=$print_setting[1]['Header']['value'];
	if(strlen(trim(strip_tags($print_setting[1]['Footer']['value']))))$print_footer=$print_setting[1]['Footer']['value'];
	
	if($print_setting[1]['Header']['margin']>0)$margin_top=$print_setting[1]['Header']['margin'];
	if($print_setting[1]['Footer']['margin']>0)$margin_bot=$print_setting[1]['Footer']['margin'];
}else{
	$section='Contact Lenses Selection';
	//get custom header for frames
	if(strlen(trim(strip_tags($print_setting[3]['Header']['value']))))$print_header=$print_setting[3]['Header']['value'];
	if(strlen(trim(strip_tags($print_setting[3]['Footer']['value']))))$print_footer=$print_setting[3]['Footer']['value'];
	
	if($print_setting[3]['Header']['margin']>0)$margin_top=$print_setting[3]['Header']['margin'];
	if($print_setting[3]['Footer']['margin']>0)$margin_bot=$print_setting[3]['Footer']['margin'];
}
//if user left some space or some code behind (we assumed that user will not put 7 or less character for header footer)
if(strlen($print_header)>=7)$print_header=$print_header;
else $print_header='';

if(strlen($print_footer)>=7)$print_footer=$print_footer;
else $print_footer='';

$in_locationID=$_SESSION['pro_fac_id'];
function removespecialchar($str)
{
	return preg_replace('/[^A-Za-z0-9\+\/\-]/', ' ',$str);
}

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

//------------------------ Start Discount Code Qry -----------------------//

$getdis_data="select d_id,d_code,d_default from discount_code";
$getdis_data_qry = imw_query($getdis_data);
while($getdis_data_row = imw_fetch_array($getdis_data_qry)){
	$dis_code_arr[$getdis_data_row['d_id']]=$getdis_data_row['d_code'];
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

if($_SESSION['order_id']>0)
{
			$order_id=$_SESSION['order_id'];
			
			$sum_pt_paid = "0";
			$sum_pt_paid_qry = imw_query("SELECT IF(`module_type_id`=3, SUM(`pt_paid`)+SUM(`pt_paid_os`), SUM(`pt_paid`)) AS 'paid' FROM `in_order_details` WHERE `order_id`='".$order_id."'");
			if($sum_pt_paid_qry){
				$sum_pt_paid_qry = imw_fetch_assoc($sum_pt_paid_qry);
				$sum_pt_paid = $sum_pt_paid_qry['paid'];
			}
			
			$sel_qry_val = "select * from in_order_details where order_id ='$order_id' and patient_id='$patient_id' and module_type_id='1' and del_status='0'";

			$sel_qry = imw_query($sel_qry_val);
			$sel_order=imw_fetch_array($sel_qry);
			$frame_order_detail_id=$sel_order['id'];			
						
			$sel_lens_qry=imw_query("select * from in_order_details where order_id ='$order_id' and patient_id='$patient_id' and module_type_id='2' and del_status='0'");
			$sel_lens_order=imw_fetch_array($sel_lens_qry);
			$lens_order_detail_id=$sel_lens_order['id'];
			
			$sel_contact_qry=imw_query("select * from in_order_details where order_id ='$order_id' and patient_id='$patient_id' and module_type_id='3' and del_status='0'");
			
			$sel_contact_order=imw_fetch_array($sel_contact_qry);
			$cl_order_detail_id=$sel_contact_order['id'];

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
//echo "Select * FROM in_optical_order_form WHERE order_id='".$order_id."' AND det_order_id='$lens_order_detail_id' AND patient_id='".$patient_id."'";
			 
			 
			 
			$lensRs=imw_query("Select * FROM in_optical_order_form WHERE order_id='".$order_id."' AND patient_id='".$patient_id."'");
			
			$lensResArr=array();
			
			while($lensResData=imw_fetch_array($lensRs))
			{
					$lensResArr[]=$lensResData;
			}
			//echo count($lensResArr);	
			//CONTACT LENS PRESCRIPTION
			$clLensRs=imw_query("Select * FROM in_cl_prescriptions WHERE order_id='".$order_id."' AND patient_id='".$patient_id."'");
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

$lab_qry = imw_query("select * from in_lens_lab where del_status='0' order by lab_name asc");	
while($lab_row = imw_fetch_assoc($lab_qry)){
if($lab_row['id']==$sel_order['lab_id']){ $frame_lab_name = $lab_row['lab_name']; }   
}

$rows="";
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

$rows = data("select * from in_color where del_status='0' order by color_name asc");
foreach($rows as $r)
{ 
$sel=($r['id']==$sel_contact_order['color_id'])? 'selected': ''; 
$contacts_color_name = ucfirst($r['color_name']); 
}	


$rows="";
$rows = data("select * from in_supply where del_status='0' order by supply_name asc");
foreach($rows as $r)
{ 
$sel=($r['id']==$sel_contact_order['supply_id'])? 'selected': ''; 
$contacts_supply_name = ucfirst($r['supply_name']);
}	
	
$css = '<style type="text/css">	
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
.paddingBottom{
	margin-top:10px;
}
.tb_subheading{
	font-size:10px;
	font-weight:bold;
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
.vtop{
	vertical-align:top;
}
</style>';
					
					$pinfo = imw_query("select fname,lname,mname,DOB,sex,postal_code,city,state,street,preferr_contact,phone_home,phone_biz,phone_cell from patient_data where id = '".$patient_id."'");
					$pinforow = imw_fetch_assoc($pinfo);
                    $pname='';
                    $dob='';
                    if($pinforow) {
                        $pname = $pinforow['lname'].", ".$pinforow['fname']." ".$pinforow['mname'];
                        $pdob= $pinforow['DOB'];
                        $dob=date("m-d-Y",strtotime($pdob));
                    }
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
					
					$facility=imw_query("select loc_name,fax,tel_num,zip,city,state,loc_logo,tax_label,address from in_location where id='".$in_locationID."'");
					$getfacilityInfo=imw_fetch_assoc($facility);
					$facility_name=ucfirst($getfacilityInfo['loc_name']);
					$facility_fax=$getfacilityInfo['fax'];
					$facility_ph=$getfacilityInfo['tel_num'];
					$facility_zip=$getfacilityInfo['zip'];
					$facility_city=$getfacilityInfo['city'];
					$facility_state=$getfacilityInfo['state'];
					$facility_logo=$getfacilityInfo['loc_logo'];
					$facility_address=$getfacilityInfo['address'];
					$facility_tax_label = ($getfacilityInfo['tax_label']=="")?"Tax":$getfacilityInfo['tax_label'];
					
					$logo_path=$GLOBALS['WEB_PATH']."/interface/patient_interface/uploaddir/facility_logo/";
					if($facility_logo!=''){
						$imgWidth = (array_key_exists('LOCAL_SERVER', $GLOBALS) && (strtolower($GLOBALS['LOCAL_SERVER']) == 'fairview') || strtolower($GLOBALS['LOCAL_SERVER']) == 'eyecarearkansas') ? 240 : 120;//90 was default change to 120
						$image= show_image_thumb($facility_logo, $imgWidth, 90);
					}
$backtop='36';				
$backbottom='2';
if($print_header)
{
	$print_header=$print_header;	
	$backtop+=$margin_top;					
}
if($print_footer)
{			
	$backbottom+=$margin_bot;
}

// get physician name 
$physician_name = '';
if(isset($_SESSION['order_id']) && !empty($_SESSION['order_id']) && !empty($patient_id))
{
	if(sizeof($lensResArr)>0)
	{
		$physician_info=imw_query("Select physician_name, physician_id, order_id, patient_id
		FROM in_optical_order_form WHERE order_id='".$_SESSION['order_id']."' AND patient_id='".$patient_id."' ");
		$physician_info_row=imw_fetch_assoc($physician_info);
		if(!empty($physician_info_row['physician_name']))
		{
			if($physician_info_row['physician_name']){
				$physician_name = $physician_info_row['physician_name'];	
			}else $physician_id = $physician_info_row['physician_id'];
		}	
	}elseif(sizeof($clLensResArr)>0)
	{
		$physician_info=imw_query("Select physician_name, physician_id
		FROM in_cl_prescriptions WHERE order_id='".$_SESSION['order_id']."' AND patient_id='".$patient_id."' ");
		$physician_info_row=imw_fetch_assoc($physician_info);
		if(!empty($physician_info_row['physician_name']))
		{
			if($physician_info_row['physician_name']){
				$physician_name = $physician_info_row['physician_name'];	
			}else $physician_id = $physician_info_row['physician_id'];
		}	
	}
	
	if(!$physician_name && $physician_id)
	{
		//get phy name from database
		$qRef=imw_query("select CONCAT(LastName,', ',FirstName)as name from refferphysician where physician_Reffer_id=$physician_id");
		$rRef=imw_fetch_object($qRef);
		$physician_name = $rRef->name;
	}
}

//
$pdfHTML='<page backtop="'.$backtop.'mm" backbottom="'.$backbottom.'mm">
<page_header height="150">'.$print_header.'	<br clear="all">

<table width="730" cellpadding="0" cellspacing="0">
<tr>
<td width="233" align="left" class="tb_headingHeader"><strong>'.$section.'</strong></td>
<td width="233" align="center" class="tb_headingHeader">Order ID: '.$_SESSION['order_id'].'</td>
<td width="273" align="right" class="tb_headingHeader"><strong>Created By: '.$_SESSION['authProviderName']." &nbsp; " .date("m-d-Y h:i a",time()).'</strong> </td>
</tr>
<tr>
<td width="233" align="left" valign="top" class="text_size"><strong>'.$facility_name.'</strong><br>'.$facility_address.'<br>'.$facility_city.',&nbsp;'.$facility_state.'&nbsp;'.$facility_zip.'<br>Ph: '.core_phone_format($facility_ph).'<br> Fax: '.core_phone_format($facility_fax).'<br>Order Facility: '.$order_location.'<br>Order Date: '.$order_entered_date;
if($order_due_date)$pdfHTML.='<br>Order Due Date: '.$order_due_date;
if($physician_name)$pdfHTML.='<br>Physician Name: '.$physician_name;
$pdfHTML.='<br></td>
<td width="233" align="center" valign="top" class="text_size paddingTop">'.$image.'</td>
<td width="273" align="right" valign="top" class="text_size"><strong>'.$pname.' - '.$patient_id.'</strong><br>'.$dob.'<br>'.$pstreet.'&nbsp;<br>'.$pcity.',&nbsp;'.$pstate.'&nbsp;'.$pzipcode.'<br />'.(($ptPhone!="")?"Ph: ".core_phone_format($ptPhone):"").'</td>
</tr>

</table>
</page_header>
<page_footer>'.$print_footer.'
</page_footer>';

	$sel_lens_qryy_check=imw_query("select * from in_order_details where order_id ='$order_id' and patient_id='$patient_id' and module_type_id='2' and del_status='0'");
	
	if(imw_num_rows($sel_lens_qryy_check)>0)
	{

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
			$field .= " in_lens_ar.ar_name , ";
			$qry .= " inner join in_lens_ar on in_lens_ar.id = '".$sel_lens_order['a_r_id']."'";
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
			if($lensOptionRow['transition_name']!=""){ $transition_name = $lensOptionRow['transition_name']."; "; }
			if($lensOptionRow['ar_name']!=""){ $ar_name = $lensOptionRow['ar_name']."; "; }
			if($lensOptionRow['tint_type']!=""){ $tint_type = $lensOptionRow['tint_type']."; "; }
			if($lensOptionRow['progressive_name']!=""){ $progressive_name = $lensOptionRow['progressive_name']."; "; }
			if($lensOptionRow['polarized_name']!=""){ $polarized_name = $lensOptionRow['polarized_name']."; "; }
			if($lensOptionRow['edge_name']!=""){ $edge_name = $lensOptionRow['edge_name']."; "; }
		} 
		if($sel_lens_order['lens_other']!="")
		{
			$lens_other = $sel_lens_order['lens_other']."; ";
		}
			
		if($sel_lens_order['uv400']==1)
		{
			$uv400="uv400";
		}
		if($sel_lens_order['pgx']==1)
		{
			$pgx="pgx";
		}
	}
	
$other_qry_check = imw_query("select iod.* from in_order_details as iod 
				where iod.order_id ='$order_id' 
				and iod.patient_id='$patient_id' 
				and module_type_id IN(1,2,3,5,6,7,9) and iod.del_status='0'")or die(imw_error());

/*Disinfectant data for contact lens*/
$disinfectants = array();
$dinfectant_detail_qry = imw_query("SELECT `id`, `name` FROM `in_cl_disinfecting` ORDER BY `id` ASC");
if($dinfectant_detail_qry && imw_num_rows($dinfectant_detail_qry)>0){
	while($row = imw_fetch_assoc($dinfectant_detail_qry)){
		$disinfectants[$row['id']] = $row['name'];
	}
}
$disinfectant = array();
$disinfectant_qry = imw_query("SELECT * FROM `in_order_cl_detail` WHERE `order_id`='$order_id' AND `module_type_id`='3' AND `del_status`='0'");
if($disinfectant_qry && imw_num_rows($disinfectant_qry)>0){
	while($row = imw_fetch_assoc($disinfectant_qry)){
		$detail = array();
		$detail['name'] = $disinfectants[$row['item_id']];
		$detail['prac_code'] = $row['prac_code'];
		$detail['price'] = $row['price'];
		$detail['qty'] = $row['qty'];
		$detail['allowed'] = $row['allowed'];
		$detail['discount'] = $row['discount'];
		$detail['total_amount'] = $row['total_amount'];
		$detail['ins_amount'] = $row['ins_amount'];
		$detail['pt_paid'] = $row['pt_paid'];
		$detail['pt_resp'] = $row['pt_resp'];
		
		$sum_pt_paid += $row['pt_paid'];
		
		$disinfectant[$row['order_detail_id']] = $detail;
	}
}
/*End disinfectant data for contact lens*/

/*Remake Data*/
$remakeData = array();
$remake_qry = imw_query("SELECT `prac_code_id`, `price`, `qty`, `allowed`, `discount`, `total_amount`, `ins_amount`, `pt_paid`, `pt_resp` FROM `in_order_remake_details` WHERE `order_id`='".$order_id."'");
if($remake_qry && imw_num_rows($remake_qry)>0){
	$row = imw_fetch_assoc($remake_qry);
	
	$remakeData['name'] = "Remake Charges";
	$remakeData['prac_code'] = $row['prac_code_id'];
	$remakeData['price'] = $row['price'];
	$remakeData['qty'] = $row['qty'];
	$remakeData['allowed'] = $row['allowed'];
	$remakeData['discount'] = $row['discount'];
	$remakeData['total_amount'] = $row['total_amount'];
	$remakeData['ins_amount'] = $row['ins_amount'];
	$remakeData['pt_paid'] = $row['pt_paid'];
	$remakeData['pt_resp'] = $row['pt_resp'];	
}
/*End Remake Data*/


$creditCards = array("AX"=>"American Express", "Dis"=>"Discover", "MC"=>"Master Card", "Visa"=>"Visa");
$sel_order_payment_details = false;
$sel_order_comment_qry = imw_query("SELECT `comment`, main_default_discount_code, main_default_ins_case, comment, payment_mode, checkNo, creditCardNo, creditCardCo, expirationDate,overall_discount,total_overall_discount,tax_payable,tax_pt_paid,grand_total FROM `in_order` WHERE `id`='".$order_id."'");
if($sel_order_comment_qry){
	$sel_order_payment_details = imw_fetch_assoc($sel_order_comment_qry);
}
	
	if(imw_num_rows($other_qry_check)>0)
	{
	
	$pdfHTML.= '
	<table class="border paddingBottom" width="710" cellpadding="0" cellspacing="0">
		<tr>
			<td width="140" class="tb_subheading">Item - Description</td>
			<td width="70" class="tb_subheading">Brand</td> 
			<td width="90" class="tb_subheading">V Code</td> 
			<td width="60" align="right" class="tb_subheading">Unit Cost</td>
			<td width="30" align="right" class="tb_subheading">Unit</td>
			<td width="60" align="right" class="tb_subheading">T. Unit Cost</td>
			<td width="60" align="right" class="tb_subheading">Ins. Resp</td>
			<td width="60" align="right" class="tb_subheading">Discount</td>
			<td width="60" align="right" class="tb_subheading">Pt Paid</td>
			<td width="60" align="right" class="tb_subheading">Pt Resp</td>
		</tr>';
		$grand_price=$grand_disc=$grand_tot=$grand_insamt=$grand_ptpaid=$grand_ptresp=0;
		$price=$disc=$tot_amt=$insamt=$ptpaid=$ptresp=0;
		while($other_qry_row=imw_fetch_array($other_qry_check))
		{
			$brand='';
			$color='';
			if($proc_code_arr[$other_qry_row['item_prac_code']]==""){
				$prac_code=$other_qry_row['item_prac_code_default'];
			}
			else{
				$prac_code=$proc_code_arr[$other_qry_row['item_prac_code']];
			}
			//get brand detail if any
			if($other_qry_row['brand_id'])
			{
				if($other_qry_row['module_type_id']==1)//get brand name for frame
				{
					$getBrand=imw_query("select frame_source from in_frame_sources where id='$other_qry_row[brand_id]'")or die(imw_error());
					$brandData=imw_fetch_object($getBrand);
					$brand=$brandData->frame_source;
				}
				elseif($other_qry_row['module_type_id']==3)//get brand name for contact lens
				{
					$getBrand=imw_query("select brand_name from in_contact_brand where id='$other_qry_row[brand_id]'")or die(imw_error());
					$brandData=imw_fetch_object($getBrand);
					$brand=$brandData->brand_name;
				}
			}
			//get color detal if any
			if($other_qry_row['color_id'])
			{
				if($other_qry_row['module_type_id']==1)//get color name for frame
				{
					$getColor=imw_query("select color_name from in_frame_color where id='$other_qry_row[color_id]'")or die(imw_error());
					$colorData=imw_fetch_object($getColor);
				 	$color=$colorData->color_name;
				}
				elseif($other_qry_row['module_type_id']==2)//get color name for contact lens
				{
					$getColor=imw_query("select color_name from in_lens_color where id='$other_qry_row[color_id]'")or die(imw_error());
					$colorData=imw_fetch_object($getColor);
					$color=$colorData->color_name;
				}
				elseif($other_qry_row['module_type_id']==3)//get color name for contact lens
				{
					$getColor=imw_query("select color_name from in_color where id='$other_qry_row[color_id]'")or die(imw_error());
					$colorData=imw_fetch_object($getColor);
					$color=$colorData->color_name;
				}	
			}
			
			if($other_qry_row['module_type_id']==2)
			{
				
				$lens_details = array();
				//array('lens','progressive','material','transition','a_r','tint','polarization','edge','color','uv400','other','pgx');
				$lens_details['lens'] = $other_qry_row['type_id'];
				$lens_details['design'] = $other_qry_row['design_id'];
				//$lens_details['progressive'] = $other_qry_row['progressive_id'];
				$lens_details['material'] = $other_qry_row['material_id'];
				//$lens_details['transition'] = $other_qry_row['transition_id'];
				$lens_details['a_r'] = $other_qry_row['a_r_id'];
				//$lens_details['tint'] = $other_qry_row['tint_id'];
				//$lens_details['polarization'] = $other_qry_row['polarized_id'];
				//$lens_details['edge'] = $other_qry_row['edge_id'];
				//$lens_details['color'] = $other_qry_row['color_id'];
				//$lens_details['uv'] = $other_qry_row['uv400'];
				//$lens_details['pgx'] = $other_qry_row['pgx'];
				$values = getLensDetailValues($lens_details);
				
				//$show_lens_value_arr = array('type_id','progressive_id','material_id','transition_id','a_r_id','tint_id','polarized_id','edge_id','color_id','uv400','lens_other','pgx');
				$show_lens_value_arr = array('type_id','design_id','material_id','a_r_id', 'diopter_id', 'oversize_id', 'lens_other');
					
				//$show_itemized_name_arr = array('lens','progressive','material','transition','a_r','tint','polarization','edge','color','UV400','other','pgx');
				$show_itemized_name_arr = array('lens','design','material','a_r','diopter', 'oversize', 'other');
				
				/*Attributes having multiple values*/
				$multiple_vals = array('a_r', "material");
				
				for($l=0;$l<count($show_lens_value_arr);$l++)
				{
					if(($other_qry_row[$show_lens_value_arr[$l]] > 0) || ($other_qry_row[$show_lens_value_arr[$l]]!="" && $show_itemized_name_arr[$l]=="other"))
					{						
						$seg_type_whr="";
						if(!in_array($show_itemized_name_arr[$l], $multiple_vals)){
							if($show_itemized_name_arr[$l]=="lens"){
								//$seg_type_whr=" and total_amt>0";
							}
							$sql = "select * from in_order_lens_price_detail where order_id ='$order_id' and order_detail_id='".$other_qry_row['id']."' and patient_id='$patient_id' and itemized_name='".$show_itemized_name_arr[$l]."' and del_status='0' $seg_type_whr";
							$sel_price_qry=imw_query($sql);
						}
						else{
							$sql = "select * from in_order_lens_price_detail where order_id ='$order_id' and order_detail_id='".$other_qry_row['id']."' and patient_id='$patient_id' and itemized_name LIKE '".$show_itemized_name_arr[$l]."%' and del_status='0'";
							$sel_price_qry=imw_query($sql);
						}
						$materials = array();
						$material_dt_resp = imw_query("SELECT `id`, `material_name`, `vw_code`, `prac_code` FROM `in_lens_material` WHERE `id`='".$other_qry_row['material_id']."'");
						if($material_dt_resp && imw_num_rows($material_dt_resp)>0){
							$material_1 = imw_fetch_assoc($material_dt_resp);
							
							$materials[$material_1['id']] = $material_1['material_name'];
							
							$prac_code = explode(";", $material_1['prac_code']);
							
							if(count($prac_code)>1){
								$vw_code = explode("-", $material_1['vw_code']);
								$mat_resp_parent = imw_query("SELECT `id`, `material_name`, `vw_code` FROM `in_lens_material` WHERE `vw_code`='".$vw_code[0]."-".$vw_code[1]."-NONE-NONE-00' LIMIT 1");
								if($mat_resp_parent && imw_num_rows($mat_resp_parent)>0){
									$material_2 = imw_fetch_assoc($mat_resp_parent);
									$mat_name_2 = explode(" ", $material_2['material_name']);
									
									$materials[$material_1['id']] = str_replace($mat_name_2, "", $materials[$material_1['id']]);
									$materials[$material_2['id']] = $material_2['material_name'];
								}
							}
						}
						$materials = array_map('trim', $materials);
						
						while($sel_lens_price_data=imw_fetch_array($sel_price_qry))
						{
							
							$itemized_nm = "";
							$itemiszed_value = "";
							if($proc_code_arr[$sel_lens_price_data['item_prac_code']]==""){
								$prac_code=$sel_lens_price_data['item_prac_code_default'];
							}
							else{
								$prac_code=$proc_code_arr[$sel_lens_price_data['item_prac_code']];
							}
							if($l==0)
							{
								$upc_code = $other_qry_row['upc_code'];
							}
							if($l!=0){
								$upc_code='&nbsp;';
							}
							
							if(substr($sel_lens_price_data['itemized_name'],0,8)=="material")
							{ 
								$itemized_nm = "Material";
								$value_id = explode("_",$sel_lens_price_data['itemized_name']);
								$value_id = end($value_id);
								
								$itemiszed_value = isset($materials[$value_id])?$materials[$value_id]:"";
								
							}
							elseif(substr($sel_lens_price_data['itemized_name'],0,3)=="a_r")
							{ 
								$itemized_nm = "Treatment";
								$value_id = explode("_",$sel_lens_price_data['itemized_name']);
								$value_id = end($value_id);
								
								$sql = "SELECT `ar_name` FROM `in_lens_ar` WHERE `id`='".$value_id."' AND `del_status`='0'";
								$ar_val = imw_query($sql);
								if($ar_val && imw_num_rows($ar_val)>0){
									$ar_val = imw_fetch_assoc($ar_val);
									$itemiszed_value = $ar_val['ar_name'];
								}
								else{
									$itemiszed_value = $values['a_r'];
								}
							} 
							elseif($sel_lens_price_data['itemized_name']=="uv400"){
								$itemized_nm = "UV400";
							}
							elseif($sel_lens_price_data['itemized_name']=="pgx"){
								$itemized_nm = "";
							}
							elseif($sel_lens_price_data['itemized_name']=="polarization"){
								$itemized_nm = "Polarized";
								$itemiszed_value = $values['polarization'];
							}
							elseif($sel_lens_price_data['itemized_name']=="lens"){
								$itemized_nm = "Seg Type";
								$itemiszed_value = $values['lens'];
							}
							elseif($sel_lens_price_data['itemized_name']=="design"){
								$itemized_nm = "Design";
								$itemiszed_value = $values['design'];
							}
							
							/*elseif($sel_lens_price_data['itemized_name']=="lens")
							{
								$itemized_nm = "<strong>Seg type</strong> - ".$values['lens'];
							}*/
							/*elseif($sel_lens_price_data['itemized_name']=="color")
							{
								$color_name=$color;	
								$color='';
							}*/
							else 
							{ 
								$itemized_nm = $sel_lens_price_data['itemized_name'];
								$itemiszed_value = $values[$sel_lens_price_data['itemized_name']];
							}

							if($sel_lens_price_data['itemized_name']!="color")$color_name='';
							
							$price += $sel_lens_price_data['wholesale_price'];
							//$tot_amt += $sel_lens_price_data['total_amt'];
							$tot_amt += $sel_lens_price_data['allowed'];
							$insamt += $sel_lens_price_data['ins_amount'];
							$ptpaid += $sel_lens_price_data['pt_paid'];
							$ptresp += $sel_lens_price_data['pt_resp'];
							$tot_allowed+=$sel_lens_price_data['allowed'];
							$tot_qty+=$sel_lens_price_data['qty'];						
							
							if($sel_lens_price_data['allowed']!=''){
								$allowed=$sel_lens_price_data['allowed']-$sel_lens_price_data['ins_amount'];
								$discount=$sel_lens_price_data['discount'];
								$discount = explode("%", $discount);
								if(count($discount)>1){
									$disc_val=$allowed*$discount[0]/100;
								}
								else{
									$disc_val=$discount[0];
								}
								
								$disc_total+=$disc_val;
							}
							else{
								$disc_val=$sel_lens_price_data['discount'];
							}
							$disc += $disc_val;
										
							if($itemiszed_value != "")
							{
								$seperator = " - ";
							}else{
								$seperator = "";
							}								
										
							$pdfHTML.=
							
							'<tr>
								<td width="140" class="text_size">'.ucfirst($itemized_nm).$seperator.ucfirst($itemiszed_value).'</td>
								<td width="70" class="text_size">'.$brand.'</td>
								<td width="90" class="text_size">'.$prac_code.'</td>
								<td align="right" class="text_size">'.currency_symbol(true).$sel_lens_price_data['wholesale_price'].'</td>
								<td align="right" class="text_size">'.$sel_lens_price_data['qty'].'</td>
								<td align="right" class="text_size">'.currency_symbol(true).$sel_lens_price_data['allowed'].'</td>
								<td align="right" class="text_size">'.currency_symbol(true).$sel_lens_price_data['ins_amount'].'</td>
								<td align="right" class="text_size">'.currency_symbol(true).number_format((float)$disc_val,2).'</td>
								<td align="right" class="text_size">'.currency_symbol(true).$sel_lens_price_data['pt_paid'].'</td>
								<td align="right" class="text_size">'.currency_symbol(true).$sel_lens_price_data['pt_resp'].'</td>
							</tr>';
						}
					}
				}
			}
			else if($other_qry_row['module_type_id']==3){
				$detail = $other_qry_row['id'];
				
				//OD
				$price += $other_qry_row['price'];
				$other_qry_row['allowed'] = $other_qry_row['price']*$other_qry_row['qty_right'];
				$discount = explode("%", $other_qry_row['discount']);
				$disc_val = 0;
				if(count($discount)>1){
					$disc_val=(($other_qry_row['price']*$other_qry_row['qty_right'])-$other_qry_row['ins_amount'])*$discount[0]/100;
				}
				else{
					$disc_val=$discount[0];
				}
				
				$disc += $disc_val;
				//$tot_amt += $other_qry_row['total_amount'];
				$tot_amt += $other_qry_row['price']*$other_qry_row['qty_right'];
				$insamt += $other_qry_row['ins_amount'];
				$ptpaid += $other_qry_row['pt_paid'];
				$ptresp += $other_qry_row['pt_resp'];
				$tot_allowed+=$other_qry_row['allowed'];
				$tot_qty+=$other_qry_row['qty_right'];
				
				/*Item Description if Item is medicine*/
				$item_desc = "&nbsp;";
				$item_types = array(6, 7);	/*Medicines, Accessories*/
				if(in_array($other_qry_row['module_type_id'], $item_types)){
					$desc_resp = imw_query("SELECT `type_desc` FROM `in_item` WHERE `id`='".$other_qry_row['item_id']."'");
					if($desc_resp && imw_num_rows($desc_resp)>0){
						$desc_resp = imw_fetch_assoc($desc_resp);
						$item_desc = " - ".$desc_resp['type_desc'];
					}
				}
				elseif($other_qry_row['module_type_id']==1 && $other_qry_row['pof_check']==1){
					$item_desc = " - ".'Pt Own Frame';
				}
				
				$pdfHTML.=
				'<tr>
					  <td width="140" class="text_size"><strong style="color: #00f;">RT</strong>  '.$other_qry_row['item_name'].$item_desc.'</td>
					  <td width="70" class="text_size">'.$brand.'</td>
					  <td width="90" align="right" class="text_size">'.$prac_code.'</td>
					  <td align="right" class="text_size">'.currency_symbol(true).$other_qry_row['price'].'</td>
					  <td align="right" class="text_size">'.$other_qry_row['qty_right'].'</td>
					  <td align="right" class="text_size">'.currency_symbol(true).number_format($other_qry_row['allowed'],2).'</td>          
					  <td align="right" class="text_size">'.currency_symbol(true).$other_qry_row['ins_amount'].'</td>
					  <td align="right" class="text_size">'.currency_symbol(true).number_format((float)$disc_val,2).'</td>
					  <td align="right" class="text_size">'.currency_symbol(true).$other_qry_row['pt_paid'].'</td>
					  <td align="right" class="text_size">'.currency_symbol(true).$other_qry_row['pt_resp'].'</td>
			</tr>';
				
				
				//OS
				$price += $other_qry_row['price_os'];
				$other_qry_row['allowed'] = $other_qry_row['price_os']*$other_qry_row['qty'];
				$discount = explode("%", $other_qry_row['discount_os']);
				$disc_val = 0;
				if(count($discount)>1){
					$disc_val=(($other_qry_row['price_os']*$other_qry_row['qty'])-$other_qry_row['ins_amount_os'])*$discount[0]/100;
				}
				else{
					$disc_val=$discount[0];
				}
				
				$disc += $disc_val;
				//$tot_amt += $other_qry_row['total_amount'];
				$tot_amt += $other_qry_row['price_os']*$other_qry_row['qty'];
				$insamt += $other_qry_row['ins_amount_os'];
				$ptpaid += $other_qry_row['pt_paid_os'];
				$ptresp += $other_qry_row['pt_resp_os'];
				$tot_allowed+=$other_qry_row['allowed'];
				$tot_qty+=$other_qry_row['qty'];
				
				
				$pdfHTML.=
				'<tr>
					  <td width="140" class="text_size"><strong style="color: #008000;">LT</strong>  '.$other_qry_row['item_name_os'].$item_desc.'</td>
					  <td width="70" class="text_size">'.$brand.'</td>
					  <td width="90" align="right" class="text_size">'.$prac_code.'</td>
					  <td align="right" class="text_size">'.currency_symbol(true).$other_qry_row['price_os'].'</td>
					  <td align="right" class="text_size">'.$other_qry_row['qty'].'</td>
					  <td align="right" class="text_size">'.currency_symbol(true).number_format($other_qry_row['allowed'],2).'</td>          
					  <td align="right" class="text_size">'.currency_symbol(true).$other_qry_row['ins_amount_os'].'</td>
					  <td align="right" class="text_size">'.currency_symbol(true).number_format((float)$disc_val,2).'</td>
					  <td align="right" class="text_size">'.currency_symbol(true).$other_qry_row['pt_paid_os'].'</td>
					  <td align="right" class="text_size">'.currency_symbol(true).$other_qry_row['pt_resp_os'].'</td>
					</tr>';
				/************************************************/
				if(isset($disinfectant[$detail])){
					$dt = $disinfectant[$detail];
					
					$price += $dt['price'];
					$dt['allowed'] = $dt['price']*$dt['qty'];
					$discount = explode("%", $dt['discount']);
					$disc_val = 0;
					if(count($discount)>1){
						$disc_val=(($dt['price']*$dt['qty'])-$dt['ins_amount'])*$discount[0]/100;
					}
					else{
						$disc_val=$discount[0];
					}
					
					$disc += $disc_val;
					//$tot_amt += $dt['total_amount'];
					$tot_amt += $dt['price']*$dt['qty'];
					$insamt += $dt['ins_amount'];
					$ptpaid += $dt['pt_paid'];
					$ptresp += $dt['pt_resp'];
					$tot_allowed+=$dt['allowed'];
					$tot_qty +=$dt['qty'];
					
			$pdfHTML.='<tr>
					<td width="140" class="text_size">'.$dt['name'].'</td>
					<td width="70" class="text_size">'.$brand.'</td>
					<td width="90" align="right" class="text_size">'.$prac_code.'</td>
					<td align="right" class="text_size">'.currency_symbol(true).$dt['price'].'</td>
					<td align="right" class="text_size">'.$dt['qty'].'</td>
					<td align="right" class="text_size">'.currency_symbol(true).($dt['price']*$dt['qty']).'</td> 
					<td align="right" class="text_size">'.currency_symbol(true).$dt['ins_amount'].'</td>
					<td align="right" class="text_size">'.currency_symbol(true).number_format((float)$disc_val,2).'</td>
					<td align="right" class="text_size">'.currency_symbol(true).$dt['pt_paid'].'</td>
					<td align="right" class="text_size">'.currency_symbol(true).$dt['pt_resp'].'</td>
			</tr>';
				}
			}
			else
			{
				$price += $other_qry_row['price'];
				$other_qry_row['allowed'] = $other_qry_row['price']*$other_qry_row['qty'];
				$discount = explode("%", $other_qry_row['discount']);
				$disc_val = 0;
				if(count($discount)>1){
					$disc_val=(($other_qry_row['price']*$other_qry_row['qty'])-$other_qry_row['ins_amount'])*$discount[0]/100;
				}
				else{
					$disc_val=$discount[0];
				}
				
				$disc += $disc_val;
				//$tot_amt += $other_qry_row['total_amount'];
				$tot_amt += $other_qry_row['price']*$other_qry_row['qty'];
				$insamt += $other_qry_row['ins_amount'];
				$ptpaid += $other_qry_row['pt_paid'];
				$ptresp += $other_qry_row['pt_resp'];
				$tot_allowed+=$other_qry_row['allowed'];
				$tot_qty+=$other_qry_row['qty'];
				
				/*Item Description if Item is medicine*/
				$item_desc = "&nbsp;";
				$item_types = array(6, 7);	/*Medicines, Accessories*/
				if(in_array($other_qry_row['module_type_id'], $item_types)){
					$desc_resp = imw_query("SELECT `type_desc` FROM `in_item` WHERE `id`='".$other_qry_row['item_id']."'");
					if($desc_resp && imw_num_rows($desc_resp)>0){
						$desc_resp = imw_fetch_assoc($desc_resp);
						$item_desc = " - ".$desc_resp['type_desc'];
					}
				}
				elseif($other_qry_row['module_type_id']==1 && $other_qry_row['pof_check']==1){
					$item_desc = " - ".'Pt Own Frame';
				}
				$item_name="";
				$item_name=($other_qry_row['item_name_other'])?$other_qry_row['item_name_other']:$other_qry_row['item_name'];
				$item_name.=$item_desc;
				$pdfHTML.=
				'<tr>
					  <td width="140" class="text_size">'.$item_name.'</td>
					  <td width="70" class="text_size">'.$brand.'</td>
					  <td width="90" align="right" class="text_size">'.$prac_code.'</td>
					  <td align="right" class="text_size">'.currency_symbol(true).$other_qry_row['price'].'</td>
					  <td align="right" class="text_size">'.$other_qry_row['qty'].'</td>
					  <td align="right" class="text_size">'.currency_symbol(true).number_format($other_qry_row['allowed'],2).'</td>          
					  <td align="right" class="text_size">'.currency_symbol(true).$other_qry_row['ins_amount'].'</td>
					  <td align="right" class="text_size">'.currency_symbol(true).number_format((float)$disc_val,2).'</td>
					  <td align="right" class="text_size">'.currency_symbol(true).$other_qry_row['pt_paid'].'</td>
					  <td align="right" class="text_size">'.currency_symbol(true).$other_qry_row['pt_resp'].'</td>
					 </tr>';
				
				$brand='';
				$sel_lens_qry="select * from in_order_details where order_id ='$order_id' and patient_id='$patient_id' and del_status='0' and module_type_id='2' and lens_frame_id='".$other_qry_row['id']."' order by id asc";
					$sel_lens_qry=imw_query($sel_lens_qry);
					if($sel_lens_qry){
						${'pro_cont2'}=(!isset(${'pro_cont2'}))?1:++${'pro_cont2'};
						$lensPro_cont++;
					}
					if(imw_num_rows($sel_lens_qry)>0){
						$sel_records_lens = imw_fetch_array($sel_lens_qry);
						
						$pos_lensd = "_lensD";
						/*$show_lens_value_arr = array('seg_type_od','design_id_od','material_id_od','a_r_id_od','seg_type_os','design_id_os','material_id_os','a_r_id_os','lens_other');*/
						
						$show_lens_value_arr = array('seg_type_od','design_id_od','material_id_od','a_r_id_od', 'diopter_id_od', 'oversize_id');
						
						/*'progressive_id','pgx','transition_id','tint_id','polarized_id','edge_id','color_id','uv400'*/
						
						/*$show_itemized_name_arr = array('lens','design','material','a_r','lens','design','material','a_r','other');*/
						
						$show_itemized_name_arr = array('lens', 'design','material','a_r','diopter', 'oversize');
						
						/*'transition','progressive','pgx','tint','polarization','edge','color','uv400'*/
						
						$multipleLensVals = array('a_r', 'material');
						//$pro_cont=0;
						$pro=0;
						for($l=0;$l<count($show_lens_value_arr);$l++){
							/*Pos Rows for multiselect values*/
							if(in_array($show_itemized_name_arr[$l], $multipleLensVals)!==false){
								
								$vision_val = explode('_', $show_lens_value_arr[$l]);
								$vision		= array_pop($vision_val);
								$vision_qry=" AND vision='".$vision."'";
								$vision_qry = '';
								
								$sel_price_qry="select `itemized_name`, `wholesale_price`, `allowed`, `qty`, `discount`, `ins_amount`, `pt_paid`, `pt_resp`, `vision`, `item_description`, `item_id`, `item_prac_code` from in_order_lens_price_detail where order_id ='$order_id' and order_detail_id='".$sel_records_lens['id']."' and patient_id='$patient_id' and itemized_name LIKE'".$show_itemized_name_arr[$l]."%' and del_status='0' ".$vision_qry." ORDER BY id ASC";
								$sel_price_qry=imw_query($sel_price_qry);
								
								$lens_item_details = array();
								while($lens_row = imw_fetch_assoc($sel_price_qry)){
									array_push($lens_item_details, $lens_row);
								}
								
								if( count($lens_item_details) > 0 ){
									/*error_reporting(E_ALL);
									ini_set('display_errors', 1);*/
									
									//$difference = array_diff($lens_item_details[0], $lens_item_details[1]);
									//unset($difference['vision'], $difference['qty']);
									
//									$vals_merged = array('wholesale_price', 'qty', 'allowed', 'ins_amount', 'discount', 'pt_paid', 'pt_resp');
									
//									if( is_array($difference) && !isset($difference['item_description']) && !isset($difference['item_id']) ){
										
//										$final_row_data = $lens_item_details[0];
//										$final_row_data['vision'] = '&nbsp;&nbsp;';
//										
//										/*Discount calculation for the first element*/
//										$final_row_data['allowed'] = $final_row_data['wholesale_price']*$final_row_data['qty'];
//										$discount = explode("%", $final_row_data['discount']);
//										$disc_val = 0;
//										if(count($discount)>1){
//											$disc_val=(($final_row_data['wholesale_price']*$final_row_data['qty'])-$final_row_data['ins_amount'])*$discount[0]/100;
//										}
//										else{
//											$disc_val=$discount[0];
//										}
//										$final_row_data['discount'] = $disc_val;
////										$disc += $disc_val;
//										/*End discount calculation for the first element*/
//										
//										
//										unset($lens_item_details[0]);
//										$final_details_count = count($lens_item_details);
										
//										for($i=0; $i <= $final_details_count; $i++){
										foreach($lens_item_details as $i=>$tempValues){
											
											$lens_item_details[$i]['allowed'] = $lens_item_details[$i]['wholesale_price']*$lens_item_details[$i]['qty'];
											$discount = explode("%", $lens_item_details[$i]['discount']);
											$disc_val = 0;
											if(count($discount)>1){
												$disc_val=(($lens_item_details[$i]['wholesale_price']*$lens_item_details[$i]['qty'])-$lens_item_details[$i]['ins_amount'])*$discount[0]/100;
											}
											else{
												$disc_val=$discount[0];
											}
											$lens_item_details[$i]['discount'] = $disc_val;
//											$disc += $disc_val;
											
											
//											foreach( $vals_merged as $val_merge ){
//												$final_row_data[$val_merge] += $lens_item_details[$i][$val_merge];
//											}
										}
										unset($tempValues);
//										$lens_item_details = array($final_row_data);
										
//									}
								}
								krsort($lens_item_details);
								
								while($sel_lens_price_data=array_pop($lens_item_details)){
								
									//$nName = substr($sel_lens_price_data['itemized_name'], 0, strrpos($sel_lens_price_data['itemized_name'], "_"));
									$nName = $sel_lens_price_data['itemized_name'];
									$nName = trim($nName);
									if($pro==1) { $clas = ""; } else { $clas = "even"; }
									/*Fix for Previous Data*/
									$itmPrac = ($proc_code_arr[$sel_lens_price_data['item_prac_code']]!="" && $sel_lens_price_data['item_prac_code']!="0")?$proc_code_arr[$sel_lens_price_data['item_prac_code']]:$sel_lens_price_data['item_prac_code_default'];
									
									$itemized_name = $sel_lens_price_data['itemized_name'];
									$vision = "";
									if($sel_lens_price_data['vision'] == 'os')
									{
										$vision = "<strong style='color: #008000;'>LT</strong>";
									}else if($sel_lens_price_data['vision'] == 'od')
									{
										$vision = "<strong style='color: #00f;'>RT</strong>";
									}
									$lensItemName = "";
											if(substr($nName,0,3)=="a_r")
												$lensItemName = "Treatment";
											elseif($nName=="lens")
												$lensItemName = "Seg type";
											elseif(substr($nName,0,8)=="material")
												$lensItemName = "Material";
											else
												$lensItemName = ucfirst($nName);
									$price += $sel_lens_price_data['wholesale_price'];
									$sel_lens_price_data['allowed'] = $sel_lens_price_data['allowed'];
									
									##
									$disc_val = $sel_lens_price_data['discount'];
									$disc += $disc_val;
									##
									
									//$tot_amt += $other_qry_row['total_amount'];
									$tot_amt += $sel_lens_price_data['allowed'];
									$insamt += $sel_lens_price_data['ins_amount'];
									$ptpaid += $sel_lens_price_data['pt_paid'];
									$ptresp += $sel_lens_price_data['pt_resp'];
									$tot_allowed+=$sel_lens_price_data['allowed'];
									$tot_qty+=$sel_lens_price_data['qty'];		
											
								$pdfHTML.=
								'<tr>
									  <td width="140" class="text_size">'.$vision." ".$lensItemName." - ".$sel_lens_price_data['item_description'].'</td>
									  <td width="70" class="text_size">'.$brand.'</td>
									  <td width="90" align="right" class="text_size">'.$itmPrac.'</td>
									  <td align="right" class="text_size">'.currency_symbol(true).number_format($sel_lens_price_data['wholesale_price'], 2).'</td>
									  <td align="right" class="text_size">'.$sel_lens_price_data['qty'].'</td>
									  <td align="right" class="text_size">'.currency_symbol(true).number_format($sel_lens_price_data['allowed'],2).'</td>
									  <td align="right" class="text_size">'.currency_symbol(true).number_format($sel_lens_price_data['ins_amount'], 2).'</td>
									  <td align="right" class="text_size">'.currency_symbol(true).number_format((float)$disc_val,2).'</td>
									  <td align="right" class="text_size">'.currency_symbol(true).number_format($sel_lens_price_data['pt_paid'], 2).'</td>
									  <td align="right" class="text_size">'.currency_symbol(true).number_format($sel_lens_price_data['pt_resp'], 2).'</td>
								</tr>';
								}
							}
							
							/*==============================*/
							
								$vision_val = explode('_', $show_lens_value_arr[$l]);
								$vision		= array_pop($vision_val);
								$vision_qry=" AND vision='".$vision."'";
								$vision_qry='';
								
								if( in_array($show_itemized_name_arr[$l], $multipleLensVals) == false){
								
									$sel_price_qry = "select `itemized_name`, `wholesale_price`, `allowed`, `qty`, `discount`, `ins_amount`, `pt_paid`, `pt_resp`, `vision`, `item_description`, `item_id`, item_prac_code from in_order_lens_price_detail where order_id ='$order_id' and order_detail_id='".$sel_records_lens['id']."' and patient_id='$patient_id' and itemized_name='".$show_itemized_name_arr[$l]."' and del_status='0' ".$vision_qry;
									$sel_price_qry=imw_query($sel_price_qry);
									
									$lens_item_details = array();
									while($lens_row = imw_fetch_assoc($sel_price_qry)){
										array_push($lens_item_details, $lens_row);
									}
									
									if( count($lens_item_details) > 0 ){
									/*error_reporting(E_ALL);
									ini_set('display_errors', 1);*/
									
									//$difference = array_diff($lens_item_details[0], $lens_item_details[1]);
									//unset($difference['vision'], $difference['qty']);
									
//									$vals_merged = array('wholesale_price', 'qty', 'allowed', 'ins_amount', 'discount', 'pt_paid', 'pt_resp');
									
//									if( is_array($difference) && !isset($difference['item_description']) && !isset($difference['item_id']) ){
										
//										$final_row_data = $lens_item_details[0];
//										$final_row_data['vision'] = '&nbsp;&nbsp;';
//										
//										/*Discount calculation for the first element*/
//										$final_row_data['allowed'] = $final_row_data['wholesale_price']*$final_row_data['qty'];
//										$discount = explode("%", $final_row_data['discount']);
//										$disc_val = 0;
//										if(count($discount)>1){
//											$disc_val=(($final_row_data['wholesale_price']*$final_row_data['qty'])-$final_row_data['ins_amount'])*$discount[0]/100;
//										}
//										else{
//											$disc_val=$discount[0];
//										}
//										$final_row_data['discount'] = $disc_val;
////										$disc += $disc_val;
//										/*End discount calculation for the first element*/
//										
//										
//										unset($lens_item_details[0]);
//										$final_details_count = count($lens_item_details);
										
//										for($i=0; $i <= $final_details_count; $i++){
										foreach($lens_item_details as $i=>$tempValues){
											
											$lens_item_details[$i]['allowed'] = $lens_item_details[$i]['wholesale_price']*$lens_item_details[$i]['qty'];
											$discount = explode("%", $lens_item_details[$i]['discount']);
											$disc_val = 0;
											if(count($discount)>1){
												$disc_val=(($lens_item_details[$i]['wholesale_price']*$lens_item_details[$i]['qty'])-$lens_item_details[$i]['ins_amount'])*$discount[0]/100;
											}
											else{
												$disc_val=$discount[0];
											}
											$lens_item_details[$i]['discount'] = $disc_val;
//											$disc += $disc_val;
											
											
//											foreach( $vals_merged as $val_merge ){
//												$final_row_data[$val_merge] += $lens_item_details[$i][$val_merge];
//											}
										}
										unset($tempValues);
//										$lens_item_details = array($final_row_data);
										
//									}
								}
								krsort($lens_item_details);
								while($sel_lens_Price_data=array_pop($lens_item_details)){
									if(substr($sel_lens_Price_data['itemized_name'],0,3)=="a_r") {
										$lensname =  "Treatment"; } 
									elseif($sel_lens_Price_data['itemized_name']=="lens") {
										$lensname = "Seg type"; }
									elseif($sel_lens_Price_data['itemized_name']=="uv400"){
										$lensname = "UV 400"; } 
									else { $lensname = ucfirst($sel_lens_Price_data['itemized_name']); }
									
									if( $sel_lens_Price_data['itemized_name'] === 'diopter' )
										$lensname = 'Prism Diopter Charges';
									elseif( $sel_lens_Price_data['itemized_name'] === 'oversize' )
										$lensname = 'Oversized Lens Charges';
									else
										$lensname .= " - ".$sel_lens_Price_data['item_description'];
									
									$itmPrac = ($proc_code_arr[$sel_lens_Price_data['item_prac_code']]!="" && $sel_lens_Price_data['item_prac_code']!="0")?$proc_code_arr[$sel_lens_Price_data['item_prac_code']]:$sel_lens_Price_data['item_prac_code_default'];
									
									$pro++;
									if($pro==1) { $clas = ""; } else { $clas = "even"; }
									
										$price += $sel_lens_Price_data['wholesale_price'];
										
										###
										$disc_val = $sel_lens_Price_data['discount'];
										$disc += $disc_val;
										###
										
										$tot_amt += $sel_lens_Price_data['allowed'];
										$insamt += $sel_lens_Price_data['ins_amount'];
										$ptpaid += $sel_lens_Price_data['pt_paid'];
										$ptresp += $sel_lens_Price_data['pt_resp'];
										$tot_allowed+=$sel_lens_Price_data['allowed'];
										$tot_qty+=$sel_lens_Price_data['qty'];	
										/*$Vision = "&nbsp;&nbsp;&nbsp;&nbsp;";*/
										$Vision = '';
										if($sel_lens_Price_data['vision'] == 'os')
										{
											$Vision = "<strong style='color: #008000;'>LT</strong>";
										}else if($sel_lens_Price_data['vision'] == 'od')
										{
											$Vision = "<strong style='color: #00f;'>RT</strong>";
										}
									
									$pdfHTML.=
									'<tr>
										  <td width="140" class="text_size">'.$Vision." ".$lensname.'</td>
										  <td width="70" class="text_size">&nbsp;</td>
										  <td width="90" align="right" class="text_size">'.$itmPrac.'</td>
										  <td align="right" class="text_size">'.currency_symbol(true).number_format($sel_lens_Price_data['wholesale_price'], 2).'</td>
										  <td align="right" class="text_size">'.$sel_lens_Price_data['qty'].'</td>
										  <td align="right" class="text_size">'.currency_symbol(true).number_format($sel_lens_Price_data['allowed'], 2).'</td>          
										  <td align="right" class="text_size">'.currency_symbol(true).number_format($sel_lens_Price_data['ins_amount'], 2).'</td>
										  <td align="right" class="text_size">'.currency_symbol(true).number_format((float)$disc_val, 2).'</td>
										  <td align="right" class="text_size">'.currency_symbol(true).number_format($sel_lens_Price_data['pt_paid'], 2).'</td>
										  <td align="right" class="text_size">'.currency_symbol(true).number_format($sel_lens_Price_data['pt_resp'], 2).'</td>
									</tr>';
								}
									
							}
						}	
						
					}
			}
		}
	
	/*Remake Data*/
	if($sel_qry_val['re_make_id']!="0" &&count($remakeData)>0){
		
		$price += $remakeData['price'];
		$remakeData['allowed'] = $remakeData['price']*$remakeData['qty'];
		$discount = explode("%", $remakeData['discount']);
		$disc_val = 0;
		if(count($discount)>1){
			$disc_val=(($remakeData['price']*$remakeData['qty'])-$remakeData['ins_amount'])*$discount[0]/100;
		}
		else{
			$disc_val=$discount[0];
		}
		
		$disc += $disc_val;
		$tot_amt += $remakeData['total_amount'];
		$insamt += $remakeData['ins_amount'];
		$ptpaid += $remakeData['pt_paid'];
		$ptresp += $remakeData['pt_resp'];
		$tot_allowed+=$remakeData['allowed'];
		$tot_qty +=$remakeData['qty'];
		
$pdfHTML.='<tr>
		<td width="140" class="text_size" colspan="1" style="text-align:center;">'.$remakeData['name'].'</td>
		<td class="text_size">&nbsp;</td>
		<td align="right" class="text_size">&nbsp;</td>
		<td align="right" class="text_size">'.currency_symbol(true).$remakeData['price'].'</td>
		<td align="right" class="text_size">'.$remakeData['qty'].'</td>
		<td align="right" class="text_size">'.currency_symbol(true).number_format($remakeData['allowed'],2).'</td> 
		<td align="right" class="text_size">'.currency_symbol(true).$remakeData['ins_amount'].'</td>
		<td align="right" class="text_size">'.currency_symbol(true).number_format((float)$disc_val,2).'</td>
		<td align="right" class="text_size">'.currency_symbol(true).$remakeData['pt_paid'].'</td>
		<td align="right" class="text_size">'.currency_symbol(true).$remakeData['pt_resp'].'</td>
</tr>';
	}	
	/*End Remake Data*/
	
	$grand_price += $price;
	$grand_disc += $disc;
	$grand_tot += $tot_amt;
	$grand_insamt += $insamt;
	$grand_ptpaid += $ptpaid;
	//$grand_ptresp += $ptresp;
	$grand_ptresp = $tot_amt-$insamt-$ptpaid;
	$grand_allowed+= $tot_allowed;		
	
	$total_overall_discount =  $sel_order_payment_details['total_overall_discount'];
	/*$grand_total = $sel_order_payment_details['grand_total'];
	$total_overall_discount = $sel_order_payment_details['total_overall_discount'];
	$tax_payable = $sel_order_payment_details['tax_payable'];/**/
	//$overall_discount = explode("%", trim($sel_order_payment_details['overall_discount']));
	//if(count($overall_discount)>1){
	//	$total_overall_discount = trim(($grand_tot*$overall_discount[0])/100);
	//}
	//else{
	//	$total_overall_discount = trim($overall_discount[0]);
	//}
	//$total_overall_discount = ($total_overall_discount=="")?0:$total_overall_discount;
	$grossTotal = $grand_tot-$total_overall_discount;	//Not to be printed
	//$tax_rate = ($sel_order_payment_details['tax_rate']=="")?0:$sel_order_payment_details['tax_rate'];
	//$tax_payable = ($grossTotal*$tax_rate)/100;
	$tax_payable = $sel_order_payment_details['tax_payable'];
	
	$tax_pt_paid = $sel_order_payment_details['tax_pt_paid'];
	$grand_ptpaid_1 += $grand_ptpaid + $tax_pt_paid;
	
	$grand_total = $grossTotal+$tax_payable;/*Grand Total*/
	$grand_ptResp = $grand_total - $grand_insamt - $grand_ptpaid;
	$grand_ptResp_1 = $grand_total - $grand_insamt - $grand_ptpaid_1;
	if($grand_ptResp<0){$grand_ptResp=0;}
	if($grand_ptResp_1<0){$grand_ptResp_1=0;}
	
	$sum_pt_paid += $tax_pt_paid;
	
	$pdfHTML.=
	'<tr>
		<td colspan="3" align="right" class="text_size"><strong>Sub Total :</strong></td>
		<td align="right" class="text_size"><strong>'.currency_symbol(true).number_format($grand_price,2).'</strong></td> 
		<td align="right" class="text_size"><strong>'.$tot_qty.'</strong></td> 
		<td align="right" class="text_size"><strong>'.currency_symbol(true).number_format($grand_allowed,2).'</strong></td>    
		<td align="right" class="text_size"><strong>'.currency_symbol(true).number_format($grand_insamt,2).'</strong></td>      
		<td align="right" class="text_size"><strong>'.currency_symbol(true).number_format((float)$grand_disc,2).'</strong></td>
		<td align="right" class="text_size"><strong>'.currency_symbol(true).number_format($grand_ptpaid,2).'</strong></td>
		<td align="right" class="text_size"><strong>'.currency_symbol(true).number_format($grand_ptresp-$grand_disc,2).'</strong></td>
	</tr>
	
	<tr>
		<td colspan="5" align="right" class="text_size"><strong>Total '.$facility_tax_label.' Payable :</strong></td>
		<td align="right" class="text_size"><strong>'.currency_symbol(true).number_format((float)$tax_payable,2).'</strong></td>
		<td colspan="1"></td>
		<td align="right" class="text_size"><strong>'.currency_symbol(true).number_format((float)$tax_pt_paid,2).'</strong></td>
		<td colspan="2"></td>
	</tr>
	<tr>
		<td colspan="5" align="right" class="text_size"><strong>Grand Total :</strong></td>
		<td align="right" class="text_size"><strong>'.currency_symbol(true).number_format((float)$grand_total+$total_overall_discount,2).'</strong></td>
		<td align="right" class="text_size"><strong>'.currency_symbol(true).number_format($grand_insamt,2).'</strong></td>
		<td align="right" class="text_size"><strong>'.currency_symbol(true).number_format((float)$total_overall_discount+$grand_disc,2).'</strong></td>
		<td align="right" class="text_size"><strong>'.currency_symbol(true).number_format($grand_ptpaid_1,2).'</strong></td>
		<td align="right" class="text_size"><strong>'.currency_symbol(true).number_format($grand_ptResp_1-$grand_disc,2).'</strong></td>
	</tr>
	<tr>
		<td colspan="3" align="right" class="text_size vtop"><strong>Comment :</strong></td>
		<td colspan="7" align="left" class="text_size vtop">'.splitLongString(stripslashes($sel_order_payment_details['comment']), 74, "<br />").'</td>
	</tr>
	<tr>
		<td colspan="3" align="right" class="text_size"><strong>Total Payment :</strong></td>
		<td align="right" class="text_size"><strong>'.currency_symbol(true).$sum_pt_paid.'</strong></td>
		<td colspan="6"></td> 
	</tr>
	<tr>
	';
	if($sum_pt_paid>"0"){
		$pdfHTML.='<td colspan="2" align="right" class="text_size"><strong>Method :</strong></td>
		 <td align="left" class="text_size" colspan="2">'.$sel_order_payment_details['payment_mode'].'</td>';
	}
	else{
		$pdfHTML.='<td colspan="2" align="right" class="text_size">&nbsp;</td>
		 <td align="left" class="text_size">&nbsp;</td>';
	}
	if($sel_order_payment_details['payment_mode']!="Cash" && $sel_order_payment_details['payment_mode']!="Credit Card"){
		$pdfHTML.='
			<td align="left" class="text_size" colspan="3"><strong>Check# </strong>'.$sel_order_payment_details['checkNo'].'</td>
		 ';
	}
	elseif($sel_order_payment_details['payment_mode']=="Cash"){
		$pdfHTML.='<td colspan="2" align="right" class="text_size">&nbsp;</td>
		 <td align="left" class="text_size">&nbsp;</td>';
	}
	elseif($sel_order_payment_details['payment_mode']=="Credit Card"){
		//$pdfHTML.= '<td align="left" class="text_size">&nbsp;</td>';
		$pdfHTML.= '<td class="text_size" colspan="2"><strong>Type:</strong>'.$creditCards[$sel_order_payment_details['creditCardCo']].'</td>';
		
		$pdfHTML.= '<td class="text_size" colspan="2"><strong>CC #:</strong>'.$sel_order_payment_details['creditCardNo'].'</td>';
		
		$pdfHTML.= '<td class="text_size" colspan="2"><strong>Exp. Date:</strong>'.$sel_order_payment_details['expirationDate'].' </td>';
		
	}
	
	/*charges comment*/
	/*$pdfHTML.=
	'<tr>
		<td colspan="5" align="right"><strong>Comment :</strong></td>
		<td colspan="7" align="left"><strong>'.$sel_order_payment_details['comment'].'</strong></td> 
		<td>&nbsp;</td>
		<td>&nbsp;</td>
	</tr>';*/
	
	
	/**/
	$pdfHTML.= '</tr></table>';
	
	/*Total Payment*/
		/*Comment /Signature spot*/
		if( isset($GLOBALS['LOCAL_SERVER']) && $GLOBALS['LOCAL_SERVER'] == 'DesertOpthalmology' ){
			$pdfHTML.= '<br /><strong><u>ALL SALES FINAL</u> -  <u>NO REFUNDS</u></strong><br /><br />';
			$pdfHTML.= 'I acknowledge I am making this purchase.<br /><br />';
			$pdfHTML.= 'Date:_________________&nbsp;&nbsp;&nbsp;&nbsp;Signature:_________________________<br /><br />';
			$pdfHTML.= 'I have received my order.<br />';
			$pdfHTML.= '(Spectacles)&nbsp;&nbsp;&nbsp;&nbsp;(Contacts)<br /><br />';
			$pdfHTML.= 'Date:_________________&nbsp;&nbsp;&nbsp;&nbsp;Signature:_________________________';
		}
		/*End Comment /Signature spot*/
	}
  
  $pdfHTML.='</page>';
  $pdfText = $css.$pdfHTML;
  
  file_put_contents('../../library/new_html2pdf/print_pos_'.$_SESSION['authId'].'.html',$pdfText);
?>

<script>

var url = '<?php echo $GLOBALS['WEB_PATH'];?>/library/new_html2pdf/createPdf.php?op=p&file_name=../../library/new_html2pdf/print_pos_<?php echo $_SESSION['authId'];?>';
window.location.href = url;

</script>