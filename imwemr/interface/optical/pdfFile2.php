<?php
require_once("../../config/globals.php");
require_once("../../library/patient_must_loaded.php");
require_once('../../library/classes/common_function.php');
require_once('../../library/classes/SaveFile.php');
require_once('../../library/classes/work_view/wv_functions.php');

$patient_id = $_SESSION['patient'];
$qry = "select * from patient_data where id = '$patient_id'";
$patientDetails = imw_query($qry);
$qryRow = imw_fetch_array($patientDetails);

$patient_name = $qryRow['lname'].', '.$qryRow['fname'].' '.$qryRow['mname'];
$patient_name = trim($patient_name).' - '.$qryRow['id'];;
$patient_address = $qryRow['street'].' '.$qryRow['street2'].','.$qryRow['city'].','.$qryRow['state'].' '.$qryRow['postal_code'];
$phone = explode('-',$qryRow['phone_home']);
$phone_home = '('.$phone[0].') '.$phone[1].'-'.$phone[2];
$providerID = $qryRow['providerID'];
$qry = "select concat(lname,', ',fname) as name, mname,id from users where id = '$providerID'";
$phyDetails = imw_query($qry);
$phyQryRes = imw_fetch_assoc($phyDetails);

$physicianName = $phyQryRes['name'].' '.$phyQryRes['mname'];
extract($_REQUEST);
$reorder = $reorder == 1 ? 'Yes' : 'No';
//$prism_od = $prism_od == 1 ? 'Yes' : 'No';
$HT_lens = $HT_lens == 1 ? 'Yes' : 'No';
$ar_charge = $ar_charge != '' ? 'Yes' : 'No';
$Polaroid_material = $Polaroid_material != '' ? 'Yes' : 'No';
$slad_off = $slad_off != '' ? 'Yes' : 'No';
$Photochromatic = $Photochromatic != '' ? 'Yes' : 'No';

$prismColWidth="25";
$prism ='<table border="0" cellpadding="1" cellspacing="1">
<tr><td style="width:'.$prismColWidth.'px;">P</td><td style="width:'.$prismColWidth.';">'.$elem_visMrOdP.'</td><td style="width:'.$prismColWidth.';">'.$elem_visMrOdPrism.'</td><td style="width:'.$prismColWidth.';">'.$elem_visMrOdSlash.'</td><td style="width:'.$prismColWidth.';">'.$elem_visMrOdSel1.'</td></tr>
<tr><td>P</td><td>'.$elem_visMrOsP.'</td><td>'.$elem_visMrOsPrism.'</td><td>'.$elem_visMrOsSlash.'</td><td>'.$elem_visMrOsSel1.'</td></tr>
</table>
';

//Get Group Name
$sql="SELECT name FROM groups_new WHERE del_status='0' ORDER BY gro_id LIMIT 0,1";
$row= sqlQuery($sql);
if($row!=false){
	$nameGrp = $row["name"];
}else{
	$nameGrp = "";
}

// ORDER DTAILS
$qry = "select * from optical_order_form where Optical_Order_Form_id = '".$_REQUEST['order_id']."'";
$orderQryRs = imw_query($qry);
$orderQryRes=imw_fetch_array($orderQryRs);
extract($orderQryRes);

$frame_scr = ($frame_scr=='') ? $frame_scr='No' : $frame_scr;

$table = '
<style>
.text_15b{ 
	font-family:Arial, Helvetica, sans-serif; 
	font-size:15px; 
	color:#333333; 
	font-weight:bold;  
}
.text_12{ 
	font-family:Arial, Helvetica, sans-serif; 
	font-size:12px; 
	color:#333333;  
}
.text{ 
	font-family:Arial, Helvetica, sans-serif; 
	font-size:11px; 
	color:#FFFFFF;  
	font-weight:bold;  
}
</style>
<page backtop="0mm" backbottom="5mm">
<table width="750" border="0" align="center" cellpadding="1" cellspacing="1" style="border:1px solid #4684ab;">
	<tr>
		<td width="750" align="center" colspan="6" class="text_15b">
			'.$nameGrp.'			
		</td>
	</tr>
	<tr>
		<td bgcolor="#4684ab" align="center" class="text">Patient Name</td>
		<td bgcolor="#4684ab" align="center" class="text">Demographics</td>
		<td bgcolor="#4684ab" align="center" class="text">Physician Name</td>
		<td bgcolor="#4684ab" align="center" class="text">Date</td>
		<td bgcolor="#4684ab" align="center" class="text">Ref#</td>
		<td bgcolor="#4684ab" align="center" class="text">Reorder</td>
	</tr>
	<tr height="20">
		<td valign="top" class="text_12" align="center" nowrap="nowrap">'.$patient_name.'</td>
		<td align="left" class="text_12" valign="top" nowrap="nowrap">'.$patient_address.'&nbsp;'.$phone_home.'</td>
		<td valign="top" class="text_12" align="center" nowrap="nowrap">'.$physicianName.'</td>	
		<td valign="top" class="text_12" align="center">'.date('m-d-Y').'</td>
		<td valign="top" class="text_12" align="center">'.$ref.'</td>
		<td align="center" class="text_12" valign="top">'.$reorder.'</td>
	</tr>	
