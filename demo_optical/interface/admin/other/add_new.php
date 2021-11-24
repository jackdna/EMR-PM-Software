<?php
	/*
	File: add_new.php
	Coded in PHP7
	Purpose: Add New Location/Reason
	Access Type: Direct access
	*/
	require_once("../../../config/config.php");
	require_once("../../../library/classes/functions.php");
	$opr_id = $_SESSION['authId'];
	$date = date("Y-m-d");
	$time = date("h:i:s"); 
	
	$sel_manu = "select a.pos_facility_id,a.facilityPracCode,b.pos_prac_code from pos_facilityies_tbl a,pos_tbl b	where a.pos_id = b.pos_id order by facilityPracCode asc";
	$sel_res = imw_query($sel_manu);
	$sel_num = imw_num_rows($sel_res);
	if($sel_num > 0)
		{
		while($sel_row = imw_fetch_array($sel_res))
		{
			$show_prac_code = (strlen($sel_row['facilityPracCode']) > 18) ? substr($sel_row['facilityPracCode'], 0 , 15)."..." : $sel_row['facilityPracCode'];
			$manu_opt .= '<option value="'.$sel_row['pos_facility_id'].'">'.addslashes($show_prac_code)." - ".$sel_row['pos_prac_code'].'</option>'; 	 
		}
	}
	
	$slct_group = "select * from groups_new order by name asc";
	$get_group = imw_query($slct_group);
	if(imw_num_rows($get_group)>0){
	while($group = imw_fetch_array($get_group)){				
		$gourp_opt .='<option value="'.$group['gro_id'].'">'.addslashes($group['name']).'</option>';
	}
	}
	
	//---------- ADD NEW LOCATION -----------//
	if($_REQUEST['save']=="Save")
	{
		for($i=1; $i<=$_POST['totRows']; $i++)
		{
			if(trim($_POST['add_loc_name'.$i])!="")
			{
			$sel_manu = "select id, loc_name from in_location where del_status!='2' and loc_name = '".$_POST['add_loc_name'.$i]."'";
			$sel_res = imw_query($sel_manu);
			$sel_num = imw_num_rows($sel_res);
			if($sel_num == 0)
			{	
				
				 $insert_query = "insert in_location set 
				loc_name = '".imw_real_escape_string(trim($_POST['add_loc_name'.$i]))."',
				contact_person='".imw_real_escape_string(trim($_POST['add_contact_person'.$i]))."',
				fax = '".imw_real_escape_string(trim($_POST['add_fax'.$i]))."',
				tel_num = '".imw_real_escape_string(preg_replace('/[^0-9]/','',trim($_POST['add_tel'.$i])))."',
				tax_label = '".imw_real_escape_string(trim($_POST['add_tax_label'.$i]))."',
				tax = '".imw_real_escape_string(trim(implode("~~~", $_POST['location_tax'.$i])))."',
				address = '".imw_real_escape_string(trim($_POST['add_address'.$i]))."',
				npi = '".imw_real_escape_string(trim($_POST['add_npi'.$i]))."',
				city = '".imw_real_escape_string(trim($_POST['add_city'.$i]))."',
				zip = '".imw_real_escape_string(trim($_POST['add_zip'.$i]))."',					
				zip_ext = '".imw_real_escape_string(trim($_POST['add_zip_ext'.$i]))."',
				state = '".imw_real_escape_string(trim($_POST['add_state'.$i]))."',
				pos='".$_POST['fac_prac_code'.$i]."',
				entered_date='$date', 
				entered_time='$time', 
				entered_by='$opr_id',
				fac_group='".imw_real_escape_string(trim($_POST['fac_group'.$i]))."',
				idoc_fac_id='".$_POST['idoc_fac'.$i]."'";
				
				$run = imw_query($insert_query);
				$last_insert = imw_insert_id();
				
				if($last_insert!="")
				{
					if($_FILES['logo'.$i]['name']!="")
						{
							if(!is_dir("../../patient_interface/uploaddir/facility_logo/facility_".$last_insert."_".$last_insert)){
								//mkdir("../../patient_interface/uploaddir/facility_logo/facility_".$last_insert."_".$last_insert, 0777, true);
							}
							//$target="../../patient_interface/uploaddir/file_".$last_insert."_".$last_insert."/".$_FILES['logo'.$i]['name'];
							$target="facility_".$last_insert."_".$_FILES['logo'.$i]['name'];
							$path="../../patient_interface/uploaddir/facility_logo/";
						}
					
					move_uploaded_file($_FILES['logo'.$i]['tmp_name'],$path.$target);
					
					$query="update in_location set loc_logo='".$target."' where id=".$last_insert;
					imw_query($query);	
				}
				if($run)
				{
					echo "<script>window.opener.main_iframe.admin_iframe.location.reload(); window.close();</script>";	
				}
			}
		}
		
	}
}
?>

