<?php 
require_once("../../config/globals.php");
require_once("../../library/patient_must_loaded.php");
require_once('../../library/classes/common_function.php');

require_once($GLOBALS['srcdir'].'/classes/cls_common_function.php');
$cls_object = New CLSCommonFunction;

require_once('../../library/classes/optical_class.php');

$pid = $_SESSION['patient'];
$auth_id = $_SESSION['authId'];
$optical_obj = New Optical($pid,$auth_id);





$patient_id = $optical_obj->patient_id;
$discount_app = $optical_obj->noBalBill($patient_id);


$vendName = $_GET['vendor'];
$frame = $_GET['frame'];
$style = $_GET['style'];
$frame_style = $_GET['frame_style'];
$color = $_GET['color'];
$frame_color = $_GET['frame_color'];
$style1 = $_GET['style1'];
$lens = $_GET['lens'];
$lens_type = $_GET['lens_type'];
$material = $_GET['material'];
$tint = $_GET['tint'];
$polarized = $_GET['polarized'];
$ar_charge = $_GET['ar_charge'];
$scr_cost = $_GET['scr_cost'];
$patientId = $_GET['patientId'];

if($patientId != '')
{
	getReOrderVal($patientId);
}
 
if($vendName != '' && $style == '' && !$frame_style && $color == '' && $frame_color == '' && $style1 == '')
{
	getMakeFrame($vendName,$frame);
}
else if($style != '' && $vendName != '' && $frame != '' && $color == '')
{
	getFrameStyle($vendName,$frame,$style);
}
else if($frame_style != '')
{
	getFrameColor($vendName,$frame,$frame_style);
}
else if($color != '' && $vendName != '' && $frame != '' && $style != '')
{
	setFrameColor($vendName,$frame,$style,$color);
}
else if($vendName != '' && $frame != '' && $style1 != '')
{
	getFrameCost($vendName,$frame,$style1);
}
else if($lens != '' && $lens_type != '' && $material != '' && $tint == '' && $polarized == '' && $ar_charge == '' && $scr_cost == '')
{
	getLensCost($lens,$lens_type,$material);
	//getLensDiscount($lens,$lens_type,$material);
}
else if($lens != '' && $lens_type != '' && $material != '' && $tint != '')
{
	getLensTintCost($lens,$lens_type,$material,$tint);
}
else if($lens != '' && $lens_type != '' && $material != '' && $polarized != '')
{
	getLensTintCost($lens,$lens_type,$material,$polarized);
}
else if($lens != '' && $lens_type != '' && $material != '' && $ar_charge != '')
{
	getLensTintCost($lens,$lens_type,$material,$ar_charge);
}
else if($lens != '' && $lens_type != '' && $material != '' && $scr_cost != '')
{
	getLensTintCost($lens,$lens_type,$material,$scr_cost);
}


//function for getting name make of frame 
function getMakeFrame($vendName,$frame)
{
	if($frame != '')
	{
		$qry = "SELECT distinct make_frame from optical_frames where vendor_name = '$vendName' and make_frame like '$frame%'";
	}
	else
	{
		$qry = "SELECT distinct make_frame from optical_frames where vendor_name = '$vendName'";
	}
	
	$res = imw_query($qry);
	
	while($row = imw_fetch_array($res))
	{
		echo $row['make_frame'].'*n';
	}
}

function getFrameStyle($vendName,$frame,$style)
{
$qry = "SELECT distinct frame_style from optical_frames where vendor_name = '$vendName' and make_frame = '$frame' and frame_style like '$style%'";
	
	$res = imw_query($qry);
	
	while($row = imw_fetch_array($res))
	{
		echo $row['frame_style'].'*n';
	}
}

function getFrameColor($vendName,$frame,$style)
{
	$qry = "SELECT distinct frame_color from optical_frames where vendor_name = '$vendName' and make_frame = '$frame' and frame_style = '$style'";
	$res = imw_query($qry);
	$row = imw_fetch_array($res);
	
	if(imw_num_rows($res) == 1)
	{
		echo $row['frame_color'];
	}
	else
	{
		return false;
	}
}

