<?php
	/*
	File: frame_brand.php
	Coded in PHP7
	Purpose: Add/Edit/Delete: Frame Brand
	Access Type: Direct access
	*/
	require_once(dirname('__FILE__')."/../../../config/config.php");
	$msg_stat = "none";
	$opr_id = $_SESSION['authId'];
	$date = date("Y-m-d");
	$time = date("h:i:s");
	//UPDATE AND INSERT NEW FRAME BRAND//
	if(isset($_POST['save']) || isset($_POST['del_hidden']))	
	{	
		if(count($_POST['select_record'])>0)
		{
			for($v=0;$v<count($_POST['select_record']);$v++)
			{	
				if(trim($_POST['del_hidden'])=="")
				{
					$rec_id = $_POST['select_record'][$v];
					$rec_sourcename = trim($_POST['sourcename'][$rec_id]);
					$rec_manufac_name = $_POST['manufac_name'][$rec_id];
					if($rec_sourcename!="")
					{			
						$updateQry = "update in_frame_sources set frame_source = '".imw_real_escape_string($rec_sourcename)."', modified_date='$date', modified_time='$time', modified_by='$opr_id' where id = '".$rec_id."' ";		
						imw_query($updateQry);
						
						$selec = imw_query("select * from in_brand_manufacture where brand_id = '".$rec_id."'");
						$num_rows = imw_num_rows($selec);
						if($num_rows > 0) {
							$del_rec = imw_query("delete from in_brand_manufacture where brand_id='".$rec_id."'");
						}
						if($rec_manufac_name!="")
						{							
							$insert_brand_manu = imw_query("insert in_brand_manufacture set brand_id='".$rec_id."', manufacture_id = '".imw_real_escape_string($rec_manufac_name)."' ");
						}
						$msg = "Record(s) Saved Successfully";
						$msg_stat = "block";
			
						if(trim($_POST['input_val'])!="")
						{
 							$edit_time_insert_query = "insert in_frame_sources set frame_source = '".imw_real_escape_string($_POST['input_val'])."' ";					
						}
					}
				}
				else
				{
					$rec_id="";
					$rec_id = $_POST['select_record'][$v];
					$updateQry = "";
					$updateQry = "update in_frame_sources set del_status = '2', del_date='$date', del_time='$time', del_by='$opr_id' where id = '".$rec_id."' ";
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
				imw_query("insert in_frame_sources set frame_source = '".imw_real_escape_string($_POST['input_val'])."' ");
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
		$whr = " and frame_source like '".$_REQUEST['alpha']."%' ";	
	}

	$targetpage = "frame_brand.php"; 	
	$limit = 50; 
	$query = "SELECT COUNT(*) as num FROM in_frame_sources where del_status != '2' $whr";
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
<meta name="viewport" content="width=device-width, maximum-scale=1.0; charset=UTF-8" />
<title>Optical</title>
<link rel="stylesheet" href="<?php echo $GLOBALS['WEB_PATH'];?>/library/css/inv_css.css?<?php echo constant("cache_version"); ?>" />
<link rel="stylesheet" href="<?php echo $GLOBALS['WEB_PATH'];?>/library/css/jquery.multiSelect.css?<?php echo constant("cache_version"); ?>" />

<script src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/jquery-1.10.1.min.js?<?php echo constant("cache_version"); ?>"></script>
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

jQ(document).ready(function(){
	del = function(){
	 	if( jQ(".getchecked:checked").length == 0 ){
           top.falert('Please check atleast one record');
        }else{
			top.fconfirm('Are you sure to delete selected record(s) ?',del_callBack);
		}
	}
	jQ("#selectall").click(function(){		
		if(jQ(this).is(":checked")){
			jQ(".getchecked").prop('checked', true);
		}else{
			jQ(".getchecked").prop('checked', false);
		}
	});
});
function setStatus(tbname,rowid,value,colname){
	var dataString = 'table='+ tbname + '&id=' + rowid + '&value=' + value + '&column=' + colname + '&page=change';
	jQ.ajax({
		type: "POST",
		url: "change_status.php",
		data: dataString,
		cache: false,
		success: function(response){
			if(response=="true"){
				if(value==1){
					jQ('#status'+rowid).attr('src','../../../images/off.png');
					jQ('#status'+rowid).attr('title','InActive');
					jQ('#status'+rowid).attr("onclick","javascript:setStatus('in_frame_sources',"+rowid+",'0','del_status',this)");
				}
				else if(value==0){
					jQ('#status'+rowid).attr('src','../../../images/on.png');
					jQ('#status'+rowid).attr('title','Active');
					jQ('#status'+rowid).attr("onclick","javascript:setStatus('in_frame_sources',"+rowid+",'1','del_status',this)");
				}
				
			}
		}
	});
}
	
function refrsh(){window.location.href='frame_brand.php';}
function open_addnew_popup()
{
	top.WindowDialog.closeAll();
	var Add_new_popup=top.WindowDialog.open('Add_new_popup',WEB_PATH+'/interface/admin/add_new.php?module_name=frame_sources&col_name=frame_source&type=frames&heading=Frame_Brand','Add_new_popup','width=700,height=380,left=300,scrollbars=no,top=80,fullscreen=0,resizable=0');
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
    <div class="mt10" style="height:<?php echo $_SESSION['wn_height']-360;?>px;">
        <form action="" name="addframe" onSubmit="return validateform()" id="firstform" method="post" style="margin:0px;">
        <?php
        $aprxHght=($total_pages>$limit)?435:405;
		?>
        <div id="listing_record" style="height:<?php echo $_SESSION['wn_height']-$aprxHght;?>px; overflow-y:auto;">
            <table class="table_collapse">
                <tr class="listheading">
                  <td width="10">
                    <input type="hidden" id="del_hidden" name="del_hidden" value="" />
                    <input type="checkbox" id="selectall" value="" title="Select All" /></td>
                  <td width="610">Brand Name<div class="success_msg" style="display:<?php echo $msg_stat;?>;"><?php echo $msg; ?></div></td>
          		  <td width="250">Manufacturer</td>
                  <td align="center" width="100">Status</td>
                </tr>
                <tbody class="table_cell_padd2">
                    <?php 
					    $sql="select * from in_frame_sources where del_status != '2' $whr order by frame_source asc LIMIT $start, $limit";
                        $res = imw_query($sql);
                        $num = imw_num_rows($res);
                        if($num>0)
                        {
                            $i=0;
							
                            while($row = imw_fetch_array($res))
                            {
                                $status = $row['del_status'];
                                if($rowbg=='even'){$rowbg="odd";}else{$rowbg="even";}
                    ?>
                    <tr class="<?php echo $rowbg;?>">
                     <td><input type="checkbox" class="getchecked" value="<?php echo $row['id']; ?>" id="checked_<?php echo $i; ?>" name="select_record[]" /></td>
                      <td>
                        <input type="text" value="<?php echo $row["frame_source"]; ?>" class="sourcename_field" id="sourcename_field_<?php echo $i; ?>" onChange="selectCurrentCheck('<?php echo $i; ?>')" name="sourcename[<?php echo $row['id']; ?>]" style="width:600px;"/>
                      </td>
                      <td class="rptDropDown">                      	
                        <?php $brand_manu_qry = "select id, manufacture_id from in_brand_manufacture where brand_id='".$row['id']."'";
						$brand_manu_res = imw_query($brand_manu_qry);
						$brand_manu_nums = imw_num_rows($brand_manu_res);
						while($brand_manu_row = imw_fetch_array($brand_manu_res))
						{
							$exist_brand_id[] = $brand_manu_row['manufacture_id'];
						}
						?>
                        <select name="manufac_name[<?php echo $row['id']; ?>]" style="width:250px;" onChange="selectCurrentCheck('<?php echo $i; ?>')" id="manufac_name<?php echo $row['id']; ?>">
                        <option value="">Select</option>
                        <?php $manu_detail_qry = "select id, manufacturer_name from in_manufacturer_details where frames_chk='1' and del_status='0' order by manufacturer_name asc";
                        $manu_detail_res = imw_query($manu_detail_qry);
                        $manu_detail_nums = imw_num_rows($manu_detail_res);
                        if($manu_detail_nums > 0)
                        {	
                            while( $manu_detail_row = imw_fetch_array($manu_detail_res)) { 
							$selet='';
                            if(in_array($manu_detail_row['id'], $exist_brand_id)) 
							{ $selet = 'selected'; } ?>
                            <option <?php echo $selet; ?> value="<?php echo $manu_detail_row['id']; ?>"><?php echo $manu_detail_row['manufacturer_name']; ?></option>
                    	<?php } 
						} unset($exist_brand_id); ?>
                        </select>
					
                      </td>
                      <td align="center">
                        <?php if($status=="1") 
                        { ?>	
                        <img id="status<?php echo $row['id']; ?>" title="InActive" class="noborder" style="cursor: pointer;" onClick="javascript:setStatus('in_frame_sources','<?php echo $row['id']; ?>','0','del_status',this)" src="../../../images/off.png" complete="complete"/>
                        <?php } else { ?>
                        <img id="status<?php echo $row['id']; ?>" title="Active" class="noborder" style="cursor: pointer;" onClick="javascript:setStatus('in_frame_sources','<?php echo $row['id']; ?>','1','del_status',this)" src="../../../images/on.png" complete="complete"/>
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
                      <td colspan="4" align="center" class="even">No Record Exist</td>
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
			<li class="fl"><a style="border-radius:5px; padding:2px 6px; color:#fff; text-decoration:none; font-weight:bold; text-transform:uppercase; margin:3px; background:<?php if($_REQUEST['alpha']=="az") { echo "#fb9608"; } else { echo "#09F"; } ?>;" href="?alpha=az">A-Z</a></li>
			<?php foreach($alpha as $key=>$value) 
			{ ?>
			<li class="fl"><a style="border-radius:5px; padding:2px 6px; color:#fff; text-decoration:none; font-weight:bold; text-transform:uppercase; margin:2.3px; background:<?php if($_REQUEST['alpha']==$value) { echo "#fb9608"; } else { echo "#09F"; } ?>" href="?alpha=<?php echo $value; ?>"><?php echo $value; ?></a></li>
			<?php } ?>
			</ul>
        </div>
       </form>
    </div>

<script type="text/javascript">
function submitFrom(){
	document.addframe.submit();
}
$(document).ready(function(){

selectCurrentCheck = function(ab)
{
   jQ("#checked_"+ab).prop('checked', true);
   var currentval = $('#sourcename_field_'+ab);

	if($.trim(currentval.val()) == "")
	{
			top.falert('Please Enter Brand Name');	
			setTimeout(function(){$( currentval ).focus(); },0);
	}
	else
	{
		$(".sourcename_field").each(function( index ) 
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
	$(".sourcename_field").each(function(index)
	{
		if($.trim($(this).val()) == "")
		{
			top.falert("Please Enter Brand Name");
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