<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Optical</title>
<link rel="stylesheet" href="../../../library/css/inv_css.css?<?php echo constant("cache_version"); ?>" />
<!--for fancy models-->
<link rel="stylesheet" type="text/css" href="<?php echo $GLOBALS['WEB_PATH'];?>/library/css/jquery.modal.css?<?php echo constant("cache_version"); ?>" />
<link rel="stylesheet" type="text/css" href="<?php echo $GLOBALS['WEB_PATH'];?>/library/css/jquery.modal.theme-xenon.css?<?php echo constant("cache_version"); ?>" />
<link rel="stylesheet" type="text/css" href="<?php echo $GLOBALS['WEB_PATH'];?>/library/css/jquery.modal.theme-atlant.css?<?php echo constant("cache_version"); ?>" />
<!--for fancy models end here-->
<script src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/jquery-1.10.1.min.js?<?php echo constant("cache_version"); ?>"></script>
<!--for fancy models-->
<script src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/jquery.modal.js?<?php echo constant("cache_version"); ?>"></script>
<script src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/jquery.modal.function.js?<?php echo constant("cache_version"); ?>"></script>
<!--for fancy models end here-->
<script src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/common.js?<?php echo constant("cache_version"); ?>"></script>
<style type="text/css">
.tax_wrap{
	background-color: rgba(0,0,0,0.2);
    top: 0px;
    width: 100%;
    height: 100%;
    position: absolute;
	display: none;
	left: 0px;
}
.tax_div{
	width: 320px;
	margin: 0 auto;
	overflow: hidden;
	position: relative;
	top: 2%;
	z-index: 9999;
	background-color: #FFF;
}
.tax_wrap td{padding:2px;}
.button{
	border-radius: 5px;
    padding: 2px 6px;
    color: #fff;
    text-decoration: none;
    font-weight: bold;
    text-transform: uppercase;
    margin: 3px;
    background: #09F;
	font-size:14px;
	line-height:30px;
}
.location_tax{
	text-align: right;
}
</style>
<script>
function createRows(startNum,NoOfRows)
{
	var rowData_b='';
	var newNum = parseInt(startNum) + 1;
	var totalrows='';
	var row_class='';
	var ph_format = "<?php echo $GLOBALS['phone_format'];?>";
	var manu_opt = '<?php echo $manu_opt; ?>';
	var group_opt = '<?php echo $gourp_opt;?>';
	if(NoOfRows>0)
	{		
		oldId=startNum;
		
		totalrows = startNum+NoOfRows;
		if(totalrows%2!=0)
		{
			row_class = "even";
		}
		else
		{
			row_class = "odd";
		}
		
		for(i=newNum; i<=totalrows; i++)
		{	
			rowData_b='';
			
			rowData_b+='<tr id="tr_b_'+i+'">';
			rowData_b+='<td class="module_label '+row_class+'">';
			rowData_b+='<div class="inputblock fl" style="width:120px;"><input onchange="javascript:checkname(this)" type="text" style="width:119px;" name="add_loc_name'+i+'" id="add_vendor_name'+i+'" class="add_vendor_name" value="" /><br /><span>Location Name</span></div>';
			rowData_b+='<div class="inputblock fl" style="width:120px; margin:0 0 0 20px;"><input onchange="javascript:checkname(this)" type="text" style="width:119px;" name="add_contact_person'+i+'"  value="" /><br /><span>Contact Person</span></div>';
			rowData_b+='<div class="inputblock fl" style="width:120px; margin: 0 0 0 20px;"><input type="text" style="width:119px;" name="add_tel'+i+'" value="" onChange="set_phone_format(this,\''+ph_format+'\');" /><br /><span>Phone</span></div>';
			rowData_b+='<div class="inputblock fl" style="width:120px; margin: 0 0 0 20px;"><input type="text" style="width:119px;" name="add_fax'+i+'" value="" onChange="set_phone_format(this,\''+ph_format+'\',\'fax\');"  /><br /><span>Fax</span></div>';
			rowData_b+='<div class="inputblock fl" style="width:170px; margin: 0 0 0 20px;"><select name="fac_prac_code'+i+'" style="width:169px;"><option value="">- Select POS -</option>';
			rowData_b+=manu_opt;
			rowData_b+='</select><br/><span>POS</span></div>';
			rowData_b+='<div class="inputblock fl" style="width:180px; margin: 3px 0 0 15px;"><input type="file" name="logo'+i+'" value="" /><br /><span>Logo</span></div>';
			rowData_b+='<div class="inputblock fl" style="width:110px; margin: 5px 0 0 0;"><input type="text" style="width:109px;" name="add_address'+i+'" value="" /><br /><span>Address</span></div>';
			rowData_b+='<div class="inputblock fl" style="width:110px; margin: 5px 0 0 15px;"><input type="text" style="width:40px;" name="add_zip'+i+'" id="zip_'+i+'" onKeyUp="zip_vs_state(this,'+i+');" onblur="zip_vs_state_length(this,'+i+')" value="" maxlength="5"/>-<input type="text" style="width:40px; margin-left:2px;" name="add_zip_ext'+i+'" value="" maxlength="4"/><br /><span>Zip Code</span></div>';
			rowData_b+='<div class="inputblock fl" style="width:80px; margin: 5px 0 0 2px;"><input type="text" style="width:80px;" name="add_city'+i+'" id="city_'+i+'" value=""/><br /><span>City</span></div>';
			rowData_b+='<div class="inputblock fl" style="width:30px; margin: 5px 0 0 15px;"><input type="text" style="width:30px;" name="add_state'+i+'" id="state_'+i+'" value=""/><br /><span>State</span></div>';
			rowData_b+='<div class="inputblock fl" style="width:110px; margin: 5px 0 0 15px;"><input type="text" style="width:109px;" name="add_npi'+i+'" value="" /><br /><span>NPI</span></div>';
			rowData_b+='<div class="inputblock fl" style="width:63px; margin: 5px 0 0 15px;"><input type="text" style="width:60px;" name="add_tax_label'+i+'" value=""/><br /><span>Tax Label</span></div>';
			
			rowData_b+='<div class="inputblock fl " style="width:65px; margin: 5px 0 0 15px;"><a href="javascript:void(0);" onClick=\'$("#tax_wrap'+i+'").show();\' class="button">Tax(%)</a></div>';
			rowData_b+='<!-- tax div --><div id="tax_wrap'+i+'" class="tax_wrap"><div id="tax_div'+i+'" class="tax_div"><div class="module_border" style="overflow:hidden"><div class="listheading pl5">Tax(%) for Location<img onClick="$(\'#tax_wrap'+i+'\').hide();" src="<?php echo $GLOBALS['WEB_PATH']; ?>/images/del.png" style="margin-top:4px;margin-right:5px;float:right;cursor:pointer;" /></div>';
			rowData_b+='<table class="module_border" style="width:100%"><tr><td style="width:40%">Frames</td><td style="width:60%;"><input type="text" class="location_tax" name="location_tax'+i+'[]" id="tax_frame" value="0" onChange="checkTaxVal(this)" autocomplete="off" /></td></tr>';
			rowData_b+='<tr><td>Lenses</td><td><input type="text" class="location_tax" name="location_tax'+i+'[]" id="tax_lens" value="0" onChange="checkTaxVal(this)" autocomplete="off" /></td></tr>';
			rowData_b+='<tr><td>Contact Lenses</td><td><input type="text" class="location_tax" name="location_tax'+i+'[]" id="tax_cl" value="0" onChange="checkTaxVal(this)" autocomplete="off" /></td></tr>';
			rowData_b+='<tr><td>Medication</td><td><input type="text" class="location_tax" name="location_tax'+i+'[]" id="tax_med" value="0" onChange="checkTaxVal(this)" autocomplete="off" /></td></tr>';
			rowData_b+='<tr><td>Supplies</td><td><input type="text" class="location_tax" name="location_tax'+i+'[]" id="tax_supp" value="0" onChange="checkTaxVal(this)" autocomplete="off" /></td></tr>';
			rowData_b+='<tr><td>Others</td><td><input type="text" class="location_tax" name="location_tax'+i+'[]" id="tax_other" value="0" onChange="checkTaxVal(this)" autocomplete="off" /></td></tr>';
			/*rowData_b+='<tr><td>Remake</td><td><input type="text" class="location_tax" name="location_tax'+i+'[]" id="tax_remake" value="0" onChange="checkTaxVal(this)" autocomplete="off" /></td></tr>';*/
			rowData_b+='</table>';
			
			rowData_b+='<div class="btn_cls"><input type="button" name="save" value="Save" onClick="$(\'#tax_wrap'+i+'\').hide();"></div></div></div></div><!-- End tax div -->';
			
			rowData_b+='<div class="inputblock fl" style="width:100px; margin: 5px 0 0 15px;"><select name="fac_group'+i+'" style="width:139px;"><option value="">- Select Group -</option>';
			rowData_b+=group_opt;
			rowData_b+='</select><br/><span>Group</span></div>';
			rowData_b+='<br>';
			rowData_b+='<div class="inputblock fl" style="width:100%;  margin: 0 0 0 0; ">';
			rowData_b+='<select style="width:159px" name="idoc_fac'+i+'" id="idoc_fac'+i+'" class="text_10">';
			rowData_b+='<option value="">- Select Facility -</option>';
			rowData_b+='<?php
				$vquery_t = "select id, name from facility order by name asc";
				$vsql_t = imw_query($vquery_t);
				while($rs_t = imw_fetch_array($vsql_t)){
					echo("<option value=\"".$rs_t['id']."\">".$rs_t['name']."</option>");
				}
				?>';
			rowData_b+='</select><span style="color: #969696; font-size: 12px">(used to switch between optical and iDoc and to post charges over iDoc)</span>';
			rowData_b+='<br>';
			rowData_b+='<span>iDoc Facility</span></div>';
			rowData_b+='</td>';
			rowData_b+='</tr>';
			
			$("#tr_b_"+oldId).after(rowData_b);
			oldId=i;
		}
		document.getElementById('totRows').value= i-1;
	}
}