function setFrameColor($vendName,$frame,$style,$color)
{
	$qry = "SELECT distinct frame_color from optical_frames where vendor_name = '$vendName' and make_frame = '$frame' and frame_style = '$style' and frame_color like '$color%'";
	$res = imw_query($qry);
	
	while($row = imw_fetch_array($res))
	{
		echo $row['frame_color'].'*n';
	}
}


function getFrameCost($vendName,$frame,$style)
{
	$qry = "SELECT retail_price,horizontal,vertical,diagonal,bridge,diagonal,frame_color from optical_frames where vendor_name = '$vendName' and make_frame = '$frame' and frame_style = '$style'";
	$res = imw_query($qry);
	$row = imw_fetch_array($res);
	
	header('Content-Type: text/xml');
	echo '<?xml version="1.0" encoding="ISO-8859-1"?>
	<frameCost>';
	echo "<retailPrice>" . $row['retail_price'] . "</retailPrice>";
	echo "<color>" . $row['frame_color'] . "</color>";
	echo "<discount>" . getFrameDiscount($vendName,$frame,$style) . "</discount>";
	echo "<horizontal>".$row['horizontal']."</horizontal>";
	echo "<vertical>".$row['vertical']."</vertical>";
	echo "<bridge>".$row['bridge']."</bridge>";
	echo "<diagonal>".$row['diagonal']."</diagonal>";
	echo "</frameCost>";
}


function getLensCost($lens,$lens_type,$material)
{
	
	$qry = "SELECT Retail_Price,Transitions_price,polarized_price from optical_lenses where Tab_val = '$lens' and lens_type = '$lens_type' and lens_material = '$material'";
	$res = imw_query($qry);
	$row = imw_fetch_array($res);
	
		
	//return $row['Retail_Price'];
	header('Content-Type: text/xml');
	echo '<?xml version="1.0" encoding="ISO-8859-1"?>
	<lensCost>';
	echo "<retailPrice>" . $row['Retail_Price'] . "</retailPrice>";
	echo "<transPrice>" . $row['Transitions_price'] . "</transPrice>";
	echo "<polarPrice>" . $row['polarized_price'] . "</polarPrice>";
	echo "<discount>" . getLensDiscount($lens,$lens_type,$material) . "</discount>";
	echo "</lensCost>";
}

function getLensTintCost($lens,$lens_type,$material,$val)
{
	$field ='';
	if($val == 'Solid')
	{
		$field = 'TINT_Gradient';
	}
	else if($val == 'Gradient')
	{
		$field = 'TINT_Solid';
	}
	else if($val == 'Gray' || $val == 'Brown' || $val == 'G15')
	{
		$field = 'polarized_price';
	}
	else if($val == 'ar_val')
	{
		$field = 'AR_price';
	}
	else if($val == 'scr')
	{
		$field = 'SCR';
	}
	
	$qry = "SELECT $field from optical_lenses where Tab_val = '$lens' and lens_type = '$lens_type' and lens_material = '$material'";
	$res = imw_query($qry);
	$row = imw_fetch_array($res);
	
	echo $row[$field];
}

//function for getting lens discount
function getLensDiscount($lens,$lens_type,$material)
{
	global $discount_app;
	$qry = "SELECT Retail_Price,Family_Friend_Discount,Patient_Discount,patient_discount_actual,family_discount_actual from 
			optical_lenses where Tab_val = '$lens' and lens_type = '$lens_type' and lens_material = '$material'";
	$res = imw_query($qry);
	$row = imw_fetch_array($res);
	
	if(($discount_app == 0  && $row['Patient_Discount'] != 0) || ($discount_app == 1 && $row['Family_Friend_Discount'] == 0 && $row['family_discount_actual'] == 0))
	{
		return $row['Patient_Discount']."p";	
	}
	else if(($discount_app == 0  && $row['Patient_Discount'] == 0) || ($discount_app == 1 && $row['Family_Friend_Discount'] == 0 && $row['family_discount_actual'] == 0))
	{
		return $row['patient_discount_actual']."a";	
	}
	else if($discount_app == 1 && $row['Family_Friend_Discount'] != 0)
	{
		return $row['Family_Friend_Discount']."p";	
	}
	else if($discount_app == 1 && $row['Family_Friend_Discount'] == 0)
	{
		return $row['family_discount_actual']."a";	
	}
}

