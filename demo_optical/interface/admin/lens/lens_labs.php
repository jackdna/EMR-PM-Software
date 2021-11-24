<?php
	/*
	File: lens_lab.php
	Coded in PHP7
	Purpose: Add/Edit/Delete: Lens Lab
	Access Type: Direct access
	*/
	require_once(dirname('__FILE__')."/../../../config/config.php");
	$msg_stat = "none";
	$opr_id = $_SESSION['authId'];
	$date = date("Y-m-d");
	$time = date("h:i:s");
	//------- UPDATE AND INSERT LENS LAB---------//
	if(isset($_POST['save']) || isset($_POST['del_hidden']))	
	{	
		if(count($_POST['select_record'])>0)
		{
			for($v=0;$v<count($_POST['select_record']);$v++)
			{
				if(trim($_POST['del_hidden'])=="")
				{
					$rec_id = $_POST['select_record'][$v];
					$rec_labname = trim($_POST['labname'][$rec_id]);
					$rec_vw_user = $_POST['vw_user_'.$rec_id];
					if($rec_labname!="")
					{		
						$updateQry = "update in_lens_lab set lab_name = '".imw_real_escape_string($rec_labname)."', modified_date='$date', modified_time='$time', modified_by='$opr_id' where id = '".$rec_id."' ";		
						imw_query($updateQry);
						$msg = "Record(s) Saved Successfully";
						$msg_stat = "block";
						if($rec_id>0){
							$met_des_qry="";
							imw_query("delete from in_vw_user_lab where lab_id='$rec_id'");
							foreach($rec_vw_user as $des_val){
								if($met_des_qry!=""){
									$met_des_qry.=", ";
								}
								$met_des_qry.="('".$rec_id."', '".$des_val."')";
							}
							imw_query("insert into in_vw_user_lab (lab_id,vw_user_id) values $met_des_qry");
						}
						if(trim($_POST['input_val'])!="")
						{
							$edit_time_insert_query = "insert in_lens_lab set lab_name = '".imw_real_escape_string($_POST['input_val'])."' ";					
						}
					}
				}
				else
				{
					$rec_id="";
					$rec_id = $_POST['select_record'][$v];
					$updateQry = "";
					$updateQry = "update in_lens_lab set del_status = '2', del_date='$date', del_time='$time', del_by='$opr_id' where id = '".$rec_id."' ";
					imw_query($updateQry);					
				}
			}
		}
		else
		{
			if(trim($_POST['input_val'])!="")
			{
				$msg = "Record(s) Saved Successfully";
				$msg_stat = "block";
				imw_query("insert in_lens_lab set lab_name = '".imw_real_escape_string($_POST['input_val'])."' ");
			}
		}
		if($edit_time_insert_query!="")
		{
			$msg = "Record(s) Saved Successfully";
			$msg_stat = "block";
			imw_query($edit_time_insert_query);
		}
	}
	
	if($_REQUEST['alpha'] && $_REQUEST['alpha']!='az')
	{
		$whr = " and lab_name like '".$_REQUEST['alpha']."%' ";	
	}
	
	$targetpage = "lens_labs.php"; 	
	$limit = 50; 
	$query = "SELECT COUNT(*) as num FROM in_lens_lab where del_status != '2' $whr";
	$total_pages = imw_fetch_array(imw_query($query));
	$total_pages = $total_pages[num];
	$stages = 3;
	$page = imw_escape_string($_GET['page']);
	if($page){
		$start = ($page - 1) * $limit; 
	}else{
		$start = 0;	
	}
	
	$dsn_qry=imw_query("select * from in_vision_web where vw_user!='' order by vw_user");	
	while($dsn_row=imw_fetch_array($dsn_qry)){
		$dsn_arr[$dsn_row['id']]=$dsn_row['vw_user'];
	}
?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Optical</title>
<link rel="stylesheet" href="../../../library/css/inv_css.css?<?php echo constant("cache_version"); ?>" />
<script src="../../../library/js/jquery-1.10.1.min.js?<?php echo constant("cache_version"); ?>"></script>

<link rel="stylesheet" href="<?php echo $GLOBALS['WEB_PATH'];?>/library/css/jquery.multiSelect.css?<?php echo constant("cache_version"); ?>" />
<script type="text/javascript" src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/jquery.multiSelect_edited.js?<?php echo constant("cache_version"); ?>"></script>

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
	
	var dd_pro = new Array();
	dd_pro["listHeight"] = 200;
	dd_pro["noneSelected"] = "Select All";
	$(".vw_user").multiSelect(dd_pro);
});
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
					$('#status'+rowid).attr("onclick","javascript:setStatus('in_lens_lab',"+rowid+",'0','del_status',this)");
				}
				else if(value==0)
				{
					$('#status'+rowid).attr('src','../../../images/on.png');
					$('#status'+rowid).attr('title','Active');
					$('#status'+rowid).attr("onclick","javascript:setStatus('in_lens_lab',"+rowid+",'1','del_status',this)");
				}
				
			}
		}
	});
}
	