var rowData_c='';
var tr  = '';
function addrow()	
{
	var row_class='';
	var getRows = $('table.countrow tr[id^="tr_"]').size();
	
	if(getRows%2==0)
	{
		row_class = "even";
	}
	else
	{
		row_class = "odd";
	}
	
	y = getRows+1;
	var ph_format = "<?php echo $GLOBALS['phone_format'];?>";
	var manu_opt = '<?php echo $manu_opt; ?>';
	var group_opt = '<?php echo $gourp_opt;?>';
	
	rowData_c+='<tr id="tr_b_'+y+'">';
	rowData_c+='<td class="module_label '+row_class+'">';
	rowData_c+='<div class="inputblock fl" style="width:120px;"><input onchange="javascript:checkname(this)" type="text" style="width:119px;" name="add_loc_name'+y+'" id="add_vendor_name'+y+'" class="add_vendor_name" value="" /><br /><span>Location Name</span></div>';
	rowData_c+='<div class="inputblock fl" style="width:120px; margin:0 0 0 20px;"><input onchange="javascript:checkname(this)" type="text" style="width:119px;" name="add_contact_person'+y+'"  value="" /><br /><span>Contact Person</span></div>';
	rowData_c+='<div class="inputblock fl" style="width:120px; margin: 0 0 0 20px;"><input type="text" style="width:119px;" name="add_tel'+y+'" value="" onChange="set_phone_format(this,\''+ph_format+'\');" /><br /><span>Phone</span></div>';
	rowData_c+='<div class="inputblock fl" style="width:120px; margin: 0 0 0 20px;"><input type="text" style="width:119px;" name="add_fax'+y+'" value="" onChange="set_phone_format(this,\''+ph_format+'\',\'fax\');"  /><br /><span>Fax</span></div>';
	rowData_c+='<div class="inputblock fl" style="width:170px; margin: 0 0 0 20px;"><select name="fac_prac_code'+y+'" style="width:169px;"><option value="">- Select POS -</option>';
	rowData_c+=manu_opt;
	rowData_c+='</select><br/><span>POS</span></div>';
	rowData_c+='<div class="inputblock fl" style="width:180px; margin: 3px 0 0 15px;"><input type="file" name="logo'+y+'" value="" /><br /><span>Logo</span></div>';
	rowData_c+='<div class="inputblock fl" style="width:110px; margin: 5px 0 0 0;"><input type="text" style="width:109px;" name="add_address'+y+'" value="" /><br /><span>Address</span></div>';
	rowData_c+='<div class="inputblock fl" style="width:110px; margin: 5px 0 0 15px;"><input type="text" style="width:40px;" name="add_zip'+y+'" id="zip_'+y+'" onKeyUp="zip_vs_state(this,'+y+');" onblur="zip_vs_state_length(this,'+y+')" value="" maxlength="5"/>-<input type="text" style="width:40px; margin-left:2px;" name="add_zip_ext'+y+'" value="" maxlength="4"/><br /><span>Zip Code</span></div>';
	rowData_c+='<div class="inputblock fl" style="width:80px; margin: 5px 0 0 2px;"><input type="text" style="width:80px;" name="add_city'+y+'" id="city_'+y+'" value=""/><br /><span>City</span></div>';
	rowData_c+='<div class="inputblock fl" style="width:30px; margin: 5px 0 0 15px;"><input type="text" style="width:30px;" name="add_state'+y+'" id="state_'+y+'" value=""/><br /><span>State</span></div>';
	rowData_c+='<div class="inputblock fl" style="width:110px; margin: 5px 0 0 15px;"><input type="text" style="width:109px;" name="add_npi'+y+'" value="" /><br /><span>NPI</span></div>';
	rowData_c+='<div class="inputblock fl" style="width:63px; margin: 5px 0 0 15px;"><input type="text" style="width:60px;" name="add_tax_label'+y+'" value=""/><br /><span>Tax Label</span></div>';
	
	rowData_c+='<div class="inputblock fl " style="width:65px; margin: 5px 0 0 15px;"><a href="javascript:void(0);" onClick=\'$("#tax_wrap'+y+'").show();\' class="button">Tax(%)</a></div>';
	rowData_c+='<!-- tax div --><div id="tax_wrap'+y+'" class="tax_wrap"><div id="tax_div'+y+'" class="tax_div"><div class="module_border" style="overflow:hidden"><div class="listheading pl5">Tax(%) for Location<img onClick="$(\'#tax_wrap'+y+'\').hide();" src="<?php echo $GLOBALS['WEB_PATH']; ?>/images/del.png" style="margin-top:4px;margin-right:5px;float:right;cursor:pointer;" /></div>';
	rowData_c+='<table class="module_border" style="width:100%"><tr><td style="width:40%">Frames</td><td style="width:60%;"><input type="text" class="location_tax" name="location_tax'+y+'[]" id="tax_frame" value="0" onChange="checkTaxVal(this)" autocomplete="off" /></td></tr>';
	rowData_c+='<tr><td>Lenses</td><td><input type="text" class="location_tax" name="location_tax'+y+'[]" id="tax_lens" value="0" onChange="checkTaxVal(this)" autocomplete="off" /></td></tr>';
	rowData_c+='<tr><td>Contact Lenses</td><td><input type="text" class="location_tax" name="location_tax'+y+'[]" id="tax_cl" value="0" onChange="checkTaxVal(this)" autocomplete="off" /></td></tr>';
	rowData_c+='<tr><td>Medication</td><td><input type="text" class="location_tax" name="location_tax'+y+'[]" id="tax_med" value="0" onChange="checkTaxVal(this)" autocomplete="off" /></td></tr>';
	rowData_c+='<tr><td>Supplies</td><td><input type="text" class="location_tax" name="location_tax'+y+'[]" id="tax_supp" value="0" onChange="checkTaxVal(this)" autocomplete="off" /></td></tr>';
	rowData_c+='<tr><td>Others</td><td><input type="text" class="location_tax" name="location_tax'+y+'[]" id="tax_other" value="0" onChange="checkTaxVal(this)" autocomplete="off" /></td></tr>';
	/*rowData_c+='<tr><td>Remake</td><td><input type="text" class="location_tax" name="location_tax'+y+'[]" id="tax_remake" value="0" onChange="checkTaxVal(this)" autocomplete="off" /></td></tr>';*/
	rowData_c+='</table>';
	
	rowData_c+='<div class="btn_cls"><input type="button" name="save" value="Save" onClick="$(\'#tax_wrap'+y+'\').hide();"></div></div></div></div><!-- End tax div -->';
	
	rowData_c+='<div class="inputblock fl" style="width:100px; margin: 5px 0 0 15px;"><select name="fac_group'+y+'" style="width:139px;"><option value="">- Select Group -</option>';
	rowData_c+=group_opt;
	rowData_c+='</select><br/><span>Group</span></div>';
	rowData_c+='<br>';
	rowData_c+='<div class="inputblock fl" style="width:100%;  margin: 0 0 0 0; ">';
	rowData_c+='<select style="width:159px" name="idoc_fac'+i+'" id="idoc_fac'+i+'" class="text_10">';
	rowData_c+='<option value="">- Select Facility -</option>';
	rowData_c+='<?php
		$vquery_t = "select id, name from facility order by name asc";
		$vsql_t = imw_query($vquery_t);
		while($rs_t = imw_fetch_array($vsql_t)){
			echo("<option value=\"".$rs_t['id']."\">".$rs_t['name']."</option>");
		}
		?>';
	rowData_c+='</select><span style="color: #969696; font-size: 12px">(used to switch between optical and iDoc and to post charges over iDoc)</span>';
	rowData_c+='<br>';
	rowData_c+='<span>iDoc Facility</span></div>';
			
	rowData_c+='</td>';
	rowData_c+='</tr>';
	
	$("#tr_b_"+getRows).after(rowData_c); // ADD NEW ROW
	rowData_c='';
	if(getRows>=1)
	{
		$("#removebtn").show();
	}
	$("#totRows").val(y);
}

