<?php
/*
File: patient_rx_history.php
Coded in PHP7
Purpose: Patient RX History
Access Type: Direct access
*/
require_once(dirname('__FILE__')."/../../config/config.php");
require_once($GLOBALS['DIR_PATH']."/library/classes/functions.php"); 
$pat_id=$_SESSION['patient_session_id'];
$divHeight = (($_SESSION['wn_height']-460)/2)-5;
?>
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, maximum-scale=1.0; charset=UTF-8" />
<title>Optical</title>
<link rel="stylesheet" href="../../library/css/inv_css.css?<?php echo constant("cache_version"); ?>" />
<script src="../../library/js/jquery-1.10.1.min.js?<?php echo constant("cache_version"); ?>"></script>
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
function popup_win(url,winName,features){
	var n = winName;
	var tb_oPU = [];
	if(!tb_oPU[n] || !(tb_oPU[n].open) || (tb_oPU[n].closed == true)){
		top.WindowDialog.closeAll();
		tb_oPU[n] =top.WindowDialog.open('Add_new_popup',url,winName,features);
		if(n=='newPatientWindow'){window.top.chk_window_opened["chkinwin"] = true;}
	}
	tb_oPU[n].focus();
}
function printMrPRS(e, givenMr,fId,date){
	var parWidth = parent.document.body.clientWidth/2;
	var parHeight = parent.document.body.clientHeight/2;
	popup_win('print_rx.php?printType=1&chartIdPRS='+fId+'&givenMr='+givenMr+'&dos_date='+date,'printPatientMr','scrollbars=1,resizable=1,width='+parWidth+'height='+parHeight+',top=0,left=0');
	e.stopPropagation();
}

