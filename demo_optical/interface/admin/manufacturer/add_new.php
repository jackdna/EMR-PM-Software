<?php
	/*
	File: add_new.php
	Coded in PHP7
	Purpose: Add New Manufacture
	Access Type: Direct access
	*/
	require_once("../../../config/config.php");
	require_once("../../../library/classes/functions.php");
	$opr_id = $_SESSION['authId'];
	$date = date("Y-m-d");
	$time = date("h:i:s");
	//---------- ADD NEW MANUFACTURE------------//
	if($_REQUEST['save']=="Save")
	{
		for($i=1; $i<=$_POST['totRows']; $i++)
		{
			if(trim($_POST['add_manufacturer_name'.$i])!="")
			{
				$sel_manu = "select id, manufacturer_name from in_manufacturer_details where del_status!='2' and manufacturer_name = '".$_POST['add_manufacturer_name'.$i]."'";
				$sel_res = imw_query($sel_manu);
				$sel_num = imw_num_rows($sel_res);
				if($sel_num == 0)
				{					
					$insert_query = "insert in_manufacturer_details set 
					manufacturer_name = '".imw_real_escape_string(trim($_POST['add_manufacturer_name'.$i]))."',
					fax = '".imw_real_escape_string(trim($_POST['add_fax'.$i]))."',
					tel_num = '".imw_real_escape_string(preg_replace('/[^0-9]/','',$_POST['add_tel'.$i]))."',
					mobile = '".imw_real_escape_string(preg_replace('/[^0-9]/','',trim($_POST['add_mobile'.$i])))."',
					manufacturer_address = '".imw_real_escape_string(trim($_POST['add_address'.$i]))."',
					email = '".imw_real_escape_string(trim($_POST['add_email'.$i]))."',
					frames_chk = '".$_POST['add_manu_frames'.$i]."',
					lenses_chk = '".$_POST['add_manu_lenses'.$i]."',
					cont_lenses_chk = '".$_POST['add_manu_cont_lenses'.$i]."',
					supplies_chk = '".$_POST['add_manu_supplies'.$i]."',
					medicine_chk = '".$_POST['add_manu_medicine'.$i]."',
					accessories_chk = '".$_POST['add_manu_accessories'.$i]."',
					city = '".imw_real_escape_string($_POST['add_city'.$i])."',
					zip = '".imw_real_escape_string($_POST['add_zip'.$i])."',
					zip_ext = '".imw_real_escape_string($_POST['add_zip_ext'.$i])."',
					state = '".imw_real_escape_string($_POST['add_state'.$i])."',
					entered_date='$date', 
					entered_time='$time', 
					entered_by='$opr_id'					
					";
					
					$run = imw_query($insert_query);
					$manufact_id = imw_insert_id();
					
					if($_POST['copy_vendor'.$i]=="on")
					{
						$sel_vend = "select id, vendor_name from in_vendor_details where del_status!='2' and vendor_name = '".$_POST['add_manufacturer_name'.$i]."'";
						$sel_res_vend = imw_query($sel_vend);
						$sel_num_vend = imw_num_rows($sel_res_vend);
						$vend_id = "";
						if($sel_num_vend > 0)
						{
							$vend_row = imw_fetch_assoc($sel_res_vend);
							$act = "update";
							$whr = ", modified_date='$date', modified_time='$time', modified_by='$opr_id'  where id='".$vend_row['id']."'";
							$vend_id = $vend_row['id'];
						}
						else
						{
							$act = "insert";
							$whr = ", entered_date='$date', entered_time='$time', entered_by='$opr_id'";
						}
						$vendor_query = "$act in_vendor_details set 
						vendor_name = '".imw_real_escape_string(trim($_POST['add_manufacturer_name'.$i]))."',
						fax = '".imw_real_escape_string(trim($_POST['add_fax'.$i]))."',
						tel_num = '".imw_real_escape_string(preg_replace('/[^0-9]/','',trim($_POST['add_tel'.$i])))."',
						mobile = '".imw_real_escape_string(preg_replace('/[^0-9]/','',trim($_POST['add_mobile'.$i])))."',
						vendor_address = '".imw_real_escape_string(trim($_POST['add_address'.$i]))."',
						email = '".imw_real_escape_string(trim($_POST['add_email'.$i]))."',
						city = '".imw_real_escape_string(trim($_POST['add_city'.$i]))."',
						zip = '".imw_real_escape_string(trim($_POST['add_zip'.$i]))."',					
						zip_ext = '".imw_real_escape_string(trim($_POST['add_zip_ext'.$i]))."',
						state = '".imw_real_escape_string(trim($_POST['add_state'.$i]))."'
						$whr";
						
						$run_vendor = imw_query($vendor_query);
						if($vend_id=="")
						{
							$vend_id = imw_insert_id();
						}							
						$insert_vendor_manu = imw_query("insert in_vendor_manufacture set vendor_id='".$vend_id."', manufacture_id = '".$manufact_id."'");
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
<script src="../../../library/js/jquery-1.10.1.min.js?<?php echo constant("cache_version"); ?>"></script>
<script src="../../../library/js/common.js?<?php echo constant("cache_version"); ?>"></script>
<script>
function createRows(startNum,NoOfRows)
{
	var rowData_b='';
	var newNum = parseInt(startNum) + 1;
	var totalrows='';
	var row_class='';
	var ph_format = "<?php echo $GLOBALS['phone_format'];?>";
	
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
			rowData_b+='<div class="inputblock fl" style="width:160px; margin: 0 0 0 0px;"><input onchange="javascript:checkname(this)" type="text" style="width:155px;" class="add_manufacturer_name" name="add_manufacturer_name'+i+'"  value="" /><br /><span>Manufacturer Name</span></div>';
			rowData_b+='<div class="inputblock fl" style="width:135px; margin: 0 0 0 20px;"><input type="text" style="width:134px;" name="add_fax'+i+'" value="" onChange="set_phone_format(this,\''+ph_format+'\',\'fax\');" /><br /><span>Fax</span></div>';
			rowData_b+='<div class="inputblock fl" style="width:135px; margin: 0 0 0 20px;"><input type="text" style="width:134px;" name="add_tel'+i+'" value="" onChange="set_phone_format(this,\''+ph_format+'\');" /><br /><span>Tel</span></div>';
			rowData_b+='<div class="inputblock fl" style="width:135px; margin: 0 0 0 20px;"><input type="text" style="width:134px;" name="add_mobile'+i+'" value="" onChange="set_phone_format(this,\''+ph_format+'\');"/><br /><span>Mobile</span></div>';
			rowData_b+='<div class="inputblock fl" style="width:135px; margin: 0 0 0 20px;"><input type="text" style="width:134px;" name="add_email'+i+'" value="" /><br /><span>Email</span></div>';
			rowData_b+='<div class="inputblock fl" style="width:320px; margin: 5px 20px 0 0;"><input type="text" style="width:315px;" name="add_address'+i+'" value="" /><br /><span>Address</span></div>';
			rowData_b+='<div class="inputblock fl" style="width:135px; margin: 5px 0 0 0;"><input type="text" style="width:55px;" name="add_zip'+i+'" id="zip_'+i+'" onKeyUp="zip_vs_state(this,'+i+');" onblur="zip_vs_state_length(this,'+i+')"  value="" maxlength="5"/><input type="text" style="width:55px; margin-left:4px;" name="add_zip_ext'+i+'" value="" maxlength="4"/><br /><span>Zip Code</span></div>';
			rowData_b+='<div class="inputblock fl" style="width:135px; margin: 5px 0 0 20px;"><input type="text" style="width:134px;" name="add_city'+i+'" id="city_'+i+'" value="" /><br /><span>City</span></div>';
			
			rowData_b+='<div class="inputblock fl" style="width:135px; margin: 5px 0 0 20px;"><input value="" type="text" style="width:134px;" name="add_state'+i+'" id="state_'+i+'" /><br /><span>State</span></div>';
			rowData_b+='<div class="inputblock fl" style="width:80px; margin: 5px 0 0 0px;"><input type="checkbox" name="add_manu_frames'+i+'" value="1"><span>Frames</span></div><div class="inputblock fl" style="width:80px; margin: 5px 0 0 5px;"><input type="checkbox" name="add_manu_lenses'+i+'" value="1"><span>Lenses</span></div><div class="inputblock fl" style="width:90px; margin: 5px 0 0 5px;"><input type="checkbox" name="add_manu_cont_lenses'+i+'" value="1"><span>Contacts</span></div><div class="inputblock fl" style="width:90px; margin: 5px 0 0 5px;"><input type="checkbox" name="add_manu_supplies'+i+'" value="1"><span>Supplies</span></div><div class="inputblock fl" style="width:100px; margin: 5px 0 0 5px;"><input type="checkbox" name="add_manu_medicine'+i+'" value="1"><span>Medicines</span></div><div class="inputblock fl" style="width:110px; margin: 5px 0 0 5px;"><input type="checkbox" name="add_manu_accessories'+i+'" value="1"><span>Accessories</span></div><div class="inputblock fl" style="width:140px; margin: 5px 0 0 5px;"><input type="checkbox" name="copy_vendor'+i+'"><span>Copy to vendor</span></div><div style="clear:both; margin-left:3px;">Manufacturer Type</div>';
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
	var ph_format = "<?php echo $GLOBALS['phone_format'];?>";
	var getRows = $(".countrow tr").size();
	if(getRows%2==0)
	{
		row_class = "even";
	}
	else
	{
		row_class = "odd";
	}
	
	y = getRows+1;
	
	rowData_c+='<tr id="tr_b_'+y+'">';	
	rowData_c+='<td class="module_label '+row_class+'">';
	rowData_c+='<div class="inputblock fl" style="width:160px; margin: 0 0 0 0px;"><input onchange="javascript:checkname(this)" type="text" style="width:155px;" class="add_manufacturer_name" name="add_manufacturer_name'+y+'" value=""  /><br /><span>Manufacturer Name</span></div>';
	rowData_c+='<div class="inputblock fl" style="width:135px; margin: 0 0 0 20px;"><input type="text" style="width:134px;" name="add_fax'+y+'" value="" onChange="set_phone_format(this,\''+ph_format+'\',\'fax\');" /><br /><span>Fax</span></div>';
	rowData_c+='<div class="inputblock fl" style="width:135px; margin: 0 0 0 20px;"><input type="text" style="width:134px;" name="add_tel'+y+'" value="" onChange="set_phone_format(this,\''+ph_format+'\');" /><br /><span>Tel</span></div>';
	rowData_c+='<div class="inputblock fl" style="width:135px; margin: 0 0 0 20px;"><input type="text" style="width:134px;" name="add_mobile'+y+'" value="" onChange="set_phone_format(this,\''+ph_format+'\');"/><br /><span>Mobile</span></div>';
	rowData_c+='<div class="inputblock fl" style="width:135px; margin: 0 0 0 20px;"><input type="text" style="width:134px;" name="add_email'+y+'" value="" /><br /><span>Email</span></div>';
	rowData_c+='<div class="inputblock fl" style="width:320px; margin: 5px 20px 0 0;"><input type="text" style="width:315px;" name="add_address'+y+'" value="" /><br /><span>Address</span></div>';
	rowData_c+='<div class="inputblock fl" style="width:135px; margin: 5px 0 0 0;"><input type="text" style="width:55px;" name="add_zip'+y+'" id="zip_'+y+'" onKeyUp="zip_vs_state(this,'+y+');" onblur="zip_vs_state_length(this,'+y+')" value="" maxlength="5"/><input type="text" style="width:55px; margin-left:4px;" name="add_zip_ext'+y+'" value="" maxlength="4"/><br /><span>Zip Code</span></div>';
	rowData_c+='<div class="inputblock fl" style="width:135px; margin: 5px 0 0 20px;"><input type="text" style="width:134px;" name="add_city'+y+'" id="city_'+y+'" value="" /><br /><span>City</span></div>';
	rowData_c+='<div class="inputblock fl" style="width:135px; margin: 5px 0 0 20px;"><input value="" type="text" style="width:134px;" name="add_state'+y+'" id="state_'+y+'" /><br /><span>State</span></div>';
	rowData_c+='<div class="inputblock fl" style="width:80px; margin: 5px 0 0 0px;"><input type="checkbox" name="add_manu_frames'+y+'" value="1"><span>Frames</span></div><div class="inputblock fl" style="width:80px; margin: 5px 0 0 5px;"><input type="checkbox" name="add_manu_lenses'+y+'" value="1"><span>Lenses</span></div><div class="inputblock fl" style="width:90px; margin: 5px 0 0 5px;"><input type="checkbox" name="add_manu_cont_lenses'+y+'" value="1"><span>Contacts</span></div><div class="inputblock fl" style="width:90px; margin: 5px 0 0 5px;"><input type="checkbox" name="add_manu_supplies'+y+'" value="1"><span>Supplies</span></div><div class="inputblock fl" style="width:100px; margin: 5px 0 0 5px;"><input type="checkbox" name="add_manu_medicine'+y+'" value="1"><span>Medicines</span></div><div class="inputblock fl" style="width:110px; margin: 5px 0 0 5px;"><input type="checkbox" name="add_manu_accessories'+y+'" value="1"><span>Accessories</span></div><div class="inputblock fl" style="width:140px; margin: 5px 0 0 5px;"><input type="checkbox" name="copy_vendor'+y+'"><span>Copy to vendor</span></div><div style="clear:both; margin-left:3px;">Manufacturer Type</div>';
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
	remove_row = $(".countrow tr").size();
	
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
<body>

    <div style="width:840px; margin:0 auto;">
    <img id="loading_img" style="display:none; position:absolute; top:20%; left:45%;" src="../../../images/loading_image.gif" />
       <div class="module_border">
        	<form name="addnew" id="firstform" action="" method="post">
            <input type="hidden" name="totRows" id="totRows" value="" />
        		<div class="listheading pl5">
                	Add Manufacturer Information
                </div>
				
                <div style="height:250px; overflow-x:hidden; overflow-y:scroll">
                <table class="table_collapse countrow table_cell_padd5 module_border">
					<tr id="tr_b_1">
						<td class="module_label even">                            
                    
							<div class="inputblock fl" style="width:160px; margin: 0 0 0 0px;"><input onChange="javascript:checkname(this)" type="text" style="width:155px;" name="add_manufacturer_name1" class="add_manufacturer_name" id="add_manufacturer_name1" value="" /><br />
							<span>Manufacturer Name</span></div>
							
							<div class="inputblock fl" style="width:135px; margin: 0 0 0 20px;"><input type="text" style="width:134px;" name="add_fax1" value="" onChange="set_phone_format(this,'<?php echo $GLOBALS['phone_format'];?>','fax');" /><br />
							<span>Fax</span></div>
							
							<div class="inputblock fl" style="width:135px; margin: 0 0 0 20px;"><input type="text" style="width:134px;" name="add_tel1" value="" onChange="set_phone_format(this,'<?php echo $GLOBALS['phone_format'];?>');" /><br />
							<span>Tel</span></div>
							
							<div class="inputblock fl" style="width:135px; margin: 0 0 0 20px;"><input type="text" style="width:134px;" name="add_mobile1" value="" onChange="set_phone_format(this,'<?php echo $GLOBALS['phone_format'];?>');"/><br />
							<span>Mobile</span></div>
							
							<div class="inputblock fl" style="width:135px; margin: 0 0 0 20px;"><input type="text" style="width:134px;" name="add_email1" value="" /><br />
							<span>Email</span></div>
							
							<div class="inputblock fl" style="width:320px; margin: 5px 20px 0 0;"><input type="text" style="width:315px;" name="add_address1" value="" /><br />
							<span>Address</span></div>
							
							<div class="inputblock fl" style="width:135px; margin: 5px 0 0 0;"><input type="text" style="width:55px;" name="add_zip1" id="zip_1" onKeyUp="zip_vs_state(this,'1');" onBlur="zip_vs_state_length(this,'1')" value="" maxlength="5"/><input type="text" style="width:58px; margin-left:4px;" name="add_zip_ext1" value="" maxlength="4"/><br />
							<span>Zip Code</span></div>
							
							<div class="inputblock fl" style="width:135px; margin: 5px 0 0 20px;"><input type="text" style="width:134px;" name="add_city1" id="city_1" value="" /><br />
							<span>City</span></div>
							
							<div class="inputblock fl" style="width:135px; margin: 5px 0 0 20px;"><input value="" type="text" style="width:134px;" name="add_state1" id="state_1" /><br /><span>State</span></div>
							
							<div class="inputblock fl" style="width:80px; margin: 5px 0 0 0px;"><input type="checkbox" name="add_manu_frames1" value="1"><span>Frames</span></div>
							<div class="inputblock fl" style="width:80px; margin: 5px 0 0 5px;"><input type="checkbox" name="add_manu_lenses1" value="1"><span>Lenses</span></div>
							<div class="inputblock fl" style="width:90px; margin: 5px 0 0 5px;"><input type="checkbox" name="add_manu_cont_lenses1" value="1"><span>Contacts</span></div>
							<div class="inputblock fl" style="width:90px; margin: 5px 0 0 5px;"><input type="checkbox" name="add_manu_supplies1" value="1"><span>Supplies</span></div>
							<div class="inputblock fl" style="width:100px; margin: 5px 0 0 5px;"><input type="checkbox" name="add_manu_medicine1" value="1"><span>Medicines</span></div>
							<div class="inputblock fl" style="width:110px; margin: 5px 0 0 5px;"><input type="checkbox" name="add_manu_accessories1" value="1"><span>Accessories</span></div>	
							<div class="inputblock fl" style="width:140px; margin: 5px 0 0 5px;"><input type="checkbox" name="copy_vendor1"><span>Copy to vendor</span></div>	
						<div style="clear:both; margin-left:3px;">Manufacturer Type</div>
						</td>
					</tr>                                          
				</table>
                </div>
                <div style="float:right; padding-top:14px;">
                    <img onClick="addrow();" style="cursor:pointer;" src="../../../images/addrow.png" /> 
                    <img style="cursor:pointer;" id="removebtn" onClick="removerow();" src="../../../images/removerow.png" />
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
	document.getElementById('add_manufacturer_name1').focus();
});
</script>

<script type="text/javascript">

$(document).ready(function()
{

checkname = function(item_name)
{
	$(".add_manufacturer_name").each(function(index)
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