</table>
<table width="750" border="0" align="center" cellpadding="0" cellspacing="0" style="border:1px solid #4684ab;">
	<tr height="20">
		<td bgcolor="#4684ab" align="center" width="72" class="text">Vision</td>
		<td bgcolor="#4684ab" align="center" width="72" class="text">Sphere</td>
		<td bgcolor="#4684ab" align="center" width="72" class="text">Cyl</td>
		<td bgcolor="#4684ab" align="center" width="72" class="text">Axis</td>
		<td bgcolor="#4684ab" align="center" width="30" class="text">Add</td>
		<td bgcolor="#4684ab" align="center" width="156" class="text">Prism</td>
		<td bgcolor="#4684ab" align="center" width="30" class="text">HT</td>
		<td bgcolor="#4684ab" align="center" width="71" class="text">DIST PD</td>
		<td bgcolor="#4684ab" align="center" width="70" class="text">Near PD</td>
		<td bgcolor="#4684ab" align="center" width="70" class="text">Base</td>
	</tr>
	<tr>
		<td align="center" class="text_12">OD</td>
		<td align="center" class="text_12">'.$sphere_od.'</td>
		<td align="center" class="text_12">'.$cyl_od.'</td>
		<td align="center" class="text_12">'.$axis_od.'</td>
		<td align="center" class="text_12">'.$add_od.'</td>
		<td align="center" class="text_12" rowspan="2">'.$prism.'</td>
		<td align="center" class="text_12">'.$optic_ht.'</td>
		<td align="center" class="text_12">'.$dist_pd_od.'</td>
		<td align="center" class="text_12">'.$near_pd_od.'</td>
		<td align="center" class="text_12">'.$base_od.'</td>
	</tr>
	<tr>
		<td align="center" class="text_12">OS</td>
		<td align="center" class="text_12">'.$sphere_os.'</td>
		<td align="center" class="text_12">'.$cyl_os.'</td>
		<td align="center" class="text_12">'.$axis_os.'</td>
		<td align="center" class="text_12">'.$add_os.'</td>
		<td align="center" class="text_12">'.$optic_ht_os.'</td>
		<td align="center" class="text_12">'.$dist_pd_os.'</td>
		<td align="center" class="text_12">'.$near_pd_os.'</td>
		<td align="center" class="text_12">'.$base_os.'</td>
	</tr>
</table>
<table width="750" cellpadding="1" cellspacing="1" style="border:1px solid #4684ab;">
	<tr>
		<td bgcolor="#4684ab" width="56" align="center" class="text">Manufacturer</td>
		<td bgcolor="#4684ab" width="56" align="center" class="text">Make</td>
		<td bgcolor="#4684ab" width="56" align="center" class="text">Style</td>
		<td bgcolor="#4684ab" width="56" align="center" class="text">Color</td>
		<td bgcolor="#4684ab" width="57" align="center" class="text">Eye</td>
		<td bgcolor="#4684ab" width="57" align="center" class="text">Bridge</td>
		<td bgcolor="#4684ab" width="57" align="center" class="text">A</td>
		<td bgcolor="#4684ab" width="57" align="center" class="text">B</td>
		<td bgcolor="#4684ab" width="57" align="center" class="text">ED</td>
		<td bgcolor="#4684ab" width="57" align="center" class="text">Templ</td>
		<td bgcolor="#4684ab" width="58" align="center" class="text">SCR</td>
		<td bgcolor="#4684ab" width="58" align="center" class="text">UV</td>
	</tr>
	<tr>
		<td class="text_12" align="center">'.$vendor_name.'</td>
		<td class="text_12" align="center">'.$frame_name.'</td>
		<td class="text_12" align="center">'.$frame_style.'</td>
		<td class="text_12" align="center">'.$frame_color.'</td>
		<td class="text_12" align="center">'.$frame_eye.'</td>
		<td class="text_12" align="center">'.$frame_bridge.'</td>
		<td class="text_12" align="center">'.$frame_a.'</td>
		<td class="text_12" align="center">'.$frame_b.'</td>
		<td class="text_12" align="center">'.$frame_ed.'</td>
		<td class="text_12" align="center">'.$temple.'</td>
		<td class="text_12" align="center">'.$frame_scr.'</td>
		<td class="text_12" align="center">'.$frame_uv.'</td>
	</tr>