//function for getting frame discount
function getFrameDiscount($vendName,$frame,$style)
{
	global $discount_app;
	$qry = "SELECT discount_family_friend,discount_patient,patient_discount_actual,family_discount_actual
			FROM optical_frames WHERE vendor_name = '$vendName' and make_frame = '$frame'
			and frame_style = '$style' and frame_status = '0'";
	$res = imw_query($qry);
	$row = imw_fetch_array($res);	
	
	if(($discount_app == 0  && $row['discount_patient'] != 0) || ($discount_app == 1 && $row['discount_family_friend'] == 0 && $row['family_discount_actual'] == 0))
	{
		return $row['discount_patient']."p";
	}
	else if(($discount_app == 0  && $row['discount_patient'] == 0) || ($discount_app == 1 && $row['discount_family_friend'] == 0 && $row['family_discount_actual'] == 0))
	{
		return $row['patient_discount_actual']."a";	
	}
	else if($discount_app == 1 && $row['discount_family_friend'] != 0)
	{
		return $row['discount_family_friend']."p";	
	}
	else if($discount_app == 1 && $row['discount_family_friend'] == 0)
	{
		return $row['family_discount_actual']."a";	
	}	
}

//function for setting reorder values
function getReOrderVal($patientId)
{
	$qry_reorder = "select * from optical_order_form where patient_id = '$patientId' and order_status = '0' order by Optical_Order_Form_id desc limit 1";

	$res_reorder = imw_query($qry_reorder);
	$row_reorder = imw_fetch_array($res_reorder);

	header('Content-Type: text/xml');
	echo '<?xml version="1.0" encoding="ISO-8859-1"?>
	<reOrder>';
	echo "<sphere_od>".$row_reorder['sphere_od']."</sphere_od>";
	echo "<cyl_od>".$row_reorder['cyl_od']."</cyl_od>";
	echo "<axis_od>".$row_reorder['axis_od']."</axis_od>";
	echo "<add_od>".$row_reorder['cyl_od']."</add_od>";
	echo "<elem_visMrOdP>".$row_reorder['mr_od_p']."</elem_visMrOdP>";
	echo "<elem_visMrOdPrism>".$row_reorder['mr_od_prism']."</elem_visMrOdPrism>";
	echo "<elem_visMrOdSlash>".$row_reorder['mr_od_splash']."</elem_visMrOdSlash>";
	echo "<elem_visMrOdSel1>".$row_reorder['mr_od_sel']."</elem_visMrOdSel1>";
	echo "<optic_ht>".$row_reorder['optic_ht']."</optic_ht>";
	echo "<dist_pd_od>".$row_reorder['dist_pd_od']."</dist_pd_od>";
	echo "<near_pd_od>".$row_reorder['near_pd_od']."</near_pd_od>";
	echo "<base_od>".$row_reorder['base_od']."</base_od>";
	echo "<sphere_os>".$row_reorder['sphere_os']."</sphere_os>";
	echo "<cyl_os>".$row_reorder['cyl_os']."</cyl_os>";
	echo "<axis_os>".$row_reorder['axis_os']."</axis_os>";
	echo "<add_os>".$row_reorder['add_os']."</add_os>";
	echo "<elem_visMrOsP>".$row_reorder['mr_os_p']."</elem_visMrOsP>";
	echo "<elem_visMrOsPrism>".$row_reorder['mr_os_prism']."</elem_visMrOsPrism>";
	echo "<elem_visMrOsSlash>".$row_reorder['mr_os_splash']."</elem_visMrOsSlash>";
	echo "<elem_visMrOsSel1>".$row_reorder['mr_os_sel']."</elem_visMrOsSel1>";
	echo "<optic_ht_os>".$row_reorder['optic_ht_os']."</optic_ht_os>";
	echo "<dist_pd_os>".$row_reorder['dist_pd_os']."</dist_pd_os>";
	echo "<near_pd_os>".$row_reorder['near_pd_os']."</near_pd_os>";
	echo "<base_os>".$row_reorder['base_os']."</base_os>";
	echo "<vendor_name>".$row_reorder['vendor_name']."</vendor_name>";
	echo "<frame_name>".$row_reorder['frame_name']."</frame_name>";
	echo "<frame_style>".$row_reorder['frame_style']."</frame_style>";
	echo "<frame_color>".$row_reorder['frame_color']."</frame_color>";
	echo "<frame_eye>".$row_reorder['frame_eye']."</frame_eye>";
	echo "<frame_bridge>".$row_reorder['frame_bridge']."</frame_bridge>";
	echo "<frame_a>".$row_reorder['frame_a']."</frame_a>";
	echo "<frame_b>".$row_reorder['frame_b']."</frame_b>";
	echo "<frame_ed>".$row_reorder['frame_ed']."</frame_ed>";
	echo "<temple>".$row_reorder['temple']."</temple>";
	echo "<frame_scr>".$row_reorder['frame_scr']."</frame_scr>";
	echo "<frame_uv>".$row_reorder['frame_uv']."</frame_uv>";
	echo "<lens_opt>".$row_reorder['lens_opt']."</lens_opt>";
	echo "<bifocal_opt>".$row_reorder['bifocal_opt']."</bifocal_opt>";
	echo "<lens_material>".$row_reorder['lens_material']."</lens_material>";
	echo "<tini_opt>".$row_reorder['tini_opt']."</tini_opt>";
	echo "<HT_lens>".$row_reorder['HT_lens']."</HT_lens>";
	echo "<ar_charge>".$row_reorder['ar_charge']."</ar_charge>";
	echo "<ar_desc>".$row_reorder['ar_desc']."</ar_desc>";
	echo "<frame_cost>".$row_reorder['frame_cost']."</frame_cost>";
	echo "<Notification_comments>".$row_reorder['Notification_comments']."</Notification_comments>";
	echo "<ref_frame_order>".$row_reorder['ref_frame_order']."</ref_frame_order>";
	echo "<lenese_cost>".$row_reorder['lenese_cost']."</lenese_cost>";
	echo "<lens_order>".$row_reorder['lens_order']."</lens_order>";
	echo "<ref_lens_order>".$row_reorder['ref_lens_order']."</ref_lens_order>";
	echo "<tint_cost>".$row_reorder['tint_cost']."</tint_cost>";
	echo "<frame_recieve>".$row_reorder['frame_recieve']."</frame_recieve>";
	echo "<ref_frame_recieve>".$row_reorder['ref_frame_recieve']."</ref_frame_recieve>";
	echo "<polar_cost>".$row_reorder['polar_cost']."</polar_cost>";
	echo "<lens_recieve>".$row_reorder['lens_recieve']."</lens_recieve>";
	echo "<ref_lens_recieve>".$row_reorder['ref_lens_recieve']."</ref_lens_recieve>";
	echo "<trans_cost>".$row_reorder['trans_cost']."</trans_cost>";
	echo "<patient_notify>".$row_reorder['patient_notify']."</patient_notify>";
	echo "<ref_pt_notify>".$row_reorder['ref_pt_notify']."</ref_pt_notify>";
	echo "<scr_cost>".$row_reorder['scr_cost']."</scr_cost>";
	echo "<patient_picked_up>".$row_reorder['patient_picked_up']."</patient_picked_up>";
	echo "<ref_pt_picked>".$row_reorder['ref_pt_picked']."</ref_pt_picked>";
	echo "<ar_cost>".$row_reorder['ar_cost']."</ar_cost>";
	echo "<sale_date>".$row_reorder['sale_date']."</sale_date>";
	echo "<ref_date_sale>".$row_reorder['ref_date_sale']."</ref_date_sale>";
	echo "<other_cost>".$row_reorder['other_cost']."</other_cost>";
	echo "<promotions>".$row_reorder['promotions']."</promotions>";
	echo "<discount_frames>".$row_reorder['discount_frames']."</discount_frames>";
	echo "<paid_by>".$row_reorder['paid_by']."</paid_by>";
	echo "<payment_method>".$row_reorder['payment_method']."</payment_method>";
	echo "<discount>".$row_reorder['discount']."</discount>";
	echo "<comments>".$row_reorder['comments']."</comments>";
	echo "<total>".$row_reorder['total']."</total>";
	echo "<deposit>".$row_reorder['deposit']."</deposit>";
	echo "<balance>".$row_reorder['balance']."</balance>";
	echo "<frame_dis_ap>".$row_reorder['frame_dis_ap']."</frame_dis_ap>";
	echo "</reOrder>";
}


?>