function printRxPRS(id,type,date){
	var parWidth = parent.document.body.clientWidth/2;
	var parHeight = parent.document.body.clientHeight/2;
	popup_win('print_rx.php?printType=2&method=1&workSheetId='+id+'&scl_type='+type+'&dos_date='+date,'printPatientContact','scrollbars=1,resizable=1,width='+parWidth+'height='+parHeight+',top=0,left=0');
}
</script>
</head>
<body>
<?php if($_SESSION['patient_session_id']==""){ ?>
	<div class="text_12b" style="height:<?php echo $_SESSION['wn_height']-612;?>px; text-align:center;"><div style="margin-top:300px;">Please select Patient to Proceed</div></div>
<?php }else{ 
// LENS QUERY
/*$qry="Select DATE_FORMAT(order_place_date, '%m-%d-%Y') as 'orderedDate', sphere_od, cyl_od, axis_od, add_od, sphere_os, cyl_os, axis_os, add_os FROM optical_order_form 
WHERE patient_id='".$_SESSION['patient_session_id']."' ORDER BY order_place_date DESC";  
$rs=imw_query($qry);
*/?>
<div class="listheading text_bigger">Lens Prescriptions</div>
<?php
include('lens_rx_list.php');
//print_r($arrLensRX);
if(sizeof($arrLensRX)>0) { ?>
<table class="table_collapse" style="width:99.5%">
    <tr class="listheading">
      <td class="alignCenter" style="width:80px">DOS</td>
      <td class="alignCenter" style="width:50px">Vision</td>
      <td class="alignCenter" style="width:90px">Sphere</td>
      <td class="alignCenter" style="width:90px">Cylinder</td>
      <td class="alignCenter" style="width:80px">Axis</td>
      <td class="alignCenter" style="width:80px;">Add</td>
	  <td class="alignCenter" style="width:90px;">Prism</td>
      <td class="alignCenter" style="width:90px; padding-right:15px;">Print</td>
    </tr>
</table>
<div style="height:<?php echo $divHeight;?>px; overflow-y:auto;">
<table class="table_collapse text14" style="width:99.8%">   
<?php
//	 for($i=1; $i<sizeof($arrLensRX); $i++){
foreach($arrLensRX1 as $arrLensRX){
	foreach($arrLensRX as $key=>$val){
		$i = $key;
		 if($rowbg=='even'){$rowbg="odd";}else{$rowbg="even";}
	?>
    <tr class="<?php echo $rowbg;?> cellBorder" style="height:22px;">
      <td rowspan="2" class="alignCenter" style="width:80px"><?php echo $arrLensRX[$i]['OD']['DOS'];?></td>
      <td class="alignCenter blueColor" style="width:50px">OD</td>
	  <td class="alignCenter" style="width:90px"><?php echo $arrLensRX[$i]['OD']['Sphere'];?></td>
      <td class="alignCenter" style="width:90px"><?php echo $arrLensRX[$i]['OD']['Cylinder'];?></td>
      <td class="alignCenter" style="width:80px"><?php echo $arrLensRX[$i]['OD']['Axis'];?></td>
      <td class="alignCenter" style="width:80px"><?php echo $arrLensRX[$i]['OD']['Add'];?></td>
	  <td class="alignCenter" style="width:80px">
	  <?php
		//=======PRISM OD DATA WORKS START HERE=====
	    if($arrLensPrism[$i]['OD']['mr_od_p'] && $arrLensPrism[$i]['OD']['mr_od_sel']){
			echo $arrLensPrism[$i]['OD']['mr_od_p'].'<img src="../../images/pic_vision_pc.jpg" />'.$arrLensPrism[$i]['OD']['mr_od_sel'];
		}else if ($arrLensPrism[$i]['OD']['mr_od_p']){  
			echo $arrLensPrism[$i]['OD']['mr_od_p'].'&nbsp;<img src="../../images/pic_vision_pc.jpg" />'; 
		}
		if($arrLensPrism[$i]['OD']['mr_od_splash'] && $arrLensPrism[$i]['OD']['mr_od_prism']){
			echo '&nbsp/&nbsp'. $arrLensPrism[$i]['OD']['mr_od_splash'].'&nbsp'.$arrLensPrism[$i]['OD']['mr_od_prism'];
		}else if($arrLensPrism[$i]['OD']['mr_od_splash'])
		{
			echo '&nbsp/&nbsp'. $arrLensPrism[$i]['OD']['mr_od_splash'].'&nbsp';
		}
		if(empty($arrLensPrism[$i]['OD']['mr_od_p']) && empty($arrLensPrism[$i]['OD']['mr_od_splash'])){
			echo 'none';			
		}		 		 
	  ?>
	</td>
      <td class="alignCenter" style="width:100px" rowspan="2"><a href="#" onClick="printMrPRS(event, '<?php echo $arrLensRX[$i]['OD']['MR']?>',<?php echo $arrLensRX[$i]['OD']['ID'];?>,'<?php echo $arrLensRX[$i]['OD']['DOS'];?>')"><img src="../../images/print.png" width="20px;"></a> </td>
    </tr>
    <tr class="<?php echo $rowbg;?> cellBorder" style="height:22px;">
      <td class="alignCenter greenColor">OS</td>
	  <td class="alignCenter"><?php echo $arrLensRX[$i]['OS']['Sphere'];?></td>
      <td class="alignCenter"><?php echo $arrLensRX[$i]['OS']['Cylinder'];?></td>
      <td class="alignCenter"><?php echo $arrLensRX[$i]['OS']['Axis'];?></td>
      <td class="alignCenter"><?php echo $arrLensRX[$i]['OS']['Add'];?></td>
	  <td class="alignCenter" style="width:80px">
		<?php
	  	//=====PRISM OS DATA WORKS START HERE========
		if($arrLensPrism[$i]['OS']['mr_os_p'] && $arrLensPrism[$i]['OS']['mr_os_sel']){
			echo $arrLensPrism[$i]['OS']['mr_os_p'].'<img src="../../images/pic_vision_pc.jpg" />'.$arrLensPrism[$i]['OS']['mr_os_sel'];
		}else if ($arrLensPrism[$i]['OS']['mr_os_p']){  
			echo $arrLensPrism[$i]['OS']['mr_os_p'].'&nbsp;<img src="../../images/pic_vision_pc.jpg" />'; 
		}
		 if($arrLensPrism[$i]['OS']['mr_os_splash'] && $arrLensPrism[$i]['OS']['mr_os_prism']){
			echo '&nbsp/&nbsp'. $arrLensPrism[$i]['OS']['mr_os_splash'].'&nbsp'.$arrLensPrism[$i]['OS']['mr_os_prism'];
		}else if($arrLensPrism[$i]['OS']['mr_os_splash']){
			echo '&nbsp/&nbsp'. $arrLensPrism[$i]['OS']['mr_os_splash'].'&nbsp';
		}
		if(empty($arrLensPrism[$i]['OS']['mr_os_p']) && empty($arrLensPrism[$i]['OS']['mr_os_splash'])){
			echo 'none';			
		}	
		?>
	  </td>	
	</tr>
    <?php } } ?>
</table>
</div>
<?php }else{ echo '<div style="height:'.$divHeight.'px;" class="alignCenter">No Record Exists.</div>';}?>

<div class="listheading text_bigger" style="margin-top:5px">Contact Lens Prescriptions</div>
<?php
$arrRxDetails=array();
include('cl_rx_list.php');
//print_r($arrRxDetails);
if(count($arrRxDetails)>0){
?>
<table class="table_collapse" style="width:99.9.5%">
    <tr class="listheading">
      <td class="alignCenter" style="width:100px">DOS</td>
      <td class="alignCenter" style="width:80px">Type</td>
      <td class="alignCenter" style="width:50px">Vision</td>
      <td class="alignCenter" style="width:100px">Sphere</td>
      <td class="alignCenter" style="width:100px">Cylinder</td>
      <td class="alignCenter" style="width:100px">Axis</td>
      <td class="alignCenter" style="width:100px">BC</td>
      <td class="alignCenter" style="width:100px">Add</td>
      <td class="alignCenter" style="width:100px;">Diameter</td>
      <td class="alignCenter" style="width:100px; padding-right:15px;">Print</td>
    </tr>
</table> 
<div style="height:<?php echo $divHeight;?>px; overflow-y:auto;">
<table class="table_collapse text14" style="width:99.8%">   
    <?php
	foreach($arrRxDetails1 as $arrRxDetails){
		foreach($arrRxDetails as $rxDetails){
			
		$clws_id=$rxDetails['clws_id'];
		$clType=$rxDetails['clType'];
		if(!$clType)
		{
			$clType='Custom';
			$clws_id=$rxDetails['custom_id'];
		}
		if($rowbg=='even'){$rowbg="odd";}else{$rowbg="even";}	
		$cl_dos = $rxDetails['DOS'];
	?>
    <tr class="<?php echo $rowbg;?> cellBorder" style="height:22px;">
      <td rowspan="2" class="alignCenter" style="width:100px"><?php echo $cl_dos;?></td>
      <td rowspan="2" class="alignCenter" style="width:80px"><?php echo $clType;?></td>
      <td class="alignCenter blueColor" style="width:50px">OD</td>
	  <td class="alignCenter" style="width:100px"><?php echo $rxDetails['orderHxS'];?></td>
      <td class="alignCenter" style="width:100px"><?php echo $rxDetails['orderHxC'];?></td>
      <td class="alignCenter" style="width:100px"><?php echo $rxDetails['orderHxA'];?></td>
      <td class="alignCenter" style="width:100px"><?php echo $rxDetails['orderHxBc'];?></td>
      <td class="alignCenter" style="width:100px"><?php echo $rxDetails['orderHxAdd'];?></td>
      <td class="alignCenter" style="width:100px"><?php echo $rxDetails['orderHxDia'];?></td>
      <td class="alignCenter" style="width:100px" rowspan="2"><a href="#" onClick="printRxPRS('<?php echo $clws_id;?>','<?php echo $clType;?>','<?php echo $cl_dos;?>')"><img src="../../images/print.png" width="20px;"></a></td>
    </tr>
    <tr class="<?php echo $rowbg;?> cellBorder" style="height:22px;">
      <td class="alignCenter greenColor" style="width:50px">OS</td>
	  <td class="alignCenter" style="width:100px"><?php echo $rxDetails['orderHxS_OS'];?></td>
      <td class="alignCenter" style="width:100px"><?php echo $rxDetails['orderHxC_OS'];?></td>
      <td class="alignCenter" style="width:100px"><?php echo $rxDetails['orderHxA_OS'];?></td>
      <td class="alignCenter" style="width:100px"><?php echo $rxDetails['orderHxBc_OS'];?></td>
      <td class="alignCenter" style="width:100px"><?php echo $rxDetails['orderHxAdd_OS'];?></td>
      <td class="alignCenter" style="width:100px"><?php echo $rxDetails['orderHxDia_OS'];?></td>
    </tr>
    <?php }
	 }?>
</table>
</div>
<?php }else{ echo '<div style="height:'.$divHeight.'px;" class="alignCenter">No Record Exists.</div>';}?>
<?php if(sizeof($arrLensRX)>0 && count($arrRxDetails)>0){?><!--<div class="btn_cls" style="width: 100%;"><input type="button" value="Print" onClick="print_rx();"></div>--><?php }?>
<?php }?>
</body>
<script type="text/javascript">
$(document).ready(function(e) {
	//BUTTONS
	var mainBtnArr=[];
	top.btn_show("admin",mainBtnArr);		
    
});
</script>
</html>