</table>
<table width="100%" cellpadding="0" cellspacing="0" border="0" style="border:1px solid #4684ab;">
	<tr bgcolor="#4684ab">
		<td width="90" bgcolor="#4684ab" class="text" align="center">Lens Type</td>
		<td width="90" bgcolor="#4684ab" class="text" align="center">&nbsp;</td>
		<td width="90" bgcolor="#4684ab" class="text" align="center">Lens Material</td>
		<td width="78" bgcolor="#4684ab" class="text" align="center">TINT</td>
		<td width="71" bgcolor="#4684ab" class="text" align="center">HT</td>
		<td width="72" bgcolor="#4684ab" class="text" align="center">AR</td>
		<td width="73" bgcolor="#4684ab" class="text" align="center">Polaroid</td>
		<td width="73" bgcolor="#4684ab" class="text" align="center">Slad-Off</td>
		<td width="72" bgcolor="#4684ab" class="text" align="center">Photochromatic</td>
	</tr>
	<tr>
		<td align="center" class="text_12">'.$lens_opt.'</td>
		<td align="center" class="text_12">'.$bifocal_opt.'</td>
		<td align="center" class="text_12">'.$lens_material.'</td>		
		<td align="center" class="text_12">'.$tini_opt.'</td>
		<td align="center" class="text_12">'.$HT_lens.'</td>
		<td align="center" class="text_12">'.$ar_charge.'</td>
		<td align="center" class="text_12">'.$Polaroid_material.'</td>
		<td align="center" class="text_12">'.$slad_off.'</td>
		<td align="center" class="text_12">'.$Photochromatic.'</td>
	</tr>
