<?php
/*pro_fac_id*/
require_once(dirname('__FILE__')."/../../../config/config.php");
require_once(dirname('__FILE__')."/../../../library/classes/functions.php");
$column_data_limit=13;
$batch_id=$_REQUEST['batch'];

/*Get Facility Name*/
	$location_name = "";
	$resp_fac = imw_query("SELECT `loc_name` FROM `in_location` WHERE `id`='".$_SESSION["pro_fac_id"]."'");
	if($resp_fac && imw_num_rows($resp_fac)>0){
		$location_name_data = imw_fetch_object($resp_fac);
		$location_name = $location_name_data->loc_name;
	}
	$user_qry=imw_query("select * from users where id=".$_SESSION['authId']."");
  	$user_row=imw_fetch_array($user_qry);
	$user_name=$user_row['lname'].", ". $user_row['fname'];
/*End Get Facility Name*/

/*Resons List*/
$reason_arr = array();
$query5=imw_query("select id,reason_name from in_reason where del_status='0' order by reason_name");
while($sel_row5=imw_fetch_array($query5)){ 
	$reason_arr[$sel_row5['id']]=$sel_row5['reason_name'];
}

/*Item/Module Types List*/
$module_arr = array();
$query2=imw_query("select * from in_module_type");
while($row2=imw_fetch_array($query2)){
	$module_arr[$row2['id']]=$row2['module_type_name'];
}
?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Optical</title>
<link rel="stylesheet" href="<?php echo $GLOBALS['WEB_PATH'];?>/library/css/inv_css.css?<?php echo constant("cache_version"); ?>" />
<!--for fancy models-->
<link rel="stylesheet" type="text/css" href="<?php echo $GLOBALS['WEB_PATH'];?>/library/css/jquery.modal.css?<?php echo constant("cache_version"); ?>" />
<link rel="stylesheet" type="text/css" href="<?php echo $GLOBALS['WEB_PATH'];?>/library/css/jquery.modal.theme-xenon.css?<?php echo constant("cache_version"); ?>" />
<link rel="stylesheet" type="text/css" href="<?php echo $GLOBALS['WEB_PATH'];?>/library/css/jquery.modal.theme-atlant.css?<?php echo constant("cache_version"); ?>" />
<!--for fancy models end here-->


<script src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/jquery-1.10.1.min.js?<?php echo constant("cache_version"); ?>"></script>
<script src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/jquery-ui.js?<?php echo constant("cache_version"); ?>"></script>
<!--for fancy models-->
<script src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/jquery.modal.js?<?php echo constant("cache_version"); ?>"></script>
<script src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/jquery.modal.function.js?<?php echo constant("cache_version"); ?>"></script>

<script src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/common.js?<?php echo constant("cache_version"); ?>"></script>
<script src="<?php echo $GLOBALS['WEB_PATH'];?>/library/dymo/DYMO.Label.Framework.latest.js?<?php echo constant("cache_version"); ?>"></script>
<script>
var searchUPc = new Array();
$(document).ready(function(){
	var val="";
	var sr_no="";
	$("#selectall").click(function(){		
		if($(this).is(":checked")){
			$(".print_item").prop('checked', true);
		}else{
			$(".print_item").prop('checked', false);
		}
	});
	

	//BUTTONS
	<?php if($_REQUEST['batch']==''){?>
		$("#action_buttons").hide();
	<?php }?>
	<?php if($_REQUEST['status']=='updated'){?>
		
		$("#action_buttons").show();
    <?php }?>
	
});


function show_div()
{
	$('#show_con').css("display","block");
	document.getElementById('scan_image').focus();	
	
	$("#action_buttons").show();
}
function show_alert_div(a)
{
	
	//window.location.href="index.php?batch_status=Updated";	
	window.opener.location.href="index.php?batch_status=Updated";
	window.self.close()
}
function alert_msg_update()
{
	top.falert("You can't Update records now");
}
function get_batch(batch_id,status)
{
	//window.location.href="index.php?batch="+batch_id+"&status="+status;
	window.opener.location.href="index.php?batch="+batch_id+"&status="+status;
	window.self.close()
}
function show_recon()
{
	document.getElementById('batches_div').style.display="none";
	document.getElementById('search_btn').removeAttribute("disabled");
	document.getElementById('new_stock_btn').removeAttribute("disabled");
	document.getElementById('scan_image').removeAttribute("disabled");
	
	document.getElementById('scan_image').focus();
}
function reload_page(){
	//window.location.href=WEB_PATH+'/interface/admin/stock_reconc/index.php';
	window.opener.location.href=WEB_PATH+'/interface/admin/stock_reconc/index.php';
	window.self.close()
}
function load_batches()
{
	//window.location.href="index.php";
	window.opener.location.reload();
	window.self.close()
}
function print_batch()
{
	var upc_len=document.getElementsByName('upc_code[]').length;
	var batch=document.getElementsByName('in_bat_rec_id[]');
	var items=document.getElementsByName('item_id[]');
	var upc_cs=document.getElementsByName('upc_code[]');
	var batch_id=document.getElementById('batch_id_field').value;
	var item_id="";
	var upc=""
	var data="";
	var data1="";
	var data2="";
	for(i=0;i<upc_len;i++)
	{
		data+=(items[i].value)+",";
		data1+=(upc_cs[i].value)+",";
		data2+=(batch[i].value)+",";
	}
	$.ajax({
		type:"POST",
		url:"batch_print.php",
		data:"upc="+data1+"&items="+data+"&batch="+data2+"&batch_id="+batch_id,
		beforeSend: function(){
			$("#loading").show();
		},
		success: function(msg)
		{
			var url='<?php echo $GLOBALS['WEB_PATH']?>/library/new_html2pdf/createPdf.php?op=l&file_name=batch_data';
			//var url='<?php echo $GLOBALS['WEB_PATH']?>/library/new_html2pdf/batch_data.html';
			window.opener.top.WindowDialog.closeAll();
			var Add_new_popup=window.opener.top.WindowDialog.open('Add_new_popup',url);
			$("#loading").hide();
		}
	});
}
function reason_sel(b)
{
	$('.reason_sel').val(b);
}
function adv_reason_sel(b)
{
	$('.adv_reason_sel .reason_sel').val(b);
}
$(document).ready(function(e) {
	var type_id =$("#type_optical_id").val();
	get_type_manufacture1(type_id,'0');
});
</script>
<style>
	.printing_upc, .printing_data{
	display:none;
}

