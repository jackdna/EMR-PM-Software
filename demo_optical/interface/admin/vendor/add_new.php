<?php
	/*
	File: add_new.php
	Coded in PHP7
	Purpose: Add New Vendor
	Access Type: Direct access
	*/
	require_once("../../../config/config.php");
	require_once("../../../library/classes/functions.php");
	$opr_id = $_SESSION['authId'];
	$date = date("Y-m-d");
	$time = date("h:i:s"); 
	
	$sel_manu = "select id, manufacturer_name from in_manufacturer_details where del_status='0' order by manufacturer_name asc";
	$sel_res = imw_query($sel_manu);
	$sel_num = imw_num_rows($sel_res);
	if($sel_num > 0)
	{
		while($sel_row = imw_fetch_array($sel_res))
		{
			$manu_opt .= '<option value="'.$sel_row['id'].'">'.htmlentities($sel_row['manufacturer_name'],ENT_QUOTES).'</option>'; 	 
		}
	}
	//--------- INSERT NEW VENDOR-------------//
	if($_REQUEST['save']=="Save")
	{
		for($i=1; $i<=$_POST['totRows']; $i++)
		{
			if(trim($_POST['add_vendor_name'.$i])!="")
			{
			$sel_manu = "select id, vendor_name from in_vendor_details where del_status!='2' and vendor_name = '".$_POST['add_vendor_name'.$i]."'";
			$sel_res = imw_query($sel_manu);
			$sel_num = imw_num_rows($sel_res);
			if($sel_num == 0)
			{			
				$insert_query = "insert in_vendor_details set 
				vendor_name = '".imw_real_escape_string(trim($_POST['add_vendor_name'.$i]))."',
				vendor_id = '".imw_real_escape_string(trim($_POST['add_vendor_id'.$i]))."',
				second_vendor_id = '".imw_real_escape_string(trim($_POST['add_second_vendor_id'.$i]))."',
				fax = '".imw_real_escape_string(trim($_POST['add_fax'.$i]))."',
				tel_num = '".imw_real_escape_string(preg_replace('/[^0-9]/','',trim($_POST['add_tel'.$i])))."',
				mobile = '".imw_real_escape_string(preg_replace('/[^0-9]/','',trim($_POST['add_mobile'.$i])))."',
				vendor_address = '".imw_real_escape_string(trim($_POST['add_address'.$i]))."',
				email = '".imw_real_escape_string(trim($_POST['add_email'.$i]))."',
				city = '".imw_real_escape_string(trim($_POST['add_city'.$i]))."',
				zip = '".imw_real_escape_string(trim($_POST['add_zip'.$i]))."',					
				zip_ext = '".imw_real_escape_string(trim($_POST['add_zip_ext'.$i]))."',
				state = '".imw_real_escape_string(trim($_POST['add_state'.$i]))."',
				sales_rep_fname='".imw_real_escape_string(trim($_POST['sales_rep_fname'.$i]))."',
				sales_rep_mname='".imw_real_escape_string(trim($_POST['sales_rep_mname'.$i]))."',
				sales_rep_lname='".imw_real_escape_string(trim($_POST['sales_rep_lname'.$i]))."',
				sales_rep_work_no='".imw_real_escape_string(trim($_POST['sales_rep_work_no'.$i]))."',
				sales_rep_cell_no='".imw_real_escape_string(trim($_POST['sales_rep_cell_no'.$i]))."',
				sales_rep_email='".imw_real_escape_string(trim($_POST['sales_rep_email'.$i]))."',
				entered_date='$date', 
				entered_time='$time', 
				entered_by='$opr_id'
				";
				
				$run = imw_query($insert_query);
				$last_insert = imw_insert_id();
				
				if($_POST['manufac_name'.$i]!='')
				{
					for($k=0;$k<count($_POST['manufac_name'.$i]);$k++)
					{
						$vendor_qry = "insert in_vendor_manufacture set vendor_id = '".$last_insert."', manufacture_id = '".imw_real_escape_string($_POST['manufac_name'.$i][$k])."' ";
					imw_query($vendor_qry);
					}
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
<link rel="stylesheet" href="<?php echo $GLOBALS['WEB_PATH'];?>/library/css/jquery.multiSelect.css?<?php echo constant("cache_version"); ?>" />
<script src="../../library/js/jquery-1.10.1.min.js?<?php echo constant("cache_version"); ?>"></script>
<script src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/jquery.js?<?php echo constant("cache_version"); ?>"></script> 
<script type="text/javascript" src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/jquery.multiSelect.js?<?php echo constant("cache_version"); ?>"></script>
<!--for fancy models-->
<script src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/jquery.modal.js?<?php echo constant("cache_version"); ?>"></script>
<script src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/jquery.modal.function.js?<?php echo constant("cache_version"); ?>"></script>
<!--for fancy models end here-->
<script src="../../../library/js/common.js?<?php echo constant("cache_version"); ?>"></script>
<script>
function createRows(startNum,NoOfRows)
{
	var rowData_b='';
	var newNum = parseInt(startNum) + 1;
	var totalrows='';
	var row_class='';
	var ph_format = "<?php echo $GLOBALS['phone_format'];?>";
	var manu_opt = '<?php echo $manu_opt; ?>';
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
			rowData_b+='<div class="inputblock fl" style="width:120px;"><input onchange="javascript:checkname(this)" type="text" style="width:119px;" name="add_vendor_name'+i+'" id="add_vendor_name'+i+'" class="add_vendor_name" value="" /><br /><span>Vendor Name</span></div>';
			rowData_b+='<div class="inputblock fl" style="width:70px; margin:0 0 0 20px"><input onChange="javascript:checkname(this)" type="text" style="width:69px;" name="add_vendor_id'+i+'" value="" /><br /><span>Vendor Id</span></div>';
			rowData_b+='<div class="inputblock fl" style="width:110px; margin:0 0 0 20px"><input onChange="javascript:checkname(this)" type="text" style="width:109px;" name="add_second_vendor_id'+i+'" value="" /><br /><span>Second Id</span></div>';
			rowData_b+='<div class="inputblock fl" style="width:110px; margin: 0 0 0 20px;"><input type="text" style="width:109px;" name="add_fax'+i+'" value="" onChange="set_phone_format(this,\''+ph_format+'\',\'fax\');"  /><br /><span>Fax</span></div>';
			rowData_b+='<div class="inputblock fl" style="width:110px; margin: 0 0 0 20px;"><input type="text" style="width:109px;" name="add_tel'+i+'" value="" onChange="set_phone_format(this,\''+ph_format+'\');" /><br /><span>Tel</span></div>';
			rowData_b+='<div class="inputblock rptDropDown fl" style="width:110px; margin: 0 0 0 20px;"><select name="manufac_name'+i+'" style="width:109px;" id="manufac_name'+i+'" multiple="multiple"><option value="">Select</option>';
			rowData_b+=manu_opt;
			rowData_b+='</select><br/><span style="font-family:wf_SegoeUILight;font-size:1.300em;">Manufacturer</span></div>';
			rowData_b+='<div class="inputblock cb fl" style="width:120px; margin: 5px 0 0 0;"><input type="text" style="width:119px;" name="add_address'+i+'" value="" /><br /><span>Address</span></div>';
			rowData_b+='<div class="inputblock fl" style="width:70px; margin: 5px 0 0 20px;"><input type="text" style="width:69px;" name="add_mobile'+i+'" value="" onChange="set_phone_format(this,\''+ph_format+'\');"/><br /><span>Mobile</span></div>';
			rowData_b+='<div class="inputblock fl" style="width:120px; margin: 5px 0 0 20px;"><input type="text" style="width:45px;" name="add_zip'+i+'" id="zip_'+i+'" onKeyUp="zip_vs_state(this,'+i+');" onblur="zip_vs_state_length(this,'+i+')" value="" maxlength="5"/>-<input type="text" style="width:45px; margin-left:2px;" name="add_zip_ext'+i+'" value="" maxlength="4"/><br /><span>Zip Code</span></div>';
			rowData_b+='<div class="inputblock fl" style="width:110px; margin: 5px 0 0 10px;"><input type="text" style="width:109px;" name="add_city'+i+'" id="city_'+i+'" value=""/><br /><span>City</span></div>';
			rowData_b+='<div class="inputblock fl" style="width:110px; margin: 5px 0 0 20px;"><input type="text" style="width:109px;" name="add_state'+i+'" id="state_'+i+'" value=""/><br /><span>State</span></div>';
			rowData_b+='<div class="inputblock fl" style="width:120px; margin: 5px 0 0 20px;"><input type="text" style="width:119px;" name="add_email'+i+'" value="" /><br /><span>Email</span></div>';

			rowData_b+='<div class="inputblock fl" style="width:80px; margin: 5px 0 0 0px;"><span style="font-weight:bold;">Sales Rep:</span></div>';
			rowData_b+='<div class="inputblock fl" style="width:90px; margin: 5px 0 0 0px;"><input type="text" style="width:89px;" name="sales_rep_fname'+i+'" value="" /><br /><span>First Name</span></div>';
			rowData_b+='<div class="inputblock fl" style="width:95px; margin: 5px 0 0 20px;"><input type="text" style="width:89px;" name="sales_rep_mname'+i+'" value="" /><br /><span>Middle Name</span></div>';
			rowData_b+='<div class="inputblock fl" style="width:90px; margin: 5px 0 0 20px;"><input type="text" style="width:89px;" name="sales_rep_lname'+i+'" value="" /><br /><span>Last Name</span></div>';
			rowData_b+='<div class="inputblock fl" style="width:90px; margin: 5px 0 0 20px;"><input type="text" style="width:89px;" name="sales_rep_work_no'+i+'" value=""  onChange="set_phone_format(this,\''+ph_format+'\');"/><br /><span>Work#</span></div>';
			rowData_b+='<div class="inputblock fl" style="width:90px; margin: 5px 0 0 20px;"><input type="text" style="width:89px;" name="sales_rep_cell_no'+i+'" value=""  onChange="set_phone_format(this,\''+ph_format+'\');"/><br /><span>Cell#</span></div>';
			rowData_b+='<div class="inputblock fl" style="width:130px; margin: 5px 0 0 20px;"><input type="text" style="width:129px;" name="sales_rep_email'+i+'" value="" /><br /><span>Email</span></div>';

			rowData_b+='</td>';
			rowData_b+='</tr>';
			
			$("#tr_b_"+oldId).after(rowData_b);
			oldId=i;
			var dd_pro = new Array();
			dd_pro["listHeight"] = 100;
			dd_pro["noneSelected"] = "Select All";
			$("#manufac_name"+i).multiSelect(dd_pro);
		}
		document.getElementById('totRows').value= i-1;
	}
}

var rowData_c='';
var tr  = '';
function addrow()	
{
	var row_class='';
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
	var ph_format = "<?php echo $GLOBALS['phone_format'];?>";
	var manu_opt = '<?php echo $manu_opt; ?>';
	
	rowData_c+='<tr id="tr_b_'+y+'">';
	rowData_c+='<td class="module_label '+row_class+'">';
	rowData_c+='<div class="inputblock fl" style="width:120px;"><input onchange="javascript:checkname(this)" type="text" style="width:119px;" name="add_vendor_name'+y+'" id="add_vendor_name'+y+'" class="add_vendor_name" value="" /><br /><span>Vendor Name</span></div>';
	rowData_c+='<div class="inputblock fl" style="width:70px; margin:0 0 0 20px"><input onChange="javascript:checkname(this)" type="text" style="width:69px;" name="add_vendor_id'+y+'" value="" /><br /><span>Vendor Id</span></div>';
	rowData_c+='<div class="inputblock fl" style="width:110px; margin:0 0 0 20px"><input onChange="javascript:checkname(this)" type="text" style="width:109px;" name="add_second_vendor_id'+i+'" value="" /><br /><span>Second Id</span></div>';
	rowData_c+='<div class="inputblock fl" style="width:110px; margin: 0 0 0 20px;"><input type="text" style="width:109px;" name="add_fax'+y+'" value="" onChange="set_phone_format(this,\''+ph_format+'\',\'fax\');" /><br /><span>Fax</span></div>';
	rowData_c+='<div class="inputblock fl" style="width:110px; margin: 0 0 0 20px;"><input type="text" style="width:109px;" name="add_tel'+y+'" value="" onChange="set_phone_format(this,\''+ph_format+'\');" /><br /><span>Tel</span></div>';
	rowData_c+='<div class="inputblock rptDropDown fl" style="width:110px;margin: 0 0 0 20px;"><select name="manufac_name'+y+'" style="width:109px;" id="manufac_name'+y+'" multiple="multiple"><option value="">Select</option>';
	rowData_c+=manu_opt;
	rowData_c+='</select><br /><span style="font-family:wf_SegoeUILight;font-size:1.300em;">Manufacturer</span></div>';
	rowData_c+='<div class="inputblock cb fl" style="width:120px; margin: 5px 0 0 0;"><input type="text" style="width:119px;" name="add_address'+y+'" value="" /><br /><span>Address</span></div>';
	rowData_c+='<div class="inputblock fl" style="width:70px; margin: 5px 0 0 20px;"><input type="text" style="width:69px;" name="add_mobile'+i+'" value="" onChange="set_phone_format(this,\''+ph_format+'\');"/><br /><span>Mobile</span></div>';
	rowData_c+='<div class="inputblock fl" style="width:120px; margin: 5px 0 0 20px;"><input type="text" style="width:45px;" name="add_zip'+y+'" id="zip_'+y+'" onKeyUp="zip_vs_state(this,'+y+');" onblur="zip_vs_state_length(this,'+y+')" value="" maxlength="5"/>-<input type="text" style="width:45px; margin-left:2px;" name="add_zip_ext'+y+'" value="" maxlength="4"/><br /><span>Zip Code</span></div>';
	rowData_c+='<div class="inputblock fl" style="width:110px; margin: 5px 0 0 10px;"><input type="text" style="width:109px;" name="add_city'+y+'" id="city_'+y+'" value=""/><br /><span>City</span></div>';
	rowData_c+='<div class="inputblock fl" style="width:110px; margin: 5px 0 0 20px;"><input type="text" style="width:109px;" name="add_state'+y+'" id="state_'+y+'" value=""/><br /><span>State</span></div>';
	rowData_c+='<div class="inputblock fl" style="width:120px; margin: 5px 0 0 20px;"><input type="text" style="width:119px;" name="add_email'+y+'" value="" /><br /><span>Email</span></div>';
	
	rowData_c+='<div class="inputblock fl" style="width:80px; margin: 5px 0 0 0px;"><span style="font-weight:bold;">Sales Rep:</span></div>';
	rowData_c+='<div class="inputblock fl" style="width:90px; margin: 5px 0 0 0px;"><input type="text" style="width:89px;" name="sales_rep_fname'+y+'" value="" /><br /><span>First Name</span></div>';
	rowData_c+='<div class="inputblock fl" style="width:95px; margin: 5px 0 0 20px;"><input type="text" style="width:89px;" name="sales_rep_mname'+y+'" value="" /><br /><span>Middle Name</span></div>';
	rowData_c+='<div class="inputblock fl" style="width:90px; margin: 5px 0 0 20px;"><input type="text" style="width:89px;" name="sales_rep_lname'+y+'" value="" /><br /><span>Last Name</span></div>';
	rowData_c+='<div class="inputblock fl" style="width:90px; margin: 5px 0 0 20px;"><input type="text" style="width:89px;" name="sales_rep_work_no'+y+'" value=""  onChange="set_phone_format(this,\''+ph_format+'\');"/><br /><span>Work#</span></div>';
	rowData_c+='<div class="inputblock fl" style="width:90px; margin: 5px 0 0 20px;"><input type="text" style="width:89px;" name="sales_rep_cell_no'+y+'" value=""  onChange="set_phone_format(this,\''+ph_format+'\');"/><br /><span>Cell#</span></div>';
	rowData_c+='<div class="inputblock fl" style="width:130px; margin: 5px 0 0 20px;"><input type="text" style="width:129px;" name="sales_rep_email'+y+'" value="" /><br /><span>Email</span></div>';

	rowData_c+='</td>';
	
	rowData_c+='</tr>';
	
	$("#tr_b_"+getRows).after(rowData_c); // ADD NEW ROW
	rowData_c='';
	var dd_pro1 = new Array();
	dd_pro1["listHeight"] = 100;
	dd_pro1["noneSelected"] = "Select All";
	$("#manufac_name"+y).multiSelect(dd_pro1);

	
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
<body style="overflow:hidden;">

    <div style="width:820px; margin:0 auto; overflow:hidden;">
    <img id="loading_img" style="display:none; position:absolute; top:20%; left:45%;" src="../../../images/loading_image.gif" />
       <div class="module_border" style="overflow:hidden;">
        	<form name="addnew" id="firstform" action="" method="post">
            <input type="hidden" name="totRows" id="totRows" value="" />
        		<div class="listheading pl5">
                	Add Vendor Information
                </div>
				
                <div style="height:250px; overflow-x:hidden; overflow-y:scroll">
                <table class="table_collapse countrow table_cell_padd5 module_border">
					<tr id="tr_b_1">
						<td class="module_label even">
							<div class="inputblock fl" style="width:120px;"><input onChange="javascript:checkname(this)" type="text" style="width:119px;" name="add_vendor_name1" class="add_vendor_name" id="add_vendor_name1" value="" /><br />
							<span>Vendor Name</span></div>
							
							<div class="inputblock fl" style="width:70px; margin:0 0 0 20px"><input onChange="javascript:checkname(this)" type="text" style="width:69px;" name="add_vendor_id1" value="" /><br />
							<span>Vendor Id</span></div>
							
							<div class="inputblock fl" style="width:110px; margin:0 0 0 20px"><input onChange="javascript:checkname(this)" type="text" style="width:109px;" name="add_second_vendor_id1" value="" /><br />
							<span>Second Id</span></div>
							
							<div class="inputblock fl" style="width:110px; margin: 0 0 0 20px;"><input type="text" style="width:109px;" name="add_fax1" value="" onChange="set_phone_format(this,'<?php echo $GLOBALS['phone_format'];?>','fax');" /><br />
							<span>Fax</span></div>
							
							<div class="inputblock fl" style="width:110px; margin: 0 0 0 20px;"><input type="text" style="width:109px;" name="add_tel1" value="" onChange="set_phone_format(this,'<?php echo $GLOBALS['phone_format'];?>');" /><br />
							<span>Tel</span></div>
							
							<div class="inputblock rptDropDown fl" style="width:110px; margin: 0 0 0 20px;">
							<select name="manufac_name1" style="width:109px;" id="manufac_name1" multiple="multiple">
								<option value="">Select</option>
								<?php echo $manu_opt; ?>
							</select><br />
                            
							<span style="font-family:wf_SegoeUILight;font-size:1.300em;">Manufacturer</span></div>							
							<div class="inputblock cb fl" style="width:120px; margin: 5px 0 0 0;"><input type="text" style="width:119px;" name="add_address1" value="" /><br />
							<span>Address</span></div>
							
							<div class="inputblock fl" style="width:70px; margin: 5px 0 0 20px;"><input type="text" style="width:69px;" name="add_mobile1" value="" onChange="set_phone_format(this,'<?php echo $GLOBALS['phone_format'];?>');"/><br />
							<span>Mobile#</span></div>
							
							<div class="inputblock fl" style="width:120px; margin: 5px 0 0 20px;"><input type="text" style="width:45px;" name="add_zip1" id="zip_1" onKeyUp="zip_vs_state(this,'1');" onBlur="zip_vs_state_length(this,'1')" value="" maxlength="5"/>-<input type="text" style="width:45px; margin-left:2px;" name="add_zip_ext1" value="" maxlength="4"/><br />
							<span>Zip Code</span></div>
							
							<div class="inputblock fl" style="width:110px; margin: 5px 0 0 10px;"><input type="text" style="width:109px;" name="add_city1" id="city_1" value=""/><br />
							<span>City</span></div>
							
							<div class="inputblock fl" style="width:110px; margin: 5px 0 0 20px;"><input type="text" style="width:109px;" name="add_state1" id="state_1" value=""/><br />
							<span>State</span></div>
							
							<div class="inputblock fl" style="width:120px; margin: 5px 0 0 20px;"><input type="text" style="width:119px;" name="add_email1" value="" /><br />
							<span>Email</span></div> 
							
							<div class="inputblock fl" style="width:80px;  margin: 5px 0 0 0; ">
								<span style="font-weight:bold;">Sales Rep:</span>
							</div>
							<div class="inputblock fl" style="width:90px;  margin: 5px 0 0 0; ">
								<input value="" type="text" style="width:89px;" id="sales_rep_fname1" name="sales_rep_fname1" /><br />
								<span>First Name</span>
							</div>
							<div class="inputblock fl" style="width:95px;  margin: 5px 0 0 20px; ">
								<input value="" type="text" style="width:89px;" id="sales_rep_mname1" name="sales_rep_mname1" /><br />
								<span>Middle Name</span>
							</div>
							<div class="inputblock fl" style="width:90px;  margin: 5px 0 0 20px; ">
								<input value="" type="text" style="width:89px;" id="sales_rep_lname1" name="sales_rep_lname1" /><br />
								<span>Last Name</span>
							</div>
							<div class="inputblock fl" style="width:90px;  margin: 5px 0 0 20px; ">
								<input onChange="set_phone_format(this,'<?php echo $GLOBALS['phone_format'];?>');"  value="" type="text" style="width:89px;" id="sales_rep_work_no1" name="sales_rep_work_no1" /><br />
								<span>Work#</span>
							</div>
							<div class="inputblock fl" style="width:90px;  margin: 5px 0 0 20px; ">
								<input onChange="set_phone_format(this,'<?php echo $GLOBALS['phone_format'];?>');" value="" type="text" style="width:89px;" id="sales_rep_cell_no1" name="sales_rep_cell_no1" /><br />
								<span>Cell#</span>
							</div>
							<div class="inputblock fl" style="width:130px;  margin: 5px 0 0 20px; ">
								<input value="" type="text" style="width:129px;" id="sales_rep_email1" name="sales_rep_email1" /><br />
								<span>Email</span>
							</div>
							
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
	var dd_pro = new Array();
	dd_pro["listHeight"] = 100;
	dd_pro["noneSelected"] = "Select All";
	$("#manufac_name1").multiSelect(dd_pro);
	createRows(1,1);
	document.getElementById('add_vendor_name1').focus();
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