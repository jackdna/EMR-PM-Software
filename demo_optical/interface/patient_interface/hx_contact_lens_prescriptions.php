<?php 
/*
File: hx_contact_lens_prescriptions.php
Coded in PHP7
Purpose: Hx Contact Lens Prescriptions
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
	<div style="padding:0px; width:800px; margin:0 auto;">
        <div class="listheading">
            <div class="fl">Hx Contact Lens Prescriptions</div>
            <div class="fl" style="width:auto; color:#03F; text-align:left; margin-left:140px;"><?php echo $patient_name_id;?></div>		
        </div>
<?php
$arrRxDetails=array();
$qry="Select op_ord.*, ord.item_name FROM in_cl_prescriptions as op_ord inner join in_order_details as ord on op_ord.det_order_id=ord.id WHERE op_ord.patient_id='".$_SESSION['patient_session_id']."' ORDER BY op_ord.id DESC";  
$rs=imw_query($qry);
while($row = imw_fetch_array($rs))
{
	$arrRxDetails[] = $row;
}
?>
<table class="table_collapse" style="width:99.9.5%">
    <tr class="listheading">
      <td class="alignCenter" style="width:100px">Item Name</td>
      <td class="alignCenter" style="width:50px">Vision</td>
      <td class="alignCenter" style="width:80px">Sphere</td>
      <td class="alignCenter" style="width:100px">Cylinder</td>
      <td class="alignCenter" style="width:80px">Axis</td>
      <td class="alignCenter" style="width:80px">BC</td>
      <td class="alignCenter" style="width:80px">Add</td>
      <td class="alignCenter" style="width:80px; padding-right:15px;">Diameter</td>
    </tr>
</table> 
<?php 
	if(count($arrRxDetails)>0)
	{
?>
<div style="height:220px; overflow-y:scroll;">
<table class="table_collapse text14" style="width:99.8%">   
    <?php
		foreach($arrRxDetails as $RxDetails)
		{
			if($rowbg=='even'){$rowbg="odd";}else{$rowbg="even";}
	?>
    <tr class="<?php echo $rowbg;?> cellBorder" style="height:22px;">
      <td rowspan="2" class="alignCenter" style="width:100px"><?php echo $RxDetails['item_name'];?></td>
      <td class="alignCenter blueColor" style="width:50px">OD</td>
	  <td class="alignCenter" style="width:80px"><?php echo replace_spl_char($RxDetails['sphere_od']);?></td>
      <td class="alignCenter" style="width:100px"><?php echo replace_spl_char($RxDetails['cylinder_od']);?></td>
      <td class="alignCenter" style="width:80px"><?php echo replace_spl_char($RxDetails['axis_od']);?></td>
      <td class="alignCenter" style="width:80px"><?php echo replace_spl_char($RxDetails['base_od']);?></td>
      <td class="alignCenter" style="width:80px"><?php echo replace_spl_char($RxDetails['add_od']);?></td>
      <td class="alignCenter" style="width:80px"><?php echo replace_spl_char($RxDetails['diameter_od']);?></td>
    </tr>
    <tr class="<?php echo $rowbg;?> cellBorder" style="height:22px;">
      <td class="alignCenter greenColor">OS</td>
	  <td class="alignCenter"><?php echo replace_spl_char($RxDetails['sphere_os']);?></td>
      <td class="alignCenter"><?php echo replace_spl_char($RxDetails['cylinder_os']);?></td>
      <td class="alignCenter"><?php echo replace_spl_char($RxDetails['axis_os']);?></td>
      <td class="alignCenter"><?php echo replace_spl_char($RxDetails['base_os']);?></td>
      <td class="alignCenter"><?php echo replace_spl_char($RxDetails['add_os']);?></td>
      <td class="alignCenter"><?php echo replace_spl_char($RxDetails['diameter_os']);?>
      </td>
    </tr>
    <?php } ?>
</table>
</div>
<?php }else{ echo '<div style="height:220px;" class="alignCenter">No Record Exists.</div>';}?>

	<div class="btn_cls mt10" style="width: 95%; float: left;">
      <input type="button" name="Cancel" value="Close" onClick="javascript:window.close();"/>
    </div>
 </div>
</body>
</html>