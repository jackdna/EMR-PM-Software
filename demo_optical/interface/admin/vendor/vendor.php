<?php
	/*
	File: vendor.php
	Coded in PHP7
	Purpose: View/Update/Delete Vendor
	Access Type: Direct access
	*/
	require_once(dirname('__FILE__')."/../../../config/config.php");
	require_once("../../../library/classes/functions.php");
	$msg_stat = "none";
	$opr_id = $_SESSION['authId'];
	$date = date("Y-m-d");
	$time = date("h:i:s"); 
	//-------- UPDATE, INSERT AND DELETE VENDOR---------//	
	if(isset($_POST['save']) || isset($_POST['del_hidden']))	
	{	
		if(count($_POST['select_record'])>0)
		{
			for($v=0;$v<count($_POST['select_record']);$v++)
			{
				if(trim($_POST['del_hidden'])=="")
				{
					$rec_id = $_POST['select_record'][$v];
					$rec_vendor_name = imw_real_escape_string(trim($_POST['vendor_name'][$rec_id]));
					$rec_vendor_id = imw_real_escape_string(trim($_POST['vendor_id'][$rec_id]));
					$rec_second_vendor_id = imw_real_escape_string(trim($_POST['second_vendor_id'][$rec_id]));
					$rec_fax = imw_real_escape_string(trim($_POST['fax'][$rec_id]));			
					$rec_tel = imw_real_escape_string(preg_replace('/[^0-9]/','',trim($_POST['tel'][$rec_id])));			
					$rec_mobile = imw_real_escape_string(preg_replace('/[^0-9]/','',trim($_POST['mobile'][$rec_id])));			
					$rec_vendor_address = imw_real_escape_string(trim($_POST['address'][$rec_id]));			
					$rec_email = imw_real_escape_string(trim($_POST['email'][$rec_id]));
					$rec_city = imw_real_escape_string(trim($_POST['city'][$rec_id]));
					$rec_zip = imw_real_escape_string(trim($_POST['zip'][$rec_id]));
					$rec_zip_ext = imw_real_escape_string(trim($_POST['zip_ext'][$rec_id]));
					$rec_state = imw_real_escape_string(trim($_POST['state'][$rec_id]));
					$rec_manufac_name = $_POST['manufac_name'.$rec_id];
					$rec_sales_rep_fname = imw_real_escape_string(trim($_POST['sales_rep_fname'][$rec_id]));
					$rec_sales_rep_mname = imw_real_escape_string(trim($_POST['sales_rep_mname'][$rec_id]));
					$rec_sales_rep_lname = imw_real_escape_string(trim($_POST['sales_rep_lname'][$rec_id]));
					$rec_sales_rep_work_no = imw_real_escape_string(preg_replace('/[^0-9]/','',$_POST['sales_rep_work_no'][$rec_id]));
					$rec_sales_rep_cell_no = imw_real_escape_string(preg_replace('/[^0-9]/','',$_POST['sales_rep_cell_no'][$rec_id]));
					$rec_sales_rep_email = imw_real_escape_string(trim($_POST['sales_rep_email'][$rec_id]));
					
					if($rec_vendor_name!="")
					{				
						$updateQry = "update in_vendor_details set 
						vendor_name = '".$rec_vendor_name."',
						vendor_id = '".$rec_vendor_id."',
						second_vendor_id = '".$rec_second_vendor_id."', 
						fax = '".$rec_fax."',
						tel_num = '".$rec_tel."',
						mobile = '".$rec_mobile."',
						vendor_address = '".$rec_vendor_address."',
						email = '".$rec_email."',
						city = '".$rec_city."',
						zip = '".$rec_zip."',
						zip_ext = '".$rec_zip_ext."',
						state='".$rec_state."',
						sales_rep_fname='".$rec_sales_rep_fname."',
						sales_rep_mname='".$rec_sales_rep_mname."',
						sales_rep_lname='".$rec_sales_rep_lname."',
						sales_rep_work_no='".$rec_sales_rep_work_no."',
						sales_rep_cell_no='".$rec_sales_rep_cell_no."',
						sales_rep_email='".$rec_sales_rep_email."',
						modified_date='$date', 
						modified_time='$time', 
						modified_by='$opr_id'  
						where id = '".$rec_id."' ";		
					
						imw_query($updateQry);
						$selec = imw_query("select * from in_vendor_manufacture where vendor_id = '".$rec_id."'");
						$num_rows = imw_num_rows($selec);
						if($num_rows > 0) {
							$del_rec = imw_query("delete from in_vendor_manufacture where vendor_id='".$rec_id."'");
						}
						for($i=0;$i<count($rec_manufac_name);$i++)
						{								
							$insert_brand_manu = imw_query("insert in_vendor_manufacture set vendor_id='".$rec_id."', manufacture_id = '".imw_real_escape_string($rec_manufac_name[$i])."' ");
						}
						$msg = "Record(s) Saved Successfully";
						$msg_stat = "block";
					}
		
					if(trim($_POST['add_vendor_name'])!="")
					{
						$edit_time_insert_query = "insert in_vendor_details set 
						vendor_name = '".$_POST['add_vendor_name']."',
						fax = '".$_POST['add_fax']."',
						tel_num = '".$_POST['add_tel']."',
						mobile = '".$_POST['add_mobile']."',
						vendor_address = '".$_POST['add_address']."',
						email = '".$_POST['add_email']."',
						city = '".$_POST['add_city']."',
						zip = '".$_POST['add_zip']."',
						sales_rep_fname='".$rec_sales_rep_fname."',
						sales_rep_mname='".$rec_sales_rep_mname."',
						sales_rep_lname='".$rec_sales_rep_lname."',
						sales_rep_work_no='".$rec_sales_rep_work_no."',
						sales_rep_cell_no='".$rec_sales_rep_cell_no."',
						sales_rep_email='".$rec_sales_rep_email."'						
						";
					}
				}
				else
				{
					$rec_id="";
					$rec_id = $_POST['select_record'][$v];
					$updateQry = "";
					$updateQry = "update in_vendor_details set del_status = '2', del_date='$date', del_time='$time', del_by='$opr_id' where id = '".$rec_id."' ";
					imw_query($updateQry);					
				}
			}
		}
		else
		{
			if(trim($_POST['add_vendor_name'])!="")
			{
				$msg = "Record(s) Saved Successfully";
				$msg_stat = "block";
				$insqry="insert in_vendor_details set 
				vendor_name = '".$_POST['add_vendor_name']."',
				fax = '".$_POST['add_fax']."',
				tel_num = '".$_POST['add_tel']."',
				mobile = '".$_POST['add_mobile']."',
				vendor_address = '".$_POST['add_address']."',
				email = '".$_POST['add_email']."',
				city = '".$_POST['add_city']."',
				zip = '".$_POST['add_zip']."',
				sales_rep_fname='".$rec_sales_rep_fname."',
				sales_rep_mname='".$rec_sales_rep_mname."',
				sales_rep_lname='".$rec_sales_rep_lname."',
				sales_rep_work_no='".$rec_sales_rep_work_no."',
				sales_rep_cell_no='".$rec_sales_rep_cell_no."',
				sales_rep_email='".$rec_sales_rep_email."'
				";
				imw_query($insqry);			
			}
		}
		if($edit_time_insert_query!="")
		{
			$msg = "Record(s) Saved Successfully";
			$msg_stat = "block";
			echo $edit_time_insert_query;
			imw_query($edit_time_insert_query);
		}
	}
	if($_REQUEST['alpha'] && $_REQUEST['alpha']!='az' && $_REQUEST['search']!="Search")
	{
		$whr = " and vendor_name like '".$_REQUEST['alpha']."%' ";
	}
	elseif($_REQUEST['search']=="Search" && trim($_REQUEST['vandr'])!="")
	{
		$whr = " and (vendor_name like '".$_REQUEST['vandr']."%' or vendor_id like '".$_REQUEST['vandr']."%' or second_vendor_id like '".$_REQUEST['vandr']."%') ";
	}
	$targetpage = "vendor.php"; 	
	$limit = 15;
	$query = "SELECT COUNT(*) as num FROM in_vendor_details where del_status != '2' $whr";
	$total_pages = imw_fetch_array(imw_query($query));
	$total_pages = $total_pages[num];
	$stages = 3;
	$page = imw_escape_string($_GET['page']);
	if($page){
		$start = ($page - 1) * $limit; 
	}else{
		$start = 0;	
	}
	if($_REQUEST['search']=="Search" && trim($_REQUEST['vandr'])!="")
	{
		$start = 0;
	}