var remove_row='';
function removerow()
{
	remove_row = $('table.countrow tr[id^="tr_"]').size();
	
	if(remove_row>1)
	{
		if(remove_row==2)
		{
			$("#removebtn").hide();
		}
		
		$("#tr_b_"+remove_row).remove();
	}
	$("#totRows").val(remove_row-1);
}

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
<body style="overflow:hidden;">

    <div style="width:1000px; margin:0 auto; overflow:hidden;">
    <img id="loading_img" style="display:none; position:absolute; top:20%; left:45%;" src="../../../images/loading_image.gif" />
       <div class="module_border" style="overflow:hidden;">
        	<form name="addnew" id="firstform" action="" method="post" enctype="multipart/form-data">
            <input type="hidden" name="totRows" id="totRows" value="" />
        		<div class="listheading pl5">
                	Add Location Information
                </div>
				
                <div style="height:220px; overflow-x:hidden; overflow-y:scroll">
                <table class="table_collapse countrow table_cell_padd5 module_border">
					<tr id="tr_b_1">
						<td class="module_label even">
							<div class="inputblock fl" style="width:120px;"><input onChange="javascript:checkname(this)" type="text" style="width:119px;" name="add_loc_name1" class="add_vendor_name" id="add_loc_name1" value="" /><br />
							<span>Location Name</span></div>
							
							<div class="inputblock fl" style="width:120px; margin: 0 0 0 20px;"><input onChange="javascript:checkname(this)" type="text" style="width:119px;" name="add_conatct_person1" value="" /><br />
							<span>Contact Person</span></div>
							
							<div class="inputblock fl" style="width:120px; margin: 0 0 0 20px;"><input type="text" style="width:119px;" name="add_tel1" value="" onChange="set_phone_format(this,'<?php echo $GLOBALS['phone_format'];?>');" /><br />
							<span>Phone</span></div>
							
							<div class="inputblock fl" style="width:120px; margin: 0 0 0 20px;"><input type="text" style="width:119px;" name="add_fax1" value="" onChange="set_phone_format(this,'<?php echo $GLOBALS['phone_format'];?>','fax');" /><br />
							<span>Fax</span></div>
							
							<div class="inputblock fl" style="width:120px; margin: 0 0 0 20px;">
								<select style="width:169px" name="fac_prac_code1" id="fac_prac_code" class="text_10">
									<option value="">- Select POS -</option>
									<?php
									$vquery_t = "select a.pos_facility_id,a.facilityPracCode,b.pos_prac_code from pos_facilityies_tbl a,pos_tbl b
												where a.pos_id = b.pos_id order by facilityPracCode asc";
									$vsql_t = imw_query($vquery_t);
									while($rs_t = imw_fetch_array($vsql_t)){
										$se="";
										if($row['pos']==$rs_t['pos_facility_id']){
											$se="selected";
										}
										$show_prac_code = (strlen($rs_t['facilityPracCode']) > 18) ? substr($rs_t['facilityPracCode'], 0 , 15)."..." : $rs_t['facilityPracCode'];
							
										echo("<option ".$se." value='".$rs_t['pos_facility_id']."'>".$show_prac_code." - ".$rs_t['pos_prac_code']."</option>");
									}
									?>
								</select>
								<br />
								<span>POS</span>
							</div>	
							
                            <div class="inputblock fl" style="width:180px; margin: 3px 0 0 65px;"><input type="file" name="logo1" value="" /><br />
							<span>Logo</span></div>
                            
							<div class="inputblock fl" style="width:110px; margin: 5px 0 0 0;"><input type="text" style="width:109px;" name="add_address1" value="" /><br />
							<span>Address</span></div>
							
							<div class="inputblock fl" style="width:110px; margin: 5px 0 0 15px;"><input type="text" style="width:40px;" name="add_zip1" id="zip_1" onKeyUp="zip_vs_state(this,'1');" onBlur="zip_vs_state_length(this,'1')" value="" maxlength="5"/>-<input type="text" style="width:40px; margin-left:2px;" name="add_zip_ext1" value="" maxlength="4"/><br />
							<span>Zip Code</span></div>
							
							<div class="inputblock fl" style="width:80px; margin: 5px 0 0 2px;">
                            <input type="text" style="width:80px;" name="add_city1" id="city_1" value=""/><br />
							<span>City</span></div>
							
							<div class="inputblock fl" style="width:30px; margin: 5px 0 0 15px;">
                            <input type="text" style="width:30px;" name="add_state1" id="state_1" value=""/><br />
							<span>State</span></div>
							
							<div class="inputblock fl" style="width:110px; margin: 5px 0 0 15px;">
                            <input type="text" style="width:109px;" name="add_npi1" value="" /><br />
							<span>NPI</span></div> 
							
							<div class="inputblock fl " style="width:63px; margin: 5px 0 0 15px;">
                            <input type="text" style="width:60px;" name="add_tax_label1" value=""/><br />
							<span>Tax Label</span></div>

							<div class="inputblock fl " style="width:65px; margin: 5px 0 0 15px;">
                           		<a href="javascript:void(0);" onClick='$("#tax_wrap1").show();' class="button">Tax(%)</a>
							</div>
