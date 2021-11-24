<?php
	/*
	File: manufacture.php
	Coded in PHP7
	Purpose: Edit/Delete Manufacture
	Access Type: Direct access
	*/
	require_once(dirname('__FILE__')."/../../../config/config.php");
	require_once("../../../library/classes/functions.php");
	$msg_stat = "none";
	$opr_id = $_SESSION['authId'];
	$date = date("Y-m-d");
	$time = date("h:i:s"); 	
	if(isset($_POST['save']) || isset($_POST['del_hidden']))	
	{	
		if(count($_POST['select_record'])>0)
		{
			for($v=0;$v<count($_POST['select_record']);$v++)
			{
				
				if(trim($_POST['del_hidden'])=="")
				{
					$rec_id = $_POST['select_record'][$v];
					
					$rec_manufacturer_name = imw_real_escape_string(trim($_POST['manufacturer_name'][$rec_id]));
					$rec_fax = imw_real_escape_string(trim($_POST['fax'][$rec_id]));			
					$rec_tel = imw_real_escape_string(preg_replace('/[^0-9]/','',trim($_POST['tel'][$rec_id])));			
					$rec_mobile = imw_real_escape_string(preg_replace('/[^0-9]/','',trim($_POST['mobile'][$rec_id])));			
					$rec_manufacturer_address = imw_real_escape_string(trim($_POST['address'][$rec_id]));			
					$rec_email = imw_real_escape_string(trim($_POST['email'][$rec_id]));	
					$rec_manu_frames = $_POST['manu_frames'][$rec_id];	
					$rec_manu_lenses = $_POST['manu_lenses'][$rec_id];	
					$rec_manu_cont_lenses = $_POST['manu_cont_lenses'][$rec_id];	
					$rec_manu_supplies = $_POST['manu_supplies'][$rec_id];	
					$rec_manu_medicine = $_POST['manu_medicine'][$rec_id];	
					$rec_manu_accessories = $_POST['manu_accessories'][$rec_id];
					$rec_city = imw_real_escape_string(trim($_POST['city'][$rec_id]));
					$rec_zip = imw_real_escape_string(trim($_POST['zip'][$rec_id]));
					$rec_zip_ext = imw_real_escape_string(trim($_POST['zip_ext'][$rec_id]));	
					$rec_state = imw_real_escape_string(trim($_POST['state'][$rec_id]));
					if($rec_manufacturer_name!="")
					{		
						$updateQry = "update in_manufacturer_details set 
						manufacturer_name = '".$rec_manufacturer_name."', 
						fax = '".$rec_fax."',
						tel_num = '".$rec_tel."',
						mobile = '".$rec_mobile."',
						manufacturer_address = '".$rec_manufacturer_address."',
						email = '".$rec_email."',
						frames_chk = '".$rec_manu_frames."',
						lenses_chk = '".$rec_manu_lenses."',
						cont_lenses_chk = '".$rec_manu_cont_lenses."',
						supplies_chk = '".$rec_manu_supplies."',
						medicine_chk = '".$rec_manu_medicine."',
						accessories_chk = '".$rec_manu_accessories."',
						city = '".$rec_city."',
						zip = '".$rec_zip."',
						zip_ext = '".$rec_zip_ext."',
						state = '".$rec_state."',
						modified_date='$date', 
						modified_time='$time', 
						modified_by='$opr_id'
						where id = '".$rec_id."' ";		
						//echo $updateQry; die();
						imw_query($updateQry);
						$msg = "Record(s) Saved Successfully";
						$msg_stat = "block";
					}
		
					if(trim($_POST['add_manufacturer_name'])!="")
					{
						$edit_time_insert_query = "insert in_manufacturer_details set 
						manufacturer_name = '".imw_real_escape_string($_POST['add_manufacturer_name'])."',
						fax = '".imw_real_escape_string($_POST['add_fax'])."',
						tel_num = '".imw_real_escape_string($_POST['add_tel'])."',
						mobile = '".imw_real_escape_string($_POST['add_mobile'])."',
						manufacturer_address = '".imw_real_escape_string($_POST['add_address'])."',
						email = '".imw_real_escape_string($_POST['add_email'])."',
						frames_chk = '".$_POST['add_manu_frames']."',
						lenses_chk = '".$_POST['add_manu_lenses']."',
						cont_lenses_chk = '".$_POST['add_manu_cont_lenses']."',
						supplies_chk = '".$_POST['add_manu_supplies']."',
						medicine_chk = '".$_POST['add_manu_medicine']."',
						accessories_chk = '".$_POST['add_manu_accessories']."',
						city = '".imw_real_escape_string($_POST['add_city'])."',
						zip = '".imw_real_escape_string($_POST['add_zip'])."'						
						";
					}
				}
				else
				{
					$rec_id="";
					$rec_id = $_POST['select_record'][$v];
					$updateQry = "";
					$updateQry = "update in_manufacturer_details set del_status = '2', del_date='$date', del_time='$time', del_by='$opr_id' where id = '".$rec_id."' ";
					imw_query($updateQry);					
				}
			}
		}
		else
		{
			if(trim($_POST['add_manufacturer_name'])!="")
			{
				$msg = "Record(s) Saved Successfully";
				$msg_stat = "block";
				$insqry="insert in_manufacturer_details set 
				manufacturer_name = '".imw_real_escape_string($_POST['add_manufacturer_name'])."',
				fax = '".imw_real_escape_string($_POST['add_fax'])."',
				tel_num = '".imw_real_escape_string($_POST['add_tel'])."',
				mobile = '".imw_real_escape_string($_POST['add_mobile'])."',
				manufacturer_address = '".imw_real_escape_string($_POST['add_address'])."',
				email = '".imw_real_escape_string($_POST['add_email'])."', 
				frames_chk = '".$_POST['add_manu_frames']."',
				lenses_chk = '".$_POST['add_manu_lenses']."',
				cont_lenses_chk = '".$_POST['add_manu_cont_lenses']."',
				supplies_chk = '".$_POST['add_manu_supplies']."',
				medicine_chk = '".$_POST['add_manu_medicine']."',
				accessories_chk = '".$_POST['add_manu_accessories']."'
				city = '".imw_real_escape_string($_POST['add_city'])."',
				zip = '".imw_real_escape_string($_POST['add_zip'])."'	
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
		$whr = " and manufacturer_name like '".$_REQUEST['alpha']."%' ";
	}
	elseif($_REQUEST['search']=="Search" && trim($_REQUEST['manufact'])!="")
	{
		$whr = " and manufacturer_name like '".$_REQUEST['manufact']."%' ";
	}
	$targetpage = "manufacturer.php"; 	
	$limit = 15;
	$query = "SELECT COUNT(*) as num FROM in_manufacturer_details where del_status != '2' $whr";
	$total_pages = imw_fetch_array(imw_query($query));
	$total_pages = $total_pages[num];
	$stages = 3;
	$page = imw_escape_string($_GET['page']);
	if($page){
		$start = ($page - 1) * $limit; 
	}else{
		$start = 0;	
	}
	if($_REQUEST['search']=="Search" && trim($_REQUEST['manufact'])!="")
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
<script src="../../../library/js/jquery-1.10.1.min.js?<?php echo constant("cache_version"); ?>"></script>
<script src="../../../library/js/common.js?<?php echo constant("cache_version"); ?>"></script>
<script type="text/javascript">
var WEB_PATH='<?php echo $GLOBALS['WEB_PATH'];?>';
function del_callBack(result)
{
	if(result==true)
	{
		$("#del_hidden").val("1");
		$("#firstform").submit();
	}	
}
$(document).ready(function()
{
	del = function()
	{
	 	if( $(".getchecked:checked").length == 0 ) 
		{
           top.falert('Please check atleast one record');
        }
		else
		{
			top.fconfirm('Are you sure to delete selected record(s) ?',del_callBack);
		}
	}
	
	$("#selectall").click(function()
	{		
		if($(this).is(":checked"))
		{
			$(".getchecked").prop('checked', true);
		}
		else
		{
			$(".getchecked").prop('checked', false);
		}
	});
		
	if($(".listing_table tr").size()>15)
	{
		$("#listing_record").addClass('listing_record');
	}
	else
	{
		$("#listing_record").removeClass('listing_record');
	}
});
</script>
<script type="text/javascript">
	function setStatus(tbname,rowid,value,colname)
	{
		var dataString = 'table='+ tbname + '&id=' + rowid + '&value=' + value + '&column=' + colname + '&page=change';
		$.ajax({
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
						$('#status'+rowid).attr('src','../../../images/off.png');
						$('#status'+rowid).attr('title','InActive');
						$('#status'+rowid).attr("onclick","javascript:setStatus('in_manufacturer_details',"+rowid+",'0','del_status',this)");
					}
					else if(value==0)
					{
						$('#status'+rowid).attr('src','../../../images/on.png');
						$('#status'+rowid).attr('title','Active');
						$('#status'+rowid).attr("onclick","javascript:setStatus('in_manufacturer_details',"+rowid+",'1','del_status',this)");
					}
					
				}
			}
		});
	}
	function refrsh()
	{
		window.location.href='manufacturer.php';
	}
	