?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Optical</title>
<link rel="stylesheet" href="../../../library/css/inv_css.css?<?php echo constant("cache_version"); ?>" />
<link rel="stylesheet" href="<?php echo $GLOBALS['WEB_PATH'];?>/library/css/jquery.multiSelect.css?<?php echo constant("cache_version"); ?>" />

<script src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/jquery-1.10.1.min.js?<?php echo constant("cache_version"); ?>"></script>
<script src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/common.js?<?php echo constant("cache_version"); ?>"></script>
<script type="text/javascript">var jQ = jQuery.noConflict();</script>
<script src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/jquery.js?<?php echo constant("cache_version"); ?>"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/jquery.multiSelect.js?<?php echo constant("cache_version"); ?>"></script>
<script type="text/javascript">
var WEB_PATH='<?php echo $GLOBALS['WEB_PATH'];?>';
function del_callBack(result)
{
	if(result==true)
	{
		jQ("#del_hidden").val("1");
		jQ("#firstform").submit();
	}	
}
jQ(document).ready(function()
{
	del = function()
	{
	 	if( jQ(".getchecked:checked").length == 0 ) 
		{
           top.falert('Please check atleast one record');
        }
		else
		{
			top.fconfirm('Are you sure to delete selected record(s) ?',del_callBack);
		}
	}
	
	jQ("#selectall").click(function()
	{		
		if($(this).is(":checked"))
		{
			jQ(".getchecked").prop('checked', true);
		}
		else
		{
			jQ(".getchecked").prop('checked', false);
		}
	});
		
	if(jQ(".listing_table tr").size()>15)
	{
		jQ("#listing_record").addClass('listing_record');
	}
	else
	{
		jQ("#listing_record").removeClass('listing_record');
	}
});

