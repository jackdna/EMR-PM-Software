<?php 
/*
File: hx_lens_prescription.php
Coded in PHP7
Purpose: Hx Lens Prescriptions
Access Type: Direct access
*/
require_once("../../config/config.php");
require_once("../../library/classes/functions.php"); 

if($_SESSION['patient_session_id']!=""){
	$p_name_qry = imw_query("select lname,fname,mname from patient_data where id = '".$_SESSION['patient_session_id']."' ");
	$p_name_row = imw_fetch_assoc($p_name_qry);
	$patient_name_id = $p_name_row['lname'].", ".$p_name_row['fname']." ".$p_name_row['mname']." - ".$_SESSION['patient_session_id'];
}

?>


<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Optical</title>
<link rel="stylesheet" href="../../library/css/inv_css.css?<?php echo constant("cache_version"); ?>" />
<script src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/jquery-1.10.1.min.js?<?php echo constant("cache_version"); ?>"></script>
<script>
$(document).unbind('keydown').bind('keydown', function (event) {
    var doPrevent = false;
    if (event.keyCode === 8) {
        var d = event.srcElement || event.target;
        if ((d.tagName.toUpperCase() === 'INPUT' && (d.type.toUpperCase() === 'TEXT' || d.type.toUpperCase() === 'PASSWORD' || d.type.toUpperCase() === 'FILE')) 
             || d.tagName.toUpperCase() === 'TEXTAREA') {
            doPrevent = d.readOnly || d.disabled;
        }
        else {
            doPrevent = true;
        }
    }

    if (doPrevent) {
        event.preventDefault();
    }
});
</script>
</head>
<body>
	<div style="padding:0px; margin:0 auto; width:800px;">
        <div class="listheading">
            <div class="fl">Hx Lens Prescriptions</div>
            <div class="fl" style="width:auto; color:#03F; text-align:left; margin-left:140px;"><?php echo $patient_name_id;?></div>		
        </div>
<?php
$arrRxDetails=array();
$qry="Select op_ord.*, ord.item_name FROM in_optical_order_form as op_ord inner join in_order_details as ord on op_ord.det_order_id=ord.id WHERE op_ord.patient_id='".$_SESSION['patient_session_id']."' ORDER BY op_ord.id DESC";  
$rs=imw_query($qry);
while($row = imw_fetch_array($rs))
{
	$arrRxDetails[] = $row;
}
?>
<table class="table_collapse" style="width:99.9.5%">
    <tr class="listheading">
      <td  style="width:80px">Item Name</td>
      <td  style="width:50px">Vision</td>
      <td  style="width:50px">Sphere</td>
      <td  style="width:60px">Cylinder</td>
      <td  style="width:50px">Axis</td>
      <td  style="width:50px;">Add</td>
	   <td  style="width:50px;">Prism</td>
	   <td  style="width:50px;">Base</td>
	   <td  style="width:50px;">Seg</td>
	   <td  style="width:50px;">NPD</td>
	   <td  style="width:50px;">DPD</td>
    </tr>
</table> 
<?php 
	if(count($arrRxDetails)>0)
	{
?>
<div style="height:220px; overflow-y:scroll;">
	<?php
		foreach($arrRxDetails as $RxDetails)
		{
		if($RxDetails['mr_od_splash']!="" && $RxDetails['mr_od_sel']!="")
		{
			$od_prism = $RxDetails['mr_od_p']." ".$RxDetails['mr_od_prism']." / ".$RxDetails['mr_od_splash']." ".$RxDetails['mr_od_sel'];
		}
		else
		{
			$od_prism = $RxDetails['mr_od_p']." ".$RxDetails['mr_od_prism'];
		}
		
		if($RxDetails['mr_os_splash']!="" && $RxDetails['mr_os_sel']!="")
		{
			$os_prism = $RxDetails['mr_os_p']." ".$RxDetails['mr_os_prism']." / ".$RxDetails['mr_os_splash']." ".$RxDetails['mr_os_sel']; 
		}
		else
		{
			$os_prism = $RxDetails['mr_os_p']." ".$RxDetails['mr_os_prism'];
		}  
		if($rowbg=='even'){$rowbg="odd";}else{$rowbg="even";}
	?>

<table class="table_collapse text14" style="width:99.8%">   
    <tr class="<?php echo $rowbg;?> cellBorder" style="height:22px;">
      <td rowspan="2" class="alignCenter" style="width:80px"><?php echo $RxDetails['item_name'];?></td>
      <td class="alignCenter blueColor" style="width:50px">OD</td>
	  <td class="alignCenter" style="width:50px"><?php echo replace_spl_char($RxDetails['sphere_od']);?></td>
      <td class="alignCenter" style="width:60px"><?php echo replace_spl_char($RxDetails['cyl_od']);?></td>
      <td class="alignCenter" style="width:50px"><?php echo replace_spl_char($RxDetails['axis_od']);?></td>
      <td class="alignCenter" style="width:50px"><?php echo replace_spl_char($RxDetails['add_od']);?></td>
	  <td class="alignCenter" style="width:50px"><?php echo $od_prism;?></td>
	  <td class="alignCenter" style="width:50px"><?php echo replace_spl_char($RxDetails['base_od']);?></td>
	  <td class="alignCenter" style="width:50px"><?php echo replace_spl_char($RxDetails['seg_od']);?></td>
	  <td class="alignCenter" style="width:50px"><?php echo replace_spl_char($RxDetails['near_pd_od']);?></td>
	  <td class="alignCenter" style="width:50px"><?php echo replace_spl_char($RxDetails['dist_pd_od']);?></td>
    </tr>
    <tr class="<?php echo $rowbg;?> cellBorder" style="height:22px;">
      <td class="alignCenter greenColor">OS</td>
	  <td class="alignCenter"><?php echo replace_spl_char($RxDetails['sphere_os']);?></td>
      <td class="alignCenter"><?php echo replace_spl_char($RxDetails['cyl_os']);?></td>
      <td class="alignCenter"><?php echo replace_spl_char($RxDetails['axis_os']);?></td>
      <td class="alignCenter"><?php echo replace_spl_char($RxDetails['add_os']);?>
	  <td class="alignCenter"><?php echo $os_prism;?></td>
	  <td class="alignCenter"><?php echo replace_spl_char($RxDetails['base_os']);?></td>
	  <td class="alignCenter"><?php echo replace_spl_char($RxDetails['seg_os']);?></td>
	  <td class="alignCenter"><?php echo replace_spl_char($RxDetails['near_pd_os']);?></td>
	  <td class="alignCenter"><?php echo replace_spl_char($RxDetails['dist_pd_os']);?></td>
      </td>
    </tr>
</table>
<?php } ?>
</div>
<?php } else { echo '<div style="height:220px;" class="alignCenter">No Record Exists.</div>'; }?>

	<div class="btn_cls mt10" style="width: 95%; float: left;">
      <input type="button" name="Cancel" value="Close" onClick="javascript:window.close();"/>
    </div>
 </div>
</body>
</html>