<!-- tax div -->
	<div id="tax_wrap1" class="tax_wrap">
		<div id="tax_div1" class="tax_div">
			<div class="module_border" style="overflow:hidden">
				<div class="listheading pl5">
					Tax(%) for Location
					<img onClick="$('#tax_wrap1').hide();" src="<?php echo $GLOBALS['WEB_PATH']; ?>/images/del.png" style="margin-top:4px;margin-right:5px;float:right;cursor:pointer;" />
				</div>
				<table class="module_border" style="width:100%">
					<tr>
						<td style="width:40%">Frames</td>
						<td style="width:60%;">
							<input type="text" class="location_tax" name="location_tax1[]" id="tax_frame" value="0" onChange="checkTaxVal(this)" autocomplete="off" />
						</td>
					</tr>
					<tr>
						<td>Lenses</td>
						<td>
							<input type="text" class="location_tax" name="location_tax1[]" id="tax_lens" value="0" onChange="checkTaxVal(this)" autocomplete="off" />
						</td>
					</tr>
					<tr>
						<td>Contact Lenses</td>
						<td>
							<input type="text" class="location_tax" name="location_tax1[]" id="tax_cl" value="0" onChange="checkTaxVal(this)" autocomplete="off" />
						</td>
					</tr>
					<tr>
						<td>Medication</td>
						<td>
							<input type="text" class="location_tax" name="location_tax1[]" id="tax_med" value="0" onChange="checkTaxVal(this)" autocomplete="off" />
						</td>
					</tr>
					<tr>
						<td>Supplies</td>
						<td>
							<input type="text" class="location_tax" name="location_tax1[]" id="tax_supp" value="0" onChange="checkTaxVal(this)" autocomplete="off" />
						</td>
					</tr>
					<tr>
						<td>Others</td>
						<td>
							<input type="text" class="location_tax" name="location_tax1[]" id="tax_other" value="0" onChange="checkTaxVal(this)" autocomplete="off" />
						</td>
					</tr>
					<!--<tr>
						<td>Remake</td>
						<td>
							<input type="text" class="location_tax" name="location_tax1[]" id="tax_remake" value="0" onChange="checkTaxVal(this)" autocomplete="off" />
						</td>
					</tr>-->
				</table>
				<div class="btn_cls">
					<input type="button" name="save" value="Save" onClick="$('#tax_wrap1').hide();">
				</div>
			</div>
		</div>  
	</div>