function open_addnew_popup()
{
	top.WindowDialog.closeAll();
	var Add_new_popup=top.WindowDialog.open('Add_new_popup',WEB_PATH+'/interface/admin/manufacturer/add_new.php','Add_new_popup', 'width=860,height=330,left=300,scrollbars=no,top=80,fullscreen=0,resizable=0');
	Add_new_popup.focus();
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
   <table class="table_collapse">
        <tr class="listheading">
          <td style="width:20px;">
          <input type="hidden" id="del_hidden" name="del_hidden" value="" />
          <input type="checkbox" id="selectall" value="" /></td>
          <td style="width:auto;">Manufacturer Name<div class="success_msg" style="display:<?php echo $msg_stat;?>;"><?php echo $msg; ?></div></td>
          <td align="center" style="width:60px;">Status</td>
        </tr>
		<tr style="background: #D5EFFF;">
		  <td>&nbsp;</td>
		  <td style="text-align:right">
		  	Search Manufacturer:&nbsp;<input type="text" style="width:100px;" name="manufact" id="manufact" value="<?php echo $_POST['manufact']; ?>">&nbsp;&nbsp;</td>
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
		
			$sql = "select * from in_manufacturer_details where del_status != '2' $whr order by manufacturer_name asc LIMIT $start, $limit";
								
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
          <td valign="top" style="width:20px;"><input type="checkbox" class="getchecked" value="<?php echo $row['id']; ?>" id="checked_<?php echo $i; ?>" name="select_record[]" /></td>
          <td style="width:auto;" class="module_label">
                <div class="inputblock fl" style="width:160px; margin: 0 0 0 0;">
				<input onChange="selectCurrentCheck('<?php echo $i; ?>')" class="manufacturer_name" id="manufacturer_name_<?php echo $i; ?>" value="<?php echo $row['manufacturer_name']; ?>" type="text" style="width:150px;" name="manufacturer_name[<?php echo $row['id']; ?>]" /><br />
                <span>Manufacturer Name</span></div>
                
                <div class="inputblock fl" style="width:115px; margin: 0 0 0 15px;">
				<input onChange="selectCurrentCheck('<?php echo $i; ?>'); set_phone_format(this,'<?php echo $GLOBALS['phone_format'];?>','fax');" value="<?php echo  stripslashes(core_phone_format($row['fax'])); ?>" type="text" style="width:114px;" name="fax[<?php echo $row['id']; ?>]" /><br />
                <span>Fax</span></div>
                
                <div class="inputblock fl" style="width:115px; margin: 0 0 0 15px;"><input onChange="selectCurrentCheck('<?php echo $i; ?>'); set_phone_format(this,'<?php echo $GLOBALS['phone_format'];?>');" value="<?php echo stripslashes(core_phone_format($row['tel_num'])); ?>" type="text" style="width:114px;" name="tel[<?php echo $row['id']; ?>]" /><br />
                <span>Tel</span></div>
                
                <div class="inputblock fl" style="width:115px; margin: 0 0 0 15px;"><input onChange="selectCurrentCheck('<?php echo $i; ?>'); set_phone_format(this,'<?php echo $GLOBALS['phone_format'];?>');" value="<?php echo stripslashes(core_phone_format($row['mobile'])); ?>" type="text" style="width:114px;" name="mobile[<?php echo $row['id']; ?>]" /><br />
                <span>Mobile</span></div>
                
                <div class="inputblock fl" style="width:115px; margin: 0 0 0 15px;"><input onChange="selectCurrentCheck('<?php echo $i; ?>')" value="<?php echo $row['email']; ?>" type="text" style="width:114px;" name="email[<?php echo $row['id']; ?>]" /><br />
                <span>Email</span></div>
                
                <div class="inputblock fl" style="width:290px; margin: 0 0 0 15px;"><input onChange="selectCurrentCheck('<?php echo $i; ?>')" value="<?php echo $row['manufacturer_address']; ?>" type="text" style="width:285px;" name="address[<?php echo $row['id']; ?>]" /><br />
                <span>Address</span></div>
				
				<div class="inputblock fl" style="width:120px; margin: 5px 0 5px 0;"><input onChange="selectCurrentCheck('<?php echo $i; ?>');" onBlur="zip_vs_state_length(this,'<?php echo $i; ?>')" onKeyUp="zip_vs_state(this,'<?php echo $i; ?>')" value="<?php echo $row['zip']; ?>" type="text" style="width:48px;" name="zip[<?php echo $row['id']; ?>]" id="zip_<?php echo $i; ?>" maxlength="5"/><input onChange="selectCurrentCheck('<?php echo $i; ?>')"  type="text" style="width:48px; margin-left:4px;" name="zip_ext[<?php echo $row['id']; ?>]" value="<?php echo $row['zip_ext']; ?>" maxlength="4"/><br />
                <span>Zip Code</span></div>
				
				<div class="inputblock fl" style="width:120px; margin: 5px 0 5px 15px;"><input onChange="selectCurrentCheck('<?php echo $i; ?>')" value="<?php echo $row['city']; ?>" type="text" style="width:119px;" name="city[<?php echo $row['id']; ?>]" id="city_<?php echo $i; ?>" /><br />
                <span>City</span></div>
				
				<div class="inputblock fl" style="width:120px; margin: 5px 0 5px 15px;"><input onChange="selectCurrentCheck('<?php echo $i; ?>')" value="<?php echo $row['state']; ?>" type="text" style="width:119px;" name="state[<?php echo $row['id']; ?>]" id="state_<?php echo $i; ?>" /><br />
                <span>State</span></div>
				
                <div class="inputblock fl" style="width:550px; margin: 3px 0 5px 15px;">
                <table style="font-size:90%;" width="550" cellpadding="0" cellspacing="0">
                    <tr>
                        <td style="padding:0;" width="80">
                            <input type="checkbox" <?php if($row['frames_chk']=="1"){ echo 'checked="checked"'; } ?> value="1" name="manu_frames[<?php echo $row['id']; ?>]" onChange="selectCurrentCheck('<?php echo $i; ?>')">Frames
                        </td>
                        <td style="padding:0;" width="80">
                            <input type="checkbox" <?php if($row['lenses_chk']=="1"){ echo 'checked="checked"'; } ?> value="1" name="manu_lenses[<?php echo $row['id']; ?>]" onChange="selectCurrentCheck('<?php echo $i; ?>')">Lenses
                        </td>
                        <td style="padding:0;" width="90">
                            <input type="checkbox" <?php if($row['cont_lenses_chk']=="1"){ echo 'checked="checked"'; } ?> value="1" name="manu_cont_lenses[<?php echo $row['id']; ?>]" onChange="selectCurrentCheck('<?php echo $i; ?>')">Contacts
                        </td>
                        <td style="padding:0;" width="90">
                            <input type="checkbox" <?php if($row['supplies_chk']=="1"){ echo 'checked="checked"'; } ?> value="1" name="manu_supplies[<?php echo $row['id']; ?>]" onChange="selectCurrentCheck('<?php echo $i; ?>')">Supplies
                        </td>
                        <td style="padding:0;" width="100">
                            <input type="checkbox" <?php if($row['medicine_chk']=="1"){ echo 'checked="checked"'; } ?> value="1" name="manu_medicine[<?php echo $row['id']; ?>]" onChange="selectCurrentCheck('<?php echo $i; ?>')">Medicines
                        </td>
                        <td style="padding:0;" width="110">
                            <input type="checkbox" <?php if($row['accessories_chk']=="1"){ echo 'checked="checked"'; } ?> value="1" name="manu_accessories[<?php echo $row['id']; ?>]" onChange="selectCurrentCheck('<?php echo $i; ?>')">Accessories
                        </td>
                    </tr>
                    <tr>
                        <td colspan="6" valign="top" style="font-size:16px;">Manufacturer Type</td>
                    </tr>
                </table>
                </div>
          </td>
          <td align="center" valign="top" style="width:40px;">
            <?php if($status=="1") 
            { ?>	
            <img id="status<?php echo $row['id']; ?>" title="InActive" class="noborder" style="cursor: pointer;" onClick="javascript:setStatus('in_manufacturer_details','<?php echo $row['id']; ?>','0','del_status',this)" src="../../../images/off.png" complete="complete"/>
            <?php } else { ?>
            <img id="status<?php echo $row['id']; ?>" title="Active" class="noborder" style="cursor: pointer;" onClick="javascript:setStatus('in_manufacturer_details','<?php echo $row['id']; ?>','1','del_status',this)" src="../../../images/on.png" complete="complete"/>
            <?php 
            } ?>
			<!-- Copy the manufacturer to the vendors -->
			<img src="<?php echo $GLOBALS['WEB_PATH']; ?>/images/copy.png" onClick="copyToVendors(<?php echo $row['id']; ?>);" title="Copy the Manufacturer to Vendors" style="height:30px;cursor:pointer;margin-top:10px;" />
          </td>
        </tr>
        <?php
                $i++; 
                }
            }
            else
            {
				$numrows=1;
        ?>
        <tr>
          <td colspan="2" align="center" class="even">No Record Exist</td>
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
</form>

<script type="text/javascript">
/*function fot ehe Ajax Call to copy the Manufacturer to the Vendors List*/
function copyToVendors(manufId){
	
	if( typeof(manufId)=='undefined' ){
		return false;
	}
	
	$.ajax({
		method: 'POST',
		data: 'action=copyManufToVendors&manufId='+manufId,
		url: 'ajax.php',
		beforeSend: function(){
			
		},
		success: function(resp){
			if(resp=='success'){
				top.falert('Manufacturer copied to Vendors successfully.');
			}
			else{
				top.falert('<strong>Error in copying the data.</strong><br>'+resp);
			}
		},
		complete: function(){
			
		}
	});
	
}

function submitFrom(){
	document.addframe.submit();
}
$(document).ready(function()
{
	selectCurrentCheck = function(ab)
	{
	   $("#checked_"+ab).prop('checked', true);
	
	   var currentval = $('#manufacturer_name_'+ab);
	
		if($.trim(currentval.val()) == "")
		{
				top.falert('Please Enter Manufacturer');	
				setTimeout(function(){$( currentval ).focus(); },0);
		}
		else
		{
			$(".manufacturer_name").each(function( index ) 
			{
				if($.trim($(this).val()).toLowerCase() == $.trim(currentval.val()).toLowerCase() && $(this).attr('id') != currentval.attr('id'))
				{
					top.falert(currentval.val()+' Already Exist');
					$( currentval ).val('');
					setTimeout(function(){$( currentval ).focus(); },0);
				}	
			});	
		}
	}
	  
	validateform = function()
	{	
		$(".manufacturer_name").each(function(index)
		{
			if($.trim($(this).val()) == "")
			{
				top.falert("Please Enter Manufacturer");
				$(this).focus();
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