function refrsh()
{
	window.location.href='lens_labs.php';
}

function open_addnew_popup()
{
	top.WindowDialog.closeAll();
	var Add_new_popup=top.WindowDialog.open('Add_new_popup',WEB_PATH+'/interface/admin/add_new.php?module_name=lens_lab&col_name=lab_name&heading=Lens_Lab_Name', 'Add_new_popup', 'width=320,height=380,left=300,scrollbars=no,top=80,fullscreen=0,resizable=0');
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
<style type="text/css">
.rptDropDown>a.multiSelect>span{width:146px !important;}
</style>
</head>
<body>
<form onSubmit="return validateform()" name="addframe" id="firstform" action="" method="post" class="mt10">
    <table class="table_collapse">
        <tr class="listheading">
          <td style="width:20px;">
            <input type="hidden" id="del_hidden" name="del_hidden" value="" />
            <input type="checkbox" id="selectall" value="" /></td>
          <td style="width:auto;">Lens Lab<div class="success_msg" style="display:<?php echo $msg_stat;?>;"><?php echo $msg; ?></div></td>
		  <td style="width:330px;;">VisionWeb User</td>
          <td align="center" style="width:100px;">Status</td>
        </tr>
        </table><?php
		$aprxHght=($total_pages>$limit)?465:435;
		?>
	<div id="listing_record" style="height:<?php echo $_SESSION['wn_height']-$aprxHght;?>px; overflow-y:auto;">
        <table class="table_collapse">
        <tbody class="table_cell_padd2">
            <?php 
			
				$sql_trt_des=imw_query("select * from in_vw_user_lab order by vw_user_id");
				while($row_trt_des=imw_fetch_array($sql_trt_des)){
					$trt_mat_arr[$row_trt_des['lab_id']][]=$row_trt_des['vw_user_id'];
				}
				
                $sql="select * from in_lens_lab where del_status != '2' $whr order by lab_name asc LIMIT $start, $limit";
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
				$user_id_arr=$trt_mat_arr[$row['id']];		
            ?>
            <tr class="<?php echo $rowbg;?>">
              <td style="width:20px;"><input type="checkbox" class="getchecked" value="<?php echo $row['id']; ?>" id="checked_<?php echo $i; ?>" name="select_record[]" /></td>
              <td style="width:auto;">
                <input type="text" value="<?php echo $row["lab_name"]; ?>" class="materialname_field" id="materialname_field_<?php echo $i; ?>"  onChange="selectCurrentCheck('<?php echo $i; ?>')" name="labname[<?php echo $row['id']; ?>]" style="width:600px;"/>
              </td>
			  <td style="width:370px;" class="rptDropDown"  onClick="selectCurrentCheck('<?php echo $i; ?>')">
			  	<select name="vw_user[<?php echo $row['id']; ?>]" id="vw_user_<?php echo $row['id']; ?>" style="width:90%;" class="vw_user">
                	<option value="">VW User</option>
                    <?php foreach($dsn_arr as $dsn_key=>$dsn_val){ ?>
                    	<option value="<?php echo $dsn_key; ?>" <?php if(in_array($dsn_key,$user_id_arr)){echo"selected";} ?>><?php echo $dsn_val; ?></option>
                    <?php } ?>
                </select>
              </td>
              <td align="center" style="width:70px;">
                <?php if($status=="1") 
                { ?>	
                <img id="status<?php echo $row['id']; ?>" title="InActive" class="noborder" style="cursor: pointer;" onClick="javascript:setStatus('in_lens_lab','<?php echo $row['id']; ?>','0','del_status',this)" src="../../../images/off.png" complete="complete"/>
                <?php } else { ?>
                <img id="status<?php echo $row['id']; ?>" title="Active" class="noborder" style="cursor: pointer;" onClick="javascript:setStatus('in_lens_lab','<?php echo $row['id']; ?>','1','del_status',this)" src="../../../images/on.png" complete="complete"/>
                <?php 
                } ?>
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
              <td colspan="3" align="center" class="even">No Record Exist</td>
            </tr>
            <?php } ?>
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
</form>

<script type="text/javascript">
function submitFrom(){
	document.addframe.submit();
}
$(document).ready(function()
{
	selectCurrentCheck = function(ab)
	{
	   $("#checked_"+ab).prop('checked', true);
	
	   var currentval = $('#materialname_field_'+ab);
	
		if($.trim(currentval.val()) == "")
		{
				top.falert('Please Enter Material Name');	
				setTimeout(function(){$( currentval ).focus(); },0);
		}
		else
		{
			$(".materialname_field").each(function( index ) 
			{
				//top.falert($(this).val()+" == "+currentval);
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
		$(".materialname_field").each(function(index)
		{
			if($.trim($(this).val()) == "")
			{
				top.falert("Please Enter Material Name");
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