<!-- End tax div -->
                            <div class="inputblock fl" style="width:100px; margin: 5px 0 0 15px;">
                                <select style="width:139px" name="fac_group1" class="text_10" onChange="selectCurrentCheck('<?php echo $i; ?>');">
                                    <option value="">- Select Group -</option>
                                    <?php
                                    $vquery_t = "select * from groups_new order by name asc";
                                    $vsql_t = imw_query($vquery_t);
                                    while($rs_t = imw_fetch_array($vsql_t)){
                                        echo("<option value='".$rs_t['gro_id']."'>".$rs_t['name']."</option>");
                                    }
                                    ?>
                                </select>
							<span>Group</span></div>
                            <br>
							<div class="inputblock fl" style="width:100%;  margin: 0 0 0 0; ">
							<select style="width:159px" name="idoc_fac1" id="idoc_fac" class="text_10">
								<option value="">- Select Facility -</option>
								<?php
								$vquery_t = "select id, name from facility order by name asc";
								$vsql_t = imw_query($vquery_t);
								while($rs_t = imw_fetch_array($vsql_t)){
									echo("<option value='".$rs_t['id']."'>".$rs_t['name']."</option>");
								}
								?>
							</select><span style="color: #969696; font-size: 12px">(used to switch between optical and iDoc and to post charges over iDoc)</span>
							<br>
							<span>iDoc Facility</span></div>
                            
                            
						</td>
					</tr>                                          
				</table> 
                </div>
                <div style="float:right; padding-top:14px;">
                    <img onClick="addrow();" style="cursor:pointer;" src="../../../images/addrow.png" /> <img style="cursor:pointer;" id="removebtn" onClick="removerow();" src="../../../images/removerow.png" />
				</div>  
                <div class="btn_cls">
                    <input type="submit" name="save" value="Save" />
                </div>
           </form>
        </div>
    </div>
<script>
$(document).ready(function(){
	createRows(1,1);
	document.getElementById('add_loc_name1').focus();
});
</script>

<script type="text/javascript">
$(document).ready(function()
{

checkname = function(item_name)
{
	$(".add_vendor_name").each(function(index)
	{
		if($.trim($(this).val())!="" && $.trim($(this).val()).toLowerCase() == $.trim($(item_name).val()).toLowerCase() && $(this).attr('id') != $(item_name).attr('id'))
		{
			top.falert($(item_name).val()+' Already Exist');
			$(item_name).val('');
			setTimeout(function(){$(item_name).focus(); },0);
		}	
	});

}

});
</script> 
</body>
</html>