</table>
<table width="100%" border="0"  style="border:1px solid #4684ab;">
	<tr>
		<td bgcolor="#4684ab" colspan="2" class="text">Cost</td>
		<td bgcolor="#4684ab" colspan="4" class="text" >&nbsp;Special Instruction</td>
	</tr>
	<tr>
		<td class="text_12" bgcolor="#FFFFFF" width="56">Frames</td>
		<td class="text_12" align="right" bgcolor="#FFFFFF" width="110">
			'.$txtframePrice.'&nbsp;'.$frame_cost.'
		</td>
		<td class="text_12" width="160">Date Frame Ordered:</td>										
		<td class="text_12" width="128" align="right">'.$Notification_comments.'</td>
		<td width="50" align="right" class="text_12">Ref. :</td>										
		<td width="175" align="left" class="text_12">'.$ref_frame_order.'</td>
	</tr>
	<tr>
		<td class="text_12" bgcolor="#FFFFFF">Lenses</td>
		<td class="text_12" align="right" bgcolor="#FFFFFF">'.$lenese_cost.'</td>
		<td class="text_12">Date Lens Ordered:</td>
		<td class="text_12" align="right">'.$lens_order.'</td>
		<td class="text_12" align="right">Ref. :</td>
		<td class="text_12">'.$ref_lens_order.'</td>
	</tr>
	<tr>	
		<td class="text_12" bgcolor="#FFFFFF">Prism</td>
		<td class="text_12" align="right" bgcolor="#FFFFFF">'.$prism_cost.'</td>
		<td class="text_12" nowrap="nowrap">Date Frame Recieved:</td>
		<td class="text_12" align="right">'.$frame_recieve.'</td>
		<td class="text_12" align="right">Ref. :</td>
		<td class="text_12">'.$ref_frame_recieve.'</td>
	</tr>
	<tr>	
		<td class="text_12" bgcolor="#FFFFFF">Polarized</td>
		<td class="text_12" align="right" bgcolor="#FFFFFF">'.$polar_cost.'</td>
		<td class="text_12" nowrap="nowrap">Date Lens Recieved:</td>
		<td class="text_12" align="right">'.$lens_recieve.'</td>
		<td class="text_12" align="right">Ref. :</td>
		<td class="text_12">'.$ref_lens_recieve.'</td>
	</tr>
	<tr>	
		<td class="text_12" bgcolor="#FFFFFF">Transition</td>
		<td class="text_12" align="right" bgcolor="#FFFFFF">'.$trans_cost.'</td>
		<td class="text_12" nowrap="nowrap">Date Pt. Notified:</td>
		<td class="text_12" align="right">'.$patient_notify.'</td>
		<td class="text_12" align="right">Ref. :</td>
		<td class="text_12">'.$ref_pt_notify.'</td>
	</tr>
	<tr>	
		<td class="text_12" bgcolor="#FFFFFF">SCR</td>
		<td class="text_12" align="right" bgcolor="#FFFFFF">'.$scr_cost.'</td>
		<td class="text_12" nowrap="nowrap">Date dispensed:</td>
		<td class="text_12" align="right">'.$patient_picked_up.'</td>
		<td class="text_12" align="right">Ref. :</td>
		<td class="text_12">'.$ref_pt_picked.'</td>
	</tr>
	<tr>	
		<td class="text_12" bgcolor="#FFFFFF">AR</td>
		<td class="text_12" align="right" bgcolor="#FFFFFF">'.$ar_cost.'</td>
		<td class="text_12" nowrap="nowrap">Date of Sale:</td>
		<td class="text_12" align="right">'.$sale_date.'</td>
		<td class="text_12" align="right">Ref. :</td>
		<td class="text_12">'.$ref_date_sale.'</td>
	</tr>
	<tr>	
		<td class="text_12" bgcolor="#FFFFFF">Slad-Off</td>
		<td class="text_12" align="right" bgcolor="#FFFFFF">'.$Slad_Off_cost.'</td>
		<td class="text_12" nowrap="nowrap"></td>
		<td class="text_12" align="right"></td>
		<td class="text_12" align="right"></td>
		<td class="text_12"></td>
	</tr>
	<tr>	
		<td class="text_12" bgcolor="#FFFFFF">Miscellaneous</td>
		<td class="text_12" align="right" bgcolor="#FFFFFF">
			'.$tint_cost_price.'&nbsp;'.$hi_cost_price.'
		</td>
		<td class="text_12">Promotions :</td>
		<td class="text_12" align="right">'.$promotions.'</td>
	</tr>
	<tr>	
		<td class="text_12" bgcolor="#FFFFFF">Discount(Frames)</td>
		<td class="text_12" align="right" bgcolor="#FFFFFF">'.$discount_frames.'</td>
		<td class="text_12" nowrap="nowrap">Paid By :</td>
		<td class="text_12" align="right">'.$paid_by.'</td>
		<td class="text_12" align="right">Method :</td>
		<td class="text_12" align="left">'.$payment_method.'</td>
	</tr>
	<tr>	
		<td class="text_12" bgcolor="#FFFFFF">Discount (Lens)</td>
		<td class="text_12" align="right" bgcolor="#FFFFFF">'.$discount.'</td>
		<td class="text_12" valign="top">Comments :</td>
		<td class="text_12" align="right" width="175">'.$comments.'</td>
	</tr>
	<tr>	
		<td class="text_12" bgcolor="#FFFFFF">Total</td>
		<td class="text_12" align="right" bgcolor="#FFFFFF">'.$total.'</td>
	</tr>
	<tr>	
		<td class="text_12" bgcolor="#FFFFFF">Deposit</td>
		<td class="text_12" align="right" bgcolor="#FFFFFF">'.$deposit.'</td>
	</tr>
	<tr>	
		<td class="text_12" bgcolor="#FFFFFF">Balance</td>
		<td class="text_12" align="right" bgcolor="#FFFFFF">'.$balance.'</td>
	</tr>
</table>
</page>';

$file_path = write_html($table);
?>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/js/jquery.min.1.12.4.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/js/common.js"></script>
<script type="text/javascript">
	top.JS_WEB_ROOT_PATH = '<?php echo $GLOBALS['webroot']; ?>';
	top.html_to_pdf('<?php echo $file_path; ?>','p',);
	window.close();
</script>