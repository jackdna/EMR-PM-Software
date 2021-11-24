<?php
/*
File: cont_lens_brand.php
Coded in PHP7
Purpose: Add/Edit/Delete: Contact Lense Brand
Access Type: Direct access
*/
require_once(dirname('__FILE__')."/../../../config/config.php");
require_once(dirname('__FILE__')."/../../../library/classes/functions.php");
$msg_stat = "none";
$opr_id = $_SESSION['authId'];
$date = date("Y-m-d");
$time = date("h:i:s");

//update practice code if not filled
	$q1=imw_query("SELECT * FROM  `in_prac_codes` where module_id=3 and del_status='0'");
	$d1=imw_fetch_object($q1);
	$defaultPracCode=back_prac_id($d1->prac_code);
	if($defaultPracCode){
		imw_query("update in_contact_brand set prac_code ='$defaultPracCode' where prac_code='' OR prac_code=0")or die(imw_error());
	}
//UPDATE AND INSERT CONTACT LENS BRAND//

if(isset($_POST['save']) || isset($_POST['del_hidden']))	
{	
	if(count($_POST['select_record'])>0)
	{
		for($v=0;$v<count($_POST['select_record']);$v++)
		{
			if(trim($_POST['del_hidden'])=="")
			{
				$rec_id = $_POST['select_record'][$v];
				$rec_supplyname = trim($_POST['supplyname'][$rec_id]);
				$rec_manufac_name = $_POST['manufac_name'][$rec_id];
				$rec_prac_code = $_POST['prac_code'][$rec_id];
				$rec_wholesale_price = $_POST['wholesale_price'][$rec_id];
				$rec_purchase_price = $_POST['purchase_price'][$rec_id];
				$rec_retail_price = $_POST['retail_price'][$rec_id];
				
				$procedureId = back_prac_id($rec_prac_code, false, 3);
								
				if($rec_supplyname!="")
				{			
					$updateQry = "update in_contact_brand set brand_name = '".imw_real_escape_string($rec_supplyname)."', prac_code='".$procedureId."', wholesale_price='".$rec_wholesale_price."', purchase_price='".$rec_purchase_price."', retail_price='".$rec_retail_price."', modified_date='$date', modified_time='$time', modified_by='$opr_id' where id = '".$rec_id."'";		
				
					imw_query($updateQry);
					
					$selec = imw_query("select * from in_contact_brand_manufacture where brand_id = '".$rec_id."'");
					$num_rows = imw_num_rows($selec);
					if($num_rows > 0) {
						$del_rec = imw_query("delete from in_contact_brand_manufacture where brand_id='".$rec_id."'");
					}
					if($rec_manufac_name!="")
					{							
						$insert_brand_manu = imw_query("insert in_contact_brand_manufacture set brand_id='".$rec_id."', manufacture_id = '".imw_real_escape_string($rec_manufac_name)."' ");
					}
					
					$msg = "Record(s) Saved Successfully";
					$msg_stat = "block";
		
					if(trim($_POST['input_val'])!="")
					{
						$edit_time_insert_query = "insert in_contact_brand set brand_name = '".imw_real_escape_string($_POST['input_val'])."' ";					
					}
				}
			}
			else
			{
				$rec_id="";
				$rec_id = $_POST['select_record'][$v];
				$updateQry = "";
				$updateQry = "update in_contact_brand set del_status = '2', del_date='$date', del_time='$time', del_by='$opr_id' where id = '".$rec_id."' ";
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
			imw_query("insert in_contact_brand set brand_name = '".imw_real_escape_string($_POST['input_val'])."' ");
		}
	}
	if($edit_time_insert_query!="")
	{
		$msg = "Record(s) Saved Successfully";
		$msg_stat = "block";
		imw_query($edit_time_insert_query);
	}
}

$manufacturers = array();
$manu_detail_qry = "select id, manufacturer_name from in_manufacturer_details where cont_lenses_chk='1' and del_status='0' order by manufacturer_name asc";
$manu_detail_res = imw_query($manu_detail_qry);
$manu_detail_nums = imw_num_rows($manu_detail_res);
if($manu_detail_nums > 0){	
	while($manu_detail_row = imw_fetch_assoc($manu_detail_res)){
		$manufacturers[$manu_detail_row['id']] = $manu_detail_row['manufacturer_name'];
	}
}

 if($_REQUEST['alpha'] && $_REQUEST['alpha']!='az')
{
	$whr = " and brand_name like '".$_REQUEST['alpha']."%' ";
}
$targetpage = "cont_lens_brand.php"; 	
$limit = 50; 
$query = "SELECT COUNT(*) as num FROM in_contact_brand where del_status != '2' $whr";
$total_pages = imw_fetch_array(imw_query($query));
$total_pages = $total_pages[num];
$stages = 3;
$page = imw_escape_string($_GET['page']);
if($page){
	$start = ($page - 1) * $limit; 
}else{
	$start = 0;	
}

/************ Start Getting Data for CPT Codes ************/
$proc_code_arr=array();
$proc_code_desc_arr=array();
$sql = "SELECT `cpt_fee_id`, `cpt_prac_code`, `cpt_desc` FROM `cpt_fee_tbl` 
		WHERE `status`='active' AND `delete_status`='0'
		ORDER BY `cpt_prac_code` ASC";
$sql = imw_query($sql);
if($sql && imw_num_rows($sql)>0){
	while($row = imw_fetch_assoc($sql)){
		$proc_code_arr[$row['cpt_fee_id']] = $row['cpt_prac_code'];
		$proc_code_desc_arr[$row['cpt_fee_id']] = $row['cpt_desc'];
	}
}
imw_free_result($sql);
/************ End Getting Data for CPT Codes ************/
?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Optical</title>
<link rel="stylesheet" href="../../../library/css/inv_css.css?<?php echo constant("cache_version"); ?>" />
<script src="../../../library/js/jquery-1.10.1.min.js?<?php echo constant("cache_version"); ?>"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/common.js?<?php echo constant("cache_version"); ?>"></script>

<link rel="stylesheet" href="<?php echo $GLOBALS['WEB_PATH'];?>/library/css/ajaxTypeahead.css?<?php echo constant("cache_version"); ?>" />
<script src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/ajaxTypeahead.js?<?php echo constant("cache_version"); ?>"></script> 

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
					$('#status'+rowid).attr("onclick","javascript:setStatus('in_contact_brand',"+rowid+",'0','del_status',this)");
				}
				else if(value==0)
				{
					$('#status'+rowid).attr('src','../../../images/on.png');
					$('#status'+rowid).attr('title','Active');
					$('#status'+rowid).attr("onclick","javascript:setStatus('in_contact_brand',"+rowid+",'1','del_status',this)");
				}
				
			}
		}
	});
}
	
