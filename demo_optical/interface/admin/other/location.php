<?php
	/*
	File: location.php
	Coded in PHP7
	Purpose: Edit and View Location
	Access Type: Direct access
	*/
	require_once(dirname('__FILE__')."/../../../config/config.php");
	require_once("../../../library/classes/functions.php");
	$msg_stat = "none";
	$opr_id = $_SESSION['authId'];
	$date = date("Y-m-d");
	$time = date("h:i:s"); 
	$target="";
	if(isset($_POST['save']) || isset($_POST['del_hidden']))	
	{	
		if(count($_POST['select_record'])>0)
		{
			for($v=0;$v<count($_POST['select_record']);$v++)
			{
				
				if(trim($_POST['del_hidden'])=="")
				{
					$rec_id = $_POST['select_record'][$v];
					if($_FILES['logo_fac_'.$rec_id]['name']!="")
					{
						/*if(!is_dir("../../patient_interface/uploaddir/file_".$rec_id."_".$rec_id)){
							mkdir("../../patient_interface/uploaddir/file_".$rec_id."_".$rec_id, 0777, true);
						}
						$target="../../patient_interface/uploaddir/file_".$rec_id."_".$rec_id."/".$_FILES['logo_fac_'.$rec_id]['name'];*/
						$target="facility_".$rec_id."_".$_FILES['logo_fac_'.$rec_id]['name'];
						$path="../../patient_interface/uploaddir/facility_logo/";
					}
					/*else
					{
						$target=$_POST['file_logo'][$rec_id];
					}*/
					
					move_uploaded_file($_FILES['logo_fac_'.$rec_id]['tmp_name'],$path.$target);
					
					
					$rec_loc_name = imw_real_escape_string(trim($_POST['loc_name'][$rec_id]));
					$rec_cont_person = imw_real_escape_string(trim($_POST['contact_person'][$rec_id]));
					$rec_fax = imw_real_escape_string(preg_replace('/[^0-9]/','',trim($_POST['fax'][$rec_id])));			
					$rec_tel = imw_real_escape_string(preg_replace('/[^0-9]/','',trim($_POST['tel'][$rec_id])));
					$rec_address = imw_real_escape_string(trim($_POST['address'][$rec_id]));			
					$rec_tax_label = imw_real_escape_string(trim($_POST['tax_label'][$rec_id]));
					$rec_city = imw_real_escape_string(trim($_POST['city'][$rec_id]));
					$rec_zip = imw_real_escape_string(trim($_POST['zip'][$rec_id]));
					$rec_zip_ext = imw_real_escape_string(trim($_POST['zip_ext'][$rec_id]));
					$rec_state = imw_real_escape_string(trim($_POST['state'][$rec_id]));
					$rec_npi = imw_real_escape_string(trim($_POST['npi'][$rec_id]));
					$rec_pos = $_POST['fac_prac_code'][$rec_id];
					$rec_fac_group = $_POST['fac_group'][$rec_id];
					$rec_idoc_fac_id = $_POST['idoc_fac'][$rec_id];
					$hq = $_POST['hq'][$rec_id];
					if($hq>0){
						imw_query("UPDATE in_location SET hq='0'");
					}
					if($rec_loc_name!="")
					{		
						$loc_logo_qry="";
						if($target!=""){
							$loc_logo_qry=",loc_logo='$target'";
						}
						$updateQry = "update in_location set 
						loc_name = '".$rec_loc_name."',
						contact_person='".$rec_cont_person."', 
						fax = '".$rec_fax."',
						tel_num = '".$rec_tel."',
						tax_label = '".$rec_tax_label."',
						address = '".$rec_address."',
						npi = '".$rec_npi."',
						city = '".$rec_city."',
						zip = '".$rec_zip."',
						zip_ext = '".$rec_zip_ext."',
						state='".$rec_state."',
						pos='".$rec_pos."',
						modified_date='$date', 
						modified_time='$time', 
						modified_by='$opr_id',
						hq='".$hq."',
						fac_group='$rec_fac_group',
						idoc_fac_id='$rec_idoc_fac_id'
						$loc_logo_qry
						where id = '".$rec_id."'";
						imw_query($updateQry);
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
						zip = '".$_POST['add_zip']."'						
						";
					}
				}
				else
				{
					$rec_id="";
					$rec_id = $_POST['select_record'][$v];
					$updateQry = "";
					$updateQry = "update in_location set del_status = '2', del_date='$date', del_time='$time', del_by='$opr_id' where id = '".$rec_id."' ";
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
				zip = '".$_POST['add_zip']."'
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
	if($_REQUEST['alpha'] && $_REQUEST['alpha']!='az')
	{
		$whr = " and loc_name like '".$_REQUEST['alpha']."%' ";
	}
	$targetpage = "location.php"; 	
	$limit = 15;
	$query = "SELECT COUNT(*) as num FROM in_location where del_status != '2' $whr";
	$total_pages = imw_fetch_array(imw_query($query));
	$total_pages = $total_pages[num];
	$stages = 3;
	$page = imw_escape_string($_GET['page']);
	if($page){
		$start = ($page - 1) * $limit; 
	}else{
		$start = 0;	
	}
?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Optical</title>
<link rel="stylesheet" href="../../../library/css/inv_css.css?<?php echo constant("cache_version"); ?>" />
<script src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/jquery-1.10.1.min.js?<?php echo constant("cache_version"); ?>"></script>
<script src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/common.js?<?php echo constant("cache_version"); ?>"></script>
<style type="text/css">
#tax_wrap{
	background-color: rgba(0,0,0,0.2);
    top: 0px;
    width: 100%;
    height: 100%;
    position: absolute;
	display: none;
}
#tax_div{
	width: 320px;
	margin: 0 auto;
	overflow: hidden;
	position: relative;
	top: 15%;
	z-index: 9999;
	background-color: #FFF;
}
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
}
.taxbtn{
	padding:2px;
	line-height:14px;
}
.location_tax{
	text-align: right;
}
</style>
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
						$('#status'+rowid).attr("onclick","javascript:setStatus('in_location',"+rowid+",'0','del_status',this)");
					}
					else if(value==0)
					{
						$('#status'+rowid).attr('src','../../../images/on.png');
						$('#status'+rowid).attr('title','Active');
						$('#status'+rowid).attr("onclick","javascript:setStatus('in_location',"+rowid+",'1','del_status',this)");
					}
					
				}
			}
		});
	}
	function refrsh()
	{
		window.location.href='location.php';
	}
	