</script>
<script type="text/javascript">
	function setStatus(tbname,rowid,value,colname)
	{
		var dataString = 'table='+ tbname + '&id=' + rowid + '&value=' + value + '&column=' + colname + '&page=change';
		jQ.ajax({
			type: "POST",
			url: "change_status.php",
			data: dataString,
			cache: false,
			success: function(response)
			{
				if(response=="true")
				{
					if(value==1)
					{
						jQ('#status'+rowid).attr('src','../../../images/off.png');
						jQ('#status'+rowid).attr('title','InActive');
						jQ('#status'+rowid).attr("onclick","javascript:setStatus('in_vendor_details',"+rowid+",'0','del_status',this)");
					}
					else if(value==0)
					{
						jQ('#status'+rowid).attr('src','../../../images/on.png');
						jQ('#status'+rowid).attr('title','Active');
						jQ('#status'+rowid).attr("onclick","javascript:setStatus('in_vendor_details',"+rowid+",'1','del_status',this)");
					}
				}
			}
		});
	}
	function refrsh()
	{
		window.location.href='vendor.php';
	}
	
function open_addnew_popup()
{
	top.WindowDialog.closeAll();
	var addwin=top.WindowDialog.open('Add_new_popup',WEB_PATH+'/interface/admin/vendor/add_new.php','Add_new_popup', 'width=830,height=380,left=300,scrollbars=no,top=80,fullscreen=0,resizable=0');
	addwin.focus();
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
<form onSubmit="return validateform()" name="addframe" id="firstform" action="" method="post" enctype="multipart/form-data" class="mt10">
<img id="loading_img" style="display:none; position:absolute; top:20%; left:45%;" src="../../../images/loading_image.gif" />
   <table class="table_collapse" >
        <tr class="listheading">
          <td width="10">
          <input type="hidden" id="del_hidden" name="del_hidden" value="" />
          <input type="checkbox" id="selectall" value="" /></td>
          <td width="560">Vendor Name <div class="success_msg" style="display:<?php echo $msg_stat;?>;"><?php echo $msg; ?></div></td>
          <td align="center" width="40">Status</td>
        </tr>
		<tr style="background: #D5EFFF;">
		  <td>&nbsp;</td>
		  <td style="text-align:right">
		  	Search Vendor:&nbsp;<input type="text" style="width:100px;" name="vandr" id="vandr" value="<?php echo $_POST['vandr']; ?>">&nbsp;&nbsp;</td>
		  <td class="btn_cls" style="padding:0px;"><input type="submit" value="Search" name="search"></td>
		</tr>
    </table>
    <?php
	$aprxHght=($total_pages>$limit)?495:465;
	?>
	<div id="listing_record" style="height:<?php echo $_SESSION['wn_height']-$aprxHght;?>px; overflow-y:auto;">
    <table class="table_collapse">
        <tbody class="table_cell_padd2">
        <?php 
			
			$sql = "select * from in_vendor_details where del_status != '2' $whr order by vendor_name asc LIMIT $start, $limit";	
            $res = imw_query($sql);
            $num = imw_num_rows($res);
            
            if($num>0)
            {
                $i=0;
                while($row = imw_fetch_array($res))
                {
                    $status = $row['del_status'];

                    if($i%2==0)	
                    {
                        $rowbg="even";	
                    }
                    else
                    {
                        $rowbg="odd";	
                    }
        ?>
        <tr class="<?=$rowbg;?>">
          <td width="10" valign="top"><input type="checkbox" class="getchecked" value="<?php echo $row['id']; ?>" id="checked_<?php echo $i; ?>" name="select_record[]" /></td>
          <td width="560" class="module_label" style="">
			<div class="inputblock fl" style="width:110px;  margin: 0 0 0 0; "><input onChange="selectCurrentCheck('<?php echo $i; ?>')" value="<?php echo $row['vendor_name']; ?>" type="text" style="width:109px;" class="vendor_name" id="vendor_name_<?php echo $i; ?>" name="vendor_name[<?php echo $row['id']; ?>]" /><br />
			<span>Vendor Name</span></div>
			
			<div class="inputblock fl" style="width:100px;  margin: 0 0 0 20px; "><input onChange="selectCurrentCheck('<?php echo $i; ?>')" value="<?php echo $row['vendor_id']; ?>" type="text" style="width:99px;" name="vendor_id[<?php echo $row['id']; ?>]" /><br />
			<span>Vendor Id</span></div>
			
			<div class="inputblock fl" style="width:95px;  margin: 0 0 0 20px; "><input onChange="selectCurrentCheck('<?php echo $i; ?>')" value="<?php echo $row['second_vendor_id']; ?>" type="text" style="width:94px;" name="second_vendor_id[<?php echo $row['id']; ?>]" /><br />
			<span>Second Id</span></div>
			
			<div class="inputblock fl" style="width:95px; margin: 0 0 0 20px;">
			<input onChange="selectCurrentCheck('<?php echo $i; ?>'); set_phone_format(this,'<?php echo $GLOBALS['phone_format'];?>','fax');" value="<?php echo stripslashes(core_phone_format($row['fax'])); ?>" type="text" style="width:94px;" name="fax[<?php echo $row['id']; ?>]" /><br />
			<span>Fax</span></div>
			
			<div class="inputblock fl" style="width:100px; margin: 0 0 0 20px;">
			<input onChange="selectCurrentCheck('<?php echo $i; ?>'); set_phone_format(this,'<?php echo $GLOBALS['phone_format'];?>');" value="<?php echo stripslashes(core_phone_format($row['tel_num'])); ?>" type="text" style="width:99px;" name="tel[<?php echo $row['id']; ?>]" /><br />
			<span>Tel</span></div>
			
			<div class="inputblock fl rptDropDown" style="width:110px; margin: 0 0 0 20px;">
			<?php $vendor_manu_qry = "select id, manufacture_id from in_vendor_manufacture where vendor_id='".$row['id']."'";
			$vendor_manu_res = imw_query($vendor_manu_qry);
			$vendor_manu_nums = imw_num_rows($vendor_manu_res);
			while($vendor_manu_row = imw_fetch_array($vendor_manu_res))
			{
				$exist_vendor_id[] = $vendor_manu_row['manufacture_id'];
			}
			?><select name="manufac_name[<?php echo $row['id']; ?>]" style="width:109px;padding-top:5px;" onChange="selectCurrentCheck('<?php echo $i; ?>')" id="manufac_name<?php echo $row['id']; ?>" multiple="multiple">
			<option value="">Select</option>
			<?php $manu_detail_qry = "select id, manufacturer_name from in_manufacturer_details where del_status='0' order by manufacturer_name asc";
			$manu_detail_res = imw_query($manu_detail_qry);
			$manu_detail_nums = imw_num_rows($manu_detail_res);
			if($manu_detail_nums > 0)
			{	
				while( $manu_detail_row = imw_fetch_array($manu_detail_res)) { 
				$selet='';
				if(in_array($manu_detail_row['id'], $exist_vendor_id)) 
				{ $selet = 'selected'; } ?>
				<option <?php echo $selet; ?> value="<?php echo $manu_detail_row['id']; ?>"><?php echo $manu_detail_row['manufacturer_name']; ?></option>
			<?php } 
			} unset($exist_vendor_id); ?>
			</select>
			<br/>
			<span style="padding:0px; font-family:wf_SegoeUILight;font-size:1.300em;">Manufacturer</span></div>
			
			<div class="inputblock fl" style="width:210px; margin: 0 0 0 30px;"><input onChange="selectCurrentCheck('<?php echo $i; ?>')" value="<?php echo $row['vendor_address']; ?>" type="text" style="width:209px;" name="address[<?php echo $row['id']; ?>]" /><br />
			<span>Address</span></div>
			
			<div class="inputblock cb fl" style="width:110px; margin: 5px 0 0 0px;"><input onChange="selectCurrentCheck('<?php echo $i; ?>'); set_phone_format(this,'<?php echo $GLOBALS['phone_format'];?>');" value="<?php echo stripslashes(core_phone_format($row['mobile'])); ?>" type="text" style="width:109px;" name="mobile[<?php echo $row['id']; ?>]" /><br />
			<span>Mobile</span></div>
			
			<div class="inputblock fl" style="width:110px; margin: 5px 0 5px 20px;"><input onChange="selectCurrentCheck('<?php echo $i; ?>');" onBlur="zip_vs_state_length(this,'<?php echo $i; ?>')" onKeyUp="zip_vs_state(this,'<?php echo $i; ?>')" value="<?php echo $row['zip']; ?>" type="text" style="width:40px;" name="zip[<?php echo $row['id']; ?>]" id="zip_<?php echo $i; ?>" maxlength="5"/>-<input type="text" style="width:40px; margin-left:2px;" onChange="selectCurrentCheck('<?php echo $i; ?>')" name="zip_ext[<?php echo $row['id']; ?>]" value="<?php echo $row['zip_ext']; ?>" maxlength="4"/><br />
			<span>Zip Code</span></div>
			
			<div class="inputblock fl" style="width:95px; margin: 5px 0 5px 7px;"><input onChange="selectCurrentCheck('<?php echo $i; ?>')" value="<?php echo $row['city']; ?>" type="text" style="width:94px;" name="city[<?php echo $row['id']; ?>]" id="city_<?php echo $i; ?>"/><br />
			<span>City</span></div>
			
			<div class="inputblock fl" style="width:95px; margin: 5px 0 5px 20px;"><input onChange="selectCurrentCheck('<?php echo $i; ?>')" value="<?php echo $row['state']; ?>" type="text" style="width:94px;" name="state[<?php echo $row['id']; ?>]" id="state_<?php echo $i; ?>" /><br />
			<span>State</span></div>
			
			<div class="inputblock fl" style="width:240px; margin: 5px 0 5px 20px;"><input onChange="selectCurrentCheck('<?php echo $i; ?>')" value="<?php echo $row['email']; ?>" type="text" style="width:239px;" name="email[<?php echo $row['id']; ?>]" /><br />
			<span>Email</span></div>
          </td>
          <td align="center" valign="top" width="40">
            <?php if($status=="1") 
            { ?>	
            <img id="status<?php echo $row['id']; ?>" title="InActive" class="noborder" style="cursor: pointer;" onClick="javascript:setStatus('in_vendor_details','<?php echo $row['id']; ?>','0','del_status',this)" src="../../../images/off.png" complete="complete"/>
            <?php } else { ?>
            <img id="status<?php echo $row['id']; ?>" title="Active" class="noborder" style="cursor: pointer;" onClick="javascript:setStatus('in_vendor_details','<?php echo $row['id']; ?>','1','del_status',this)" src="../../../images/on.png" complete="complete"/>
            <?php 
            } ?>
          </td>
        </tr>
		<tr class="<?=$rowbg;?>">
			<td></td>
			<td colspan="2">
				<div class="inputblock fl" style="width:130px;  margin: 0 0 0 0; ">
					<span style="font-weight:bold;">Sales Rep:</span>
				</div>
				<div class="inputblock fl" style="width:100px;  margin: 0 0 0 0; ">
					<input onChange="selectCurrentCheck('<?php echo $i; ?>')" value="<?php echo $row['sales_rep_fname']; ?>" type="text" style="width:99px;" id="sales_rep_fname_<?php echo $i; ?>" name="sales_rep_fname[<?php echo $row['id']; ?>]" /><br />
					<span>First Name</span>
				</div>
				<div class="inputblock fl" style="width:100px;  margin: 0 0 0 20px; ">
					<input onChange="selectCurrentCheck('<?php echo $i; ?>')" value="<?php echo $row['sales_rep_mname']; ?>" type="text" style="width:99px;" id="sales_rep_mname_<?php echo $i; ?>" name="sales_rep_mname[<?php echo $row['id']; ?>]" /><br />
					<span>Middle Name</span>
				</div>
				<div class="inputblock fl" style="width:100px;  margin: 0 0 0 20px; ">
					<input onChange="selectCurrentCheck('<?php echo $i; ?>')" value="<?php echo $row['sales_rep_lname']; ?>" type="text" style="width:99px;" id="sales_rep_lname_<?php echo $i; ?>" name="sales_rep_lname[<?php echo $row['id']; ?>]" /><br />
					<span>Last Name</span>
				</div>
				<div class="inputblock fl" style="width:100px;  margin: 0 0 0 20px; ">
					<input onChange="selectCurrentCheck('<?php echo $i; ?>'); set_phone_format(this,'<?php echo $GLOBALS['phone_format'];?>');"  value="<?php echo stripslashes(core_phone_format($row['sales_rep_work_no'])); ?>" type="text" style="width:99px;" id="sales_rep_work_no_<?php echo $i; ?>" name="sales_rep_work_no[<?php echo $row['id']; ?>]" /><br />
					<span>Work#</span>
				</div>
				<div class="inputblock fl" style="width:100px;  margin: 0 0 0 20px; ">
					<input onChange="selectCurrentCheck('<?php echo $i; ?>');  set_phone_format(this,'<?php echo $GLOBALS['phone_format'];?>');" value="<?php echo stripslashes(core_phone_format($row['sales_rep_cell_no'])); ?>" type="text" style="width:99px;" id="sales_rep_cell_no_<?php echo $i; ?>" name="sales_rep_cell_no[<?php echo $row['id']; ?>]" /><br />
					<span>Cell#</span>
				</div>
				<div class="inputblock fl" style="width:240px;  margin: 0 0 0 20px; ">
					<input onChange="selectCurrentCheck('<?php echo $i; ?>')" value="<?php echo $row['sales_rep_email']; ?>" type="text" style="width:239px;" id="sales_rep_email_<?php echo $i; ?>" name="sales_rep_email[<?php echo $row['id']; ?>]" /><br />
					<span>Email</span>
				</div>
			</td>
		</tr>
		<script>
			var nums = '<?php echo $row['id']; ?>';
			var dd_pro = new Array();
			dd_pro["listHeight"] = 100;
			dd_pro["noneSelected"] = "Select All";
			$("#manufac_name"+nums).multiSelect(dd_pro);
			$("#manufac_name"+nums).attr("onClick","selectCurrentCheck('<?php echo $i; ?>')");
		 </script> 
		 <?php $i++;
			 }  }
            else
            {
				$numrows=1;
        ?>
        <tr>
          <td colspan="3" align="center" class="even" >No Record Exist</td>
        </tr>
        <?php } ?>
        </tbody>
    </table>

</div>

<div class="btn_cls cb">

<?php
require_once'../paging_new.php';
$alpha=array();

$alpha[1] = "a";
$alpha[2] = "b";
$alpha[3] = "c";
$alpha[4] = "d";
$alpha[5] = "e";
$alpha[6] = "f";
$alpha[7] = "g";
$alpha[8] = "h";
$alpha[9] = "i";
$alpha[10] = "j";
$alpha[11] = "k";
$alpha[12] = "l";
$alpha[13] = "m";
$alpha[14] = "n";
$alpha[15] = "o";
$alpha[16] = "p";
$alpha[17] = "q";
$alpha[18] = "r";
$alpha[19] = "s";
$alpha[20] = "t";
$alpha[21] = "u";
$alpha[22] = "v";
$alpha[23] = "w";
$alpha[24] = "x";
$alpha[25] = "y";
$alpha[26] = "z";
?>

<ul style="float:left; margin:10px 0 10px 170px; width:100%; <?php if($numrows==1 && !isset($_REQUEST['alpha'])){ ?>display:none;<?php } ?>">
<li class="fl"><a style="border-radius:5px; padding:2px 6px; color:#fff; text-decoration:none; font-weight:bold; text-transform:uppercase; margin:3px; background:<?php if($_REQUEST['alpha']=="az") { echo "#fb9608"; } else { echo "#09F"; } ?>" href="?alpha=az">A-Z</a></li>
<?php foreach($alpha as $key=>$value) 
	{ ?>
<li class="fl"><a style="border-radius:5px; padding:2px 6px; color:#fff; text-decoration:none; font-weight:bold; text-transform:uppercase; margin:2.3px; background:<?php if($_REQUEST['alpha']==$value) { echo "#fb9608"; } else { echo "#09F"; } ?>" href="?alpha=<?php echo $value; ?>"><?php echo $value; ?></a></li>
<?php } ?>
</ul>

</div>

</div>  
</form>                  
           </div>           
           
        </div>
    </div>
        
<script type="text/javascript">
function submitFrom(){
	document.addframe.submit();
}
jQ(document).ready(function()
{
	selectCurrentCheck = function(ab)
	{
	   jQ("#checked_"+ab).prop('checked', true);
	
	   var currentval = jQ('#vendor_name_'+ab);
	
		if(jQ.trim(currentval.val()) == "")
		{
				top.falert('Please Enter Vendor');	
				setTimeout(function(){jQ( currentval ).focus(); },0);
		}
		else
		{
			jQ(".vendor_name").each(function( index ) 
			{
				//top.falert($(this).val()+" == "+currentval);
				if(jQ.trim(jQ(this).val()).toLowerCase() == jQ.trim(currentval.val()).toLowerCase() && jQ(this).attr('id') != currentval.attr('id'))
				{
					top.falert(currentval.val()+' Already Exist');
					jQ( currentval ).val('');
					setTimeout(function(){jQ( currentval ).focus(); },0);
				}	
			});	
		}
	}
	  
	validateform = function()
	{	
		jQ(".vendor_name").each(function(index)
		{
			if(jQ.trim(jQ(this).val()) == "")
			{
				top.falert("Please Enter Vendor");
				jQ(this).focus();
				return false;
			}
		});
	}
	//BUTTONS
	var mainBtnArr = new Array();
	mainBtnArr[0] = new Array("frame","Save","top.main_iframe.admin_iframe.submitFrom();");
	mainBtnArr[1] = new Array("frame","New","top.main_iframe.admin_iframe.open_addnew_popup()");
	mainBtnArr[2] = new Array("frame","Delete","top.main_iframe.admin_iframe.del()");
	top.btn_show("admin",mainBtnArr);

});
</script>      
</body>
</html>