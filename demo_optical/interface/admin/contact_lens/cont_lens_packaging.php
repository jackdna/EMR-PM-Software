<?php
/*
File: cont_lens_usage.php
Coded in PHP7
Purpose: Add/Edit/Delete: Contact Lense Packaging
Access Type: Direct access
*/
require_once(dirname('__FILE__')."/../../../config/config.php");
$msg_stat = "none";
$opr_id = $_SESSION['authId'];
$date = date("Y-m-d");
$time = date("h:i:s");

//UPDATE CONTACT LENS USAGE OPTIONS//
if(isset($_POST['save']) || isset($_POST['del_hidden'])){
	if(count($_POST['select_record'])>0){
		for($v=0;$v<count($_POST['select_record']);$v++){
			if(trim($_POST['del_hidden'])==""){
				$rec_id = $_POST['select_record'][$v];
				$opt_val = trim($_POST['option'][$rec_id]);
				$wsVal = (int)trim($_POST['ws'][$rec_id]);
				if($opt_val!="" || $rec_id!=""){
					$updateQry = "UPDATE `in_options` SET `opt_val`='".imw_real_escape_string($opt_val)."', `opt_sub_type`='".$wsVal."', `modified_date`='$date', `modified_time`='$time', `modified_by`='$opr_id' WHERE `id` = '".$rec_id."' AND `opt_type`='5' and module_id='3'";
					imw_query($updateQry);
					$msg = "Record(s) Saved Successfully";
					$msg_stat = "block";
				}
			}
			else{
				$rec_id="";
				$rec_id = $_POST['select_record'][$v];
				$updateQry = "";
				$updateQry = "UPDATE `in_options` SET `del_status`='2', `del_date`='".$date."', `del_time`='".$time."', `del_by`='".$opr_id."' WHERE `id`='".$rec_id."' AND `opt_type`='5' and module_id='3'";
				imw_query($updateQry);					
			}
		}
	}
}

if($_REQUEST['alpha'] && $_REQUEST['alpha']!='az'){
		$whr = " AND `opt_val` LIKE '".$_REQUEST['alpha']."%'";	
	}	
	$limit = 50; 
	$query = "SELECT COUNT(*) AS 'num' FROM `in_options` WHERE `del_status`!='2' and module_id='3' AND `opt_type`='5' ".$whr;
	$total_pages = imw_fetch_array(imw_query($query));
	$total_pages = $total_pages[num];
	$stages = 3;
	$page = imw_escape_string($_GET['page']);
	if($page){
		$start = ($page - 1) * $limit; 
	}else{
		$start = 0;	
	}

/*List of wear schedules*/
$wearSchedules = array();
$sqlWS = 'SELECT `id`, `cat_name` FROM `in_contact_cat` WHERE `del_status`=0 ORDER BY `cat_name` ASC';
$ws = imw_query($sqlWS);