function open_addnew_popup()
{
	top.WindowDialog.closeAll();
	var Add_new_popup=top.WindowDialog.open('Add_new_popup',WEB_PATH+'/interface/admin/other/add_new.php','Add_new_popup', 'width=1000,height=350,left=160,scrollbars=no,top=80,fullscreen=0,resizable=0');
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
   <table class="table_collapse" >
        <tr class="listheading">
          <td style="width:10px">
          <input type="hidden" id="del_hidden" name="del_hidden" value="" />
          <input type="checkbox" id="selectall" value="" /></td>
          <td style="width:700px;">Location Name <div class="success_msg" style="display:<?php echo $msg_stat;?>;"><?php echo $msg; ?></div></td>
          <td style="width:65px;">&nbsp;</td>
          <td align="right" style="width:75px;padding-right:25px;">Status</td>
        </tr>
    </table>
     <?php
	$aprxHght=($total_pages>$limit)?465:435;
	?>
	<div id="listing_record" style="height:<?php echo $_SESSION['wn_height']-$aprxHght;?>px; overflow-y:auto;">
    
    <table class="table_collapse">
        <tbody class="table_cell_padd2">
        <?php 
			$sql = "select * from in_location where del_status != '2' $whr order by loc_name asc LIMIT $start, $limit";	
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
          <td width="700" class="module_label" style=""><div class="inputblock fl" style="width:125px;  margin: 0 0 0 0; "><input onChange="selectCurrentCheck('<?php echo $i; ?>')" value="<?php echo $row['loc_name']; ?>" type="text" style="width:114px;" class="vendor_name" id="loc_name_<?php echo $i; ?>" name="loc_name[<?php echo $row['id']; ?>]" /><br />
<span>Location Name</span></div>

<div class="inputblock fl" style="width:125px;  margin: 0 0 0 5px; "><input onChange="selectCurrentCheck('<?php echo $i; ?>')" value="<?php echo $row['contact_person']; ?>" type="text" style="width:114px;" name="contact_person[<?php echo $row['id']; ?>]" /><br />
<span>Contact Person</span></div>

<div class="inputblock fl" style="width:102px; margin: 0 0 0 5px;"><input onChange="selectCurrentCheck('<?php echo $i; ?>'); set_phone_format(this,'<?php echo $GLOBALS['phone_format'];?>');" value="<?php echo stripslashes(core_phone_format($row['tel_num'])); ?>" type="text" style="width:90px;" name="tel[<?php echo $row['id']; ?>]" /><br />
<span>Phone</span></div>

<div class="inputblock fl" style="width:102px; margin: 0 0 0 5px;"><input onChange="selectCurrentCheck('<?php echo $i; ?>'); set_phone_format(this,'<?php echo $GLOBALS['phone_format'];?>','fax');" value="<?php echo stripslashes(core_phone_format($row['fax'])); ?>" type="text" style="width:90px;" name="fax[<?php echo $row['id']; ?>]" /><br />
<span>Fax</span></div>

<div class="inputblock fl" style="width:160px; margin: 0 0 0 5px;"><select style="width:159px" name="fac_prac_code[<?php echo $row['id']; ?>]" id="fac_prac_code" class="text_10" onChange="selectCurrentCheck('<?php echo $i; ?>');">
		<option value="">- Select POS -</option>
		<?php
		$vquery_t = "select a.pos_facility_id,a.facilityPracCode,b.pos_prac_code from pos_facilityies_tbl a,pos_tbl b where a.pos_id = b.pos_id order by facilityPracCode asc";
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
<span>POS</span></div>

<div class="inputblock fl" style="width:220px; margin: 0 0 0 10px;"><input onChange="selectCurrentCheck('<?php echo $i; ?>')" value="<?php echo $row['address']; ?>" type="text" style="width:210px;" name="address[<?php echo $row['id']; ?>]" /><br />
<span>Address</span></div>

<div class="inputblock cb fl" style="width:122px; margin: 5px 0 5px 0px;"><input onChange="selectCurrentCheck('<?php echo $i; ?>');" onBlur="zip_vs_state_length(this,'<?php echo $i; ?>')" onKeyUp="zip_vs_state(this,'<?php echo $i; ?>')" value="<?php echo $row['zip']; ?>" type="text" style="width:45px;" name="zip[<?php echo $row['id']; ?>]" id="zip_<?php echo $i; ?>" maxlength="5"/>-<input type="text" style="width:45px; margin-left:2px;" onChange="selectCurrentCheck('<?php echo $i; ?>')" name="zip_ext[<?php echo $row['id']; ?>]" value="<?php echo $row['zip_ext']; ?>" maxlength="4"/><br />
<span>Zip Code</span></div>

<div class="inputblock fl" style="width:120px; margin: 5px 0 5px 7px;"><input onChange="selectCurrentCheck('<?php echo $i; ?>')" value="<?php echo $row['city']; ?>" type="text" style="width:110px;" name="city[<?php echo $row['id']; ?>]" id="city_<?php echo $i; ?>"/><br />
<span>City</span></div>

<div class="inputblock fl" style="width:120px; margin: 5px 0 5px 5px;"><input onChange="selectCurrentCheck('<?php echo $i; ?>')" value="<?php echo $row['state']; ?>" type="text" style="width:110px;" name="state[<?php echo $row['id']; ?>]" id="state_<?php echo $i; ?>" /><br />
<span>State</span></div>

<div class="inputblock fl" style="width:120px; margin: 5px 0 5px 5px;"><input onChange="selectCurrentCheck('<?php echo $i; ?>')" value="<?php echo $row['npi']; ?>" type="text" style="width:110px;" name="npi[<?php echo $row['id']; ?>]" /><br />
<span>NPI</span></div>

<div class="inputblock fl" style="width:70px; margin: 5px 0 0 5px;"><input onChange="selectCurrentCheck('<?php echo $i; ?>');" value="<?php echo stripslashes($row['tax_label']); ?>" type="text" style="width:60px;" name="tax_label[<?php echo $row['id']; ?>]" /><br />
<span>Tax Label</span></div>

<div class="inputblock fl" style="width:55px; margin: 5px 0 0 5px;">
	<a class="button taxbtn" href="javascript:void(0);" onClick="show_tax('<?php echo $row['id']; ?>', '<?php echo $row['loc_name']; ?>', '<?php echo stripslashes($row['tax']); ?>')">Tax(%)</a>
<!--input onChange="selectCurrentCheck('<?php echo $i; ?>');" value="<?php echo stripslashes($row['tax']); ?>" type="text" style="width:47px;" name="tax[<?php echo $row['id']; ?>]" /><br /-->
</div>

<div class="inputblock fl" style="width:220px; margin: 5px 0 0 10px;">
		<select style="width:159px" name="fac_group[<?php echo $row['id']; ?>]" id="fac_group" class="text_10" onChange="selectCurrentCheck('<?php echo $i; ?>');">
		<option value="">- Select Group -</option>
		<?php
		$vquery_t = "select * from groups_new order by name asc";
		$vsql_t = imw_query($vquery_t);
		while($rs_t = imw_fetch_array($vsql_t)){
			$se="";
			if($row['fac_group']==$rs_t['gro_id']){
				$se="selected";
			}
			echo("<option ".$se." value='".$rs_t['gro_id']."'>".$rs_t['name']."</option>");
		}
		?>
	</select>
    <span style="padding-left:5px;">
    	<input type="checkbox" name="hq[<?php echo $row['id']; ?>]" id="hq_<?php echo $row['id']; ?>" value="1" class="hq" cIndex="<?php echo $i; ?>" <?php echo ($row['hq']=='1')?'checked="checked"':''; ?>/>
    	<label for="hq_<?php echo $row['id']; ?>">HQ</label>
    </span>    
<br />
<span>Group</span></div>
          </td>
          <td style="width:60px;padding-top:0;" align="center">
           <?php if($row['loc_logo']!=""){
			echo show_image_thumb($row['loc_logo'], 40, 40, '', $style='border:1px solid #CCC;margin-top:-20px;','locatio_page');
			 }?>
             
           </br>
           	<input id="logo_fac_<?php echo $row['id']; ?>" name="logo_fac_<?php echo $row['id']; ?>" type="file" style="visibility:hidden;width:1px;" onChange="selectCurrentCheck('<?php echo $i; ?>')"/>
            <input type="hidden" value="<?php echo $row['loc_logo'];?>" name="file_logo[<?php echo $row['id']; ?>]">
			<input type="button" value="Upload Logo" onclick="$('#logo_fac_<?php echo $row['id']; ?>').click();" /></td>
          <td align="center" valign="top" width="40">
            <?php if($status=="1") 
            { ?>	
            <img id="status<?php echo $row['id']; ?>" title="InActive" class="noborder" style="cursor: pointer;" onClick="javascript:setStatus('in_location','<?php echo $row['id']; ?>','0','del_status',this)" src="../../../images/off.png" complete="complete"/>
            <?php } else { ?>
            <img id="status<?php echo $row['id']; ?>" title="Active" class="noborder" style="cursor: pointer;" onClick="javascript:setStatus('in_location','<?php echo $row['id']; ?>','1','del_status',this)" src="../../../images/on.png" complete="complete"/>
            <?php 
            } ?>
           </td>
        </tr>
        <tr class="<?=$rowbg;?>">
			<td>&nbsp;</td>
          <td colspan="3" align="left" >
			<div class="inputblock fl" style="width:100%;  margin: 0 0 0 0; ">
				<select style="width:159px" name="idoc_fac[<?php echo $row['id']; ?>]" id="idoc_fac" class="text_10" onChange="selectCurrentCheck('<?php echo $i; ?>');">
					<option value="">- Select Facility -</option>
					<?php
					$vquery_t = "select id, name from facility order by name asc";
					$vsql_t = imw_query($vquery_t);
					while($rs_t = imw_fetch_array($vsql_t)){
						$se="";
						if($row['idoc_fac_id']==$rs_t['id']){
							$se="selected";
						}
						echo("<option ".$se." value='".$rs_t['id']."'>".$rs_t['name']."</option>");
					}
					?>
				</select><span style="color: #969696; font-size: 12px">(used to switch between optical and iDoc and to post charges over iDoc. One location linked to only one facility is must)</span>
				<br>
				<span>iDoc Facility</span></div>
			</td>
        </tr>
		 <?php $i++;
			 }  }
            else
            {
				$numrows=1;
        ?>
        <tr>
          <td colspan="4" align="center" class="even" >No Record Exist</td>
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

    <input type="submit" name="save" value="Save" />
    <input type="button" name="new" value="New" onClick="javascript:open_addnew_popup();"/>
    <input type="button" name="delete" value="Delete" onClick="javascript:del();"/>
</div>

</div>  
</form>  

<div id="tax_wrap">
	<div id="tax_div">
		<div class="module_border" style="overflow:hidden">
			<div class="listheading pl5">
				Tax(%) for <span id="tax_loc_name"></span>
				<img onClick="$('#tax_wrap').hide();" src="<?php echo $GLOBALS['WEB_PATH']; ?>/images/del.png" style="margin-top:4px;margin-right:5px;float:right;cursor:pointer;" />
			</div>
			<form name="location_tax_save" id="location_tax_save" method="POST" onSubmit="return false;">
				<input type="hidden" name="facility_id" id="facility_id" />
				<table class="module_border" style="width:100%">
					<tr>
						<td style="width:40%">Frames</td>
						<td style="width:60%;">
							<input type="text" class="location_tax" name="location_tax[]" id="tax_frame" value="0" onChange="checkTaxVal(this)" autocomplete="off" />
						</td>
					</tr>
					<tr>
						<td>Lenses</td>
						<td>
							<input type="text" class="location_tax" name="location_tax[]" id="tax_lens" value="0" onChange="checkTaxVal(this)" autocomplete="off" />
						</td>
					</tr>
					<tr>
						<td>Contact Lenses</td>
						<td>
							<input type="text" class="location_tax" name="location_tax[]" id="tax_cl" value="0" onChange="checkTaxVal(this)" autocomplete="off" />
						</td>
					</tr>
					<tr>
						<td>Medication</td>
						<td>
							<input type="text" class="location_tax" name="location_tax[]" id="tax_med" value="0" onChange="checkTaxVal(this)" autocomplete="off" />
						</td>
					</tr>
					<tr>
						<td>Supplies</td>
						<td>
							<input type="text" class="location_tax" name="location_tax[]" id="tax_supp" value="0" onChange="checkTaxVal(this)" autocomplete="off" />
						</td>
					</tr>
					<tr>
						<td>Others</td>
						<td>
							<input type="text" class="location_tax" name="location_tax[]" id="tax_other" value="0" onChange="checkTaxVal(this)" autocomplete="off" />
						</td>
					</tr>
					<!--<tr>
						<td>Remake</td>
						<td>
							<input type="text" class="location_tax" name="location_tax[]" id="tax_remake" value="0" onChange="checkTaxVal(this)" autocomplete="off" />
						</td>
					</tr>-->
				</table>
				<div class="btn_cls">
					<input type="hidden" name="action" value="saveTax" />
					<input type="button" name="save" value="Save" onClick="saveTax();">
				</div>
			</form>
		</div>
	</div>  
</div>              
           </div>
        </div>
    </div>
    
    
<script type="text/javascript">
function show_tax(id, name, tax){
	/*Tax div*/
	var tax_div = $("div#tax_div");
	
	/*Set Facility id*/
	$(tax_div).find("input#facility_id").val(id);
	
	/*Set Name in PopUp*/
	$(tax_div).find("span#tax_loc_name").text(name);
	
	/*Tax Fields*/
	var tax_fields = $(tax_div).find('input.location_tax');
	tax = tax.split("~~~");
	
	$.each(tax_fields, function(key,elem){
		$(elem).val(tax[key]);
	});
	
	$("#tax_wrap").show();
	
}
function saveTax(){
	
	if($("#facility_id").val()!=""){
		/*Extract data from tax form*/
		var data = $("form#location_tax_save").serializeArray();
		$.each(data, function(i,val){
			/*Set value to 0 is entered blank*/
			if(val.name=="location_tax[]"){
				value = val.value;
				val.value = (value=="")?'0':value;
			}
		});
		$.ajax({
			url: '<?php echo $GLOBALS['WEB_PATH']; ?>/interface/admin/ajax.php',
			data: data,
			method: 'POST',
			complete: function(){
				/*Hide tax div on request complete*/
				$("#tax_wrap").hide();
				top.falert("Tax saved successfully.");
				location.reload();
			}
		});
	}
	return false;
}

function submitFrom(){
	document.addframe.submit();
}
$(document).ready(function()
{
	selectCurrentCheck = function(ab)
	{
	   $("#checked_"+ab).prop('checked', true);
	
	   var currentval = $('#loc_name_'+ab);
	
		if($.trim(currentval.val()) == "")
		{
				top.falert('Please Enter Location');	
				setTimeout(function(){$( currentval ).focus(); },0);
		}
		else
		{
				
		}
	}
	  
	validateform = function()
	{	
		$(".vendor_name").each(function(index)
		{
			if($.trim($(this).val()) == "")
			{
				top.falert("Please Enter Location");
				$(this).focus();
				return false;
			}
		});
	}
	
	$(".hq").on('change', function(event){
		if($(this).is(':checked')){
			$(".hq").prop('checked',false);
			$(this).prop('checked',true);
		}
		else{
			$(".hq").prop('checked',false);
		}
		selectCurrentCheck($(this).attr('cIndex'));
	});
	
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