.btn_cls1 {
	position: absolute;
	margin: auto;
	bottom: 0;
	width: 98%;
}
.batches_div a {
	text-decoration: none;
}
.batch_div_msg {
	float: right;
	width: 590px;
	margin: 0 0 0 0;
	color: #0D6030;
	font-weight: bold;
}
.head_tab th {
	border-right: 1px solid #E8E8E8;
}
.disc_input, .pprice_input, .rprice_input, .wprice_input{width: 50px;}
/*New Stock Style*/
#newStock{width: 100%; height: 100%; display: block; position: absolute; background-color: rgba(0,0,0,0.4);}
.stockdiv{background-color:#fff; height:54%; width:95%; margin:0 auto; top:4%; position:relative; border-radius:7px;box-shadow:0px 0px 2px 1px #FFF;}
.itemOptions>table{width:100%}
.itemOptions>table tr.bgCol{background-color:#EEE}
.itemOptions>table tr>td:nth-child(odd){width:150px;padding-left:10px;}
.hide{display:none;}
.nbtnDiv{bottom:0px;position:absolute;width:93.2%;}
.nHide{float:right;margin-right:10px;margin-top:0px;background:#eee;padding:5px;border-radius:20px;cursor:pointer;}
.errMsg{color:#f00;display:none;}
#advancedSearch{width: 100%; height: 100%; display: block; position: absolute; background-color: rgba(0,0,0,0.4);}

#print_label_div{width: 80%; left:8%; display: block; position: absolute; background-color: rgba(0,0,0,0.4);}
.resultdiv{background-color:#fff; height:88%; width:95%; margin:0 auto; top:4%; position:relative; border-radius:7px;box-shadow:0px 0px 2px 1px #FFF;}
select[disabled]{background-color:rgb(235,235,228);}
	
</style>
</head>
<body>
<div class="mt10 rec_con">
  <div class="listheading">
  	Stock Reconciliation - <?php echo $location_name; ?>
	<?php if($_REQUEST['batch']!=""){
		$qur_bat=imw_query("SELECT `bt`.`save_date`, `bt`.`updated_date`, `bt`.`status`,
							  CONCAT(`u`.`lname`, ', ', `u`.`fname`) AS `username`
							  FROM `in_batch_table` `bt` LEFT JOIN `users` `u` ON(`u`.`id` = `bt`.`user_id`) 
							  WHERE `bt`.`id` = ".$_REQUEST['batch']);
		$bat_row=imw_fetch_array($qur_bat);
		
		$savedate = "";
		$savetime = "";
		if($bat_row['save_date']!="0000-00-00 00:00:00"){
			$savedate=date("m-d-Y",strtotime($bat_row['save_date']));
			$savetime=date("h:i:s",strtotime($bat_row['save_date']));
		}
		
		$updtdate = "";
		$updttime = "";
		if($bat_row['updated_date']!="0000-00-00 00:00:00"){
			$updtdate=date("m-d-Y",strtotime($bat_row['updated_date']));
			$updttime=date("h:i:s",strtotime($bat_row['updated_date']));
		}
		$batch_user_name = $bat_row['username'];
	?>
	<div class="batch_div_msg" id="batch_div_msg">
		<?php if($bat_row['status']=="saved"){
			echo "Batch Saved On: ".$savedate." " .$savetime."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
			echo "Saved By: ".$batch_user_name;
		}
		else{ 
			echo "Batch Reconciled on: ".$updtdate." " .$updttime."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
			echo "Reconciled by: ".$batch_user_name;
		}
	?>
	</div>
	<?php } ?>
  </div>
  
 	<form name="recon_form" id="recon_form" action="" method="post" style="width: 100%">
	<!-- Hidden Field for new stock Indocator -->
	<input type="hidden" name="new_stock" id="new_stock" value="0" />
	<table style="width:100%;border:0px none;" class="btn_cls">
	  <tr><td width="50%" align="left">
	  <?php if($_REQUEST['status']!="updated"){?>
	  <table>
	  <tr>
		<th>Enter UPC Code</th>
		<td><input type="text" name="scan_image[]"  id="scan_image" autocomplete="off" autofocus></td>
		<td colspan="2">
		<input type="submit" value="Search" id="search_btn">
		<input type="submit" value="New Stock" id="new_stock_btn">
		<input type="button" value="Advanced Search" id="advanced_search_btn" onClick="adv_srh_fun();">
		  </td>
	  </tr>
	</table>
	<?php }?>
	</td>
	<td width="50%" align="right"><div id="action_buttons" style="display: <?php echo($_REQUEST['status'])?'block':'none';?>">
	<input type="button" value="Print" id="Print" onClick="print_batch();">
	<input type="button" value="Print Labels" id="Print Labels" onClick="print();">
	<input type="button" value="Close" id="Back" onClick="reload_page();">
	</div>
	</td></tr></table>
  </form>  
</div>
<div class="back_btn" id="back_btn" style="display:none;">
  <div class="btn_cls btn_cls1">
    <input type="button" value="Back" onClick="window.location.href='./index.php'">
  </div>
</div>
<div class="err_div"></div>
<div id="show_con" style="width: 100%">
<?php
	$minus_hg="260";
	if($_REQUEST['status']=='updated'){$minus_hg="260";}
	$item_div=($_REQUEST['status']=='updated')?"288":"242";
	$summary_div=230;
?>

<div class="uper_cont" style="height:<?php echo $_SESSION['wn_height']-$minus_hg;?>px;overflow:hidden;">

<div style="height:<?php echo $_SESSION['wn_height']-($minus_hg+$summary_div)?>px;overflow-y:scroll;overflow-x:hidden; width:100%">
   
   <form action="" method="post" name="quan_form" id="quan_form" style="width:100%;">
		<div style="display:none">
			<input type="submit" value="Save & Reconcile" name="save_qnty" id="save_qnty">
		</div>
    <table style="width:100%;border:0px none;">
      <thead>
      <?php if($_REQUEST['status']=='updated'){?>
        <tr class="listheading sepTH">
          <th style="width:3%;"><input type="checkbox" name="select_all" id="selectall" style="display: none"><label for="selectall">S.No</label></th>
          <th style="width:8%;">UPC Code</th>
          <th style="width:8%;" title="Product Type">P. Type</th>
          <th style="width:8%;">Product Name</th>
          <th style="width:16%;">Size</th>
          <th style="width:9%;">Brand</th>
		  <th style="width:8%;">Color</th>
		  <th style="width:5%;">Style</th>
          <th style="width:5%;">Discount</th>
		  <th style="width:5%;" title="Wholesale Price">W. Price</th>
		  <th style="width:5%;" title="Retail Price">R. Price</th>
		  <th style="width:5%;" title="Purchase Price">P. Price</th>
          <th style="width:5%;" title="Fac. Qty.">F. Qty.</th>
          <th style="width:5%;" title="Reconciled Quantity">Rec. Qty.</th>
          <th style="width:5%;"><?php if($bat_row['status']=="saved" || !isset($bat_row['status']))
		  {?>
            <select style="height:23px;width:80px;" class="reason_sel" onChange="reason_sel(this.value);">
              <option value="0">Reason</option>
              <?php $query=imw_query("select * from in_reason where del_status='0' order by reason_name");
		  			while($sel_row=imw_fetch_array($query)){
				?>
              <option value="<?php echo $sel_row['id'];?>"><?php echo $sel_row['reason_name'];?></option>
              <?php } ?>
            </select>
            <?php }else
		  {
			  echo "<label style='width:80px;'>Reason</label>";
		  }?>
          </th>
        </tr>
        <?php }else{?>
        <tr class="listheading sepTH">
          <th style="width:3%;"><input type="checkbox" name="select_all" id="selectall"></th>
          <th style="width:8%;">UPC Code</th>
          <th style="width:8%;" title="Product Type">P Type</th>
          <th style="width:8%;">Product Name</th>
          <!--<th style="width:111px;">Manufacturer</th>-->
          <th style="width:16%;">Size</th>
          <th style="width:9%;">Brand</th>
		  <th style="width:8%;">Color</th>
		  <th style="width:5%;">Style</th>
          <th style="width:5%;">Discount</th>
		  <th style="width:5%;" title="Wholesale Price">W. Price</th>
		  <th style="width:5%;" title="Retail Price">R. Price</th>
		  <th style="width:5%;" title="Purchase Price">P. Price</th>
          <th style="width:5%;" title="Fac. Qty.">F. Qty.</th>
          <th style="width:5%;">Rec. Qty.</th>
          <th style="width:5%;"><?php if($bat_row['status']=="saved" || !isset($bat_row['status']))
		  {?>
            <select style="height:23px;width:80px;" class="reason_sel" onChange="reason_sel(this.value);">
              <option value="0">Reason</option>
              <?php foreach($reason_arr as $res_key=>$res_val){	?>
              <option value="<?php echo $res_key;?>"><?php echo $res_val;?></option>
              <?php } ?>
            </select>
            <?php }else
		  {
			  echo "<label style='width:80px;'>Reason</label>";
		  }?>
          </th>
        </tr>
        <?php }?>
      </thead>
 
        <tbody id="TestTable">
          <?php if(isset($_REQUEST['batch'])){
			 
			$query1=imw_query("select id,vendor_name from in_vendor_details");
            while($row1=imw_fetch_array($query1)){
				$vendor_arr[$row1['id']]=$row1['vendor_name'];
			}
			
			$query3=imw_query("select id,frame_source from in_frame_sources");
            while($row3=imw_fetch_array($query3)){
				$frame_source_arr[$row3['id']]=$row3['frame_source'];
			}
			$query4=imw_query("select id,brand_name from in_contact_brand");
            while($row4=imw_fetch_array($query4)){
				$contact_brand_arr[$row4['id']]=$row4['brand_name'];
			}
			$query5=imw_query("select id,color_name,color_code from in_color");
            while($row5=imw_fetch_array($query5)){
				$color_name_arr[$row5['id']]=$row5['color_name'];
			}
			
			$query6=imw_query("select id,color_name,color_code from in_frame_color");
            while($row6=imw_fetch_array($query6)){
				$frame_color_name_arr[$row6['id']]=$row6['color_name'];
			}
			
			$query7=imw_query("select id,style_name from in_frame_styles");
            while($row7=imw_fetch_array($query7)){
				$frame_style_name_arr[$row7['id']]=$row7['style_name'];
			}
	
			 
        $i="";
		$j=1;
        $d="";
        $prev_upc = array();
        echo "<script>show_div();</script>";
        $query_u=imw_query("select * from in_batch_records where in_batch_id=".$_REQUEST['batch']."");
        $i=imw_num_rows($query_u);
        print "<script type=\"text/javascript\">
            i = ".($i+1).";
        </script>";
        echo "<input type='hidden' id='batch_id_field' name='batch_id_field' value='".$_REQUEST['batch']."'>";	
        while($row_u=imw_fetch_array($query_u))
        {
            $upc=$row_u['in_item_id'];
            array_push($prev_upc,"'".$row_u['item_upc_code']."'");
            $query=imw_query("select * from in_item where id='".$upc."'");
        if(imw_num_rows($query)>0)
        {
                
        while($row=imw_fetch_array($query)) {
			
            $query5=imw_query("select * from in_item_loc_total where loc_id='".$_SESSION['pro_fac_id']."' and item_id='".$row['id']."'");
            $row5=imw_fetch_array($query5);
            if($_REQUEST['status']=='saved'){
				$upc_code_tr=str_replace(' ','_',$row['upc_code'])."_r";
					echo "<tr id='".$upc_code_tr."'>";
					echo "<input type='hidden' name='upc_code[]' value='".$row['upc_code']."'>";
					echo "<input type='hidden' name='in_bat_rec_id[]' value='".$row_u['id']."'>";
					echo "<input type='hidden' name='fac_quant[]' value=".$row5['stock'].">";
					echo "<input type='hidden' name='tot_qnt[]' value=".$row['qty_on_hand'].">";
					echo "<input type='hidden' name='item_id[]' value=".$row['id'].">";
					echo "<input type='hidden' name=\"module_type[]\" class=\"module_type\">";
					
					echo "<input type='hidden' name=\"retail_price_flag[]\" class=\"retail_flag\" value='".$row_u['retail_price_flag']."'>";
					echo "<input type='hidden' name=\"purchase_price_flag[]\" class=\"purchase_flag\" value='".$row_u['purchase_price_flag']."'>";

					echo "<input type='hidden' name=\"prod_name[]\" class=\"prod_name\" />";
				    echo "<td><input type='checkbox' class='print_item' name='print_item[]' value='".$row['upc_code']."'></td>";
					echo "<td>".$row['upc_code']."</td>";
					echo "<td>".$module_arr[$row['module_type_id']]."</td>";
					//brand
					echo "<td";
					if(strlen($row['name'])>$column_data_limit){
						$pro_name=substr($row['name'],0,$column_data_limit).'..';
						echo " data-title=\"".$row['name']."\" onMouseOver=\"tooltip(this, 'block');\" onMouseOut=\"tooltip(this,'none');\"";
					}else {$pro_name=$row['name'];}
					echo ">".$pro_name."</td>";
					/*//vendor
					echo "<td";
					$vendorName=$vendor_arr[$row['vendor_id']];
					if(strlen($vendorName)>$column_data_limit){
						$vendor=substr($vendorName,0,$column_data_limit).'..';
						echo " data-title=\"".$vendorName."\" onMouseOver=\"tooltip(this, 'block');\" onMouseOut=\"tooltip(this,'none');\"";
					}else {$vendor=$vendorName;}
					echo ">".$vendor."</td>";*/
				
					//size of frame
					echo "<td>";
				if($row['module_type_id']==1){	
				echo $row[fpd]."-".$row[bridge]."-".$row[temple];
				/*echo"<table style='width:100%;'>
						<tr>
							<td width='25%'><strong>A</strong> $row[a]</td>
							<td width='25%'><strong>B</strong> $row[b]</td>
							<td width='25%'><strong>ED</strong> $row[ed]</td>
							<td width='25%'><strong>DBL</strong> $row[dbl]</td>
						</tr></table>
						<table style='width:100%;'>
						<tr>
							<td width='33%'><strong>Temple</strong> $row[temple]</td>
							<td width='33%'><strong>Bridge</strong> $row[bridge]</td>
							<td width='auto'><strong>FPD</strong> $row[fpd]</td>
						</tr>
					</table>";*/
				}
				echo"</td>";
				
					if($row['module_type_id']==3){
						//brand
                  	 	echo "<td";
						$brandName=$contact_brand_arr[$row['brand_id']];
						if(strlen($brandName)>$column_data_limit){
							$brand=substr($brandName,0,$column_data_limit).'..';
							echo " data-title=\"".$brandName."\" onMouseOver=\"tooltip(this, 'block');\" onMouseOut=\"tooltip(this,'none');\"";
						}else {$brand=$brandName;}
						echo ">".$brand."</td>";
						//color
						echo "<td";
						$colorName=$color_name_arr[$row['color']];
						if(strlen($colorName)>$column_data_limit){
							$color=substr($colorName,0,$column_data_limit).'..';
							echo " data-title=\"".$colorName."\" onMouseOver=\"tooltip(this, 'block');\" onMouseOut=\"tooltip(this,'none');\"";
						}else {$color=$colorName;}
						echo ">".$color."</td>";
					}else{
						//brand
						echo "<td";
						$brandName=$frame_source_arr[$row['brand_id']];
						if(strlen($brandName)>$column_data_limit){
							$brand=substr($brandName,0,$column_data_limit).'..';
							echo " data-title=\"".$brandName."\" onMouseOver=\"tooltip(this, 'block');\" onMouseOut=\"tooltip(this,'none');\"";
						}else {$brand=$brandName;}
						echo ">".$brand."</td>";
						//color
						echo "<td";
						$colorName=$frame_color_name_arr[$row['color']];
						if(strlen($colorName)>$column_data_limit){
							$color=substr($colorName,0,$column_data_limit).'..';
							echo " data-title=\"".$colorName."\" onMouseOver=\"tooltip(this, 'block');\" onMouseOut=\"tooltip(this,'none');\"";
						}else {$color=$colorName;}
						echo ">".$color."</td>";
					}
					echo "<td";
					$styleName=$frame_style_name_arr[$row['frame_style']];
					if(strlen($styleName)>$column_data_limit){
						$style=substr($styleName,0,$column_data_limit).'..';
						echo " data-title=\"".$styleName."\" onMouseOver=\"tooltip(this, 'block');\" onMouseOut=\"tooltip(this,'none');\"";
					}else {$style=$styleName;}
					echo ">".$style."</td>";
				
					echo "<td><input type='text' name=\"discount[]\" value='".$row_u['discount']."' id='".str_replace(" ", "_",$row['upc_code'])."_disc' class='disc_input' data-discount='".$upc_code_tr."'></td>";
				
					echo "<td><input type='text' name=\"wholesale_price_exis[]\" value='".$row_u['wholesale_price']."' id='".str_replace(" ", "_",$row['upc_code'])."_wPriceExis' class='wprice_input' data-wholesale='".$upc_code_tr."'></td>";
				
					echo "<td><input type='text' name=\"retail_price_exis[]\" value='".$row_u['retail_price']."' id='".str_replace(" ", "_",$row['upc_code'])."_rPriceExis' class='rprice_input' onChange=\"retailPriceChanged('".$upc_code_tr."')\" data-retail='".$upc_code_tr."'></td>";
				
					echo "<td><input type='text' name=\"purchase_price_exis[]\" value='".$row_u['purchase_price']."' id='".str_replace(" ", "_",$row['upc_code'])."_pPriceExis' class='pprice_input' data-purchase='".$upc_code_tr."'></td>";
					
				//echo "<td>".$row_u['discount']."</td>";
				//echo "<td>".$row_u['retail_price']."</td>";
				
                echo "<td>".$row5['stock']."</td>";
                echo "<td><input type='text' class='quant_input' value=\"".$row_u['in_item_quant']."\" name='item_quan[]' id='".str_replace(" ", "_",$row['upc_code'])."' data-quant='".$upc_code_tr."'></td>";
                echo "<td style='width:80px;'><select style='height:23px;width:80px;' name='resn_sel[]' class='reason_sel'><option value='0'>Select</option>";
                    foreach($reason_arr as $res_key=>$res_val){
                      if($row_u['reason']==$res_key)
                      {
                          $selected="selected";
                      }
                      else
                      {
                          $selected="";
                      }
						echo '<option value="'.$res_key.'" '.$selected.'>'.$res_val.'</option>';
					}
          			echo '</select>';
          		echo '</td>';
          echo '</tr>';
			}else{
				$upc_code_tr=str_replace(' ','_',$row['upc_code'])."_r";
					echo "<tr id='".$upc_code_tr."'>";
					echo "<input type='hidden' name='upc_code[]' value='".$row['upc_code']."'>";
					echo "<input type='hidden' name='in_bat_rec_id[]' value='".$row_u['id']."'>";
					echo "<input type='hidden' name='fac_quant[]' value=".$row5['stock'].">";
					echo "<input type='hidden' name='tot_qnt[]' value=".$row['qty_on_hand'].">";
					echo "<input type='hidden' name='item_id[]' value=".$row['id'].">";
					echo "<input type='hidden' name=\"module_type[]\" class=\"module_type\">";
					
					echo "<input type='hidden' name=\"retail_price_flag[]\" class=\"retail_flag\" value='".$row_u['retail_price_flag']."'>";
					echo "<input type='hidden' name=\"purchase_price_flag[]\" class=\"purchase_flag\" value='".$row_u['purchase_price_flag']."'>";
					
					echo "<input type='hidden' name=\"prod_name[]\" class=\"prod_name\" />";
					echo "<td id='sr_no'><input type='checkbox' class='print_item' name='print_item[]' value='".$row['upc_code']."'>".$j."</td>";
					echo "<td>".$row['upc_code']."</td>";
					echo "<td>".$module_arr[$row['module_type_id']]."</td>";
					echo "<td>".$row['name']."</td>";
					echo "<td>".$vendor_arr[$row['vendor_id']]."</td>";
						if($row['module_type_id']==3){
							echo "<td>".$contact_brand_arr[$row['brand_id']]."</td>";
							echo "<td>".$color_name_arr[$row['color']]."</td>";
						}else{
							echo "<td>".$frame_source_arr[$row['brand_id']]."</td>";
							echo "<td>".$frame_color_name_arr[$row['color']]."</td>";
						}
					echo "<td";
					$styleName=$frame_style_name_arr[$row['frame_style']];
					if(strlen($styleName)>$column_data_limit){
						$style=substr($styleName,0,$column_data_limit).'..';
						echo " data-title=\"".$styleName."\" onMouseOver=\"tooltip(this, 'block');\" onMouseOut=\"tooltip(this,'none');\"";
					}else {$style=$styleName;}
					echo ">".$style."</td>";
				
						echo "<td><input type='text' name=\"discount[]\" value='".$row_u['discount']."' id='".str_replace(" ", "_",$row['upc_code'])."_disc' class='disc_input' disabled></td>";
				
					echo "<td><input type='text' name=\"wholesale_price_exis[]\" value='".$row_u['wholesale_price']."' id='".str_replace(" ", "_",$row['upc_code'])."_wPriceExis' class='wprice_input' disabled></td>";
				
					echo "<td><input type='text' name=\"retail_price_exis[]\" value='".$row_u['retail_price']."' id='".str_replace(" ", "_",$row['upc_code'])."_rPriceExis' class='rprice_input' onChange=\"retailPriceChanged('".$upc_code_tr."')\" disabled></td>";
				
					echo "<td><input type='text' name=\"purchase_price_exis[]\" value='".$row_u['purchase_price']."' id='".str_replace(" ", "_",$row['upc_code'])."_pPriceExis' class='pprice_input'  disabled></td>";	
						//echo "<td>".$row_u['discount']."</td>";
						//echo "<td>".$row_u['retail_price']."</td>";
                        echo "<td>".$row_u['in_fac_prev_qty']."</td>";
                        echo "<td><input type='text' class='quant_input' value=\"".$row_u['in_item_quant']."\" name='item_quan[]' id='".str_replace(" ", "_",$row['upc_code'])."' disabled></td>";
                        echo "<td style='width:80px;'>";
                          if($reason_arr[$row_u['reason']]!=""){echo $reason_arr[$row_u['reason']];}else{ echo ""; }
                       
                      echo '</td>';
          		echo '</tr>';
         }
        $i--;
		$j++;
        }
        }
        }
        if(count($prev_upc)>0){
            
            print "<script type=\"text/javascript\">
                searchUPc = new Array(".implode(",",$prev_upc).");
                $.each(searchUPc, function(i, val){
                    searchUPc[i] = String(val);
                });
                </script>";
        }
}
		$rec_array=array();
		$rec_array2=array();
?>
          </tbody>
      </table>
	
    </form>
    
  </div>
      <?php if($_REQUEST['status']=='saved' || $_REQUEST['status']=='updated') {?>
      <div class="summary_div" style="margin-top:10px;">
        <div class="listheading">Reconciliation Summary</div>

        <table id='tabl_sum' style="padding:5px;">
<?php 
	$sum_query = 'SELECT `br`.*, `i`.`module_type_id`, `i`.`wholesale_cost`, IFNULL(`lt`.`stock`, 0) AS \'stock\'  FROM `in_batch_records` `br` LEFT JOIN `in_item` `i` ON(`br`.`in_item_id`=`i`.`id`) LEFT JOIN `in_item_loc_total` `lt` ON(`br`.`in_item_id`=`lt`.`item_id` AND `lt`.`loc_id`='.((int)$_SESSION['pro_fac_id']).') WHERE `br`.`in_batch_id`='.((int)$_REQUEST['batch']);
	$sum_query=imw_query($sum_query);
	
	$retail_price=$wholesal=$purchase_price=0;
	if(imw_num_rows($sum_query)>0)
	{
		/*<th style='width:70px;padding:5px;'>Int. Qty.</th>*/
		echo "<tr style='background:#e2e2e2;'>
		<th style='width:120px;padding:5px;text-align:left;'>Product Type</th>
		<th style='width:70px;padding:5px;'>Fac. Qty.</th>
		<th style='width:70px;padding:5px;'>Rec. Qty.</th>
		<th style='width:100px;padding:5px;'>Int. Amount</th>
		<th style='width:100px;padding:5px;'>Adj. Amount</th>
		<th style='width:130px;padding:5px;'>Total Rec. Amount</th>
		</tr>";
		while($sum_row=imw_fetch_array($sum_query))
		{
			if($_REQUEST['status']=='updated')
			{
				$qty=$sum_row['in_item_quant']-$sum_row['in_fac_prev_qty'];
				$rec_array[$module_arr[$sum_row['module_type_id']]][$sum_row['in_item_id']]['rec']=$qty;
				$rec_array[$module_arr[$sum_row['module_type_id']]][$sum_row['in_item_id']]['tot']=$sum_row['prev_tot_qty'];
				$rec_array[$module_arr[$sum_row['module_type_id']]][$sum_row['in_item_id']]['fac']=$sum_row['in_fac_prev_qty'];
				$rec_array[$module_arr[$sum_row['module_type_id']]][$sum_row['in_item_id']]['rec_pric']=($qty*$sum_row['wholesale_cost']);
				$rec_array[$module_arr[$sum_row['module_type_id']]][$sum_row['in_item_id']]['tot_pric']=($sum_row['prev_tot_qty']*$sum_row['wholesale_cost']);
				$rec_array[$module_arr[$sum_row['module_type_id']]][$sum_row['in_item_id']]['rem_pric']=($rec_array[$module_arr[$sum_row['module_type_id']]][$sum_row['in_item_id']]['rec_pric'])+($rec_array[$module_arr[$sum_row['module_type_id']]][$sum_row['in_item_id']]['tot_pric']);
				$rec_array[$module_arr[$sum_row['module_type_id']]][$sum_row['in_item_id']]['tot_fac_pric']=($sum_row['in_fac_prev_qty']*$sum_row['wholesale_cost']);
				$rec_array[$module_arr[$sum_row['module_type_id']]][$sum_row['in_item_id']]['rem_fac_pric']=($rec_array[$module_arr[$sum_row['module_type_id']]][$sum_row['in_item_id']]['rec_pric'])+($rec_array[$module_arr[$sum_row['module_type_id']]][$sum_row['in_item_id']]['tot_fac_pric']);
			}
			else
			{
				$qty=$sum_row['in_item_quant']-$sum_row['stock'];
				$rec_array[$module_arr[$sum_row['module_type_id']]][$sum_row['in_item_id']]['rec']=$qty;
				$rec_array[$module_arr[$sum_row['module_type_id']]][$sum_row['in_item_id']]['tot']=$sum_row['prev_tot_qty'];
				$rec_array[$module_arr[$sum_row['module_type_id']]][$sum_row['in_item_id']]['fac']=$sum_row['in_fac_prev_qty'];
				$rec_array[$module_arr[$sum_row['module_type_id']]][$sum_row['in_item_id']]['rec_pric']=($qty*$sum_row['wholesale_cost']);	
				$rec_array[$module_arr[$sum_row['module_type_id']]][$sum_row['in_item_id']]['tot_pric']=($sum_row['prev_tot_qty']*$sum_row['wholesale_cost']);
				$rec_array[$module_arr[$sum_row['module_type_id']]][$sum_row['in_item_id']]['rem_pric']=($rec_array[$module_arr[$sum_row['module_type_id']]][$sum_row['in_item_id']]['rec_pric'])+($rec_array[$module_arr[$sum_row['module_type_id']]][$sum_row['in_item_id']]['tot_pric']);
				$rec_array[$module_arr[$sum_row['module_type_id']]][$sum_row['in_item_id']]['tot_fac_pric']=($sum_row['in_fac_prev_qty']*$sum_row['wholesale_cost']);
				$rec_array[$module_arr[$sum_row['module_type_id']]][$sum_row['in_item_id']]['rem_fac_pric']=($rec_array[$module_arr[$sum_row['module_type_id']]][$sum_row['in_item_id']]['rec_pric'])+($rec_array[$module_arr[$sum_row['module_type_id']]][$sum_row['in_item_id']]['tot_fac_pric']);
			}
			//$wholesal=bcadd($wholesal,($sum_row['wholesale_cost']*$sum_row['prev_tot_qty']),2);
			$wholesal=number_format($wholesal+($sum_row['wholesale_cost']*$sum_row['prev_tot_qty']), 2);
		}
		$module_ar1=$arra=array();
		foreach($rec_array as $key=>$value)
		{
			echo "<tr><th style='text-align:left;padding-left:5px;'>".ucwords($key)."</th>";
			foreach($value as $key1=>$val1)
			{
				foreach($val1 as $key2=>$val2)
				{
					$module_ar1[$key][$key2][]=$val2;
				}
			}
			
			//echo "<td>".array_sum($module_ar1[$key]['tot'])."</td>";
			echo "<td>".array_sum($module_ar1[$key]['fac'])."</td>";
			echo "<td>".array_sum($module_ar1[$key]['rec'])."</td>";
			echo "<td style='text-align:right;padding-right:5px;'>".number_format(array_sum($module_ar1[$key]['tot_fac_pric']),2,".","")."</td>";
			echo "<td style='text-align:right;padding-right:5px;'>".number_format(array_sum($module_ar1[$key]['rec_pric']),2,".","")."</td>";
			echo "<td style='text-align:right;padding-right:5px;'>".number_format(array_sum($module_ar1[$key]['rem_fac_pric']),2,".","")."</td>";
			echo "</tr>";
		}
	}
?>
        </table>
      </div>
      <?php } ?>
      
      </div>
</div>
<div id="loading" style="display:none;">
    <img src="<?php echo $GLOBALS['WEB_PATH']; ?>/images/loading_image.gif" />
</div>

<div id="print_label_div" style="display: none; height: <?php echo $_SESSION['wn_height']-450?>px">
	<div class="resultdiv">
		<div class="listheading" style="border-radius:2px;padding-left:10px;background-size:3.5px;height:26px;">Print Labels
			<img class="nHide" src="<?php echo $GLOBALS['WEB_PATH']; ?>/images/del.png" onClick="$('#print_label_div').hide();" />
		</div>
		<div class="itemOptions row">
			<div class="col-sm-12 btn_cls">
			<label for="dymoPrinter">Select Printer</label>
			<select id="dymoPrinter"></select>

			<label for="dymoPaper" style="margin-left:20px;">Paper Size</label>
			<select id="dymoPaper">
				<!--<option value="PriceTag.label">Price Tag 22mm x 24mm</option>-->
				<option value="PriceTag1.label">Price Tag 25.4mm x 76.2mm</option>
				<!--<option value="Address.label">Address 28mm x 89mm</option>
				<option value="ExtraSmall_2UP.label">Extra Small (2-Up) 13mm x 25mm</option>-->
			</select>
			<input type="button" name="printFinalLabel" id="printFinalLabel" onclick="top.main_iframe.admin_iframe.printLabel();" value="Print" style="display: none">
			</div>
			<div class="row">
				<div class="col-sm-12" id="printable_data">
					<!--data will be here-->
					<div style="height:<?php echo $_SESSION['wn_height']-628?>px;overflow:scroll;overflow-x:hidden;margin:5px 0 0 0">
					  <div id="tab_data_cat">
						Loading Printable Data...
					  </div>
				  </div>
				</div>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
	
// stores loaded label info
var label;
	
// Printer's List
var printersSelect = document.getElementById('dymoPrinter');
// Label's List
var labelSelected = document.getElementById('dymoPaper');
var WEB_PATH='<?php echo $GLOBALS['WEB_PATH'];?>';

var reasons ='';
var i=1;
function print()
{
	var upc_name="";
	var upc_qty="";
	var len=document.getElementsByName('print_item[]').length;
	var item1=document.getElementsByName('print_item[]');
	for(var i=0;i<len;i++)
	{
		if(item1[i].checked==true)
		{
			upc_name+=(item1[i].value)+",";
			//get added qty
			upc_qty+=($("#"+item1[i].value).val())+",";
		}
	}
	if(upc_name=="")
	{
		falert("Please Select record(s) to print");
		return false;
	}
	upc_name=upc_name.substring(0, ((upc_name.length)-1));
	$("#print_label_div").show();
	// load printers list on startup
	//loadPrinters();
	$('#tab_data_cat').html('Loading Printable Data...');
	$.ajax({
		type:"POST",
		url:"stock_print_ajax.php",
		data:"reconcile=1&upc_name="+upc_name+"&upc_qty="+upc_qty,
		success: function(msg)
		{
			
			$('#tab_data_cat').html(msg);
			if(msg)
			{
				//document.getElementById('content_tab_print').style.display="block";	
				//BUTTONS
				//var mainBtnArr = new Array();
				//mainBtnArr[0] = new Array("frame","Print","top.main_iframe.admin_iframe.print_rec();");
				//top.btn_show("admin",mainBtnArr);						
			}else{
				//BUTTONS
				//var mainBtnArr = new Array();
				//top.btn_show("admin",mainBtnArr);						
			}
		},
		complete: function(){
			//$('#load_img').hide();
			//$("#printable_data").html('');
			//SHOW PRINT BUTTON
			$("#printFinalLabel").show();
			
		}
	});
	
}	
// called when the document completly loaded
	// To load Dymo Printer
	function onload(){
		// loads all supported printers into a Select List 
		function loadPrinters()
		{
			var printers = dymo.label.framework.getLabelWriterPrinters();
			if (printers.length == 0)
			{
				//alert("No DYMO LabelWriter printers are installed. Install DYMO LabelWriter printers.");
				//return;
			}
	
			for (var i = 0; i < printers.length; ++i)
			{
				var printer = printers[i];
				var printerName = printer.name;
	
				var option = document.createElement('option');
				option.value = printerName;
				option.appendChild(document.createTextNode(printerName));
				printersSelect.appendChild(option);
			}
		}
		
		// load printers list on startup
		loadPrinters();
	};
	
	// register onload event
	$(window).on('load', function(){
		onload();
	});
	
function loadLabelFromWeb(){

	// use jQuery API to load label
	$.ajax({
		url: top.WRP+"/library/dymo/"+labelSelected.value,
		async:false,
		success:function(data){
			label = dymo.label.framework.openLabelXml(data);
		}
	});
}

/*Print Labels for selected items*/
function printLabel(){

	try{
		// Load label Structure
		loadLabelFromWeb();

		if (!label){
			falert("Load label before printing");
			return;
		}
		if(printersSelect.value==""){
			falert("No DYMO LabelWriter printers are installed. Install DYMO LabelWriter printers.");
			return;
		}

		// set data using LabelSet and text markup
		//var labelSet = new dymo.label.framework.LabelSetRecord();
		var labelSet = new dymo.label.framework.LabelSetBuilder();
		var record; 
		//alert(label);
		/*Getting Data from Tabele*/
		var selectedRecords = document.getElementsByName('print_item[]');
		var selectedUPC = new Array();
		var innerI=0;
		$.each(selectedRecords, function(i,obj){
			if(obj.checked===true){
				printCount = 0;

				upc_data = $(".printing_upc",top.main_iframe.admin_iframe.document).get(innerI).innerHTML;
				print_data = $(".printing_data",top.main_iframe.admin_iframe.document).get(innerI).innerHTML;

				if(labelSelected.value === 'ExtraSmall_2UP.label'){
					print_data = print_data.replace(/-/g, "<br/>");
				}

				print_data = print_data.replace(/<br>/g, "<br/>");
				printCount = $(".labelCount").get(innerI).value;
				printCount = printCount.replace(/[^\d]/g, "");

				if(printCount==""){printCount=0;}
				printCount = parseInt(printCount);
				for(i=printCount; i>0; i--){
					/*Add Data to Dymo LabelSet*/
					record = labelSet.addRecord();
					record.setText('BARCODE', upc_data);
					record.setTextMarkup('TEXT', print_data);
					/*End Add Data to Dymo LabelSet*/
				}
				innerI++;
			}
		});
		/*End Getting Data from Table*/

		label.print(printersSelect.value, null, labelSet.toString());
		delete labelSet;
	}
	catch(e){
		falert(e.message || e);
	}
}

</script>
</body>
</html>