function refrsh()
{
	window.location.href='cont_lens_supply.php';
}

function open_addnew_popup()
{
	top.WindowDialog.closeAll();
	var Add_new_popup=top.WindowDialog.open('Add_new_popup',WEB_PATH+'/interface/admin/add_new.php?module_name=contact_brand&col_name=brand_name&type=cont_lenses&heading=Contact_Lenses_Brand','Add_new_popup','width=930,height=380,left=300,scrollbars=no,top=80,fullscreen=0,resizable=0');
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
<form onSubmit="return validateform()" name="addframe" id="firstform" action="" method="post" class="mt10"><?php
		$aprxHght=($total_pages>$limit)?435:405;
		?>
	<div id="listing_record" style="height:<?php echo $_SESSION['wn_height']-$aprxHght;?>px; overflow-y:auto;">
        <table class="table_collapse">
            <thead>
                <tr class="listheading">
                    <td style="width:10px;">
                        <input type="hidden" id="del_hidden" name="del_hidden" value="" />
                        <input type="checkbox" id="selectall" value="" />
                    </td>
					<td style="width:160px;">Manufacturer</td>
                    <td style="width:360px;">Contact Lens Brand<div class="success_msg" style="margin-right:4%;display:<?php echo $msg_stat;?>;"><?php echo $msg; ?></div></td>
					<td title="Practice Code" style="width:100px">Prac. Code</td>
					<td style="width:100px;">Wholesale Price</td>
					<td style="width:100px;">Purchase Price</td>
					<td style="width:100px;">Retail Price</td>
                    <td align="center" style="width:40px;">Status</td>
                </tr>
            </thead>
            <tbody class="table_cell_padd2">
                <?php
                   	$sql="select * from in_contact_brand where del_status != '2' $whr order by brand_name asc LIMIT $start, $limit";
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
                <tr class="<?php echo $rowbg;?>">
                  <td><input type="checkbox" class="getchecked" value="<?php echo $row['id']; ?>" id="checked_<?php echo $i; ?>" name="select_record[]" /></td>
                  <td>
                  	<select name="manufac_name[<?php echo $row['id']; ?>]" style="width:98%;" onChange="selectCurrentCheck('<?php echo $i; ?>')" id="manufac_name<?php echo $row['id']; ?>">
                    	<option value="">Select</option>
<?php
	$sql = "SELECT `manufacture_id` FROM `in_contact_brand_manufacture` WHERE `brand_id`='".$row['id']."'";
	$resp = imw_query($sql);
	$manu_id = "";
	if($resp && imw_num_rows($resp)>0){
		$manu_id = imw_fetch_assoc($resp);
		$manu_id = $manu_id['manufacture_id'];
	}
	foreach($manufacturers as $key=>$val){
		$selected = "";
		if($key == $manu_id){
			$selected = 'selected="selected"';
		}
		echo '<option '.$selected.' value="'.$key.'">'.$val.'</option>';
	}
?>
                    </select>
                  </td>
				  <td>
                    <input type="text" value="<?php echo $row['brand_name']; ?>" class="supplyname_field" id="supplyname_field_<?php echo $i; ?>" onChange="selectCurrentCheck('<?php echo $i; ?>')" name="supplyname[<?php echo $row['id']; ?>]" style="width:98%;"/>
                  </td>
				  <td>
				  	<input type="text" value="<?php echo $proc_code_arr[$row['prac_code']]; ?>" name="prac_code[<?php echo $row['id']; ?>]" id="prac_code_<?php echo $i; ?>" class="prac_code" onChange="selectCurrentCheck('<?php echo $i; ?>')" style="width:92%;" autocomplete="off" />
				  </td>
				  <td>
				  	<input type="text" value="<?php echo $row['wholesale_price']; ?>" name="wholesale_price[<?php echo $row['id']; ?>]" id="wholesale_price_<?php echo $i; ?>" onChange="convert_float(this);selectCurrentCheck('<?php echo $i; ?>')" style="width:92%;" />
				  </td>
				  <td>
				  	<input type="text" value="<?php echo $row['purchase_price']; ?>" name="purchase_price[<?php echo $row['id']; ?>]" id="purchase_price_<?php echo $i; ?>" onChange="convert_float(this);selectCurrentCheck('<?php echo $i; ?>')" style="width:92%;" />
				  </td>
				  <td>
				  	<input type="text" value="<?php echo $row['retail_price']; ?>" name="retail_price[<?php echo $row['id']; ?>]" id="retail_price_<?php echo $i; ?>" onChange="convert_float(this);selectCurrentCheck('<?php echo $i; ?>')" style="width:92%;" />
				  </td>
                  <td align="center">
                    <?php if($status=="1") 
                    { ?>	
                    <img id="status<?php echo $row['id']; ?>" title="InActive" class="noborder" style="cursor: pointer;" onClick="javascript:setStatus('in_contact_brand','<?php echo $row['id']; ?>','0','del_status',this)" src="../../../images/off.png" complete="complete"/>
                    <?php } else { ?>
                    <img id="status<?php echo $row['id']; ?>" title="Active" class="noborder" style="cursor: pointer;" onClick="javascript:setStatus('in_contact_brand','<?php echo $row['id']; ?>','1','del_status',this)" src="../../../images/on.png" complete="complete"/>
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
                  <td colspan="8" align="center" class="even">No Record Exist</td>
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
        </div>
    </div>
	
<script type="text/javascript">
function submitFrom(){
	document.addframe.submit();
}
$(document).ready(function()
{
	selectCurrentCheck = function(ab)
	{
	   $("#checked_"+ab).prop('checked', true);
	
	   var currentval = $('#supplyname_field_'+ab);
	
		if($.trim(currentval.val()) == "")
		{
				top.falert('Please Enter Contact Lens Brand');	
				setTimeout(function(){$( currentval ).focus(); },0);
		}
		else
		{
			$(".supplyname_field").each(function( index ) 
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
		$(".supplyname_field").each(function(index)
		{
			if($.trim($(this).val()) == "")
			{
				top.falert("Please Enter Contact Lens Brand");
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
	
	$(".prac_code").ajaxTypeahead({
		url: '<?php echo $GLOBALS['WEB_PATH']; ?>/interface/patient_interface/ajax.php',
		type: 'praCode',
		showAjaxVals: 'defaultCodeC'
	});
});
</script>
</body>
</html>