if($ws && imw_num_rows($ws) > 0 )
{
	while( $row = imw_fetch_object( $ws ) )
	{
		$wearSchedules[$row->cat_name] = $row->id;
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
$(document).ready(function(){
	
	del = function(){
	 	if( $(".getchecked:checked").length == 0 ){
           top.falert('Please check atleast one record');
        }
		else{
			top.fconfirm('Are you sure to delete selected record(s) ?',del_callBack);
		}
	}
	
	$("#selectall").click(function(){
		if($(this).is(":checked")){
			$(".getchecked").prop('checked', true);
		}
		else{
			$(".getchecked").prop('checked', false);
		}
	});
});
function setStatus(tbname,rowid,value,colname){
	var dataString = 'table='+ tbname + '&id=' + rowid + '&value=' + value + '&column=' + colname + '&page=change';
	$.ajax({
		type: "POST",
		url: "change_status.php",
		data: dataString,
		cache: false,
		success: function(response){
			if(response=="true"){
				if(value==1){
					$('#status'+rowid).attr('src','../../../images/off.png');
					$('#status'+rowid).attr('title','InActive');
					$('#status'+rowid).attr("onclick","javascript:setStatus('in_options',"+rowid+",'0','del_status',this)");
				}
				else if(value==0){
					$('#status'+rowid).attr('src','../../../images/on.png');
					$('#status'+rowid).attr('title','Active');
					$('#status'+rowid).attr("onclick","javascript:setStatus('in_options',"+rowid+",'1','del_status',this)");
				}
			}
		}
	});
}

function refrsh(){
	window.location.href='cont_lens_usage_type.php';
}

function open_addnew_popup(){
	top.WindowDialog.closeAll();
	var Add_new_popup=top.WindowDialog.open('Add_new_popup',WEB_PATH+'/interface/admin/add_new.php?module_name=contact_lens_packaging&col_name=opt_val&heading=Contact_Lenses_Packaging&m_id=3','Add_new_popup','width=320,height=380,left=300,scrollbars=no,top=80,fullscreen=0,resizable=0');
	Add_new_popup.focus();
}

$(document).unbind('keydown').bind('keydown', function (event){
    var doPrevent = false;
    if(event.keyCode === 8){
        var d = event.srcElement || event.target;
        if ((d.tagName.toUpperCase() === 'INPUT' && (d.type.toUpperCase() === 'TEXT' || d.type.toUpperCase() === 'PASSWORD' || d.type.toUpperCase() === 'FILE')) 
             || d.tagName.toUpperCase() === 'TEXTAREA') {
            doPrevent = d.readOnly || d.disabled;
        }
        else{
            doPrevent = true;
        }
    }

    if(doPrevent){
        event.preventDefault();
    }
});
</script>
</head>
<body>
<form onSubmit="return validateform()" name="addcontactLensPackaging" id="firstform" method="post" class="mt10">
  <table class="table_collapse">
    <tr class="listheading">
      <td width="10"><input type="hidden" id="del_hidden" name="del_hidden" value="" />
        <input type="checkbox" id="selectall" value="" /></td>
      <td width="654">Contact Lens Packaging
        <div class="success_msg" style="display:<?php echo $msg_stat;?>;"><?php echo $msg; ?></div></td>
      <td width="248">Wear Scheduler</td>
	  <td align="center" width="100">Status</td>
    </tr>
  </table><?php
		$aprxHght=($total_pages>$limit)?465:435;
		?>
	<div id="listing_record" style="height:<?php echo $_SESSION['wn_height']-$aprxHght;?>px; overflow-y:auto;">
    <table class="table_collapse">
      <tbody class="table_cell_padd2">
        <?php 
	
	$sql="SELECT * FROM `in_options` WHERE `del_status`!='2' ".$whr." AND `opt_type`='5' and module_id='3' ORDER BY `opt_val` ASC LIMIT ".$start.", ".$limit;
	$res = imw_query($sql);
	$num = imw_num_rows($res);
	if($num>0){
		$i=0;
		while($row = imw_fetch_array($res)){
			$status = $row['del_status'];
			if($i%2==0){
				$rowbg="even";	
			}
			else{
				$rowbg="odd";	
			}
?>
        <tr class="<?php echo $rowbg;?>">
          <td width="10"><input type="checkbox" class="getchecked" value="<?php echo $row['id']; ?>" id="checked_<?php echo $i; ?>" name="select_record[]" /></td>
          <td width="600"><input type="text" value="<?php echo $row["opt_val"]; ?>" class="opt_val_field" id="opt_val_field_<?php echo $i; ?>" onChange="selectCurrentCheck('<?php echo $i; ?>')" name="option[<?php echo $row['id']; ?>]" style="width:96%;"/></td>
		  <td width="250">
				<select name="ws[<?php echo $row['id']; ?>]" id="ws_<?php echo $i; ?>" onChange="selectCurrentCheck('<?php echo $i; ?>')" style="width: 80%;">
					<option value="0">Select</option>
<?php
				foreach($wearSchedules as $key=>$value)
				{
					echo '<option value="'.$value.'" '.(( $row['opt_sub_type']==$value )?'selected':'').'>'.$key.'</option>';
				}
?>
				</select>
		  </td>
          <td align="center" width="70"><?php if($status=="1"){ ?>
            <img id="status<?php echo $row['id']; ?>" title="InActive" class="noborder" style="cursor: pointer;" onClick="javascript:setStatus('in_options','<?php echo $row['id']; ?>','0','del_status',this)" src="../../../images/off.png" complete="complete"/>
            <?php 
}
else{
?>
            <img id="status<?php echo $row['id']; ?>" title="Active" class="noborder" style="cursor: pointer;" onClick="javascript:setStatus('in_options','<?php echo $row['id']; ?>','1','del_status',this)" src="../../../images/on.png" complete="complete"/>
            <?php 
}
?></td>
        </tr>
        <?php
	$i++; 
	}
}
else{
	$numrows=1;
?>
        <tr>
          <td colspan="4" align="center" class="even">No Record Exist</td>
        </tr>
        <?php
}
?>
      </tbody>
    </table>
  </div>
  <div class="btn_cls">
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
<script type="text/javascript">
function submitFrom(){
	document.addcontactLensPackaging.submit();
}
$(document).ready(function(){
	selectCurrentCheck = function(ab){
	   $("#checked_"+ab).prop('checked', true);
	
	   var currentval = $('#opt_val_field_'+ab);
	
		if($.trim(currentval.val()) == ""){
				top.falert('Please Enter Contact Lens Color');	
				setTimeout(function(){$( currentval ).focus(); },0);
		}
		else{
			$(".opt_val_field").each(function( index ){
				if($.trim($(this).val()).toLowerCase() == $.trim(currentval.val()).toLowerCase() && $(this).attr('id') != currentval.attr('id')){
					top.falert(currentval.val()+' Already Exist');
					$( currentval ).val('');
					setTimeout(function(){$( currentval ).focus(); },0);
				}
			});
		}
	}
	
	validateform = function(){	
		$(".opt_val_field").each(function(index){
			if($.trim($(this).val()) == ""){
				top.falert("Please Enter Contact Lens Color");
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